<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecretaryMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasRole('secretary')) {
            abort(403, 'Access denied. Secretary role required.');
        }

        return $next($request);
    }
}