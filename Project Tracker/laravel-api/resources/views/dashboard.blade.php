@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = \Illuminate\Support\Str::of(auth()->user()?->name ?? '')->explode(' ')->first();

    /*
    |--------------------------------------------------------------------------
    | Safe dashboard health fallback
    |--------------------------------------------------------------------------
    | Uses the controller's healthSummary when available.
    | Falls back to the existing dashboard counters when it is not.
    */
    $dashboardHealth = $healthSummary ?? [
        'on_track' => max(
            0,
            (int) $projectCount
            - (int) $atRiskProjectCount
            - (int) $criticalProjectCount
            - (int) $completedProjectCount
        ),
        'at_risk' => (int) $atRiskProjectCount,
        'critical' => (int) $criticalProjectCount,
        'completed' => (int) $completedProjectCount,
    ];

    $healthTotal = max(1, (int) $projectCount);

    $healthRows = [
        [
            'key' => 'on-track',
            'label' => 'On Track',
            'value' => (int) ($dashboardHealth['on_track'] ?? 0),
            'class' => 'health-on-track',
        ],
        [
            'key' => 'at-risk',
            'label' => 'At Risk',
            'value' => (int) ($dashboardHealth['at_risk'] ?? 0),
            'class' => 'health-at-risk',
        ],
        [
            'key' => 'critical',
            'label' => 'Critical',
            'value' => (int) ($dashboardHealth['critical'] ?? 0),
            'class' => 'health-critical',
        ],
        [
            'key' => 'completed',
            'label' => 'Completed',
            'value' => (int) ($dashboardHealth['completed'] ?? 0),
            'class' => 'health-completed',
        ],
    ];

    $budgetUtilizationSafe = (float) ($budgetUtilization ?? 0);
    $overallCompletionSafe = (float) ($overallCompletion ?? 0);
@endphp

