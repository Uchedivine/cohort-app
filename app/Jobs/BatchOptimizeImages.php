<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BatchOptimizeImages implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

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
        public array $imagePaths
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        logger()->info('Starting batch image optimization', [
            'count' => count($this->imagePaths),
        ]);

        $optimized = 0;
        $failed = 0;

        foreach ($this->imagePaths as $path) {
            if ($this->batch()->cancelled()) {
                logger()->info('Batch cancelled, stopping optimization');
                break;
            }

            try {
                if (Storage::disk('public')->exists($path)) {
                    OptimizeImage::dispatchSync($path);
                    $optimized++;
                } else {
                    logger()->warning('Image not found', ['path' => $path]);
                    $failed++;
                }
            } catch (\Exception $e) {
                logger()->error('Failed to optimize image in batch', [
                    'path' => $path,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        logger()->info('Batch optimization completed', [
            'optimized' => $optimized,
            'failed' => $failed,
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        logger()->error('Batch optimization job failed', [
            'count' => count($this->imagePaths),
            'error' => $exception->getMessage(),
        ]);
    }
}
