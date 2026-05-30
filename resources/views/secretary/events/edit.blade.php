@extends('layouts.app')
@section('title', 'Edit Event')

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
    .form-group textarea:focus,
    .form-group select:focus { border-color:var(--gold); }
    .form-group textarea { resize:vertical; min-height:120px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }
    .btn-submit {
        background:var(--gold); color:var(--navy); border:none;
        padding:11px 28px; border-radius:5px; font-size:.9rem;
        font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif;
        transition:background .2s;
    }
    .btn-submit:hover { background:var(--gold-dark); }
    .btn-back {
        color:var(--muted); font-size:.85rem; text-decoration:none;
        margin-bottom:1.5rem; display:inline-block;
    }
    .btn-back:hover { color:var(--navy); }

    .media-section { margin-top:2rem; }
    .media-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem; margin-top:1rem; }
    .media-item { border:1px solid var(--border); border-radius:8px; overflow:hidden; }
    .media-item img { width:100%; height:120px; object-fit:cover; }
    .media-item p { font-size:.78rem; color:var(--muted); padding:8px; }

    @media(max-width:640px){
        .form-wrap { padding:0 1.25rem; }
        .form-row { grid-template-columns:1fr; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Edit Event</h1>
    <p class="reveal">Update event details and manage media</p>
</div>

<div class="form-wrap">
    <a href="{{ route('secretary.events.index') }}" class="btn-back reveal">← Back to Events</a>

    <div class="form-card reveal">
        <form method="POST" action="{{ route('secretary.events.update', $event) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Event Title *</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description">{{ old('description', $event->description) }}</textarea>
                @error('description')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Start Date & Time *</label>
                    <input type="datetime-local" name="start_date"
                        value="{{ old('start_date', $event->start_date?->format('Y-m-d\TH:i')) }}" required>
                    @error('start_date')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>End Date & Time</label>
                    <input type="datetime-local" name="end_date"
                        value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}">
                    @error('end_date')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" value="{{ old('location', $event->location) }}" placeholder="City, Country">
                    @error('location')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label>Virtual Link</label>
                    <input type="url" name="virtual_link" value="{{ old('virtual_link', $event->virtual_link) }}" placeholder="https://zoom.us/...">
                    @error('virtual_link')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-group">
                <label>RSVP Link</label>
                <input type="url" name="rsvp_link" value="{{ old('rsvp_link', $event->rsvp_link) }}" placeholder="https://...">
                @error('rsvp_link')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Banner Image</label>
                @if($event->banner_image)
                    <div style="margin-bottom:10px;">
                        <img src="{{ asset('storage/'.$event->banner_image) }}"
                             style="height:120px; border-radius:6px; object-fit:cover;">
                    </div>
                @endif
                <input type="file" name="banner_image" accept="image/jpeg,image/png,image/webp">
                @error('banner_image')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Tags</label>
                <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
                    @foreach($tags as $tag)
                        <label style="display:flex; align-items:center; gap:5px; font-size:.85rem; font-weight:400; cursor:pointer;">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ $event->tags->contains($tag->id) ? 'checked' : '' }}>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <button type="submit" class="btn-submit">Update Event</button>
                <a href="{{ route('secretary.events.index') }}" style="color:var(--muted); font-size:.85rem; text-decoration:none;">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Media Upload Section --}}
    <div class="form-card media-section reveal" style="margin-top:1.5rem;">
        <h3 style="font-size:1.2rem; color:var(--navy); margin-bottom:1rem;">Event Media</h3>

        <form method="POST" action="{{ route('secretary.events.media.upload', $event) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Media Type</label>
                    <select name="media_type" id="mediaType" onchange="toggleMediaFields()">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Caption</label>
                    <input type="text" name="caption" placeholder="Optional caption">
                </div>
            </div>
            <div class="form-group" id="imageField">
                <label>Image File</label>
                <input type="file" name="file_path" accept="image/jpeg,image/png,image/webp">
            </div>
            <div class="form-group" id="videoField" style="display:none;">
                <label>Video URL (YouTube / Vimeo)</label>
                <input type="url" name="video_url" placeholder="https://youtube.com/...">
            </div>
            <button type="submit" class="btn-submit" style="padding:9px 20px; font-size:.85rem;">Upload Media</button>
        </form>

        @if($event->media->count() > 0)
            <div class="media-grid" style="margin-top:1.5rem;">
                @foreach($event->media as $media)
                    <div class="media-item">
                        @if($media->media_type === 'image')
                            <img src="{{ asset('storage/'.$media->file_path) }}" alt="{{ $media->caption }}">
                        @else
                            <div style="height:120px; background:var(--navy); display:flex; align-items:center; justify-content:center; font-size:2rem;">🎥</div>
                        @endif
                        <p>{{ $media->caption ?? 'No caption' }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
    function toggleMediaFields() {
        const type = document.getElementById('mediaType').value;
        document.getElementById('imageField').style.display = type === 'image' ? 'block' : 'none';
        document.getElementById('videoField').style.display = type === 'video' ? 'block' : 'none';
    }
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
