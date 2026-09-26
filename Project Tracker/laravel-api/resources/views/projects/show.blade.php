@extends('layouts.app')

@section('title', $project->name)

@section('content')

@php
    use Illuminate\Support\Str;

    /*
    |--------------------------------------------------------------------------
    | PROJECT DATA
    |--------------------------------------------------------------------------
    */

    $progress = max(0, min(100, (float) ($project->progress ?? 0)));

    $status = strtolower((string) ($project->status ?? 'planning'));
    $statusSlug = Str::slug($status);

    $priority = strtolower((string) ($project->priority ?? 'medium'));
    $prioritySlug = Str::slug($priority);

    $health = $project->health ?? [
        'key' => 'on-track',
        'label' => 'On Track',
        'reasons' => [],
    ];

    $healthKey = $health['key'] ?? 'on-track';
    $healthLabel = $health['label'] ?? 'On Track';
    $healthReasons = $health['reasons'] ?? [];

    $startDate = $project->start_date
        ? $project->start_date->copy()
        : null;

    $endDate = $project->end_date
        ? $project->end_date->copy()
        : null;

    $daysLeft = $endDate
        ? (int) now()->startOfDay()->diffInDays(
            $endDate->copy()->startOfDay(),
            false
        )
        : null;

    $timelineProgress = 0;

    if ($startDate && $endDate && $endDate->greaterThan($startDate)) {
        $totalDays = $startDate->diffInDays($endDate);
        $elapsedDays = max(
            0,
            min(
                $totalDays,
                $startDate->diffInDays(now()->startOfDay())
            )
        );

        $timelineProgress = $totalDays > 0
            ? (int) round(($elapsedDays / $totalDays) * 100)
            : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    $user = auth()->user();

    $canManageProject = $user && (
        $user->role === 'admin' ||
        $user->role === 'project-manager' ||
        (int) $project->owner_id === (int) $user->id
    );

    $canManageTeam = $canManageProject;

    /*
    |--------------------------------------------------------------------------
    | INTERNAL PROJECT DATA
    |--------------------------------------------------------------------------
    */

    $members = collect();

    if ($canManageTeam) {
        $members = $project->members()
            ->orderBy('name')
            ->get();
    }

    $availableUsers = collect();

    if ($canManageTeam) {
        $availableUsers = \App\Models\User::query()
            ->whereNotIn('id', $members->pluck('id'))
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | COUNTS
    |--------------------------------------------------------------------------
    */

    $taskCount = (int) ($project->tasks_count ?? 0);
    $completedTaskCount = (int) ($project->completed_tasks_count ?? 0);
    $overdueTaskCount = (int) ($project->overdue_tasks_count ?? 0);

    $milestoneCount = (int) ($project->milestones_count ?? 0);
    $upcomingMilestoneCount = (int) ($project->upcoming_milestones_count ?? 0);

    /*
    |--------------------------------------------------------------------------
    | MILESTONES
    |--------------------------------------------------------------------------
    */

    $milestones = collect();

    try {
        if ($project->relationLoaded('milestones')) {
            $milestones = $project->milestones
                ->sortBy(function ($milestone) {
                    return $milestone->due_date
                        ? $milestone->due_date->timestamp
                        : PHP_INT_MAX;
                })
                ->values();
        } else {
            $milestones = $project->milestones()
                ->orderByRaw('due_date IS NULL, due_date ASC')
                ->get();
        }
    } catch (\Throwable $e) {
        $milestones = collect();
    }

    /*
    |--------------------------------------------------------------------------
    | RECENT TASKS
    |--------------------------------------------------------------------------
    */

    $recentTasks = collect();

    if ($canManageProject) {
        try {
            $recentTasks = $project->tasks()
                ->latest()
                ->take(8)
                ->get();
        } catch (\Throwable $e) {
            $recentTasks = collect();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBJECTIVES / DELIVERABLES
    |--------------------------------------------------------------------------
    */

    $objectives = $project->phases ?? null;
    $deliverables = $project->deliverables ?? null;

    /*
    |--------------------------------------------------------------------------
    | STATUS TEXT
    |--------------------------------------------------------------------------
    */

    $statusLabel = Str::headline($status);
    $priorityLabel = Str::headline($priority);

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    $healthClass = match ($healthKey) {
        'critical' => 'health-critical',
        'at-risk' => 'health-risk',
        'completed' => 'health-completed',
        default => 'health-track',
    };

    $statusClass = match ($statusSlug) {
        'completed' => 'status-completed',
        'in-progress', 'active' => 'status-active',
        'on-hold', 'pending' => 'status-hold',
        'cancelled' => 'status-cancelled',
        default => 'status-planning',
    };

@endphp


<style>
/* ==========================================================================
   PROJECT DETAILS
   ========================================================================== */

.project-detail-page {
    --pd-text: #182238;
    --pd-muted: #68758f;
    --pd-line: rgba(24, 34, 56, .09);
    --pd-card: rgba(255, 255, 255, .72);
    --pd-card-strong: rgba(255, 255, 255, .88);
    --pd-border: rgba(255, 255, 255, .78);
    --pd-accent: #6366f1;
    --pd-accent-2: #22d3ee;
    --pd-track: rgba(24, 34, 56, .09);
    --pd-shadow: 0 18px 50px rgba(31, 41, 75, .10);

    position: relative;
    isolation: isolate;
    color: var(--pd-text);
    padding-bottom: 40px;
}

body.dark .project-detail-page {
    --pd-text: #edf2ff;
    --pd-muted: #9aa8c5;
    --pd-line: rgba(255, 255, 255, .10);
    --pd-card: rgba(255, 255, 255, .055);
    --pd-card-strong: rgba(255, 255, 255, .085);
    --pd-border: rgba(255, 255, 255, .12);
    --pd-track: rgba(255, 255, 255, .10);
    --pd-shadow: 0 20px 55px rgba(0, 0, 0, .30);
}

/* Background glow */

.project-detail-page::before {
    content: "";
    position: absolute;
    inset: -40px;
    z-index: -1;
    pointer-events: none;

    background:
        radial-gradient(
            520px 360px at 5% 5%,
            rgba(99, 102, 241, .22),
            transparent 70%
        ),
        radial-gradient(
            500px 360px at 95% 15%,
            rgba(34, 211, 238, .18),
            transparent 70%
        ),
        radial-gradient(
            520px 400px at 70% 90%,
            rgba(168, 85, 247, .14),
            transparent 70%
        );
}

/* ==========================================================================
   GENERAL
   ========================================================================== */

.pd-container {
    width: 100%;
    max-width: 1450px;
    margin: 0 auto;
}

.pd-card {
    background:
        linear-gradient(
            145deg,
            var(--pd-card-strong),
            var(--pd-card)
        );

    border: 1px solid var(--pd-border);
    border-radius: 22px;

    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);

    box-shadow:
        var(--pd-shadow),
        inset 0 1px 0 rgba(255,255,255,.45);
}

body.dark .pd-card {
    box-shadow:
        var(--pd-shadow),
        inset 0 1px 0 rgba(255,255,255,.08);
}

.pd-card-inner {
    padding: 26px;
}

.pd-eyebrow {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 10px;

    color: var(--pd-accent);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.pd-eyebrow-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
    box-shadow: 0 0 10px currentColor;
}

.pd-muted {
    color: var(--pd-muted);
}

/* ==========================================================================
   HEADER
   ========================================================================== */

.pd-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 24px;

    margin-bottom: 24px;
}

.pd-header-main {
    min-width: 0;
}

.pd-header h1 {
    margin: 0;

    color: var(--pd-text);

    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.08;
    font-weight: 850;
    letter-spacing: -.045em;
}

.pd-header-description {
    max-width: 800px;
    margin: 13px 0 0;

    color: var(--pd-muted);

    font-size: 15px;
    line-height: 1.7;
}

.pd-header-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;

    margin-top: 16px;
}

.pd-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 9px;
    flex: 0 0 auto;
}

