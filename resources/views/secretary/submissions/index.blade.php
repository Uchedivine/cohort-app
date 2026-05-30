@extends('layouts.app')
@section('title', 'Review Submissions')

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
    }
    .page-header h1 {
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--white);
    }
    .page-header p {
        font-size: 0.95rem;
        color: #94a3b8;
    }

    .filters-section {
        background: var(--cream);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
    }
    .filters-inner {
        max-width: 1200px;
        margin: 0 auto;
    }
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
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
    .filter-actions {
        display: flex;
        gap: 10px;
        margin-top: 1rem;
    }
    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
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
    .btn-secondary {
        background: var(--white);
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-secondary:hover {
        border-color: var(--navy);
    }

    .submissions-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .submissions-inner {
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

    .submission-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .submission-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .submission-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border-color: var(--gold);
    }
    .submission-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .submission-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .submission-meta {
        font-size: 0.85rem;
        color: var(--muted);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .submission-meta-item {
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
    .submission-preview {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    .submission-actions {
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
    .btn-review {
        background: var(--navy);
        color: var(--white);
    }
    .btn-review:hover {
        background: #1e293b;
    }
    .btn-approve {
        background: var(--green);
        color: var(--white);
    }
    .btn-approve:hover {
        background: #047857;
    }
    .btn-reject {
        background: #dc2626;
        color: var(--white);
    }
    .btn-reject:hover {
        background: #b91c1c;
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
        .filters-grid { grid-template-columns: 1fr; }
        .submissions-section { padding: 2rem 1.25rem; }
        .submission-header { flex-direction: column; gap: 1rem; }
        .submission-meta { flex-direction: column; gap: 0.5rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Review Submissions</h1>
        <p>Approve, reject, or request changes to cohort member submissions</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FILTERS
════════════════════════════════════════════ -->
<section class="filters-section">
    <div class="filters-inner">
        <form method="GET" action="{{ route('secretary.submissions.index') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="type">Content Type</label>
                    <select name="type" id="type">
                        <option value="">All Types</option>
                        <option value="Story" {{ request('type') == 'Story' ? 'selected' : '' }}>Stories</option>
                        <option value="Resource" {{ request('type') == 'Resource' ? 'selected' : '' }}>Resources</option>
                        <option value="Organization" {{ request('type') == 'Organization' ? 'selected' : '' }}>Org Profiles</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All Statuses</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="needs_changes" {{ request('status') == 'needs_changes' ? 'selected' : '' }}>Needs Changes</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="organization">Organization</label>
                    <select name="organization" id="organization">
                        <option value="">All Organizations</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" {{ request('organization') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sort">Sort By</label>
                    <select name="sort" id="sort">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('secretary.submissions.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     SUBMISSIONS LIST
════════════════════════════════════════════ -->
<section class="submissions-section">
    <div class="submissions-inner">
        <div class="results-header">
            <div class="results-count">
                {{ $submissions->total() }} {{ Str::plural('submission', $submissions->total()) }} found
            </div>
        </div>

        @if($submissions->isNotEmpty())
            <div class="submission-list">
                @foreach($submissions as $submission)
                    <div class="submission-card">
                        <div class="submission-header">
                            <div class="submission-info">
                                <h3>{{ $submission->submittable->title ?? 'Organization Profile Update' }}</h3>
                                <div class="submission-meta">
                                    <span class="submission-meta-item">
                                        📁 {{ class_basename($submission->submittable_type) }}
                                    </span>
                                    <span class="submission-meta-item">
                                        🏢 {{ $submission->submittable->organization->name ?? 'N/A' }}
                                    </span>
                                    <span class="submission-meta-item">
                                        👤 {{ $submission->submittedBy->name }}
                                    </span>
                                    <span class="submission-meta-item">
                                        📅 {{ $submission->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                            <span class="status-badge status-{{ $submission->status }}">
                                {{ str_replace('_', ' ', $submission->status) }}
                            </span>
                        </div>

                        @if($submission->submittable->summary ?? $submission->submittable->description ?? null)
                            <div class="submission-preview">
                                {{ Str::limit($submission->submittable->summary ?? $submission->submittable->description, 150) }}
                            </div>
                        @endif

                        <div class="submission-actions">
                            <a href="{{ route('secretary.submissions.show', $submission) }}" class="btn-sm btn-review">
                                👁️ Review Details
                            </a>
                            @if($submission->status === 'submitted' || $submission->status === 'needs_changes')
                                <form action="{{ route('secretary.submissions.approve', $submission) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-sm btn-approve">✓ Quick Approve</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">
                {{ $submissions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No submissions found</h3>
                <p>Try adjusting your filters or check back later.</p>
            </div>
        @endif
    </div>
</section>

@endsection
