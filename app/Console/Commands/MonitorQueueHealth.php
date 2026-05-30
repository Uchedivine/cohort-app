<?php

namespace App\Console\Commands;

use App\Services\JobMonitorService;
use Illuminate\Console\Command;

class MonitorQueueHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check queue health and display statistics';

    /**
     * Execute the console command.
     */
    public function handle(JobMonitorService $monitor): int
    {
        $this->info('Checking queue health...');
        $this->newLine();

        $health = $monitor->getHealthStatus();

        // Display status
        if ($health['healthy']) {
            $this->info('✓ Queue Status: HEALTHY');
        } else {
            $this->error('✗ Queue Status: UNHEALTHY');
        }

        $this->newLine();

        // Display statistics
        $stats = $health['statistics'];
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Pending Jobs', $stats['pending']],
                ['Failed Jobs', $stats['failed']],
                ['Processed Today', $stats['processed_today']],
                ['Avg Wait Time', $stats['average_wait_time'] . 's'],
            ]
        );

        // Show warnings
        if ($stats['pending'] > 500) {
            $this->warn('⚠ High number of pending jobs. Consider adding more workers.');
        }

        if ($stats['failed'] > 50) {
            $this->warn('⚠ High number of failed jobs. Review error logs.');
        }

        // Show recent failed jobs if any
        if ($stats['failed'] > 0) {
            $this->newLine();
            $this->info('Recent Failed Jobs:');
            
            $failedJobs = $monitor->getRecentFailedJobs(5);
            
            if ($failedJobs->isNotEmpty()) {
                $rows = $failedJobs->map(function ($job) {
                    return [
                        substr($job->uuid, 0, 8),
                        $job->queue,
                        \Carbon\Carbon::parse($job->failed_at)->diffForHumans(),
                    ];
                });

                $this->table(
                    ['ID', 'Queue', 'Failed'],
                    $rows
                );

                $this->newLine();
                $this->info('Retry failed jobs with: php artisan queue:retry all');
            }
        }

        return $health['healthy'] ? Command::SUCCESS : Command::FAILURE;
    }
}
