<?php

namespace Tests\Unit\Services;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    private FileUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        $this->service = new FileUploadService();
    }

    /** @test */
    public function it_can_upload_an_image()
    {
        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $result = $this->service->uploadImage($file, 'test');

        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('thumbnail', $result);
        Storage::disk('public')->assertExists($result['path']);
    }

    /** @test */
    public function it_can_create_thumbnail()
    {
        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $result = $this->service->uploadImage($file, 'test', true);

        $this->assertNotNull($result['thumbnail']);
        Storage::disk('public')->assertExists($result['thumbnail']);
    }

    /** @test */
    public function it_validates_image_mime_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid image type');

        $file = UploadedFile::fake()->create('test.pdf', 100);
        $this->service->uploadImage($file, 'test');
    }

    /** @test */
    public function it_validates_image_size()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Image size exceeds');

        // Create 6MB file (exceeds 5MB limit)
        $file = UploadedFile::fake()->image('test.jpg')->size(6144);
        $this->service->uploadImage($file, 'test');
    }

    /** @test */
    public function it_can_upload_a_document()
    {
        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $path = $this->service->uploadDocument($file, 'documents');

        $this->assertNotEmpty($path);
        Storage::disk('public')->assertExists($path);
    }

    /** @test */
    public function it_validates_document_mime_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid document type');

        $file = UploadedFile::fake()->image('test.jpg');
        $this->service->uploadDocument($file, 'documents');
    }

    /** @test */
    public function it_can_delete_a_file()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $result = $this->service->uploadImage($file, 'test');

        $deleted = $this->service->deleteFile($result['path']);

        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($result['path']);
    }

    /** @test */
    public function it_handles_deleting_non_existent_file()
    {
        $deleted = $this->service->deleteFile('non-existent.jpg');

        $this->assertFalse($deleted);
    }

    /** @test */
    public function it_checks_if_file_exists()
    {
        $file = UploadedFile::fake()->image('test.jpg');
        $result = $this->service->uploadImage($file, 'test');

        $exists = $this->service->fileExists($result['path']);

        $this->assertTrue($exists);
    }

    /** @test */
    public function it_rejects_double_extension_files()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid filename format');

        $file = UploadedFile::fake()->create('test.php.jpg', 100);
        $this->service->uploadImage($file, 'test');
    }
}
