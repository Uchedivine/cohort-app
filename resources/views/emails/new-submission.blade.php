@extends('emails.layout')

@section('title', 'New Submission')

@section('content')
    <p class="email-greeting">New Submission Awaiting Review</p>
    
    <div class="email-content">
        <p>A new <strong>{{ $submittableType }}</strong> has been submitted and is awaiting your review.</p>
        
        <div class="email-info-box">
            <p style="margin: 0;"><strong>Title:</strong> {{ $submittableTitle }}</p>
            <p style="margin: 8px 0 0 0;"><strong>Submitted by:</strong> {{ $submitterName }} ({{ $organizationName }})</p>
            <p style="margin: 8px 0 0 0;"><strong>Submitted at:</strong> {{ $submittedAt->format('M d, Y g:i A') }}</p>
        </div>
        
        <p>Please review this submission at your earliest convenience.</p>
        
        <a href="{{ $reviewUrl }}" class="email-button">Review Submission</a>
        
        <p style="margin-top: 24px; font-size: 14px;">
            <a href="{{ $queueUrl }}" style="color: #059669;">View all pending submissions</a>
        </p>
    </div>
@endsection
