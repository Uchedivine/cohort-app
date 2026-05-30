<x-guest-layout>
<style>
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:#1a1f2e; margin-bottom:6px; }
    .form-group input {
        width:100%; border:1px solid #e2ddd4; border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:#2c2c2c; outline:none; transition:border-color .2s;
    }
    .form-group input:focus { border-color:#c9a84c; box-shadow:0 0 0 3px rgba(201,168,76,.12); }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }
    .form-title { font-size:1.6rem; color:#1a1f2e; margin-bottom:.4rem; text-align:center; }
    .form-subtitle { font-size:.82rem; color:#6b7280; text-align:center; margin-bottom:1.75rem; line-height:1.6; }
    .btn-submit {
        width:100%; background:#c9a84c; color:#1a1f2e; border:none;
        padding:12px; border-radius:6px; font-size:.9rem; font-weight:500;
        cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s;
    }
    .btn-submit:hover { background:#a8863a; }
    .back-link { display:block; text-align:center; margin-top:1.25rem; font-size:.82rem; color:#6b7280; text-decoration:none; }
    .back-link:hover { color:#1a1f2e; }
    .session-status { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:10px 14px; border-radius:6px; font-size:.85rem; margin-bottom:1.25rem; }
</style>

@if (session('status'))
    <div class="session-status">{{ session('status') }}</div>
@endif

<h2 class="form-title">Reset Password</h2>
<p class="form-subtitle">Enter your email and we'll send you a password reset link.</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-submit">Send Reset Link</button>
</form>

<a href="{{ route('login') }}" class="back-link">← Back to login</a>
</x-guest-layout>