@extends('layouts.app')
@section('title', $event->title)

@section('content')

<style>
    .event-hero {
        background: var(--navy);
        padding: 80px 2rem 0;
    }
    .event-hero-inner {
        max-width: 1000px;
        margin: 0 auto;
        padding-bottom: 3rem;
    }
    .event-breadcrumb {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 1.5rem;
    }
    .event-breadcrumb a {
        color: var(--gold);
        text-decoration: none;
    }
    .event-breadcrumb a:hover {
        text-decoration: underline;
    }
    .event-tags {
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
    .event-hero h1 {
        font-size: clamp(2rem, 5vw, 3rem);
        font-weight: 600;
        color: var(--white);
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    .event-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .event-meta-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .event-meta-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #94a3b8;
    }
    .event-meta-value {
        font-size: 1rem;
        color: var(--white);
        font-weight: 500;
    }

    .event-banner {
        max-width: 1000px;
        margin: -60px auto 0;
        position: relative;
        z-index: 10;
    }
    .event-banner-img {
        width: 100%;
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .event-banner-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .event-content-section {
        background: var(--cream);
        padding: 5rem 2rem;
    }
    .event-content-inner {
        max-width: 900px;
        margin: 0 auto;
    }
    .event-description {
        font-size: 1.05rem;
        line-height: 1.9;
        color: var(--text);
        margin-bottom: 3rem;
    }
    .event-description p {
        margin-bottom: 1.5rem;
    }

    .event-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 3rem;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--gold);
        color: var(--navy);
    }
    .btn-primary:hover {
        background: var(--gold-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }
    .btn-secondary {
        background: var(--navy);
        color: var(--white);
    }
    .btn-secondary:hover {
        background: #1e293b;
    }

    .info-box {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .info-box h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .info-box p {
        color: #4b5563;
        line-height: 1.7;
    }

    .media-gallery {
        background: #eeeae0;
        padding: 4rem 2rem;
    }
    .media-inner {
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
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .media-item {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .media-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
    }
    .media-thumb {
        height: 200px;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        overflow: hidden;
    }
    .media-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .media-caption {
        padding: 1rem;
        font-size: 0.85rem;
        color: #6b7280;
    }

    @media (max-width: 768px) {
        .event-hero { padding: 60px 1.25rem 0; }
        .event-banner { margin-top: -40px; padding: 0 1.25rem; }
        .event-banner-img { height: 250px; }
        .event-content-section { padding: 3rem 1.25rem; }
        .event-meta-grid { grid-template-columns: 1fr; }
        .event-actions { flex-direction: column; }
        .btn { width: 100%; justify-content: center; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="event-hero">
    <div class="event-hero-inner">
        <div class="event-breadcrumb">
            <a href="{{ route('home') }}">Home</a> / 
            <a href="{{ route('events.index') }}">Events</a> / 
            {{ $event->title }}
        </div>

        @if($event->tags->isNotEmpty())
            <div class="event-tags">
                @foreach($event->tags as $tag)
                    <span class="tag-pill">{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <h1>{{ $event->title }}</h1>

        <div class="event-meta-grid">
            <div class="event-meta-item">
                <span class="event-meta-label">Date</span>
                <span class="event-meta-value">{{ $event->start_date->format('F d, Y') }}</span>
            </div>
            <div class="event-meta-item">
                <span class="event-meta-label">Time</span>
                <span class="event-meta-value">
                    {{ $event->start_date->format('g:i A') }}
                    @if($event->end_date)
                        - {{ $event->end_date->format('g:i A') }}
                    @endif
                </span>
            </div>
            <div class="event-meta-item">
                <span class="event-meta-label">Location</span>
                <span class="event-meta-value">{{ $event->location ?? 'Virtual' }}</span>
            </div>
            @if($event->virtual_link)
                <div class="event-meta-item">
                    <span class="event-meta-label">Format</span>
                    <span class="event-meta-value">💻 Virtual Event</span>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     BANNER IMAGE
════════════════════════════════════════════ -->
@if($event->banner_image)
<section class="event-banner">
    <div class="event-banner-img">
        <img src="{{ asset('storage/' . $event->banner_image) }}" alt="{{ $event->title }}">
    </div>
</section>
@endif

<!-- ═══════════════════════════════════════════
     CONTENT
════════════════════════════════════════════ -->
<section class="event-content-section">
    <div class="event-content-inner">
        <div class="event-actions">
            @if($event->rsvp_link)
                <a href="{{ $event->rsvp_link }}" target="_blank" class="btn btn-primary">
                    ✓ RSVP Now
                </a>
            @endif
            @if($event->virtual_link)
                <a href="{{ $event->virtual_link }}" target="_blank" class="btn btn-secondary">
                    💻 Join Virtual Event
                </a>
            @endif
        </div>

        <div class="info-box">
            <h3>📋 About This Event</h3>
            <div class="event-description">
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>

        @if($event->location && !$event->virtual_link)
            <div class="info-box">
                <h3>📍 Location Details</h3>
                <p>{{ $event->location }}</p>
            </div>
        @endif

        @if($event->organizations->isNotEmpty())
            <div class="info-box">
                <h3>🏢 Participating Organizations</h3>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    @foreach($event->organizations as $org)
                        <a href="{{ route('organizations.show', $org->slug) }}" 
                           style="color: var(--navy); text-decoration: none; font-weight: 500;">
                            → {{ $org->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ═══════════════════════════════════════════
     MEDIA GALLERY
════════════════════════════════════════════ -->
@if($event->media->isNotEmpty())
<section class="media-gallery">
    <div class="media-inner">
        <h2 class="section-title">Event Gallery</h2>
        <div class="media-grid">
            @foreach($event->media as $media)
                <div class="media-item">
                    @if($media->type === 'image')
                        <div class="media-thumb">
                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->caption }}">
                        </div>
                    @elseif($media->type === 'video')
                        <div class="media-thumb">
                            <a href="{{ $media->video_url }}" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none; font-size: 3rem;">
                                🎥
                            </a>
                        </div>
                    @endif
                    @if($media->caption)
                        <div class="media-caption">{{ $media->caption }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
