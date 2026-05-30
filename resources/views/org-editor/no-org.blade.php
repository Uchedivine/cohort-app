@extends('layouts.app')
@section('title', 'No Organization Assigned')

@section('content')
<style>
    .no-org-wrap {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.25rem;
    }
    .no-org-box {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 3rem 2.5rem;
        max-width: 480px;
        width: 100%;
        text-align: center;
    }
    .no-org-icon { font-size: 3rem; margin-bottom: 1.25rem; display: block; }
    .no-org-box h2 { font-size: 1.6rem; color: var(--navy); margin-bottom: .75rem; }
    .no-org-box p  { font-size: .9rem; color: var(--muted); line-height: 1.7; margin-bottom: 1.5rem; }
    .no-org-box .email {
        background: var(--cream);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: .75rem 1rem;
        font-size: .85rem;
        color: var(--navy);
        margin-bottom: 1.5rem;
    }
    .btn-logout-full {
        display: inline-block;
        background: var(--navy);
        color: var(--white);
        padding: 10px 24px;
        border-radius: 5px;
        font-size: .875rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: background .2s;
    }
    .btn-logout-full:hover { background: var(--gold); color: var(--navy); }
</style>

<div class="no-org-wrap">
    <div class="no-org-box">
        <span class="no-org-icon">🏢</span>
        <h2>No Organization Assigned</h2>
        <p>
            Your account has been created but hasn't been linked to an organization yet.
            Please contact the Cohort Secretary to get your organization assigned.
        </p>
        <div class="email">
            Logged in as: <strong>{{ auth()->user()->email }}</strong>
        </div>
        <p style="font-size:.82rem; color:var(--muted); margin-bottom:1.25rem;">
            Once the secretary assigns your organization, log back in and you'll have full access to your dashboard.
        </p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout-full">Log Out</button>
        </form>
    </div>
</div>
@endsection