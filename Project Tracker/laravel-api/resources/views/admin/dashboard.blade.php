@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<style>
    .admin-dashboard {
        max-width: 1500px;
        margin: 0 auto;
    }

    .admin-hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        padding: 32px;
        margin-bottom: 26px;
        color: white;
        background:
            radial-gradient(circle at 90% 10%, rgba(255,255,255,.16), transparent 30%),
            linear-gradient(135deg, #111827 0%, #1f2937 55%, #111827 100%);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
    }

    .admin-hero::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -70px;
        bottom: -110px;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.12);
    }

    .admin-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 25px;
    }

    .admin-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.12);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 14px;
    }

    .admin-label span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #34d399;
        box-shadow: 0 0 0 4px rgba(52,211,153,.15);
    }

    .admin-hero h1 {
        margin: 0 0 8px;
        font-size: 32px;
        line-height: 1.15;
        letter-spacing: -.03em;
    }

    .admin-hero p {
        margin: 0;
        max-width: 650px;
        color: rgba(255,255,255,.7);
        font-size: 14px;
    }

    .admin-hero-actions {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    .admin-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 16px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        transition: .2s ease;
    }

    .admin-btn-primary {
        background: white;
        color: #111827;
    }

    .admin-btn-secondary {
        background: rgba(255,255,255,.08);
        color: white;
        border: 1px solid rgba(255,255,255,.12);
    }

    .admin-btn:hover {
        transform: translateY(-1px);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 26px;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 25px rgba(15,23,42,.05);
    }

    .stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: #f3f4f6;
        font-size: 20px;
    }

    .stat-title {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        font-weight: 600;
    }

    .stat-value {
        margin: 4px 0 0;
        color: #111827;
        font-size: 29px;
        font-weight: 800;
        letter-spacing: -.03em;
    }

    .stat-description {
        margin-top: 8px;
        color: #9ca3af;
        font-size: 12px;
    }

    .admin-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(300px, .8fr);
        gap: 22px;
        margin-bottom: 22px;
    }

    .admin-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(15,23,42,.05);
        overflow: hidden;
    }

    .card-header {
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
    }

    .card-header h2 {
        margin: 0;
        font-size: 16px;
        color: #111827;
    }

    .card-header p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #9ca3af;
    }

    .card-link {
        color: #4f46e5;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
    }

    .table-wrap {
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-table th {
        padding: 13px 22px;
        text-align: left;
        color: #9ca3af;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .07em;
        font-weight: 700;
        background: #fafafa;
    }

    .admin-table td {
        padding: 15px 22px;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        background: #eef2ff;
        color: #4338ca;
        font-size: 12px;
        font-weight: 800;
    }

    .user-name {
        font-weight: 700;
        color: #111827;
    }

    .user-email {
        margin-top: 2px;
        color: #9ca3af;
        font-size: 11px;
    }

    .role-badge,
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        background: #f3f4f6;
        color: #4b5563;
        text-transform: capitalize;
    }

    .overview-list {
        padding: 8px 20px 18px;
    }

    .overview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 2px;
        border-bottom: 1px solid #f1f5f9;
    }

    .overview-item:last-child {
        border-bottom: 0;
    }

    .overview-left {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .overview-icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        background: #f8fafc;
        color: #475569;
    }

    .overview-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .overview-value {
        font-size: 16px;
        font-weight: 800;
        color: #111827;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        padding: 20px;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 13px;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        color: #374151;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: .2s ease;
    }

    .quick-action:hover {
        border-color: #c7d2fe;
        background: #f8faff;
        transform: translateY(-1px);
    }

    .quick-action-icon {
        width: 30px;
        height: 30px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        background: #f3f4f6;
    }

    .project-progress {
        min-width: 120px;
    }

    .progress-bar {
        height: 6px;
        width: 100%;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
    }

    .progress-value {
        height: 100%;
        border-radius: inherit;
        background: #4f46e5;
    }

    .progress-text {
        margin-top: 5px;
        font-size: 10px;
        color: #9ca3af;
    }

    .empty-state {
        padding: 35px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }

    @media (max-width: 1100px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .admin-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .admin-hero {
            padding: 24px;
        }

        .admin-hero-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .admin-hero h1 {
            font-size: 25px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard">

```
{{-- HERO --}}
<section class="admin-hero">

    <div class="admin-hero-content">

        <div>
            <div class="admin-label">
                <span></span>
                Administrator Access
            </div>

            <h1>Admin Dashboard</h1>

            <p>
                Manage your Project Tracker workspace, users, projects,
                tasks and system activity from one place.
            </p>
        </div>

        <div class="admin-hero-actions">

            <a href="{{ route('dashboard') }}"
               class="admin-btn admin-btn-secondary">
                ← Workspace
            </a>

            <a href="{{ route('projects.index') }}"
               class="admin-btn admin-btn-primary">
                View Projects →
            </a>

        </div>

    </div>

</section>

{{-- STATISTICS --}}
<section class="stats-grid">

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <p class="stat-title">Total Users</p>
                <div class="stat-value">{{ $userCount }}</div>
            </div>

            <div class="stat-icon">
                👥
            </div>
        </div>

        <div class="stat-description">
            {{ $adminCount }} administrator account
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <p class="stat-title">Total Projects</p>
                <div class="stat-value">{{ $projectCount }}</div>
            </div>

            <div class="stat-icon">
                📁
            </div>
        </div>

        <div class="stat-description">
            {{ $activeProjectCount }} currently active
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <p class="stat-title">Completed Projects</p>
                <div class="stat-value">{{ $completedProjectCount }}</div>
            </div>

            <div class="stat-icon">
                ✓
            </div>
        </div>

        <div class="stat-description">
            Projects successfully completed
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div>
                <p class="stat-title">Completed Tasks</p>
                <div class="stat-value">{{ $completedTaskCount }}</div>
            </div>

            <div class="stat-icon">
                ✓
            </div>
        </div>

        <div class="stat-description">
            Out of {{ $taskCount }} total tasks
        </div>
    </div>

</section>

{{-- USERS + SYSTEM OVERVIEW --}}
<section class="admin-grid">

    <div class="admin-card">

        <div class="card-header">
            <div>
                <h2>Recent Users</h2>
                <p>Latest accounts added to the workspace</p>
            </div>

            <span class="card-link">
                {{ $userCount }} users
            </span>
        </div>

        <div class="table-wrap">

            @if($recentUsers->count())

                <table class="admin-table">

                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($recentUsers as $user)

                        @php
                            $initials = collect(
                                explode(' ', trim($user->name))
                            )
                            ->filter()
                            ->map(
                                fn ($part) =>
                                    strtoupper(substr($part, 0, 1))
                            )
                            ->take(2)
                            ->implode('');
                        @endphp

                        <tr>

                            <td>
                                <div class="user-cell">

                                    <div class="user-avatar">
                                        {{ $initials ?: 'U' }}
                                    </div>

                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                        </div>

                                        <div class="user-email">
                                            {{ $user->email }}
                                        </div>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <span class="role-badge">
                                    {{ str_replace('-', ' ', $user->role ?? 'member') }}
                                </span>
                            </td>

                            <td>
                                {{ $user->created_at?->format('d M Y') }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            @else

                <div class="empty-state">
                    No users found.
                </div>

            @endif

        </div>

    </div>

    <div class="admin-card">

        <div class="card-header">
            <div>
                <h2>System Overview</h2>
                <p>Current workspace activity</p>
            </div>
        </div>

        <div class="overview-list">

            <div class="overview-item">
                <div class="overview-left">
                    <div class="overview-icon">📋</div>
                    <span class="overview-label">Total Tasks</span>
                </div>

                <strong class="overview-value">
                    {{ $taskCount }}
                </strong>
            </div>

            <div class="overview-item">
                <div class="overview-left">
                    <div class="overview-icon">⚡</div>
                    <span class="overview-label">Active Sprints</span>
                </div>

                <strong class="overview-value">
                    {{ $activeSprintCount }}
                </strong>
            </div>

            <div class="overview-item">
                <div class="overview-left">
                    <div class="overview-icon">⚠</div>
                    <span class="overview-label">Open Risks</span>
                </div>

                <strong class="overview-value">
                    {{ $openRiskCount }}
                </strong>
            </div>

            <div class="overview-item">
                <div class="overview-left">
                    <div class="overview-icon">🚀</div>
                    <span class="overview-label">Active Projects</span>
                </div>

                <strong class="overview-value">
                    {{ $activeProjectCount }}
                </strong>
            </div>

        </div>

    </div>

</section>

{{-- RECENT PROJECTS --}}
<section class="admin-card" style="margin-bottom:22px;">

    <div class="card-header">

        <div>
            <h2>Recent Projects</h2>
            <p>Latest projects in your workspace</p>
        </div>

        <a href="{{ route('projects.index') }}"
           class="card-link">
            View all →
        </a>

    </div>

    <div class="table-wrap">

        @if($recentProjects->count())

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Progress</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($recentProjects as $project)

                    <tr>

                        <td>
                            <strong>
                                {{ $project->name }}
                            </strong>
                        </td>

                        <td>
                            <span class="status-badge">
                                {{ str_replace('-', ' ', $project->status ?? 'planning') }}
                            </span>
                        </td>

                        <td>
                            <span class="status-badge">
                                {{ $project->priority ?? 'medium' }}
                            </span>
                        </td>

                        <td>

                            <div class="project-progress">

                                <div class="progress-bar">
                                    <div class="progress-value"
                                         style="width: {{ min(100, max(0, (int) ($project->progress ?? 0))) }}%;">
                                    </div>
                                </div>

                                <div class="progress-text">
                                    {{ (int) ($project->progress ?? 0) }}% complete
                                </div>

                            </div>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @else

            <div class="empty-state">
                No projects found.
            </div>

        @endif

    </div>

</section>

{{-- QUICK ACTIONS --}}
<section class="admin-card">

    <div class="card-header">

        <div>
            <h2>Quick Actions</h2>
            <p>Common administrator tasks</p>
        </div>

    </div>

    <div class="quick-actions">

        <a href="{{ route('projects.create') }}"
           class="quick-action">

            <span class="quick-action-icon">＋</span>

            Create Project

        </a>

        <a href="{{ route('projects.index') }}"
           class="quick-action">

            <span class="quick-action-icon">📁</span>

            Manage Projects

        </a>

        <a href="{{ route('web.tasks.index') }}"
           class="quick-action">

            <span class="quick-action-icon">✓</span>

            Manage Tasks

        </a>

        <a href="{{ route('analytics') }}"
           class="quick-action">

            <span class="quick-action-icon">📊</span>

            View Analytics

        </a>

    </div>

</section>
```

</div>

@endsection
