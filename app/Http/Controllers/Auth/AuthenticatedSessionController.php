<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

   public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    // Clear any previously intended URL so role-based redirect always wins
    $request->session()->forget('url.intended');

    $user = Auth::user();

    if ($user->hasRole('secretary')) {
        return redirect()->route('secretary.dashboard');
    }

    if ($user->hasRole('org_editor')) {
        return redirect()->route('org-editor.dashboard');
    }

    return redirect()->route('home');
}

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}