@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')

<style>
    .page-header {
        background: var(--navy);
        color: var(--white);
        padding: 50px 2rem 40px;
    }
    .page-header-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .page-header h1 {
        font-size: clamp(1.8rem, 4vw, 2.4rem);
        font-weight: 600;
        color: var(--white);
    }
    .btn-create {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--gold);
        color: var(--navy);
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: background 0.2s;
    }
    .btn-create:hover {
        background: var(--gold-dark);
    }

    .users-section {
        background: var(--cream);
        padding: 3rem 2rem;
        min-height: 70vh;
    }
    .users-inner {
        max-width: 1200px;
        margin: 0 auto;
    }

    .users-table {
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 10px;
        overflow: hidden;
    }
    .table-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1fr 1.5fr;
        gap: 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--navy);
    }
    .table-row {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: grid;
        grid-template-columns: 2fr 1.5fr 1fr 1fr 1.5fr;
        gap: 1rem;
        align-items: center;
        transition: background 0.2s;
    }
    .table-row:last-child {
        border-bottom: none;
    }
    .table-row:hover {
        background: #f8f9fa;
    }
    .user-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .user-name {
        font-weight: 600;
        color: var(--navy);
    }
    .user-email {
        font-size: 0.85rem;
        color: var(--muted);
    }
    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .role-secretary {
        background: #dbeafe;
        color: #1e40af;
    }
    .role-org-editor {
        background: #d1fae5;
        color: #065f46;
    }
    .status-active {
        color: var(--green);
    }
    .status-inactive {
        color: #6b7280;
    }
    .table-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn-sm {
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-edit {
        background: var(--navy);
        color: var(--white);
    }
    .btn-edit:hover {
        background: #1e293b;
    }
    .btn-delete {
        background: #dc2626;
        color: var(--white);
    }
    .btn-delete:hover {
        background: #b91c1c;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    @media (max-width: 968px) {
        .table-header, .table-row {
            grid-template-columns: 1fr;
            gap: 0.5rem;
        }
        .table-header { display: none; }
        .table-row > div::before {
            content: attr(data-label);
            font-weight: 600;
            display: inline-block;
            margin-right: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .page-header { padding: 40px 1.25rem 30px; }
        .page-header-inner { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .btn-create { width: 100%; justify-content: center; }
        .users-section { padding: 2rem 1.25rem; }
    }
</style>

<!-- ═══════════════════════════════════════════
     HEADER
════════════════════════════════════════════ -->
<section class="page-header">
    <div class="page-header-inner">
        <h1>Manage Users</h1>
        <a href="{{ route('secretary.users.create') }}" class="btn-create">
            ➕ Create New User
        </a>
    </div>
</section>

<!-- ═══════════════════════════════════════════
     USERS TABLE
════════════════════════════════════════════ -->
<section class="users-section">
    <div class="users-inner">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="users-table">
            <div class="table-header">
                <div>User</div>
                <div>Organization</div>
                <div>Role</div>
                <div>Status</div>
                <div>Actions</div>
            </div>

            @forelse($users as $user)
                <div class="table-row">
                    <div data-label="User" class="user-info">
                        <span class="user-name">{{ $user->name }}</span>
                        <span class="user-email">{{ $user->email }}</span>
                    </div>
                    <div data-label="Organization">
                        {{ $user->organization->name ?? '—' }}
                    </div>
                    <div data-label="Role">
                        <span class="role-badge role-{{ $user->roles->first()?->name ?? 'org-editor' }}">
                            {{ $user->roles->first()?->name ?? 'org_editor' }}
                        </span>
                    </div>
                    <div data-label="Status">
                        <span class="status-{{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? '● Active' : '○ Inactive' }}
                        </span>
                    </div>
                    <div data-label="Actions" class="table-actions">
                        <a href="{{ route('secretary.users.edit', $user) }}" class="btn-sm btn-edit">
                            ✏️ Edit
                        </a>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('secretary.users.destroy', $user) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-sm btn-delete">
                                    🗑️ Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div style="padding: 3rem; text-align: center; color: var(--muted);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                    <p>No users found</p>
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div style="margin-top: 2rem;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
