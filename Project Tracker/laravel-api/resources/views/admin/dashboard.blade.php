@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<style>
    /* =====================================================
       ADMIN DASHBOARD - GLASS THEME
       Scoped under .ad-page so nothing else is affected.
    ===================================================== */

    .ad-page {
        --ad-text: #1e2a44;
        --ad-muted: #6a7690;
        --ad-glass: rgba(255, 255, 255, 0.55);
        --ad-glass-strong: rgba(255, 255, 255, 0.78);
        --ad-border: rgba(255, 255, 255, 0.8);
        --ad-line: rgba(30, 42, 68, 0.08);
        --ad-track: rgba(30, 42, 68, 0.09);
        --ad-shadow: 0 12px 38px rgba(49, 46, 129, 0.12);
        --ad-accent: #6366f1;

        position: relative;
        isolation: isolate;
        max-width: 1500px;
        margin: 0 auto;
        color: var(--ad-text);
    }

    body.dark .ad-page {
        --ad-text: #e9effd;
        --ad-muted: #9aa8c7;
        --ad-glass: rgba(255, 255, 255, 0.06);
        --ad-glass-strong: rgba(255, 255, 255, 0.11);
        --ad-border: rgba(255, 255, 255, 0.13);
        --ad-line: rgba(255, 255, 255, 0.09);
        --ad-track: rgba(255, 255, 255, 0.12);
        --ad-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
    }

    /* colour glows behind the glass */
    .ad-page::before {
        content: '';
        position: absolute;
        inset: -34px;
        z-index: -1;
        pointer-events: none;
        background:
            radial-gradient(560px 380px at 6% 4%, rgba(99, 102, 241, 0.26), transparent 70%),
            radial-gradient(520px 380px at 96% 28%, rgba(34, 211, 238, 0.2), transparent 70%),
            radial-gradient(560px 420px at 55% 72%, rgba(244, 114, 182, 0.14), transparent 70%),
            radial-gradient(460px 360px at 4% 92%, rgba(139, 92, 246, 0.18), transparent 70%);
    }

    body.dark .ad-page::before {
        background:
            radial-gradient(560px 380px at 6% 4%, rgba(99, 102, 241, 0.34), transparent 70%),
            radial-gradient(520px 380px at 96% 28%, rgba(34, 211, 238, 0.16), transparent 70%),
            radial-gradient(560px 420px at 55% 72%, rgba(217, 70, 239, 0.16), transparent 70%),
            radial-gradient(460px 360px at 4% 92%, rgba(59, 130, 246, 0.2), transparent 70%);
    }

    @keyframes ad-rise {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .ad-page > * { animation: ad-rise 0.55s ease both; }
    .ad-page > *:nth-child(2) { animation-delay: 0.05s; }
    .ad-page > *:nth-child(3) { animation-delay: 0.1s; }
    .ad-page > *:nth-child(4) { animation-delay: 0.15s; }
    .ad-page > *:nth-child(5) { animation-delay: 0.2s; }

    @media (prefers-reduced-motion: reduce) {
        .ad-page > * { animation: none; }
    }

    /* ---------- glass surface ---------- */

    .ad-glass {
        border: 1px solid var(--ad-border);
        border-radius: 20px;
        background: linear-gradient(145deg, var(--ad-glass-strong), var(--ad-glass));
        backdrop-filter: blur(18px) saturate(160%);
        -webkit-backdrop-filter: blur(18px) saturate(160%);
        box-shadow: var(--ad-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    body.dark .ad-glass {
        box-shadow: var(--ad-shadow), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    /* ---------- hero ---------- */

    .ad-hero {
        position: relative;
        overflow: hidden;
        margin-bottom: 26px;
        padding: 34px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 24px;
        color: #ffffff;
        background:
            radial-gradient(circle at 12% 0%, rgba(99, 102, 241, 0.5), transparent 45%),
            radial-gradient(circle at 92% 100%, rgba(34, 211, 238, 0.28), transparent 45%),
            linear-gradient(135deg, #111827 0%, #172554 48%, #312e81 100%);
        box-shadow: 0 22px 60px rgba(49, 46, 129, 0.35);
    }

    .ad-hero::after {
        content: '';
        position: absolute;
        right: -70px;
        bottom: -110px;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .ad-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 25px;
    }

    .ad-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 7px 13px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .ad-label span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #34d399;
        box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
    }

    .ad-hero h1 {
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -0.03em;
    }

    .ad-hero p {
        max-width: 650px;
        margin: 0;
        color: rgba(255, 255, 255, 0.72);
        font-size: 14px;
        line-height: 1.6;
    }

    .ad-hero-actions {
        display: flex;
        flex-shrink: 0;
        gap: 10px;
    }

    .ad-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .ad-btn:hover {
        transform: translateY(-2px);
    }

    .ad-btn-primary {
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 10px 28px rgba(99, 102, 241, 0.5);
    }

    .ad-btn-primary:hover {
        box-shadow: 0 14px 34px rgba(99, 102, 241, 0.65);
    }

    .ad-btn-secondary {
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    .ad-btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* ---------- stats ---------- */

    .ad-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 26px;
    }

    .ad-stat {
        position: relative;
        overflow: hidden;
        padding: 22px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .ad-stat::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 3px;
        background: var(--ad-bar, linear-gradient(90deg, #6366f1, #22d3ee));
    }

    .ad-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 48px rgba(49, 46, 129, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }

    .ad-stat:nth-child(1) { --ad-bar: linear-gradient(90deg, #6366f1, #818cf8); --ad-ico: linear-gradient(135deg, #6366f1, #8b5cf6); --ad-ico-glow: rgba(99, 102, 241, 0.4); }
    .ad-stat:nth-child(2) { --ad-bar: linear-gradient(90deg, #3b82f6, #22d3ee); --ad-ico: linear-gradient(135deg, #3b82f6, #22d3ee); --ad-ico-glow: rgba(59, 130, 246, 0.4); }
    .ad-stat:nth-child(3) { --ad-bar: linear-gradient(90deg, #10b981, #6ee7b7); --ad-ico: linear-gradient(135deg, #10b981, #34d399); --ad-ico-glow: rgba(16, 185, 129, 0.4); }
    .ad-stat:nth-child(4) { --ad-bar: linear-gradient(90deg, #f43f5e, #fb923c); --ad-ico: linear-gradient(135deg, #f43f5e, #fb923c); --ad-ico-glow: rgba(244, 63, 94, 0.4); }

    .ad-stat-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .ad-stat-title {
        margin: 0;
        color: var(--ad-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .ad-stat-value {
        margin: 8px 0 0;
        color: var(--ad-text);
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.04em;
    }

    .ad-stat-desc {
        color: var(--ad-muted);
        font-size: 12px;
    }

    .ad-icon {
        display: grid;
        flex: 0 0 auto;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 13px;
        color: #ffffff;
        background: var(--ad-ico, linear-gradient(135deg, #6366f1, #8b5cf6));
        box-shadow: 0 8px 20px var(--ad-ico-glow, rgba(99, 102, 241, 0.4));
    }

    .ad-icon svg {
        width: 20px;
        height: 20px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* ---------- grid + cards ---------- */

    .ad-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.8fr);
        gap: 22px;
        margin-bottom: 22px;
    }

    .ad-card {
        overflow: hidden;
    }

    .ad-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--ad-line);
    }

    .ad-card-header h2 {
        margin: 0;
        color: var(--ad-text);
        font-size: 16px;
        font-weight: 800;
    }

    .ad-card-header p {
        margin: 4px 0 0;
        color: var(--ad-muted);
        font-size: 12px;
    }

    .ad-link {
        padding: 6px 14px;
        border: 1px solid var(--ad-border);
        border-radius: 999px;
        background: var(--ad-glass-strong);
        color: var(--ad-accent);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        transition: background 0.2s ease, color 0.2s ease;
    }

    a.ad-link:hover {
        background: var(--ad-accent);
        color: #ffffff;
    }

    /* ---------- tables ---------- */

    .ad-table-wrap {
        overflow-x: auto;
    }

    .ad-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ad-table th {
        padding: 13px 22px;
        background: rgba(99, 102, 241, 0.08);
        color: var(--ad-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-align: left;
        text-transform: uppercase;
    }

    .ad-table td {
        padding: 15px 22px;
        border-top: 1px solid var(--ad-line);
        color: var(--ad-text);
        font-size: 13px;
    }

    .ad-table tbody tr {
        transition: background 0.2s ease;
    }

    .ad-table tbody tr:hover {
        background: var(--ad-glass-strong);
    }

    .ad-user {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .ad-avatar {
        display: grid;
        flex: 0 0 auto;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    .ad-user-name {
        color: var(--ad-text);
        font-weight: 700;
    }

    .ad-user-email {
        margin-top: 2px;
        color: var(--ad-muted);
        font-size: 11px;
    }

    /* ---------- badges ---------- */

    .ad-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 11px;
        border: 1px solid rgba(99, 102, 241, 0.25);
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.14);
        color: #4f46e5;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
    }

    .ad-badge.is-planning,
    .ad-badge.is-medium {
        border-color: rgba(245, 158, 11, 0.3);
        background: rgba(245, 158, 11, 0.16);
        color: #b45309;
    }

    .ad-badge.is-in-progress,
    .ad-badge.is-active {
        border-color: rgba(59, 130, 246, 0.3);
        background: rgba(59, 130, 246, 0.16);
        color: #1d4ed8;
    }

    .ad-badge.is-completed,
    .ad-badge.is-low {
        border-color: rgba(16, 185, 129, 0.3);
        background: rgba(16, 185, 129, 0.16);
        color: #047857;
    }

    .ad-badge.is-on-hold,
    .ad-badge.is-high {
        border-color: rgba(244, 63, 94, 0.3);
        background: rgba(244, 63, 94, 0.15);
        color: #be123c;
    }

    body.dark .ad-badge { color: #a5b4fc; }
    body.dark .ad-badge.is-planning,
    body.dark .ad-badge.is-medium { color: #fcd34d; }
    body.dark .ad-badge.is-in-progress,
    body.dark .ad-badge.is-active { color: #93c5fd; }
    body.dark .ad-badge.is-completed,
    body.dark .ad-badge.is-low { color: #6ee7b7; }
    body.dark .ad-badge.is-on-hold,
    body.dark .ad-badge.is-high { color: #fda4af; }

    /* ---------- system overview ---------- */

    .ad-overview {
        padding: 8px 20px 18px;
    }

    .ad-overview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 2px;
        border-bottom: 1px solid var(--ad-line);
    }

    .ad-overview-item:last-child {
        border-bottom: 0;
    }

    .ad-overview-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ad-overview-label {
        color: var(--ad-text);
        font-size: 13px;
        font-weight: 600;
    }

    .ad-overview-value {
        color: var(--ad-text);
        font-size: 18px;
        font-weight: 800;
    }

    .ad-overview-item:nth-child(1) { --ad-ico: linear-gradient(135deg, #6366f1, #8b5cf6); --ad-ico-glow: rgba(99, 102, 241, 0.35); }
    .ad-overview-item:nth-child(2) { --ad-ico: linear-gradient(135deg, #f59e0b, #fbbf24); --ad-ico-glow: rgba(245, 158, 11, 0.35); }
    .ad-overview-item:nth-child(3) { --ad-ico: linear-gradient(135deg, #f43f5e, #fb923c); --ad-ico-glow: rgba(244, 63, 94, 0.35); }
    .ad-overview-item:nth-child(4) { --ad-ico: linear-gradient(135deg, #3b82f6, #22d3ee); --ad-ico-glow: rgba(59, 130, 246, 0.35); }

    .ad-overview .ad-icon {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        box-shadow: 0 6px 16px var(--ad-ico-glow);
    }

    .ad-overview .ad-icon svg {
        width: 17px;
        height: 17px;
    }

    /* ---------- progress ---------- */

    .ad-progress {
        min-width: 130px;
    }

    .ad-bar {
        height: 7px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--ad-track);
    }

    .ad-bar i {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #6366f1, #22d3ee);
        box-shadow: 0 0 10px rgba(34, 211, 238, 0.5);
    }

    .ad-progress small {
        display: block;
        margin-top: 5px;
        color: var(--ad-muted);
        font-size: 10px;
    }

    .ad-empty {
        padding: 35px;
        color: var(--ad-muted);
        font-size: 13px;
        text-align: center;
    }

    /* ---------- quick actions ---------- */

    .ad-actions {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        padding: 20px;
    }

    .ad-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid var(--ad-border);
        border-radius: 14px;
        background: var(--ad-glass);
        color: var(--ad-text);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .ad-action:hover {
        transform: translateY(-3px);
        background: var(--ad-glass-strong);
        box-shadow: 0 12px 28px rgba(99, 102, 241, 0.2);
    }

    .ad-action:nth-child(1) { --ad-ico: linear-gradient(135deg, #6366f1, #8b5cf6); --ad-ico-glow: rgba(99, 102, 241, 0.35); }
    .ad-action:nth-child(2) { --ad-ico: linear-gradient(135deg, #3b82f6, #22d3ee); --ad-ico-glow: rgba(59, 130, 246, 0.35); }
    .ad-action:nth-child(3) { --ad-ico: linear-gradient(135deg, #10b981, #34d399); --ad-ico-glow: rgba(16, 185, 129, 0.35); }
    .ad-action:nth-child(4) { --ad-ico: linear-gradient(135deg, #f43f5e, #fb923c); --ad-ico-glow: rgba(244, 63, 94, 0.35); }

    .ad-action .ad-icon {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        box-shadow: 0 6px 16px var(--ad-ico-glow);
    }

    .ad-action .ad-icon svg {
        width: 17px;
        height: 17px;
    }

    /* ---------- responsive ---------- */

    @media (max-width: 1100px) {
        .ad-stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .ad-grid {
            grid-template-columns: 1fr;
        }

        .ad-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 700px) {
        .ad-hero {
            padding: 24px;
        }

        .ad-hero-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .ad-hero h1 {
            font-size: 25px;
        }

        .ad-stats,
        .ad-actions {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="ad-page">

    {{-- =========================
         HERO
    ========================== --}}
    <section class="ad-hero">

        <div class="ad-hero-content">

            <div>
                <div class="ad-label">
                    <span></span>
                    Administrator Access
                </div>

                <h1>Admin Dashboard</h1>

                <p>
                    Workspace overview of users, projects, tasks
                    and system activity.
                </p>
            </div>

            <div class="ad-hero-actions">

                <a href="{{ route('dashboard') }}" class="ad-btn ad-btn-secondary">
                    &larr; Workspace
                </a>

                <a href="{{ route('projects.index') }}" class="ad-btn ad-btn-primary">
                    View Projects &rarr;
                </a>

            </div>

        </div>

    </section>


    {{-- =========================
         STATISTICS
    ========================== --}}
    <section class="ad-stats">

        <div class="ad-glass ad-stat">
            <div class="ad-stat-top">
                <div>
                    <p class="ad-stat-title">Total Users</p>
                    <div class="ad-stat-value">{{ $userCount }}</div>
                </div>

                <div class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8"/><path d="M18 14.2A6.5 6.5 0 0 1 21.5 20"/></svg>
                </div>
            </div>

            <div class="ad-stat-desc">
                {{ $adminCount }} administrator {{ \Illuminate\Support\Str::plural('account', $adminCount) }}
            </div>
        </div>

        <div class="ad-glass ad-stat">
            <div class="ad-stat-top">
                <div>
                    <p class="ad-stat-title">Total Projects</p>
                    <div class="ad-stat-value">{{ $projectCount }}</div>
                </div>

                <div class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                </div>
            </div>

            <div class="ad-stat-desc">
                {{ $activeProjectCount }} currently active
            </div>
        </div>

        <div class="ad-glass ad-stat">
            <div class="ad-stat-top">
                <div>
                    <p class="ad-stat-title">Completed Projects</p>
                    <div class="ad-stat-value">{{ $completedProjectCount }}</div>
                </div>

                <div class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.5 2.5L16 9.5"/></svg>
                </div>
            </div>

            <div class="ad-stat-desc">
                Projects successfully completed
            </div>
        </div>

        <div class="ad-glass ad-stat">
            <div class="ad-stat-top">
                <div>
                    <p class="ad-stat-title">Completed Tasks</p>
                    <div class="ad-stat-value">{{ $completedTaskCount }}</div>
                </div>

                <div class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="4"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                </div>
            </div>

            <div class="ad-stat-desc">
                Out of {{ $taskCount }} total tasks
            </div>
        </div>

    </section>


    {{-- =========================
         USERS + SYSTEM OVERVIEW
    ========================== --}}
    <section class="ad-grid">

        {{-- RECENT USERS --}}
        <div class="ad-glass ad-card">

            <div class="ad-card-header">
                <div>
                    <h2>Recent Users</h2>
                    <p>Latest accounts added to the workspace</p>
                </div>

                <span class="ad-link">
                    {{ $userCount }} users
                </span>
            </div>

            <div class="ad-table-wrap">

                @if($recentUsers->count())

                    <table class="ad-table">

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
                                    $initials = collect(explode(' ', trim($user->name)))
                                        ->filter()
                                        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                @endphp

                                <tr>

                                    <td>
                                        <div class="ad-user">

                                            <div class="ad-avatar">
                                                {{ $initials ?: 'U' }}
                                            </div>

                                            <div>
                                                <div class="ad-user-name">{{ $user->name }}</div>
                                                <div class="ad-user-email">{{ $user->email }}</div>
                                            </div>

                                        </div>
                                    </td>

                                    <td>
                                        <span class="ad-badge">
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

                    <div class="ad-empty">
                        No users found.
                    </div>

                @endif

            </div>

        </div>


        {{-- SYSTEM OVERVIEW --}}
        <div class="ad-glass ad-card">

            <div class="ad-card-header">
                <div>
                    <h2>System Overview</h2>
                    <p>Current workspace activity</p>
                </div>
            </div>

            <div class="ad-overview">

                <div class="ad-overview-item">
                    <div class="ad-overview-left">
                        <div class="ad-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9z"/><path d="M9 12h6M9 16h4"/></svg>
                        </div>
                        <span class="ad-overview-label">Total Tasks</span>
                    </div>

                    <strong class="ad-overview-value">{{ $taskCount }}</strong>
                </div>

                <div class="ad-overview-item">
                    <div class="ad-overview-left">
                        <div class="ad-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 3L5 14h6l-1 7 8-11h-6z"/></svg>
                        </div>
                        <span class="ad-overview-label">Active Sprints</span>
                    </div>

                    <strong class="ad-overview-value">{{ $activeSprintCount }}</strong>
                </div>

                <div class="ad-overview-item">
                    <div class="ad-overview-left">
                        <div class="ad-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9.5 16.5H2.5z"/><path d="M12 10v4"/><path d="M12 17.2v.01"/></svg>
                        </div>
                        <span class="ad-overview-label">Open Risks</span>
                    </div>

                    <strong class="ad-overview-value">{{ $openRiskCount }}</strong>
                </div>

                <div class="ad-overview-item">
                    <div class="ad-overview-left">
                        <div class="ad-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19c0-6 4-11 14-14-1 10-6 14-12 14"/><path d="M9 15l-4 4"/></svg>
                        </div>
                        <span class="ad-overview-label">Active Projects</span>
                    </div>

                    <strong class="ad-overview-value">{{ $activeProjectCount }}</strong>
                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         RECENT PROJECTS
    ========================== --}}
    <section class="ad-glass ad-card" style="margin-bottom: 22px;">

        <div class="ad-card-header">

            <div>
                <h2>Recent Projects</h2>
                <p>Latest projects in your workspace</p>
            </div>

            <a href="{{ route('projects.index') }}" class="ad-link">
                View all &rarr;
            </a>

        </div>

        <div class="ad-table-wrap">

            @if($recentProjects->count())

                <table class="ad-table">

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

                            @php
                                $statusSlug = \Illuminate\Support\Str::slug($project->status ?? 'planning');
                                $prioritySlug = \Illuminate\Support\Str::slug($project->priority ?? 'medium');
                                $progress = min(100, max(0, (int) ($project->progress ?? 0)));
                            @endphp

                            <tr>

                                <td>
                                    <strong>{{ $project->name }}</strong>
                                </td>

                                <td>
                                    <span class="ad-badge is-{{ $statusSlug }}">
                                        {{ str_replace('-', ' ', $project->status ?? 'planning') }}
                                    </span>
                                </td>

                                <td>
                                    <span class="ad-badge is-{{ $prioritySlug }}">
                                        {{ $project->priority ?? 'medium' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="ad-progress">

                                        <div class="ad-bar">
                                            <i style="width: {{ $progress }}%;"></i>
                                        </div>

                                        <small>{{ $progress }}% complete</small>

                                    </div>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <div class="ad-empty">
                    No projects found.
                </div>

            @endif

        </div>

    </section>


    {{-- =========================
         QUICK ACTIONS
    ========================== --}}
    <section class="ad-glass ad-card">

        <div class="ad-card-header">

            <div>
                <h2>Quick Actions</h2>
                <p>Common administrator tasks</p>
            </div>

        </div>

        <div class="ad-actions">

            <a href="{{ route('projects.create') }}" class="ad-action">
                <span class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                Create Project
            </a>

            <a href="{{ route('projects.index') }}" class="ad-action">
                <span class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                </span>
                Manage Projects
            </a>

            <a href="{{ route('web.tasks.index') }}" class="ad-action">
                <span class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="4"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                </span>
                Manage Tasks
            </a>

            <a href="{{ route('analytics') }}" class="ad-action">
                <span class="ad-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>
                </span>
                View Analytics
            </a>

        </div>

    </section>

</div>

@endsection