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
        .highlight {
            background: #edf2ee; border-left: 4px solid #3a5a3e;
            padding: 1rem 1.25rem; border-radius: 0 6px 6px 0;
            margin: 1.5rem 0; font-size: .9rem; color: #2c2c2c;
        }
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
            <h2>🎉 Your Application Has Been Approved!</h2>
            <p>Dear {{ $organization->user->name }},</p>
            <p>
                We are delighted to inform you that <strong>{{ $organization->name }}</strong>
                has been approved to join the cohort. Your organisation is now live on the platform.
            </p>
            <div class="highlight">
                <strong>{{ $organization->name }}</strong><br>
                {{ $organization->location }} · {{ $organization->thematic_focus }}
            </div>
            <p>You can now log in to your member portal to:</p>
            <ul style="color:#4b5563; font-size:.9rem; line-height:2; padding-left:1.25rem;">
                <li>Complete your organisation profile</li>
                <li>Submit stories and resources</li>
                <li>Manage your presence on the platform</li>
            </ul>
            <a href="{{ url('/login') }}" class="btn">Access Your Dashboard →</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} · This is an automated message.
        </div>
    </div>
</body>
</html>