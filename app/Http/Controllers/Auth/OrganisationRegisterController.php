<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Events\OrganisationApplicationSubmitted;
use Illuminate\Validation\Rules\Password;

class OrganisationRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register-organisation');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'org_name'        => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'location'        => 'required|string|max:255',
            'thematic_focus'  => 'required|string|max:255',
            'website'         => 'nullable|url',
            'why_join'        => 'required|string|max:1000',
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => ['required', 'confirmed', Password::min(8)
                                    ->mixedCase()
                                    ->numbers()
                                    ->symbols()],
        ]);

        DB::transaction(function () use ($validated) {
            // Create the organization
            $organization = Organization::create([
                'name'             => $validated['org_name'],
                'slug'             => Str::slug($validated['org_name']) . '-' . Str::random(5),
                'short_description'=> $validated['short_description'],
                'location'         => $validated['location'],
                'thematic_focus'   => $validated['thematic_focus'],
                'website'          => $validated['website'] ?? null,
                'highlights'       => $validated['why_join'],
                'status'           => 'pending',
                'applied_at'       => now(),
                'user_id'          => 1, // temp, updated below
            ]);

            // Create the user
            $user = User::create([
                'name'            => $validated['name'],
                'email'           => $validated['email'],
                'password'        => Hash::make($validated['password']),
                'organization_id' => $organization->id,
            ]);

            // Assign org_editor role
            $user->assignRole('org_editor');

            // Update org user_id to the actual user
            $organization->update(['user_id' => $user->id]);

            // Dispatch event to notify secretary
            OrganisationApplicationSubmitted::dispatch($organization);

            // Log them in
            auth()->login($user);
        });

        return redirect()->route('org-editor.pending')
            ->with('success', 'Your application has been submitted. We will review it shortly.');
    }

    public function reapply(Request $request)
    {
        $organization = auth()->user()->organization;

        if (!$organization || $organization->status !== 'rejected') {
            return redirect()->route('org-editor.dashboard');
        }

        $validated = $request->validate([
            'org_name'         => 'required|string|max:255',
            'short_description'=> 'required|string|max:500',
            'location'         => 'required|string|max:255',
            'thematic_focus'   => 'required|string|max:255',
            'website'          => 'nullable|url',
            'why_join'         => 'required|string|max:1000',
        ]);

        $organization->update([
            'name'              => $validated['org_name'],
            'short_description' => $validated['short_description'],
            'location'          => $validated['location'],
            'thematic_focus'    => $validated['thematic_focus'],
            'website'           => $validated['website'] ?? null,
            'highlights'        => $validated['why_join'],
            'status'            => 'pending',
            'rejection_reason'  => null,
            'applied_at'        => now(),
        ]);

        return redirect()->route('org-editor.pending')
            ->with('success', 'Your reapplication has been submitted for review.');
    }
}