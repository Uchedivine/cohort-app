@extends('layouts.app')
@section('title', $message->subject)

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.6rem,3vw,2.2rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.85rem; }
    .page-body { max-width:900px; margin:3rem auto; padding:0 2rem; }

    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }

    .message-card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:1.5rem; }
    .message-header { border-bottom:1px solid var(--border); padding-bottom:1rem; margin-bottom:1.5rem; }
    .message-subject { font-size:1.4rem; font-weight:600; color:var(--navy); margin-bottom:.75rem; }
    .message-meta { font-size:.85rem; color:var(--muted); display:flex; gap:1.5rem; flex-wrap:wrap; }
    .message-body { font-size:.95rem; color:var(--text); line-height:1.8; white-space:pre-wrap; }

    .replies-section { margin-top:2rem; }
    .replies-section h3 { font-size:1.1rem; color:var(--navy); margin-bottom:1.25rem; }
    .reply-card { background:var(--white); border:1px solid var(--border); border-radius:8px; padding:1.5rem; margin-bottom:1rem; }
    .reply-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid #f3f4f6; }
    .reply-author { font-weight:600; color:var(--navy); font-size:.9rem; }
    .reply-date { font-size:.8rem; color:var(--muted); }
    .reply-body { font-size:.9rem; color:var(--text); line-height:1.7; white-space:pre-wrap; }

    .reply-form-card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; margin-top:2rem; }
    .reply-form-card h3 { font-size:1.05rem; color:var(--navy); margin-bottom:1.25rem; }
    .form-group { margin-bottom:1.25rem; }
    .form-group label { display:block; font-size:.9rem; font-weight:600; color:var(--navy); margin-bottom:8px; }
    .form-group textarea { width:100%; border:1px solid var(--border); border-radius:6px; padding:11px 14px; font-size:.9rem; font-family:'DM Sans',sans-serif; color:var(--text); outline:none; transition:border-color .2s; resize:vertical; min-height:120px; }
    .form-group textarea:focus { border-color:var(--gold); }
    .form-error { font-size:.8rem; color:#dc2626; margin-top:6px; }

    .btn-reply { background:var(--green); color:var(--white); border:none; padding:10px 24px; border-radius:6px; font-size:.9rem; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s; }
    .btn-reply:hover { background:#2d4a31; }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .message-card, .reply-form-card { padding:1.5rem; }
        .message-meta { flex-direction:column; gap:.5rem; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">{{ $message->subject }}</h1>
    <p class="reveal">Message from Secretary</p>
</div>

<div class="page-body">
    <a href="{{ route('org-editor.messages.index') }}" class="btn-back reveal">← Back to Messages</a>

    {{-- Original Message --}}
    <div class="message-card reveal">
        <div class="message-header">
            <div class="message-subject">{{ $message->subject }}</div>
            <div class="message-meta">
                <span>📅 Received {{ $message->created_at->format('M d, Y · H:i') }}</span>
                <span>👤 From: {{ $message->sender->name }} (Secretary)</span>
            </div>
        </div>
        <div class="message-body">{{ $message->body }}</div>
    </div>

    {{-- Replies --}}
    @if($message->replies->isNotEmpty())
        <div class="replies-section reveal">
            <h3>💬 Conversation ({{ $message->replies->count() }} {{ Str::plural('reply', $message->replies->count()) }})</h3>
            @foreach($message->replies as $reply)
                <div class="reply-card">
                    <div class="reply-header">
                        <span class="reply-author">
                            @if($reply->sent_by === auth()->id())
                                You
                            @else
                                {{ $reply->sender->name }} (Secretary)
                            @endif
                        </span>
                        <span class="reply-date">{{ $reply->created_at->format('M d, Y · H:i') }}</span>
                    </div>
                    <div class="reply-body">{{ $reply->body }}</div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Reply Form --}}
    <div class="reply-form-card reveal">
        <h3>✍️ Send a Reply</h3>
        <form method="POST" action="{{ route('org-editor.messages.reply', $message) }}">
            @csrf
            <div class="form-group">
                <label for="body">Your Reply *</label>
                <textarea id="body" name="body" required
                    placeholder="Type your reply here...">{{ old('body') }}</textarea>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-reply">📤 Send Reply</button>
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
