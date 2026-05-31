<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f0e8; margin:0; padding:2rem; }
        .wrap { max-width:560px; margin:0 auto; }
        .header { background:#1a1f2e; padding:2rem; border-radius:10px 10px 0 0; text-align:center; }
        .header h1 { color:#c9a84c; font-size:1.4rem; margin:0; }
        .body {
            background:#ffffff; padding:2rem;
            border-radius:0 0 10px 10px;
            border:1px solid #e2ddd4; border-top:none;
        }
        .body h2 { color:#1a1f2e; font-size:1.2rem; margin-bottom:1rem; }
        .body p  { color:#4b5563; font-size:.9rem; line-height:1.7; margin-bottom:1rem; }
        .detail-box {
            background:#f5f0e8; border:1px solid #e2ddd4;
            border-radius:8px; padding:1.25rem; margin:1.5rem 0;
        }
        .detail-row { display:flex; justify-content:space-between; padding:.4rem 0; font-size:.875rem; border-bottom:1px solid #e2ddd4; }
        .detail-row:last-child { border-bottom:none; }
        .detail-label { color:#6b7280; }
        .detail-value { color:#1a1f2e; font-weight:500; }
        .btn {
            display:inline-block; background:#c9a84c; color:#1a1f2e;
            padding:12px 28px; border-radius:5px; text-decoration:none;
            font-weight:500; font-size:.9rem; margin-top:.5rem;
        }
        .footer { text-align:center; margin-top:1.5rem; font-size:.78rem; color:#9ca3af; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <h1>◆ {{ config('app.name') }}</h1>
        </div>
        <div class="body">
            <h2>📬 New Organisation Application</h2>
            <p>Hi {{ $secretary->name }},</p>
            <p>
                A new organisation has applied to join the cohort and is awaiting your review.
            </p>
            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">Organisation</span>
                    <span class="detail-value">{{ $organization->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location</span>
                    <span class="detail-value">{{ $organization->location ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Thematic Focus</span>
                    <span class="detail-value">{{ ucfirst($organization->thematic_focus ?? '—') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Contact</span>
                    <span class="detail-value">{{ $organization->user?->name }} · {{ $organization->user?->email }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Applied</span>
                    <span class="detail-value">{{ $organization->applied_at?->format('M d, Y · H:i') }}</span>
                </div>
            </div>
            <p>Log in to your secretary panel to review and make a decision.</p>
            <a href="{{ url('/secretary/applications') }}" class="btn">Review Application →</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} · This is an automated message.
        </div>
    </div>
</body>
</html>