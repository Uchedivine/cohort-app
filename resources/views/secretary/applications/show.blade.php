@extends('layouts.app')
@section('title', 'Review Application')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .page-body { max-width:860px; margin:3rem auto; padding:0 2rem; }

    .card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:1.5rem; }
    .card h3 { font-size:1.1rem; color:var(--navy); margin-bottom:1.25rem; padding-bottom:.75rem; border-bottom:1px solid var(--border); }

    .detail-row { display:flex; justify-content:space-between; padding:.65rem 0; border-bottom:1px solid #f3f4f6; font-size:.875rem; gap:1rem; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { color:var(--muted); flex-shrink:0; }
    .detail-value { color:var(--navy); font-weight:500; text-align:right; }

    .why-join-box {
        background:var(--cream); border:1px solid var(--border);
        border-radius:8px; padding:1.25rem; font-size:.875rem;
        color:var(--text); line-height:1.75;
    }

    .status-badge {
        display:inline-block; padding:4px 12px; border-radius:20px;
        font-size:.75rem; font-weight:600; text-transform:uppercase;
    }
    .status-pending   { background:#fef3c7; color:#92400e; }
    .status-approved  { background:#d1fae5; color:#065f46; }
    .status-rejected  { background:#fee2e2; color:#991b1b; }
    .status-needs_changes { background:#fee2e2; color:#991b1b; }
    .status-published { background:#dcfce7; color:#166534; }

    .action-card {
        background:var(--white); border:1px solid var(--border);
        border-radius:10px; padding:2rem; margin-bottom:1rem;
    }
    .action-card h3 { font-size:1rem; color:var(--navy); margin-bottom:1rem; }
    .action-card p  { font-size:.85rem; color:var(--muted); margin-bottom:1rem; line-height:1.6; }

    .btn-approve {
        background:var(--green); color:var(--white); border:none;
        padding:11px 28px; border-radius:5px; font-size:.9rem;
        font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
        transition:background .2s; width:100%;
    }
    .btn-approve:hover { background:#2d4a31; }

    .btn-reject {
        background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;
        padding:11px 28px; border-radius:5px; font-size:.9rem;
        font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
        transition:background .2s; width:100%;
    }
    .btn-reject:hover { background:#fca5a5; }

    .btn-changes {
        background:#fef3c7; color:#92400e; border:1px solid #fcd34d;
        padding:11px 28px; border-radius:5px; font-size:.9rem;
        font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
        transition:background .2s; width:100%;
    }
    .btn-changes:hover { background:#fcd34d; }

    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:var(--navy); margin-bottom:6px; }
    .form-group textarea {
        width:100%; border:1px solid var(--border); border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:var(--text); outline:none; transition:border-color .2s;
        resize:vertical; min-height:100px;
    }
    .form-group textarea:focus { border-color:var(--gold); }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }

    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }

    .already-reviewed {
        background:var(--cream); border:1px solid var(--border);
        border-radius:10px; padding:2rem; text-align:center;
        font-size:.9rem; color:var(--muted);
    }
    .already-reviewed strong { color:var(--navy); display:block; margin-bottom:.5rem; font-size:1rem; }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .detail-row { flex-direction:column; gap:4px; }
        .detail-value { text-align:left; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Review Application</h1>
    <p class="reveal">{{ $organization->name }}</p>
</div>

<div class="page-body">
    <a href="{{ route('secretary.applications.index') }}" class="btn-back reveal">← Back to Applications</a>

    {{-- Organisation Details --}}
    <div class="card reveal">
        <h3>Organisation Details</h3>
        <div class="detail-row">
            <span class="detail-label">Organisation Name</span>
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
            <span class="detail-label">Website</span>
            <span class="detail-value">
                @if($organization->website)
                    <a href="{{ $organization->website }}" target="_blank"
                       style="color:var(--gold); text-decoration:none;">
                        {{ $organization->website }}
                    </a>
                @else
                    —
                @endif
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="detail-value">
                <span class="status-badge status-{{ $organization->status }}">
                    {{ str_replace('_', ' ', $organization->status) }}
                </span>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Applied</span>
            <span class="detail-value">
                {{ $organization->applied_at?->format('M d, Y · H:i') ?? '—' }}
            </span>
        </div>
    </div>

    {{-- Short Description --}}
    <div class="card reveal">
        <h3>About the Organisation</h3>
        <p style="font-size:.9rem; color:var(--text); line-height:1.75;">
            {{ $organization->short_description ?? 'No description provided.' }}
        </p>
    </div>

    {{-- Why Join --}}
    @if($organization->highlights)
        <div class="card reveal">
            <h3>Why They Want to Join</h3>
            <div class="why-join-box">
                {{ $organization->highlights }}
            </div>
        </div>
    @endif

    {{-- Contact Person --}}
    @if($organization->user)
        <div class="card reveal">
            <h3>Contact Person</h3>
            <div class="detail-row">
                <span class="detail-label">Name</span>
                <span class="detail-value">{{ $organization->user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $organization->user->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Created</span>
                <span class="detail-value">{{ $organization->user->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    @endif

    {{-- Action Panel --}}
    @if($organization->status === 'pending' || $organization->status === 'needs_changes')

        {{-- Approve --}}
        <div class="action-card reveal">
            <h3>✅ Approve Application</h3>
            <p>
                Approving this application will make the organisation live on the platform
                and give the contact person full access to their member dashboard.
                They will receive an email notification.
            </p>
            <button type="button" class="btn-approve" onclick="openModal('approveModal')">
                Approve Organisation
            </button>

            <form method="POST" action="{{ route('secretary.applications.approve', $organization) }}" id="approveForm">
                @csrf
            </form>
        </div>

        {{-- Request Changes --}}
        <div class="action-card reveal">
            <h3>🔄 Request Changes</h3>
            <p>Ask the applicant to update their information before you make a final decision.</p>
            <form method="POST" action="{{ route('secretary.applications.request-changes', $organization) }}">
                @csrf
                <div class="form-group">
                    <label>What needs to be updated?</label>
                    <textarea name="rejection_reason" required
                        placeholder="Explain what information needs to be updated or clarified..."></textarea>
                    @error('rejection_reason')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-changes">Request Changes</button>
            </form>
        </div>

        {{-- Reject --}}
        <div class="action-card reveal">
            <h3>❌ Reject Application</h3>
            <p>
                Rejecting this application will notify the applicant by email.
                They will be able to update their details and reapply.
            </p>
            <form method="POST" action="{{ route('secretary.applications.reject', $organization) }}">
                @csrf
                <div class="form-group">
                    <label>Reason for Rejection *</label>
                    <textarea name="rejection_reason" required
                        placeholder="Provide a clear reason so the applicant can understand and potentially reapply..."></textarea>
                    @error('rejection_reason')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <button type="button" class="btn-reject" onclick="openModal('rejectModal')">
                    Reject Application
                </button>
            </form>
        </div>

    @else
        <div class="already-reviewed reveal">
            <strong>This application has already been reviewed</strong>
            <span class="status-badge status-{{ $organization->status }}">
                {{ str_replace('_', ' ', $organization->status) }}
            </span>
        </div>
    @endif

</div>

{{-- Approve Modal --}}
<div id="approveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; padding:1.25rem;">
    <div style="background:var(--white); border-radius:12px; padding:2rem; max-width:440px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h3 style="font-size:1.3rem; color:var(--navy); margin-bottom:.75rem;">Approve Organisation</h3>
        <p style="font-size:.875rem; color:var(--muted); line-height:1.7; margin-bottom:1.5rem;">
            Are you sure you want to approve <strong style="color:var(--navy);">{{ $organization->name }}</strong>?
            They will be made live on the platform and notified by email.
        </p>
        <div style="display:flex; gap:1rem;">
            <button onclick="document.getElementById('approveForm').submit()"
                style="flex:1; background:var(--green); color:var(--white); border:none; padding:11px; border-radius:6px; font-size:.9rem; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;">
                Yes, Approve
            </button>
            <button onclick="closeModal('approveModal')"
                style="flex:1; background:#f3f4f6; color:var(--text); border:1px solid var(--border); padding:11px; border-radius:6px; font-size:.9rem; cursor:pointer; font-family:'DM Sans',sans-serif;">
                Cancel
            </button>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; padding:1.25rem;">
    <div style="background:var(--white); border-radius:12px; padding:2rem; max-width:480px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h3 style="font-size:1.3rem; color:var(--navy); margin-bottom:.75rem;">Reject Application</h3>
        <p style="font-size:.875rem; color:var(--muted); line-height:1.7; margin-bottom:1.25rem;">
            Rejecting <strong style="color:var(--navy);">{{ $organization->name }}</strong> will notify them by email.
            They will be able to update their details and reapply.
        </p>
        <form method="POST" action="{{ route('secretary.applications.reject', $organization) }}">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.85rem; font-weight:500; color:var(--navy); margin-bottom:6px;">
                    Reason for Rejection *
                </label>
                <textarea name="rejection_reason" required rows="4"
                    placeholder="Provide a clear reason so the applicant can understand and potentially reapply..."
                    style="width:100%; border:1px solid var(--border); border-radius:6px; padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif; color:var(--text); outline:none; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; gap:1rem;">
                <button type="submit"
                    style="flex:1; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:11px; border-radius:6px; font-size:.9rem; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;">
                    Yes, Reject
                </button>
                <button type="button" onclick="closeModal('rejectModal')"
                    style="flex:1; background:#f3f4f6; color:var(--text); border:1px solid var(--border); padding:11px; border-radius:6px; font-size:.9rem; cursor:pointer; font-family:'DM Sans',sans-serif;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on backdrop click
    document.querySelectorAll('[id$="Modal"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // Existing reveal observer
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection