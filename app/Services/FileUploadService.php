<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use InvalidArgumentException;

class FileUploadService
{
    /**
     * Allowed MIME types for different file categories
     */
    private const ALLOWED_IMAGES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    private const ALLOWED_DOCUMENTS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    /**
     * Maximum file sizes in bytes
     */
    private const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    private const MAX_DOCUMENT_SIZE = 10 * 1024 * 1024; // 10MB

    /**
     * Upload an image file with optimization
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param bool $createThumbnail
     * @return array ['path' => string, 'thumbnail' => string|null]
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', bool $createThumbnail = false): array
    {
        $this->validateImage($file);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        $path = "{$directory}/{$filename}";

        // Optimize and save image
        $optimizedImage = $this->optimizeImage($file);
        Storage::disk('public')->put($path, $optimizedImage);

        $result = ['path' => $path, 'thumbnail' => null];

        // Create thumbnail if requested
        if ($createThumbnail) {
            $thumbnailPath = $this->createThumbnail($file, $directory, $filename);
            $result['thumbnail'] = $thumbnailPath;
        }

        return $result;
    }

    /**
     * Upload a document file
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string File path
     */
    public function uploadDocument(UploadedFile $file, string $directory = 'documents'): string
    {
        $this->validateDocument($file);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        $path = "{$directory}/{$filename}";

        // Store file
        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return $path;
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @return bool
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @throws InvalidArgumentException
     */
    private function validateImage(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_IMAGES)) {
            throw new InvalidArgumentException('Invalid image type. Allowed: JPG, PNG, GIF, WebP');
        }

        // Check file size
        if ($file->getSize() > self::MAX_IMAGE_SIZE) {
            throw new InvalidArgumentException('Image size exceeds maximum allowed size of 5MB');
        }

        // Check for double extensions (security)
        if (substr_count($file->getClientOriginalName(), '.') > 1) {
            throw new InvalidArgumentException('Invalid filename format');
        }
    }

    /**
     * Validate document file
     *
     * @param UploadedFile $file
     * @throws InvalidArgumentException
     */
    private function validateDocument(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_DOCUMENTS)) {
            throw new InvalidArgumentException('Invalid document type. Allowed: PDF, DOC, DOCX, PPT, PPTX');
        }

        // Check file size
        if ($file->getSize() > self::MAX_DOCUMENT_SIZE) {
            throw new InvalidArgumentException('Document size exceeds maximum allowed size of 10MB');
        }

        // Check for double extensions (security)
        if (substr_count($file->getClientOriginalName(), '.') > 1) {
            throw new InvalidArgumentException('Invalid filename format');
        }
    }

    /**
     * Optimize image for web
     *
     * @param UploadedFile $file
     * @return string Binary image data
     */
    private function optimizeImage(UploadedFile $file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Resize if too large (max 1920px width)
        if ($image->width() > 1920) {
            $image->scale(width: 1920);
        }

        // Encode with quality optimization
        $extension = strtolower($file->getClientOriginalExtension());
        
        return match($extension) {
            'jpg', 'jpeg' => $image->toJpeg(quality: 85)->toString(),
            'png' => $image->toPng()->toString(),
            'gif' => $image->toGif()->toString(),
            'webp' => $image->toWebp(quality: 85)->toString(),
            default => $image->toJpeg(quality: 85)->toString(),
        };
    }

    /**
     * Create thumbnail from image
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string $originalFilename
     * @return string Thumbnail path
     */
    private function createThumbnail(UploadedFile $file, string $directory, string $originalFilename): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Create thumbnail (300px width, maintain aspect ratio)
        $image->scale(width: 300);

        // Generate thumbnail filename
        $thumbnailFilename = 'thumb_' . $originalFilename;
        $thumbnailPath = "{$directory}/{$thumbnailFilename}";

        // Save thumbnail
        $thumbnailData = $image->toJpeg(quality: 80)->toString();
        Storage::disk('public')->put($thumbnailPath, $thumbnailData);

        return $thumbnailPath;
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get file size in human-readable format
     *
     * @param string $path
     * @return string
     */
    public function getFileSize(string $path): string
    {
        if (!Storage::disk('public')->exists($path)) {
            return '0 B';
        }

        $bytes = Storage::disk('public')->size($path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file exists
     *
     * @param string|null $path
     * @return bool
     */
    public function fileExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }
}
