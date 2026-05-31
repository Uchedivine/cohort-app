<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Story\StoreStoryRequest;
use App\Http\Requests\Story\UpdateStoryRequest;
use App\Models\Story;
use App\Models\Tag;
use App\Services\FileUploadService;
use App\Services\SubmissionService;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private SubmissionService $submissionService
    ) {}

    public function index()
    {
        $stories = Story::where('organization_id', auth()->user()->organization_id)
            ->with(['submissions', 'tags'])
            ->latest()
            ->paginate(10);

        return view('org-editor.stories.index', compact('stories'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('org-editor.stories.create', compact('tags'));
    }

    public function store(StoreStoryRequest $request)
    {
        $validated = $request->validated();

        // Handle featured image upload using FileUploadService
        if ($request->hasFile('featured_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'),
                    'stories',
                    true // Create thumbnail
                );
                $validated['featured_image'] = $uploadResult['path'];
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['featured_image' => $e->getMessage()])->withInput();
            }
        }

        // Set organization and user
        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        
        // Determine status based on action
        $action = $request->input('action', 'submit');
        $validated['status'] = $action === 'draft' ? 'draft' : 'submitted';

        // Create story
        $story = Story::create($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $story->tags()->sync($request->tags);
        }

        // Create submission using SubmissionService
        $submission = $this->submissionService->createSubmission(
            $story,
            auth()->user(),
            $validated['status']
        );

        // Log activity
        activity()
            ->performedOn($story)
            ->causedBy(auth()->user())
            ->withProperties(['submission_id' => $submission->id])
            ->log('Story created and submitted for review');

        $message = $action === 'draft' ? 'Story saved as draft.' : 'Story submitted for review.';

        return redirect()->route('org-editor.stories.index')
            ->with('success', $message);
    }

    public function edit(Story $story)
    {
        $this->authorizeStory($story);
        $tags = Tag::all();
        return view('org-editor.stories.edit', compact('story', 'tags'));
    }

    public function update(UpdateStoryRequest $request, Story $story)
    {
        $this->authorizeStory($story);

        $validated = $request->validated();

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'),
                    'stories',
                    true
                );
                $validated['featured_image'] = $uploadResult['path'];

                // Delete old image
                if ($story->featured_image) {
                    $this->fileUploadService->deleteFile($story->featured_image);
                }
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['featured_image' => $e->getMessage()])->withInput();
            }
        }

        // Determine status based on action
        $action = $request->input('action', 'submit');
        $validated['status'] = $action === 'draft' ? 'draft' : 'submitted';

        // Update story
        $story->update($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $story->tags()->sync($request->tags);
        }

        // Create new submission if submitting for review
        if ($action === 'submit') {
            $submission = $this->submissionService->createSubmission(
                $story,
                auth()->user(),
                'submitted'
            );

            activity()
                ->performedOn($story)
                ->causedBy(auth()->user())
                ->withProperties(['submission_id' => $submission->id])
                ->log('Story updated and resubmitted for review');
        }

        $message = $action === 'draft' ? 'Story saved as draft.' : 'Story resubmitted for review.';

        return redirect()->route('org-editor.stories.index')
            ->with('success', $message);
    }

    /**
     * Resubmit a rejected story
     */
    public function resubmit(Story $story)
    {
        $this->authorizeStory($story);

        // Verify story is rejected
        if ($story->status !== 'rejected') {
            return redirect()->route('org-editor.stories.index')
                ->with('error', 'Only rejected stories can be resubmitted.');
        }

        // Get the rejected submission
        $rejectedSubmission = $story->submissions()
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if (!$rejectedSubmission || !$rejectedSubmission->allow_resubmission) {
            return redirect()->route('org-editor.stories.index')
                ->with('error', 'This story is not eligible for resubmission.');
        }

        try {
            // Create new submission linked to rejected one
            $newSubmission = $this->submissionService->resubmit(
                $story,
                $rejectedSubmission,
                auth()->user()
            );

            return redirect()->route('org-editor.stories.index')
                ->with('success', 'Story resubmitted for review.');
        } catch (\Exception $e) {
            return redirect()->route('org-editor.stories.index')
                ->with('error', 'Failed to resubmit story: ' . $e->getMessage());
        }
    }

    private function authorizeStory(Story $story)
    {
        if ($story->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}