.pd-actions form {
    margin: 0;
}

/* ==========================================================================
   BUTTONS
   ========================================================================== */

.pd-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    min-height: 40px;
    padding: 0 15px;

    border: 1px solid var(--pd-border);
    border-radius: 11px;

    background: var(--pd-card-strong);
    color: var(--pd-text);

    font: inherit;
    font-size: 13px;
    font-weight: 750;

    text-decoration: none;
    cursor: pointer;

    transition:
        transform .18s ease,
        box-shadow .18s ease,
        background .18s ease;
}

.pd-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 22px rgba(99,102,241,.15);
    color: var(--pd-text);
}

.pd-btn-primary {
    color: white;
    border-color: transparent;

    background:
        linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
        );

    box-shadow: 0 9px 24px rgba(99,102,241,.28);
}

.pd-btn-primary:hover {
    color: white;
    box-shadow: 0 12px 30px rgba(99,102,241,.38);
}

.pd-btn-danger {
    color: #be123c;
    border-color: rgba(225,29,72,.22);
    background: rgba(225,29,72,.07);
}

.pd-btn-danger:hover {
    color: white;
    background: #e11d48;
}

/* ==========================================================================
   BADGES
   ========================================================================== */

.pd-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    min-height: 28px;
    padding: 0 10px;

    border: 1px solid transparent;
    border-radius: 999px;

    font-size: 11px;
    font-weight: 800;
    text-transform: capitalize;
}

.pd-badge::before {
    content: "";

    width: 6px;
    height: 6px;

    border-radius: 50%;
    background: currentColor;

    box-shadow: 0 0 8px currentColor;
}

.status-planning {
    color: #b45309;
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.22);
}

.status-active {
    color: #1d4ed8;
    background: rgba(59,130,246,.12);
    border-color: rgba(59,130,246,.22);
}

.status-completed {
    color: #047857;
    background: rgba(16,185,129,.12);
    border-color: rgba(16,185,129,.22);
}

.status-hold {
    color: #b45309;
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.22);
}

.status-cancelled {
    color: #be123c;
    background: rgba(244,63,94,.12);
    border-color: rgba(244,63,94,.22);
}

.health-track {
    color: #047857;
}

.health-risk {
    color: #b45309;
}

