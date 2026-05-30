<?php

namespace App\Console\Commands;

use App\Jobs\CleanupOldFiles;
use Illuminate\Console\Command;

class CleanupStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:cleanup 
                            {--days=90 : Delete files older than this many days}
                            {--now : Run cleanup immediately instead of queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old temporary files from storage';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $runNow = $this->option('now');

        $this->info("Cleaning up files older than {$days} days...");

        if ($runNow) {
            $this->info('Running cleanup immediately...');
            CleanupOldFiles::dispatchSync($days);
            $this->info('Cleanup completed!');
        } else {
            $this->info('Queuing cleanup job...');
            CleanupOldFiles::dispatch($days);
            $this->info('Cleanup job queued successfully!');
        }

        return Command::SUCCESS;
    }
}
