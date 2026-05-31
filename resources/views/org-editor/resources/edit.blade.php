@extends('layouts.app')
@section('title', 'Edit Resource')

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

    .resource-type-selector {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .type-option {
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .type-option:hover {
        border-color: var(--gold);
        background: #fafaf9;
    }
    .type-option input[type="radio"] {
        display: none;
    }
    .type-option input[type="radio"]:checked + label {
        color: var(--navy);
    }
    .type-option input[type="radio"]:checked ~ .type-icon {
        transform: scale(1.1);
    }
    .type-option.active {
        border-color: var(--gold);
        background: linear-gradient(135deg, #fffbf0 0%, var(--white) 100%);
    }
    .type-icon {
        font-size: 3rem;
        margin-bottom: 0.75rem;
        transition: transform 0.2s;
    }
    .type-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--navy);
        display: block;
        cursor: pointer;
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

    .current-file {
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .current-file-icon {
        font-size: 1.5rem;
    }
    .current-file-info {
        flex: 1;
    }
    .current-file-name {
        font-weight: 600;
        color: var(--navy);
        font-size: 0.9rem;
    }
    .current-file-meta {
        font-size: 0.8rem;
        color: var(--muted);
    }

    .conditional-field {
        display: none;
    }
    .conditional-field.active {
        display: block;
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
        color: white;
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

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .form-section { padding: 2rem 1.25rem; }
        .form-card { padding: 1.5rem; }
        .resource-type-selector { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Edit Resource</h1>
        <p>Update your resource details</p>
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

        <form action="{{ route('org-editor.resources.update', $resource) }}" method="POST" enctype="multipart/form-data" id="resourceForm">
            @csrf
            @method('PUT')
            <div class="form-card">
                <div class="form-group">
                    <label>Resource Type <span class="required">*</span></label>
                    <div class="resource-type-selector">
                        <div class="type-option" onclick="selectType('file')">
                            <input type="radio" name="type" value="file" id="type_file" 
                                {{ old('type', $resource->resource_type == 'file' ? 'file' : '') == 'file' ? 'checked' : '' }}>
                            <div class="type-icon">📄</div>
                            <label for="type_file" class="type-label">File Upload</label>
                            <small style="color: var(--muted);">PDF, DOC, PPT</small>
                        </div>
                        <div class="type-option" onclick="selectType('link')">
                            <input type="radio" name="type" value="link" id="type_link" 
                                {{ old('type', $resource->resource_type == 'external_link' ? 'link' : '') == 'link' ? 'checked' : '' }}>
                            <div class="type-icon">🔗</div>
                            <label for="type_link" class="type-label">External Link</label>
                            <small style="color: var(--muted);">Website, Article</small>
                        </div>
                        <div class="type-option" onclick="selectType('video')">
                            <input type="radio" name="type" value="video" id="type_video" 
                                {{ old('type', $resource->resource_type == 'video_link' ? 'video' : '') == 'video' ? 'checked' : '' }}>
                            <div class="type-icon">🎥</div>
                            <label for="type_video" class="type-label">Video Link</label>
                            <small style="color: var(--muted);">YouTube, Vimeo</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">Resource Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $resource->title) }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" class="form-control" required>{{ old('description', $resource->description) }}</textarea>
                    <div class="form-help">Describe what this resource is about and who it's for</div>
                </div>

                <!-- File Upload Field -->
                <div class="form-group conditional-field" id="field_file">
                    <label for="file_path">Upload File</label>
                    
                    @if($resource->file_path)
                        <div class="current-file">
                            <div class="current-file-icon">📄</div>
                            <div class="current-file-info">
                                <div class="current-file-name">{{ basename($resource->file_path) }}</div>
                                <div class="current-file-meta">
                                    {{ $resource->mime_type }} • {{ number_format($resource->file_size / 1024, 2) }} KB
                                </div>
                            </div>
                        </div>
                        <div class="form-help" style="margin-bottom: 0.75rem;">Upload a new file to replace the current one</div>
                    @endif
                    
                    <div class="file-upload" onclick="document.getElementById('file_path').click()">
                        <input type="file" id="file_path" name="file_path" accept=".pdf,.doc,.docx,.ppt,.pptx">
                        <div class="file-upload-icon">📁</div>
                        <div class="file-upload-text">
                            <strong>Click to upload</strong> or drag and drop<br>
                            <small>PDF, DOC, DOCX, PPT, PPTX up to 10MB</small>
                        </div>
                    </div>
                </div>

                <!-- External Link Field -->
                <div class="form-group conditional-field" id="field_link">
                    <label for="external_url">External URL <span class="required">*</span></label>
                    <input type="url" id="external_url" name="external_url" class="form-control" 
                        value="{{ old('external_url', $resource->external_url) }}" placeholder="https://">
                    <div class="form-help">Link to the resource on another website</div>
                </div>

                <!-- Video Link Field -->
                <div class="form-group conditional-field" id="field_video">
                    <label for="video_url">Video URL <span class="required">*</span></label>
                    <input type="url" id="video_url" name="video_url" class="form-control" 
                        value="{{ old('video_url', $resource->external_url) }}" placeholder="https://youtube.com/... or https://vimeo.com/...">
                    <div class="form-help">YouTube or Vimeo video link</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Update Resource
                    </button>
                    <a href="{{ route('org-editor.resources.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()" style="margin-left: auto;">
                        Delete Resource
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form (hidden) -->
        <form id="deleteForm" action="{{ route('org-editor.resources.destroy', $resource) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</section>

<script>
    function selectType(type) {
        // Update radio buttons
        document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('active'));
        document.querySelector(`#type_${type}`).checked = true;
        document.querySelector(`#type_${type}`).closest('.type-option').classList.add('active');

        // Show/hide conditional fields
        document.querySelectorAll('.conditional-field').forEach(field => field.classList.remove('active'));
        document.getElementById(`field_${type}`).classList.add('active');

        // Update required attributes (file is optional on edit if already exists)
        const hasExistingFile = {{ $resource->file_path ? 'true' : 'false' }};
        document.getElementById('file_path').required = (type === 'file' && !hasExistingFile);
        document.getElementById('external_url').required = (type === 'link');
        document.getElementById('video_url').required = (type === 'video');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const checkedType = document.querySelector('input[name="type"]:checked');
        if (checkedType) {
            selectType(checkedType.value);
        }
    });

    // File upload preview
    document.getElementById('file_path').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const uploadText = document.querySelector('.file-upload-text');
            uploadText.innerHTML = `<strong>Selected:</strong> ${fileName}`;
        }
    });

    // Delete confirmation
    function confirmDelete() {
        if (confirm('Are you sure you want to delete this resource? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>

@endsection
