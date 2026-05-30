@extends('emails.layout')

@section('title', 'Welcome to Cohort')

@section('content')
    <p class="email-greeting">Welcome to Cohort, {{ $userName }}!</p>
    
    <div class="email-content">
        <p>Your account has been created successfully. You can now access the Cohort Web App.</p>
        
        <div class="email-info-box">
            <p style="margin: 0;"><strong>Email:</strong> {{ $userEmail }}</p>
            <p style="margin: 8px 0 0 0;"><strong>Temporary Password:</strong> <code style="background: #fff; padding: 4px 8px; border-radius: 4px;">{{ $temporaryPassword }}</code></p>
            <p style="margin: 8px 0 0 0;"><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $role)) }}</p>
            @if($organizationName)
                <p style="margin: 8px 0 0 0;"><strong>Organization:</strong> {{ $organizationName }}</p>
            @endif
        </div>
        
        <p><strong>Important:</strong> Please change your password after your first login for security purposes.</p>
        
        <a href="{{ $loginUrl }}" class="email-button">Login to Your Account</a>
        
        <hr>
        
        <h3 style="color: #0f172a; font-size: 16px; margin: 24px 0 12px 0;">Getting Started</h3>
        
        @if($role === 'org_editor')
            <ul style="color: #334155; padding-left: 20px;">
                <li>Update your organization profile</li>
                <li>Submit stories about your work</li>
                <li>Upload resources to share with the community</li>
                <li>Track your submission status</li>
            </ul>
        @elseif($role === 'secretary')
            <ul style="color: #334155; padding-left: 20px;">
                <li>Review pending submissions</li>
                <li>Manage user accounts</li>
                <li>Create and publish events</li>
                <li>Manage tags and categories</li>
            </ul>
        @endif
        
        <p>If you have any questions, please don't hesitate to reach out to our support team.</p>
    </div>
@endsection
