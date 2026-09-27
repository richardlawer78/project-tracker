@extends('layouts.app')

@section('title', 'Investor Dashboard')

@section('content')
<div class="container-fluid py-4 px-3 px-lg-4">

    <div class="investor-hero mb-4">
        <div class="text-uppercase small fw-semibold text-primary mb-2" style="letter-spacing:.08em;">
            Investor Portal
        </div>
        <h1 class="mb-2 fw-bold">Welcome, {{ auth()->user()->name }}</h1>
        <p class="text-muted mb-0">
            Explore available projects, follow progress, and let our team know when you are interested in an opportunity.
        </p>
    </div>

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="investor-stat">
                <div class="investor-stat-icon">
                    <i class="ri-folder-3-line"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Projects</div>
                    <div class="investor-stat-value">{{ $projectCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="investor-stat">
                <div class="investor-stat-icon">
                    <i class="ri-line-chart-line"></i>
                </div>
                <div>
                    <div class="text-muted small">Active Projects</div>
                    <div class="investor-stat-value">{{ $activeProjectCount }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="investor-stat">
                <div class="investor-stat-icon">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <div>
                    <div class="text-muted small">Completed Projects</div>
                    <div class="investor-stat-value">{{ $completedProjectCount }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="mb-3">
        <h2 class="h4 fw-bold mb-1">All Projects</h2>
        <p class="text-muted small mb-0">
            View project status, progress and timeline.
        </p>
    </div>

    @if($projects->isEmpty())

        <div class="investor-empty">
            <i class="ri-folder-open-line"></i>
            <h3>No projects available</h3>
            <p>There are currently no projects available to view.</p>
        </div>

    @else

        <div class="investor-project-list">

            @foreach($projects as $project)

                @php
                    $progress = min(100, max(0, (int) $project->progress));
                    $status = $project->status ?? 'planning';

                    $statusLabel = str_replace('-', ' ', ucfirst($status));

                    $statusClass = match ($status) {
                        'completed' => 'status-completed',
                        'in-progress' => 'status-progress',
                        'on-hold' => 'status-hold',
                        default => 'status-planning',
                    };
                @endphp

                <div class="investor-project-row">

                    <div class="project-main">
                        <div class="project-icon">
                            <i class="ri-building-4-line"></i>
                        </div>

                        <div class="project-info">
                            <h3>{{ $project->name }}</h3>

                            <p>
                                {{ $project->description
                                    ? \Illuminate\Support\Str::limit($project->description, 150)
                                    : 'Project information and progress updates.' }}
                            </p>
                        </div>
                    </div>

                    <div class="project-status-column">
                        <span class="project-label">Status</span>
                        <span class="project-status {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="project-progress-column">
                        <div class="project-progress-heading">
                            <span class="project-label">Progress</span>
                            <strong>{{ $progress }}%</strong>
                        </div>

                        <div class="investor-progress">
                            <div
                                class="investor-progress-bar"
                                style="width: {{ $progress }}%;"
                            ></div>
                        </div>
                    </div>

                    <div class="project-timeline">
                        <span class="project-label">Timeline</span>

                        <div>
                            <i class="ri-calendar-line"></i>
                            {{ $project->start_date?->format('d M Y') ?? 'Not set' }}
                        </div>

                        <div>
                            <i class="ri-arrow-right-line"></i>
                            {{ $project->end_date?->format('d M Y') ?? 'Not set' }}
                        </div>
                    </div>

                    <div class="project-action">
                        <div class="d-flex flex-column gap-2"><a href="{{ route('investor.projects.show', $project) }}" class="btn btn-primary">View Project <i class="ri-arrow-right-line"></i></a><a href="{{ route('investor.projects.show', $project) }}#interest" class="btn btn-outline-primary"><i class="ri-hand-coin-line"></i> Express Interest</a></div>
                    </div>

                </div>

            @endforeach

        </div>

    @endif

</div>

<style>
    .investor-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f5f8ff 100%);
        border: 1px solid #e5eaf2;
        border-radius: 18px;
        padding: 28px 30px;
        box-shadow: 0 4px 18px rgba(20, 35, 70, .04);
    }

    .investor-hero h1 {
        color: #17233d;
        font-size: 2rem;
    }

    .investor-stat {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 16px;
        padding: 20px 22px;
        box-shadow: 0 4px 18px rgba(20, 35, 70, .04);
    }

    .investor-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef4ff;
        color: #2864d7;
        font-size: 21px;
        flex-shrink: 0;
    }

    .investor-stat-value {
        color: #17233d;
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .investor-project-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .investor-project-row {
        display: grid;
        grid-template-columns: minmax(0, 2.2fr) minmax(90px, .75fr) minmax(120px, 1.05fr) minmax(125px, .9fr) 110px;
        align-items: center;
        gap: 24px;
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 3px 14px rgba(20, 35, 70, .035);
        transition: box-shadow .18s ease, transform .18s ease;
    }

    .investor-project-row:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(20, 35, 70, .08);
    }

    .project-main {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .project-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #eef4ff;
        color: #2864d7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        flex-shrink: 0;
    }

    .project-info {
        min-width: 0;
    }

    .project-info h3 {
        color: #17233d;
        font-size: .98rem;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .project-info p {
        color: #7b879b;
        font-size: .78rem;
        line-height: 1.45;
        margin: 0;
    }

    .project-label {
        display: block;
        color: #9099a9;
        font-size: .68rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 7px;
    }

    .project-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-completed {
        background: #eaf8f0;
        color: #16834b;
    }

    .status-progress {
        background: #edf4ff;
        color: #2864d7;
    }

    .status-hold {
        background: #fff5df;
        color: #a96c00;
    }

    .status-planning {
        background: #f0f2f6;
        color: #586274;
    }

    .project-progress-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 7px;
    }

    .project-progress-heading .project-label {
        margin-bottom: 0;
    }

    .project-progress-heading strong {
        color: #2864d7;
        font-size: .8rem;
    }

    .investor-progress {
        width: 100%;
        height: 7px;
        background: #e9edf4;
        border-radius: 999px;
        overflow: hidden;
    }

    .investor-progress-bar {
        height: 100%;
        background: #2864d7;
        border-radius: inherit;
    }

    .project-timeline > div {
        color: #4f5b70;
        font-size: .76rem;
        margin-bottom: 5px;
        white-space: nowrap;
    }

    .project-timeline i {
        color: #8b95a6;
        margin-right: 4px;
    }

    .project-action {
        text-align: right;
    }

    .project-action .btn {
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .investor-empty {
        text-align: center;
        background: #fff;
        border: 1px solid #e5eaf2;
        border-radius: 18px;
        padding: 60px 20px;
    }

    .investor-empty > i {
        font-size: 38px;
        color: #2864d7;
    }

    .investor-empty h3 {
        margin: 14px 0 6px;
        font-size: 1.1rem;
    }

    .investor-empty p {
        color: #7b879b;
        margin: 0;
    }

    @media (max-width: 1200px) {
        .investor-project-row {
            grid-template-columns: minmax(240px, 2fr) 120px minmax(150px, 1.2fr);
        }

        .project-timeline,
        .project-action {
            grid-column: span 1;
        }
    }

    @media (max-width: 900px) {
        .investor-project-row {
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .project-main {
            grid-column: 1 / -1;
        }

        .project-action {
            text-align: left;
        }
    }

    @media (max-width: 575px) {
        .investor-hero {
            padding: 22px;
        }

        .investor-project-row {
            grid-template-columns: 1fr;
        }

        .project-main,
        .project-status-column,
        .project-progress-column,
        .project-timeline,
        .project-action {
            grid-column: auto;
        }
    }
</style>
@endsection