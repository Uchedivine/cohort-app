@extends('layouts.app')
@section('title', 'Secretary Dashboard')

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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    .stat-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
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
    .stat-card.highlight {
        border-color: var(--gold);
        background: linear-gradient(135deg, #fffbf0 0%, var(--white) 100%);
    }

    .quick-actions {
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
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
    }
    .action-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
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
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .action-card p {
        font-size: 0.8rem;
        color: var(--muted);
    }

    .activity-section {
        margin-bottom: 3rem;
    }
    .activity-feed {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }
    .activity-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .activity-content {
        flex: 1;
    }
    .activity-text {
        font-size: 0.9rem;
        color: var(--text);
        margin-bottom: 0.25rem;
    }
    .activity-meta {
        font-size: 0.75rem;
        color: var(--muted);
    }
    .activity-time {
        font-size: 0.75rem;
        color: var(--muted);
        flex-shrink: 0;
    }

    .pending-submissions {
        margin-bottom: 3rem;
    }
    .submission-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: box-shadow 0.2s;
    }
    .submission-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .submission-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.25rem;
    }
    .submission-meta {
        font-size: 0.8rem;
        color: var(--muted);
    }
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #fef3c7;
        color: #92400e;
    }
    .submission-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    .btn-sm {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--navy);
        color: var(--white);
    }
    .btn-primary:hover {
        background: #1e293b;
    }

    @media (max-width: 768px) {
        .dashboard-hero { padding: 50px 1.25rem 40px; }
        .dashboard-content { padding: 2rem 1.25rem; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .action-grid { grid-template-columns: 1fr; }
        .submission-header { flex-direction: column; gap: 0.75rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="dashboard-hero">
    <div class="dashboard-hero-inner">
        <h1>Secretary Dashboard</h1>
        <p>Manage submissions, users, events, and cohort content</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     DASHBOARD CONTENT
════════════════════════════════════════════ -->
<section class="dashboard-content">
    <div class="dashboard-inner">
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card highlight">
                <div class="stat-icon">⏳</div>
                <span class="stat-number">{{ $stats['pending_submissions'] }}</span>
                <span class="stat-label">Pending Review</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏢</div>
                <span class="stat-number">{{ $stats['total_organizations'] }}</span>
                <span class="stat-label">Organizations</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📖</div>
                <span class="stat-number">{{ $stats['published_stories'] }}</span>
                <span class="stat-label">Published Stories</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <span class="stat-number">{{ $stats['published_resources'] }}</span>
                <span class="stat-label">Resources</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <span class="stat-number">{{ $stats['upcoming_events'] }}</span>
                <span class="stat-label">Upcoming Events</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <span class="stat-number">{{ $stats['total_users'] }}</span>
                <span class="stat-label">Users</span>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="action-grid">
                <a href="{{ route('secretary.submissions.index') }}" class="action-card">
                    <div class="action-icon">📋</div>
                    <h3>Review Submissions</h3>
                    <p>Approve or request changes</p>
                </a>
                <a href="{{ route('secretary.events.create') }}" class="action-card">
                    <div class="action-icon">➕</div>
                    <h3>Create Event</h3>
                    <p>Add new cohort event</p>
                </a>
                <a href="{{ route('secretary.users.index') }}" class="action-card">
                    <div class="action-icon">👥</div>
                    <h3>Manage Users</h3>
                    <p>Add or edit user accounts</p>
                </a>
                <a href="{{ route('secretary.tags.index') }}" class="action-card">
                    <div class="action-icon">🏷️</div>
                    <h3>Manage Tags</h3>
                    <p>Edit themes and SDGs</p>
                </a>
            </div>
        </div>

        <!-- Pending Submissions -->
        @if($pendingSubmissions->isNotEmpty())
        <div class="pending-submissions">
            <h2 class="section-title">Pending Submissions ({{ $pendingSubmissions->count() }})</h2>
            @foreach($pendingSubmissions as $submission)
                <div class="submission-card">
                    <div class="submission-header">
                        <div>
                            <div class="submission-title">
                                {{ $submission->submittable->title ?? 'Organization Profile Update' }}
                            </div>
                            <div class="submission-meta">
                                {{ class_basename($submission->submittable_type) }} • 
                                {{ $submission->submittable->organization->name ?? 'N/A' }} • 
                                Submitted {{ $submission->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <span class="status-badge">{{ str_replace('_', ' ', $submission->status) }}</span>
                    </div>
                    <div class="submission-actions">
                        <a href="{{ route('secretary.submissions.show', $submission) }}" class="btn-sm btn-primary">
                            Review →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="activity-section">
            <h2 class="section-title">Recent Activity</h2>
            <div class="activity-feed">
                @forelse($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="activity-icon">
                            @if($activity->description === 'approved')
                                ✅
                            @elseif($activity->description === 'rejected')
                                ❌
                            @elseif($activity->description === 'submitted')
                                📤
                            @else
                                📝
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">
                                <strong>{{ $activity->causer->name ?? 'System' }}</strong> 
                                {{ $activity->description }} 
                                {{ $activity->subject_type ? class_basename($activity->subject_type) : 'item' }}
                            </div>
                            <div class="activity-meta">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-text" style="color: var(--muted);">No recent activity</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

@endsection
