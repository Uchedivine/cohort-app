<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tag;
use App\Services\FileUploadService;
use App\Services\SubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private SubmissionService $submissionService
    ) {}

    public function index()
    {
        $events = Event::where('organization_id', auth()->user()->organization_id)
            ->with(['submissions', 'tags'])
            ->latest()
            ->paginate(10);

        return view('org-editor.events.index', compact('events'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('org-editor.events.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'virtual_link' => 'nullable|url|max:255',
            'rsvp_link' => 'nullable|url|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sdgs' => 'nullable|array',
            'sdgs.*' => 'integer|between:1,17',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('banner_image'),
                    'events',
                    true // Create thumbnail
                );
                $validated['banner_image'] = $uploadResult['path'];
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['banner_image' => $e->getMessage()])->withInput();
            }
        }

        // Set organization and user
        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'submitted';

        // Create event
        $event = Event::create($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $event->tags()->sync($request->tags);
        }

        // Create submission using SubmissionService
        $submission = $this->submissionService->createSubmission(
            $event,
            auth()->user(),
            'submitted'
        );

        // Log activity
        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->withProperties(['submission_id' => $submission->id])
            ->log('Event created and submitted for review');

        return redirect()->route('org-editor.events.index')
            ->with('success', 'Event submitted for review.');
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        // Only allow editing if status is draft or needs_changes
        if (!in_array($event->status, ['draft', 'needs_changes', 'submitted'])) {
            return redirect()->route('org-editor.events.index')
                ->with('error', 'Cannot edit an event that has been published.');
        }

        $tags = Tag::all();
        return view('org-editor.events.edit', compact('event', 'tags'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        // Only allow updating if status is draft or needs_changes
        if (!in_array($event->status, ['draft', 'needs_changes', 'submitted'])) {
            return redirect()->route('org-editor.events.index')
                ->with('error', 'Cannot update an event that has been published.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'virtual_link' => 'nullable|url|max:255',
            'rsvp_link' => 'nullable|url|max:255',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sdgs' => 'nullable|array',
            'sdgs.*' => 'integer|between:1,17',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('banner_image'),
                    'events',
                    true
                );
                $validated['banner_image'] = $uploadResult['path'];

                // Delete old image
                if ($event->banner_image) {
                    $this->fileUploadService->deleteFile($event->banner_image);
                }
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['banner_image' => $e->getMessage()])->withInput();
            }
        }

        // Update slug if title changed
        if (isset($validated['title']) && $validated['title'] !== $event->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        // Set status to submitted
        $validated['status'] = 'submitted';

        // Update event
        $event->update($validated);

        // Sync tags
        if ($request->has('tags')) {
            $event->tags()->sync($request->tags ?? []);
        }

        // Create new submission
        $submission = $this->submissionService->createSubmission(
            $event,
            auth()->user(),
            'submitted'
        );

        activity()
            ->performedOn($event)
            ->causedBy(auth()->user())
            ->withProperties(['submission_id' => $submission->id])
            ->log('Event updated and resubmitted for review');

        return redirect()->route('org-editor.events.index')
            ->with('success', 'Event resubmitted for review.');
    }

    /**
     * Resubmit a rejected event
     */
    public function resubmit(Event $event)
    {
        $this->authorizeEvent($event);

        // Verify event is rejected
        if ($event->status !== 'rejected') {
            return redirect()->route('org-editor.events.index')
                ->with('error', 'Only rejected events can be resubmitted.');
        }

        // Get the rejected submission
        $rejectedSubmission = $event->submissions()
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if (!$rejectedSubmission || !$rejectedSubmission->allow_resubmission) {
            return redirect()->route('org-editor.events.index')
                ->with('error', 'This event is not eligible for resubmission.');
        }

        try {
            // Create new submission linked to rejected one
            $newSubmission = $this->submissionService->resubmit(
                $event,
                $rejectedSubmission,
                auth()->user()
            );

            return redirect()->route('org-editor.events.index')
                ->with('success', 'Event resubmitted for review.');
        } catch (\Exception $e) {
            return redirect()->route('org-editor.events.index')
                ->with('error', 'Failed to resubmit event: ' . $e->getMessage());
        }
    }

    private function authorizeEvent(Event $event)
    {
        if ($event->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}
