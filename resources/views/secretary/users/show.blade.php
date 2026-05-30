@extends('layouts.app')
@section('title', 'View User')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .page-body { max-width:760px; margin:3rem auto; padding:0 2rem; }
    .card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:1.5rem; }
    .card h3 { font-size:1.1rem; color:var(--navy); margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border); }
    .detail-row { display:flex; justify-content:space-between; padding:.6rem 0; border-bottom:1px solid #f3f4f6; font-size:.875rem; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { color:var(--muted); }
    .detail-value { color:var(--navy); font-weight:500; text-align:right; }
    .role-badge {
        display:inline-block; background:var(--green-light); color:var(--green);
        padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600;
        text-transform:uppercase; letter-spacing:.04em;
    }
    .btn-gold {
        background:var(--gold); color:var(--navy); padding:9px 20px;
        border-radius:5px; font-size:.85rem; font-weight:500;
        text-decoration:none; transition:background .2s; display:inline-block;
    }
    .btn-gold:hover { background:var(--gold-dark); }
    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }
    .status-badge {
        display:inline-block; padding:2px 8px; border-radius:20px;
        font-size:.7rem; font-weight:600; text-transform:uppercase;
    }
    .status-submitted { background:#fef3c7; color:#92400e; }
    .status-approved  { background:#d1fae5; color:#065f46; }
    .status-published { background:#dcfce7; color:#166534; }
    .status-rejected  { background:#fee2e2; color:#991b1b; }
    .status-needs_changes { background:#fee2e2; color:#991b1b; }
    .status-draft     { background:#f3f4f6; color:#6b7280; }
    @media(max-width:640px){ .page-body { padding:0 1.25rem; } .detail-row { flex-direction:column; gap:4px; } .detail-value { text-align:left; } }
</style>

<div class="page-header">
    <h1 class="reveal">{{ $user->name }}</h1>
    <p class="reveal">{{ $user->email }}</p>
</div>

<div class="page-body">
    <a href="{{ route('secretary.users.index') }}" class="btn-back reveal">← Back to Users</a>

    <div class="card reveal">
        <h3>User Details</h3>
        <div class="detail-row">
            <span class="detail-label">Full Name</span>
            <span class="detail-value">{{ $user->name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Email</span>
            <span class="detail-value">{{ $user->email }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Role</span>
            <span class="detail-value">
                @foreach($user->roles as $role)
                    <span class="role-badge">{{ str_replace('_', ' ', $role->name) }}</span>
                @endforeach
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Organization</span>
            <span class="detail-value">{{ $user->organization?->name ?? 'Not assigned' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Joined</span>
            <span class="detail-value">{{ $user->created_at->format('M d, Y') }}</span>
        </div>
        <div style="margin-top:1.5rem;">
            <a href="{{ route('secretary.users.edit', $user) }}" class="btn-gold">Edit User</a>
        </div>
    </div>

    <div class="card reveal">
        <h3>Submission History ({{ $user->submissions->count() }})</h3>
        @forelse($user->submissions as $submission)
            <div class="detail-row">
                <span class="detail-label">{{ class_basename($submission->submittable_type) }}</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ $submission->status }}">
                        {{ str_replace('_', ' ', $submission->status) }}
                    </span>
                    <span style="color:var(--muted); font-size:.78rem; margin-left:8px;">
                        {{ $submission->submitted_at?->format('M d, Y') }}
                    </span>
                </span>
            </div>
        @empty
            <p style="color:var(--muted); font-size:.875rem;">No submissions yet.</p>
        @endforelse
    </div>
</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
