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
                <select name="team">
                    <option value="">Select a team</option>

                    @foreach($teams as $team)
                        <option value="{{ $team }}" @selected(old('team', $project->team) === $team)>
                            {{ $team }}
                        </option>
                    @endforeach
                </select>
            </label>

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
