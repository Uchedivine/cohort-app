@extends('emails.layout')

@section('title', 'Monthly Digest')

@section('content')
    <p class="email-greeting">Hello {{ $userName }},</p>
    
    <div class="email-content">
        <p>Here's your monthly digest for <strong>{{ $month }}</strong>.</p>
        
        <div class="email-info-box">
            <h3 style="margin: 0 0 12px 0; color: #0f172a; font-size: 16px;">📊 This Month's Activity</h3>
            <p style="margin: 4px 0;"><strong>New Stories:</strong> {{ $statistics['stories_count'] ?? 0 }}</p>
            <p style="margin: 4px 0;"><strong>New Resources:</strong> {{ $statistics['resources_count'] ?? 0 }}</p>
            <p style="margin: 4px 0;"><strong>Events:</strong> {{ $statistics['events_count'] ?? 0 }}</p>
        </div>
        
        @if($events->isNotEmpty())
            <h3 style="color: #0f172a; font-size: 18px; margin: 32px 0 16px 0;">📅 Upcoming Events</h3>
            @foreach($events as $event)
                <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                    <p style="margin: 0; font-weight: 600; color: #0f172a;">{{ $event->title }}</p>
                    <p style="margin: 4px 0; font-size: 14px; color: #64748b;">
                        {{ $event->start_date->format('M d, Y') }}
                        @if($event->location)
                            • {{ $event->location }}
                        @endif
                    </p>
                </div>
            @endforeach
        @endif
        
        @if($stories->isNotEmpty())
            <h3 style="color: #0f172a; font-size: 18px; margin: 32px 0 16px 0;">📖 Recent Stories</h3>
            @foreach($stories as $story)
                <div style="margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                    <p style="margin: 0; font-weight: 600; color: #0f172a;">{{ $story->title }}</p>
                    <p style="margin: 4px 0; font-size: 14px; color: #64748b;">
                        by {{ $story->organization->name ?? 'Unknown' }}
                    </p>
                </div>
            @endforeach
        @endif
        
        <a href="{{ $homeUrl }}" class="email-button">Visit Cohort</a>
    </div>
@endsection
