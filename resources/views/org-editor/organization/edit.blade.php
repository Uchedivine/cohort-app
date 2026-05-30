@extends('layouts.app')
@section('title', 'Edit Organization Profile')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 900px;
        margin: 0 auto;
    }
    .page-header h1 {
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--white);
    }
    .page-header p {
        font-size: 0.95rem;
        color: #94a3b8;
    }

    .form-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .form-inner {
        max-width: 900px;
        margin: 0 auto;
    }

    .status-alert {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .status-alert.pending {
        border-left: 4px solid #3b82f6;
        background: #eff6ff;
    }
    .status-alert.needs-changes {
        border-left: 4px solid #f59e0b;
        background: #fffbeb;
    }
    .status-alert.approved {
        border-left: 4px solid #10b981;
        background: #f0fdf4;
    }
    .status-icon {
        font-size: 1.5rem;
    }
    .status-content h3 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .status-content p {
        font-size: 0.9rem;
        color: #4b5563;
        line-height: 1.6;
    }
    .reviewer-note {
        background: #f8f9fa;
        border-left: 3px solid var(--gold);
        padding: 1rem;
        margin-top: 0.75rem;
        border-radius: 4px;
    }
    .reviewer-note strong {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted);
        margin-bottom: 0.5rem;
    }

    .form-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2.5rem;
    }
    .form-section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--gold);
    }

    .form-group {
        margin-bottom: 1.75rem;
    }
    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .form-group label .required {
        color: #dc2626;
    }
    .form-help {
        font-size: 0.8rem;
        color: var(--muted);
        margin-top: 0.25rem;
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
        transition: border-color 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }
    textarea.form-control-large {
        min-height: 200px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .file-upload {
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }
    .file-upload:hover {
        border-color: var(--gold);
        background: #fafaf9;
    }
    .file-upload input[type="file"] {
        display: none;
    }
    .file-upload-icon {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
    }
    .file-upload-text {
        font-size: 0.9rem;
        color: var(--muted);
    }
    .current-logo {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 6px;
        text-align: center;
    }
    .current-logo img {
        max-width: 150px;
        max-height: 150px;
        border-radius: 8px;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border);
    }
    .btn {
        padding: 12px 28px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--gold);
        color: var(--navy);
    }
    .btn-primary:hover {
        background: var(--gold-dark);
    }
    .btn-outline {
        background: transparent;
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-outline:hover {
        border-color: var(--navy);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .form-section { padding: 2rem 1.25rem; }
        .form-card { padding: 1.5rem; }
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Edit Organization Profile</h1>
        <p>Update your organization information for public display</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FORM
════════════════════════════════════════════ -->
<section class="form-section">
    <div class="form-inner">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($pendingSubmission)
            <div class="status-alert {{ $pendingSubmission->status }}">
                <div class="status-icon">
                    @if($pendingSubmission->status === 'submitted')
                        ⏳
                    @elseif($pendingSubmission->status === 'needs_changes')
                        🔄
                    @elseif($pendingSubmission->status === 'approved')
                        ✅
                    @endif
                </div>
                <div class="status-content">
                    <h3>
                        @if($pendingSubmission->status === 'submitted')
                            Changes Pending Review
                        @elseif($pendingSubmission->status === 'needs_changes')
                            Changes Requested
                        @elseif($pendingSubmission->status === 'approved')
                            Changes Approved
                        @endif
                    </h3>
                    <p>
                        @if($pendingSubmission->status === 'submitted')
                            Your profile updates are currently under review by the cohort secretary.
                        @elseif($pendingSubmission->status === 'needs_changes')
                            The secretary has requested changes to your submission. Please review the feedback below and resubmit.
                        @elseif($pendingSubmission->status === 'approved')
                            Your changes have been approved and will be published shortly.
                        @endif
                    </p>
                    @if($pendingSubmission->reviewer_notes)
                        <div class="reviewer-note">
                            <strong>Reviewer Feedback:</strong>
                            {{ $pendingSubmission->reviewer_notes }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <form action="{{ route('org-editor.organization.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-card">
                <h2 class="form-section-title">Basic Information</h2>

                <div class="form-group">
                    <label for="name">Organization Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $organization->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="short_description">Short Description <span class="required">*</span></label>
                    <textarea id="short_description" name="short_description" class="form-control" required>{{ old('short_description', $organization->short_description) }}</textarea>
                    <div class="form-help">A brief tagline or mission statement (1-2 sentences)</div>
                </div>

                <div class="form-group">
                    <label for="full_profile">Full Profile <span class="required">*</span></label>
                    <textarea id="full_profile" name="full_profile" class="form-control form-control-large" required>{{ old('full_profile', $organization->full_profile) }}</textarea>
                    <div class="form-help">Detailed description of your organization, mission, and work</div>
                </div>

                <div class="form-group">
                    <label for="logo">Organization Logo</label>
                    @if($organization->logo)
                        <div class="current-logo">
                            <img src="{{ asset('storage/' . $organization->logo) }}" alt="Current logo">
                            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--muted);">Current logo</p>
                        </div>
                    @endif
                    <div class="file-upload" onclick="document.getElementById('logo').click()" style="margin-top: 1rem;">
                        <input type="file" id="logo" name="logo" accept="image/*">
                        <div class="file-upload-icon">🖼️</div>
                        <div class="file-upload-text">
                            <strong>Click to upload new logo</strong><br>
                            <small>PNG, JPG up to 2MB</small>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $organization->location) }}" placeholder="City, Country">
                    </div>

                    <div class="form-group">
                        <label for="founded_year">Founded Year</label>
                        <input type="number" id="founded_year" name="founded_year" class="form-control" value="{{ old('founded_year', $organization->founded_year) }}" min="1900" max="{{ date('Y') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="thematic_focus">Thematic Focus</label>
                    <input type="text" id="thematic_focus" name="thematic_focus" class="form-control" value="{{ old('thematic_focus', $organization->thematic_focus) }}" placeholder="e.g., Health, Environment, Education">
                </div>
            </div>

            <div class="form-card" style="margin-top: 2rem;">
                <h2 class="form-section-title">Programs & Highlights</h2>

                <div class="form-group">
                    <label for="programs">Programs</label>
                    <textarea id="programs" name="programs" class="form-control form-control-large">{{ old('programs', $organization->programs) }}</textarea>
                    <div class="form-help">Describe your key programs and initiatives</div>
                </div>

                <div class="form-group">
                    <label for="highlights">Highlights & Achievements</label>
                    <textarea id="highlights" name="highlights" class="form-control form-control-large">{{ old('highlights', $organization->highlights) }}</textarea>
                    <div class="form-help">Notable achievements, awards, or impact metrics</div>
                </div>
            </div>

            <div class="form-card" style="margin-top: 2rem;">
                <h2 class="form-section-title">Contact & Social Media</h2>

                <div class="form-group">
                    <label for="contact_email">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" class="form-control" value="{{ old('contact_email', $organization->contact_email) }}">
                </div>

                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" class="form-control" value="{{ old('website', $organization->website) }}" placeholder="https://">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="facebook">Facebook</label>
                        <input type="url" id="facebook" name="facebook" class="form-control" value="{{ old('facebook', $organization->facebook) }}" placeholder="https://facebook.com/...">
                    </div>

                    <div class="form-group">
                        <label for="twitter">Twitter</label>
                        <input type="url" id="twitter" name="twitter" class="form-control" value="{{ old('twitter', $organization->twitter) }}" placeholder="https://twitter.com/...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="linkedin">LinkedIn</label>
                        <input type="url" id="linkedin" name="linkedin" class="form-control" value="{{ old('linkedin', $organization->linkedin) }}" placeholder="https://linkedin.com/...">
                    </div>

                    <div class="form-group">
                        <label for="instagram">Instagram</label>
                        <input type="url" id="instagram" name="instagram" class="form-control" value="{{ old('instagram', $organization->instagram) }}" placeholder="https://instagram.com/...">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Submit for Review
                    </button>
                    <a href="{{ route('org-editor.dashboard') }}" class="btn btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    // File upload preview
    document.getElementById('logo').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const uploadText = this.parentElement.querySelector('.file-upload-text');
            uploadText.innerHTML = `<strong>Selected:</strong> ${fileName}`;
        }
    });
</script>

@endsection
