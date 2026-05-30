@extends('layouts.app')
@section('title', 'Manage Tags')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 1000px;
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

    .tags-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .tags-inner {
        max-width: 1000px;
        margin: 0 auto;
    }

    .create-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .create-card h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
    }
    .create-form {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }
    .form-group {
        margin-bottom: 0;
    }
    .form-group label {
        display: block;
        font-size: 0.85rem;
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
        transition: border-color 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }
    .btn-create {
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        background: var(--gold);
        color: var(--navy);
        border: none;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
    }
    .btn-create:hover {
        background: var(--gold-dark);
    }

    .tags-list {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2rem;
    }
    .tags-list h2 {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 1.5rem;
    }
    .tags-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    .tag-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        background: #f8f9fa;
        border: 1px solid var(--border);
        border-radius: 8px;
        transition: box-shadow 0.2s;
    }
    .tag-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .tag-info {
        flex: 1;
    }
    .tag-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 4px;
    }
    .tag-meta {
        font-size: 0.75rem;
        color: var(--muted);
    }
    .tag-type {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
        text-transform: uppercase;
        background: var(--green-light);
        color: var(--green);
        margin-right: 6px;
    }
    .tag-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn-sm {
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete {
        background: #dc2626;
        color: var(--white);
    }
    .btn-delete:hover {
        background: #b91c1c;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--muted);
    }
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .tags-section { padding: 2rem 1.25rem; }
        .create-form { grid-template-columns: 1fr; }
        .tags-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Manage Tags</h1>
        <p>Create and manage tags, themes, and SDGs for categorizing content</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     TAGS MANAGEMENT
════════════════════════════════════════════ -->
<section class="tags-section">
    <div class="tags-inner">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        <!-- Create New Tag -->
        <div class="create-card">
            <h2>Create New Tag</h2>
            <form action="{{ route('secretary.tags.store') }}" method="POST" class="create-form">
                @csrf
                <div class="form-group">
                    <label for="name">Tag Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           placeholder="e.g., Health, SDG 3, Climate Action" required>
                </div>
                <div class="form-group">
                    <label for="type">Tag Type</label>
                    <select id="type" name="type" class="form-control">
                        <option value="general">General</option>
                        <option value="sdg">SDG</option>
                        <option value="theme">Theme</option>
                        <option value="sector">Sector</option>
                    </select>
                </div>
                <button type="submit" class="btn-create">➕ Create Tag</button>
            </form>
        </div>

        <!-- Tags List -->
        <div class="tags-list">
            <h2>All Tags ({{ $tags->count() }})</h2>
            
            @if($tags->isNotEmpty())
                <div class="tags-grid">
                    @foreach($tags as $tag)
                        <div class="tag-item">
                            <div class="tag-info">
                                <div class="tag-name">{{ $tag->name }}</div>
                                <div class="tag-meta">
                                    <span class="tag-type">{{ $tag->type ?? 'general' }}</span>
                                    Used {{ $tag->stories_count + $tag->resources_count + $tag->events_count + $tag->organizations_count }} times
                                </div>
                            </div>
                            <div class="tag-actions">
                                <form action="{{ route('secretary.tags.destroy', $tag) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this tag? It will be removed from all content.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-delete">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🏷️</div>
                    <h3 style="font-size: 1.2rem; color: var(--navy); margin-bottom: 0.5rem;">No tags yet</h3>
                    <p>Create your first tag to start categorizing content.</p>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
