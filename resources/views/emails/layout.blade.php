<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cohort Web App')</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f1e8;
            color: #0f172a;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background-color: #0f172a;
            padding: 32px 24px;
            text-align: center;
        }
        .email-logo {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 28px;
            font-weight: 700;
            color: #d4af37;
            margin: 0;
        }
        .email-body {
            padding: 40px 24px;
        }
        .email-greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
        }
        .email-content {
            font-size: 15px;
            color: #334155;
            margin-bottom: 24px;
        }
        .email-button {
            display: inline-block;
            padding: 12px 32px;
            background-color: #d4af37;
            color: #0f172a;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 16px 0;
        }
        .email-button:hover {
            background-color: #c19d2f;
        }
        .email-info-box {
            background-color: #f5f1e8;
            border-left: 4px solid #059669;
            padding: 16px;
            margin: 24px 0;
        }
        .email-warning-box {
            background-color: #fef3c7;
            border-left: 4px solid: #f59e0b;
            padding: 16px;
            margin: 24px 0;
        }
        .email-footer {
            background-color: #f8fafc;
            padding: 24px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .email-footer a {
            color: #059669;
            text-decoration: none;
        }
        hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 24px 0;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1 class="email-logo">COHORT</h1>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>
                This email was sent by Cohort Web App<br>
                <a href="{{ route('home') }}">Visit our website</a>
            </p>
            <p style="margin-top: 16px; font-size: 12px;">
                © {{ date('Y') }} Cohort. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
