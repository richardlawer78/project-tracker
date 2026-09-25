@extends('layouts.app')

@section('title', 'My Profile')

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
    .pf-page {
        --pf-text: #1e2a44;
        --pf-muted: #64708a;
        --pf-card: #ffffff;
        --pf-line: rgba(30, 42, 68, 0.12);
        --pf-input: #ffffff;
        --pf-accent: #6366f1;

        max-width: 760px;
        margin: 0 auto;
        color: var(--pf-text);
    }

    body.dark .pf-page {
        --pf-text: #e9effd;
        --pf-muted: #9aa8c7;
        --pf-card: rgba(255, 255, 255, 0.07);
        --pf-line: rgba(255, 255, 255, 0.14);
        --pf-input: rgba(255, 255, 255, 0.08);
    }

    .pf-head h1 {
        margin: 0 0 4px;
        font-size: 26px;
        font-weight: 800;
    }

    .pf-head p {
        margin: 0 0 22px;
        color: var(--pf-muted);
        font-size: 14px;
    }

    .pf-card {
        padding: 28px;
        border: 1px solid var(--pf-line);
        border-radius: 20px;
        background: var(--pf-card);
        box-shadow: 0 12px 40px rgba(49, 46, 129, 0.10);
    }

    .pf-photo {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--pf-line);
    }

    .pf-avatar {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 96px;
        height: 96px;
        overflow: hidden;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font-size: 32px;
        font-weight: 800;
        box-shadow: 0 10px 26px rgba(99, 102, 241, 0.35);
    }

    .pf-avatar img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pf-photo-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pf-photo-actions small {
        color: var(--pf-muted);
        font-size: 12px;
    }

    .pf-upload {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 0 16px;
        border: 1px solid var(--pf-line);
        border-radius: 12px;
        background: var(--pf-input);
        color: var(--pf-text);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        width: fit-content;
    }

    .pf-upload:hover {
        border-color: var(--pf-accent);
        color: var(--pf-accent);
    }

    .pf-upload input {
        display: none;
    }

    .pf-remove {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--pf-muted);
        font-size: 13px;
        cursor: pointer;
    }

    .pf-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .pf-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .pf-field.pf-full {
        grid-column: 1 / -1;
    }

    .pf-field label {
        font-size: 13px;
        font-weight: 700;
    }

    .pf-field input,
    .pf-field select {
        width: 100%;
        box-sizing: border-box;
        min-height: 44px;
        padding: 10px 13px;
        border: 1px solid var(--pf-line);
        border-radius: 10px;
        background: var(--pf-input);
        color: var(--pf-text);
        font: inherit;
        font-size: 14px;
        outline: none;
    }

    .pf-field input:focus,
    .pf-field select:focus {
        border-color: var(--pf-accent);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .pf-field select option {
        color: #1e2a44;
    }

    .pf-field input[disabled] {
        opacity: 0.65;
        cursor: not-allowed;
    }

    .pf-error {
        margin: 0;
        color: #e11d48;
        font-size: 13px;
    }

    .pf-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .pf-save {
        min-height: 44px;
        padding: 0 24px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font: inherit;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
    }

    @media (max-width: 640px) {
        .pf-photo {
            flex-direction: column;
            align-items: flex-start;
        }

        .pf-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="pf-page">

    <div class="pf-head">
        <h1>My profile</h1>
        <p>Update your photo and personal details.</p>
    </div>

    <form
        class="pf-card"
        method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
    >
        @csrf

        {{-- PHOTO --}}
        <div class="pf-photo">

            <div class="pf-avatar" id="pf-preview">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                @else
                    {{ $user->initials }}
                @endif
            </div>

            <div class="pf-photo-actions">

                <label class="pf-upload">
                    Choose photo
                    <input
                        type="file"
                        name="avatar"
                        id="pf-file"
                        accept="image/png,image/jpeg,image/webp"
                    >
                </label>

                <small>JPG, PNG or WebP, up to 2 MB.</small>

                @if($user->avatar)
                    <label class="pf-remove">
                        <input type="checkbox" name="remove_avatar" value="1">
                        Remove current photo
                    </label>
                @endif

                @error('avatar')
                    <p class="pf-error">{{ $message }}</p>
                @enderror

            </div>

        </div>

        {{-- DETAILS --}}
        <div class="pf-grid">

            <div class="pf-field">
                <label for="name">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                >
                @error('name')
                    <p class="pf-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pf-field">
                <label for="job_title">Job title</label>

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

                    <option value="other" {{ $isOtherJob ? 'selected' : '' }}>Other</option>

                </select>

                <input
                    type="text"
                    id="job_title_other"
                    name="job_title_other"
                    value="{{ $otherJobValue }}"
                    placeholder="Type your job title"
                    style="{{ $isOtherJob ? '' : 'display: none;' }}"
                >

                @error('job_title')
                    <p class="pf-error">{{ $message }}</p>
                @enderror

                @error('job_title_other')
                    <p class="pf-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="pf-field pf-full">
                <label for="email">Email</label>
                <input type="email" id="email" value="{{ $user->email }}" disabled>
            </div>

        </div>

        <div class="pf-actions">
            <button type="submit" class="pf-save">Save changes</button>
        </div>

    </form>

</div>

<script>
    (function () {
        var file = document.getElementById('pf-file');
        var preview = document.getElementById('pf-preview');

        /* Job title: "Other" reveals a box to type your own */
        var jobSelect = document.getElementById('job_title');
        var jobOther = document.getElementById('job_title_other');

        function toggleOtherJob() {
            var isOther = jobSelect.value === 'other';
            jobOther.style.display = isOther ? '' : 'none';
            jobOther.required = isOther;
            if (isOther && document.activeElement === jobSelect) { jobOther.focus(); }
        }

        jobSelect.addEventListener('change', toggleOtherJob);
        jobOther.required = jobSelect.value === 'other';

        file.addEventListener('change', function () {
            if (!file.files || !file.files[0]) { return; }

            var reader = new FileReader();

            reader.onload = function (e) {
                preview.innerHTML = '';
                var img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                preview.appendChild(img);
            };

            reader.readAsDataURL(file.files[0]);
        });
    })();
</script>

@endsection