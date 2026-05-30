<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Organization;
use App\Services\ContentRevisionService;
use App\Services\SubmissionService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct(
        private SubmissionService $submissionService,
        private ContentRevisionService $revisionService,
        private NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $query = Submission::with(['submittable', 'submittedBy'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('submittable_type', $request->type);
        }

        $submissions = $query->paginate(15);

        // Get statistics
        $statistics = $this->submissionService->getStatistics();

        return view('secretary.submissions.index', compact('submissions', 'statistics'));
    }

    public function show(Submission $submission)
    {
        $submission->load(['submittable', 'submittedBy', 'reviewer']);

        $revision = null;
        $diff = null;

        // Get revision and diff for organization updates
        if ($submission->submittable_type === Organization::class) {
            $revision = $this->revisionService->getLatestRevision($submission->submittable);
            
            if ($revision) {
                $diff = $this->revisionService->getFormattedDiff($revision);
            }
        }

        return view('secretary.submissions.show', compact('submission', 'revision', 'diff'));
    }

    public function approve(Submission $submission)
    {
        try {
            // Use SubmissionService to approve
            $this->submissionService->approve($submission, auth()->user());

            // Apply content revision if it exists (for organization updates)
            if ($submission->submittable_type === Organization::class) {
                $revision = $this->revisionService->getLatestRevision($submission->submittable);
                
                if ($revision && $revision->status === 'pending') {
                    $this->revisionService->applyRevision($revision);
                }
            }

            // Send notification
            $this->notificationService->sendSubmissionStatusNotification($submission, 'approved');

            return redirect()->route('secretary.submissions.index')
                ->with('success', 'Submission approved and published successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve submission: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Submission $submission)
    {
        $request->validate([
            'reviewer_notes' => 'required|string|min:10',
        ]);

        try {
            // Use SubmissionService to reject
            $this->submissionService->reject(
                $submission,
                auth()->user(),
                $request->reviewer_notes
            );

            // Update submittable status
            $submission->submittable->update(['status' => 'rejected']);

            // Send notification
            $this->notificationService->sendSubmissionStatusNotification($submission, 'rejected');

            return redirect()->route('secretary.submissions.index')
                ->with('success', 'Submission rejected.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reject submission: ' . $e->getMessage());
        }
    }

    public function requestChanges(Request $request, Submission $submission)
    {
        $request->validate([
            'reviewer_notes' => 'required|string|min:10',
        ]);

        try {
            // Use SubmissionService to request changes
            $this->submissionService->requestChanges(
                $submission,
                auth()->user(),
                $request->reviewer_notes
            );

            // Update submittable status
            $submission->submittable->update(['status' => 'needs_changes']);

            // Send notification
            $this->notificationService->sendSubmissionStatusNotification($submission, 'needs_changes');

            return redirect()->route('secretary.submissions.index')
                ->with('success', 'Changes requested successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to request changes: ' . $e->getMessage());
        }
    }
}