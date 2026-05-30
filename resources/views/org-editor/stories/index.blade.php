@extends('layouts.app')
@section('title', 'My Stories')

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

    .stories-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .stories-inner {
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

    .story-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .story-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        transition: box-shadow 0.2s, border-color 0.2s;
    }
    .story-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border-color: var(--gold);
    }
    .story-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    .story-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .story-meta {
        font-size: 0.85rem;
        color: var(--muted);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .story-meta-item {
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
    .story-summary {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    .story-actions {
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
        .stories-section { padding: 2rem 1.25rem; }
        .story-header { flex-direction: column; gap: 1rem; }
        .story-meta { flex-direction: column; gap: 0.5rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>My Stories</h1>
        <a href="{{ route('org-editor.stories.create') }}" class="btn-create">
            ✍️ Create New Story
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FILTERS
════════════════════════════════════════════ -->
<section class="filters-section">
    <div class="filters-inner">
        <form method="GET" action="{{ route('org-editor.stories.index') }}" style="display: flex; gap: 1rem; width: 100%;">
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
     STORIES LIST
════════════════════════════════════════════ -->
<section class="stories-section">
    <div class="stories-inner">
        <div class="results-header">
            <div class="results-count">
                {{ $stories->total() }} {{ Str::plural('story', $stories->total()) }} found
            </div>
        </div>

        @if($stories->isNotEmpty())
            <div class="story-list">
                @foreach($stories as $story)
                    <div class="story-card">
                        <div class="story-header">
                            <div class="story-info">
                                <h3>{{ $story->title }}</h3>
                                <div class="story-meta">
                                    <span class="story-meta-item">
                                        📅 Created {{ $story->created_at->format('M d, Y') }}
                                    </span>
                                    @if($story->published_at)
                                        <span class="story-meta-item">
                                            ✓ Published {{ $story->published_at->format('M d, Y') }}
                                        </span>
                                    @endif
                                    @if($story->author)
                                        <span class="story-meta-item">
                                            ✍️ {{ $story->author }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span class="status-badge status-{{ $story->status ?? 'draft' }}">
                                {{ str_replace('_', ' ', $story->status ?? 'draft') }}
                            </span>
                        </div>

                        @if($story->summary)
                            <div class="story-summary">
                                {{ Str::limit($story->summary, 200) }}
                            </div>
                        @endif

                        @if($story->submission && $story->submission->reviewer_notes)
                            <div class="reviewer-note">
                                <strong>Reviewer Feedback:</strong>
                                {{ $story->submission->reviewer_notes }}
                            </div>
                        @endif

                        <div class="story-actions">
                            @if($story->status === 'published')
                                <a href="{{ route('stories.show', $story->slug) }}" target="_blank" class="btn-sm btn-view">
                                    👁️ View Public
                                </a>
                            @endif
                            @if(in_array($story->status, ['draft', 'needs_changes', null]))
                                <a href="{{ route('org-editor.stories.edit', $story) }}" class="btn-sm btn-edit">
                                    ✏️ Edit
                                </a>
                            @endif
                            @if($story->status === 'draft')
                                <form action="{{ route('org-editor.stories.update', $story) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="action" value="submit">
                                    <button type="submit" class="btn-sm btn-edit">
                                        📤 Submit for Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">
                {{ $stories->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📖</div>
                <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No stories yet</h3>
                <p>Start sharing your organization's impact by creating your first story.</p>
                <a href="{{ route('org-editor.stories.create') }}" class="btn-create" style="margin-top: 1.5rem;">
                    ✍️ Create Your First Story
                </a>
            </div>
        @endif
    </div>
</section>

@endsection
