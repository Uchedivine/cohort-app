<?php

namespace App\Listeners;

use App\Events\SubmissionSubmitted;
use App\Mail\NewSubmissionMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotifySecretaryOfNewSubmission
{
    public function handle(SubmissionSubmitted $event): void
    {
        $secretaries = User::role('secretary')->whereNotNull('email')->get();
        if ($secretaries->isEmpty()) return;

        foreach ($secretaries as $secretary) {
            try {
                Mail::to($secretary)->send(new NewSubmissionMail($event->submission, $secretary));
            } catch (\Exception $e) {
                logger()->error('Failed to notify secretary of new submission', [
                    'submission_id' => $event->submission->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}