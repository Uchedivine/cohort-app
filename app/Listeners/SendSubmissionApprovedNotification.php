<?php

namespace App\Listeners;

use App\Events\SubmissionApproved;
use App\Mail\SubmissionApprovedMail;
use Illuminate\Support\Facades\Mail;

class SendSubmissionApprovedNotification
{
    public function handle(SubmissionApproved $event): void
    {
        $recipient = $event->submission->submittedBy;
        if (!$recipient || !$recipient->email) return;

        try {
            Mail::to($recipient)->send(new SubmissionApprovedMail($event->submission, $event->reviewer));
        } catch (\Exception $e) {
            logger()->error('Failed to send submission approved notification', [
                'submission_id' => $event->submission->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}