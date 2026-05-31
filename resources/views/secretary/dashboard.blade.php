@extends('layouts.app')
@section('title', 'Secretary Dashboard')

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
    .badge {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        font-size: .7rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        margin-top: 8px;
    }
    @media(max-width:640px){
        .dash-grid { padding: 0 1.25rem; margin: 2rem auto; }
    }
</style>

<div class="dash-header">
    <h1>Secretary Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}. You have full administrative access.</p>
</div>

<div class="dash-grid">
    <a href="{{ route('secretary.submissions.index') }}" class="dash-card">
        <span class="dash-card-icon">📋</span>
        <h3>Review Queue</h3>
        <p>Review and approve submissions from org editors</p>
        <span class="badge">Pending submissions</span>
    </a>

    <a href="{{ route('secretary.applications.index') }}" class="dash-card">
        <span class="dash-card-icon">📬</span>
        <h3>Organisation Applications</h3>
        <p>Review and approve organisations applying to join the cohort</p>
        <span class="badge">New applications</span>
    </a>

    <a href="{{ route('secretary.messages.index') }}" class="dash-card">
        <span class="dash-card-icon">💬</span>
        <h3>Messages</h3>
        <p>Send messages to organisations and view conversations</p>
    </a>

    <a href="{{ route('secretary.events.index') }}" class="dash-card">
        <span class="dash-card-icon">📅</span>
        <h3>Manage Events</h3>
        <p>Create, edit and publish cohort events</p>
    </a>

    <a href="{{ route('secretary.users.index') }}" class="dash-card">
        <span class="dash-card-icon">👥</span>
        <h3>Manage Users</h3>
        <p>Create org editor accounts and assign organizations</p>
    </a>

    <a href="{{ route('secretary.tags.index') }}" class="dash-card">
        <span class="dash-card-icon">🏷️</span>
        <h3>Manage Tags</h3>
        <p>Create and manage tags, SDGs and thematic categories</p>
    </a>

    <a href="{{ route('organizations.index') }}" class="dash-card">
        <span class="dash-card-icon">🏢</span>
        <h3>View Directory</h3>
        <p>See the public organization directory</p>
    </a>

    <a href="{{ route('stories.index') }}" class="dash-card">
        <span class="dash-card-icon">📖</span>
        <h3>View Stories</h3>
        <p>Browse all published stories</p>
    </a>
</div>
@endsection