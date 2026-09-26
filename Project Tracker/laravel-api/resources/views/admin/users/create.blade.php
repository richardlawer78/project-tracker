@extends('layouts.app')

@section('title', 'Add User')

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

    $currentJobTitle = old('job_title');

    $isOtherJob = $currentJobTitle === 'other'
        || (!empty($currentJobTitle) && !in_array($currentJobTitle, $jobTitles));

    $otherJobValue = old(
        'job_title_other',
        ($isOtherJob && $currentJobTitle !== 'other') ? $currentJobTitle : ''
    );
@endphp

<style>
    .user-create-page {
        max-width: 1100px;
        margin: 0 auto;
    }

    .user-create-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .user-create-header h1 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    .user-create-header p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #374151 !important;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .back-btn:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .user-create-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .user-create-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: #fff;
    }

    .user-create-card-header h2 {
        margin: 0 0 5px;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .user-create-card-header p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }

    .user-create-form {
        padding: 24px;
    }

    .user-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px 20px;
    }

    .user-form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .user-form-group label {
        color: #374151;
        font-size: 13px;
        font-weight: 600;
    }

    .user-form-group input,
    .user-form-group select {
        width: 100%;
        box-sizing: border-box;
        min-height: 44px;
        padding: 10px 13px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #111827;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .user-form-group input::placeholder {
        color: #9ca3af;
    }

    .user-form-group input:focus,
    .user-form-group select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
    }

    .user-form-help {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.5;
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
        gap: 20px;
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
        gap: 10px;
        padding-top: 24px;
        margin-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .user-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 10px 17px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .user-btn-secondary {
        background: #fff;
        color: #374151 !important;
        border-color: #d1d5db;
    }

    .user-btn-secondary:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .user-btn-primary {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .user-btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
        transform: translateY(-1px);
    }

    .alert-danger {
        margin-bottom: 20px;
        padding: 14px 16px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 13px;
    }

    .alert-danger ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    @media (max-width: 700px) {
        .user-create-header {
            flex-direction: column;
        }

        .back-btn {
            width: 100%;
            justify-content: center;
        }

        .user-form-grid,
        .password-section {
            grid-template-columns: 1fr;
        }

        .password-section,
        .password-section-title {
            grid-column: auto;
        }

        .user-form-actions {
            flex-direction: column-reverse;
        }

        .user-btn {
            width: 100%;
        }
    }
</style>

<div class="user-create-page">

    <div class="user-create-header">
        <div>
            <h1>Add User</h1>
            <p>Create a new user and assign their role and team information.</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="back-btn">
            <i class="ri-arrow-left-line"></i>
            Back to Users
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="user-create-card">

        <div class="user-create-card-header">
            <h2>User Information</h2>
            <p>Enter the account details, role, job information, and permissions for the new user.</p>
        </div>

        <form
            action="{{ route('admin.users.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="user-create-form"
        >
            @csrf

            <div class="user-form-grid">

                {{-- Profile Photo --}}
                <div class="user-form-group" style="grid-column: 1 / -1;">
                    <label for="avatar">Profile Photo (optional)</label>

                    <input
                        type="file"
                        id="avatar"
                        name="avatar"
                        accept="image/png,image/jpeg,image/webp"
                    >

                    <p class="user-form-help">JPG, PNG or WebP, up to 2 MB.</p>

                    @error('avatar')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Full Name --}}
                <div class="user-form-group">
                    <label for="name">Full Name</label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Enter full name"
                        autocomplete="name"
                        required
                    >

                    @error('name')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="user-form-group">
                    <label for="email">Email Address</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Enter email address"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="user-form-group">
                    <label for="role">Role</label>

                    <select id="role" name="role" required>
                        <option value="">Select a role</option>

                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="project-manager" {{ old('role') === 'project-manager' ? 'selected' : '' }}>
                            Project Manager
                        </option>

                        <option value="team-lead" {{ old('role') === 'team-lead' ? 'selected' : '' }}>
                            Team Lead
                        </option>

                        <option value="developer" {{ old('role') === 'developer' ? 'selected' : '' }}>
                            Developer
                        </option>

                        <option value="team-member" {{ old('role') === 'team-member' ? 'selected' : '' }}>
                            Team Member
                        </option>

                        <option value="member" {{ old('role') === 'member' ? 'selected' : '' }}>
                            Member
                        </option>
                    </select>

                    @error('role')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Job Title --}}
                <div class="user-form-group">
                    <label for="job_title">Job Title</label>

                    <select id="job_title" name="job_title">
                        <option value="">Select a job title</option>

                        @foreach($jobTitles as $title)
                            <option
                                value="{{ $title }}"
                                {{ !$isOtherJob && $currentJobTitle === $title ? 'selected' : '' }}
                            >
                                {{ $title }}
                            </option>
                        @endforeach

                        <option value="other" {{ $isOtherJob ? 'selected' : '' }}>
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

                {{-- Availability --}}
                <div class="user-form-group">
                    <label for="availability_percent">Availability (%)</label>

                    <input
                        type="number"
                        id="availability_percent"
                        name="availability_percent"
                        value="{{ old('availability_percent', 100) }}"
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

                </div>

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
                    <i class="ri-user-add-line"></i>
                    Create User
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
