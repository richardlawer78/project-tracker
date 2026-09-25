@extends('layouts.app')

@section('title', 'User Management')

@section('content')

<style>
    .users-page {
        max-width: 1400px;
        margin: 0 auto;
    }

    .users-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .users-page-header h1 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    .users-page-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .users-add-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 8px;
        background: #2563eb;
        color: #fff !important;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .users-add-btn:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    .users-alert {
        padding: 13px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .users-alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .users-alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .users-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .users-stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 18px 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .users-stat-label {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .users-stat-value {
        color: #111827;
        font-size: 25px;
        font-weight: 700;
    }

    .users-table-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
    }

    .users-table-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .users-table-header h2 {
        margin: 0 0 4px;
        font-size: 17px;
        color: #111827;
    }

    .users-table-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }

    .users-table-wrapper {
        overflow-x: auto;
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .users-table th {
        text-align: left;
        padding: 13px 18px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .users-table td {
        padding: 15px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #374151;
        font-size: 14px;
        vertical-align: middle;
    }

    .users-table tbody tr:hover {
        background: #f9fafb;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .user-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 2px;
    }

    .user-email {
        color: #6b7280;
        font-size: 12px;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .availability {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 110px;
    }

    .availability-bar {
        width: 60px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .availability-fill {
        height: 100%;
        background: #22c55e;
        border-radius: 999px;
    }

    .actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 7px 11px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .edit-btn {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .edit-btn:hover {
        background: #dbeafe;
    }

    .delete-btn {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .delete-btn:hover {
        background: #fee2e2;
    }

    .empty-state {
        text-align: center;
        padding: 55px 20px;
    }

    .empty-state-icon {
        font-size: 38px;
        color: #9ca3af;
        margin-bottom: 12px;
    }

    .empty-state h3 {
        margin: 0 0 6px;
        color: #111827;
        font-size: 17px;
    }

    .empty-state p {
        margin: 0 0 18px;
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .users-page-header {
            flex-direction: column;
        }

        .users-add-btn {
            width: 100%;
            justify-content: center;
        }

        .users-stats {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="users-page">

<div class="users-page-header">
    <div>
        <h1>User Management</h1>
        <p>Manage users, roles, and access to the Project Tracker.</p>
    </div>

    <a href="{{ route('admin.users.create') }}" class="users-add-btn">
        <i class="ri-user-add-line"></i>
        Add User
    </a>
</div>

@if(session('success'))
    <div class="users-alert users-alert-success">
        <i class="ri-checkbox-circle-line"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="users-alert users-alert-error">
        <i class="ri-error-warning-line"></i>
        {{ session('error') }}
    </div>
@endif

<div class="users-stats">
    <div class="users-stat-card">
        <div class="users-stat-label">Total Users</div>
        <div class="users-stat-value">{{ $users->count() }}</div>
    </div>

    <div class="users-stat-card">
        <div class="users-stat-label">Administrators</div>
        <div class="users-stat-value">
            {{ $users->where('role', 'admin')->count() }}
        </div>
    </div>

    <div class="users-stat-card">
        <div class="users-stat-label">Team Members</div>
        <div class="users-stat-value">
            {{ $users->whereIn('role', ['team-member', 'member', 'developer', 'team-lead', 'project-manager'])->count() }}
        </div>
    </div>
</div>

<div class="users-table-card">

    <div class="users-table-header">
        <h2>All Users</h2>
        <p>View and manage everyone who has access to the system.</p>
    </div>

    @if($users->count())
        <div class="users-table-wrapper">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Job Title</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar" @if($user->avatar_url) style="overflow:hidden;" @endif>
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                                        @else
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        @endif
                                    </div>

                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                        </div>

                                        <div class="user-email">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ str_replace('-', ' ', $user->role) }}
                                </span>
                            </td>

                            <td>
                                {{ $user->job_title ?? '—' }}
                            </td>

                            <td>
                                @if($user->availability_percent !== null)
                                    <div class="availability">
                                        <div class="availability-bar">
                                            <div
                                                class="availability-fill"
                                                style="width: {{ min(100, max(0, $user->availability_percent)) }}%;"
                                            ></div>
                                        </div>

                                        <span>
                                            {{ $user->availability_percent }}%
                                        </span>
                                    </div>
                                @else
                                    —
                                @endif
                            </td>

                            <td>
                                <div class="actions">
                                    <a
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="action-btn edit-btn"
                                    >
                                        <i class="ri-edit-line"></i>
                                        Edit
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <form
                                            action="{{ route('admin.users.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="action-btn delete-btn"
                                            >
                                                <i class="ri-delete-bin-line"></i>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="ri-group-line"></i>
            </div>

            <h3>No users found</h3>

            <p>There are currently no users in the Project Tracker.</p>

            <a href="{{ route('admin.users.create') }}" class="users-add-btn">
                <i class="ri-user-add-line"></i>
                Add First User
            </a>
        </div>
    @endif

</div>

</div>
@endsection