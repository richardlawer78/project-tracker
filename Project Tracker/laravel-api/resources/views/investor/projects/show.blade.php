@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="container-fluid py-4 px-3 px-lg-4">

    <div class="investor-detail-header mb-4">

        <a href="{{ route('investor.dashboard') }}" class="investor-back-link">
            <i class="ri-arrow-left-line"></i>
            Back to Investor Dashboard
        </a>

        <div class="mt-4">
            <div class="card p-4">
                <h3 class="mb-2">Interested in this project?</h3>
                <p class="text-muted">Send us your interest and our team will contact you.</p>

                <form method="POST" action="{{ route('projects.public.interest', $project) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="Tell us what you are interested in..."></textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="ri-hand-coin-line me-1"></i>
                        Express Interest
                    </button>
                </form>
            </div>
        </div>
        </a>

        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mt-3">
            <div>
                <div class="text-uppercase small fw-semibold text-primary mb-2" style="letter-spacing:.08em;">
                    Project Overview
                </div>

                <h1 class="investor-project-title">
                    {{ $project->name }}
                </h1>

                <p class="text-muted mb-0">
                    Project information, progress and milestones.
                </p>
            </div>

            @php
                $status = $project->status ?? 'planning';
                $statusLabel = str_replace('-', ' ', ucfirst($status));

                $statusClass = match ($status) {
                    'completed' => 'status-completed',
                    'in-progress' => 'status-progress',
                    'on-hold' => 'status-hold',
                    default => 'status-planning',
                };
            @endphp

            <span class="project-status {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>

    </div>

    <div class="row g-4">

        <div class="col-xl-8">

            <div class="investor-detail-card mb-4">
                <div class="detail-card-heading">
                    <div class="detail-icon">
                        <i class="ri-information-line"></i>
                    </div>

                    <div>
                        <h2>Project Overview</h2>
                        <p>General information about this project.</p>
                    </div>
                </div>

                @if($project->description)
                    <p class="project-description-large mb-0">
                        {{ $project->description }}
                    </p>
                @else
                    <p class="text-muted mb-0">
                        No project description is currently available.
                    </p>
                @endif
            </div>

            <div class="investor-detail-card">

                <div class="detail-card-heading">
                    <div class="detail-icon">
                        <i class="ri-flag-line"></i>
                    </div>

                    <div>
                        <h2>Milestones</h2>
                        <p>Key project milestones and their current status.</p>
                    </div>
                </div>

                @if($project->milestones->isEmpty())

                    <div class="empty-detail">
                        <i class="ri-calendar-close-line"></i>
                        <span>No milestones are currently available.</span>
                    </div>

                @else

                    <div class="milestone-list">

                        @foreach($project->milestones as $milestone)

                            @php
                                $milestoneStatus = $milestone->status ?? 'pending';
                                $milestoneLabel = str_replace('-', ' ', ucfirst($milestoneStatus));
                            @endphp

                            <div class="milestone-item">

                                <div class="milestone-marker">
                                    <i class="ri-check-line"></i>
                                </div>

                                <div class="milestone-content">
                                    <div class="d-flex flex-wrap justify-content-between gap-2">
                                        <h3>{{ $milestone->name }}</h3>

                                        <span class="milestone-status">
                                            {{ $milestoneLabel }}
                                        </span>
                                    </div>

                                    @if($milestone->due_date)
                                        <div class="milestone-date">
                                            <i class="ri-calendar-line"></i>
                                            Due {{ $milestone->due_date->format('d M Y') }}
                                        </div>
                                    @endif
                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

        <div class="col-xl-4">

            <div class="investor-detail-card mb-4">

                <div class="detail-card-heading">
                    <div class="detail-icon">
                        <i class="ri-line-chart-line"></i>
                    </div>

                    <div>
                        <h2>Project Progress</h2>
                        <p>Current overall progress.</p>
                    </div>
                </div>

                @php
                    $progress = min(100, max(0, (int) $project->progress));
                @endphp

                <div class="progress-summary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Overall progress</span>
                        <strong>{{ $progress }}%</strong>
                    </div>

                    <div class="investor-progress">
                        <div
                            class="investor-progress-bar"
                            style="width: {{ $progress }}%;"
                        ></div>
                    </div>
                </div>

            </div>

            <div class="investor-detail-card">

                <div class="detail-card-heading">
                    <div class="detail-icon">
                        <i class="ri-calendar-schedule-line"></i>
                    </div>

                    <div>
                        <h2>Timeline</h2>
                        <p>Project schedule.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="ri-calendar-line"></i>
                    </div>

                    <div>
                        <span>Start Date</span>
                        <strong>
                            {{ $project->start_date?->format('d M Y') ?? 'Not specified' }}
                        </strong>
                    </div>
                </div>

                <div class="timeline-divider"></div>

                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="ri-calendar-check-line"></i>
                    </div>

                    <div>
                        <span>End Date</span>
                        <strong>
                            {{ $project->end_date?->format('d M Y') ?? 'Not specified' }}
                        </strong>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<style>
    .investor-detail-header {
        background: linear-gradient(135deg, #ffffff 0%, #f5f8ff 100%);
        border: 1px solid #e5eaf2;
        border-radius: 18px;
        padding: 25px 28px;
        box-shadow: 0 4px 18px rgba(20, 35, 70, .04);
    }

    .investor-back-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #2864d7;
        text-decoration: none;
        font-size: .84rem;
        font-weight: 600;
    }

    .investor-back-link:hover {
        text-decoration: underline;
    }

    .investor-project-title {
        color: #17233d;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 7px;
    }

    .project-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: .74rem;
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

    .investor-detail-card {
        background: #ffffff;
        border: 1px solid #e5eaf2;
        border-radius: 17px;
        padding: 24px;
        box-shadow: 0 4px 18px rgba(20, 35, 70, .04);
    }

    .detail-card-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 22px;
    }

    .detail-icon {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #eef4ff;
        color: #2864d7;
        font-size: 19px;
    }

    .detail-card-heading h2 {
        color: #17233d;
        font-size: 1rem;
        font-weight: 700;
        margin: 2px 0 4px;
    }

    .detail-card-heading p {
        color: #8a94a6;
        font-size: .76rem;
        margin: 0;
    }

    .project-description-large {
        color: #4f5b70;
        font-size: .9rem;
        line-height: 1.7;
    }

    .investor-progress {
        height: 9px;
        background: #e9edf4;
        border-radius: 999px;
        overflow: hidden;
    }

    .investor-progress-bar {
        height: 100%;
        background: #2864d7;
        border-radius: inherit;
    }

    .progress-summary > div:first-child {
        color: #687388;
        font-size: .82rem;
    }

    .progress-summary strong {
        color: #2864d7;
        font-size: 1rem;
    }

    .milestone-list {
        display: flex;
        flex-direction: column;
    }

    .milestone-item {
        display: flex;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #edf0f5;
    }

    .milestone-item:first-child {
        padding-top: 0;
    }

    .milestone-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .milestone-marker {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #eef4ff;
        color: #2864d7;
    }

    .milestone-content {
        flex: 1;
        min-width: 0;
    }

    .milestone-content h3 {
        color: #29344d;
        font-size: .86rem;
        font-weight: 650;
        margin: 3px 0 5px;
    }

    .milestone-status {
        background: #f1f3f7;
        color: #667084;
        border-radius: 999px;
        padding: 5px 9px;
        font-size: .68rem;
        font-weight: 600;
    }

    .milestone-date {
        color: #8a94a6;
        font-size: .73rem;
    }

    .milestone-date i {
        margin-right: 4px;
    }

    .empty-detail {
        display: flex;
        align-items: center;
        gap: 9px;
        color: #8a94a6;
        font-size: .82rem;
        padding: 8px 0;
    }

    .empty-detail i {
        font-size: 18px;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .timeline-icon {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f3f6fb;
        color: #66758d;
    }

    .timeline-item span,
    .timeline-item strong {
        display: block;
    }

    .timeline-item span {
        color: #8a94a6;
        font-size: .7rem;
        margin-bottom: 3px;
    }

    .timeline-item strong {
        color: #29344d;
        font-size: .82rem;
    }

    .timeline-divider {
        width: 1px;
        height: 24px;
        background: #dfe4ec;
        margin: 5px 0 5px 19px;
    }

    @media (max-width: 575px) {
        .investor-detail-header {
            padding: 20px;
        }

        .investor-detail-card {
            padding: 19px;
        }

        .investor-project-title {
            font-size: 1.55rem;
        }
    }
</style>
@endsection