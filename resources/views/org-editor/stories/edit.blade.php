@extends('layouts.app')
@section('title', 'Edit Story')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .form-wrap { max-width:760px; margin:3rem auto; padding:0 2rem; }
    .form-card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; }
    .form-group { margin-bottom:1.5rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:var(--navy); margin-bottom:6px; }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width:100%; border:1px solid var(--border); border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:var(--text); background:var(--white); outline:none; transition:border-color .2s;
    }
    .form-group input:focus,
    .form-group textarea:focus { border-color:var(--gold); }
    .form-group textarea { resize:vertical; min-height:140px; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }
    .btn-submit {
        background:var(--gold); color:var(--navy); border:none;
        padding:11px 28px; border-radius:5px; font-size:.9rem;
        font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
        transition:background .2s;
    }
    .btn-submit:hover { background:var(--gold-dark); }
    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }
    .needs-changes-alert {
        background:#fef3c7; border:1px solid #fcd34d; border-radius:8px;
        padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:.875rem; color:#92400e;
    }
    .needs-changes-alert strong { display:block; margin-bottom:4px; }
    .section-divider { border:none; border-top:1px solid var(--border); margin:1.5rem 0; }
    @media(max-width:640px){ .form-wrap { padding:0 1.25rem; } }
</style>

<div class="page-header">
    <h1 class="reveal">Edit Story</h1>
    <p class="reveal">Update and resubmit your story for review</p>
</div>

<div class="form-wrap">
    <a href="{{ route('org-editor.stories.index') }}" class="btn-back reveal">← Back to My Stories</a>

    @if($story->status === 'needs_changes')
        @php $lastSubmission = $story->submissions()->latest()->first(); @endphp
        @if($lastSubmission?->reviewer_notes)
            <div class="needs-changes-alert reveal">
                <strong>📝 Changes Requested by Secretary:</strong>
                {{ $lastSubmission->reviewer_notes }}
            </div>
        @endif
    @endif

    <div class="form-card reveal">
        <form method="POST" action="{{ route('org-editor.stories.update', $story) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Story Title *</label>
                <input type="text" name="title" value="{{ old('title', $story->title) }}" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" value="{{ old('author', $story->author) }}" placeholder="Author name">
                @error('author')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Summary</label>
                <textarea name="summary" rows="3" placeholder="Brief summary of the story...">{{ old('summary', $story->summary) }}</textarea>
                @error('summary')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Full Story</label>
                <textarea name="full_story" rows="8" placeholder="Tell the full story...">{{ old('full_story', $story->full_story) }}</textarea>
                @error('full_story')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Featured Image</label>
                @if($story->featured_image)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/'.$story->featured_image) }}"
                             style="height:120px; border-radius:6px; object-fit:cover;">
                    </div>
                @endif
                <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp">
                @error('featured_image')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <hr class="section-divider">
            <p style="font-size:.85rem; color:var(--muted); margin-bottom:1.25rem;">Optional structured sections</p>

            <div class="form-group">
                <label>Problem</label>
                <textarea name="problem" rows="3" placeholder="What problem did you address?">{{ old('problem', $story->problem) }}</textarea>
            </div>
            <div class="form-group">
                <label>Approach</label>
                <textarea name="approach" rows="3" placeholder="How did you approach it?">{{ old('approach', $story->approach) }}</textarea>
            </div>
            <div class="form-group">
                <label>Outcome</label>
                <textarea name="outcome" rows="3" placeholder="What were the results?">{{ old('outcome', $story->outcome) }}</textarea>
            </div>
            <div class="form-group">
                <label>Lessons Learned</label>
                <textarea name="lessons" rows="3" placeholder="What did you learn?">{{ old('lessons', $story->lessons) }}</textarea>
            </div>

            <hr class="section-divider">

            <div class="form-group">
                <label>Tags</label>
                <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
                    @foreach($tags as $tag)
                        <label style="display:flex; align-items:center; gap:5px; font-size:.85rem; font-weight:400; cursor:pointer;">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ $story->tags->contains($tag->id) ? 'checked' : '' }}>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <button type="submit" class="btn-submit">Resubmit for Review</button>
                <a href="{{ route('org-editor.stories.index') }}" style="color:var(--muted); font-size:.85rem; text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
