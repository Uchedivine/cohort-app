@extends('emails.layout')

@section('title', 'Changes Requested')

@section('content')
    <p class="email-greeting">Changes Requested</p>
    
    <div class="email-content">
        <p>Your <strong>{{ $submittableType }}</strong> titled "<strong>{{ $submittableTitle }}</strong>" needs some changes before it can be published.</p>
        
        <div class="email-info-box">
            <p style="margin: 0;"><strong>Reviewed by:</strong> {{ $reviewerName }}</p>
            <p style="margin: 8px 0 0 0;"><strong>Feedback:</strong> {{ $feedback }}</p>
        </div>
        
        <p>Please review the feedback and make the necessary changes. Once you're ready, you can resubmit your content for review.</p>
        
        <a href="{{ $editUrl }}" class="email-button">Edit & Resubmit</a>
    </div>
@endsection
