@extends('layouts.app')
@section('title', 'My Resources')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }

    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }

    .page-body { max-width:1000px; margin:3rem auto; padding:0 2rem; }

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

    .resource-table { width:100%; border-collapse:collapse; }
    .resource-table th {
        text-align:left; font-size:.75rem; text-transform:uppercase;
        letter-spacing:.06em; color:var(--muted); padding:10px 14px;
        border-bottom:2px solid var(--border); background:var(--white);
    }
    .resource-table td {
        padding:14px; border-bottom:1px solid var(--border);
        font-size:.875rem; background:var(--white); vertical-align:middle;
    }
    .resource-table tr:hover td { background:#faf9f7; }

    .status-badge {
        display:inline-block; padding:3px 10px; border-radius:20px;
        font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em;
    }
    .status-draft     { background:#f3f4f6; color:#6b7280; }
    .status-submitted { background:#fef3c7; color:#92400e; }
    .status-needs_changes { background:#fee2e2; color:#991b1b; }
    .status-approved  { background:#d1fae5; color:#065f46; }
    .status-published { background:#dcfce7; color:#166534; }
    .status-rejected  { background:#fee2e2; color:#991b1b; }

    .type-badge {
        display:inline-block; padding:2px 8px; border-radius:4px;
        font-size:.7rem; background:var(--green-light); color:var(--green);
        text-transform:uppercase; letter-spacing:.04em;
    }
    .empty-state {
        text-align:center; padding:4rem 2rem;
        background:var(--white); border:1px solid var(--border); border-radius:10px;
    }
    .empty-state p { color:var(--muted); margin-bottom:1.5rem; }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .resource-table thead { display:none; }
        .resource-table td {
            display:block; padding:8px 14px;
            border-bottom:none;
        }
        .resource-table tr {
            display:block; border:1px solid var(--border);
            border-radius:8px; margin-bottom:1rem;
            background:var(--white);
        }
    }
</style>

<div class="page-header">
    <h1 class="reveal">My Resources</h1>
    <p class="reveal">Manage your submitted resources and track their status</p>
</div>

<div class="page-body">
    <div class="top-bar reveal">
        <h2 style="font-size:1.1rem; color:var(--navy);">{{ $resources->total() }} resource(s)</h2>
        <a href="{{ route('org-editor.resources.create') }}" class="btn-gold">+ Submit New Resource</a>
    </div>

    @if($resources->isEmpty())
        <div class="empty-state reveal">
            <p style="font-size:2rem; margin-bottom:1rem;">📎</p>
            <p>You haven't submitted any resources yet.</p>
            <a href="{{ route('org-editor.resources.create') }}" class="btn-gold">Submit Your First Resource</a>
        </div>
    @else
        <div style="overflow-x:auto;" class="reveal">
            <table class="resource-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Theme</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                        <tr>
                            <td><strong>{{ $resource->title }}</strong></td>
                            <td><span class="type-badge">{{ $resource->resource_type }}</span></td>
                            <td>{{ $resource->theme ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ $resource->status }}">
                                    {{ str_replace('_', ' ', $resource->status) }}
                                </span>
                            </td>
                            <td style="color:var(--muted); font-size:.8rem;">
                                {{ $resource->created_at->format('M d, Y') }}
                            </td>
                            <td style="font-size:.8rem; color:var(--muted); max-width:200px;">
                                @if($resource->submissions->last()?->reviewer_notes)
                                    {{ Str::limit($resource->submissions->last()->reviewer_notes, 60) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                @if(in_array($resource->status, ['draft', 'needs_changes']))
                                    <a href="{{ route('org-editor.resources.edit', $resource) }}" 
                                       style="display:inline-block; padding:5px 12px; background:var(--navy); color:white; border-radius:4px; font-size:.75rem; text-decoration:none; margin-right:4px;">
                                        ✏️ Edit
                                    </a>
                                @endif
                                @if($resource->status === 'rejected')
                                    @php
                                        $rejectedSubmission = $resource->submissions
                                            ->where('status', 'rejected')
                                            ->sortByDesc('created_at')
                                            ->first();
                                    @endphp
                                    @if($rejectedSubmission && $rejectedSubmission->allow_resubmission)
                                        <form action="{{ route('org-editor.resources.resubmit', $resource) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" 
                                                    style="padding:5px 12px; background:var(--green); color:white; border:none; border-radius:4px; font-size:.75rem; cursor:pointer;">
                                                🔄 Resubmit
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size:.75rem; color:var(--muted);">
                                            Contact secretary
                                        </span>
                                    @endif
                                @elseif(!in_array($resource->status, ['draft', 'needs_changes']))
                                    <span style="font-size:.75rem; color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:1.5rem;">
            {{ $resources->links() }}
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