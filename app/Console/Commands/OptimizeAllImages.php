<?php

namespace App\Console\Commands;

use App\Jobs\BatchOptimizeImages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class OptimizeAllImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize 
                            {--directory=* : Specific directories to optimize}
                            {--batch-size=50 : Number of images per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all images in storage using batch processing';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting batch image optimization...');

        $directories = $this->option('directory') ?: [
            'stories',
            'events',
            'logos',
            'resources',
            'event-media',
        ];

        $batchSize = (int) $this->option('batch-size');
        $allImages = [];

        // Collect all images
        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                $this->warn("Directory not found: {$directory}");
                continue;
            }

            $files = Storage::disk('public')->allFiles($directory);
            
            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $allImages[] = $file;
                }
            }
        }

        if (empty($allImages)) {
            $this->info('No images found to optimize.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($allImages) . ' images to optimize.');

        // Split into batches
        $batches = array_chunk($allImages, $batchSize);
        
        $this->info('Creating ' . count($batches) . ' batch jobs...');

        // Create batch
        $batch = Bus::batch([])->then(function () {
            logger()->info('All image optimization batches completed');
        })->catch(function ($batch, \Throwable $e) {
            logger()->error('Image optimization batch failed', [
                'error' => $e->getMessage(),
            ]);
        })->finally(function () {
            logger()->info('Image optimization batch processing finished');
        })->dispatch();

        // Add jobs to batch
        foreach ($batches as $index => $imageBatch) {
            $batch->add(new BatchOptimizeImages($imageBatch));
            $this->info("Batch " . ($index + 1) . " dispatched (" . count($imageBatch) . " images)");
        }

        $this->info('All batches dispatched successfully!');
        $this->info('Batch ID: ' . $batch->id);
        $this->info('Monitor progress with: php artisan queue:monitor');

        return Command::SUCCESS;
    }
}
