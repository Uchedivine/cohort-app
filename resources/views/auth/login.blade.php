<x-guest-layout>
<style>
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block; font-size: .85rem; font-weight: 500;
        color: #1a1f2e; margin-bottom: 6px;
    }
    .form-group input {
        width: 100%; border: 1px solid #e2ddd4; border-radius: 6px;
        padding: 10px 14px; font-size: .875rem; font-family: 'DM Sans', sans-serif;
        color: #2c2c2c; outline: none; transition: border-color .2s;
    }
    .form-group input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,.12); }
    .form-error { font-size: .78rem; color: #dc2626; margin-top: 4px; }
    .form-title { font-size: 1.6rem; color: #1a1f2e; margin-bottom: .4rem; text-align: center; }
    .form-subtitle { font-size: .82rem; color: #6b7280; text-align: center; margin-bottom: 1.75rem; }
    .btn-submit {
        width: 100%; background: #c9a84c; color: #1a1f2e; border: none;
        padding: 12px; border-radius: 6px; font-size: .9rem; font-weight: 500;
        cursor: pointer; font-family: 'DM Sans', sans-serif; transition: background .2s;
        margin-top: .5rem;
    }
    .btn-submit:hover { background: #a8863a; }
    .form-footer {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 1.25rem; font-size: .82rem; flex-wrap: wrap; gap: .5rem;
    }
    .form-footer a { color: #6b7280; text-decoration: none; }
    .form-footer a:hover { color: #1a1f2e; }
    .remember-row {
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 1.25rem; font-size: .85rem; color: #6b7280;
    }
    .remember-row input { width: auto; }
    .session-status {
        background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;
        padding: 10px 14px; border-radius: 6px; font-size: .85rem; margin-bottom: 1.25rem;
    }
</style>

@if (session('status'))
    <div class="session-status">{{ session('status') }}</div>
@endif

<h2 class="form-title">Welcome back</h2>
<p class="form-subtitle">Sign in to access your member portal</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email"
               value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               required autocomplete="current-password">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="remember-row">
        <input type="checkbox" id="remember_me" name="remember">
        <label for="remember_me" style="cursor:pointer;">Remember me</label>
    </div>

    <button type="submit" class="btn-submit">Sign In</button>

    <div class="form-footer">
        <span style="color:#6b7280; font-size:.8rem;">Member access only</span>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">Forgot password?</a>
        @endif
    </div>
</form>
</x-guest-layout>