.health-critical {
    color: #be123c;
}

.health-completed {
    color: #047857;
}

.pd-health-badge {
    background: rgba(16,185,129,.10);
    border-color: rgba(16,185,129,.20);
}

.health-risk .pd-health-badge {
    background: rgba(245,158,11,.10);
    border-color: rgba(245,158,11,.20);
}

.health-critical .pd-health-badge {
    background: rgba(244,63,94,.10);
    border-color: rgba(244,63,94,.20);
}

/* ==========================================================================
   HERO PROGRESS
   ========================================================================== */

.pd-progress-hero {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 28px;
    align-items: center;

    margin-bottom: 20px;
}

.pd-progress-title {
    margin: 0;

    font-size: 16px;
    font-weight: 800;
    color: var(--pd-text);
}

.pd-progress-subtitle {
    margin: 6px 0 18px;

    color: var(--pd-muted);
    font-size: 13px;
}

.pd-progress-bar {
    height: 12px;
    overflow: hidden;

    border-radius: 999px;
    background: var(--pd-track);
}

.pd-progress-bar i {
    display: block;
    height: 100%;

    width: {{ $progress }}%;

    border-radius: inherit;

    background:
        linear-gradient(
            90deg,
            #6366f1,
            #8b5cf6,
            #22d3ee
        );

    box-shadow:
        0 0 15px rgba(34,211,238,.35);

    transition: width .4s ease;
}

.pd-progress-labels {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-top: 8px;

    color: var(--pd-muted);
    font-size: 12px;
}

.pd-progress-percent {
    color: var(--pd-text);
    font-size: 42px;
    line-height: 1;
    font-weight: 850;
    letter-spacing: -.05em;
}

/* ==========================================================================
   STATS
   ========================================================================== */

.pd-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;

    margin-bottom: 20px;
}

.pd-stat {
    padding: 20px;
}

.pd-stat-label {
    display: block;

    color: var(--pd-muted);

    font-size: 11px;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
}

.pd-stat-value {
    display: block;

    margin-top: 9px;

    color: var(--pd-text);

    font-size: 24px;
    font-weight: 850;
    letter-spacing: -.03em;
}

.pd-stat-help {
    display: block;

    margin-top: 4px;

    color: var(--pd-muted);
    font-size: 11px;
}

/* ==========================================================================
   MAIN GRID
   ========================================================================== */

.pd-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(320px, .85fr);
    gap: 20px;
    align-items: start;
}

.pd-column {
    display: flex;
    flex-direction: column;
    gap: 20px;

    min-width: 0;
}

.pd-card-title {
    margin: 0;

    color: var(--pd-text);

    font-size: 18px;
    font-weight: 850;
    letter-spacing: -.02em;
}

.pd-card-description {
    margin: 5px 0 0;

    color: var(--pd-muted);
    font-size: 12px;
    line-height: 1.6;
}

/* ==========================================================================
   OVERVIEW
   ========================================================================== */

.pd-overview {
    margin: 16px 0 0;

    color: var(--pd-muted);

    font-size: 14px;
    line-height: 1.8;
    white-space: pre-line;
}

.pd-section-divider {
    height: 1px;
    margin: 22px 0;

    background: var(--pd-line);
}

/* ==========================================================================
   PROJECT DETAILS
   ========================================================================== */

.pd-details {
    display: grid;
    grid-template-columns: 125px minmax(0, 1fr);
}

.pd-details dt,
.pd-details dd {
    margin: 0;
    padding: 12px 0;

    border-top: 1px solid var(--pd-line);
}

.pd-details dt {
    color: var(--pd-muted);
    font-size: 12px;
}

.pd-details dd {
    color: var(--pd-text);
    font-size: 13px;
    font-weight: 700;
}

/* ==========================================================================
   TIMELINE
   ========================================================================== */

.pd-timeline {
    margin-top: 20px;
}

.pd-timeline-track {
    position: relative;

    height: 8px;

    border-radius: 999px;
    background: var(--pd-track);
}

.pd-timeline-track i {
    display: block;

    width: {{ $timelineProgress }}%;
    height: 100%;

    border-radius: inherit;

    background:
        linear-gradient(
            90deg,
            #6366f1,
            #22d3ee
        );
}

.pd-timeline-labels {
    display: flex;
    justify-content: space-between;
    gap: 15px;

    margin-top: 9px;

    color: var(--pd-muted);
    font-size: 11px;
}

.pd-timeline-labels strong {
    color: var(--pd-text);
}

/* ==========================================================================
   MILESTONES
   ========================================================================== */

.pd-milestones {
    display: flex;
    flex-direction: column;
    gap: 0;

    margin-top: 16px;
}

.pd-milestone {
    display: grid;
    grid-template-columns: 16px minmax(0, 1fr) auto;
    gap: 13px;

    padding: 14px 0;

    border-top: 1px solid var(--pd-line);
}

.pd-milestone:first-child {
    border-top: 0;
    padding-top: 4px;
}

.pd-milestone-dot {
    width: 10px;
    height: 10px;
    margin-top: 5px;

    border-radius: 50%;

    background: var(--pd-accent);
    box-shadow: 0 0 12px rgba(99,102,241,.45);
}

