<?php

namespace App\Listeners;

use App\Events\SubmissionSubmitted;
use App\Mail\NewSubmissionMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifySecretaryOfNewSubmission implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(SubmissionSubmitted $event): void
    {
        // Get all secretary users
        $secretaries = User::role('secretary')
            ->whereNotNull('email')
            ->get();

        if ($secretaries->isEmpty()) {
            return;
        }

        foreach ($secretaries as $secretary) {
            Mail::to($secretary)->queue(
                new NewSubmissionMail($event->submission, $secretary)
            );
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(SubmissionSubmitted $event, \Throwable $exception): void
    {
        logger()->error('Failed to notify secretary of new submission', [
            'submission_id' => $event->submission->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
