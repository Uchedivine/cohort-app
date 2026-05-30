<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Home')</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --navy:       #1a1f2e;
            --cream:      #f5f0e8;
            --gold:       #c9a84c;
            --gold-dark:  #a8863a;
            --white:      #ffffff;
            --text:       #2c2c2c;
            --muted:      #6b7280;
            --border:     #e2ddd4;
            --green:      #3a5a3e;
            --green-light:#edf2ee;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text);
        }
        h1, h2, h3, h4 {
            font-family: 'Cormorant Garamond', serif;
        }

        /* ── Navbar ── */
        .navbar {
            background: var(--navy);
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-brand {
            color: var(--gold);
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.25rem;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        /* Desktop links */
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
        }
        .nav-links a {
            color: #cbd5e1;
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--gold); }

        /* CTA button */
        .nav-cta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }
        .btn-portal {
            background: var(--gold);
            color: var(--navy);
            padding: 7px 16px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.2s;
        }
        .btn-portal:hover { background: var(--gold-dark); }
        .btn-logout {
            background: none;
            border: 1px solid #4b5563;
            color: #9ca3af;
            padding: 6px 13px;
            border-radius: 4px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
        }
        .btn-logout:hover { border-color: #9ca3af; color: var(--white); }

        /* Hamburger button */
        .hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            z-index: 1100;
        }
        .hamburger span {
            display: block;
            width: 24px;
            height: 2px;
            background: var(--gold);
            border-radius: 2px;
            transition: transform 0.3s, opacity 0.3s;
        }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile drawer */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 56px;
            left: 0;
            right: 0;
            background: var(--navy);
            border-top: 1px solid #2d3548;
            padding: 1.5rem 2rem 2rem;
            flex-direction: column;
            gap: 0;
            z-index: 999;
            transform: translateY(-10px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .mobile-menu.open {
            display: flex;
            transform: translateY(0);
            opacity: 1;
        }
        .mobile-menu a, .mobile-menu button {
            color: #cbd5e1;
            font-size: 1rem;
            text-decoration: none;
            padding: 14px 0;
            border-bottom: 1px solid #2d3548;
            background: none;
            border-left: none;
            border-right: none;
            border-top: none;
            text-align: left;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            width: 100%;
            transition: color 0.2s;
        }
        .mobile-menu a:hover, .mobile-menu button:hover { color: var(--gold); }
        .mobile-menu a:last-child, .mobile-menu button:last-child { border-bottom: none; }
        .mobile-menu .mob-cta {
            margin-top: 1.25rem;
            background: var(--gold);
            color: var(--navy);
            padding: 12px 0;
            border-radius: 4px;
            font-weight: 500;
            text-align: center;
            border: none;
            font-size: 0.9rem;
        }
        .mobile-menu .mob-cta:hover { background: var(--gold-dark); color: var(--navy); }

        /* Flash */
        .flash-success {
            background: #d1fae5;
            border-left: 4px solid #059669;
            color: #065f46;
            padding: 12px 24px;
            font-size: 0.875rem;
        }
        .flash-error {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            color: #991b1b;
            padding: 12px 24px;
            font-size: 0.875rem;
        }

        /* Footer */
        .footer {
            background: var(--navy);
            color: #6b7280;
            text-align: center;
            padding: 2rem;
            font-size: 0.8rem;
            margin-top: 6rem;
        }
        .footer-brand {
            color: var(--gold);
            font-family: 'Cormorant Garamond', serif;
            font-size: 1rem;
            margin-bottom: 6px;
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .nav-cta   { display: none; }
            .hamburger { display: flex; }
            .navbar    { padding: 0 1.25rem; }
        }
    </style>
</head>
<body>

<!-- ═══════════ NAVBAR ═══════════ -->
<nav class="navbar">
    <a href="{{ route('home') }}" class="navbar-brand">◆ {{ config('app.name') }}</a>

    <!-- Desktop links -->
    <ul class="nav-links">
        <li><a href="{{ route('organizations.index') }}">Directory</a></li>
        <li><a href="{{ route('stories.index') }}">Stories</a></li>
        <li><a href="{{ route('resources.index') }}">Resources</a></li>
        <li><a href="{{ route('events.index') }}">Events</a></li>
        <li><a href="{{ route('search.index') }}" title="Search">🔍</a></li>
    </ul>

    <!-- Desktop CTA -->
    <div class="nav-cta">
        @auth
            @if(auth()->user()->isSecretary())
                <a href="{{ route('secretary.dashboard') }}" class="btn-portal">Secretary Panel</a>
            @elseif(auth()->user()->isOrgEditor())
                <a href="{{ route('org-editor.dashboard') }}" class="btn-portal">My Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-portal">Member Portal →</a>
        @endauth
    </div>

    <!-- Hamburger -->
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- ═══════════ MOBILE MENU ═══════════ -->
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('organizations.index') }}">Directory</a>
    <a href="{{ route('stories.index') }}">Stories</a>
    <a href="{{ route('resources.index') }}">Resources</a>
    <a href="{{ route('events.index') }}">Events</a>
    <a href="{{ route('search.index') }}">🔍 Search</a>
    @auth
        @if(auth()->user()->isSecretary())
            <a href="{{ route('secretary.dashboard') }}">Secretary Panel</a>
        @elseif(auth()->user()->isOrgEditor())
            <a href="{{ route('org-editor.dashboard') }}">My Dashboard</a>
        @endif
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <a href="{{ route('login') }}" class="mob-cta">Member Portal →</a>
    @endauth
</div>

<!-- ═══════════ FLASH MESSAGES ═══════════ -->
@if(session('success'))
    <div class="flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash-error">{{ session('error') }}</div>
@endif

<!-- ═══════════ PAGE CONTENT ═══════════ -->
@yield('content')

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="footer">
    <p class="footer-brand">◆ {{ config('app.name') }}</p>
    <p>&copy; {{ date('Y') }} All rights reserved.</p>
</footer>

<!-- ═══════════ HAMBURGER JS ═══════════ -->
<script>
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobileMenu');

    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
    });

    // Close menu when a link is tapped
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('open');
            mobileMenu.classList.remove('open');
        });
    });
</script>

</body>
</html>