<?php

namespace App\Listeners;

use App\Events\SubmissionApproved;
use App\Events\SubmissionRejected;
use App\Events\SubmissionSubmitted;
use App\Events\SubmissionNeedsChanges;

class LogSubmissionActivity
{
    /**
     * Handle submission submitted event.
     */
    public function handleSubmitted(SubmissionSubmitted $event): void
    {
        activity()
            ->performedOn($event->submission)
            ->causedBy($event->submission->submittedBy)
            ->withProperties([
                'status' => 'submitted',
                'submittable_type' => class_basename($event->submission->submittable_type),
            ])
            ->log('Submission submitted for review');
    }

    /**
     * Handle submission approved event.
     */
    public function handleApproved(SubmissionApproved $event): void
    {
        activity()
            ->performedOn($event->submission)
            ->causedBy($event->reviewer)
            ->withProperties([
                'status' => 'approved',
                'submittable_type' => class_basename($event->submission->submittable_type),
            ])
            ->log('Submission approved and published');
    }

    /**
     * Handle submission rejected event.
     */
    public function handleRejected(SubmissionRejected $event): void
    {
        activity()
            ->performedOn($event->submission)
            ->causedBy($event->reviewer)
            ->withProperties([
                'status' => 'rejected',
                'reason' => $event->submission->reviewer_notes,
            ])
            ->log('Submission rejected');
    }

    /**
     * Handle submission needs changes event.
     */
    public function handleNeedsChanges(SubmissionNeedsChanges $event): void
    {
        activity()
            ->performedOn($event->submission)
            ->causedBy($event->reviewer)
            ->withProperties([
                'status' => 'needs_changes',
                'feedback' => $event->submission->reviewer_notes,
            ])
            ->log('Changes requested on submission');
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            SubmissionSubmitted::class => 'handleSubmitted',
            SubmissionApproved::class => 'handleApproved',
            SubmissionRejected::class => 'handleRejected',
            SubmissionNeedsChanges::class => 'handleNeedsChanges',
        ];
    }
}
