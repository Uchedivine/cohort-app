<?php

namespace App\Console\Commands;

use App\Models\Organization;
use App\Models\Story;
use App\Models\Resource;
use App\Models\Event;
use Illuminate\Console\Command;

class ReindexSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex 
                            {model? : Specific model to reindex (Organization, Story, Resource, Event)}
                            {--flush : Flush existing index before reindexing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindex all searchable models for Scout';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $model = $this->argument('model');
        $flush = $this->option('flush');

        if ($model) {
            $this->reindexModel($model, $flush);
        } else {
            $this->info('Reindexing all searchable models...');
            $this->newLine();

            $this->reindexModel('Organization', $flush);
            $this->reindexModel('Story', $flush);
            $this->reindexModel('Resource', $flush);
            $this->reindexModel('Event', $flush);

            $this->newLine();
            $this->info('✓ All models reindexed successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Reindex a specific model
     */
    private function reindexModel(string $modelName, bool $flush): void
    {
        $modelClass = "App\\Models\\{$modelName}";

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelName} not found");
            return;
        }

        $this->info("Reindexing {$modelName}...");

        if ($flush) {
            $this->line("  Flushing existing index...");
            $modelClass::removeAllFromSearch();
        }

        $count = $modelClass::published()->count();
        
        if ($count === 0) {
            $this->warn("  No published records found");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $modelClass::published()->chunk(100, function ($records) use ($bar) {
            $records->searchable();
            $bar->advance($records->count());
        });

        $bar->finish();
        $this->newLine();
        $this->info("  ✓ Indexed {$count} records");
    }
}
