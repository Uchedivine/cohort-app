@extends('layouts.app')
@section('title', 'Member Dashboard')

@section('content')
<style>
    .dash-header {
        background: var(--navy);
        padding: 48px 2rem;
        text-align: center;
    }
    .dash-header h1 { color: var(--white); font-size: 2rem; }
    .dash-header p  { color: #94a3b8; margin-top: .5rem; font-size: .9rem; }

    .dash-grid {
        max-width: 1100px;
        margin: 3rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
    }
    .dash-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow .2s, transform .2s, border-color .2s;
    }
    .dash-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,.08);
        transform: translateY(-3px);
        border-color: var(--gold);
    }
    .dash-card-icon { font-size: 2rem; margin-bottom: 1rem; display: block; }
    .dash-card h3   { font-size: 1.1rem; color: var(--navy); margin-bottom: 4px; }
    .dash-card p    { font-size: .82rem; color: var(--muted); }

    .org-banner {
        max-width: 1100px;
        margin: 2rem auto 0;
        padding: 0 2rem;
    }
    .org-banner-inner {
        background: var(--green-light);
        border: 1px solid #c6d9c8;
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .org-banner-inner p { font-size: .88rem; color: var(--green); }
    .org-banner-inner strong { color: var(--navy); }

    @media(max-width:640px){
        .dash-grid  { padding: 0 1.25rem; margin: 2rem auto; }
        .org-banner { padding: 0 1.25rem; }
    }
</style>

<div class="dash-header">
    <h1>Member Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}. Manage your organization's content here.</p>
</div>

@if(auth()->user()->organization)
<div class="org-banner">
    <div class="org-banner-inner">
        <span style="font-size:1.5rem;">🏢</span>
        <p>You are managing <strong>{{ auth()->user()->organization->name }}</strong></p>
    </div>
</div>
@endif

<div class="dash-grid">
    <a href="{{ route('org-editor.organization.edit') }}" class="dash-card">
        <span class="dash-card-icon">✏️</span>
        <h3>Edit Organization Profile</h3>
        <p>Update your organization's details, logo, and programs</p>
    </a>

    <a href="{{ route('org-editor.stories.create') }}" class="dash-card">
        <span class="dash-card-icon">📝</span>
        <h3>Submit a Story</h3>
        <p>Share your organization's impact stories for review</p>
    </a>

    <a href="{{ route('org-editor.stories.index') }}" class="dash-card">
        <span class="dash-card-icon">📚</span>
        <h3>My Stories</h3>
        <p>View and manage your submitted stories and their status</p>
    </a>

    <a href="{{ route('org-editor.resources.create') }}" class="dash-card">
        <span class="dash-card-icon">📎</span>
        <h3>Submit a Resource</h3>
        <p>Upload documents, links or videos for review</p>
    </a>

    <a href="{{ route('org-editor.resources.index') }}" class="dash-card">
        <span class="dash-card-icon">🗂️</span>
        <h3>My Resources</h3>
        <p>View and manage your submitted resources and their status</p>
    </a>

    <a href="{{ route('org-editor.messages.index') }}" class="dash-card" style="position:relative;">
        <span class="dash-card-icon">💬</span>
        <h3>Messages</h3>
        <p>View messages from the secretary</p>
        @php
            $unreadCount = \App\Http\Controllers\OrgEditor\MessageController::getUnreadCount();
        @endphp
        @if($unreadCount > 0)
            <span style="position:absolute; top:1rem; right:1rem; background:var(--gold); color:var(--navy); padding:4px 10px; border-radius:20px; font-size:.7rem; font-weight:600;">
                {{ $unreadCount }} new
            </span>
        @endif
    </a>

    <a href="{{ route('home') }}" class="dash-card">
        <span class="dash-card-icon">🌍</span>
        <h3>View Public Site</h3>
        <p>See how your organization appears to the public</p>
    </a>
</div>
@endsection