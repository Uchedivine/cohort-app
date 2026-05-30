New Event Published

{{ $eventTitle }}

@if($eventDescription)
{{ Str::limit($eventDescription, 200) }}
@endif

Date: {{ $startDate->format('M d, Y') }}@if($endDate && !$startDate->isSameDay($endDate)) - {{ $endDate->format('M d, Y') }}@endif

@if($location)
Location: {{ $location }}
@endif

@if($virtualLink)
Virtual: {{ $virtualLink }}
@endif

View Event Details: {{ $eventUrl }}

@if($rsvpLink)
RSVP: {{ $rsvpLink }}
@endif

---
This email was sent by Cohort Web App
Visit our website: {{ route('home') }}
