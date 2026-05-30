@extends('layouts.app')

@section('title', 'Search - Cohort')

@section('content')
<div class="search-page">
    <!-- Search Header -->
    <div class="search-header">
        <div class="container">
            <h1>Search</h1>
            
            <!-- Search Form -->
            <form action="{{ route('search.index') }}" method="GET" class="search-form">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ $query }}" 
                        placeholder="Search organizations, stories, resources, events..."
                        class="search-input"
                        autocomplete="off"
                        id="searchInput"
                    >
                    <button type="submit" class="search-button">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </div>

                <!-- Type Filter -->
                <div class="search-filters">
                    <label>
                        <input type="radio" name="type" value="all" {{ $type === 'all' ? 'checked' : '' }}>
                        <span>All</span>
                    </label>
                    <label>
                        <input type="radio" name="type" value="organizations" {{ $type === 'organizations' ? 'checked' : '' }}>
                        <span>Organizations</span>
                    </label>
                    <label>
                        <input type="radio" name="type" value="stories" {{ $type === 'stories' ? 'checked' : '' }}>
                        <span>Stories</span>
                    </label>
                    <label>
                        <input type="radio" name="type" value="resources" {{ $type === 'resources' ? 'checked' : '' }}>
                        <span>Resources</span>
                    </label>
                    <label>
                        <input type="radio" name="type" value="events" {{ $type === 'events' ? 'checked' : '' }}>
                        <span>Events</span>
                    </label>
                </div>
            </form>

            @if($query)
                <p class="search-meta">
                    Found <strong>{{ $results['total'] ?? 0 }}</strong> results for "<strong>{{ $query }}</strong>"
                </p>
            @endif
        </div>
    </div>

    <!-- Search Results -->
    <div class="container search-results">
        @if($query && isset($results['total']) && $results['total'] > 0)
            
            <!-- Organizations -->
            @if($results['organizations']->isNotEmpty())
                <section class="results-section">
                    <h2>Organizations ({{ $results['organizations']->count() }})</h2>
                    <div class="results-grid">
                        @foreach($results['organizations'] as $org)
                            <a href="{{ route('organizations.show', $org->slug) }}" class="result-card">
                                @if($org->logo)
                                    <img src="{{ Storage::url($org->logo) }}" alt="{{ $org->name }}" class="result-image">
                                @else
                                    <div class="result-image-placeholder">{{ substr($org->name, 0, 1) }}</div>
                                @endif
                                <div class="result-content">
                                    <h3>{{ $org->name }}</h3>
                                    <p>{{ Str::limit($org->short_description, 120) }}</p>
                                    @if($org->location)
                                        <span class="result-meta">📍 {{ $org->location }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Stories -->
            @if($results['stories']->isNotEmpty())
                <section class="results-section">
                    <h2>Stories ({{ $results['stories']->count() }})</h2>
                    <div class="results-grid">
                        @foreach($results['stories'] as $story)
                            <a href="{{ route('stories.show', $story->slug) }}" class="result-card">
                                @if($story->featured_image)
                                    <img src="{{ Storage::url($story->featured_image) }}" alt="{{ $story->title }}" class="result-image">
                                @else
                                    <div class="result-image-placeholder">📖</div>
                                @endif
                                <div class="result-content">
                                    <h3>{{ $story->title }}</h3>
                                    <p>{{ Str::limit($story->summary, 120) }}</p>
                                    <span class="result-meta">by {{ $story->organization->name ?? 'Unknown' }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Resources -->
            @if($results['resources']->isNotEmpty())
                <section class="results-section">
                    <h2>Resources ({{ $results['resources']->count() }})</h2>
                    <div class="results-grid">
                        @foreach($results['resources'] as $resource)
                            <a href="{{ route('resources.show', $resource->slug) }}" class="result-card">
                                <div class="result-image-placeholder">
                                    @if($resource->resource_type === 'file') 📄
                                    @elseif($resource->resource_type === 'external_link') 🔗
                                    @else 🎥
                                    @endif
                                </div>
                                <div class="result-content">
                                    <h3>{{ $resource->title }}</h3>
                                    <p>{{ Str::limit($resource->description, 120) }}</p>
                                    <span class="result-meta">{{ ucfirst(str_replace('_', ' ', $resource->resource_type)) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Events -->
            @if($results['events']->isNotEmpty())
                <section class="results-section">
                    <h2>Events ({{ $results['events']->count() }})</h2>
                    <div class="results-grid">
                        @foreach($results['events'] as $event)
                            <a href="{{ route('events.show', $event->slug) }}" class="result-card">
                                @if($event->banner_image)
                                    <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->title }}" class="result-image">
                                @else
                                    <div class="result-image-placeholder">📅</div>
                                @endif
                                <div class="result-content">
                                    <h3>{{ $event->title }}</h3>
                                    <p>{{ Str::limit($event->description, 120) }}</p>
                                    <span class="result-meta">{{ $event->start_date->format('M d, Y') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

        @elseif($query)
            <div class="no-results">
                <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2>No results found</h2>
                <p>Try different keywords or browse our content</p>
                <div class="browse-links">
                    <a href="{{ route('organizations.index') }}">Browse Organizations</a>
                    <a href="{{ route('stories.index') }}">Browse Stories</a>
                    <a href="{{ route('resources.index') }}">Browse Resources</a>
                    <a href="{{ route('events.index') }}">Browse Events</a>
                </div>
            </div>
        @else
            <div class="search-empty">
                <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2>Start searching</h2>
                <p>Enter keywords to search across all content</p>
            </div>
        @endif
    </div>
</div>

<style>
.search-page {
    min-height: 100vh;
    background: #f5f1e8;
}

.search-header {
    background: #0f172a;
    color: white;
    padding: 100px 0 60px;
}

.search-header h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
}

.search-form {
    max-width: 800px;
    margin: 0 auto;
}

.search-input-group {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.search-input {
    flex: 1;
    padding: 1rem 1.5rem;
    border: 2px solid #334155;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    color: #0f172a;
}

.search-input:focus {
    outline: none;
    border-color: #d4af37;
}

.search-input::placeholder {
    color: #94a3b8;
}

.search-button {
    padding: 1rem 2rem;
    background: #d4af37;
    color: #0f172a;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-button:hover {
    background: #c19d2f;
}

.search-filters {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.search-filters label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.1);
}

.search-filters input[type="radio"] {
    accent-color: #d4af37;
}

.search-filters label:has(input:checked) {
    background: #d4af37;
    color: #0f172a;
}

.search-meta {
    text-align: center;
    margin-top: 1.5rem;
    color: #cbd5e1;
}

.search-results {
    padding: 60px 1.25rem;
}

.results-section {
    margin-bottom: 4rem;
}

.results-section h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: #0f172a;
    margin-bottom: 1.5rem;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.result-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.result-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15),
                0 0 0 3px rgba(15, 23, 42, 0.1);
}

.result-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.result-image-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, #059669 0%, #0f172a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
}

