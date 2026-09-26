@extends('layouts.app')

@section('title', $project->exists ? 'Edit project' : 'New project')

@section('content')

<div class="page-heading">
    <div>
        <h1>{{ $project->exists ? 'Edit project' : 'Create new project' }}</h1>
        <p>Add a project to your portfolio</p>
    </div>
</div>

<form
    class="form-shell"
    method="POST"
    action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}"
>
    @csrf

    @if($project->exists)
        @method('PUT')
    @endif

    <div class="card form-section">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="ri-folder-info-line" aria-hidden="true"></i></span>
            <div>
                <h2>Project details</h2>
                <p>The basics — what it's called and who it's for.</p>
            </div>
        </div>

        <div class="form-grid">
            <label>
                Project name
                <input name="name" required value="{{ old('name', $project->name) }}" placeholder="e.g. Website redesign">
                @error('name')<small class="error">{{ $message }}</small>@enderror
            </label>

            <label>
                Client
                <input name="client" value="{{ old('client', $project->client) }}" placeholder="e.g. Internal Operations">
            </label>

            <label>
                Team

                @php
                    $selectedMemberIds = old(
                        'members',
                        $project->exists
                            ? $project->members->pluck('id')->all()
                            : []
                    );
                @endphp

                <select id="team-user-select">
                    <option value="">Select a user</option>

                    @foreach($users as $user)
                        <option value="{{ $user->id }}" data-name="{{ $user->name }}">
                            {{ $user->name }}
                            @if($user->job_title)
                                — {{ $user->job_title }}
                            @endif
                        </option>
                    @endforeach
                </select>

                <div id="selected-team-members" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:8px;"></div>

                <div id="team-member-inputs"></div>

                <small>
                    Select the users who will work on this project. Choose them one at a time.
                    At least 2 team members are required.
                </small>

                @error('members')
                    <small class="error">{{ $message }}</small>
                @enderror
            </label>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const select = document.getElementById('team-user-select');
                    const selectedBox = document.getElementById('selected-team-members');
                    const inputsBox = document.getElementById('team-member-inputs');

                    let selectedUsers = @json($selectedMemberIds);

                    selectedUsers = selectedUsers.map(Number);

                    function renderSelectedUsers() {
                        selectedBox.innerHTML = '';
                        inputsBox.innerHTML = '';

                        selectedUsers.forEach(function (userId) {
                            const option = Array.from(select.options).find(
                                option => Number(option.value) === Number(userId)
                            );

                            if (!option) return;

                            const badge = document.createElement('span');
                            badge.style.cssText =
                                'display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid #ddd;border-radius:999px;background:#f5f5f5;';

                            badge.innerHTML = `
                                <span>${option.dataset.name}</span>
                                <button
                                    type="button"
                                    data-remove="${userId}"
                                    style="border:0;background:none;cursor:pointer;font-size:16px;"
                                >×</button>
                            `;

                            selectedBox.appendChild(badge);

                            const hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'members[]';
                            hidden.value = userId;

                            inputsBox.appendChild(hidden);
                        });

                        select.value = '';
                    }

                    select.addEventListener('change', function () {
                        const userId = Number(this.value);

                        if (!userId) return;

                        if (!selectedUsers.includes(userId)) {
                            selectedUsers.push(userId);
                            renderSelectedUsers();
                        }
                    });

                    selectedBox.addEventListener('click', function (event) {
                        const button = event.target.closest('[data-remove]');

                        if (!button) return;

                        const userId = Number(button.dataset.remove);

                        selectedUsers = selectedUsers.filter(
                            id => id !== userId
                        );

                        renderSelectedUsers();
                    });

                    renderSelectedUsers();
                });
            </script>

            <label>
                Methodology
                <select name="project_type">
                    @foreach(['agile', 'predictive', 'hybrid'] as $v)
                        <option value="{{ $v }}" @selected(old('project_type', $project->project_type ?: 'agile') === $v)>
                            {{ ucfirst($v) }}
                        </option>
                    @endforeach
                </select>
            </label>
        </div>
    </div>

    <div class="card form-section">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="ri-flag-line" aria-hidden="true"></i></span>
            <div>
                <h2>Status &amp; timeline</h2>
                <p>Where it stands right now and when it runs.</p>
            </div>
        </div>

        <div class="form-grid">
            <label>
                Status
                <select name="status">
                    @foreach(['planning', 'in-progress', 'on-hold', 'completed'] as $v)
                        <option value="{{ $v }}" @selected(old('status', $project->status ?: 'planning') === $v)>
                            {{ ucfirst($v) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Priority
                <select name="priority">
                    @foreach(['low', 'medium', 'high'] as $v)
                        <option value="{{ $v }}" @selected(old('priority', $project->priority ?: 'medium') === $v)>
                            {{ ucfirst($v) }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Start date
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
            </label>

            <label>
                End date
                <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}">
                @error('end_date')<small class="error">{{ $message }}</small>@enderror
            </label>
        </div>
    </div>

    <div class="card form-section">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="ri-money-dollar-circle-line" aria-hidden="true"></i></span>
            <div>
                <h2>Budget &amp; progress</h2>
                <p>Track spend against budget and how far along things are.</p>
            </div>
        </div>

        <div class="form-grid {{ $project->exists ? 'form-grid-3' : '' }}">
            <label>
                Budget
                <div class="input-prefix">
                    <span>$</span>
                    <input type="number" min="0" step="0.01" name="budget" value="{{ old('budget', $project->budget) }}" placeholder="0.00">
                </div>
            </label>

            @if($project->exists)
                <label>
                    Spent
                    <div class="input-prefix">
                        <span>$</span>
                        <input type="number" min="0" step="0.01" name="spent" value="{{ old('spent', $project->spent) }}" placeholder="0.00">
                    </div>
                    @error('spent')<small class="error">{{ $message }}</small>@enderror
                </label>
            @endif

            <label>
                Progress
                <div class="input-suffix">
                    <input type="number" min="0" max="100" name="progress" value="{{ old('progress', (int) $project->progress) }}" placeholder="0">
                    <span>%</span>
                </div>
                <small>Set manually — no longer auto-calculated from tasks.</small>
                @error('progress')<small class="error">{{ $message }}</small>@enderror
            </label>
        </div>
    </div>

    <div class="card form-section">
        <div class="form-section-heading">
            <span class="form-section-icon"><i class="ri-file-text-line" aria-hidden="true"></i></span>
            <div>
                <h2>Description</h2>
                <p>Give the team a bit of context.</p>
            </div>
        </div>

        <label class="wide">
            <textarea name="description" rows="5" placeholder="What is this project about?">{{ old('description', $project->description) }}</textarea>
        </label>
    </div>

    <div class="actions">
        <a href="{{ route('projects.index') }}">Cancel</a>

        <button class="button">
            {{ $project->exists ? 'Save changes' : 'Create project' }}
        </button>
    </div>
</form>

@endsection

