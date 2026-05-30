@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
<style>
    .reveal { opacity:0; transform:translateY(24px); transition:opacity .6s ease,transform .6s ease; }
    .reveal.visible { opacity:1; transform:translateY(0); }
    .page-header { background:var(--navy); padding:48px 2rem; text-align:center; }
    .page-header h1 { font-size:clamp(1.8rem,3.5vw,2.6rem); color:var(--white); }
    .page-header p  { color:#94a3b8; margin-top:.5rem; font-size:.9rem; }
    .form-wrap { max-width:640px; margin:3rem auto; padding:0 2rem; }
    .form-card { background:var(--white); border:1px solid var(--border); border-radius:10px; padding:2rem; }
    .form-group { margin-bottom:1.5rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:var(--navy); margin-bottom:6px; }
    .form-group input,
    .form-group select {
        width:100%; border:1px solid var(--border); border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:var(--text); background:var(--white); outline:none; transition:border-color .2s;
    }
    .form-group input:focus,
    .form-group select:focus { border-color:var(--gold); }
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
    .user-meta { background:var(--cream); border:1px solid var(--border); border-radius:8px; padding:1rem 1.25rem; margin-bottom:1.5rem; font-size:.85rem; color:var(--muted); }
    .user-meta strong { color:var(--navy); }
    @media(max-width:640px){ .form-wrap { padding:0 1.25rem; } }
</style>

<div class="page-header">
    <h1 class="reveal">Edit User</h1>
    <p class="reveal">Update user details, role and organization</p>
</div>

<div class="form-wrap">
    <a href="{{ route('secretary.users.index') }}" class="btn-back reveal">← Back to Users</a>

    <div class="form-card reveal">
        <div class="user-meta">
            <p>Editing: <strong>{{ $user->name }}</strong> · Joined {{ $user->created_at->format('M d, Y') }}</p>
        </div>

        <form method="POST" action="{{ route('secretary.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Role *</label>
                <select name="role" required>
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}"
                            {{ $user->roles->first()?->name === $role->name ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
                @error('role')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label>Organization</label>
                <select name="organization_id">
                    <option value="">No organization assigned</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}"
                            {{ $user->organization_id == $org->id ? 'selected' : '' }}>
                            {{ $org->name }}
                        </option>
                    @endforeach
                </select>
                @error('organization_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex; gap:1rem; align-items:center; flex-wrap:wrap;">
                <button type="submit" class="btn-submit">Update User</button>
                <a href="{{ route('secretary.users.index') }}" style="color:var(--muted); font-size:.85rem; text-decoration:none;">Cancel</a>
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
