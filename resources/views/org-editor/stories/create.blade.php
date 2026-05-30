@extends('layouts.app')
@section('title', 'Create Story')

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
        min-height: 120px;
        resize: vertical;
    }
    textarea.form-control-large {
        min-height: 300px;
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

    .structured-fields {
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1.5rem;
        margin-top: 1rem;
    }
    .structured-fields h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1rem;
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
    .btn-secondary {
        background: var(--navy);
        color: var(--white);
    }
    .btn-secondary:hover {
        background: #1e293b;
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
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Create New Story</h1>
        <p>Share your organization's impact story with the cohort</p>
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

        <form action="{{ route('org-editor.stories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-card">
                <div class="form-group">
                    <label for="title">Story Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label for="summary">Summary <span class="required">*</span></label>
                    <textarea id="summary" name="summary" class="form-control" required>{{ old('summary') }}</textarea>
                    <div class="form-help">A brief overview of your story (2-3 sentences)</div>
                </div>

                <div class="form-group">
                    <label for="content">Full Story <span class="required">*</span></label>
                    <textarea id="content" name="content" class="form-control form-control-large" required>{{ old('content') }}</textarea>
                    <div class="form-help">Tell your complete story with details and context</div>
                </div>

                <div class="form-group">
                    <label for="author">Author Name</label>
                    <input type="text" id="author" name="author" class="form-control" value="{{ old('author') }}">
                </div>

                <div class="form-group">
                    <label for="featured_image">Featured Image</label>
                    <div class="file-upload" onclick="document.getElementById('featured_image').click()">
                        <input type="file" id="featured_image" name="featured_image" accept="image/*">
                        <div class="file-upload-icon">🖼️</div>
                        <div class="file-upload-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            <small>PNG, JPG, GIF up to 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Optional: Structured Story Sections</label>
                    <div class="structured-fields">
                        <h4>📋 Problem / Challenge</h4>
                        <textarea name="structured_content[problem]" class="form-control" placeholder="What problem or challenge did you address?">{{ old('structured_content.problem') }}</textarea>

                        <h4 style="margin-top: 1.5rem;">💡 Approach / Solution</h4>
                        <textarea name="structured_content[approach]" class="form-control" placeholder="How did you approach solving it?">{{ old('structured_content.approach') }}</textarea>

                        <h4 style="margin-top: 1.5rem;">✨ Outcomes / Impact</h4>
                        <textarea name="structured_content[outcome]" class="form-control" placeholder="What were the results and impact?">{{ old('structured_content.outcome') }}</textarea>

                        <h4 style="margin-top: 1.5rem;">📚 Lessons Learned</h4>
                        <textarea name="structured_content[lessons]" class="form-control" placeholder="What did you learn from this experience?">{{ old('structured_content.lessons') }}</textarea>
                    </div>
                    <div class="form-help">These sections help structure your story for better readability</div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="submit" class="btn btn-primary">
                        Submit for Review
                    </button>
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">
                        Save as Draft
                    </button>
                    <a href="{{ route('org-editor.dashboard') }}" class="btn btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    // File upload preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const uploadText = document.querySelector('.file-upload-text');
            uploadText.innerHTML = `<strong>Selected:</strong> ${fileName}`;
        }
    });
</script>

@endsection
