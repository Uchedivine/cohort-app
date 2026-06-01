<?php

namespace App\Listeners;

use App\Events\SubmissionRejected;
use App\Mail\SubmissionRejectedMail;
use Illuminate\Support\Facades\Mail;

class SendSubmissionRejectedNotification
{
    public function handle(SubmissionRejected $event): void
    {
        $recipient = $event->submission->submittedBy;
        if (!$recipient || !$recipient->email) return;

        try {
            Mail::to($recipient)->send(new SubmissionRejectedMail($event->submission, $event->reviewer));
        } catch (\Exception $e) {
            logger()->error('Failed to send submission rejected notification', [
                'submission_id' => $event->submission->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}