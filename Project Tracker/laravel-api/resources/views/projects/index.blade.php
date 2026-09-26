 @'
@extends('layouts.app')

@section('title', 'Projects')

@section('content')

<style>
    .projects-page {
        --border: #e5e7eb;
        --muted: #64748b;
        --heading: #172033;
        --primary: #2563eb;
        --surface: #ffffff;
        --soft: #f8fafc;
        --shadow: 0 10px 30px rgba(15, 23, 42, .06);
        width: 100%;
    }

    .projects-page * {
        box-sizing: border-box;
    }

    .projects-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 24px;
    }

    .projects-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .projects-eyebrow span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
    }

    .projects-page h1 {
        margin: 0;
        color: var(--heading);
        font-size: 32px;
        line-height: 1.15;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .projects-subtitle {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 14px;
    }

    .new-project {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 10px;
        background: var(--primary);
        color: #fff !important;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .20);
        transition: .18s ease;
    }

    .new-project:hover {
        transform: translateY(-1px);
        background: #1d4ed8;
    }

    .new-project-plus {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 6px;
        background: rgba(255,255,255,.16);
        font-size: 17px;
    }

    .projects-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-card {
        padding: 18px;
        border: 1px solid var(--border);
        border-radius: 14px;
        background: var(--surface);
        box-shadow: var(--shadow);
    }

    .summary-label {
        display: block;
        margin-bottom: 7px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 600;
    }

    .summary-value {
        display: block;
        color: var(--heading);
        font-size: 26px;
        line-height: 1;
        font-weight: 800;
    }

    .summary-text {
        display: block;
        margin-top: 7px;
        color: var(--muted);
        font-size: 12px;
    }

    .projects-card {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: var(--surface);
        box-shadow: var(--shadow);
    }

    .projects-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--border);
    }

    .projects-card-title {
        margin: 0;
        color: var(--heading);
        font-size: 17px;
        font-weight: 750;
    }

    .projects-card-description {
        margin: 5px 0 0;
        color: var(--muted);
        font-size: 13px;
    }

    .project-count {
        padding: 6px 10px;
        border: 1px solid #dbe4f0;
        border-radius: 999px;
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
    }

    .projects-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .projects-table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .projects-table th {
        padding: 13px 18px;
        border-bottom: 1px solid var(--border);
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .07em;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .projects-table td {
        padding: 17px 18px;
        border-bottom: 1px solid #edf1f5;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .projects-table tbody tr {
        transition: background .15s ease;
    }

    .projects-table tbody tr:hover td {
        background: #f8fbff;
    }

    .projects-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .project-cell {
        min-width: 270px;
    }

    .project-main {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .project-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #eef4ff;
        color: var(--primary);
        font-size: 15px;
        font-weight: 800;
    }

    .project-info {
        min-width: 0;
    }

    .project-name {
        display: block;
        overflow: hidden;
        color: var(--heading);
        font-size: 14px;
        font-weight: 750;
        line-height: 1.35;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .project-description {
        display: block;
        max-width: 330px;
        margin-top: 3px;
        overflow: hidden;
        color: #94a3b8;
        font-size: 12px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .client-name {
        color: #475569;
        font-weight: 600;
    }

    .status-badge,
    .health-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 750;
        line-height: 1;
        white-space: nowrap;
        text-transform: capitalize;
    }

    .status-badge::before,
    .health-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-planning {
        background: #eff6ff;
        border-color: #dbeafe;
        color: #2563eb;
    }

    .status-in-progress,
    .status-active {
        background: #ecfdf5;
        border-color: #d1fae5;
        color: #059669;
    }

    .status-completed {
        background: #f0fdf4;
        border-color: #dcfce7;
        color: #16a34a;
    }

    .status-on-hold,
    .status-pending {
        background: #fff7ed;
        border-color: #fed7aa;
        color: #ea580c;
    }

    .status-cancelled {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    .status-default {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #64748b;
    }

    .health-on-track {
        background: #ecfdf5;
        border-color: #d1fae5;
        color: #059669;
    }

    .health-at-risk {
        background: #fff7ed;
        border-color: #fed7aa;
        color: #ea580c;
    }

    .health-critical {
        background: #fef2f2;
        border-color: #fecaca;
        color: #dc2626;
    }

    .health-completed {
        background: #eff6ff;
        border-color: #dbeafe;
        color: #2563eb;
    }

    .progress-cell {
        min-width: 160px;
    }

    .progress-line {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress-track {
        position: relative;
        width: 105px;
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: #e8edf4;
    }

    .progress-fill {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #3b82f6, #6366f1);
    }

    .progress-number {
        min-width: 36px;
        color: #334155;
        font-size: 12px;
        font-weight: 750;
    }

    .timeline-date {
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .open-project {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 10px;
        border-radius: 8px;
        color: var(--primary) !important;
        text-decoration: none !important;
        font-size: 12px;
        font-weight: 750;
        white-space: nowrap;
    }

    .open-project:hover {
        background: #eff6ff;
    }

    .empty-state {
        padding: 60px 24px !important;
        text-align: center;
    }

    .empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        margin-bottom: 12px;
        border-radius: 14px;
        background: #eff6ff;
        color: var(--primary);
        font-size: 22px;
    }

    .empty-title {
        display: block;
        margin-bottom: 5px;
        color: var(--heading);
        font-size: 15px;
        font-weight: 750;
    }

    .empty-text {
        color: var(--muted);
        font-size: 13px;
    }

    .pagination-wrap {
        padding: 17px 20px;
        border-top: 1px solid var(--border);
    }

    /* Dark mode */

    .dark .projects-page {
        --border: rgba(148, 163, 184, .14);
        --muted: #94a3b8;
        --heading: #f1f5f9;
        --surface: #111827;
        --shadow: 0 12px 35px rgba(0,0,0,.2);
    }

    .dark .projects-table th {
        background: #172033;
        color: #94a3b8;
    }

    .dark .projects-table td {
        border-color: var(--border);
    }

    .dark .projects-table tbody tr:hover td {
        background: #172033;
    }

    .dark .project-name,
    .dark .summary-value,
    .dark .projects-card-title,
    .dark .empty-title {
        color: #f8fafc;
    }

    .dark .client-name,
    .dark .timeline-date,
    .dark .progress-number {
        color: #cbd5e1;
    }

    .dark .project-count {
        background: #172033;
        border-color: var(--border);
        color: #cbd5e1;
    }

    .dark .progress-track {
        background: #263244;
    }

    @media (max-width: 900px) {
        .projects-summary {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .projects-header {
            align-items: stretch;
            flex-direction: column;
        }

        .new-project {
            width: 100%;
        }

        .projects-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .projects-table {
            min-width: 780px;
        }
    }
</style>

@php
    $visibleProjectCount = method_exists($projects, 'total')
        ? $projects->total()
        : $projects->count();

    $activeProjectCount = $projects->getCollection()->filter(function ($project) {
        return !in_array(
            strtolower((string) $project->status),
            ['completed', 'cancelled'],
            true
        );
    })->count();

    $attentionProjectCount = $projects->getCollection()->filter(function ($project) {
        return in_array(
            $project->health['key'] ?? '',
            ['at-risk', 'critical'],
            true
        );
    })->count();
@endphp

<div class="projects-page">

    <div class="projects-header">

        <div>
            <div class="projects-eyebrow">
                <span></span>
                Project workspace
            </div>

            <h1>Projects</h1>

            <p class="projects-subtitle">
                Manage delivery, monitor project health, and keep your team focused on the work.
            </p>
        </div>

        @if(\App\ProjectAccess::canCreate(auth()->user()))
            <a
                class="new-project"
                href="{{ route('projects.create') }}"
            >
                <span class="new-project-plus">+</span>
                New project
            </a>
        @endif

    </div>


    <div class="projects-summary">

        <div class="summary-card">
            <span class="summary-label">Total projects</span>

            <strong class="summary-value">
                {{ $visibleProjectCount }}
            </strong>

            <span class="summary-text">
                Projects in your workspace
            </span>
        </div>


        <div class="summary-card">
            <span class="summary-label">Active projects</span>

            <strong class="summary-value">
                {{ $activeProjectCount }}
            </strong>

            <span class="summary-text">
                Currently being delivered
            </span>
        </div>


        <div class="summary-card">
            <span class="summary-label">Needs attention</span>

            <strong class="summary-value">
                {{ $attentionProjectCount }}
            </strong>

            <span class="summary-text">
                At-risk or critical projects
            </span>
        </div>

    </div>


    <section class="projects-card">

        <div class="projects-card-header">

            <div>
                <h2 class="projects-card-title">
                    Project portfolio
                </h2>

                <p class="projects-card-description">
                    Current status, health, progress, and delivery timeline.
                </p>
            </div>

            <span class="project-count">
                {{ $visibleProjectCount }}
                {{ $visibleProjectCount === 1 ? 'project' : 'projects' }}
            </span>

        </div>


        <div class="projects-table-wrap">

            <table class="projects-table">

                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Health</th>
                        <th>Progress</th>
                        <th>Timeline</th>
                        <th></th>
                    </tr>
                </thead>


                <tbody>

                    @forelse($projects as $project)

                        @php
                            $health = $project->health;

                            $statusClass = match (strtolower((string) $project->status)) {
                                'planning' => 'status-planning',
                                'in-progress', 'active' => 'status-in-progress',
                                'completed' => 'status-completed',
                                'on-hold', 'pending' => 'status-on-hold',
                                'cancelled' => 'status-cancelled',
                                default => 'status-default',
                            };

                            $healthClass = match ($health['key'] ?? '') {
                                'on-track' => 'health-on-track',
                                'at-risk' => 'health-at-risk',
                                'critical' => 'health-critical',
                                'completed' => 'health-completed',
                                default => 'status-default',
                            };

                            $progress = max(
                                0,
                                min(100, (float) ($project->progress ?? 0))
                            );
                        @endphp


                        <tr>

                            <td class="project-cell">

                                <div class="project-main">

                                    <span class="project-icon">
                                        {{ strtoupper(substr($project->name, 0, 1)) }}
                                    </span>

                                    <div class="project-info">

                                        <span class="project-name">
                                            {{ $project->name }}
                                        </span>

                                        <span class="project-description">
                                            {{ \Illuminate\Support\Str::limit($project->description, 55) ?: 'No project description' }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>
                                <span class="client-name">
                                    {{ $project->client ?: 'Internal project' }}
                                </span>
                            </td>


                            <td>

                                <span class="status-badge {{ $statusClass }}">
                                    {{ str_replace('-', ' ', $project->status) }}
                                </span>

                            </td>


                            <td>

                                <span
                                    class="health-badge {{ $healthClass }}"
                                    @if(!empty($health['reasons']))
                                        title="{{ implode('; ', $health['reasons']) }}"
                                    @endif
                                >
                                    {{ $health['label'] ?? 'Unknown' }}
                                </span>

                            </td>


                            <td class="progress-cell">

                                <div class="progress-line">

                                    <div
                                        class="progress-track"
                                        aria-label="Project progress"
                                    >
                                        <span
                                            class="progress-fill"
                                            style="width: {{ $progress }}%"
                                        ></span>
                                    </div>

                                    <span class="progress-number">
                                        {{ number_format($progress, 0) }}%
                                    </span>

                                </div>

                            </td>


                            <td>

                                <span class="timeline-date">
                                    {{ $project->start_date?->format('M j, Y') ?? 'Not scheduled' }}
                                </span>

                            </td>


                            <td>

                                <a
                                    class="open-project"
                                    href="{{ route('projects.show', $project) }}"
                                >
                                    Open →
                                </a>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="7" class="empty-state">

                                <span class="empty-icon">+</span>

                                <span class="empty-title">
                                    No projects yet
                                </span>

                                <span class="empty-text">
                                    Create your first project to start managing delivery.
                                </span>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($projects->hasPages())

            <div class="pagination-wrap">
                {{ $projects->links() }}
            </div>

        @endif

    </section>

</div>

@endsection
'@ | Set-Content -Path "resources\views\projects\index.blade.php" -Encoding UTF8
