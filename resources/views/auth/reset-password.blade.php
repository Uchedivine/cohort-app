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
    .form-subtitle { font-size:.82rem; color:#6b7280; text-align:center; margin-bottom:1.75rem; }
    .btn-submit {
        width:100%; background:#c9a84c; color:#1a1f2e; border:none;
        padding:12px; border-radius:6px; font-size:.9rem; font-weight:500;
        cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s;
    }
    .btn-submit:hover { background:#a8863a; }
</style>

<h2 class="form-title">Set New Password</h2>
<p class="form-subtitle">Choose a strong password for your account.</p>

<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn-submit">Reset Password</button>
</form>
</x-guest-layout>