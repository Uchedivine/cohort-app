<x-guest-layout>
<style>
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display:block; font-size:.85rem; font-weight:500; color:#1a1f2e; margin-bottom:6px; }
    .form-group input,
    .form-group textarea,
    .form-group select {
        width:100%; border:1px solid #e2ddd4; border-radius:6px;
        padding:10px 14px; font-size:.875rem; font-family:'DM Sans',sans-serif;
        color:#2c2c2c; outline:none; transition:border-color .2s;
    }
    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus { border-color:#c9a84c; box-shadow:0 0 0 3px rgba(201,168,76,.12); }
    .form-group textarea { resize:vertical; min-height:100px; }
    .form-error { font-size:.78rem; color:#dc2626; margin-top:4px; }
    .form-title { font-size:1.6rem; color:#1a1f2e; margin-bottom:.4rem; text-align:center; }
    .form-subtitle { font-size:.82rem; color:#6b7280; text-align:center; margin-bottom:1.75rem; line-height:1.6; }
    .section-label {
        font-size:.72rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase;
        color:#9ca3af; margin:1.5rem 0 1rem; padding-bottom:.5rem;
        border-bottom:1px solid #e2ddd4;
    }
    .btn-submit {
        width:100%; background:#c9a84c; color:#1a1f2e; border:none;
        padding:12px; border-radius:6px; font-size:.9rem; font-weight:500;
        cursor:pointer; font-family:'DM Sans',sans-serif; transition:background .2s;
        margin-top:.5rem;
    }
    .btn-submit:hover { background:#a8863a; }
    .login-link { display:block; text-align:center; margin-top:1.25rem; font-size:.82rem; color:#6b7280; }
    .login-link a { color:#c9a84c; text-decoration:none; }
    .login-link a:hover { text-decoration:underline; }
</style>

<h2 class="form-title">Join the Cohort</h2>
<p class="form-subtitle">Apply to register your organisation and become a cohort member.</p>

<form method="POST" action="{{ route('organisation.register') }}">
    @csrf

    <p class="section-label">Organisation Details</p>

    <div class="form-group">
        <label>Organisation Name *</label>
        <input type="text" name="org_name" value="{{ old('org_name') }}" required placeholder="Your organisation's full name">
        @error('org_name')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Short Description *</label>
        <textarea name="short_description" rows="3" required placeholder="Brief description of your organisation and its mission...">{{ old('short_description') }}</textarea>
        @error('short_description')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Location *</label>
        <input type="text" name="location" value="{{ old('location') }}" required placeholder="City, Country">
        @error('location')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Thematic Focus *</label>
        <select name="thematic_focus" required>
            <option value="">Select a theme</option>
            <option value="health"       {{ old('thematic_focus') == 'health'       ? 'selected' : '' }}>Health</option>
            <option value="education"    {{ old('thematic_focus') == 'education'    ? 'selected' : '' }}>Education</option>
            <option value="environment"  {{ old('thematic_focus') == 'environment'  ? 'selected' : '' }}>Environment</option>
            <option value="governance"   {{ old('thematic_focus') == 'governance'   ? 'selected' : '' }}>Governance</option>
            <option value="agriculture"  {{ old('thematic_focus') == 'agriculture'  ? 'selected' : '' }}>Agriculture</option>
            <option value="digital"      {{ old('thematic_focus') == 'digital'      ? 'selected' : '' }}>Digital Equity</option>
            <option value="livelihoods"  {{ old('thematic_focus') == 'livelihoods'  ? 'selected' : '' }}>Livelihoods</option>
            <option value="other"        {{ old('thematic_focus') == 'other'        ? 'selected' : '' }}>Other</option>
        </select>
        @error('thematic_focus')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Website</label>
        <input type="url" name="website" value="{{ old('website') }}" placeholder="https://yourorganisation.org">
        @error('website')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Why do you want to join the cohort? *</label>
        <textarea name="why_join" rows="4" required placeholder="Tell us why your organisation wants to join and what you hope to contribute...">{{ old('why_join') }}</textarea>
        @error('why_join')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <p class="section-label">Your Account Details</p>

    <div class="form-group">
        <label>Your Full Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="The person managing this account">
        @error('name')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Email Address *</label>
        <input type="email" name="email" value="{{ old('email') }}" required placeholder="your@email.com">
        @error('email')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Password *</label>
        <input type="password" name="password" required placeholder="Min 8 chars, uppercase, number, symbol">
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-group">
        <label>Confirm Password *</label>
        <input type="password" name="password_confirmation" required>
        @error('password_confirmation')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <button type="submit" class="btn-submit">Submit Application</button>
</form>

<p class="login-link">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</x-guest-layout>