<style>
    /* =====================================================
       DASHBOARD - GLASS THEME
       Everything is scoped under .dx-page.
       ===================================================== */

    .dx-page {
        --dx-text: #1e2a44;
        --dx-muted: #6a7690;
        --dx-glass: rgba(255, 255, 255, 0.55);
        --dx-glass-strong: rgba(255, 255, 255, 0.78);
        --dx-solid: rgba(255, 255, 255, 0.9);
        --dx-border: rgba(255, 255, 255, 0.8);
        --dx-line: rgba(30, 42, 68, 0.08);
        --dx-track: rgba(30, 42, 68, 0.09);
        --dx-shadow: 0 12px 38px rgba(49, 46, 129, 0.12);
        --dx-accent: #6366f1;
        --dx-accent-2: #22d3ee;

        position: relative;
        isolation: isolate;
        color: var(--dx-text);
    }

    body.dark .dx-page {
        --dx-text: #e9effd;
        --dx-muted: #9aa8c7;
        --dx-glass: rgba(255, 255, 255, 0.06);
        --dx-glass-strong: rgba(255, 255, 255, 0.11);
        --dx-solid: #1c263a;
        --dx-border: rgba(255, 255, 255, 0.13);
        --dx-line: rgba(255, 255, 255, 0.09);
        --dx-track: rgba(255, 255, 255, 0.12);
        --dx-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
    }

    .dx-page::before {
        content: '';
        position: absolute;
        inset: -34px;
        z-index: -1;
        pointer-events: none;
        background:
            radial-gradient(560px 380px at 6% 4%, rgba(99, 102, 241, 0.26), transparent 70%),
            radial-gradient(520px 380px at 96% 30%, rgba(34, 211, 238, 0.2), transparent 70%),
            radial-gradient(560px 420px at 60% 70%, rgba(244, 114, 182, 0.14), transparent 70%),
            radial-gradient(460px 360px at 4% 92%, rgba(139, 92, 246, 0.18), transparent 70%);
    }

    body.dark .dx-page::before {
        background:
            radial-gradient(560px 380px at 6% 4%, rgba(99, 102, 241, 0.34), transparent 70%),
            radial-gradient(520px 380px at 96% 30%, rgba(34, 211, 238, 0.16), transparent 70%),
            radial-gradient(560px 420px at 60% 70%, rgba(217, 70, 239, 0.16), transparent 70%),
            radial-gradient(460px 360px at 4% 92%, rgba(59, 130, 246, 0.2), transparent 70%);
    }

    @keyframes dx-rise {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dx-page > * {
        animation: dx-rise 0.55s ease both;
    }

    .dx-page > *:nth-child(2) { animation-delay: 0.05s; }
    .dx-page > *:nth-child(3) { animation-delay: 0.1s; }
    .dx-page > *:nth-child(4) { animation-delay: 0.15s; }
    .dx-page > *:nth-child(5) { animation-delay: 0.2s; }
    .dx-page > *:nth-child(6) { animation-delay: 0.25s; }
    .dx-page > *:nth-child(7) { animation-delay: 0.3s; }

    @media (prefers-reduced-motion: reduce) {
        .dx-page > * {
            animation: none;
        }
    }

    .dx-page .page-heading h1 {
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--dx-text);
    }

    .dx-page .page-heading p {
        color: var(--dx-muted);
    }

    /* ---------- HERO ---------- */

    .dx-page .dashboard-hero {
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 24px;
        background:
            radial-gradient(circle at 12% 0%, rgba(99, 102, 241, 0.5), transparent 45%),
            radial-gradient(circle at 92% 100%, rgba(34, 211, 238, 0.28), transparent 45%),
            linear-gradient(135deg, #111827 0%, #172554 48%, #312e81 100%);
        box-shadow: 0 22px 60px rgba(49, 46, 129, 0.35);
    }

    .dx-page .dashboard-hero .eyebrow {
        color: #a5b4fc;
        letter-spacing: 1.6px;
    }

    .dx-page .dashboard-hero h2 {
        font-weight: 800;
        letter-spacing: -0.8px;
    }

    .dx-page .dashboard-hero p {
        color: rgba(255, 255, 255, 0.75);
    }

    .dx-page .dashboard-hero .button {
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 10px 28px rgba(99, 102, 241, 0.5);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dx-page .dashboard-hero .button:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(99, 102, 241, 0.65);
    }

    .dx-page .hero-panel {
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 16px;
        background: linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.2),
            rgba(255, 255, 255, 0.06)
        );
        backdrop-filter: blur(16px) saturate(150%);
        -webkit-backdrop-filter: blur(16px) saturate(150%);
        box-shadow:
            0 16px 40px rgba(0, 0, 0, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }

    .dx-page .hero-panel small {
        display: block;
        margin-top: 8px;
        color: rgba(255, 255, 255, 0.7);
        font-size: 11px;
    }

    .dx-page .hero-panel i b {
        background: linear-gradient(90deg, #a5b4fc, #67e8f9);
        box-shadow: 0 0 12px rgba(103, 232, 249, 0.7);
    }

    .dx-page .hero-orbit {
        border-color: rgba(255, 255, 255, 0.16);
    }

    /* ---------- STAT CARDS ---------- */

    .dx-page .stats {
        gap: 18px;
    }

    .dx-page .stats article {
        position: relative;
        overflow: hidden;
        min-height: 138px;
        padding: 22px;
        border: 1px solid var(--dx-border);
        border-radius: 20px;
        background: linear-gradient(
            145deg,
            var(--dx-glass-strong),
            var(--dx-glass)
        );
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        box-shadow:
            var(--dx-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .dx-page .stats article::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
        background: var(--dx-bar, linear-gradient(90deg, #6366f1, #22d3ee));
        opacity: 0.9;
    }

    .dx-page .stats article:hover {
        transform: translateY(-4px);
        box-shadow:
            0 20px 48px rgba(49, 46, 129, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .dx-page .stats article:nth-child(1) {
        --dx-bar: linear-gradient(90deg, #6366f1, #818cf8);
    }

    .dx-page .stats article:nth-child(2) {
        --dx-bar: linear-gradient(90deg, #10b981, #6ee7b7);
    }

    .dx-page .stats article:nth-child(3) {
        --dx-bar: linear-gradient(90deg, #3b82f6, #22d3ee);
    }

    .dx-page .stats article:nth-child(4) {
        --dx-bar: linear-gradient(90deg, #f43f5e, #fb923c);
    }

    .dx-page .stats span {
        color: var(--dx-muted);
    }

    .dx-page .stats strong {
        color: var(--dx-text);
        font-weight: 800;
        letter-spacing: -1px;
    }

    .dx-page .stats small {
        color: var(--dx-muted);
    }

    .dx-page .dx-icon {
        position: absolute;
        top: 18px;
        right: 18px;
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        color: #ffffff;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
    }

    .dx-page .dx-icon svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .dx-page .dx-green {
        background: linear-gradient(135deg, #10b981, #34d399);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }

    .dx-page .dx-blue {
        background: linear-gradient(135deg, #3b82f6, #22d3ee);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
    }

    .dx-page .dx-rose {
        background: linear-gradient(135deg, #f43f5e, #fb923c);
        box-shadow: 0 8px 20px rgba(244, 63, 94, 0.4);
    }

    .dx-page .dx-amber {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
    }

    .dx-page .dx-purple {
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
    }

    /* ---------- GLASS CARDS ---------- */

    .dx-page .card {
        border: 1px solid var(--dx-border);
        border-radius: 20px;
        background: linear-gradient(
            145deg,
            var(--dx-glass-strong),
            var(--dx-glass)
        );
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        box-shadow:
            var(--dx-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
        color: var(--dx-text);
    }

    body.dark .dx-page .card,
    body.dark .dx-page .stats article {
        background: linear-gradient(
            145deg,
            var(--dx-glass-strong),
            var(--dx-glass)
        );
        border-color: var(--dx-border);
    }

    body.dark .dx-page .card,
    body.dark .dx-page .stats article,
    body.dark .dx-page .quick-actions {
        box-shadow:
            var(--dx-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .dx-page .card h2 {
        font-weight: 800;
        letter-spacing: -0.2px;
        color: var(--dx-text);
    }

    .dx-page .card-heading p {
        color: var(--dx-muted);
    }

    .dx-page .card-heading > a {
        padding: 6px 14px;
        border: 1px solid var(--dx-border);
        border-radius: 999px;
        background: var(--dx-glass-strong);
        color: var(--dx-accent);
        font-size: 12px;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .dx-page .card-heading > a:hover {
        background: var(--dx-accent);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* ---------- HEALTH ---------- */

    .dx-page .dashboard-insights {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.8fr);
        gap: 18px;
    }

    .dx-page .health-card,
    .dx-page .insight-card {
        padding: 24px;
    }

    .dx-page .health-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 20px;
    }

    .dx-page .health-item {
        padding: 15px;
        border: 1px solid var(--dx-line);
        border-radius: 15px;
        background: var(--dx-glass);
    }

    .dx-page .health-item strong {
        display: block;
        margin-bottom: 4px;
        color: var(--dx-text);
        font-size: 24px;
        font-weight: 800;
    }

    .dx-page .health-item span {
        color: var(--dx-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .dx-page .health-bar {
        height: 10px;
        margin-top: 20px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--dx-track);
        display: flex;
    }

    .dx-page .health-bar i {
        display: block;
        height: 100%;
    }

    .dx-page .health-on-track {
        color: #059669;
    }

    .dx-page .health-at-risk {
        color: #d97706;
    }

    .dx-page .health-critical {
        color: #dc2626;
    }

    .dx-page .health-completed {
        color: #2563eb;
    }

    .dx-page .health-bar .health-on-track {
        background: #10b981;
    }

    .dx-page .health-bar .health-at-risk {
        background: #f59e0b;
    }

    .dx-page .health-bar .health-critical {
        background: #ef4444;
    }

    .dx-page .health-bar .health-completed {
        background: #6366f1;
    }

    .dx-page .insight-list {
        display: grid;
        gap: 12px;
        margin-top: 18px;
    }

    .dx-page .insight-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 14px;
        border: 1px solid var(--dx-line);
        border-radius: 14px;
        background: var(--dx-glass);
    }

    .dx-page .insight-dot {
        flex: 0 0 auto;
        width: 9px;
        height: 9px;
        margin-top: 6px;
        border-radius: 50%;
        background: #6366f1;
        box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1);
    }

    .dx-page .insight-item strong {
        display: block;
        color: var(--dx-text);
        font-size: 13px;
    }

    .dx-page .insight-item span {
        display: block;
        margin-top: 3px;
        color: var(--dx-muted);
        font-size: 12px;
    }

    /* ---------- ROWS ---------- */

    .dx-page .row {
        border-color: var(--dx-line);
        border-radius: 12px;
        color: var(--dx-text);
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .dx-page .row b {
        color: var(--dx-text);
    }

    .dx-page .row small,
    .dx-page .row em {
        color: var(--dx-muted);
    }

    .dx-page .task-row:hover,
    body.dark .dx-page .task-row:hover {
        background: var(--dx-glass-strong);
        transform: translateX(3px);
    }

    .dx-page .row time {
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.12);
        color: var(--dx-accent);
    }

    .dx-page .task-marker {
        border-color: rgba(99, 102, 241, 0.45);
    }

    .dx-page .task-marker.done {
        border-color: #10b981;
        background: linear-gradient(135deg, #10b981, #34d399);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .dx-page .empty {
        color: var(--dx-muted);
    }

    .dx-page .empty a {
        color: var(--dx-accent);
        font-weight: 700;
    }

    /* ---------- BADGES ---------- */

    .dx-page .badge {
        border: 1px solid transparent;
        font-weight: 700;
        text-transform: capitalize;
    }

    .dx-page .badge-planning,
    .dx-page .badge-pending,
    .dx-page .badge-medium {
        background: rgba(245, 158, 11, 0.16);
        border-color: rgba(245, 158, 11, 0.3);
        color: #b45309;
    }

    .dx-page .badge-in-progress,
    .dx-page .badge-active {
        background: rgba(59, 130, 246, 0.16);
        border-color: rgba(59, 130, 246, 0.3);
        color: #1d4ed8;
    }

    .dx-page .badge-completed,
    .dx-page .badge-low {
        background: rgba(16, 185, 129, 0.16);
        border-color: rgba(16, 185, 129, 0.3);
        color: #047857;
    }

    .dx-page .badge-on-hold,
    .dx-page .badge-high {
        background: rgba(244, 63, 94, 0.15);
        border-color: rgba(244, 63, 94, 0.3);
        color: #be123c;
    }

    body.dark .dx-page .badge-planning,
    body.dark .dx-page .badge-pending,
    body.dark .dx-page .badge-medium {
        color: #fcd34d;
    }

    body.dark .dx-page .badge-in-progress,
    body.dark .dx-page .badge-active {
        color: #93c5fd;
    }

    body.dark .dx-page .badge-completed,
    body.dark .dx-page .badge-low {
        color: #6ee7b7;
    }

    body.dark .dx-page .badge-on-hold,
    body.dark .dx-page .badge-high {
        color: #fda4af;
    }

    /* ---------- PROGRESS ---------- */

    .dx-page .progress {
        background: var(--dx-track);
    }

    .dx-page .progress i {
        background: linear-gradient(90deg, #6366f1, #22d3ee);
        box-shadow: 0 0 10px rgba(34, 211, 238, 0.5);
    }

    .dx-page .progress-label span {
        color: var(--dx-muted);
        font-weight: 700;
    }

    /* ---------- CHARTS ---------- */

    .dx-page .activity-chart {
        border-bottom-color: var(--dx-line);
        background-image:
            repeating-linear-gradient(
                to bottom,
                transparent 0,
                transparent 46px,
                var(--dx-line) 47px
            );
    }

    .dx-page .activity-month,
    .dx-page .chart-key,
    .dx-page .chart-note {
        color: var(--dx-muted);
    }

    .dx-page .project-bar {
        background: linear-gradient(180deg, #818cf8, #4f46e5);
        box-shadow: 0 6px 14px rgba(99, 102, 241, 0.35);
    }

    .dx-page .task-bar {
        background: linear-gradient(180deg, #f9a8d4, #db2777);
        box-shadow: 0 6px 14px rgba(219, 39, 119, 0.3);
    }

    .dx-page .activity-bars i {
        border-radius: 8px 8px 0 0;
    }

    .dx-page .key-project,
    .dx-page .new-dot {
        background: #6366f1;
    }

    .dx-page .key-task,
    .dx-page .completed-dot {
        background: #ec4899;
    }

    .dx-page .active-dot {
        background: #22d3ee;
    }

    .dx-page .target-ring {
        background: conic-gradient(
            #6366f1 0 calc(var(--new) * 1%),
            #ec4899 0 calc((var(--new) + var(--completed)) * 1%),
            #22d3ee 0 calc((var(--new) + var(--completed) + var(--active)) * 1%),
            var(--dx-track) 0
        );
        filter: drop-shadow(0 0 14px rgba(99, 102, 241, 0.35));
    }

    .dx-page .target-ring > div,
    body.dark .dx-page .target-ring > div {
        background: var(--dx-solid);
    }

    .dx-page .target-ring strong,
    .dx-page .target-legend b {
        color: var(--dx-text);
    }

    .dx-page .target-ring span,
    .dx-page .target-legend span {
        color: var(--dx-muted);
    }

    .dx-page .task-rate-overview,
    body.dark .dx-page .task-rate-overview {
        border: 1px solid var(--dx-border);
        border-radius: 14px;
        background: var(--dx-glass);
        color: var(--dx-text);
    }

    /* ---------- TEAM ---------- */

    .dx-page .team-person,
    body.dark .dx-page .team-person {
        border-color: var(--dx-line);
    }

    .dx-page .team-person b,
    body.dark .dx-page .team-person b {
        color: var(--dx-text);
    }

    .dx-page .team-person small {
        color: var(--dx-muted);
    }

    .dx-page .team-workload {
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.12);
        color: var(--dx-accent);
        font-weight: 700;
    }

    .dx-page .member-avatar {
        border-color: var(--dx-solid);
    }

    /* ---------- TABLE ---------- */

    .dx-page table {
        color: var(--dx-text);
    }

    .dx-page th {
        background: rgba(99, 102, 241, 0.08);
        color: var(--dx-muted);
        font-size: 11px;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .dx-page td,
    .dx-page th {
        border-color: var(--dx-line);
    }

    .dx-page tbody tr {
        transition: background 0.2s ease;
    }

    .dx-page tbody tr:hover {
        background: var(--dx-glass-strong);
    }

    .dx-page td a {
        color: var(--dx-text);
        font-weight: 700;
        text-decoration: none;
    }

    .dx-page td small {
        color: var(--dx-muted);
    }

    .dx-page .table-action {
        display: inline-block;
        padding: 5px 13px;
        border: 1px solid var(--dx-border);
        border-radius: 999px;
        background: var(--dx-glass-strong);
        color: var(--dx-accent) !important;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .dx-page .table-action:hover {
        background: var(--dx-accent);
        color: #ffffff !important;
    }

    /* ---------- QUICK ACTIONS ---------- */

    .dx-page .quick-actions,
    body.dark .dx-page .quick-actions {
        border: 1px solid var(--dx-border);
        border-radius: 20px;
        background: linear-gradient(
            145deg,
            var(--dx-glass-strong),
            var(--dx-glass)
        );
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        box-shadow:
            var(--dx-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .dx-page .quick-actions h2 {
        color: var(--dx-text);
        font-weight: 800;
    }

    .dx-page .quick-actions p {
        color: var(--dx-muted);
    }

    .dx-page .quick-actions .button {
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 8px 22px rgba(99, 102, 241, 0.38);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dx-page .quick-actions .button.secondary {
        border: 1px solid var(--dx-border);
        background: var(--dx-glass-strong);
        color: var(--dx-text);
        box-shadow: none;
    }

    .dx-page .quick-actions .button:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(99, 102, 241, 0.5);
    }

    /* ---------- RESPONSIVE ---------- */

    @media (max-width: 1100px) {
        .dx-page .dashboard-insights {
            grid-template-columns: 1fr;
        }

        .dx-page .health-summary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dx-page .health-summary {
            grid-template-columns: 1fr 1fr;
        }

        .dx-page .dashboard-insights {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .dx-page .health-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dx-page">

    {{-- PAGE HEADING --}}
    <div class="page-heading dashboard-heading">
        <div>
            <h1>Dashboard</h1>
            <p>Portfolio overview and delivery performance</p>
        </div>
    </div>

    {{-- HERO --}}
    <section class="dashboard-hero">
        <div>
            <span class="eyebrow">
                {{ strtoupper($greeting) }}@if($firstName), {{ strtoupper($firstName) }}@endif
            </span>

            <h2>Keep every project moving forward.</h2>

            <p>
                Plan work, stay ahead of delivery risks, and keep your team focused on the next milestone.
            </p>

            <a class="button" href="{{ route('projects.index') }}">
                Manage projects
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="hero-art" aria-hidden="true">
            <div class="hero-orbit orbit-one"></div>
            <div class="hero-orbit orbit-two"></div>

            <img
                class="hero-illustration"
                src="{{ asset('assets/images/project-hero.png') }}"
                alt=""
            >

            <div class="hero-panel">
                <span>Delivery health</span>

                <strong>{{ $completedTasks }} completed</strong>

                <i>
                    <b style="width: {{ $completionRate }}%"></b>
                </i>

                <small>
                    {{ $completionRate }}% of {{ $taskCount }} tasks done
                </small>
            </div>
        </div>
    </section>

    {{-- DASHBOARD STATISTICS --}}
    <div class="stats dashboard-stats">

        <article>
            <b class="dx-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
            </b>

            <span>New projects</span>
            <strong>{{ $newProjectCount }}</strong>
            <small>Created this month</small>
        </article>

        <article>
            <b class="dx-icon dx-green">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M8 12.5l2.5 2.5L16 9.5"/>
                </svg>
            </b>

            <span>Completed projects</span>
            <strong>{{ $completedProjectCount }}</strong>
            <small>{{ $projectCount }} total in portfolio</small>
        </article>

        <article>
            <b class="dx-icon dx-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </b>

            <span>Ongoing projects</span>
            <strong>{{ $activeProjectCount }}</strong>
            <small>{{ $pendingProjectCount }} planning or on hold</small>
        </article>

        <article>
            <b class="dx-icon dx-rose">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3l9.5 16.5H2.5z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17.2v.01"/>
                </svg>
            </b>

            <span>Delivery watch</span>
            <strong>{{ $riskCount }}</strong>
            <small>{{ $sprintCount }} active sprints</small>
        </article>

    </div>

    {{-- PROJECT HEALTH & FINANCIALS --}}
    <div class="stats dashboard-stats">

        <article>
            <b class="dx-icon dx-amber">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 3l9.5 16.5H2.5z"/>
                    <path d="M12 10v4"/>
                    <path d="M12 17.2v.01"/>
                </svg>
            </b>

            <span>At-risk projects</span>
            <strong>{{ $atRiskProjectCount + $criticalProjectCount }}</strong>
            <small>
                {{ $criticalProjectCount }} critical · {{ $atRiskProjectCount }} at risk
            </small>
        </article>

        <article>
            <b class="dx-icon dx-rose">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v6l4 2"/>
                </svg>
            </b>

            <span>Overdue projects</span>
            <strong>{{ $overdueProjectCount }}</strong>
            <small>{{ $overdueTaskCount }} overdue tasks portfolio-wide</small>
        </article>

        <article>
            <b class="dx-icon dx-purple">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M3 10h18"/>
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M7 15h4"/>
                </svg>
            </b>

            <span>Portfolio budget</span>
            <strong>${{ number_format($totalBudget, 0) }}</strong>
            <small>
                ${{ number_format($totalSpent, 0) }} spent ·
                ${{ number_format($remainingBudget, 0) }} remaining
            </small>
        </article>

        <article>
            <b class="dx-icon dx-blue">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M8 12.5l2.5 2.5L16 9.5"/>
                </svg>
            </b>

            <span>Overall completion</span>
            <strong>{{ $overallCompletion }}%</strong>
            <small>
                Average progress across {{ $projectCount }} projects
            </small>
        </article>

    </div>

    {{-- PORTFOLIO HEALTH --}}
    <div class="dashboard-insights">

        <section class="card health-card">

            <div class="card-heading">
                <div>
                    <h2>Portfolio health</h2>
                    <p>
                        Current delivery condition across your project portfolio.
                    </p>
                </div>

                <a href="{{ route('projects.index') }}">
                    View projects
                </a>
            </div>

            <div class="health-summary">

                @foreach($healthRows as $health)
                    <div class="health-item">

                        <strong class="{{ $health['class'] }}">
                            {{ $health['value'] }}
                        </strong>

                        <span>
                            {{ $health['label'] }}
                        </span>

                    </div>
                @endforeach

            </div>

            <div class="health-bar" aria-label="Portfolio health distribution">

                @foreach($healthRows as $health)
                    @php
                        $percentage = ($health['value'] / $healthTotal) * 100;
                    @endphp

                    <i
                        class="{{ $health['class'] }}"
                        style="width: {{ $percentage }}%"
                        title="{{ $health['label'] }}: {{ $health['value'] }}"
                    ></i>
                @endforeach

            </div>

        </section>

        <section class="card insight-card">

            <div class="card-heading">
                <div>
                    <h2>Portfolio insights</h2>
                    <p>
                        Actionable information from current records.
                    </p>
                </div>
            </div>

            <div class="insight-list">

                @if($overdueTaskCount > 0)
                    <div class="insight-item">
                        <i class="insight-dot"></i>

                        <div>
                            <strong>
                                {{ $overdueTaskCount }}
                                {{ $overdueTaskCount === 1 ? 'task is' : 'tasks are' }}
                                overdue
                            </strong>

                            <span>
                                Review overdue work and update delivery dates where necessary.
                            </span>
                        </div>
                    </div>
                @endif

                @if(($atRiskProjectCount + $criticalProjectCount) > 0)
                    <div class="insight-item">
                        <i class="insight-dot"></i>

                        <div>
                            <strong>
                                {{ $atRiskProjectCount + $criticalProjectCount }}
                                projects need attention
                            </strong>

                            <span>
                                {{ $criticalProjectCount }} critical and
                                {{ $atRiskProjectCount }} at risk.
                            </span>
                        </div>
                    </div>
                @endif

                @if($budgetUtilizationSafe > $overallCompletionSafe && $totalBudget > 0)
                    <div class="insight-item">
                        <i class="insight-dot"></i>

                        <div>
                            <strong>
                                Budget utilization is ahead of completion
                            </strong>

                            <span>
                                {{ number_format($budgetUtilizationSafe, 1) }}%
                                of budget used against
                                {{ number_format($overallCompletionSafe, 1) }}%
                                overall completion.
                            </span>
                        </div>
                    </div>
                @endif

                @if(
                    $overdueTaskCount === 0 &&
                    ($atRiskProjectCount + $criticalProjectCount) === 0 &&
                    !($budgetUtilizationSafe > $overallCompletionSafe && $totalBudget > 0)
                )
                    <div class="insight-item">
                        <i class="insight-dot"></i>

                        <div>
                            <strong>
                                No immediate portfolio alerts
                            </strong>

                            <span>
                                Current dashboard records do not show overdue tasks,
                                at-risk projects, or elevated budget utilization.
                            </span>
                        </div>
                    </div>
                @endif

            </div>

        </section>

    </div>

    {{-- PROJECT ANALYTICS --}}
    <div class="dashboard-analytics">

        <section class="card activity-card">

            <div class="card-heading">

                <div>
                    <h2>Project statistics</h2>

                    <p>
                        Projects and tasks created in the last six months.
                    </p>
                </div>

                <a href="{{ route('analytics') }}">
                    Reports
                </a>

            </div>

            @php
                $activityMax = max(
                    1,
                    ...$projectActivity,
                    ...$taskActivity
                );
            @endphp

            <div
                class="activity-chart"
                role="img"
                aria-label="Projects and tasks created over the last six months"
            >

                @foreach($activityMonths as $index => $month)

                    <div class="activity-month">

                        <div class="activity-bars">

                            <i
                                class="project-bar"
                                style="height: {{ max(4, ($projectActivity[$index] / $activityMax) * 100) }}%"
                                title="{{ $projectActivity[$index] }} projects"
                            ></i>

                            <i
                                class="task-bar"
                                style="height: {{ max(4, ($taskActivity[$index] / $activityMax) * 100) }}%"
                                title="{{ $taskActivity[$index] }} tasks"
                            ></i>

                        </div>

                        <span>{{ $month }}</span>

                    </div>

                @endforeach

            </div>

            <div class="chart-key">

                <span>
                    <i class="key-project"></i>
                    Projects
                </span>

                <span>
                    <i class="key-task"></i>
                    Tasks
                </span>

            </div>

        </section>

        {{-- MONTHLY TARGETS --}}
        <section class="card target-card">

            <div class="card-heading">

                <div>
                    <h2>Monthly targets</h2>

                    <p>
                        Current project measures from live records.
                    </p>
                </div>

            </div>

            @php
                $targetBase = max(
                    1,
                    $monthlyTarget['new']
                    + $monthlyTarget['completed']
                    + $monthlyTarget['active']
                );
            @endphp

            <div
                class="target-ring"
                style="
                    --new: {{ ($monthlyTarget['new'] / $targetBase) * 100 }};
                    --completed: {{ ($monthlyTarget['completed'] / $targetBase) * 100 }};
                    --active: {{ ($monthlyTarget['active'] / $targetBase) * 100 }}
                "
            >

                <div>

                    <strong>
                        {{ $monthlyTarget['total'] }}
                    </strong>

                    <span>
                        projects
                    </span>

                </div>

            </div>

            <div class="target-legend">

                <span>
                    <i class="new-dot"></i>
                    New
                    <b>{{ $monthlyTarget['new'] }}</b>
                </span>

                <span>
                    <i class="completed-dot"></i>
                    Completed
                    <b>{{ $monthlyTarget['completed'] }}</b>
                </span>

                <span>
                    <i class="active-dot"></i>
                    Active / pending
                    <b>{{ $monthlyTarget['active'] }}</b>
                </span>

            </div>

        </section>

    </div>

    {{-- DAILY TASKS & UPCOMING MILESTONES --}}
    <div class="dashboard-grid">

        {{-- DAILY TASKS --}}
        <section class="card">

            <div class="card-heading">

                <div>
                    <h2>Daily tasks</h2>

                    <p>
                        Work due next across your projects.
                    </p>
                </div>

                <a href="{{ route('web.tasks.index') }}">
                    All tasks
                </a>

            </div>

            @forelse($tasks as $task)

                <a
                    class="row task-row"
                    href="{{ route('web.tasks.edit', $task->id) }}"
                >

                    <span
                        class="task-marker {{ $task->status === 'completed' ? 'done' : '' }}"
                    >
                        {{ $task->status === 'completed' ? '✓' : '' }}
                    </span>

                    <div class="row-main">

                        <b>
                            {{ $task->title }}
                        </b>

                        <small>
                            {{ $task->project?->name ?? 'Unassigned project' }}
                            ·
                            {{ $task->assignee?->name ?? 'Unassigned' }}
                            ·
                            {{ $task->due_date?->format('M j') ?? 'No due date' }}
                        </small>

                    </div>

                    <span class="badge badge-{{ $task->priority }}">
                        {{ $task->priority }}
                    </span>

                </a>

            @empty

                <p class="empty">
                    No tasks yet.

                    <a href="{{ route('web.tasks.create') }}">
                        Create a task
                    </a>

                    to start tracking work.
                </p>

            @endforelse

        </section>

        {{-- UPCOMING MILESTONES --}}
        <section class="card">

            <div class="card-heading">

                <div>
                    <h2>Upcoming milestones</h2>

                    <p>
                        Next dates on the delivery calendar.
                    </p>
                </div>

                <a href="{{ route('web.milestones.index') }}">
                    All milestones
                </a>

            </div>

            @forelse($milestones as $milestone)

                <div class="row">

                    <div class="row-main">

                        <b>
                            {{ $milestone->name }}
                        </b>

                        <small>
                            {{ $milestone->project?->name }}
                        </small>

                    </div>

                    <time>
                        {{ $milestone->due_date?->format('M j') }}
                    </time>

                </div>

            @empty

                <p class="empty">
                    No upcoming milestones yet.
                </p>

            @endforelse

        </section>

    </div>

    {{-- TEAM & RISKS --}}
    <div class="dashboard-grid lower-grid">

        {{-- TEAM SNAPSHOT --}}
        <section class="card team-snapshot">

            <div class="card-heading">

                <div>
                    <h2>Team snapshot</h2>

                    <p>
                        People currently assigned to project work.
                    </p>
                </div>

                <a href="{{ route('web.team.index') }}">
                    Manage team
                </a>

            </div>

            @forelse($teamMembers as $index => $resource)

                <div class="team-person">

                    @if($resource->user?->avatar)

                        <img
                            class="member-avatar"
                            src="{{ asset($resource->user->avatar) }}"
                            alt="{{ $resource->user->name }}"
                        >

                    @else

                        <span
                            class="member-avatar avatar-tone-{{ $index % 5 }}"
                            aria-hidden="true"
                        >
                            {{
                                strtoupper(
                                    collect(
                                        explode(
                                            ' ',
                                            $resource->user?->name ?? '?'
                                        )
                                    )
                                    ->map(
                                        fn ($part) => substr($part, 0, 1)
                                    )
                                    ->take(2)
                                    ->implode('')
                                )
                            }}
                        </span>

                    @endif

                    <div class="row-main">

                        <b>
                            {{ $resource->user?->name ?? 'Unknown user' }}
                        </b>

                        <small>
                            {{ $resource->user?->job_title ?: 'Team member' }}
                        </small>

                    </div>

                    <span class="team-workload">
                        {{ $resource->user?->assigned_tasks_count ?? 0 }}
                        tasks
                    </span>

                </div>

            @empty

                <p class="empty">
                    No project resources have been added yet.
                </p>

            @endforelse

        </section>

        {{-- OPEN RISKS --}}
        <section class="card">

            <div class="card-heading">

                <div>
                    <h2>Open risks</h2>

                    <p>
                        Priority items needing follow-up.
                    </p>
                </div>

                <a href="{{ route('web.risks.index') }}">
                    Risk register
                </a>

            </div>

            @forelse($risks as $risk)

                <a
                    class="row"
                    href="{{ route('web.risks.edit', $risk->id) }}"
                >

                    <div class="row-main">

                        <b>
                            {{ $risk->title }}
                        </b>

                        <small>
                            {{ $risk->project?->name ?? 'Portfolio risk' }}
                            ·
                            {{ $risk->category }}
                        </small>

                    </div>

                    <span class="badge badge-{{ $risk->impact }}">
                        {{ $risk->impact }} impact
                    </span>

                </a>

            @empty

                <p class="empty">
                    No open risks. Keep monitoring your portfolio.
                </p>

            @endforelse

        </section>

    </div>

    {{-- PROJECT SUMMARY --}}
    <section class="card project-overview project-summary">

        <div class="card-heading">

            <div>

                <h2>
                    Projects summary
                </h2>

                <p>
                    Current portfolio progress, work, resources, and delivery dates.
                </p>

            </div>

            <a href="{{ route('projects.index') }}">
                View all
            </a>

        </div>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>
                        <th>Project</th>
                        <th>Tasks</th>
                        <th>Progress</th>
                        <th>Resources</th>
                        <th>Status</th>
                        <th>Health</th>
                        <th>Deadline</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($projects as $project)

                        <tr>

                            <td>

                                <a href="{{ route('projects.show', $project) }}">
                                    {{ $project->name }}
                                </a>

                                <small>
                                    {{ $project->client ?: 'Internal project' }}
                                </small>

                            </td>

                            <td>

                                {{ $project->completed_tasks_count }}
                                /
                                {{ $project->tasks_count }}

                                <small>
                                    completed
                                </small>

                            </td>

                            <td>

                                <div class="progress-label">

                                    <div class="progress">

                                        <i
                                            style="width:{{ $project->progress }}%"
                                        ></i>

                                    </div>

                                    <span>
                                        {{ $project->progress }}%
                                    </span>

                                </div>

                            </td>

                            <td>

                                {{ $project->project_resources_count }}

                                <small>
                                    assigned
                                </small>

                            </td>

                            <td>

                                <span class="badge badge-{{ $project->status }}">
                                    {{ str_replace('-', ' ', $project->status) }}
                                </span>

                            </td>

                            <td>

                                @php
                                    $health = $project->health;
                                @endphp

                                <span
                                    class="badge badge-{{ str_replace('_', '-', $health['key']) }}"
                                    @if($health['reasons'])
                                        title="{{ implode('; ', $health['reasons']) }}"
                                    @endif
                                >
                                    {{ $health['label'] }}
                                </span>

                            </td>

                            <td>

                                {{ $project->end_date?->format('M j, Y') ?? 'Not scheduled' }}

                            </td>

                            <td>

                                <a
                                    class="table-action"
                                    href="{{ route('projects.edit', $project) }}"
                                >
                                    Edit
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                class="empty"
                                colspan="8"
                            >
                                No projects yet.

                                <a href="{{ route('projects.create') }}">
                                    Create your first project
                                </a>

                                to begin.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

    {{-- TASK SUMMARY --}}
    <section class="card task-summary">

        <div class="card-heading">

            <div>

                <h2>
                    Task summary
                </h2>

                <p>
                    Weekly task activity from Laravel records.
                </p>

            </div>

            <a href="{{ route('web.tasks.index') }}">
                View all
            </a>

        </div>

        <div class="task-rate-overview">

            <div>

                <h3>
                    Tasks completed rate
                </h3>

                <p>
                    Based on completed tasks in this workspace
                </p>

            </div>

            <div>

                <strong>
                    {{ $completionRate }}%
                </strong>

                <span>
                    {{ $completedTasks }} of {{ $taskCount }}
                </span>

            </div>

        </div>

        <div
            id="tasks-report"
            class="task-report-chart"
            aria-label="This week and last week task activity chart"
        ></div>

        <script
            id="tasks-report-data"
            type="application/json"
        >
            @json([
                'thisWeek' => $thisWeekTasks,
                'lastWeek' => $lastWeekTasks
            ])
        </script>

        <p class="chart-note">
            Activity uses task creation dates because completion timestamps are not stored.
        </p>

    </section>

    {{-- QUICK ACTIONS --}}
    <section class="quick-actions">

        <div>

            <h2>
                Quick actions
            </h2>

            <p>
                Start common project-management work.
            </p>

        </div>

        <div>

            <a
                class="button"
                href="{{ route('projects.create') }}"
            >
                New project
            </a>

            <a
                class="button secondary"
                href="{{ route('web.tasks.create') }}"
            >
                New task
            </a>

            <a
                class="button secondary"
                href="{{ route('web.sprints.create') }}"
            >
                New sprint
            </a>

            <a
                class="button secondary"
                href="{{ route('web.milestones.create') }}"
            >
                New milestone
            </a>

            <a
                class="button secondary"
                href="{{ route('analytics') }}"
            >
                View reports
            </a>

        </div>

    </section>

</div>

@endsection