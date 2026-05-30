<?php

namespace App\Console\Commands;

use App\Jobs\GenerateImageVariants as GenerateImageVariantsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateImageVariants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:variants 
                            {path : Path to image or directory}
                            {--variants=* : Variants to generate (thumbnail, small, medium, large)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate image variants (thumbnails, responsive sizes)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');
        $variants = $this->option('variants') ?: ['thumbnail', 'small', 'medium'];

        if (!Storage::disk('public')->exists($path)) {
            $this->error("Path not found: {$path}");
            return Command::FAILURE;
        }

        // Check if path is a directory or file
        if (Storage::disk('public')->directoryExists($path)) {
            $this->info("Processing directory: {$path}");
            $this->processDirectory($path, $variants);
        } else {
            $this->info("Processing file: {$path}");
            $this->processFile($path, $variants);
        }

        return Command::SUCCESS;
    }

    /**
     * Process a single file
     */
    private function processFile(string $path, array $variants): void
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $this->warn("Skipping non-image file: {$path}");
            return;
        }

        GenerateImageVariantsJob::dispatch($path, $variants);
        $this->info("✓ Queued variant generation for: {$path}");
    }

    /**
     * Process all images in a directory
     */
    private function processDirectory(string $directory, array $variants): void
    {
        $files = Storage::disk('public')->files($directory);
        $count = 0;

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                GenerateImageVariantsJob::dispatch($file, $variants);
                $count++;
            }
        }

        $this->info("✓ Queued variant generation for {$count} images");
    }
}
