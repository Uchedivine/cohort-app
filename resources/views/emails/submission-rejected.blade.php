@extends('emails.layout')

@section('title', 'Submission Rejected')

@section('content')
    <p class="email-greeting">Submission Update</p>
    
    <div class="email-content">
        <p>Your <strong>{{ $submittableType }}</strong> titled "<strong>{{ $submittableTitle }}</strong>" has been rejected.</p>
        
        <div class="email-warning-box">
            <p style="margin: 0;"><strong>Reviewed by:</strong> {{ $reviewerName }}</p>
            <p style="margin: 8px 0 0 0;"><strong>Reason:</strong> {{ $reason }}</p>
        </div>
        
        <p>If you have questions about this decision, please contact the review team.</p>
        
        <a href="{{ $dashboardUrl }}" class="email-button">Go to Dashboard</a>
    </div>
@endsection
