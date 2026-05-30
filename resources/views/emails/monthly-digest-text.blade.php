Hello {{ $userName }},

Here's your monthly digest for {{ $month }}.

THIS MONTH'S ACTIVITY
- New Stories: {{ $statistics['stories_count'] ?? 0 }}
- New Resources: {{ $statistics['resources_count'] ?? 0 }}
- Events: {{ $statistics['events_count'] ?? 0 }}

@if($events->isNotEmpty())
UPCOMING EVENTS
@foreach($events as $event)
- {{ $event->title }}
  {{ $event->start_date->format('M d, Y') }}@if($event->location) • {{ $event->location }}@endif

@endforeach
@endif

@if($stories->isNotEmpty())
RECENT STORIES
@foreach($stories as $story)
- {{ $story->title }}
  by {{ $story->organization->name ?? 'Unknown' }}

@endforeach
@endif

Visit Cohort: {{ $homeUrl }}

---
This email was sent by Cohort Web App
Visit our website: {{ route('home') }}
