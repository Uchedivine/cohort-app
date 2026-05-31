<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrgEditorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Must be logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Must have org_editor role
        if (!auth()->user()->hasRole('org_editor')) {
            abort(403, 'Access denied. Organization Editor role required.');
        }

        // Must have an organization assigned
        if (!auth()->user()->organization_id) {
            return redirect()->route('org-editor.no-org');
        }

        $organization = auth()->user()->organization;

        // Check organization status
        if ($organization->status === 'pending') {
            return redirect()->route('org-editor.pending');
        }

        if ($organization->status === 'rejected') {
            return redirect()->route('org-editor.rejected');
        }

        if ($organization->status === 'needs_changes') {
            return redirect()->route('org-editor.rejected');
        }

        // Only published orgs get full access
        if ($organization->status !== 'published') {
            return redirect()->route('org-editor.pending');
        }

        return $next($request);
    }
}