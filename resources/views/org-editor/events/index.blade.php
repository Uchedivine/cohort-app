@extends('layouts.app')
@section('title', 'My Events')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h1 {
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 600;
        color: var(--white);
    }
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--gold);
        color: var(--navy);
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: background 0.2s;
    }
    .btn-create:hover {
        background: var(--gold-dark);
    }

    .filters-section {
        background: var(--cream);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
    }
    .filters-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    .filter-group {
        flex: 1;
    }
    .filter-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--navy);
        margin-bottom: 6px;
    }
    .filter-group select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.9rem;
        background: var(--white);
        color: var(--text);
    }

    .events-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .events-inner {
        max-width: 1200px;
        margin: 0 auto;
    }
    .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    .results-count {
        font-size: 0.9rem;
        color: var(--muted);
    }

    .event-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .event-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .event-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border-color: var(--gold);
    }
    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .event-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .event-meta {
        font-size: 0.85rem;
        color: var(--muted);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        flex-shrink: 0;
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
    .status-published {
        background: #d1fae5;
        color: #065f46;
    }
    .event-summary {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    .event-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .btn-sm {
        padding: 8px 16px;
        border-radius: 5px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-edit {
        background: var(--navy);
        color: var(--white);
    }
    .btn-edit:hover {
        background: #1e293b;
    }
    .btn-view {
        background: var(--white);
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-view:hover {
        border-color: var(--navy);
    }

    .reviewer-note {
        background: #fffbeb;
        border-left: 3px solid #f59e0b;
        padding: 1rem;
        margin-top: 1rem;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .reviewer-note strong {
        display: block;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #92400e;
        margin-bottom: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--muted);
    }
    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .page-header-inner { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .btn-create { width: 100%; justify-content: center; }
        .filters-inner { flex-direction: column; }
        .events-section { padding: 2rem 1.25rem; }
        .event-header { flex-direction: column; gap: 1rem; }
        .event-meta { flex-direction: column; gap: 0.5rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>My Events</h1>
        <a href="{{ route('org-editor.events.create') }}" class="btn-create">
            📅 Create New Event
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FILTERS
════════════════════════════════════════════ -->
<section class="filters-section">
    <div class="filters-inner">
        <form method="GET" action="{{ route('org-editor.events.index') }}" style="display: flex; gap: 1rem; width: 100%;">
            <div class="filter-group">
                <label for="status">Filter by Status</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="needs_changes" {{ request('status') == 'needs_changes' ? 'selected' : '' }}>Needs Changes</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
        </form>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     EVENTS LIST
════════════════════════════════════════════ -->
<section class="events-section">
    <div class="events-inner">
        <div class="results-header">
            <div class="results-count">
                {{ $events->total() }} {{ Str::plural('event', $events->total()) }} found
            </div>
        </div>

        @if($events->isNotEmpty())
            <div class="event-list">
                @foreach($events as $event)
                    <div class="event-card">
                        <div class="event-header">
                            <div class="event-info">
                                <h3>{{ $event->title }}</h3>
                                <div class="event-meta">
                                    <span class="event-meta-item">
                                        📅 {{ $event->start_date->format('M d, Y g:i A') }}
                                    </span>
                                    @if($event->location)
                                        <span class="event-meta-item">
                                            📍 {{ $event->location }}
                                        </span>
                                    @endif
                                    @if($event->virtual_link)
                                        <span class="event-meta-item">
                                            💻 Virtual
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="status-badge status-{{ $event->status ?? 'draft' }}">
                                {{ str_replace('_', ' ', $event->status ?? 'draft') }}
                            </span>
                        </div>

                        @if($event->description)
                            <div class="event-summary">
                                {{ Str::limit($event->description, 200) }}
                            </div>
                        @endif

                        @php
                            $latestSubmission = $event->submissions->sortByDesc('created_at')->first();
                        @endphp

                        @if($latestSubmission && $latestSubmission->reviewer_notes)
                            <div class="reviewer-note">
                                <strong>Reviewer Feedback:</strong>
                                {{ $latestSubmission->reviewer_notes }}
                            </div>
                        @endif

                        <div class="event-actions">
                            @if($event->status === 'published')
                                <a href="{{ route('events.show', $event->slug) }}" target="_blank" class="btn-sm btn-view">
                                    👁️ View Public
                                </a>
                            @endif
                            @if(in_array($event->status, ['draft', 'needs_changes', 'submitted']))
                                <a href="{{ route('org-editor.events.edit', $event) }}" class="btn-sm btn-edit">
                                    ✏️ Edit
                                </a>
                            @endif
                            @if($event->status === 'rejected')
                                @php
                                    $rejectedSubmission = $event->submissions
                                        ->where('status', 'rejected')
                                        ->sortByDesc('created_at')
                                        ->first();
                                @endphp
                                @if($rejectedSubmission && $rejectedSubmission->allow_resubmission)
                                    <form action="{{ route('org-editor.events.resubmit', $event) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-sm btn-edit" style="background: var(--green);">
                                            🔄 Resubmit for Review
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:.8rem; color:var(--muted);">
                                        Contact secretary to resubmit
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">
                {{ $events->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📅</div>
                <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No events yet</h3>
                <p>Start promoting your organization's activities by creating your first event.</p>
                <a href="{{ route('org-editor.events.create') }}" class="btn-create" style="margin-top: 1.5rem;">
                    📅 Create Your First Event
                </a>
            </div>
        @endif
    </div>
</section>

@endsection
