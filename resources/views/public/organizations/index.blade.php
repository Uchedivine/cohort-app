@extends('layouts.app')
@section('title', 'Organization Directory')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(28px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .reveal-delay-1{transition-delay:.1s} .reveal-delay-2{transition-delay:.2s} .reveal-delay-3{transition-delay:.3s}

    .page-header {
        background: var(--navy);
        padding: 56px 2rem 48px;
        text-align: center;
    }
    .page-header h1 { font-size: clamp(2rem,4vw,3rem); color: var(--white); margin-bottom: .5rem; }
    .page-header p  { color: #94a3b8; font-size: .95rem; }

    .filter-bar {
        background: var(--white);
        border-bottom: 1px solid var(--border);
        padding: 1rem 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
    }
    .filter-bar input,
    .filter-bar select {
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: 8px 14px;
        font-size: .85rem;
        font-family: 'DM Sans', sans-serif;
        color: var(--text);
        background: var(--white);
        outline: none;
        transition: border-color .2s;
    }
    .filter-bar input:focus,
    .filter-bar select:focus { border-color: var(--gold); }
    .filter-bar button {
        background: var(--navy);
        color: var(--white);
        border: none;
        border-radius: 5px;
        padding: 8px 20px;
        font-size: .85rem;
        cursor: pointer;
        transition: background .2s;
    }
    .filter-bar button:hover { background: var(--gold); color: var(--navy); }

    .org-grid {
        max-width: 1100px;
        margin: 3rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px,1fr));
        gap: 1.5rem;
    }
    .org-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.75rem;
        text-decoration: none;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: .75rem;
        transition: box-shadow .25s, transform .25s, border-color .25s;
    }
    .org-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,.08);
        transform: translateY(-4px);
        border-color: var(--gold);
    }
    .org-logo {
        width: 56px; height: 56px;
        border-radius: 8px;
        object-fit: cover;
        background: var(--cream);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
        border: 1px solid var(--border);
    }
    .org-logo img { width:100%; height:100%; object-fit:cover; border-radius:8px; }
    .org-card h3 { font-size: 1.15rem; color: var(--navy); }
    .org-location { font-size: .78rem; color: var(--muted); }
    .org-card p   { font-size: .85rem; color: #4b5563; line-height: 1.6; }
    .org-focus {
        font-size: .72rem;
        font-weight: 500;
        letter-spacing: .06em;
        text-transform: uppercase;
        background: var(--green-light);
        color: var(--green);
        padding: 3px 10px;
        border-radius: 20px;
        width: fit-content;
    }
    .pagination-wrap {
        max-width: 1100px;
        margin: 0 auto 4rem;
        padding: 0 2rem;
    }

    @media(max-width:640px){
        .org-grid { padding: 0 1.25rem; margin: 2rem auto; }
        .filter-bar { padding: 1rem 1.25rem; }
        .filter-bar input,
        .filter-bar select { width: 100%; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Organization Directory</h1>
    <p class="reveal reveal-delay-1">Meet the cohort members driving impact across Africa</p>
</div>

<form method="GET" action="{{ route('organizations.index') }}">
    <div class="filter-bar">
        <input type="text" name="search" placeholder="Search organizations..." value="{{ request('search') }}">
        <input type="text" name="location" placeholder="Filter by location..." value="{{ request('location') }}">
        <select name="thematic_focus">
            <option value="">All Themes</option>
            <option value="health" {{ request('thematic_focus') == 'health' ? 'selected' : '' }}>Health</option>
            <option value="education" {{ request('thematic_focus') == 'education' ? 'selected' : '' }}>Education</option>
            <option value="environment" {{ request('thematic_focus') == 'environment' ? 'selected' : '' }}>Environment</option>
            <option value="governance" {{ request('thematic_focus') == 'governance' ? 'selected' : '' }}>Governance</option>
            <option value="agriculture" {{ request('thematic_focus') == 'agriculture' ? 'selected' : '' }}>Agriculture</option>
        </select>
        <button type="submit">Filter</button>
        @if(request()->anyFilled(['search','location','thematic_focus','tag']))
            <a href="{{ route('organizations.index') }}" style="font-size:.85rem; color:var(--muted); text-decoration:none;">Clear</a>
        @endif
    </div>
</form>

<div class="org-grid">
    @forelse($organizations as $org)
        <a href="{{ route('organizations.show', $org->slug) }}" class="org-card reveal">
            <div class="org-logo">
                @if($org->logo)
                    <img src="{{ asset('storage/'.$org->logo) }}" alt="{{ $org->name }}">
                @else
                    🏢
                @endif
            </div>
            <div>
                <h3>{{ $org->name }}</h3>
                @if($org->location)
                    <p class="org-location">📍 {{ $org->location }}</p>
                @endif
            </div>
            @if($org->thematic_focus)
                <span class="org-focus">{{ $org->thematic_focus }}</span>
            @endif
            @if($org->short_description)
                <p>{{ Str::limit($org->short_description, 110) }}</p>
            @endif
        </a>
    @empty
        <div style="grid-column:1/-1; text-align:center; color:var(--muted); padding:4rem 0;">
            <p style="font-size:1.1rem;">No organizations found.</p>
        </div>
    @endforelse
</div>

<div class="pagination-wrap">
    {{ $organizations->withQueryString()->links() }}
</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection