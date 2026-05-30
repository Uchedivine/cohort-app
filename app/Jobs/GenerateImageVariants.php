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

class GenerateImageVariants implements ShouldQueue
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
     * Predefined image variants
     */
    private const VARIANTS = [
        'thumbnail' => ['width' => 300, 'height' => 300],
        'small' => ['width' => 640, 'height' => null],
        'medium' => ['width' => 1024, 'height' => null],
        'large' => ['width' => 1920, 'height' => null],
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $originalPath,
        public array $variants = ['thumbnail', 'small', 'medium']
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): array
    {
        if (!Storage::disk('public')->exists($this->originalPath)) {
            logger()->warning('Original image not found for variant generation', [
                'path' => $this->originalPath,
            ]);
            return [];
        }

        $generatedVariants = [];

        try {
            $fullPath = Storage::disk('public')->path($this->originalPath);
            $manager = new ImageManager(new Driver());
            
            $directory = dirname($this->originalPath);
            $filename = pathinfo($this->originalPath, PATHINFO_FILENAME);
            $extension = pathinfo($this->originalPath, PATHINFO_EXTENSION);

            foreach ($this->variants as $variantName) {
                if (!isset(self::VARIANTS[$variantName])) {
                    logger()->warning('Unknown variant type', ['variant' => $variantName]);
                    continue;
                }

                $config = self::VARIANTS[$variantName];
                $image = $manager->read($fullPath);

                // Resize based on variant config
                if ($config['height']) {
                    $image->cover($config['width'], $config['height']);
                } else {
                    $image->scale(width: $config['width']);
                }

                // Generate variant path
                $variantPath = "{$directory}/{$filename}_{$variantName}.{$extension}";

                // Save variant
                $variant = $image->toJpeg(quality: 85);
                Storage::disk('public')->put($variantPath, $variant->toString());

                $generatedVariants[$variantName] = $variantPath;

                logger()->info('Image variant generated', [
                    'original' => $this->originalPath,
                    'variant' => $variantName,
                    'path' => $variantPath,
                ]);
            }

            return $generatedVariants;

        } catch (\Exception $e) {
            logger()->error('Failed to generate image variants', [
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
        logger()->error('Image variant generation job failed', [
            'path' => $this->originalPath,
            'error' => $exception->getMessage(),
        ]);
    }
}
