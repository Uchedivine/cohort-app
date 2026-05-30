<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Story;
use App\Models\Resource;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendMonthlyDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'digest:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly digest email to all org editors';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $this->info('Preparing monthly digest...');

        // Get data from the past month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Get upcoming events for next month
        $events = Event::published()
            ->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addMonth())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Get recent stories from this month
        $stories = Story::published()
            ->whereBetween('published_at', [$startOfMonth, $endOfMonth])
            ->latest('published_at')
            ->take(5)
            ->get();

        // Get statistics
        $statistics = [
            'stories_count' => Story::whereBetween('published_at', [$startOfMonth, $endOfMonth])->count(),
            'resources_count' => Resource::whereBetween('published_at', [$startOfMonth, $endOfMonth])->count(),
            'events_count' => Event::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
        ];

        // Prepare digest data
        $digestData = [
            'events' => $events,
            'stories' => $stories,
            'statistics' => $statistics,
        ];

        // Send digest
        $notificationService->sendMonthlyDigest($digestData);

        $this->info('Monthly digest sent successfully!');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Stories Published', $statistics['stories_count']],
                ['Resources Added', $statistics['resources_count']],
                ['Events Created', $statistics['events_count']],
                ['Upcoming Events', $events->count()],
                ['Featured Stories', $stories->count()],
            ]
        );

        return Command::SUCCESS;
    }
}
