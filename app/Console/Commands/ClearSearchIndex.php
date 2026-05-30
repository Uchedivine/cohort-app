<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Console\Command;

class ClearSearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:clear 
                            {model? : Specific model to clear (Organization, Story, Resource, Event)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear search index for all or specific models';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $model = $this->argument('model');

        if ($model) {
            $this->clearModel($model);
        } else {
            if (!$this->confirm('This will clear all search indexes. Continue?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->info('Clearing all search indexes...');
            $this->newLine();

            $this->clearModel('Organization');
            $this->clearModel('Story');
            $this->clearModel('Resource');
            $this->clearModel('Event');

            $this->newLine();
            $this->info('✓ All indexes cleared successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Clear index for a specific model
     */
    private function clearModel(string $modelName): void
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelName} not found");
            return;
        }

        $this->info("Clearing {$modelName} index...");
        $modelClass::removeAllFromSearch();
        $this->info("  ✓ {$modelName} index cleared");
    }
}
