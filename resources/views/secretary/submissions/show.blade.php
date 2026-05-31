@extends('layouts.app')
@section('title', 'Review Submission')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 1100px;
        margin: 0 auto;
    }
    .breadcrumb {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-bottom: 1rem;
    }
    .breadcrumb a {
        color: var(--gold);
        text-decoration: none;
    }
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    .page-header h1 {
        font-size: clamp(1.6rem, 4vw, 2.2rem);
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--white);
    }
    .submission-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.9rem;
        color: #cbd5e1;
    }
    .submission-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .review-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .review-inner {
        max-width: 1100px;
        margin: 0 auto;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2rem;
    }
    .status-submitted {
        background: #dbeafe;
        color: #1e40af;
    }
    .status-needs-changes {
        background: #fef3c7;
        color: #92400e;
    }
    .status-approved {
        background: #d1fae5;
        color: #065f46;
    }

    .content-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2.5rem;
        margin-bottom: 2rem;
    }
    .content-card h2 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
    }
    .content-field {
        margin-bottom: 1.5rem;
    }
    .content-label {
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
        margin-bottom: 0.5rem;
    }
    .content-value {
        font-size: 1rem;
        color: var(--text);
        line-height: 1.7;
    }
    .content-image {
        max-width: 100%;
        border-radius: 8px;
        margin-top: 0.5rem;
    }

    .diff-view {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    .diff-column {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
    }
    .diff-column h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--border);
    }
    .diff-column.current {
        border-left: 4px solid #6b7280;
    }
    .diff-column.proposed {
        border-left: 4px solid var(--gold);
    }

    .action-panel {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
        position: sticky;
        top: 2rem;
    }
    .action-panel h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
    }
    .action-form {
        margin-bottom: 1.5rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        color: var(--text);
        background: var(--white);
        resize: vertical;
        min-height: 100px;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }

    .btn {
        width: 100%;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 0.75rem;
    }
    .btn-approve {
        background: var(--green);
        color: var(--white);
    }
    .btn-approve:hover {
        background: #047857;
    }
    .btn-changes {
        background: #f59e0b;
        color: var(--white);
    }
    .btn-changes:hover {
        background: #d97706;
    }
    .btn-reject {
        background: #dc2626;
        color: var(--white);
    }
    .btn-reject:hover {
        background: #b91c1c;
    }
    .btn-back {
        background: var(--white);
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-back:hover {
        border-color: var(--navy);
    }

    .audit-log {
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 2rem;
    }
    .audit-log h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1rem;
    }
    .audit-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
        color: #4b5563;
    }
    .audit-item:last-child {
        border-bottom: none;
    }

    @media (max-width: 968px) {
        .diff-view {
            grid-template-columns: 1fr;
        }
        .action-panel {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .review-section { padding: 2rem 1.25rem; }
        .content-card { padding: 1.5rem; }
        .diff-column { padding: 1.5rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <div class="breadcrumb">
            <a href="{{ route('secretary.dashboard') }}">Dashboard</a> / 
            <a href="{{ route('secretary.submissions.index') }}">Submissions</a> / 
            Review
        </div>
        <h1>{{ $submission->submittable->title ?? 'Organization Profile Update' }}</h1>
        <div class="submission-meta">
            <span class="submission-meta-item">
                📁 {{ class_basename($submission->submittable_type) }}
            </span>
            <span class="submission-meta-item">
                🏢 {{ $submission->submittable->organization->name ?? 'N/A' }}
            </span>
            <span class="submission-meta-item">
                👤 {{ $submission->submittedBy->name }}
            </span>
            <span class="submission-meta-item">
                📅 {{ $submission->created_at->format('M d, Y g:i A') }}
            </span>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     REVIEW CONTENT
════════════════════════════════════════════ -->
<section class="review-section">
    <div class="review-inner">
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
            <div>
                <span class="status-badge status-{{ $submission->status }}">
                    {{ str_replace('_', ' ', $submission->status) }}
                </span>

                <!-- Content Preview -->
                <div class="content-card">
                    <h2>Submitted Content</h2>

                    @if($submission->submittable->title ?? null)
                        <div class="content-field">
                            <div class="content-label">Title</div>
                            <div class="content-value">{{ $submission->submittable->title }}</div>
                        </div>
                    @endif

                    @if($submission->submittable->summary ?? $submission->submittable->short_description ?? null)
                        <div class="content-field">
                            <div class="content-label">Summary</div>
                            <div class="content-value">{{ $submission->submittable->summary ?? $submission->submittable->short_description }}</div>
                        </div>
                    @endif

                    @if($submission->submittable->content ?? $submission->submittable->full_profile ?? $submission->submittable->description ?? null)
                        <div class="content-field">
                            <div class="content-label">Content</div>
                            <div class="content-value">
                                {!! nl2br(e($submission->submittable->content ?? $submission->submittable->full_profile ?? $submission->submittable->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($submission->submittable->featured_image ?? $submission->submittable->logo ?? null)
                        <div class="content-field">
                            <div class="content-label">Image</div>
                            <img src="{{ asset('storage/' . ($submission->submittable->featured_image ?? $submission->submittable->logo)) }}" 
                                 alt="Preview" class="content-image">
                        </div>
                    @endif

                    @if($submission->submittable->author ?? null)
                        <div class="content-field">
                            <div class="content-label">Author</div>
                            <div class="content-value">{{ $submission->submittable->author }}</div>
                        </div>
                    @endif

                    @if($submission->submittable->type ?? null)
                        <div class="content-field">
                            <div class="content-label">Resource Type</div>
                            <div class="content-value">{{ ucfirst($submission->submittable->type) }}</div>
                        </div>
                    @endif

                    @if($submission->submittable->file_path ?? null)
                        <div class="content-field">
                            <div class="content-label">File</div>
                            <a href="{{ asset('storage/' . $submission->submittable->file_path) }}" target="_blank" 
                               style="color: var(--navy); text-decoration: underline;">
                                📥 Download File
                            </a>
                        </div>
                    @endif

                    @if($submission->submittable->external_url ?? $submission->submittable->video_url ?? null)
                        <div class="content-field">
                            <div class="content-label">URL</div>
                            <a href="{{ $submission->submittable->external_url ?? $submission->submittable->video_url }}" 
                               target="_blank" style="color: var(--navy); text-decoration: underline;">
                                {{ $submission->submittable->external_url ?? $submission->submittable->video_url }}
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Audit Log -->
                @if($submission->activities->isNotEmpty())
                    <div class="audit-log">
                        <h4>Activity History</h4>
                        @foreach($submission->activities as $activity)
                            <div class="audit-item">
                                <strong>{{ $activity->causer->name ?? 'System' }}</strong> 
                                {{ $activity->description }} 
                                <span style="color: var(--muted);">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Action Panel -->
            <div class="action-panel">
                <h3>Review Actions</h3>

                @if($submission->status === 'submitted' || $submission->status === 'needs_changes')
                    <!-- Approve -->
                    <form action="{{ route('secretary.submissions.approve', $submission) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-approve">
                            ✓ Approve & Publish
                        </button>
                    </form>

                    <!-- Request Changes -->
                    <form action="{{ route('secretary.submissions.request-changes', $submission) }}" method="POST" class="action-form">
                        @csrf
                        <div class="form-group">
                            <label for="notes">Request Changes</label>
                            <textarea id="notes" name="notes" class="form-control" placeholder="Explain what needs to be changed..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-changes">
                            🔄 Request Changes
                        </button>
                    </form>

                    <!-- Reject -->
                    <form action="{{ route('secretary.submissions.reject', $submission) }}" method="POST" class="action-form">
                        @csrf
                        <div class="form-group">
                            <label for="reject_notes">Reject Submission</label>
                            <textarea id="reject_notes" name="reviewer_notes" class="form-control" placeholder="Explain why this is being rejected..." required></textarea>
                        </div>
                        <div class="form-group" style="margin-top: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="allow_resubmission" value="1" style="width: auto;">
                                <span style="font-weight: normal; font-size: 0.9rem;">Allow organization to resubmit after making changes</span>
                            </label>
                            <div style="font-size: 0.8rem; color: var(--muted); margin-top: 0.25rem; margin-left: 1.5rem;">
                                If checked, the organization can revise and resubmit this content
                            </div>
                        </div>
                        <button type="submit" class="btn btn-reject">
                            ✕ Reject
                        </button>
                    </form>
                @endif

                <a href="{{ route('secretary.submissions.index') }}" class="btn btn-back">
                    ← Back to Queue
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
