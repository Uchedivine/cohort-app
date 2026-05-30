@extends('layouts.app')
@section('title', isset($event) ? 'Edit Event' : 'Create Event')

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
        min-height: 150px;
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
    .btn-danger {
        background: #dc2626;
        color: var(--white);
    }
    .btn-danger:hover {
        background: #b91c1c;
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
        <h1>{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h1>
        <p>{{ isset($event) ? 'Update event details' : 'Add a new cohort event or activity' }}</p>
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

        <form action="{{ isset($event) ? route('secretary.events.update', $event) : route('secretary.events.store') }}" 
              method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($event))
                @method('PUT')
            @endif

            <div class="form-card">
                <div class="form-group">
                    <label for="title">Event Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="{{ old('title', $event->title ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-control" required>{{ old('description', $event->description ?? '') }}</textarea>
                    <div class="form-help">Provide details about the event, agenda, and what attendees can expect</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date & Time <span class="required">*</span></label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control" 
                               value="{{ old('start_date', isset($event) ? $event->start_date->format('Y-m-d\TH:i') : '') }}" required>
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date & Time</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control" 
                               value="{{ old('end_date', isset($event) && $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" 
                           value="{{ old('location', $event->location ?? '') }}" placeholder="City, Venue, or 'Virtual'">
                </div>

                <div class="form-group">
                    <label for="virtual_link">Virtual Meeting Link</label>
                    <input type="url" id="virtual_link" name="virtual_link" class="form-control" 
                           value="{{ old('virtual_link', $event->virtual_link ?? '') }}" placeholder="https://zoom.us/...">
                    <div class="form-help">Zoom, Google Meet, or other virtual meeting link</div>
                </div>

                <div class="form-group">
                    <label for="rsvp_link">RSVP Link</label>
                    <input type="url" id="rsvp_link" name="rsvp_link" class="form-control" 
                           value="{{ old('rsvp_link', $event->rsvp_link ?? '') }}" placeholder="https://...">
                    <div class="form-help">Link to registration or RSVP form</div>
                </div>

                <div class="form-group">
                    <label for="banner_image">Banner Image</label>
                    @if(isset($event) && $event->banner_image)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ asset('storage/' . $event->banner_image) }}" 
                                 style="max-width: 300px; border-radius: 8px;">
                        </div>
                    @endif
                    <div class="file-upload" onclick="document.getElementById('banner_image').click()">
                        <input type="file" id="banner_image" name="banner_image" accept="image/*">
                        <div class="file-upload-icon">🖼️</div>
                        <div class="file-upload-text">
                            <strong>Click to upload banner</strong><br>
                            <small>PNG, JPG up to 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($event) ? 'Update Event' : 'Create Event' }}
                    </button>
                    <a href="{{ route('secretary.events.index') }}" class="btn btn-outline">Cancel</a>
                    @if(isset($event))
                        <button type="button" class="btn btn-danger" 
                                onclick="if(confirm('Are you sure you want to delete this event?')) document.getElementById('delete-form').submit();">
                            Delete Event
                        </button>
                    @endif
                </div>
            </div>
        </form>

        @if(isset($event))
            <form id="delete-form" action="{{ route('secretary.events.destroy', $event) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
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
