<?php

namespace App\Listeners;

use App\Events\EventPublished;
use App\Mail\EventPublishedMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendEventPublishedNotification
{
    public function handle(EventPublished $event): void
    {
        $recipients = User::role('org_editor')->whereNotNull('email')->get();
        if ($recipients->isEmpty()) return;

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new EventPublishedMail($event->event, $recipient));
            } catch (\Exception $e) {
                logger()->error('Failed to send event published notification', [
                    'event_id' => $event->event->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}