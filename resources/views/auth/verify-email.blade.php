<x-guest-layout>
<style>
    .verify-box { text-align: center; }
    .verify-icon { font-size: 3rem; margin-bottom: 1.25rem; display: block; }
    .form-title { font-size: 1.6rem; color: #1a1f2e; margin-bottom: .75rem; }
    .verify-text { font-size: .875rem; color: #6b7280; line-height: 1.7; margin-bottom: 1.75rem; }
    .btn-submit {
        width:100%; background:#c9a84c; color:#1a1f2e; border:none;
        padding:12px; border-radius:6px; font-size:.9rem; font-weight:500;
        cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s;
        margin-bottom:1rem;
    }
    .btn-submit:hover { background:#a8863a; }
    .session-status { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:10px 14px; border-radius:6px; font-size:.85rem; margin-bottom:1.25rem; }
    .logout-form button {
        background:none; border:none; color:#6b7280; font-size:.82rem;
        cursor:pointer; font-family:'DM Sans',sans-serif; text-decoration:underline;
    }
    .logout-form button:hover { color:#1a1f2e; }
</style>

@if (session('status') == 'verification-link-sent')
    <div class="session-status">A new verification link has been sent to your email.</div>
@endif

<div class="verify-box">
    <span class="verify-icon">📧</span>
    <h2 class="form-title">Verify Your Email</h2>
    <p class="verify-text">
        Thanks for signing up. Please verify your email address by clicking the link we sent you.
        If you didn't receive it, click below to resend.
    </p>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-submit">Resend Verification Email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit">Log out</button>
    </form>
</div>
</x-guest-layout>