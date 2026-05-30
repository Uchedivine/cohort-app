@extends('emails.layout')

@section('title', 'New Event')

@section('content')
    <p class="email-greeting">New Event Published</p>
    
    <div class="email-content">
        <h2 style="color: #0f172a; font-family: 'Cormorant Garamond', Georgia, serif; font-size: 24px; margin: 0 0 16px 0;">
            {{ $eventTitle }}
        </h2>
        
        @if($eventDescription)
            <p>{{ Str::limit($eventDescription, 200) }}</p>
        @endif
        
        <div class="email-info-box">
            <p style="margin: 0;"><strong>📅 Date:</strong> {{ $startDate->format('M d, Y') }}
                @if($endDate && !$startDate->isSameDay($endDate))
                    - {{ $endDate->format('M d, Y') }}
                @endif
            </p>
            
            @if($location)
                <p style="margin: 8px 0 0 0;"><strong>📍 Location:</strong> {{ $location }}</p>
            @endif
            
            @if($virtualLink)
                <p style="margin: 8px 0 0 0;"><strong>💻 Virtual:</strong> <a href="{{ $virtualLink }}">Join online</a></p>
            @endif
        </div>
        
        <a href="{{ $eventUrl }}" class="email-button">View Event Details</a>
        
        @if($rsvpLink)
            <p style="margin-top: 16px;">
                <a href="{{ $rsvpLink }}" style="color: #059669;">RSVP for this event</a>
            </p>
        @endif
    </div>
@endsection
