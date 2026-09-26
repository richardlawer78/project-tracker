@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

@php
    $jobTitles = [
        'Software Developer',
        'Frontend Developer',
        'Backend Developer',
        'Full Stack Developer',
        'Mobile App Developer',
        'UI/UX Designer',
        'QA Engineer',
        'DevOps Engineer',
        'Data Analyst',
        'Business Analyst',
        'Project Manager',
        'Scrum Master',
        'Product Owner',
        'Team Lead',
        'System Administrator',
    ];

    $currentJobTitle = old('job_title', $user->job_title);

    $isOtherJob = $currentJobTitle === 'other'
        || (!empty($currentJobTitle) && !in_array($currentJobTitle, $jobTitles));

    $otherJobValue = old(
        'job_title_other',
        ($isOtherJob && $currentJobTitle !== 'other') ? $currentJobTitle : ''
    );
@endphp

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

    .field-error {
        margin: 0;
        color: #dc2626;
        font-size: 12px;
    }

    .password-section {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        padding-top: 4px;
        margin-top: 4px;
    }

    .password-section-title {
        grid-column: 1 / -1;
        margin: 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
        color: #111827;
        font-size: 14px;
        font-weight: 700;
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

        .user-form-grid,
        .password-section {
            grid-template-columns: 1fr;
        }

        .user-form-group.full-width,
        .password-section,
        .password-section-title {
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
                Update the user's account information, role, and job details.
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
                Update account details, role, job information, and permissions.
            </p>

        </div>


        <form
            action="{{ route('admin.users.update', $user->id) }}"
            method="POST"
            enctype="multipart/form-data"
            class="user-edit-form"
        >

            @csrf

            @method('PUT')


            <div class="user-form-grid">

                {{-- Profile Photo --}}
                <div class="user-form-group" style="grid-column: 1 / -1;">
                    <label for="avatar">Profile Photo</label>

                    <div style="display: flex; align-items: center; gap: 14px;">
                        @if($user->avatar_url)
                            <img
                                src="{{ $user->avatar_url }}"
                                alt="{{ $user->name }}"
                                style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; flex-shrink: 0;"
                            >
                        @endif

                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            accept="image/png,image/jpeg,image/webp"
                        >
                    </div>

                    <p class="user-form-help">JPG, PNG or WebP, up to 2 MB. Choosing a new photo replaces the current one.</p>

                    @if($user->avatar)
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 500;">
                            <input
                                type="checkbox"
                                name="remove_avatar"
                                value="1"
                                style="width: auto; min-height: 0;"
                            >
                            Remove current photo
                        </label>
                    @endif

                    @error('avatar')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

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
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

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
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

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

                        <option value="">Select a role</option>

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

                    @error('role')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>


                {{-- JOB TITLE --}}
                <div class="user-form-group">

                    <label for="job_title">
                        Job Title
                    </label>

                    <select
                        id="job_title"
                        name="job_title"
                    >

                        <option value="">Select a job title</option>

                        @foreach($jobTitles as $title)
                            <option
                                value="{{ $title }}"
                                {{ !$isOtherJob && $currentJobTitle === $title ? 'selected' : '' }}
                            >
                                {{ $title }}
                            </option>
                        @endforeach

                        <option
                            value="other"
                            {{ $isOtherJob ? 'selected' : '' }}
                        >
                            Other
                        </option>

                    </select>

                    <input
                        type="text"
                        id="job_title_other"
                        name="job_title_other"
                        value="{{ $otherJobValue }}"
                        placeholder="Type the job title"
                        style="{{ $isOtherJob ? '' : 'display: none;' }}"
                    >

                    <p class="user-form-help">
                        Choose the user's position, or pick "Other" to type your own.
                    </p>

                    @error('job_title')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                    @error('job_title_other')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>


                {{-- AVAILABILITY --}}
                <div class="user-form-group">

                    <label for="availability_percent">
                        Availability (%)
                    </label>

                    <input
                        type="number"
                        id="availability_percent"
                        name="availability_percent"
                        value="{{ old('availability_percent', $user->availability_percent ?? 100) }}"
                        min="0"
                        max="100"
                        placeholder="100"
                    >

                    <p class="user-form-help">
                        Enter the user's availability from 0% to 100%.
                    </p>

                    @error('availability_percent')
                        <p class="field-error">{{ $message }}</p>
                    @enderror

                </div>


                {{-- PASSWORD SECTION (optional on edit) --}}</div>


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

<script>
    (function () {
        const jobSelect = document.getElementById('job_title');
        const jobOther = document.getElementById('job_title_other');

        function toggleOtherJob() {
            const isOther = jobSelect.value === 'other';
            jobOther.style.display = isOther ? '' : 'none';
            jobOther.required = isOther;
            if (isOther) { jobOther.focus(); }
        }

        jobSelect.addEventListener('change', toggleOtherJob);
        jobOther.required = jobSelect.value === 'other';
    })();
</script>

@endsection
