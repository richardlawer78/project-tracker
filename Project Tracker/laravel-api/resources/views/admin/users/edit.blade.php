```blade
@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<style>
    .user-edit-page {
        max-width: 1000px;
        margin: 0 auto;
    }

    .user-edit-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 28px;
    }

    .user-edit-header h1 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .user-edit-header p {
        margin: 0;
        color: #64748b;
        font-size: 15px;
    }

    .user-edit-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .user-edit-card-header {
        padding: 24px 28px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .user-edit-card-header h2 {
        margin: 0 0 6px;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .user-edit-card-header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .user-edit-form {
        padding: 28px;
    }

    .user-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
    }

    .user-form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .user-form-group.full-width {
        grid-column: 1 / -1;
    }

    .user-form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }

    .user-form-group input,
    .user-form-group select {
        width: 100%;
        min-height: 48px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        font-size: 15px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .user-form-group input::placeholder {
        color: #94a3b8;
    }

    .user-form-group input:focus,
    .user-form-group select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .user-form-group select {
        cursor: pointer;
        appearance: auto;
    }

    .user-form-help {
        margin: 0;
        font-size: 12px;
        color: #64748b;
    }

    .user-form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #e2e8f0;
    }

    .user-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 20px;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .user-btn-secondary {
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #334155;
    }

    .user-btn-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }

    .user-btn-primary {
        border: 1px solid #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .user-btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-1px);
    }

    .user-alert {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 10px;
        font-size: 14px;
    }

    .user-alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .user-alert-danger ul {
        margin: 0;
        padding-left: 20px;
    }

    @media (max-width: 700px) {

        .user-edit-header {
            flex-direction: column;
        }

        .user-form-grid {
            grid-template-columns: 1fr;
        }

        .user-form-group.full-width {
            grid-column: auto;
        }

        .user-edit-form {
            padding: 20px;
        }

        .user-edit-card-header {
            padding: 20px;
        }

        .user-form-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .user-btn {
            width: 100%;
        }

    }
</style>


<div class="user-edit-page">

    {{-- PAGE HEADER --}}
    <div class="user-edit-header">

        <div>
            <h1>Edit User</h1>

            <p>
                Update the user's account information and role.
            </p>
        </div>

        <a
            href="{{ route('admin.users.index') }}"
            class="user-btn user-btn-secondary"
        >
            ← Back to Users
        </a>

    </div>


    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="user-alert user-alert-danger">

            <strong>Please correct the following:</strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- EDIT CARD --}}
    <div class="user-edit-card">

        <div class="user-edit-card-header">

            <h2>User Information</h2>

            <p>
                Update account details and permissions.
            </p>

        </div>


        <form
            action="{{ route('admin.users.update', $user->id) }}"
            method="POST"
            class="user-edit-form"
        >

            @csrf

            @method('PUT')


            <div class="user-form-grid">

                {{-- NAME --}}
                <div class="user-form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Enter full name"
                        required
                    >

                </div>


                {{-- EMAIL --}}
                <div class="user-form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        placeholder="Enter email address"
                        required
                    >

                </div>


                {{-- ROLE --}}
                <div class="user-form-group">

                    <label for="role">
                        Role
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                    >

                        <option
                            value="admin"
                            {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}
                        >
                            Admin
                        </option>

                        <option
                            value="project-manager"
                            {{ old('role', $user->role) === 'project-manager' ? 'selected' : '' }}
                        >
                            Project Manager
                        </option>

                        <option
                            value="team-lead"
                            {{ old('role', $user->role) === 'team-lead' ? 'selected' : '' }}
                        >
                            Team Lead
                        </option>

                        <option
                            value="developer"
                            {{ old('role', $user->role) === 'developer' ? 'selected' : '' }}
                        >
                            Developer
                        </option>

                        <option
                            value="team-member"
                            {{ old('role', $user->role) === 'team-member' ? 'selected' : '' }}
                        >
                            Team Member
                        </option>

                        <option
                            value="member"
                            {{ old('role', $user->role) === 'member' ? 'selected' : '' }}
                        >
                            Member
                        </option>

                    </select>

                    <p class="user-form-help">
                        The role determines the user's access level.
                    </p>

                </div>

            </div>


            {{-- BUTTONS --}}
            <div class="user-form-actions">

                <a
                    href="{{ route('admin.users.index') }}"
                    class="user-btn user-btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="user-btn user-btn-primary"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
```
