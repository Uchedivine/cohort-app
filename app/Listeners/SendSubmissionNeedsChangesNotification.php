<?php

namespace App\Listeners;

use App\Events\SubmissionNeedsChanges;
use App\Mail\SubmissionNeedsChangesMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendSubmissionNeedsChangesNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubmissionNeedsChanges $event): void
    {
        $recipient = $event->submission->submittedBy;

        if (!$recipient || !$recipient->email) {
            return;
        }

        Mail::to($recipient)->queue(
            new SubmissionNeedsChangesMail($event->submission, $event->reviewer)
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(SubmissionNeedsChanges $event, \Throwable $exception): void
    {
        logger()->error('Failed to send submission needs changes notification', [
            'submission_id' => $event->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
