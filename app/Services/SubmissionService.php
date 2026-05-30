<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SubmissionService
{
    /**
     * Valid submission statuses
     */
    private const STATUS_DRAFT = 'draft';
    private const STATUS_SUBMITTED = 'submitted';
    private const STATUS_NEEDS_CHANGES = 'needs_changes';
    private const STATUS_APPROVED = 'approved';
    private const STATUS_REJECTED = 'rejected';

    /**
     * Valid status transitions
     */
    private const VALID_TRANSITIONS = [
        self::STATUS_DRAFT => [self::STATUS_SUBMITTED],
        self::STATUS_SUBMITTED => [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_NEEDS_CHANGES],
        self::STATUS_NEEDS_CHANGES => [self::STATUS_SUBMITTED],
        self::STATUS_APPROVED => [],
        self::STATUS_REJECTED => [],
    ];

    /**
     * Create a new submission
     *
     * @param Model $submittable
     * @param User $submittedBy
     * @param string $status
     * @return Submission
     */
    public function createSubmission(
        Model $submittable,
        User $submittedBy,
        string $status = self::STATUS_DRAFT
    ): Submission {
        $this->validateStatus($status);

        return DB::transaction(function () use ($submittable, $submittedBy, $status) {
            $submission = Submission::create([
                'submittable_type' => get_class($submittable),
                'submittable_id' => $submittable->id,
                'submitted_by' => $submittedBy->id,
                'status' => $status,
                'submitted_at' => $status === self::STATUS_SUBMITTED ? now() : null,
            ]);

            // Log activity
            activity()
                ->performedOn($submission)
                ->causedBy($submittedBy)
                ->withProperties(['status' => $status])
                ->log('Submission created');

            return $submission;
        });
    }

    /**
     * Submit a draft for review
     *
     * @param Submission $submission
     * @return Submission
     */
    public function submit(Submission $submission): Submission
    {
        $this->validateTransition($submission->status, self::STATUS_SUBMITTED);

        return DB::transaction(function () use ($submission) {
            $submission->update([
                'status' => self::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($submission)
                ->causedBy(auth()->user())
                ->withProperties(['status' => self::STATUS_SUBMITTED])
                ->log('Submission submitted for review');

            // Fire event for notifications
            event(new \App\Events\SubmissionSubmitted($submission));

            return $submission->fresh();
        });
    }

    /**
     * Approve a submission
     *
     * @param Submission $submission
     * @param User $reviewer
     * @param string|null $notes
     * @return Submission
     */
    public function approve(Submission $submission, User $reviewer, ?string $notes = null): Submission
    {
        $this->validateTransition($submission->status, self::STATUS_APPROVED);

        return DB::transaction(function () use ($submission, $reviewer, $notes) {
            // Update submission status
            $submission->update([
                'status' => self::STATUS_APPROVED,
                'reviewer_id' => $reviewer->id,
                'reviewer_notes' => $notes,
                'reviewed_at' => now(),
            ]);

            // Publish the submittable content
            $this->publishContent($submission);

            // Log activity
            activity()
                ->performedOn($submission)
                ->causedBy($reviewer)
                ->withProperties([
                    'status' => self::STATUS_APPROVED,
                    'notes' => $notes,
                ])
                ->log('Submission approved');

            // Fire event for notifications
            event(new \App\Events\SubmissionApproved($submission, $reviewer));

            return $submission->fresh();
        });
    }

    /**
     * Reject a submission
     *
     * @param Submission $submission
     * @param User $reviewer
     * @param string $reason
     * @return Submission
     */
    public function reject(Submission $submission, User $reviewer, string $reason): Submission
    {
        $this->validateTransition($submission->status, self::STATUS_REJECTED);

        return DB::transaction(function () use ($submission, $reviewer, $reason) {
            $submission->update([
                'status' => self::STATUS_REJECTED,
                'reviewer_id' => $reviewer->id,
                'reviewer_notes' => $reason,
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($submission)
                ->causedBy($reviewer)
                ->withProperties([
                    'status' => self::STATUS_REJECTED,
                    'reason' => $reason,
                ])
                ->log('Submission rejected');

            // Fire event for notifications
            event(new \App\Events\SubmissionRejected($submission, $reviewer));

            return $submission->fresh();
        });
    }

    /**
     * Request changes to a submission
     *
     * @param Submission $submission
     * @param User $reviewer
     * @param string $feedback
     * @return Submission
     */
    public function requestChanges(Submission $submission, User $reviewer, string $feedback): Submission
    {
        $this->validateTransition($submission->status, self::STATUS_NEEDS_CHANGES);

        return DB::transaction(function () use ($submission, $reviewer, $feedback) {
            $submission->update([
                'status' => self::STATUS_NEEDS_CHANGES,
                'reviewer_id' => $reviewer->id,
                'reviewer_notes' => $feedback,
                'reviewed_at' => now(),
            ]);

            // Log activity
            activity()
                ->performedOn($submission)
                ->causedBy($reviewer)
                ->withProperties([
                    'status' => self::STATUS_NEEDS_CHANGES,
                    'feedback' => $feedback,
                ])
                ->log('Changes requested');

            // Fire event for notifications
            event(new \App\Events\SubmissionNeedsChanges($submission, $reviewer));

            return $submission->fresh();
        });
    }

    /**
     * Publish the submittable content
     *
     * @param Submission $submission
     * @return void
     */
    private function publishContent(Submission $submission): void
    {
        $submittable = $submission->submittable;

        if (!$submittable) {
            return;
        }

        // Set published status and timestamp
        if (method_exists($submittable, 'publish')) {
            $submittable->publish();
        } else {
            $submittable->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
        }
    }

    /**
     * Get submissions by status
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubmissionsByStatus(string $status)
    {
        $this->validateStatus($status);

        return Submission::with(['submittable', 'submittedBy', 'reviewer'])
            ->where('status', $status)
            ->latest()
            ->get();
    }

    /**
     * Get pending submissions (submitted but not reviewed)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingSubmissions()
    {
        return Submission::with(['submittable', 'submittedBy'])
            ->where('status', self::STATUS_SUBMITTED)
            ->latest('submitted_at')
            ->get();
    }

    /**
     * Get submissions for a specific user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserSubmissions(User $user)
    {
        return Submission::with(['submittable', 'reviewer'])
            ->where('submitted_by', $user->id)
            ->latest()
            ->get();
    }

    /**
     * Get submissions for a specific organization
     *
     * @param int $organizationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrganizationSubmissions(int $organizationId)
    {
        return Submission::with(['submittable', 'submittedBy', 'reviewer'])
            ->whereHas('submittedBy', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->latest()
            ->get();
    }

    /**
     * Get submission statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total' => Submission::count(),
            'draft' => Submission::where('status', self::STATUS_DRAFT)->count(),
            'submitted' => Submission::where('status', self::STATUS_SUBMITTED)->count(),
            'needs_changes' => Submission::where('status', self::STATUS_NEEDS_CHANGES)->count(),
            'approved' => Submission::where('status', self::STATUS_APPROVED)->count(),
            'rejected' => Submission::where('status', self::STATUS_REJECTED)->count(),
        ];
    }

    /**
     * Validate status
     *
     * @param string $status
     * @throws InvalidArgumentException
     */
    private function validateStatus(string $status): void
    {
        $validStatuses = [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_NEEDS_CHANGES,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new InvalidArgumentException("Invalid status: {$status}");
        }
    }

    /**
     * Validate status transition
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @throws InvalidArgumentException
     */
    private function validateTransition(string $currentStatus, string $newStatus): void
    {
        if (!isset(self::VALID_TRANSITIONS[$currentStatus])) {
            throw new InvalidArgumentException("Invalid current status: {$currentStatus}");
        }

        if (!in_array($newStatus, self::VALID_TRANSITIONS[$currentStatus])) {
            throw new InvalidArgumentException(
                "Invalid transition from {$currentStatus} to {$newStatus}"
            );
        }
    }

    /**
     * Check if a submission can be edited
     *
     * @param Submission $submission
     * @return bool
     */
    public function canEdit(Submission $submission): bool
    {
        return in_array($submission->status, [
            self::STATUS_DRAFT,
            self::STATUS_NEEDS_CHANGES,
        ]);
    }

    /**
     * Check if a submission can be submitted
     *
     * @param Submission $submission
     * @return bool
     */
    public function canSubmit(Submission $submission): bool
    {
        return in_array($submission->status, [
            self::STATUS_DRAFT,
            self::STATUS_NEEDS_CHANGES,
        ]);
    }

    /**
     * Check if a submission can be reviewed
     *
     * @param Submission $submission
     * @return bool
     */
    public function canReview(Submission $submission): bool
    {
        return $submission->status === self::STATUS_SUBMITTED;
    }
}
