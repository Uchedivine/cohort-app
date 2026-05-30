<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CleanupOldFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $daysOld = 90
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger()->info('Starting cleanup of old files', [
            'days_old' => $this->daysOld,
        ]);

        $deletedCount = 0;
        $totalSize = 0;

        try {
            // Get all files in storage
            $directories = ['temp', 'uploads/temp', 'cache'];

            foreach ($directories as $directory) {
                if (!Storage::disk('public')->exists($directory)) {
                    continue;
                }

                $files = Storage::disk('public')->files($directory);

                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    $age = now()->timestamp - $lastModified;
                    $daysAge = $age / 86400; // Convert to days

                    if ($daysAge > $this->daysOld) {
                        $size = Storage::disk('public')->size($file);
                        Storage::disk('public')->delete($file);
                        
                        $deletedCount++;
                        $totalSize += $size;

                        logger()->debug('Deleted old file', [
                            'file' => $file,
                            'age_days' => round($daysAge, 2),
                            'size' => $size,
                        ]);
                    }
                }
            }

            logger()->info('Cleanup completed', [
                'deleted_count' => $deletedCount,
                'total_size' => $this->formatBytes($totalSize),
            ]);

        } catch (\Exception $e) {
            logger()->error('Failed to cleanup old files', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        logger()->error('Cleanup job failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}
