@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Create User')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 800px;
        margin: 0 auto;
    }
    .page-header h1 {
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--white);
    }
    .page-header p {
        font-size: 0.95rem;
        color: #94a3b8;
    }

    .form-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .form-inner {
        max-width: 800px;
        margin: 0 auto;
    }
    .form-card {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 2.5rem;
    }

    .form-group {
        margin-bottom: 1.75rem;
    }
    .form-group label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--navy);
        margin-bottom: 0.5rem;
    }
    .form-group label .required {
        color: #dc2626;
    }
    .form-help {
        font-size: 0.8rem;
        color: var(--muted);
        margin-top: 0.25rem;
    }
    .form-control {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 0.95rem;
        font-family: inherit;
        color: var(--text);
        background: var(--white);
        transition: border-color 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--gold);
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2.5rem;
        padding-top: 2rem;
        border-top: 1px solid var(--border);
    }
    .btn {
        padding: 12px 28px;
        border-radius: 6px;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-primary {
        background: var(--gold);
        color: var(--navy);
    }
    .btn-primary:hover {
        background: var(--gold-dark);
    }
    .btn-outline {
        background: transparent;
        color: var(--navy);
        border: 1px solid var(--border);
    }
    .btn-outline:hover {
        border-color: var(--navy);
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .form-section { padding: 2rem 1.25rem; }
        .form-card { padding: 1.5rem; }
        .form-actions { flex-direction: column; }
        .btn { width: 100%; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>{{ isset($user) ? 'Edit User' : 'Create New User' }}</h1>
        <p>{{ isset($user) ? 'Update user account details' : 'Add a new cohort member or secretary account' }}</p>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     FORM
════════════════════════════════════════════ -->
<section class="form-section">
    <div class="form-inner">
        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 0.5rem 0 0 1.25rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ isset($user) ? route('secretary.users.update', $user) : route('secretary.users.store') }}" 
              method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="form-card">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="{{ old('name', $user->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" 
                           value="{{ old('email', $user->email ?? '') }}" required>
                </div>

                @if(!isset($user))
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <div class="form-help">Minimum 8 characters</div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                    </div>
                @else
                    <div class="form-group">
                        <label for="password">New Password (leave blank to keep current)</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <div class="form-help">Only fill this if you want to change the password</div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="role">Role <span class="required">*</span></label>
                    <select id="role" name="role" class="form-control" required onchange="toggleOrgField()">
                        <option value="">Select Role</option>
                        <option value="secretary" {{ old('role', $user->roles->first()?->name ?? '') == 'secretary' ? 'selected' : '' }}>
                            Secretary (Full Access)
                        </option>
                        <option value="org_editor" {{ old('role', $user->roles->first()?->name ?? '') == 'org_editor' ? 'selected' : '' }}>
                            Org Editor (Limited Access)
                        </option>
                    </select>
                </div>

                <div class="form-group" id="org-field" style="display: none;">
                    <label for="organization_id">Organization <span class="required">*</span></label>
                    <select id="organization_id" name="organization_id" class="form-control">
                        <option value="">Select Organization</option>
                        @foreach($organizations as $org)
                            <option value="{{ $org->id }}" 
                                    {{ old('organization_id', $user->organization_id ?? '') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Required for Org Editor role</div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('secretary.users.index') }}" class="btn btn-outline">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    function toggleOrgField() {
        const role = document.getElementById('role').value;
        const orgField = document.getElementById('org-field');
        const orgSelect = document.getElementById('organization_id');
        
        if (role === 'org_editor') {
            orgField.style.display = 'block';
            orgSelect.required = true;
        } else {
            orgField.style.display = 'none';
            orgSelect.required = false;
            orgSelect.value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleOrgField();
    });
</script>

@endsection
