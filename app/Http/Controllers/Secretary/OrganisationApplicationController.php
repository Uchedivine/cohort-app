<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganisationApplicationController extends Controller
{
    public function index()
    {
        $pending = Organization::with(['user'])
            ->where('status', 'pending')
            ->orderBy('applied_at')
            ->paginate(15);

        $recentlyReviewed = Organization::with(['user'])
            ->whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('applied_at')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('secretary.applications.index', compact('pending', 'recentlyReviewed'));
    }

    public function show(Organization $organization)
    {
        $organization->load(['user']);
        return view('secretary.applications.show', compact('organization'));
    }

    public function approve(Organization $organization)
    {
        $organization->update([
            'status'           => 'published',
            'rejection_reason' => null,
        ]);

        if ($organization->user) {
            try {
                \Mail::to($organization->user->email)
                    ->send(new \App\Mail\OrganisationApproved($organization));
            } catch (\Exception $e) {
                logger()->error('Failed to send approval email', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('secretary.applications.index')
            ->with('success', $organization->name . ' has been approved and is now live.');
    }

    public function reject(Request $request, Organization $organization)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $organization->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($organization->user) {
            try {
                \Mail::to($organization->user->email)
                    ->send(new \App\Mail\OrganisationRejected($organization));
            } catch (\Exception $e) {
                logger()->error('Failed to send rejection email', ['error' => $e->getMessage()]);
            }
        }

        return redirect()->route('secretary.applications.index')
            ->with('success', $organization->name . ' application has been rejected.');
    }

    public function requestChanges(Request $request, Organization $organization)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $organization->update([
            'status'           => 'needs_changes',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('secretary.applications.index')
            ->with('success', 'Changes requested for ' . $organization->name . '.');
    }
}