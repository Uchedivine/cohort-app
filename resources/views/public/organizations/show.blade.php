@extends('layouts.app')
@section('title', $organization->name)

@section('content')

<style>
    .org-hero {
        background: var(--navy);
        padding: 80px 2rem 60px;
        color: var(--white);
    }
    .org-hero-inner {
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        gap: 2.5rem;
        align-items: flex-start;
    }
    .org-logo-box {
        flex-shrink: 0;
        width: 140px;
        height: 140px;
        background: var(--white);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 3px solid var(--gold);
    }
    .org-logo-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 12px;
    }
    .org-logo-placeholder {
        font-size: 3.5rem;
    }
    .org-hero-content h1 {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 600;
        color: var(--white);
        margin-bottom: 0.75rem;
        line-height: 1.2;
    }
    .org-tagline {
        font-size: 1.1rem;
        color: #94a3b8;
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    .org-meta {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: #cbd5e1;
    }
    .org-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .org-section {
        background: var(--cream);
        padding: 60px 2rem;
    }
    .org-section-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .org-section-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
    }
    .org-content {
        font-size: 1rem;
        line-height: 1.8;
        color: var(--text);
    }
    .org-content p {
        margin-bottom: 1rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .info-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
    }
    .info-card h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-card ul {
        list-style: none;
        padding: 0;
    }
    .info-card li {
        padding: 6px 0;
        color: #4b5563;
        font-size: 0.9rem;
    }

    .tag-list {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .tag-pill {
        font-size: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        background: var(--green-light);
        color: var(--green);
        padding: 5px 12px;
        border-radius: 20px;
    }

    .social-links {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .social-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: var(--navy);
        color: var(--white);
        text-decoration: none;
        border-radius: 6px;
        font-size: 0.85rem;
        transition: background 0.2s;
    }
    .social-link:hover {
        background: var(--gold);
        color: var(--navy);
    }

    .related-content {
        background: #eeeae0;
        padding: 60px 2rem;
    }
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    .content-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow 0.25s, transform 0.25s;
    }
    .content-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    .content-thumb {
        height: 160px;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    .content-body {
        padding: 1.25rem;
    }
    .content-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .content-card p {
        font-size: 0.85rem;
        color: #6b7280;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .org-hero { padding: 60px 1.25rem 50px; }
        .org-hero-inner { flex-direction: column; align-items: center; text-align: center; }
        .org-logo-box { width: 120px; height: 120px; }
        .org-meta { justify-content: center; }
        .org-section { padding: 48px 1.25rem; }
        .info-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ═══════════════════════════════════════════
     ORG HERO
════════════════════════════════════════════ -->
<section class="org-hero">
    <div class="org-hero-inner">
        <div class="org-logo-box">
            @if($organization->logo)
                <img src="{{ asset('storage/' . $organization->logo) }}" alt="{{ $organization->name }}">
            @else
                <span class="org-logo-placeholder">🏢</span>
            @endif
        </div>
        <div class="org-hero-content">
            <h1>{{ $organization->name }}</h1>
            <p class="org-tagline">{{ $organization->short_description }}</p>
            <div class="org-meta">
                @if($organization->location)
                    <span class="org-meta-item">📍 {{ $organization->location }}</span>
                @endif
                @if($organization->thematic_focus)
                    <span class="org-meta-item">🎯 {{ $organization->thematic_focus }}</span>
                @endif
                @if($organization->founded_year)
                    <span class="org-meta-item">📅 Est. {{ $organization->founded_year }}</span>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     ABOUT
════════════════════════════════════════════ -->
<section class="org-section">
    <div class="org-section-inner">
        <h2 class="org-section-title">About</h2>
        <div class="org-content">
            {!! nl2br(e($organization->full_profile)) !!}
        </div>

        <div class="info-grid">
            @if($organization->tags->isNotEmpty())
                <div class="info-card">
                    <h3>🏷️ Focus Areas & SDGs</h3>
                    <div class="tag-list">
                        @foreach($organization->tags as $tag)
                            <span class="tag-pill">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($organization->programs)
                <div class="info-card">
                    <h3>📋 Programs</h3>
                    <div class="org-content">
                        {!! nl2br(e($organization->programs)) !!}
                    </div>
                </div>
            @endif

            @if($organization->highlights)
                <div class="info-card">
                    <h3>⭐ Highlights</h3>
                    <div class="org-content">
                        {!! nl2br(e($organization->highlights)) !!}
                    </div>
                </div>
            @endif

            @if($organization->website || $organization->facebook || $organization->twitter || $organization->linkedin || $organization->instagram)
                <div class="info-card">
                    <h3>🔗 Connect</h3>
                    <div class="social-links">
                        @if($organization->website)
                            <a href="{{ $organization->website }}" target="_blank" class="social-link">🌐 Website</a>
                        @endif
                        @if($organization->facebook)
                            <a href="{{ $organization->facebook }}" target="_blank" class="social-link">📘 Facebook</a>
                        @endif
                        @if($organization->twitter)
                            <a href="{{ $organization->twitter }}" target="_blank" class="social-link">🐦 Twitter</a>
                        @endif
                        @if($organization->linkedin)
                            <a href="{{ $organization->linkedin }}" target="_blank" class="social-link">💼 LinkedIn</a>
                        @endif
                        @if($organization->instagram)
                            <a href="{{ $organization->instagram }}" target="_blank" class="social-link">📷 Instagram</a>
                        @endif
                    </div>
                    @if($organization->contact_email)
                        <p style="margin-top: 1rem; font-size: 0.9rem; color: #6b7280;">
                            ✉️ {{ $organization->contact_email }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     RELATED STORIES
════════════════════════════════════════════ -->
@if($stories->isNotEmpty())
<section class="related-content">
    <div class="org-section-inner">
        <h2 class="org-section-title">Stories from {{ $organization->name }}</h2>
        <div class="content-grid">
            @foreach($stories as $story)
                <a href="{{ route('stories.show', $story->slug) }}" class="content-card">
                    <div class="content-thumb">
                        @if($story->featured_image)
                            <img src="{{ asset('storage/' . $story->featured_image) }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            📖
                        @endif
                    </div>
                    <div class="content-body">
                        <h3>{{ $story->title }}</h3>
                        <p>{{ Str::limit($story->summary, 100) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ═══════════════════════════════════════════
     RELATED RESOURCES
════════════════════════════════════════════ -->
@if($resources->isNotEmpty())
<section class="org-section">
    <div class="org-section-inner">
        <h2 class="org-section-title">Resources from {{ $organization->name }}</h2>
        <div class="content-grid">
            @foreach($resources as $resource)
                <a href="{{ route('resources.show', $resource->slug) }}" class="content-card">
                    <div class="content-thumb">📚</div>
                    <div class="content-body">
                        <h3>{{ $resource->title }}</h3>
                        <p>{{ Str::limit($resource->description, 100) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
