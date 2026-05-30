<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessFileUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public string $fileType,
        public ?int $modelId = null,
        public ?string $modelType = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!Storage::disk('public')->exists($this->filePath)) {
            logger()->warning('File not found for processing', [
                'path' => $this->filePath,
            ]);
            return;
        }

        try {
            logger()->info('Processing file upload', [
                'path' => $this->filePath,
                'type' => $this->fileType,
                'model_id' => $this->modelId,
                'model_type' => $this->modelType,
            ]);

            // Process based on file type
            match($this->fileType) {
                'image' => $this->processImage(),
                'document' => $this->processDocument(),
                default => logger()->info('No processing needed for file type', [
                    'type' => $this->fileType,
                ]),
            };

            logger()->info('File processing completed', [
                'path' => $this->filePath,
            ]);

        } catch (\Exception $e) {
            logger()->error('Failed to process file upload', [
                'path' => $this->filePath,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Process image file
     */
    private function processImage(): void
    {
        // Dispatch optimization job
        OptimizeImage::dispatch($this->filePath);

        // Dispatch thumbnail generation job
        GenerateThumbnail::dispatch($this->filePath);

        logger()->info('Image processing jobs dispatched', [
            'path' => $this->filePath,
        ]);
    }

    /**
     * Process document file
     */
    private function processDocument(): void
    {
        // Get file info
        $fullPath = Storage::disk('public')->path($this->filePath);
        $fileSize = Storage::disk('public')->size($this->filePath);
        $mimeType = Storage::disk('public')->mimeType($this->filePath);

        logger()->info('Document processed', [
            'path' => $this->filePath,
            'size' => $fileSize,
            'mime_type' => $mimeType,
        ]);

        // Future: Add virus scanning here
        // ScanUploadedFile::dispatch($this->filePath);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        logger()->error('File processing job failed', [
            'path' => $this->filePath,
            'type' => $this->fileType,
            'error' => $exception->getMessage(),
        ]);

        // Optionally notify admin
        // event(new FileProcessingFailed($this->filePath, $exception));
    }
}
