<?php

namespace App\Listeners;

use App\Events\SubmissionNeedsChanges;
use App\Mail\SubmissionNeedsChangesMail;
use Illuminate\Support\Facades\Mail;

class SendSubmissionNeedsChangesNotification
{
    public function handle(SubmissionNeedsChanges $event): void
    {
        $recipient = $event->submission->submittedBy;
        if (!$recipient || !$recipient->email) return;

        try {
            Mail::to($recipient)->send(new SubmissionNeedsChangesMail($event->submission, $event->reviewer));
        } catch (\Exception $e) {
            logger()->error('Failed to send submission needs changes notification', [
                'submission_id' => $event->submission->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}