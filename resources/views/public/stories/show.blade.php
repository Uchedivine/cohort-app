@extends('layouts.app')
@section('title', $story->title)

@section('content')

<style>
    .story-hero {
        background: var(--navy);
        padding: 80px 2rem 0;
    }
    .story-hero-inner {
        max-width: 900px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .story-breadcrumb {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 1.5rem;
    }
    .story-breadcrumb a {
        color: var(--gold);
        text-decoration: none;
    }
    .story-breadcrumb a:hover {
        text-decoration: underline;
    }
    .story-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .tag-pill {
        font-size: 0.7rem;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: var(--gold);
        color: var(--navy);
        padding: 5px 12px;
        border-radius: 20px;
    }
    .story-hero h1 {
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 600;
        color: var(--white);
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    .story-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: #cbd5e1;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .story-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .story-meta a {
        color: var(--gold);
        text-decoration: none;
    }
    .story-meta a:hover {
        text-decoration: underline;
    }

    .story-featured {
        max-width: 900px;
        margin: -60px auto 0;
        position: relative;
        z-index: 10;
    }
    .story-featured-img {
        width: 100%;
        height: 480px;
        border-radius: 12px;
        overflow: hidden;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .story-featured-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .story-content-section {
        background: var(--cream);
        padding: 5rem 2rem;
    }
    .story-content-inner {
        max-width: 800px;
        margin: 0 auto;
    }
    .story-summary {
        font-size: 1.2rem;
        line-height: 1.8;
        color: #374151;
        font-weight: 500;
        margin-bottom: 3rem;
        padding: 1.5rem;
        background: rgba(212, 175, 55, 0.08);
        border-left: 4px solid var(--gold);
        border-radius: 6px;
    }
    .story-body {
        font-size: 1.05rem;
        line-height: 1.9;
        color: var(--text);
    }
    .story-body p {
        margin-bottom: 1.5rem;
    }
    .story-body h2 {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--navy);
        margin: 3rem 0 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
    }
    .story-body h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--navy);
        margin: 2rem 0 1rem;
    }
    .story-body ul, .story-body ol {
        margin: 1.5rem 0;
        padding-left: 2rem;
    }
    .story-body li {
        margin-bottom: 0.75rem;
    }

    .structured-section {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
        margin: 2.5rem 0;
    }
    .structured-section h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .structured-section p {
        margin-bottom: 1rem;
        line-height: 1.8;
    }

    .related-stories {
        background: #eeeae0;
        padding: 4rem 2rem;
    }
    .related-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 2rem;
        text-align: center;
    }
    .story-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .story-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow 0.25s, transform 0.25s;
    }
    .story-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    .story-card-thumb {
        height: 180px;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    .story-card-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .story-card-body {
        padding: 1.25rem;
    }
    .story-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .story-card p {
        font-size: 0.85rem;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .story-hero { padding: 60px 1.25rem 0; }
        .story-featured { margin-top: -40px; padding: 0 1.25rem; }
        .story-featured-img { height: 300px; }
        .story-content-section { padding: 3rem 1.25rem; }
        .story-meta { gap: 1rem; }
        .story-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="story-hero">
    <div class="story-hero-inner">
        <div class="story-breadcrumb">
            <a href="{{ route('home') }}">Home</a> / 
            <a href="{{ route('stories.index') }}">Stories</a> / 
            {{ $story->title }}
        </div>

        <div class="story-tags">
            @foreach($story->tags as $tag)
                <span class="tag-pill">{{ $tag->name }}</span>
            @endforeach
        </div>

        <h1>{{ $story->title }}</h1>

        <div class="story-meta">
            <span class="story-meta-item">
                🏢 <a href="{{ route('organizations.show', $story->organization->slug) }}">{{ $story->organization->name }}</a>
            </span>
            @if($story->author)
                <span class="story-meta-item">✍️ {{ $story->author }}</span>
            @endif
            <span class="story-meta-item">📅 {{ $story->published_at?->format('F d, Y') }}</span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FEATURED IMAGE
════════════════════════════════════════════ -->
@if($story->featured_image)
<section class="story-featured">
    <div class="story-featured-img">
        <img src="{{ asset('storage/' . $story->featured_image) }}" alt="{{ $story->title }}">
    </div>
</section>
@endif

<!-- ═══════════════════════════════════════════
     CONTENT
════════════════════════════════════════════ -->
<section class="story-content-section">
    <div class="story-content-inner">
        @if($story->summary)
            <div class="story-summary">
                {{ $story->summary }}
            </div>
        @endif

        <div class="story-body">
            {!! nl2br(e($story->content)) !!}
        </div>

        @if($story->structured_content)
            @php
                $structured = is_string($story->structured_content) 
                    ? json_decode($story->structured_content, true) 
                    : $story->structured_content;
            @endphp

            @if(isset($structured['problem']) && $structured['problem'])
                <div class="structured-section">
                    <h3>🎯 The Problem</h3>
                    <p>{!! nl2br(e($structured['problem'])) !!}</p>
                </div>
            @endif

            @if(isset($structured['approach']) && $structured['approach'])
                <div class="structured-section">
                    <h3>💡 Our Approach</h3>
                    <p>{!! nl2br(e($structured['approach'])) !!}</p>
                </div>
            @endif

            @if(isset($structured['outcome']) && $structured['outcome'])
                <div class="structured-section">
                    <h3>✨ Outcomes & Impact</h3>
                    <p>{!! nl2br(e($structured['outcome'])) !!}</p>
                </div>
            @endif

            @if(isset($structured['lessons']) && $structured['lessons'])
                <div class="structured-section">
                    <h3>📚 Lessons Learned</h3>
                    <p>{!! nl2br(e($structured['lessons'])) !!}</p>
                </div>
            @endif
        @endif
    </div>
</section>

<!-- ═══════════════════════════════════════════
     RELATED STORIES
════════════════════════════════════════════ -->
@if($relatedStories->isNotEmpty())
<section class="related-stories">
    <div class="related-inner">
        <h2 class="section-title">More Stories from {{ $story->organization->name }}</h2>
        <div class="story-grid">
            @foreach($relatedStories as $related)
                <a href="{{ route('stories.show', $related->slug) }}" class="story-card">
                    <div class="story-card-thumb">
                        @if($related->featured_image)
                            <img src="{{ asset('storage/' . $related->featured_image) }}" alt="{{ $related->title }}">
                        @else
                            📖
                        @endif
                    </div>
                    <div class="story-card-body">
                        <h4>{{ $related->title }}</h4>
                        <p>{{ Str::limit($related->summary, 100) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
