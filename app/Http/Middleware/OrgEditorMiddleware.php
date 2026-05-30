<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrgEditorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('org_editor')) {
            abort(403, 'Access denied. Organization Editor role required.');
        }

        if (!auth()->user()->organization_id) {
            return redirect()->route('org-editor.no-org');
        }

        return $next($request);
    }
}