@extends('layouts.app')
@section('title', 'Edit Event')

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
    .form-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2.5rem;
    }

    .reviewer-feedback {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-radius: 6px;
    }
    .reviewer-feedback h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #92400e;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .reviewer-feedback p {
        font-size: 0.9rem;
        color: #78350f;
        line-height: 1.6;
        margin: 0;
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
        min-height: 120px;
        resize: vertical;
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
    .current-image {
        margin-top: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 6px;
        text-align: center;
    }
    .current-image img {
        max-width: 300px;
        max-height: 200px;
        border-radius: 4px;
        margin-top: 0.5rem;
    }

    .sdg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }
    .sdg-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .sdg-checkbox:hover {
        border-color: var(--gold);
        background: #fafaf9;
    }
    .sdg-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    .sdg-checkbox label {
        font-size: 0.85rem;
        cursor: pointer;
        margin: 0;
    }

    .tag-select {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .tag-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tag-item:hover {
        border-color: var(--gold);
        background: #fafaf9;
    }
    .tag-item input[type="checkbox"] {
        cursor: pointer;
    }
    .tag-item label {
        font-size: 0.85rem;
        cursor: pointer;
        margin: 0;
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

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .form-section { padding: 2rem 1.25rem; }
        .form-card { padding: 1.5rem; }
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
        .sdg-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Edit Event</h1>
        <p>Update your event and resubmit for review</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FORM
════════════════════════════════════════════ -->
<section class="form-section">
    <div class="form-inner">
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

        @php
            $latestSubmission = $event->submissions->sortByDesc('created_at')->first();
        @endphp

        @if($latestSubmission && $latestSubmission->reviewer_notes)
            <div class="reviewer-feedback">
                <h3>📝 Reviewer Feedback</h3>
                <p>{{ $latestSubmission->reviewer_notes }}</p>
            </div>
        @endif

        <form action="{{ route('org-editor.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-card">
                <div class="form-group">
                    <label for="title">Event Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
                    <div class="form-help">Provide details about the event, agenda, and what attendees can expect</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date & Time <span class="required">*</span></label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date & Time</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $event->location) }}" placeholder="e.g., Community Center, 123 Main St">
                    <div class="form-help">Physical location or leave blank for virtual-only events</div>
                </div>

                <div class="form-group">
                    <label for="virtual_link">Virtual Meeting Link</label>
                    <input type="url" id="virtual_link" name="virtual_link" class="form-control" value="{{ old('virtual_link', $event->virtual_link) }}" placeholder="https://zoom.us/j/...">
                    <div class="form-help">Zoom, Teams, or other virtual meeting link</div>
                </div>

                <div class="form-group">
                    <label for="rsvp_link">RSVP / Registration Link</label>
                    <input type="url" id="rsvp_link" name="rsvp_link" class="form-control" value="{{ old('rsvp_link', $event->rsvp_link) }}" placeholder="https://eventbrite.com/...">
                    <div class="form-help">Link where people can register or RSVP</div>
                </div>

                <div class="form-group">
                    <label for="banner_image">Banner Image</label>
                    @if($event->banner_image)
                        <div class="current-image">
                            <small style="color: var(--muted);">Current image:</small>
                            <br>
                            <img src="{{ asset('storage/' . $event->banner_image) }}" alt="Current banner">
                        </div>
                    @endif
                    <div class="file-upload" onclick="document.getElementById('banner_image').click()" style="margin-top: 1rem;">
                        <input type="file" id="banner_image" name="banner_image" accept="image/*">
                        <div class="file-upload-icon">🖼️</div>
                        <div class="file-upload-text">
                            <strong>Click to upload new image</strong> or drag and drop<br>
                            <small>PNG, JPG, GIF up to 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Related SDGs (Sustainable Development Goals)</label>
                    <div class="sdg-grid">
                        @for($i = 1; $i <= 17; $i++)
                            <div class="sdg-checkbox">
                                <input type="checkbox" id="sdg_{{ $i }}" name="sdgs[]" value="{{ $i }}" {{ in_array($i, old('sdgs', $event->sdgs ?? [])) ? 'checked' : '' }}>
                                <label for="sdg_{{ $i }}">SDG {{ $i }}</label>
                            </div>
                        @endfor
                    </div>
                </div>

                @if($tags->isNotEmpty())
                    <div class="form-group">
                        <label>Tags</label>
                        <div class="tag-select">
                            @foreach($tags as $tag)
                                <div class="tag-item">
                                    <input type="checkbox" id="tag_{{ $tag->id }}" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $event->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                    <label for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Resubmit for Review
                    </button>
                    <a href="{{ route('org-editor.events.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    // File upload preview
    document.getElementById('banner_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const uploadText = document.querySelector('.file-upload-text');
            uploadText.innerHTML = `<strong>Selected:</strong> ${fileName}`;
        }
    });
</script>

@endsection
