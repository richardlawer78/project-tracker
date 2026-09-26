@extends('layouts.app')

@section('title', $project->exists ? 'Edit project' : 'New project')

@section('content')

<style>
    .project-form-page {
        --pf-text: #172033;
        --pf-muted: #64748b;
        --pf-soft: #f8fafc;
        --pf-border: #e2e8f0;
        --pf-card: #ffffff;
        --pf-primary: #2563eb;
        --pf-primary-dark: #1d4ed8;
        --pf-shadow: 0 10px 30px rgba(15, 23, 42, .06);

        width: 100%;
        max-width: 1120px;
        margin: 0 auto;
    }

    .project-form-page * {
        box-sizing: border-box;
    }

    .project-form-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 26px;
    }

    .project-form-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: var(--pf-primary);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .project-form-eyebrow span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--pf-primary);
    }

    .project-form-header h1 {
        margin: 0;
        color: var(--pf-text);
        font-size: 30px;
        line-height: 1.2;
        font-weight: 700;
    }

    .project-form-header p {
        margin: 8px 0 0;
        color: var(--pf-muted);
        font-size: 14px;
    }

    .project-mode {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 13px;
        border: 1px solid var(--pf-border);
        border-radius: 10px;
        background: var(--pf-card);
        color: var(--pf-muted);
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .project-mode i {
        color: var(--pf-primary);
        font-size: 17px;
    }

    .project-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .project-form-section {
        overflow: hidden;
        border: 1px solid var(--pf-border);
        border-radius: 16px;
        background: var(--pf-card);
        box-shadow: var(--pf-shadow);
    }

    .project-form-section-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--pf-border);
        background: linear-gradient(to bottom, #ffffff, #fbfdff);
    }

    .project-form-section-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #eff6ff;
        color: var(--pf-primary);
        font-size: 21px;
    }

    .project-form-section-heading h2 {
        margin: 0;
        color: var(--pf-text);
        font-size: 16px;
        font-weight: 700;
    }

    .project-form-section-heading p {
        margin: 4px 0 0;
        color: var(--pf-muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .project-form-section-body {
        padding: 22px;
    }

    .project-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .project-form-grid.three {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .project-form-field {
        min-width: 0;
    }

    .project-form-field.wide {
        grid-column: 1 / -1;
    }

    .project-form-field label {
        display: block;
        margin-bottom: 7px;
        color: var(--pf-text);
        font-size: 13px;
        font-weight: 650;
    }

    .project-form-field .required {
        color: #dc2626;
        margin-left: 3px;
    }

    .project-form-field input,
    .project-form-field select,
    .project-form-field textarea {
        width: 100%;
        border: 1px solid #d7e0eb;
        border-radius: 10px;
        background: #fff;
        color: var(--pf-text);
        font: inherit;
        font-size: 14px;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .project-form-field input,
    .project-form-field select {
        min-height: 46px;
        padding: 0 13px;
    }

    .project-form-field textarea {
        min-height: 135px;
        padding: 13px;
        resize: vertical;
        line-height: 1.6;
    }

    .project-form-field input::placeholder,
    .project-form-field textarea::placeholder {
        color: #94a3b8;
    }

    .project-form-field input:focus,
    .project-form-field select:focus,
    .project-form-field textarea:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
    }

    .project-form-help {
        display: block;
        margin-top: 7px;
        color: var(--pf-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    .project-form-error {
        display: block;
        margin-top: 7px;
        color: #dc2626;
        font-size: 12px;
        font-weight: 600;
    }

    .project-input-group {
        display: flex;
        align-items: stretch;
        width: 100%;
    }

    .project-input-addon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: 0 10px;
        border: 1px solid #d7e0eb;
        background: var(--pf-soft);
        color: #64748b;
        font-size: 14px;
        font-weight: 700;
    }

    .project-input-group.prefix .project-input-addon {
        border-right: 0;
        border-radius: 10px 0 0 10px;
    }

    .project-input-group.prefix input {
        border-radius: 0 10px 10px 0;
    }

    .project-input-group.suffix input {
        border-radius: 10px 0 0 10px;
    }

    .project-input-group.suffix .project-input-addon {
        border-left: 0;
        border-radius: 0 10px 10px 0;
    }

    .progress-note {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        margin-top: 8px;
        padding: 10px 11px;
        border: 1px solid #dbeafe;
        border-radius: 9px;
        background: #eff6ff;
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
    }

    .progress-note i {
        flex: 0 0 auto;
        margin-top: 1px;
        color: var(--pf-primary);
        font-size: 15px;
    }

    .project-form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 0 4px;
    }

    .project-form-actions-left {
        color: var(--pf-muted);
        font-size: 13px;
    }

    .project-form-actions-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .project-form-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 17px;
        border: 1px solid var(--pf-border);
        border-radius: 10px;
        background: #fff;
        color: #475569;
        font-size: 14px;
        font-weight: 650;
        text-decoration: none;
    }

    .project-form-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 19px;
        border: 0;
        border-radius: 10px;
        background: var(--pf-primary);
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 5px 14px rgba(37, 99, 235, .20);
    }

    .project-form-submit:hover {
        background: var(--pf-primary-dark);
    }

    .project-form-alert {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 18px;
        padding: 13px 15px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 13px;
        line-height: 1.5;
    }

    .project-form-alert ul {
        margin: 5px 0 0;
        padding-left: 18px;
    }

    @media (max-width: 850px) {
        .project-form-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .project-form-grid.three {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .project-form-header h1 {
            font-size: 25px;
        }

        .project-form-section-header,
        .project-form-section-body {
            padding: 17px;
        }

        .project-form-grid,
        .project-form-grid.three {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .project-form-field.wide {
            grid-column: auto;
        }

        .project-form-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .project-form-actions-left {
            display: none;
        }

        .project-form-actions-right {
            width: 100%;
        }

        .project-form-cancel,
        .project-form-submit {
            flex: 1;
        }
    }
</style>

<div class="project-form-page">

    <header class="project-form-header">
        <div>
            <div class="project-form-eyebrow">
                <span></span>
                Project workspace
            </div>

            <h1>{{ $project->exists ? 'Edit project' : 'Create new project' }}</h1>

            <p>
                {{ $project->exists
                    ? 'Update the project details, schedule, budget, and delivery information.'
                    : 'Set up the project details your team will use to plan and deliver the work.' }}
            </p>
        </div>

        <div class="project-mode">
            <i class="ri-checkbox-circle-line" aria-hidden="true"></i>
            {{ $project->exists ? 'Edit mode' : 'Ready to create' }}
        </div>
    </header>

    @if($errors->any())
        <div class="project-form-alert">
            <i class="ri-error-warning-line" aria-hidden="true"></i>

            <div>
                <strong>Please check the form.</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form
        class="project-form"
        method="POST"
        action="{{ $project->exists ? route('projects.update', $project) : route('projects.store') }}"
    >
        @csrf

        @if($project->exists)
            @method('PUT')
        @endif

        <section class="project-form-section">

            <div class="project-form-section-header">
                <span class="project-form-section-icon">
                    <i class="ri-folder-info-line" aria-hidden="true"></i>
                </span>

                <div class="project-form-section-heading">
                    <h2>Project details</h2>
                    <p>The basics — what the project is called and who it is for.</p>
                </div>
            </div>

            <div class="project-form-section-body">

                <div class="project-form-grid">

                    <div class="project-form-field">
                        <label for="project-name">
                            Project name <span class="required">*</span>
                        </label>

                        <input
                            id="project-name"
                            name="name"
                            type="text"
                            required
                            value="{{ old('name', $project->name) }}"
                            placeholder="e.g. Website redesign"
                        >

                        @error('name')
                            <small class="project-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="project-form-field">
                        <label for="project-client">Client</label>

                        <input
                            id="project-client"
                            name="client"
                            type="text"
                            value="{{ old('client', $project->client) }}"
                            placeholder="e.g. Internal Operations"
                        >
                    </div>

                    <div class="project-form-field">
                        <label for="project-team">Team</label>

                        <select id="project-team" name="team">
                            <option value="">Select a team</option>

                            @foreach($teams as $team)
                                <option
                                    value="{{ $team }}"
                                    @selected(old('team', $project->team) === $team)
                                >
                                    {{ $team }}
                                </option>
                            @endforeach
                        </select>

                        <small class="project-form-help">
                            Choose the team responsible for delivering this project.
                        </small>
                    </div>

                    <div class="project-form-field">
                        <label for="project-methodology">Methodology</label>

                        <select id="project-methodology" name="project_type">
                            @foreach(['agile', 'predictive', 'hybrid'] as $v)
                                <option
                                    value="{{ $v }}"
                                    @selected(old('project_type', $project->project_type ?: 'agile') === $v)
                                >
                                    {{ ucfirst($v) }}
                                </option>
                            @endforeach
                        </select>

                        <small class="project-form-help">
                            Select how the project will be planned and delivered.
                        </small>
                    </div>

                </div>
            </div>
        </section>

        <section class="project-form-section">

            <div class="project-form-section-header">
                <span class="project-form-section-icon">
                    <i class="ri-calendar-check-line" aria-hidden="true"></i>
                </span>

                <div class="project-form-section-heading">
                    <h2>Status &amp; timeline</h2>
                    <p>Define where the project stands and the period in which it will run.</p>
                </div>
            </div>

            <div class="project-form-section-body">

                <div class="project-form-grid">

                    <div class="project-form-field">
                        <label for="project-status">Status <span class="required">*</span></label>

                        <select id="project-status" name="status">
                            @foreach(['planning', 'in-progress', 'on-hold', 'completed'] as $v)
                                <option
                                    value="{{ $v }}"
                                    @selected(old('status', $project->status ?: 'planning') === $v)
                                >
                                    {{ ucfirst(str_replace('-', ' ', $v)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-form-field">
                        <label for="project-priority">Priority <span class="required">*</span></label>

                        <select id="project-priority" name="priority">
                            @foreach(['low', 'medium', 'high'] as $v)
                                <option
                                    value="{{ $v }}"
                                    @selected(old('priority', $project->priority ?: 'medium') === $v)
                                >
                                    {{ ucfirst($v) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="project-form-field">
                        <label for="project-start-date">Start date</label>

                        <input
                            id="project-start-date"
                            type="date"
                            name="start_date"
                            value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                        >
                    </div>

                    <div class="project-form-field">
                        <label for="project-end-date">End date</label>

                        <input
                            id="project-end-date"
                            type="date"
                            name="end_date"
                            value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                        >

                        @error('end_date')
                            <small class="project-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </section>

        <section class="project-form-section">

            <div class="project-form-section-header">
                <span class="project-form-section-icon">
                    <i class="ri-money-dollar-circle-line" aria-hidden="true"></i>
                </span>

                <div class="project-form-section-heading">
                    <h2>Budget &amp; progress</h2>
                    <p>Keep the project's financial information and delivery progress visible.</p>
                </div>
            </div>

            <div class="project-form-section-body">

                <div class="project-form-grid {{ $project->exists ? 'three' : '' }}">

                    <div class="project-form-field">
                        <label for="project-budget">Budget</label>

                        <div class="project-input-group prefix">
                            <span class="project-input-addon">$</span>

                            <input
                                id="project-budget"
                                type="number"
                                min="0"
                                step="0.01"
                                name="budget"
                                value="{{ old('budget', $project->budget) }}"
                                placeholder="0.00"
                            >
                        </div>

                        <small class="project-form-help">
                            The approved budget for this project.
                        </small>
                    </div>

                    @if($project->exists)

                        <div class="project-form-field">
                            <label for="project-spent">Spent</label>

                            <div class="project-input-group prefix">
                                <span class="project-input-addon">$</span>

                                <input
                                    id="project-spent"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    name="spent"
                                    value="{{ old('spent', $project->spent) }}"
                                    placeholder="0.00"
                                >
                            </div>

                            @error('spent')
                                <small class="project-form-error">{{ $message }}</small>
                            @enderror

                            <small class="project-form-help">
                                Amount spent against the project budget.
                            </small>
                        </div>

                    @endif

                    <div class="project-form-field">
                        <label for="project-progress">Progress</label>

                        <div class="project-input-group suffix">
                            <input
                                id="project-progress"
                                type="number"
                                min="0"
                                max="100"
                                name="progress"
                                value="{{ old('progress', (int) $project->progress) }}"
                                placeholder="0"
                            >

                            <span class="project-input-addon">%</span>
                        </div>

                        <div class="progress-note">
                            <i class="ri-information-line" aria-hidden="true"></i>

                            <span>
                                Project completion percentage. Team task progress can be connected to this value through the project workflow.
                            </span>
                        </div>

                        @error('progress')
                            <small class="project-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
            </div>
        </section>

        <section class="project-form-section">

            <div class="project-form-section-header">
                <span class="project-form-section-icon">
                    <i class="ri-file-text-line" aria-hidden="true"></i>
                </span>

                <div class="project-form-section-heading">
                    <h2>Project description</h2>
                    <p>Give the team useful context about the purpose and scope of the project.</p>
                </div>
            </div>

            <div class="project-form-section-body">

                <div class="project-form-field wide">

                    <label for="project-description">Description</label>

                    <textarea
                        id="project-description"
                        name="description"
                        rows="6"
                        placeholder="What is this project about? Include the main objective, scope, or expected outcome."
                    >{{ old('description', $project->description) }}</textarea>

                    <small class="project-form-help">
                        Keep this concise enough for team members to understand the project at a glance.
                    </small>

                </div>

            </div>
        </section>

        <div class="project-form-actions">

            <div class="project-form-actions-left">
                <i class="ri-shield-check-line" aria-hidden="true"></i>
                Existing project permissions and validation still apply.
            </div>

            <div class="project-form-actions-right">

                <a
                    class="project-form-cancel"
                    href="{{ route('projects.index') }}"
                >
                    Cancel
                </a>

                <button
                    class="project-form-submit"
                    type="submit"
                >
                    <i
                        class="{{ $project->exists ? 'ri-save-3-line' : 'ri-add-line' }}"
                        aria-hidden="true"
                    ></i>

                    {{ $project->exists ? 'Save changes' : 'Create project' }}
                </button>

            </div>

        </div>

    </form>

</div>

@endsection
