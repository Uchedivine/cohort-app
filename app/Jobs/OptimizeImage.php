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

class OptimizeImage implements ShouldQueue
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
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath,
        public int $maxWidth = 1920,
        public int $quality = 85
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!Storage::disk('public')->exists($this->filePath)) {
            logger()->warning('Image file not found for optimization', [
                'path' => $this->filePath,
            ]);
            return;
        }

        try {
            $fullPath = Storage::disk('public')->path($this->filePath);
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);

            // Only optimize if image is larger than max width
            if ($image->width() > $this->maxWidth) {
                $image->scale(width: $this->maxWidth);
            }

            // Get file extension
            $extension = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));

            // Optimize based on format
            $optimized = match($extension) {
                'jpg', 'jpeg' => $image->toJpeg(quality: $this->quality),
                'png' => $image->toPng(),
                'gif' => $image->toGif(),
                'webp' => $image->toWebp(quality: $this->quality),
                default => $image->toJpeg(quality: $this->quality),
            };

            // Save optimized image
            Storage::disk('public')->put($this->filePath, $optimized->toString());

            logger()->info('Image optimized successfully', [
                'path' => $this->filePath,
                'original_width' => $image->width(),
                'max_width' => $this->maxWidth,
            ]);

        } catch (\Exception $e) {
            logger()->error('Failed to optimize image', [
                'path' => $this->filePath,
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
        logger()->error('Image optimization job failed', [
            'path' => $this->filePath,
            'error' => $exception->getMessage(),
        ]);
    }
}
