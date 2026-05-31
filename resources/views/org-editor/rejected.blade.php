@extends('layouts.app')
@section('title', 'Application Not Approved')

@section('content')
<style>
    .status-wrap {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.25rem;
    }
    .status-box {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 3rem 2.5rem;
        max-width: 560px;
        width: 100%;
        text-align: center;
    }
    .status-icon { font-size: 3.5rem; margin-bottom: 1.25rem; display: block; }
    .status-box h2 { font-size: 1.8rem; color: var(--navy); margin-bottom: .75rem; }
    .status-box p  { font-size: .9rem; color: var(--muted); line-height: 1.75; margin-bottom: 1rem; }

    .reason-box {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        margin: 1.5rem 0;
        text-align: left;
    }
    .reason-box strong {
        display: block;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #92400e;
        margin-bottom: .5rem;
    }
    .reason-box p {
        font-size: .875rem;
        color: #78350f;
        line-height: 1.6;
        margin: 0;
    }

    .reapply-form { margin-top: 2rem; text-align: left; }
    .reapply-form h3 {
        font-size: 1.2rem;
        color: var(--navy);
        margin-bottom: 1.25rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid var(--border);
    }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:var(--navy); margin-bottom:6px; }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width:100%; border:1px solid var(--border); border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:var(--text); outline:none; transition:border-color .2s;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus { border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,168,76,.12); }
    .form-group textarea { resize:vertical; min-height:100px; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }

    .btn-reapply {
        width: 100%;
        background: var(--gold);
        color: var(--navy);
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-size: .9rem;
        font-weight: 500;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: background .2s;
    }
    .btn-reapply:hover { background: var(--gold-dark); }
    .btn-logout {
        display: inline-block;
        background: none;
        border: 1px solid var(--border);
        color: var(--muted);
        padding: 9px 20px;
        border-radius: 5px;
        font-size: .85rem;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: border-color .2s, color .2s;
        margin-top: 1rem;
        width: 100%;
    }
    .btn-logout:hover { border-color: var(--navy); color: var(--navy); }

    @media(max-width:640px){
        .status-box { padding: 2rem 1.5rem; }
    }
</style>

<div class="status-wrap">
    <div class="status-box">
        <span class="status-icon">📋</span>
        <h2>Application Not Approved</h2>
        <p>
            Thank you for your interest in joining the cohort.
            Unfortunately your application was not approved at this time.
        </p>

        @if(auth()->user()->organization?->rejection_reason)
            <div class="reason-box">
                <strong>Feedback from the Secretary</strong>
                <p>{{ auth()->user()->organization->rejection_reason }}</p>
            </div>
        @endif

        <p style="font-size:.85rem;">
            You are welcome to update your application and reapply below.
            Please address the feedback above before resubmitting.
        </p>

        {{-- Reapply Form --}}
        <div class="reapply-form">
            <h3>Update & Reapply</h3>

            <form method="POST" action="{{ route('org-editor.reapply') }}">
                @csrf

                @if(session('success'))
                    <div style="background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:10px 14px; border-radius:6px; font-size:.85rem; margin-bottom:1.25rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="form-group">
                    <label>Organisation Name *</label>
                    <input type="text" name="org_name"
                        value="{{ old('org_name', auth()->user()->organization?->name) }}" required>
                    @error('org_name')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Short Description *</label>
                    <textarea name="short_description" rows="3" required>{{ old('short_description', auth()->user()->organization?->short_description) }}</textarea>
                    @error('short_description')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" name="location"
                        value="{{ old('location', auth()->user()->organization?->location) }}" required>
                    @error('location')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Thematic Focus *</label>
                    <select name="thematic_focus" required>
                        <option value="">Select a theme</option>
                        @foreach(['health','education','environment','governance','agriculture','digital','livelihoods','other'] as $theme)
                            <option value="{{ $theme }}"
                                {{ old('thematic_focus', auth()->user()->organization?->thematic_focus) == $theme ? 'selected' : '' }}>
                                {{ ucfirst($theme) }}
                            </option>
                        @endforeach
                    </select>
                    @error('thematic_focus')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Website</label>
                    <input type="url" name="website"
                        value="{{ old('website', auth()->user()->organization?->website) }}"
                        placeholder="https://yourorganisation.org">
                    @error('website')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Why do you want to join the cohort? *</label>
                    <textarea name="why_join" rows="4" required>{{ old('why_join', auth()->user()->organization?->highlights) }}</textarea>
                    @error('why_join')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-reapply">Resubmit Application</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">Log Out Instead</button>
            </form>
        </div>
    </div>
</div>
@endsection