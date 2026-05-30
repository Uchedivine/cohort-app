@extends('layouts.app')
@section('title', $resource->title)

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:56px 2rem 48px; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.8rem); color:var(--white); max-width:700px; margin:0 auto 1rem; }
    .page-header p  { color:#94a3b8; font-size:.9rem; }
    .page-body { max-width:800px; margin:3rem auto; padding:0 2rem; }
    .card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:1.5rem; }
    .meta-row { display:flex; gap:1rem; flex-wrap:wrap; align-items:center; margin-bottom:1.5rem; }
    .type-badge {
        display:inline-block; background:var(--green-light); color:var(--green);
        padding:4px 12px; border-radius:20px; font-size:.75rem;
        font-weight:600; text-transform:uppercase; letter-spacing:.04em;
    }
    .tag-pill {
        display:inline-block; background:#f3f4f6; color:var(--muted);
        padding:3px 10px; border-radius:20px; font-size:.75rem;
    }
    .description { font-size:.95rem; color:#4b5563; line-height:1.8; margin-bottom:2rem; }
    .download-btn {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--navy); color:var(--white);
        padding:12px 24px; border-radius:6px; font-size:.9rem;
        font-weight:500; text-decoration:none; transition:background .2s;
    }
    .download-btn:hover { background:var(--gold); color:var(--navy); }
    .external-btn {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--gold); color:var(--navy);
        padding:12px 24px; border-radius:6px; font-size:.9rem;
        font-weight:500; text-decoration:none; transition:background .2s;
    }
    .external-btn:hover { background:var(--gold-dark); }
    .detail-row { display:flex; justify-content:space-between; padding:.6rem 0; border-bottom:1px solid #f3f4f6; font-size:.875rem; }
    .detail-row:last-child { border-bottom:none; }
    .detail-label { color:var(--muted); }
    .detail-value { color:var(--navy); font-weight:500; }
    .org-link { text-decoration:none; color:var(--gold); }
    .org-link:hover { text-decoration:underline; }
    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }
    @media(max-width:640px){ .page-body { padding:0 1.25rem; } .detail-row { flex-direction:column; gap:4px; } }
</style>

<div class="page-header">
    <h1 class="reveal">{{ $resource->title }}</h1>
    <p class="reveal">{{ $resource->organization->name ?? '' }} · {{ $resource->published_at?->format('Y') }}</p>
</div>

<div class="page-body">
    <a href="{{ route('resources.index') }}" class="btn-back reveal">← Back to Resources</a>

    <div class="card reveal">
        <div class="meta-row">
            <span class="type-badge">{{ str_replace('_', ' ', $resource->resource_type) }}</span>
            @foreach($resource->tags as $tag)
                <span class="tag-pill">{{ $tag->name }}</span>
            @endforeach
        </div>

        @if($resource->description)
            <p class="description">{{ $resource->description }}</p>
        @endif

        <div style="margin-bottom:2rem;">
            @if($resource->resource_type === 'file' && $resource->file_path)
                <a href="{{ asset('storage/'.$resource->file_path) }}" target="_blank" class="download-btn">
                    📄 Download File
                </a>
                @if($resource->file_size)
                    <span style="margin-left:12px; font-size:.8rem; color:var(--muted);">
                        {{ number_format($resource->file_size / 1024, 1) }} KB
                    </span>
                @endif
            @elseif($resource->resource_type === 'external_link' && $resource->external_url)
                <a href="{{ $resource->external_url }}" target="_blank" class="external-btn">
                    🔗 Visit Resource
                </a>
            @elseif($resource->resource_type === 'video_link' && $resource->external_url)
                <a href="{{ $resource->external_url }}" target="_blank" class="external-btn">
                    🎥 Watch Video
                </a>
            @endif
        </div>

        <div>
            <div class="detail-row">
                <span class="detail-label">Organization</span>
                <span class="detail-value">
                    @if($resource->organization)
                        <a href="{{ route('organizations.show', $resource->organization->slug) }}" class="org-link">
                            {{ $resource->organization->name }}
                        </a>
                    @else
                        —
                    @endif
                </span>
            </div>
            @if($resource->theme)
                <div class="detail-row">
                    <span class="detail-label">Theme</span>
                    <span class="detail-value">{{ ucfirst($resource->theme) }}</span>
                </div>
            @endif
            @if($resource->year)
                <div class="detail-row">
                    <span class="detail-label">Year</span>
                    <span class="detail-value">{{ $resource->year }}</span>
                </div>
            @endif
            @if($resource->published_at)
                <div class="detail-row">
                    <span class="detail-label">Published</span>
                    <span class="detail-value">{{ $resource->published_at->format('M d, Y') }}</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
