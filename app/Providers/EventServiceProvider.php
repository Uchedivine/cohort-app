<?php

namespace App\Providers;

use App\Events\EventPublished;
use App\Events\OrganisationApplicationSubmitted;
use App\Events\SubmissionApproved;
use App\Events\SubmissionNeedsChanges;
use App\Events\SubmissionRejected;
use App\Events\SubmissionSubmitted;
use App\Listeners\LogSubmissionActivity;
use App\Listeners\NotifySecretaryOfNewApplication;
use App\Listeners\NotifySecretaryOfNewSubmission;
use App\Listeners\SendEventPublishedNotification;
use App\Listeners\SendSubmissionApprovedNotification;
use App\Listeners\SendSubmissionNeedsChangesNotification;
use App\Listeners\SendSubmissionRejectedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        SubmissionSubmitted::class => [
            NotifySecretaryOfNewSubmission::class,
        ],
        SubmissionApproved::class => [
            SendSubmissionApprovedNotification::class,
        ],
        SubmissionRejected::class => [
            SendSubmissionRejectedNotification::class,
        ],
        SubmissionNeedsChanges::class => [
            SendSubmissionNeedsChangesNotification::class,
        ],
        EventPublished::class => [
            SendEventPublishedNotification::class,
        ],
        OrganisationApplicationSubmitted::class => [
            NotifySecretaryOfNewApplication::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        LogSubmissionActivity::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
