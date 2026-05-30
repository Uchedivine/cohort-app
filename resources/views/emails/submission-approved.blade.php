@extends('emails.layout')

@section('title', 'Submission Approved')

@section('content')
    <p class="email-greeting">Great news!</p>
    
    <div class="email-content">
        <p>Your <strong>{{ $submittableType }}</strong> titled "<strong>{{ $submittableTitle }}</strong>" has been approved and published.</p>
        
        <div class="email-info-box">
            <p style="margin: 0;"><strong>Reviewed by:</strong> {{ $reviewerName }}</p>
            @if($reviewerNotes)
                <p style="margin: 8px 0 0 0;"><strong>Notes:</strong> {{ $reviewerNotes }}</p>
            @endif
        </div>
        
        <p>Your content is now live and visible to the public. Thank you for your contribution to the Cohort community!</p>
        
        @if($viewUrl)
            <a href="{{ $viewUrl }}" class="email-button">View Published Content</a>
        @endif
    </div>
@endsection
