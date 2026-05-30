<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resource\StoreResourceRequest;
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

    private function authorizeResource(Resource $resource)
    {
        if ($resource->organization_id !== auth()->user()->organization_id) {
            abort(403, 'Unauthorized action.');
        }
    }
}