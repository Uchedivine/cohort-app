<?php

namespace App\Http\Controllers\OrgEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\UpdateOrganizationRequest;
use App\Services\ContentRevisionService;
use App\Services\FileUploadService;
use App\Services\SubmissionService;

class OrganizationController extends Controller
{
    public function __construct(
        private ContentRevisionService $revisionService,
        private FileUploadService $fileUploadService,
        private SubmissionService $submissionService
    ) {}

    public function edit()
{
    $organization = auth()->user()->organization;

    if (!$organization) {
        return redirect()->route('org-editor.dashboard')
            ->with('error', 'No organization assigned to your account.');
    }

    $pendingSubmission = $organization->submissions()
        ->whereIn('status', ['submitted', 'needs_changes', 'approved'])
        ->latest()
        ->first();

    return view('org-editor.organization.edit', compact('organization', 'pendingSubmission'));
}

    public function update(UpdateOrganizationRequest $request)
    {
        $organization = auth()->user()->organization;

        if (!$organization) {
            return redirect()->route('org-editor.dashboard')
                ->with('error', 'No organization assigned to your account.');
        }

        $validated = $request->validated();

        // Handle logo upload using FileUploadService
        if ($request->hasFile('logo')) {
            try {
                $uploadResult = $this->fileUploadService->uploadImage(
                    $request->file('logo'),
                    'logos',
                    false
                );
                $validated['logo'] = $uploadResult['path'];

                // Delete old logo if exists
                if ($organization->logo) {
                    $this->fileUploadService->deleteFile($organization->logo);
                }
            } catch (\InvalidArgumentException $e) {
                return back()->withErrors(['logo' => $e->getMessage()])->withInput();
            }
        }

        // Create content revision snapshot
        $revision = $this->revisionService->createSnapshot($organization, $validated);

        // Create submission using SubmissionService
        $submission = $this->submissionService->createSubmission(
            $organization,
            auth()->user(),
            'submitted'
        );

        // Update organization status
        $organization->update(['status' => 'submitted']);

        // Log activity
        activity()
            ->performedOn($organization)
            ->causedBy(auth()->user())
            ->withProperties(['submission_id' => $submission->id])
            ->log('Organization profile update submitted for review');

        return redirect()->route('org-editor.organization.edit')
            ->with('success', 'Your changes have been submitted for review.');
    }
}