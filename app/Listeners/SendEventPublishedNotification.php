<?php

namespace App\Listeners;

use App\Events\EventPublished;
use App\Mail\EventPublishedMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEventPublishedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(EventPublished $event): void
    {
        // Get all org editor users
        $recipients = User::role('org_editor')
            ->whereNotNull('email')
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(
                new EventPublishedMail($event->event, $recipient)
            );
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(EventPublished $event, \Throwable $exception): void
    {
        logger()->error('Failed to send event published notification', [
            'event_id' => $event->event->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
