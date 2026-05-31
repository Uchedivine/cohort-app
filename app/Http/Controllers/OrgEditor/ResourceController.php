<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\StoreResourceRequest;
use App\Http\Requests\Resource\UpdateResourceRequest;
use App\Models\Resource;
use App\Models\Tag;
use App\Services\FileUploadService;
use App\Services\SubmissionService;
use Illuminate\Support\Str;

class ResourceController extends Controller
{
    public function __construct(
        private FileUploadService $fileUploadService,
        private SubmissionService $submissionService
    ) {}

    public function index()
    {
        $resources = Resource::where('organization_id', auth()->user()->organization_id)
            ->with(['submissions', 'tags'])
            ->latest()
            ->paginate(10);

        return view('org-editor.resources.index', compact('resources'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('org-editor.resources.create', compact('tags'));
    }

    public function store(StoreResourceRequest $request)
    {
        $validated = $request->validated();

        // Handle file upload using FileUploadService
        if ($request->hasFile('file_path')) {
            try {
                $filePath = $this->fileUploadService->uploadDocument(
                    $request->file('file_path'),
                    'resources'
                );
                $validated['file_path'] = $filePath;
                $validated['mime_type'] = $request->file('file_path')->getMimeType();
                $validated['file_size'] = $request->file('file_path')->getSize();
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['file_path' => $e->getMessage()])->withInput();
            }
        }

        // Set organization and user
        $validated['organization_id'] = auth()->user()->organization_id;
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'submitted';

        // Create resource
        $resource = Resource::create($validated);

        // Sync tags
        if ($request->filled('tags')) {
            $resource->tags()->sync($request->tags);
        }

        // Create submission using SubmissionService
        $submission = $this->submissionService->createSubmission(
            $resource,
            auth()->user(),
            'submitted'
        );

        // Log activity
        activity()
            ->performedOn($resource)
            ->causedBy(auth()->user())
            ->withProperties(['submission_id' => $submission->id])
            ->log('Resource created and submitted for review');

        return redirect()->route('org-editor.resources.index')
            ->with('success', 'Resource submitted for review.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        // Authorization: Verify resource belongs to user's organization
        $this->authorizeResource($resource);

        // Only allow editing if status is draft or needs_changes
        if (!in_array($resource->status, ['draft', 'needs_changes'])) {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'Cannot edit a resource that has been submitted or published.');
        }

        $tags = Tag::all();
        return view('org-editor.resources.edit', compact('resource', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        // Authorization: Verify resource belongs to user's organization
        $this->authorizeResource($resource);

        // Only allow updating if status is draft or needs_changes
        if (!in_array($resource->status, ['draft', 'needs_changes'])) {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'Cannot update a resource that has been submitted or published.');
        }

        $validated = $request->validated();

        // Handle file upload if new file provided
        if ($request->hasFile('file_path')) {
            try {
                // Delete old file if exists
                if ($resource->file_path) {
                    $this->fileUploadService->deleteFile($resource->file_path);
                }

                $filePath = $this->fileUploadService->uploadDocument(
                    $request->file('file_path'),
                    'resources'
                );
                $validated['file_path'] = $filePath;
                $validated['mime_type'] = $request->file('file_path')->getMimeType();
                $validated['file_size'] = $request->file('file_path')->getSize();
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['file_path' => $e->getMessage()])->withInput();
            }
        }

        // Map form 'type' to database 'resource_type'
        $typeMap = [
            'file' => 'file',
            'link' => 'external_link',
            'video' => 'video_link',
        ];
        $validated['resource_type'] = $typeMap[$validated['type']];
        unset($validated['type']);

        // Update slug if title changed
        if (isset($validated['title']) && $validated['title'] !== $resource->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        }

        // Update resource
        $resource->update($validated);

        // Sync tags
        if ($request->has('tags')) {
            $resource->tags()->sync($request->tags ?? []);
        }

        // Log activity
        activity()
            ->performedOn($resource)
            ->causedBy(auth()->user())
            ->log('Resource updated');

        return redirect()->route('org-editor.resources.index')
            ->with('success', 'Resource updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        // Authorization: Verify resource belongs to user's organization
        $this->authorizeResource($resource);

        // Only allow deletion if status is draft or needs_changes
        if (!in_array($resource->status, ['draft', 'needs_changes'])) {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'Cannot delete a resource that has been submitted or published.');
        }

        // Delete associated file if exists
        if ($resource->file_path) {
            try {
                $this->fileUploadService->deleteFile($resource->file_path);
            } catch (\Exception $e) {
                // Log error but continue with deletion
                \Log::warning('Failed to delete resource file: ' . $e->getMessage());
            }
        }

        // Delete resource (cascade will handle submissions)
        $resource->delete();

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['resource_id' => $resource->id, 'title' => $resource->title])
            ->log('Resource deleted');

        return redirect()->route('org-editor.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }

    /**
     * Resubmit a rejected resource
     */
    public function resubmit(Resource $resource)
    {
        // Authorization: Verify resource belongs to user's organization
        $this->authorizeResource($resource);

        // Verify resource is rejected
        if ($resource->status !== 'rejected') {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'Only rejected resources can be resubmitted.');
        }

        // Get the rejected submission
        $rejectedSubmission = $resource->submissions()
            ->where('status', 'rejected')
            ->latest()
            ->first();

        if (!$rejectedSubmission || !$rejectedSubmission->allow_resubmission) {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'This resource is not eligible for resubmission.');
        }

        try {
            // Create new submission linked to rejected one
            $newSubmission = $this->submissionService->resubmit(
                $resource,
                $rejectedSubmission,
                auth()->user()
            );

            return redirect()->route('org-editor.resources.index')
                ->with('success', 'Resource resubmitted for review.');
        } catch (\Exception $e) {
            return redirect()->route('org-editor.resources.index')
                ->with('error', 'Failed to resubmit resource: ' . $e->getMessage());
        }
    }

    /**
     * Authorize that the resource belongs to the user's organization.
     *
     * @param Resource $resource
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function authorizeResource(Resource $resource)
    {
        if ($resource->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}