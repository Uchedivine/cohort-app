<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Login')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy:      #1a1f2e;
            --cream:     #f5f0e8;
            --gold:      #c9a84c;
            --gold-dark: #a8863a;
            --white:     #ffffff;
            --text:      #2c2c2c;
            --muted:     #6b7280;
            --border:    #e2ddd4;
            --green:     #3a5a3e;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h1, h2, h3 { font-family: 'Cormorant Garamond', serif; }

        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }
        .auth-box {
            background: var(--white);
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-brand a {
            color: var(--gold);
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-brand p {
            color: var(--muted);
            font-size: .82rem;
            margin-top: 4px;
        }
        .auth-footer {
            text-align: center;
            padding: 1.5rem;
            font-size: .78rem;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-box">
            <div class="auth-brand">
                <a href="{{ route('home') }}">◆ {{ config('app.name') }}</a>
                <p>Member Portal</p>
            </div>
            {{ $slot }}
        </div>
    </div>
    <footer class="auth-footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} · <a href="{{ route('home') }}" style="color:#c9a84c; text-decoration:none;">Back to site</a>
    </footer>
</body>
</html>