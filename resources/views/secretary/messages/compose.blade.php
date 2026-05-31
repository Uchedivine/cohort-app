@extends('layouts.app')
@section('title', 'Compose Message')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .page-body { max-width:800px; margin:3rem auto; padding:0 2rem; }

    .btn-back { color:var(--muted); font-size:.85rem; text-decoration:none; margin-bottom:1.5rem; display:inline-block; }
    .btn-back:hover { color:var(--navy); }

    .form-card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; }
    .form-group { margin-bottom:1.5rem; }
    .form-group label { display:block; font-size:.9rem; font-weight:600; color:var(--navy); margin-bottom:8px; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; border:1px solid var(--border); border-radius:6px;
        padding:11px 14px; font-size:.9rem; font-family:'DM Sans',sans-serif;
        color:var(--text); outline:none; transition:border-color .2s;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:var(--gold); }
    .form-group textarea { resize:vertical; min-height:200px; }
    .form-error { font-size:.8rem; color:#dc2626; margin-top:6px; }
    .form-hint { font-size:.8rem; color:var(--muted); margin-top:6px; }

    .recipient-options { display:flex; flex-direction:column; gap:1rem; margin-top:1rem; }
    .recipient-option { display:flex; align-items:flex-start; gap:10px; padding:1rem; border:1px solid var(--border); border-radius:6px; cursor:pointer; transition:border-color .2s, background .2s; }
    .recipient-option:hover { border-color:var(--gold); background:#faf9f7; }
    .recipient-option input[type="radio"] { margin-top:3px; }
    .recipient-option-content { flex:1; }
    .recipient-option-title { font-weight:600; color:var(--navy); margin-bottom:4px; }
    .recipient-option-desc { font-size:.85rem; color:var(--muted); }

    #recipientSelect { display:none; margin-top:1rem; }
    #recipientSelect.show { display:block; }

    .btn-send { background:var(--green); color:var(--white); border:none; padding:12px 32px; border-radius:6px; font-size:.95rem; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s; }
    .btn-send:hover { background:#2d4a31; }
    .btn-cancel { background:#f3f4f6; color:var(--text); border:1px solid var(--border); padding:12px 32px; border-radius:6px; font-size:.95rem; cursor:pointer; font-family:'DM Sans',sans-serif; text-decoration:none; display:inline-block; }
    .btn-cancel:hover { border-color:var(--navy); }

    @media(max-width:640px){
        .page-body { padding:0 1.25rem; }
        .form-card { padding:1.5rem; }
    }
</style>

<div class="page-header">
    <h1 class="reveal">Compose Message</h1>
    <p class="reveal">Send a message to one or more organisations</p>
</div>

<div class="page-body">
    <a href="{{ route('secretary.messages.index') }}" class="btn-back reveal">← Back to Messages</a>

    <div class="form-card reveal">
        <form method="POST" action="{{ route('secretary.messages.store') }}">
            @csrf

            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                    placeholder="Enter message subject...">
                @error('subject')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label for="body">Message *</label>
                <textarea id="body" name="body" required
                    placeholder="Write your message here...">{{ old('body') }}</textarea>
                @error('body')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Recipients *</label>
                <div class="recipient-options">
                    <label class="recipient-option">
                        <input type="radio" name="recipient_type" value="all" 
                            {{ old('recipient_type') == 'all' ? 'checked' : '' }}
                            onchange="toggleRecipientSelect()">
                        <div class="recipient-option-content">
                            <div class="recipient-option-title">All Organisations</div>
                            <div class="recipient-option-desc">Send to all approved organisations ({{ $organizations->count() }} total)</div>
                        </div>
                    </label>

                    <label class="recipient-option">
                        <input type="radio" name="recipient_type" value="multiple"
                            {{ old('recipient_type') == 'multiple' ? 'checked' : '' }}
                            onchange="toggleRecipientSelect()">
                        <div class="recipient-option-content">
                            <div class="recipient-option-title">Multiple Organisations</div>
                            <div class="recipient-option-desc">Select specific organisations from the list</div>
                        </div>
                    </label>

                    <label class="recipient-option">
                        <input type="radio" name="recipient_type" value="single"
                            {{ old('recipient_type') == 'single' ? 'checked' : '' }}
                            onchange="toggleRecipientSelect()">
                        <div class="recipient-option-content">
                            <div class="recipient-option-title">Single Organisation</div>
                            <div class="recipient-option-desc">Send to one specific organisation</div>
                        </div>
                    </label>
                </div>
                @error('recipient_type')<p class="form-error">{{ $message }}</p>@enderror

                <div id="recipientSelect">
                    <label for="recipients" style="margin-top:1rem; display:block;">Select Organisation(s) *</label>
                    <select id="recipients" name="recipients[]" multiple size="8"
                        style="height:auto; padding:8px;">
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}"
                                {{ in_array($org->id, old('recipients', [])) ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="form-hint">Hold Ctrl (Cmd on Mac) to select multiple organisations</p>
                    @error('recipients')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div style="display:flex; gap:1rem; margin-top:2rem;">
                <button type="submit" class="btn-send">📤 Send Message</button>
                <a href="{{ route('secretary.messages.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleRecipientSelect() {
        const recipientType = document.querySelector('input[name="recipient_type"]:checked')?.value;
        const recipientSelect = document.getElementById('recipientSelect');
        const selectElement = document.getElementById('recipients');

        if (recipientType === 'multiple' || recipientType === 'single') {
            recipientSelect.classList.add('show');
            selectElement.required = true;
            selectElement.multiple = recipientType === 'multiple';
            selectElement.size = recipientType === 'multiple' ? 8 : 5;
        } else {
            recipientSelect.classList.remove('show');
            selectElement.required = false;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', toggleRecipientSelect);

    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>
@endsection
