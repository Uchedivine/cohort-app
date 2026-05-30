<?php

namespace App\Listeners;

use App\Events\SubmissionRejected;
use App\Mail\SubmissionRejectedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendSubmissionRejectedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubmissionRejected $event): void
    {
        $recipient = $event->submission->submittedBy;

        if (!$recipient || !$recipient->email) {
            return;
        }

        Mail::to($recipient)->queue(
            new SubmissionRejectedMail($event->submission, $event->reviewer)
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(SubmissionRejected $event, \Throwable $exception): void
    {
        logger()->error('Failed to send submission rejected notification', [
            'submission_id' => $event->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