.result-content {
    padding: 1.5rem;
}

.result-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 0.5rem;
}

.result-content p {
    color: #64748b;
    margin-bottom: 0.75rem;
    line-height: 1.6;
}

.result-meta {
    display: inline-block;
    font-size: 0.875rem;
    color: #059669;
    font-weight: 500;
}

.no-results, .search-empty {
    text-align: center;
    padding: 4rem 1.25rem;
}

.no-results svg, .search-empty svg {
    color: #cbd5e1;
    margin: 0 auto 1.5rem;
}

.no-results h2, .search-empty h2 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2rem;
    color: #0f172a;
    margin-bottom: 0.5rem;
}

.no-results p, .search-empty p {
    color: #64748b;
    margin-bottom: 2rem;
}

.browse-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.browse-links a {
    padding: 0.75rem 1.5rem;
    background: #0f172a;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
}

.browse-links a:hover {
    background: #1e293b;
}

@media (max-width: 768px) {
    .search-header h1 {
        font-size: 2rem;
    }
    
    .search-input-group {
        flex-direction: column;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Auto-submit form when filter is changed
document.addEventListener('DOMContentLoaded', function() {
    const filterInputs = document.querySelectorAll('.search-filters input[type="radio"]');
    const searchForm = document.querySelector('.search-form');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            searchForm.submit();
        });
    });
});
</script>

@endsection
