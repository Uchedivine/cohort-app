<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DM Sans', Arial, sans-serif; background: #f5f0e8; margin: 0; padding: 2rem; }
        .email-wrap { max-width: 560px; margin: 0 auto; }
        .email-header {
            background: #1a1f2e; padding: 2rem; border-radius: 10px 10px 0 0; text-align: center;
        }
        .email-header h1 { color: #c9a84c; font-size: 1.4rem; margin: 0; }
        .email-body {
            background: #ffffff; padding: 2rem; border-radius: 0 0 10px 10px;
            border: 1px solid #e2ddd4; border-top: none;
        }
        .email-body h2 { color: #1a1f2e; font-size: 1.3rem; margin-bottom: 1rem; }
        .email-body p  { color: #4b5563; font-size: .9rem; line-height: 1.7; margin-bottom: 1rem; }
        .reason-box {
            background: #fef3c7; border-left: 4px solid #f59e0b;
            padding: 1rem 1.25rem; border-radius: 0 6px 6px 0;
            margin: 1.5rem 0; font-size: .9rem; color: #92400e;
        }
        .reason-box strong { display: block; margin-bottom: 6px; }
        .btn {
            display: inline-block; background: #c9a84c; color: #1a1f2e;
            padding: 12px 28px; border-radius: 5px; text-decoration: none;
            font-weight: 500; font-size: .9rem; margin-top: .5rem;
        }
        .footer { text-align: center; margin-top: 1.5rem; font-size: .78rem; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="email-wrap">
        <div class="email-header">
            <h1>◆ {{ config('app.name') }}</h1>
        </div>
        <div class="email-body">
            <h2>Update on Your Application</h2>
            <p>Dear {{ $organization->user->name }},</p>
            <p>
                Thank you for applying to join the cohort with
                <strong>{{ $organization->name }}</strong>.
                After careful review, we are unable to approve your application at this time.
            </p>
            @if($organization->rejection_reason)
                <div class="reason-box">
                    <strong>Reason:</strong>
                    {{ $organization->rejection_reason }}
                </div>
            @endif
            <p>
                You are welcome to update your application and reapply. Log in to your account
                to review the feedback and submit a new application.
            </p>
            <a href="{{ url('/login') }}" class="btn">Log In to Reapply →</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} · This is an automated message.
        </div>
    </div>
</body>
</html>