.pd-milestone.is-complete .pd-milestone-dot {
    background: #10b981;
    box-shadow: 0 0 12px rgba(16,185,129,.4);
}

.pd-milestone-name {
    color: var(--pd-text);
    font-size: 13px;
    font-weight: 800;
}

.pd-milestone-description {
    margin-top: 4px;

    color: var(--pd-muted);
    font-size: 12px;
    line-height: 1.5;
}

.pd-milestone-date {
    color: var(--pd-muted);
    font-size: 11px;
    white-space: nowrap;
}

.pd-empty {
    padding: 22px 0;

    color: var(--pd-muted);
    font-size: 13px;
}

/* ==========================================================================
   OBJECTIVES / DELIVERABLES
   ========================================================================== */

.pd-text-block {
    margin-top: 15px;

    color: var(--pd-muted);

    font-size: 13px;
    line-height: 1.75;
    white-space: pre-line;
}

.pd-list {
    display: flex;
    flex-direction: column;
    gap: 9px;

    margin: 15px 0 0;
    padding: 0;

    list-style: none;
}

.pd-list li {
    position: relative;

    padding-left: 20px;

    color: var(--pd-muted);

    font-size: 13px;
    line-height: 1.6;
}

.pd-list li::before {
    content: "";

    position: absolute;
    left: 1px;
    top: .65em;

    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: var(--pd-accent);
}

/* ==========================================================================
   HEALTH
   ========================================================================== */

.pd-health-panel {
    display: flex;
    align-items: flex-start;
    gap: 12px;

    margin-top: 16px;
    padding: 15px;

    border: 1px solid var(--pd-line);
    border-radius: 15px;

    background: rgba(255,255,255,.18);
}

body.dark .pd-health-panel {
    background: rgba(255,255,255,.025);
}

.pd-health-dot {
    flex: 0 0 auto;

    width: 10px;
    height: 10px;
    margin-top: 5px;

    border-radius: 50%;

    background: currentColor;
    box-shadow: 0 0 13px currentColor;
}

.pd-health-title {
    color: var(--pd-text);
    font-size: 14px;
    font-weight: 850;
}

.pd-health-reasons {
    margin: 5px 0 0;
    padding: 0;

    color: var(--pd-muted);

    font-size: 12px;
    line-height: 1.55;

    list-style: none;
}

.pd-health-reasons li {
    margin-top: 3px;
}

/* ==========================================================================
   INTERNAL WORKSPACE
   ========================================================================== */

.pd-internal {
    border-color: rgba(99,102,241,.18);
}

.pd-internal-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    margin-bottom: 15px;
    padding: 6px 9px;

    border-radius: 999px;

    color: #6366f1;
    background: rgba(99,102,241,.08);

    font-size: 10px;
    font-weight: 850;
    letter-spacing: .05em;
    text-transform: uppercase;
}

.pd-task-list {
    display: flex;
    flex-direction: column;
}

.pd-task {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    gap: 14px;

    padding: 13px 0;

    border-top: 1px solid var(--pd-line);
}

.pd-task:first-child {
    border-top: 0;
}

.pd-task-title {
    color: var(--pd-text);
    font-size: 13px;
    font-weight: 750;
}

.pd-task-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;

    margin-top: 4px;

    color: var(--pd-muted);
    font-size: 11px;
}

/* ==========================================================================
   TEAM
   ========================================================================== */

.pd-team-list {
    display: flex;
    flex-direction: column;
    gap: 10px;

    margin-top: 15px;
}

.pd-team-member {
    display: flex;
    align-items: center;
    gap: 11px;

    padding: 10px;

    border: 1px solid var(--pd-line);
    border-radius: 13px;
}

.pd-avatar {
    display: grid;
    place-items: center;

    width: 34px;
    height: 34px;
    flex: 0 0 34px;

    border-radius: 50%;

    color: white;
    background:
        linear-gradient(
            135deg,
            #6366f1,
            #8b5cf6
        );

    font-size: 12px;
    font-weight: 850;
}

.pd-member-info {
    min-width: 0;
    flex: 1;
}

.pd-member-name {
    color: var(--pd-text);
    font-size: 12px;
    font-weight: 800;
}

.pd-member-role {
    margin-top: 2px;

    color: var(--pd-muted);
    font-size: 10px;
}

.pd-member-remove {
    color: #be123c;
    background: transparent;
    border: 0;

    font: inherit;
    font-size: 11px;
    font-weight: 700;

    cursor: pointer;
}

/* ==========================================================================
   STAKEHOLDER CTA
   ========================================================================== */

.pd-interest {
    position: relative;
    overflow: hidden;

    padding: 28px;

    background:
        linear-gradient(
            135deg,
            rgba(99,102,241,.95),
            rgba(124,58,237,.94)
        );

    color: white;
}

.pd-interest::after {
    content: "";

    position: absolute;

    width: 220px;
    height: 220px;

    right: -90px;
    top: -100px;

    border-radius: 50%;

    background: rgba(255,255,255,.09);
}

.pd-interest h2 {
    position: relative;
    z-index: 1;

    margin: 0;

    color: white;

    font-size: 21px;
    font-weight: 850;
}

