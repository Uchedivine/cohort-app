@extends('layouts.app')
@section('title', 'Events Calendar')

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

    .view-toggle {
        background: var(--cream);
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
    }
    .view-toggle-inner {
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        gap: 10px;
    }
    .view-btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--white);
        color: var(--navy);
        cursor: pointer;
        transition: all 0.2s;
    }
    .view-btn.active {
        background: var(--navy);
        color: var(--white);
        border-color: var(--navy);
    }
    .view-btn:hover:not(.active) {
        border-color: var(--navy);
    }

    .events-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 60vh;
    }
    .events-inner {
        max-width: 1100px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
    }

    .event-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-bottom: 3rem;
    }
    .event-item {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
        text-decoration: none;
        color: var(--text);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .event-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.15),
                    0 0 0 3px rgba(15, 23, 42, 0.1);
        border-color: var(--navy);
    }
    .event-date-box {
        background: var(--green);
        color: var(--white);
        border-radius: 8px;
        min-width: 70px;
        text-align: center;
        padding: 12px 10px;
        flex-shrink: 0;
    }
    .event-date-day {
        font-family: 'Cormorant Garamond', serif;
        font-size: 2rem;
        font-weight: 600;
        line-height: 1;
        display: block;
    }
    .event-date-month {
        font-size: 0.7rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        opacity: 0.9;
        margin-top: 4px;
    }
    .event-info {
        flex: 1;
    }
    .event-tags {
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
    .event-info h3 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .event-meta {
        font-size: 0.85rem;
        color: var(--muted);
        margin-bottom: 10px;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .event-info p {
        font-size: 0.95rem;
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

    /* Calendar View Styles */
    #calendar-view {
        display: none;
    }
    #calendar-view.active {
        display: block;
    }
    #list-view.active {
        display: block;
    }
    #list-view {
        display: none;
    }

    /* FullCalendar customization */
    .fc {
        background: var(--white);
        border-radius: 10px;
        padding: 1.5rem;
        border: 1px solid var(--border);
    }
    .fc-toolbar-title {
        font-family: 'Cormorant Garamond', serif !important;
        color: var(--navy) !important;
        font-size: 1.8rem !important;
    }
    .fc-button {
        background: var(--navy) !important;
        border-color: var(--navy) !important;
    }
    .fc-button:hover {
        background: var(--gold) !important;
        border-color: var(--gold) !important;
        color: var(--navy) !important;
    }
    .fc-button-active {
        background: var(--gold) !important;
        border-color: var(--gold) !important;
        color: var(--navy) !important;
    }
    .fc-event {
        background: var(--green) !important;
        border-color: var(--green) !important;
        cursor: pointer;
    }
    .fc-event:hover {
        background: var(--gold) !important;
        border-color: var(--gold) !important;
    }

    @media (max-width: 768px) {
        .page-hero { padding: 60px 1.25rem 50px; }
        .events-section { padding: 2rem 1.25rem; }
        .event-item { flex-direction: row; gap: 1rem; }
        .event-date-box { min-width: 60px; padding: 10px 8px; }
        .event-date-day { font-size: 1.6rem; }
        .event-meta { gap: 0.75rem; }
        .fc { padding: 1rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HERO
════════════════════════════════════════════ -->
<section class="page-hero">
    <h1>Events Calendar</h1>
    <p>Upcoming cohort events, workshops, webinars, and community gatherings.</p>
</section>

<!-- ═══════════════════════════════════════════
     VIEW TOGGLE
════════════════════════════════════════════ -->
<section class="view-toggle">
    <div class="view-toggle-inner">
        <button class="view-btn active" id="listViewBtn" onclick="switchView('list')">📋 List View</button>
        <button class="view-btn" id="calendarViewBtn" onclick="switchView('calendar')">📅 Calendar View</button>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     EVENTS LIST VIEW
════════════════════════════════════════════ -->
<section class="events-section">
    <div class="events-inner">
        <div id="list-view" class="active">
            <!-- Upcoming Events -->
            @if($upcomingEvents->isNotEmpty())
                <h2 class="section-title">Upcoming Events</h2>
                <div class="event-list">
                    @foreach($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event->slug) }}" class="event-item">
                            <div class="event-date-box">
                                <span class="event-date-day">{{ $event->start_date->format('d') }}</span>
                                <span class="event-date-month">{{ $event->start_date->format('M') }}</span>
                            </div>
                            <div class="event-info">
                                @if($event->tags->isNotEmpty())
                                    <div class="event-tags">
                                        @foreach($event->tags->take(3) as $tag)
                                            <span class="tag-pill">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <h3>{{ $event->title }}</h3>
                                <div class="event-meta">
                                    <span class="event-meta-item">
                                        📍 {{ $event->location ?? 'Virtual' }}
                                    </span>
                                    <span class="event-meta-item">
                                        🕐 {{ $event->start_date->format('g:i A') }}
                                        @if($event->end_date)
                                            - {{ $event->end_date->format('g:i A') }}
                                        @endif
                                    </span>
                                    @if($event->virtual_link)
                                        <span class="event-meta-item">💻 Virtual</span>
                                    @endif
                                </div>
                                <p>{{ Str::limit($event->description, 180) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📅</div>
                    <h3 style="font-size: 1.3rem; color: var(--navy); margin-bottom: 0.5rem;">No upcoming events</h3>
                    <p>Check back soon for new events and activities.</p>
                </div>
            @endif

            <!-- Past Events -->
            @if($pastEvents->isNotEmpty())
                <h2 class="section-title" style="margin-top: 3rem;">Past Events</h2>
                <div class="event-list">
                    @foreach($pastEvents as $event)
                        <a href="{{ route('events.show', $event->slug) }}" class="event-item" style="opacity: 0.85;">
                            <div class="event-date-box" style="background: #6b7280;">
                                <span class="event-date-day">{{ $event->start_date->format('d') }}</span>
                                <span class="event-date-month">{{ $event->start_date->format('M') }}</span>
                            </div>
                            <div class="event-info">
                                @if($event->tags->isNotEmpty())
                                    <div class="event-tags">
                                        @foreach($event->tags->take(3) as $tag)
                                            <span class="tag-pill">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <h3>{{ $event->title }}</h3>
                                <div class="event-meta">
                                    <span class="event-meta-item">
                                        📍 {{ $event->location ?? 'Virtual' }}
                                    </span>
                                    <span class="event-meta-item">
                                        📅 {{ $event->start_date->format('M d, Y') }}
                                    </span>
                                </div>
                                <p>{{ Str::limit($event->description, 180) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($pastEvents->hasMorePages())
                    <div style="margin-top: 2rem;">
                        {{ $pastEvents->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ═══════════════════════════════════════════
             CALENDAR VIEW
        ════════════════════════════════════════════ -->
        <div id="calendar-view">
            <div id="calendar"></div>
        </div>
    </div>
</section>

<!-- FullCalendar CDN -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
    let calendar;
    
    // View switching
    function switchView(view) {
        const listView = document.getElementById('list-view');
        const calendarView = document.getElementById('calendar-view');
        const listBtn = document.getElementById('listViewBtn');
        const calendarBtn = document.getElementById('calendarViewBtn');
        
        if (view === 'list') {
            listView.classList.add('active');
            calendarView.classList.remove('active');
            listBtn.classList.add('active');
            calendarBtn.classList.remove('active');
        } else {
            listView.classList.remove('active');
            calendarView.classList.add('active');
            listBtn.classList.remove('active');
            calendarBtn.classList.add('active');
            
            // Initialize calendar if not already done
            if (!calendar) {
                initCalendar();
            }
        }
    }
    
    // Initialize FullCalendar
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            events: '{{ route('events.calendar-data') }}',
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },
            eventDisplay: 'block',
            displayEventTime: true,
            height: 'auto',
            contentHeight: 650,
            aspectRatio: 1.8,
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            }
        });
        
        calendar.render();
    }
</script>

@endsection
