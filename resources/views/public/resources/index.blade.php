@extends('layouts.app')
@section('title', 'Resources Library')

@section('content')

<style>
    .page-hero {
        background: var(--navy);
        color: var(--white);
        padding: 80px 2rem 60px;
        text-align: center;
    }
    .page-hero h1 {
        font-size: clamp(2.2rem, 5vw, 3.5rem);
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--white);
    }
    .page-hero p {
        font-size: 1.05rem;
        color: #94a3b8;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    .filters-section {
        background: var(--cream);
        padding: 2rem;
        border-bottom: 1px solid var(--border);
    }
    .filters-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    .filter-group label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--navy);
        margin-bottom: 6px;
    }
    .filter-group select,
    .filter-group input {
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
        background: var(--gold);
        color: var(--navy);
    }
    .btn-secondary {
        background: var(--white);
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-secondary:hover {
        border-color: var(--navy);
    }

    .resources-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .resources-inner {
        max-width: 1100px;
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

    .resource-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }
    .resource-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        text-decoration: none;
        color: var(--text);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }
    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15),
                    0 0 0 3px rgba(15, 23, 42, 0.1);
    }
    .resource-header {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .resource-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }
    .resource-info {
        flex: 1;
    }
    .resource-type {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--gold);
        margin-bottom: 6px;
    }
    .resource-card h3 {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .resource-meta {
        font-size: 0.78rem;
        color: var(--muted);
        margin-bottom: 10px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .resource-card p {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex: 1;
    }
    .resource-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }
    .tag-pill {
        font-size: 0.68rem;
        font-weight: 500;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        background: var(--green-light);
        color: var(--green);
        padding: 3px 9px;
        border-radius: 20px;
    }
    .resource-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        background: var(--navy);
        color: var(--white);
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.2s;
        align-self: flex-start;
    }
    .resource-action:hover {
        background: var(--gold);
        color: var(--navy);
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
        .page-hero { padding: 60px 1.25rem 50px; }
        .filters-grid { grid-template-columns: 1fr; }
        .resource-grid { grid-template-columns: 1fr; }
        .results-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="page-hero">
    <h1>Resources Library</h1>
    <p>Tools, guides, research, and resources from cohort organizations.</p>
</section>

<!-- ═══════════════════════════════════════════
     FILTERS
════════════════════════════════════════════ -->
<section class="filters-section">
    <div class="filters-inner">
        <form method="GET" action="{{ route('resources.index') }}">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="type">Resource Type</label>
                    <select name="type" id="type">
                        <option value="">All Types</option>
                        <option value="file" {{ request('type') == 'file' ? 'selected' : '' }}>File (PDF/DOC/PPT)</option>
                        <option value="link" {{ request('type') == 'link' ? 'selected' : '' }}>External Link</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
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
                    <label for="tag">Theme / Tag</label>
                    <select name="tag" id="tag">
                        <option value="">All Themes</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" placeholder="Search resources..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('resources.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     RESOURCES GRID
════════════════════════════════════════════ -->
<section class="resources-section">
    <div class="resources-inner">
        <div class="results-header">
            <div class="results-count">
                {{ $resources->total() }} {{ Str::plural('resource', $resources->total()) }} found
            </div>
        </div>

        @if($resources->isNotEmpty())
            <div class="resource-grid">
                @foreach($resources as $resource)
                    <div class="resource-card">
                        <div class="resource-header">
                            <div class="resource-icon">
                                @if($resource->type === 'file')
                                    📄
                                @elseif($resource->type === 'video')
                                    🎥
                                @else
                                    🔗
                                @endif
                            </div>
                            <div class="resource-info">
                                <div class="resource-type">{{ ucfirst($resource->type) }}</div>
                                <h3>{{ $resource->title }}</h3>
                            </div>
                        </div>

                        <div class="resource-meta">
                            <span>{{ $resource->organization->name }}</span>
                            <span>{{ $resource->published_at?->format('M Y') }}</span>
                        </div>

                        <p>{{ Str::limit($resource->description, 150) }}</p>

                        @if($resource->tags->isNotEmpty())
                            <div class="resource-tags">
                                @foreach($resource->tags->take(3) as $tag)
                                    <span class="tag-pill">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if($resource->type === 'file' && $resource->file_path)
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="resource-action">
                                📥 Download
                            </a>
                        @elseif($resource->external_url)
                            <a href="{{ $resource->external_url }}" target="_blank" class="resource-action">
                                🔗 View Resource
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">
                {{ $resources->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No resources found</h3>
                <p>Try adjusting your filters or check back later for new resources.</p>
            </div>
        @endif
    </div>
</section>

@endsection
