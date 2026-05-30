@extends('layouts.app')
@section('title', 'Home')

@section('content')

<style>
    /* ── Scroll animations ── */
    .reveal {
        opacity: 0;
        transform: translateY(32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }
    .reveal-delay-4 { transition-delay: 0.4s; }

    /* ── Hero ── */
    .hero {
        background: var(--navy);
        color: var(--white);
        padding: 100px 2rem 80px;
        text-align: center;
    }
    .hero h1 {
        font-size: clamp(2.4rem, 6vw, 4.2rem);
        font-weight: 600;
        line-height: 1.15;
        margin-bottom: 1.25rem;
        color: var(--white);
    }
    .hero h1 em {
        color: var(--gold);
        font-style: italic;
    }
    .hero p {
        font-size: clamp(0.95rem, 2vw, 1.1rem);
        color: #94a3b8;
        max-width: 580px;
        margin: 0 auto 3rem;
        line-height: 1.75;
    }

    /* ── Stats ── */
    .stats {
        display: flex;
        justify-content: center;
        gap: clamp(2rem, 6vw, 6rem);
        flex-wrap: wrap;
        padding: 0 1rem;
    }
    .stat-item { text-align: center; }
    .stat-number {
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 600;
        color: var(--gold);
        display: block;
    }
    .stat-label {
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #64748b;
        margin-top: 4px;
    }

    /* ── Section wrapper ── */
    .section {
        background: var(--cream);
        padding: 72px 2rem;
    }
    .section-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .section-title {
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 2.5rem;
    }

    /* ── Module cards ── */
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
    }
    .module-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem 1.5rem;
        text-align: center;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow 0.25s, transform 0.25s, border-color 0.25s;
    }
    .module-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
        transform: translateY(-4px);
        border-color: var(--gold);
    }
    .module-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }
    .module-card h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 6px;
    }
    .module-card p {
        font-size: 0.82rem;
        color: var(--muted);
    }

    /* ── Story cards ── */
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
    .story-thumb {
        height: 180px;
        background: #e8e4dc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    .story-body { padding: 1.25rem; }
    .story-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
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
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 6px;
        line-height: 1.3;
    }
    .story-meta {
        font-size: 0.78rem;
        color: var(--muted);
        margin-bottom: 10px;
    }
    .story-card p {
        font-size: 0.85rem;
        color: #4b5563;
        line-height: 1.6;
    }

    /* ── Event list ── */
    .event-list { display: flex; flex-direction: column; gap: 1rem; }
    .event-item {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        text-decoration: none;
        color: var(--text);
        transition: box-shadow 0.25s, border-color 0.25s;
    }
    .event-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        border-color: var(--gold);
    }
    .event-date-box {
        background: var(--green);
        color: var(--white);
        border-radius: 6px;
        min-width: 54px;
        text-align: center;
        padding: 8px 6px;
        flex-shrink: 0;
    }
    .event-date-day {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.6rem;
        font-weight: 600;
        line-height: 1;
        display: block;
    }
    .event-date-month {
        font-size: 0.65rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        opacity: 0.85;
    }
    .event-info h4 {
        font-size: 1rem;
        font-weight: 500;
        color: var(--navy);
        margin-bottom: 4px;
    }
    .event-location {
        font-size: 0.78rem;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .event-info p {
        font-size: 0.82rem;
        color: #6b7280;
        line-height: 1.5;
    }

    /* ── CTA ── */
    .cta-section {
        background: var(--navy);
        padding: 72px 2rem;
        text-align: center;
    }
    .cta-section h2 {
        font-size: clamp(1.8rem, 3.5vw, 2.6rem);
        color: var(--white);
        margin-bottom: 1rem;
    }
    .cta-section p {
        color: #94a3b8;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }
    .btn-gold {
        background: var(--gold);
        color: var(--navy);
        padding: 12px 28px;
        border-radius: 5px;
        font-weight: 500;
        font-size: 0.9rem;
        text-decoration: none;
        display: inline-block;
        transition: background 0.2s;
    }
    .btn-gold:hover { background: var(--gold-dark); }

    /* ── Mobile ── */
    @media (max-width: 640px) {
        .hero { padding: 60px 1.25rem 50px; }

        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem 1rem;
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
        }

        .section { padding: 48px 1.25rem; }

        .module-grid {
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .module-card { padding: 1.5rem 1rem; }
        .module-icon { font-size: 1.6rem; margin-bottom: 0.6rem; }
        .module-card h3 { font-size: 0.95rem; }
        .module-card p  { font-size: 0.75rem; }

        .story-grid { grid-template-columns: 1fr; }

        .event-item { flex-direction: row; gap: 1rem; align-items: flex-start; }
        .event-date-box { min-width: 50px; }
        .event-date-day { font-size: 1.4rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="hero">
    <div style="max-width: 780px; margin: 0 auto;">
        <h1 class="reveal">
            Amplifying <em>African</em> Civil<br>Society Impact
        </h1>
        <p class="reveal reveal-delay-1">
            A curated cohort of organizations driving systems change across health,
            environment, digital equity, food security, and community resilience.
        </p>

        <div class="stats reveal reveal-delay-2">
            <div class="stat-item">
                <span class="stat-number">15</span>
                <span class="stat-label">Organizations</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">7</span>
                <span class="stat-label">Countries</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">2M+</span>
                <span class="stat-label">Lives Reached</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">12</span>
                <span class="stat-label">SDGs Addressed</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     MODULE CARDS
════════════════════════════════════════════ -->
<section class="section">
    <div class="section-inner">
        <div class="module-grid">
            <a href="{{ route('organizations.index') }}" class="module-card reveal">
                <span class="module-icon">🏢</span>
                <h3>Organization Directory</h3>
                <p>Meet the cohort members</p>
            </a>
            <a href="{{ route('stories.index') }}" class="module-card reveal reveal-delay-1">
                <span class="module-icon">📖</span>
                <h3>Story Bank</h3>
                <p>Real impact, real stories</p>
            </a>
            <a href="{{ route('resources.index') }}" class="module-card reveal reveal-delay-2">
                <span class="module-icon">📚</span>
                <h3>Resources Library</h3>
                <p>Tools, guides &amp; research</p>
            </a>
            <a href="{{ route('events.index') }}" class="module-card reveal reveal-delay-3">
                <span class="module-icon">📅</span>
                <h3>Events Calendar</h3>
                <p>Upcoming cohort events</p>
            </a>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     LATEST STORIES
════════════════════════════════════════════ -->
<section class="section" style="background: #eeeae0; padding-top: 0;">
    <div class="section-inner">
        <h2 class="section-title reveal">Latest Stories</h2>
        <div class="story-grid">
            @forelse($latestStories ?? [] as $story)
                <a href="{{ route('stories.show', $story->slug) }}" class="story-card reveal">
                    <div class="story-thumb">
                        @if($story->featured_image)
                            <img src="{{ asset('storage/' . $story->featured_image) }}" style="width:100%; height:100%; object-fit:cover;">
                        @else
                            🌿
                        @endif
                    </div>
                    <div class="story-body">
                        <div class="story-tags">
                            @foreach($story->tags->take(3) as $tag)
                                <span class="tag-pill">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        <h3>{{ $story->title }}</h3>
                        <p class="story-meta">{{ $story->author }} · {{ $story->published_at?->format('Y-m-d') }}</p>
                        <p>{{ Str::limit($story->summary, 100) }}</p>
                    </div>
                </a>
            @empty
                <p style="color: var(--muted); font-size: 0.9rem;">No stories published yet.</p>
            @endforelse
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     UPCOMING EVENTS
════════════════════════════════════════════ -->
<section class="section">
    <div class="section-inner">
        <h2 class="section-title reveal">Upcoming Events</h2>
        <div class="event-list">
            @forelse($upcomingEvents ?? [] as $event)
                <a href="{{ route('events.show', $event->slug) }}" class="event-item reveal">
                    <div class="event-date-box">
                        <span class="event-date-day">{{ $event->start_date->format('d') }}</span>
                        <span class="event-date-month">{{ $event->start_date->format('M') }}</span>
                    </div>
                    <div class="event-info">
                        <h4>{{ $event->title }}</h4>
                        <p class="event-location">
                            📍 {{ $event->location ?? 'Virtual' }} ·
                            {{ $event->virtual_link ? 'Virtual' : 'In-person' }}
                        </p>
                        <p>{{ Str::limit($event->description, 120) }}</p>
                    </div>
                </a>
            @empty
                <p style="color: var(--muted); font-size: 0.9rem;">No upcoming events.</p>
            @endforelse
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     CTA
════════════════════════════════════════════ -->
<section class="cta-section">
    <h2 class="reveal">Are you a cohort member?</h2>
    <p class="reveal reveal-delay-1">Log in to your member portal to submit stories, resources and updates.</p>
    <a href="{{ route('login') }}" class="btn-gold reveal reveal-delay-2">Access Member Portal →</a>
</section>

<!-- ═══════════════════════════════════════════
     SCROLL ANIMATION JS
════════════════════════════════════════════ -->
<script>
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.12 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

@endsection