@extends('layouts.app')
@section('title', 'Story Bank')

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

    .stories-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .stories-inner {
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

    .story-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }
    .story-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: var(--text);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .story-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15),
                    0 0 0 3px rgba(15, 23, 42, 0.1);
    }
    .story-thumb {
        height: 200px;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        position: relative;
        overflow: hidden;
    }
    .story-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .story-body {
        padding: 1.5rem;
    }
    .story-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 12px;
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
    .story-card h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .story-meta {
        font-size: 0.78rem;
        color: var(--muted);
        margin-bottom: 10px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .story-card p {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
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
        .story-grid { grid-template-columns: 1fr; }
        .results-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="page-hero">
    <h1>Story Bank</h1>
    <p>Real impact stories from cohort organizations driving systems change across Africa.</p>
</section>

<!-- ═══════════════════════════════════════════
     FILTERS
════════════════════════════════════════════ -->
<section class="filters-section">
    <div class="filters-inner">
        <form method="GET" action="{{ route('stories.index') }}">
            <div class="filters-grid">
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
                    <label for="tag">Tag / SDG</label>
                    <select name="tag" id="tag">
                        <option value="">All Tags</option>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="year">Year</label>
                    <select name="year" id="year">
                        <option value="">All Years</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" name="search" id="search" placeholder="Search stories..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="{{ route('stories.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     STORIES GRID
════════════════════════════════════════════ -->
<section class="stories-section">
    <div class="stories-inner">
        <div class="results-header">
            <div class="results-count">
                {{ $stories->total() }} {{ Str::plural('story', $stories->total()) }} found
            </div>
        </div>

        @if($stories->isNotEmpty())
            <div class="story-grid">
                @foreach($stories as $story)
                    <a href="{{ route('stories.show', $story->slug) }}" class="story-card">
                        <div class="story-thumb">
                            @if($story->featured_image)
                                <img src="{{ asset('storage/' . $story->featured_image) }}" alt="{{ $story->title }}">
                            @else
                                📖
                            @endif
                        </div>
                        <div class="story-body">
                            <div class="story-tags">
                                @foreach($story->tags->take(3) as $tag)
                                    <span class="tag-pill">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                            <h3>{{ $story->title }}</h3>
                            <div class="story-meta">
                                <span>{{ $story->organization->name }}</span>
                                <span>{{ $story->published_at?->format('M d, Y') }}</span>
                                @if($story->author)
                                    <span>By {{ $story->author }}</span>
                                @endif
                            </div>
                            <p>{{ Str::limit($story->summary, 120) }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div style="margin-top: 3rem;">
                {{ $stories->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No stories found</h3>
                <p>Try adjusting your filters or check back later for new stories.</p>
            </div>
        @endif
    </div>
</section>

@endsection
