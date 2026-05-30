<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send monthly digest on the 1st of each month at 9:00 AM
        $schedule->command('digest:monthly')
            ->monthlyOn(1, '09:00')
            ->timezone('UTC');

        // Clean up old activity logs (older than 1 year)
        $schedule->command('activitylog:clean')
            ->monthly()
            ->timezone('UTC');

        // Clean up old temporary files (older than 90 days)
        $schedule->command('storage:cleanup --days=90')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->timezone('UTC');

        // Optimize images weekly
        $schedule->command('images:optimize --batch-size=100')
            ->weekly()
            ->saturdays()
            ->at('03:00')
            ->timezone('UTC');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
