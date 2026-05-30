@extends('layouts.app')
@section('title', 'Org Editor Dashboard')

@section('content')

<style>
    .dashboard-hero {
        background: var(--navy);
        color: var(--white);
        padding: 60px 2rem 50px;
    }
    .dashboard-hero-inner {
        max-width: 1200px;
        margin: 0 auto;
    }
    .dashboard-hero h1 {
        font-size: clamp(1.8rem, 4vw, 2.6rem);
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--white);
    }
    .dashboard-hero p {
        font-size: 1rem;
        color: #94a3b8;
    }

    .dashboard-content {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .dashboard-inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    .stat-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
    }
    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }
    .stat-number {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2.5rem;
        font-weight: 600;
        color: var(--navy);
        display: block;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.85rem;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .actions-section {
        margin-bottom: 3rem;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
    }
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
    }
    .action-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow 0.25s, transform 0.25s, border-color 0.25s;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .action-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        transform: translateY(-4px);
        border-color: var(--gold);
    }
    .action-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    .action-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .action-card p {
        font-size: 0.85rem;
        color: var(--muted);
    }

    .submissions-section {
        margin-bottom: 3rem;
    }
    .submissions-table {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }
    .table-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--navy);
    }
    .table-row {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 1rem;
        align-items: center;
        transition: background 0.2s;
    }
    .table-row:last-child {
        border-bottom: none;
    }
    .table-row:hover {
        background: #f8f9fa;
    }
    .submission-title {
        font-weight: 500;
        color: var(--navy);
    }
    .submission-type {
        font-size: 0.8rem;
        color: var(--muted);
    }
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .status-draft {
        background: #e5e7eb;
        color: #6b7280;
    }
    .status-submitted {
        background: #dbeafe;
        color: #1e40af;
    }
    .status-needs-changes {
        background: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }
    .table-date {
        font-size: 0.85rem;
        color: var(--muted);
    }
    .table-action {
        text-decoration: none;
        color: var(--navy);
        font-size: 0.85rem;
        font-weight: 500;
    }
    .table-action:hover {
        color: var(--gold);
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--muted);
    }
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .dashboard-hero { padding: 50px 1.25rem 40px; }
        .dashboard-content { padding: 2rem 1.25rem; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .action-grid { grid-template-columns: 1fr; }
        .table-header, .table-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .table-header { display: none; }
        .table-row > div::before {
            content: attr(data-label);
            font-weight: 600;
            display: inline-block;
            margin-right: 0.5rem;
        }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="dashboard-hero">
    <div class="dashboard-hero-inner">
        <h1>Welcome, {{ auth()->user()->organization->name }}</h1>
        <p>Manage your organization profile, stories, and resources</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     DASHBOARD CONTENT
════════════════════════════════════════════ -->
<section class="dashboard-content">
    <div class="dashboard-inner">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <span class="stat-number">{{ $stats['pending'] }}</span>
                <span class="stat-label">Pending Review</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <span class="stat-number">{{ $stats['approved'] }}</span>
                <span class="stat-label">Approved</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🔄</div>
                <span class="stat-number">{{ $stats['needs_changes'] }}</span>
                <span class="stat-label">Needs Changes</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <span class="stat-number">{{ $stats['published_stories'] }}</span>
                <span class="stat-label">Published Stories</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-section">
            <h2 class="section-title">Quick Actions</h2>
            <div class="action-grid">
                <a href="{{ route('org-editor.organization.edit') }}" class="action-card">
                    <div class="action-icon">🏢</div>
                    <h3>Edit Organization Profile</h3>
                    <p>Update your organization information</p>
                </a>
                <a href="{{ route('org-editor.stories.create') }}" class="action-card">
                    <div class="action-icon">✍️</div>
                    <h3>Create New Story</h3>
                    <p>Share your impact story</p>
                </a>
                <a href="{{ route('org-editor.resources.create') }}" class="action-card">
                    <div class="action-icon">📚</div>
                    <h3>Submit Resource</h3>
                    <p>Upload documents or links</p>
                </a>
                <a href="{{ route('org-editor.stories.index') }}" class="action-card">
                    <div class="action-icon">📋</div>
                    <h3>View All Stories</h3>
                    <p>Manage your submissions</p>
                </a>
            </div>
        </div>

        <!-- Recent Submissions -->
        <div class="submissions-section">
            <h2 class="section-title">Recent Submissions</h2>
            @if($recentSubmissions->isNotEmpty())
                <div class="submissions-table">
                    <div class="table-header">
                        <div>Title</div>
                        <div>Type</div>
                        <div>Status</div>
                        <div>Date</div>
                    </div>
                    @foreach($recentSubmissions as $submission)
                        <div class="table-row">
                            <div data-label="Title">
                                <div class="submission-title">{{ $submission->submittable->title ?? 'Organization Profile' }}</div>
                                <div class="submission-type">{{ ucfirst($submission->submittable_type) }}</div>
                            </div>
                            <div data-label="Type">
                                <span class="submission-type">{{ ucfirst(class_basename($submission->submittable_type)) }}</span>
                            </div>
                            <div data-label="Status">
                                <span class="status-badge status-{{ $submission->status }}">
                                    {{ str_replace('_', ' ', $submission->status) }}
                                </span>
                            </div>
                            <div data-label="Date">
                                <span class="table-date">{{ $submission->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3 style="font-size: 1.2rem; color: var(--navy); margin-bottom: 0.5rem;">No submissions yet</h3>
                    <p>Start by creating a story or updating your organization profile.</p>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
