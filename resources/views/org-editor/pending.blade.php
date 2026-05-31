@extends('layouts.app')
@section('title', 'Application Pending')

@section('content')
<style>
    .status-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.25rem;
    }
    .status-box {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 3rem 2.5rem;
        max-width: 520px;
        width: 100%;
        text-align: center;
    }
    .status-icon { font-size: 3.5rem; margin-bottom: 1.25rem; display: block; }
    .status-box h2 { font-size: 1.8rem; color: var(--navy); margin-bottom: .75rem; }
    .status-box p  { font-size: .9rem; color: var(--muted); line-height: 1.75; margin-bottom: 1rem; }

    .status-steps {
        background: var(--cream);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        margin: 1.5rem 0;
        text-align: left;
    }
    .status-steps p {
        font-size: .82rem;
        color: var(--navy);
        margin-bottom: 0;
        font-weight: 500;
        margin-bottom: .5rem;
    }
    .step-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        font-size: .85rem;
        color: var(--muted);
        padding: .5rem 0;
        border-bottom: 1px solid var(--border);
    }
    .step-item:last-child { border-bottom: none; }
    .step-dot {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .75rem; font-weight: 600; flex-shrink: 0;
    }
    .step-done  { background: var(--green-light); color: var(--green); }
    .step-active { background: #fef3c7; color: #92400e; }
    .step-todo  { background: #f3f4f6; color: #9ca3af; }

    .org-info {
        background: var(--green-light);
        border: 1px solid #c6d9c8;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        font-size: .875rem;
        color: var(--green);
    }
    .org-info strong { color: var(--navy); }

    .btn-logout {
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
        margin-top: .5rem;
    }
    .btn-logout:hover { background: var(--gold); color: var(--navy); }

    @media(max-width:640px){
        .status-box { padding: 2rem 1.5rem; }
    }
</style>

<div class="status-wrap">
    <div class="status-box">
        <span class="status-icon">⏳</span>
        <h2>Application Under Review</h2>
        <p>
            Thank you for applying to join the cohort.
            Your application is currently being reviewed by the Cohort Secretary.
        </p>

        @if(auth()->user()->organization)
            <div class="org-info">
                Application submitted for: <strong>{{ auth()->user()->organization->name }}</strong><br>
                @if(auth()->user()->organization->applied_at)
                    <span style="font-size:.8rem; opacity:.8;">
                        Submitted {{ auth()->user()->organization->applied_at->diffForHumans() }}
                    </span>
                @endif
            </div>
        @endif

        <div class="status-steps">
            <p>Application Progress</p>
            <div class="step-item">
                <div class="step-dot step-done">✓</div>
                <span>Application submitted</span>
            </div>
            <div class="step-item">
                <div class="step-dot step-active">⏳</div>
                <span>Under review by Cohort Secretary</span>
            </div>
            <div class="step-item">
                <div class="step-dot step-todo">3</div>
                <span>Approval decision</span>
            </div>
            <div class="step-item">
                <div class="step-dot step-todo">4</div>
                <span>Access granted to member portal</span>
            </div>
        </div>

        <p style="font-size:.82rem;">
            You'll receive an email once a decision has been made.
            This usually takes 2-3 business days.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Log Out</button>
        </form>
    </div>
</div>
@endsection