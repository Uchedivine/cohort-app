@extends('layouts.app')
@section('title', 'Sent Messages')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .page-body { max-width:1100px; margin:3rem auto; padding:0 2rem; }

    .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
    .btn-compose { background:var(--gold); color:var(--navy); padding:10px 24px; border-radius:5px; font-size:.9rem; font-weight:500; text-decoration:none; transition:background .2s; }
    .btn-compose:hover { background:var(--gold-dark); }

    .message-list { display:flex; flex-direction:column; gap:1rem; }
    .message-card {
        background:var(--white); border:1px solid var(--border); border-radius:10px;
        padding:1.5rem; text-decoration:none; color:var(--text);
        transition:box-shadow .2s, border-color .2s;
    }
    .message-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.06); border-color:var(--gold); }
    .message-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.75rem; gap:1rem; }
    .message-subject { font-size:1.05rem; font-weight:600; color:var(--navy); }
    .message-meta { font-size:.8rem; color:var(--muted); display:flex; gap:1rem; flex-wrap:wrap; }
    .message-preview { font-size:.875rem; color:var(--text); line-height:1.6; margin-bottom:.75rem; }
    .message-stats { display:flex; gap:1.5rem; font-size:.8rem; color:var(--muted); }
    .stat-item { display:flex; align-items:center; gap:5px; }

    .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.7rem; font-weight:600; }
    .badge-all { background:#dbeafe; color:#1e40af; }
    .badge-multiple { background:#fef3c7; color:#92400e; }
    .badge-single { background:#d1fae5; color:#065f46; }

    .empty-state { text-align:center; padding:4rem 2rem; background:var(--white); border:1px solid var(--border); border-radius:10px; }
    .empty-state p { color:var(--muted); margin-bottom:1.5rem; }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .message-header { flex-direction:column; }
        .message-stats { flex-direction:column; gap:.5rem; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Sent Messages</h1>
    <p class="reveal">View all messages you've sent to organisations</p>
</div>

<div class="page-body">
    <div class="top-bar reveal">
        <h2 style="font-size:1.1rem; color:var(--navy);">{{ $messages->total() }} message(s)</h2>
        <a href="{{ route('secretary.messages.compose') }}" class="btn-compose">✉️ Compose New Message</a>
    </div>

    @if($messages->isEmpty())
        <div class="empty-state reveal">
            <p style="font-size:2rem; margin-bottom:1rem;">📭</p>
            <p>You haven't sent any messages yet.</p>
            <a href="{{ route('secretary.messages.compose') }}" class="btn-compose">Send Your First Message</a>
        </div>
    @else
        <div class="message-list">
            @foreach($messages as $message)
                <a href="{{ route('secretary.messages.show', $message) }}" class="message-card reveal">
                    <div class="message-header">
                        <div style="flex:1;">
                            <div class="message-subject">{{ $message->subject }}</div>
                            <div class="message-meta">
                                <span>📅 {{ $message->created_at->format('M d, Y · H:i') }}</span>
                                <span class="badge badge-{{ $message->recipient_type }}">
                                    {{ ucfirst($message->recipient_type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="message-preview">
                        {{ Str::limit($message->body, 150) }}
                    </div>
                    <div class="message-stats">
                        <span class="stat-item">
                            👥 {{ $message->recipientCount() }} recipient(s)
                        </span>
                        <span class="stat-item">
                            💬 {{ $message->replies->count() }} reply/replies
                        </span>
                        <span class="stat-item">
                            ✓ {{ $message->recipientCount() - $message->unreadCount() }} read
                        </span>
                        @if($message->unreadCount() > 0)
                            <span class="stat-item" style="color:var(--gold); font-weight:600;">
                                ⏳ {{ $message->unreadCount() }} unread
                            </span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top:2rem;">
            {{ $messages->links() }}
        </div>
    @endif
</div>

<script>
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
