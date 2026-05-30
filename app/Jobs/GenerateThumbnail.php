<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GenerateThumbnail implements ShouldQueue
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
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $originalPath,
        public int $width = 300,
        public int $height = 300,
        public string $prefix = 'thumb_'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): string|null
    {
        if (!Storage::disk('public')->exists($this->originalPath)) {
            logger()->warning('Original image not found for thumbnail generation', [
                'path' => $this->originalPath,
            ]);
            return null;
        }

        try {
            $fullPath = Storage::disk('public')->path($this->originalPath);
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);

            // Generate thumbnail with cover (crop to fit)
            $image->cover($this->width, $this->height);

            // Generate thumbnail path
            $directory = dirname($this->originalPath);
            $filename = basename($this->originalPath);
            $thumbnailPath = $directory . '/' . $this->prefix . $filename;

            // Save thumbnail
            $thumbnail = $image->toJpeg(quality: 80);
            Storage::disk('public')->put($thumbnailPath, $thumbnail->toString());

            logger()->info('Thumbnail generated successfully', [
                'original' => $this->originalPath,
                'thumbnail' => $thumbnailPath,
                'size' => "{$this->width}x{$this->height}",
            ]);

            return $thumbnailPath;

        } catch (\Exception $e) {
            logger()->error('Failed to generate thumbnail', [
                'path' => $this->originalPath,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        logger()->error('Thumbnail generation job failed', [
            'path' => $this->originalPath,
            'error' => $exception->getMessage(),
        ]);
    }
}
