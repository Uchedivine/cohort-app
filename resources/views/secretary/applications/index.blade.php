@extends('layouts.app')
@section('title', 'Organisation Applications')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .page-body { max-width:1100px; margin:3rem auto; padding:0 2rem; }

    .section-title {
        font-size:1.2rem; color:var(--navy);
        margin-bottom:1.25rem; padding-bottom:.75rem;
        border-bottom:2px solid var(--border);
        display:flex; justify-content:space-between; align-items:center;
    }
    .count-badge {
        background:var(--gold); color:var(--navy);
        font-size:.75rem; font-weight:600;
        padding:3px 10px; border-radius:20px;
    }

    .app-card {
        background:var(--white); border:1px solid var(--border);
        border-radius:10px; padding:1.5rem;
        display:flex; justify-content:space-between;
        align-items:flex-start; gap:1rem; margin-bottom:1rem;
        text-decoration:none; color:var(--text);
        transition:box-shadow .2s, border-color .2s;
    }
    .app-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); border-color:var(--gold); }
    .app-card-left { flex:1; }
    .app-card-left h3 { font-size:1.1rem; color:var(--navy); margin-bottom:4px; }
    .app-card-left p { font-size:.85rem; color:var(--muted); margin-bottom:6px; }
    .app-meta { display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; margin-top:8px; }
    .meta-pill {
        font-size:.72rem; font-weight:500; letter-spacing:.04em;
        text-transform:uppercase; padding:3px 10px; border-radius:20px;
    }
    .pill-theme { background:var(--green-light); color:var(--green); }
    .pill-location { background:#f3f4f6; color:#6b7280; }
    .pill-date { background:#eff6ff; color:#1e40af; }

    .app-card-right {
        display:flex; flex-direction:column;
        align-items:flex-end; gap:.5rem; flex-shrink:0;
    }
    .btn-review {
        background:var(--navy); color:var(--white);
        padding:8px 18px; border-radius:5px; font-size:.8rem;
        font-weight:500; text-decoration:none; transition:background .2s;
        white-space:nowrap;
    }
    .btn-review:hover { background:var(--gold); color:var(--navy); }

    .status-badge {
        display:inline-block; padding:3px 10px; border-radius:20px;
        font-size:.7rem; font-weight:600; text-transform:uppercase;
    }
    .status-approved  { background:#d1fae5; color:#065f46; }
    .status-rejected  { background:#fee2e2; color:#991b1b; }
    .status-pending   { background:#fef3c7; color:#92400e; }
    .status-needs_changes { background:#fee2e2; color:#991b1b; }

    .empty-state {
        text-align:center; padding:3rem 2rem;
        background:var(--white); border:1px solid var(--border);
        border-radius:10px; color:var(--muted); font-size:.9rem;
    }

    .recent-section { margin-top:3rem; }
    .recent-item {
        display:flex; justify-content:space-between; align-items:center;
        padding:.875rem 0; border-bottom:1px solid var(--border);
        font-size:.875rem; flex-wrap:wrap; gap:.5rem;
    }
    .recent-item:last-child { border-bottom:none; }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .app-card { flex-direction:column; }
        .app-card-right { flex-direction:row; align-items:center; width:100%; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Organisation Applications</h1>
    <p class="reveal">Review and approve organisations applying to join the cohort</p>
</div>

<div class="page-body">

    {{-- Pending Applications --}}
    <div class="reveal">
        <div class="section-title">
            <span>Pending Review</span>
            <span class="count-badge">{{ $pending->total() }}</span>
        </div>

        @forelse($pending as $org)
            <div class="app-card reveal">
                <div class="app-card-left">
                    <h3>{{ $org->name }}</h3>
                    <p>{{ Str::limit($org->short_description, 120) }}</p>
                    <div class="app-meta">
                        @if($org->thematic_focus)
                            <span class="meta-pill pill-theme">{{ $org->thematic_focus }}</span>
                        @endif
                        @if($org->location)
                            <span class="meta-pill pill-location">📍 {{ $org->location }}</span>
                        @endif
                        @if($org->applied_at)
                            <span class="meta-pill pill-date">Applied {{ $org->applied_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    @if($org->user)
                        <p style="font-size:.78rem; color:var(--muted); margin-top:6px;">
                            Contact: {{ $org->user->name }} · {{ $org->user->email }}
                        </p>
                    @endif
                </div>
                <div class="app-card-right">
                    <a href="{{ route('secretary.applications.show', $org) }}" class="btn-review">Review →</a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <p style="font-size:2rem; margin-bottom:.75rem;">✅</p>
                <p>No pending applications. All caught up!</p>
            </div>
        @endforelse

        @if($pending->hasPages())
            <div style="margin-top:1.5rem;">{{ $pending->links() }}</div>
        @endif
    </div>

    {{-- Recently Reviewed --}}
    @if($recentlyReviewed->count() > 0)
        <div class="recent-section reveal">
            <div class="section-title">
                <span>Recently Reviewed</span>
            </div>
            @foreach($recentlyReviewed as $org)
                <div class="recent-item">
                    <div>
                        <strong style="color:var(--navy);">{{ $org->name }}</strong>
                        <span style="color:var(--muted); font-size:.8rem; margin-left:.5rem;">{{ $org->location }}</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:.75rem;">
                        <span class="status-badge status-{{ $org->status }}">{{ $org->status }}</span>
                        <a href="{{ route('secretary.applications.show', $org) }}"
                           style="font-size:.8rem; color:var(--muted); text-decoration:none;">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection