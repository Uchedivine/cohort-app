<?php

namespace App\Listeners;

use App\Events\SubmissionApproved;
use App\Mail\SubmissionApprovedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendSubmissionApprovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubmissionApproved $event): void
    {
        $recipient = $event->submission->submittedBy;

        if (!$recipient || !$recipient->email) {
            return;
        }

        Mail::to($recipient)->queue(
            new SubmissionApprovedMail($event->submission, $event->reviewer)
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(SubmissionApproved $event, \Throwable $exception): void
    {
        logger()->error('Failed to send submission approved notification', [
            'submission_id' => $event->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