.pd-interest p {
    position: relative;
    z-index: 1;

    max-width: 620px;

    margin: 8px 0 20px;

    color: rgba(255,255,255,.78);

    font-size: 13px;
    line-height: 1.65;
}

.pd-interest .pd-btn {
    position: relative;
    z-index: 1;

    color: #312e81;
    background: white;
    border-color: white;
}

/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 1100px) {
    .pd-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .pd-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .pd-header {
        flex-direction: column;
    }

    .pd-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .pd-progress-hero {
        grid-template-columns: 1fr;
    }

    .pd-progress-percent {
        font-size: 34px;
    }
}

@media (max-width: 560px) {
    .pd-stats {
        grid-template-columns: 1fr;
    }

    .pd-card-inner {
        padding: 20px;
    }

    .pd-header h1 {
        font-size: 30px;
    }

    .pd-details {
        grid-template-columns: 100px minmax(0, 1fr);
    }

    .pd-milestone {
        grid-template-columns: 14px minmax(0, 1fr);
    }

    .pd-milestone-date {
        grid-column: 2;
    }

    .pd-actions > * {
        flex: 1;
    }
}
</style>


<div class="project-detail-page">

    <div class="pd-container">

        {{-- ================================================================
             HEADER
             ================================================================ --}}

        <header class="pd-header">

            <div class="pd-header-main">

                <div class="pd-eyebrow">
                    <span class="pd-eyebrow-dot"></span>
                    Project overview
                </div>

                <h1>
                    {{ $project->name }}
                </h1>

                @if($project->description)
                    <p class="pd-header-description">
                        {{ $project->description }}
                    </p>
                @endif

                <div class="pd-header-meta">

                    <span class="pd-badge {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>

                    <span class="pd-badge {{ $healthClass }} pd-health-badge">
                        {{ $healthLabel }}
                    </span>

                    @if($project->client)
                        <span class="pd-muted">
                            {{ $project->client }}
                        </span>
                    @endif

                </div>

            </div>


            {{-- INTERNAL MANAGEMENT ACTIONS --}}

            @if($canManageProject)

                <div class="pd-actions">

                    <a
                        href="{{ route('projects.index') }}"
                        class="pd-btn"
                    >
                        Projects
                    </a>

                    <a
                        href="{{ route('projects.report', $project) }}"
                        class="pd-btn"
                    >
                        Report
                    </a>

                    <a
                        href="{{ route('projects.edit', $project) }}"
                        class="pd-btn pd-btn-primary"
                    >
                        Edit project
                    </a>

                    <form
                        method="POST"
                        action="{{ route('projects.destroy', $project) }}"
                        onsubmit="return confirm('Delete this project? This action cannot be undone.')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="pd-btn pd-btn-danger"
                        >
                            Delete
                        </button>
                    </form>

                </div>

            @else

                <div class="pd-actions">

                    <a
                        href="{{ route('projects.index') }}"
                        class="pd-btn"
                    >
                        ΓåÉ Projects
                    </a>

                    <a
                        href="#express-interest"
                        class="pd-btn pd-btn-primary"
                    >
                        Express interest
                    </a>

                </div>

            @endif

        </header>


        {{-- ================================================================
             REAL PROJECT PROGRESS
             ================================================================ --}}

        <section class="pd-card pd-card-inner pd-progress-hero">

            <div>

                <h2 class="pd-progress-title">
                    Project progress
                </h2>

                <p class="pd-progress-subtitle">
                    Current progress recorded for this project.
                </p>

                <div class="pd-progress-bar">
                    <i></i>
                </div>

                <div class="pd-progress-labels">

                    <span>
                        Project completion
                    </span>

                    <strong>
                        {{ $progress }}%
                    </strong>

                </div>

            </div>

            <div class="pd-progress-percent">
                {{ $progress }}%
            </div>

        </section>


        {{-- ================================================================
             QUICK STATS
             ================================================================ --}}

        <section class="pd-stats">

            <div class="pd-card pd-stat">

                <span class="pd-stat-label">
                    Status
                </span>

                <strong class="pd-stat-value">
                    {{ $statusLabel }}
                </strong>

                <span class="pd-stat-help">
                    Current project state
                </span>

            </div>


            <div class="pd-card pd-stat">

                <span class="pd-stat-label">
                    Health
                </span>

                <strong class="pd-stat-value">
                    {{ $healthLabel }}
                </strong>

                <span class="pd-stat-help">
                    Based on project delivery data
                </span>

            </div>


            <div class="pd-card pd-stat">

                <span class="pd-stat-label">
                    Timeline
                </span>

                <strong class="pd-stat-value">

                    @if($daysLeft === null)
                        ΓÇö
                    @elseif($daysLeft > 0)
                        {{ $daysLeft }} days
                    @elseif($daysLeft === 0)
                        Today
                    @else
                        {{ abs($daysLeft) }} days
                    @endif

                </strong>

                <span class="pd-stat-help">

                    @if($daysLeft !== null && $daysLeft < 0)
                        Past target end date
                    @else
                        Remaining to target end
                    @endif

                </span>

            </div>


            <div class="pd-card pd-stat">

                <span class="pd-stat-label">
                    Milestones
                </span>

                <strong class="pd-stat-value">
                    {{ $milestoneCount }}
                </strong>

                <span class="pd-stat-help">
                    {{ $upcomingMilestoneCount }} upcoming
                </span>

            </div>

        </section>


        {{-- ================================================================
             MAIN CONTENT
             ================================================================ --}}

        <div class="pd-grid">


            {{-- ============================================================
                 LEFT COLUMN
                 ============================================================ --}}

            <div class="pd-column">


                {{-- PROJECT OVERVIEW --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        About this project
                    </h2>

                    <p class="pd-card-description">
                        Project overview and publicly useful information.
                    </p>

                    @if($project->description)

                        <div class="pd-overview">
                            {{ $project->description }}
                        </div>

                    @else

                        <div class="pd-empty">
                            No project description has been added yet.
                        </div>

                    @endif

                </section>


                {{-- PROJECT DETAILS --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Project details
                    </h2>

                    <p class="pd-card-description">
                        Key information about the project.
                    </p>

                    <dl class="pd-details">

                        <dt>
                            Status
                        </dt>

                        <dd>
                            {{ $statusLabel }}
                        </dd>


                        <dt>
                            Health
                        </dt>

                        <dd>
                            {{ $healthLabel }}
                        </dd>


                        <dt>
                            Priority
                        </dt>

                        <dd>
                            {{ $priorityLabel }}
                        </dd>


                        @if($project->project_type)

                            <dt>
                                Type
                            </dt>

                            <dd>
                                {{ $project->project_type }}
                            </dd>

                        @endif


                        @if($project->methodology)

                            <dt>
                                Methodology
                            </dt>

                            <dd>
                                {{ $project->methodology }}
                            </dd>

                        @endif


                        @if($project->client)

                            <dt>
                                Client
                            </dt>

                            <dd>
                                {{ $project->client }}
                            </dd>

                        @endif


                        @if($startDate)

                            <dt>
                                Start date
                            </dt>

                            <dd>
                                {{ $startDate->format('M d, Y') }}
                            </dd>

                        @endif


                        @if($endDate)

                            <dt>
                                Target end
                            </dt>

                            <dd>
                                {{ $endDate->format('M d, Y') }}
                            </dd>

                        @endif

                    </dl>

                </section>


                {{-- TIMELINE --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Project timeline
                    </h2>

                    <p class="pd-card-description">
                        Delivery timeline based on the project's start and target end dates.
                    </p>

                    @if($startDate || $endDate)

                        <div class="pd-timeline">

                            <div class="pd-timeline-track">
                                <i></i>
                            </div>

                            <div class="pd-timeline-labels">

                                <span>
                                    @if($startDate)
                                        Start:
                                        <strong>
                                            {{ $startDate->format('M d, Y') }}
                                        </strong>
                                    @else
                                        Start date not set
                                    @endif
                                </span>

                                <span>
                                    @if($endDate)
                                        Target:
                                        <strong>
                                            {{ $endDate->format('M d, Y') }}
                                        </strong>
                                    @else
                                        End date not set
                                    @endif
                                </span>

                            </div>

                        </div>

                    @else

                        <div class="pd-empty">
                            Project timeline dates have not been configured yet.
                        </div>

                    @endif

                </section>


                {{-- MILESTONES --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Milestones
                    </h2>

                    <p class="pd-card-description">
                        Key delivery points for this project.
                    </p>

                    @if($milestones->count())

                        <div class="pd-milestones">

                            @foreach($milestones as $milestone)

                                @php
                                    $milestoneStatus = strtolower(
                                        (string) ($milestone->status ?? '')
                                    );

                                    $milestoneComplete = in_array(
                                        $milestoneStatus,
                                        ['completed', 'complete', 'done'],
                                        true
                                    );
                                @endphp

                                <div class="pd-milestone {{ $milestoneComplete ? 'is-complete' : '' }}">

                                    <span class="pd-milestone-dot"></span>

                                    <div>

                                        <div class="pd-milestone-name">
                                            {{ $milestone->name }}
                                        </div>

                                        @if($milestone->description)

                                            <div class="pd-milestone-description">
                                                {{ $milestone->description }}
                                            </div>

                                        @endif

                                    </div>

                                    <div class="pd-milestone-date">

                                        @if($milestone->due_date)

                                            {{ $milestone->due_date->format('M d, Y') }}

                                        @else

                                            Date not set

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="pd-empty">
                            No milestones have been added yet.
                        </div>

                    @endif

                </section>


                {{-- OBJECTIVES --}}

                @if($objectives)

                    <section class="pd-card pd-card-inner">

                        <h2 class="pd-card-title">
                            Objectives
                        </h2>

                        <p class="pd-card-description">
                            The main areas of work or phases defined for this project.
                        </p>

                        <div class="pd-text-block">
                            {{ $objectives }}
                        </div>

                    </section>

                @endif


                {{-- DELIVERABLES --}}

                @if($deliverables)

                    <section class="pd-card pd-card-inner">

                        <h2 class="pd-card-title">
                            Deliverables
                        </h2>

                        <p class="pd-card-description">
                            Expected project outcomes and deliverables.
                        </p>

                        <div class="pd-text-block">
                            {{ $deliverables }}
                        </div>

                    </section>

                @endif


                {{-- HEALTH --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Project health
                    </h2>

                    <p class="pd-card-description">
                        Current health based on available project delivery information.
                    </p>

                    <div class="pd-health-panel {{ $healthClass }}">

                        <span class="pd-health-dot"></span>

                        <div>

                            <div class="pd-health-title">
                                {{ $healthLabel }}
                            </div>

                            @if(count($healthReasons))

                                <ul class="pd-health-reasons">

                                    @foreach($healthReasons as $reason)

                                        <li>
                                            {{ $reason }}
                                        </li>

                                    @endforeach

                                </ul>

                            @else

                                <ul class="pd-health-reasons">

                                    <li>
                                        No additional health notes are currently available.
                                    </li>

                                </ul>

                            @endif

                        </div>

                    </div>

                </section>


                {{-- ========================================================
                     INTERNAL TASK AREA
                     Only visible to authorized project users.
                     ======================================================== --}}

                @if($canManageProject)

                    <section class="pd-card pd-card-inner pd-internal">

                        <div class="pd-internal-label">
                            Internal workspace
                        </div>

                        <h2 class="pd-card-title">
                            Recent work
                        </h2>

                        <p class="pd-card-description">
                            Internal delivery activity. This section is not shown as stakeholder information.
                        </p>


                        @if($recentTasks->count())

                            <div class="pd-task-list">

                                @foreach($recentTasks as $task)

                                    @php
                                        $taskStatus = Str::headline(
                                            $task->status ?? 'pending'
                                        );

                                        $taskPriority = Str::headline(
                                            $task->priority ?? 'medium'
                                        );
                                    @endphp

                                    <div class="pd-task">

                                        <div>

                                            <div class="pd-task-title">
                                                {{ $task->title }}
                                            </div>

                                            <div class="pd-task-meta">

                                                <span>
                                                    {{ $taskStatus }}
                                                </span>

                                                <span>
                                                    {{ $taskPriority }}
                                                </span>

                                                @if($task->due_date)

                                                    <span>
                                                        Due
                                                        {{ $task->due_date->format('M d, Y') }}
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                        <span class="pd-badge status-planning">
                                            {{ $taskStatus }}
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="pd-empty">
                                No recent tasks are available.
                            </div>

                        @endif


                        <div style="margin-top:18px;">

                            <a
                                href="{{ route('web.tasks.create', ['project_id' => $project->id]) }}"
                                class="pd-btn pd-btn-primary"
                            >
                                Add task
                            </a>

                        </div>

                    </section>

                @endif

            </div>


            {{-- ============================================================
                 RIGHT COLUMN
                 ============================================================ --}}

            <div class="pd-column">


                {{-- EXPRESS INTEREST --}}

                <section
                    id="express-interest"
                    class="pd-card pd-interest"
                >

                    <h2>
                        Interested in this project?
                    </h2>

                    <p>
                        Stakeholders, partners, and potential investors can express
                        interest in learning more about this project.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('projects.public.interest', $project) }}"
                        class="pd-interest-form"
                    >
                        @csrf

                        <div class="pd-form-grid">
                            <div class="pd-form-field">
                                <label for="interest-name">Name</label>
                                <input
                                    id="interest-name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <div class="pd-form-field">
                                <label for="interest-email">Email</label>
                                <input
                                    id="interest-email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    required
                                >
                            </div>

                            <div class="pd-form-field">
                                <label for="interest-phone">Phone</label>
                                <input
                                    id="interest-phone"
                                    type="text"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    maxlength="30"
                                >
                            </div>

                            <div class="pd-form-field">
                                <label for="interest-company">Company / Organisation</label>
                                <input
                                    id="interest-company"
                                    type="text"
                                    name="company"
                                    value="{{ old('company') }}"
                                    maxlength="150"
                                >
                            </div>

                            <div class="pd-form-field pd-form-field-wide">
                                <label for="interest-message">Message</label>
                                <textarea
                                    id="interest-message"
                                    name="message"
                                    rows="4"
                                    maxlength="2000"
                                    placeholder="Tell us what you would like to discuss..."
                                >{{ old('message') }}</textarea>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="pd-form-errors">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="pd-btn">
                            Express interest
                        </button>
                    </form>

                </section>


                {{-- PROJECT SNAPSHOT --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Project snapshot
                    </h2>

                    <p class="pd-card-description">
                        A quick view of the project's current position.
                    </p>


                    <dl class="pd-details">

                        <dt>
                            Progress
                        </dt>

                        <dd>
                            {{ $progress }}%
                        </dd>


                        <dt>
                            Health
                        </dt>

                        <dd>
                            {{ $healthLabel }}
                        </dd>


                        <dt>
                            Status
                        </dt>

                        <dd>
                            {{ $statusLabel }}
                        </dd>


                        <dt>
                            Milestones
                        </dt>

                        <dd>
                            {{ $milestoneCount }}
                        </dd>


                        @if($endDate)

                            <dt>
                                Target
                            </dt>

                            <dd>
                                {{ $endDate->format('M d, Y') }}
                            </dd>

                        @endif

                    </dl>

                </section>


                {{-- INTERNAL TEAM --}}
                {{-- Stakeholders do not see this section. --}}

                @if($canManageTeam)

                    <section class="pd-card pd-card-inner pd-internal">

                        <div class="pd-internal-label">
                            Internal only
                        </div>

                        <h2 class="pd-card-title">
                            Project team
                        </h2>

                        <p class="pd-card-description">
                            Team members assigned to this project.
                        </p>


                        @if($members->count())

                            <div class="pd-team-list">

                                @foreach($members as $member)

                                    @php
                                        $initials = collect(
                                            preg_split(
                                                '/\s+/',
                                                trim($member->name ?? '')
                                            )
                                        )
                                        ->filter()
                                        ->take(2)
                                        ->map(
                                            fn ($part) => strtoupper(
                                                substr($part, 0, 1)
                                            )
                                        )
                                        ->implode('');
                                    @endphp

                                    <div class="pd-team-member">

                                        <div class="pd-avatar">
                                            {{ $initials ?: '?' }}
                                        </div>

                                        <div class="pd-member-info">

                                            <div class="pd-member-name">
                                                {{ $member->name }}
                                            </div>

                                            <div class="pd-member-role">
                                                {{ $member->role ?? 'Team member' }}
                                            </div>

                                        </div>


                                        @if(
                                            $member->id !== $project->owner_id &&
                                            (
                                                $user->role === 'admin' ||
                                                $project->owner_id === $user->id
                                            )
                                        )

                                            <form
                                                method="POST"
                                                action="{{ route('projects.members.destroy', [$project, $member]) }}"
                                                onsubmit="return confirm('Remove this member from the project?')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="pd-member-remove"
                                                >
                                                    Remove
                                                </button>
                                            </form>

                                        @endif

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="pd-empty">
                                No project members have been assigned yet.
                            </div>

                        @endif


                        {{-- TEAM INVITE --}}

                        @if($availableUsers->count())

                            <div class="pd-section-divider"></div>

                            <form
                                method="POST"
                                action="{{ route('projects.members.store', $project) }}"
                            >

                                @csrf

                                <label
                                    for="project-member"
                                    style="
                                        display:block;
                                        margin-bottom:7px;
                                        color:var(--pd-text);
                                        font-size:12px;
                                        font-weight:800;
                                    "
                                >
                                    Add team member
                                </label>

                                <div
                                    style="
                                        display:flex;
                                        gap:8px;
                                        flex-wrap:wrap;
                                    "
                                >

                                    <select
                                        id="project-member"
                                        name="user_id"
                                        required
                                        style="
                                            flex:1;
                                            min-width:180px;
                                            min-height:40px;
                                            padding:0 11px;
                                            border:1px solid var(--pd-line);
                                            border-radius:10px;
                                            background:var(--pd-card-strong);
                                            color:var(--pd-text);
                                        "
                                    >

                                        <option value="">
                                            Select a user
                                        </option>

                                        @foreach($availableUsers as $availableUser)

                                            <option value="{{ $availableUser->id }}">
                                                {{ $availableUser->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    <button
                                        type="submit"
                                        class="pd-btn pd-btn-primary"
                                    >
                                        Add
                                    </button>

                                </div>

                            </form>

                        @endif

                    </section>

                @endif


                {{-- PROJECT DATES --}}

                <section class="pd-card pd-card-inner">

                    <h2 class="pd-card-title">
                        Timeline summary
                    </h2>

                    <div style="margin-top:16px;">

                        @if($startDate)

                            <div style="margin-bottom:15px;">

                                <div
                                    style="
                                        color:var(--pd-muted);
                                        font-size:11px;
                                        font-weight:800;
                                        text-transform:uppercase;
                                        letter-spacing:.05em;
                                    "
                                >
                                    Start
                                </div>

                                <div
                                    style="
                                        margin-top:4px;
                                        color:var(--pd-text);
                                        font-size:14px;
                                        font-weight:800;
                                    "
                                >
                                    {{ $startDate->format('M d, Y') }}
                                </div>

                            </div>

                        @endif


                        @if($endDate)

                            <div>

                                <div
                                    style="
                                        color:var(--pd-muted);
                                        font-size:11px;
                                        font-weight:800;
                                        text-transform:uppercase;
                                        letter-spacing:.05em;
                                    "
                                >
                                    Target completion
                                </div>

                                <div
                                    style="
                                        margin-top:4px;
                                        color:var(--pd-text);
                                        font-size:14px;
                                        font-weight:800;
                                    "
                                >
                                    {{ $endDate->format('M d, Y') }}
                                </div>

                            </div>

                        @endif

                    </div>

                </section>


                {{-- BACK TO PROJECTS --}}

                <a
                    href="{{ route('projects.index') }}"
                    class="pd-btn"
                    style="width:100%;"
                >
                    ΓåÉ Back to Projects
                </a>

            </div>

        </div>

    </div>

</div>

@endsection
