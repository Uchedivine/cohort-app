@extends('layouts.app')
@section('title', 'Manage Events')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }

    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }

    .page-body { max-width:1100px; margin:3rem auto; padding:0 2rem; }

    .top-bar {
        display:flex; justify-content:space-between; align-items:center;
        margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;
    }
    .btn-gold {
        background:var(--gold); color:var(--navy);
        padding:9px 20px; border-radius:5px; font-size:.85rem;
        font-weight:500; text-decoration:none; transition:background .2s;
    }
    .btn-gold:hover { background:var(--gold-dark); }
    .btn-danger {
        background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;
        padding:6px 14px; border-radius:5px; font-size:.8rem;
        cursor:pointer; transition:background .2s; font-family:'DM Sans',sans-serif;
    }
    .btn-danger:hover { background:#fca5a5; }
    .btn-edit {
        background:var(--green-light); color:var(--green);
        border:1px solid #c6d9c8; padding:6px 14px;
        border-radius:5px; font-size:.8rem; text-decoration:none;
        transition:background .2s;
    }
    .btn-edit:hover { background:#c6d9c8; }

    .events-table { width:100%; border-collapse:collapse; }
    .events-table th {
        text-align:left; font-size:.75rem; text-transform:uppercase;
        letter-spacing:.06em; color:var(--muted); padding:10px 14px;
        border-bottom:2px solid var(--border); background:var(--white);
    }
    .events-table td {
        padding:14px; border-bottom:1px solid var(--border);
        font-size:.875rem; background:var(--white); vertical-align:middle;
    }
    .events-table tr:hover td { background:#faf9f7; }

    .status-badge {
        display:inline-block; padding:3px 10px; border-radius:20px;
        font-size:.7rem; font-weight:600; text-transform:uppercase;
    }
    .status-published { background:#dcfce7; color:#166534; }
    .status-draft     { background:#f3f4f6; color:#6b7280; }

    .empty-state {
        text-align:center; padding:4rem 2rem;
        background:var(--white); border:1px solid var(--border); border-radius:10px;
    }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .events-table thead { display:none; }
        .events-table td { display:block; padding:8px 14px; border-bottom:none; }
        .events-table tr {
            display:block; border:1px solid var(--border);
            border-radius:8px; margin-bottom:1rem; background:var(--white);
        }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Manage Events</h1>
    <p class="reveal">Create and manage cohort events</p>
</div>

<div class="page-body">
    <div class="top-bar reveal">
        <h2 style="font-size:1.1rem; color:var(--navy);">{{ $events->total() }} event(s)</h2>
        <a href="{{ route('secretary.events.create') }}" class="btn-gold">+ Create Event</a>
    </div>

    @if($events->isEmpty())
        <div class="empty-state reveal">
            <p style="font-size:2rem; margin-bottom:1rem;">📅</p>
            <p style="color:var(--muted); margin-bottom:1.5rem;">No events created yet.</p>
            <a href="{{ route('secretary.events.create') }}" class="btn-gold">Create First Event</a>
        </div>
    @else
        <div style="overflow-x:auto;" class="reveal">
            <table class="events-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td><strong>{{ $event->title }}</strong></td>
                            <td style="color:var(--muted); font-size:.85rem;">
                                {{ $event->start_date->format('M d, Y · H:i') }}
                            </td>
                            <td>{{ $event->location ?? 'Virtual' }}</td>
                            <td>
                                <span class="status-badge status-{{ $event->status }}">
                                    {{ $event->status }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                                    <a href="{{ route('secretary.events.edit', $event) }}" class="btn-edit">Edit</a>
                                    <form method="POST" action="{{ route('secretary.events.destroy', $event) }}"
                                          onsubmit="return confirm('Delete this event?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:1.5rem;">
            {{ $events->links() }}
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