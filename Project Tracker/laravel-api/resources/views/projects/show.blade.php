@extends('layouts.app')

@section('title', $project->name)

@section('content')

@php
    $progress = max(0, min(100, (int) $project->progress));

    $budget = (float) $project->budget;
    $spent  = (float) $project->spent;
    $budgetUsed = $budget > 0 ? min(100, round(($spent / $budget) * 100)) : 0;

    $daysLeft = $project->end_date
        ? (int) now()->startOfDay()->diffInDays($project->end_date->copy()->startOfDay(), false)
        : null;

    $statusSlug   = \Illuminate\Support\Str::slug($project->status ?? 'planning');
    $prioritySlug = \Illuminate\Support\Str::slug($project->priority ?? 'medium');

    // team members / invitations
    $me = auth()->user();

    $canManageTeam = $me && (
        $me->role === 'admin'
        || $me->role === 'project-manager'
        || $project->owner_id === $me->id
    );

    $members = $project->members()->orderBy('name')->get();

    $availableUsers = $canManageTeam
        ? \App\Models\User::query()->whereNotIn('id', $members->pluck('id'))->orderBy('name')->get()
        : collect();

    $joinLinks = $canManageTeam
        ? [
            'member' => \Illuminate\Support\Facades\URL::signedRoute('projects.join', ['project' => $project, 'role' => 'member']),
            'developer' => \Illuminate\Support\Facades\URL::signedRoute('projects.join', ['project' => $project, 'role' => 'developer']),
        ]
        : [];

    // Real project health is derived from schedule, tasks and budget data.
    $health = $project->health;
    $healthReasons = $health['reasons'] ?? [];

    $taskCount = (int) ($project->tasks_count ?? 0);
    $completedTaskCount = (int) ($project->completed_tasks_count ?? 0);
    $overdueTaskCount = (int) ($project->overdue_tasks_count ?? 0);
    $milestoneCount = (int) ($project->milestones_count ?? 0);
    $upcomingMilestoneCount = (int) ($project->upcoming_milestones_count ?? 0);
    $openRiskCount = (int) ($project->open_risks_count ?? 0);
    $stakeholderCount = (int) ($project->stakeholders_count ?? 0);
    $documentCount = (int) ($project->documents_count ?? 0);
    $budgetItemCount = (int) ($project->budget_items_count ?? 0);

    $remainingBudget = max(0, $budget - $spent);
    $taskCompletion = $taskCount > 0 ? (int) round(($completedTaskCount / $taskCount) * 100) : 0;
    $healthSlug = \Illuminate\Support\Str::slug($health['label'] ?? 'On Track');
@endphp

<style>
    /* =====================================================
       PROJECT DETAILS - GLASSMORPHISM
       (everything is scoped under .pd-page so no other
       page in the app is affected)
    ===================================================== */

    .pd-page {
        --pd-text: #1e2a44;
        --pd-muted: #64708a;
        --pd-glass: rgba(255, 255, 255, 0.52);
        --pd-glass-strong: rgba(255, 255, 255, 0.72);
        --pd-border: rgba(255, 255, 255, 0.75);
        --pd-line: rgba(30, 42, 68, 0.09);
        --pd-track: rgba(30, 42, 68, 0.09);
        --pd-shadow: 0 12px 40px rgba(49, 46, 129, 0.14);
        --pd-accent: #6366f1;
        --pd-accent-2: #22d3ee;

        position: relative;
        isolation: isolate;
        color: var(--pd-text);
    }

    body.dark .pd-page {
        --pd-text: #e9effd;
        --pd-muted: #9aa8c7;
        --pd-glass: rgba(255, 255, 255, 0.07);
        --pd-glass-strong: rgba(255, 255, 255, 0.12);
        --pd-border: rgba(255, 255, 255, 0.14);
        --pd-line: rgba(255, 255, 255, 0.1);
        --pd-track: rgba(255, 255, 255, 0.12);
        --pd-shadow: 0 16px 50px rgba(0, 0, 0, 0.4);
    }

    /* colourful glow behind the glass so the blur is visible */
    .pd-page::before {
        content: '';
        position: absolute;
        inset: -34px;
        z-index: -1;
        pointer-events: none;
        background:
            radial-gradient(520px 340px at 8% 6%, rgba(99, 102, 241, 0.34), transparent 70%),
            radial-gradient(480px 360px at 92% 18%, rgba(34, 211, 238, 0.28), transparent 70%),
            radial-gradient(520px 380px at 70% 92%, rgba(244, 114, 182, 0.22), transparent 70%),
            radial-gradient(420px 320px at 12% 85%, rgba(139, 92, 246, 0.24), transparent 70%);
    }

    body.dark .pd-page::before {
        background:
            radial-gradient(520px 340px at 8% 6%, rgba(99, 102, 241, 0.4), transparent 70%),
            radial-gradient(480px 360px at 92% 18%, rgba(34, 211, 238, 0.22), transparent 70%),
            radial-gradient(520px 380px at 70% 92%, rgba(217, 70, 239, 0.22), transparent 70%),
            radial-gradient(420px 320px at 12% 85%, rgba(59, 130, 246, 0.26), transparent 70%);
    }

    /* ---------- glass surface ---------- */

    .pd-glass {
        background: linear-gradient(145deg, var(--pd-glass-strong), var(--pd-glass));
        border: 1px solid var(--pd-border);
        border-radius: 20px;
        backdrop-filter: blur(20px) saturate(160%);
        -webkit-backdrop-filter: blur(20px) saturate(160%);
        box-shadow:
            var(--pd-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.55);
    }

    body.dark .pd-glass {
        box-shadow:
            var(--pd-shadow),
            inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    /* ---------- header ---------- */

    .pd-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .pd-head h1 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 800;
        letter-spacing: -0.6px;
        color: var(--pd-text);
    }

    .pd-head-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        color: var(--pd-muted);
        font-size: 14px;
    }

    .pd-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pd-actions form {
        margin: 0;
    }

    .pd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 18px;
        border: 1px solid var(--pd-border);
        border-radius: 12px;
        background: var(--pd-glass-strong);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: var(--pd-text);
        font: inherit;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .pd-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 22px rgba(99, 102, 241, 0.22);
    }

    .pd-btn-primary {
        border-color: rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
    }

    .pd-btn-primary:hover {
        box-shadow: 0 12px 30px rgba(99, 102, 241, 0.55);
    }

    .pd-btn-danger {
        color: #e11d48;
        border-color: rgba(225, 29, 72, 0.35);
        background: rgba(225, 29, 72, 0.1);
    }

    .pd-btn-danger:hover {
        background: rgba(225, 29, 72, 0.9);
        color: #ffffff;
        box-shadow: 0 8px 22px rgba(225, 29, 72, 0.35);
    }

    /* ---------- badges ---------- */

    .pd-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border: 1px solid transparent;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
        background: rgba(99, 102, 241, 0.14);
        color: #4f46e5;
        border-color: rgba(99, 102, 241, 0.25);
    }

    .pd-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 8px currentColor;
    }

    .pd-badge.is-planning,
    .pd-badge.is-pending,
    .pd-badge.is-medium {
        background: rgba(245, 158, 11, 0.15);
        color: #b45309;
        border-color: rgba(245, 158, 11, 0.3);
    }

    .pd-badge.is-in-progress,
    .pd-badge.is-active {
        background: rgba(59, 130, 246, 0.15);
        color: #1d4ed8;
        border-color: rgba(59, 130, 246, 0.3);
    }

    .pd-badge.is-completed,
    .pd-badge.is-done,
    .pd-badge.is-low {
        background: rgba(16, 185, 129, 0.15);
        color: #047857;
        border-color: rgba(16, 185, 129, 0.3);
    }

    .pd-badge.is-on-hold,
    .pd-badge.is-blocked,
    .pd-badge.is-high {
        background: rgba(244, 63, 94, 0.14);
        color: #be123c;
        border-color: rgba(244, 63, 94, 0.3);
    }

    body.dark .pd-badge { color: #a5b4fc; }
    body.dark .pd-badge.is-planning,
    body.dark .pd-badge.is-pending,
    body.dark .pd-badge.is-medium { color: #fcd34d; }
    body.dark .pd-badge.is-in-progress,
    body.dark .pd-badge.is-active { color: #93c5fd; }
    body.dark .pd-badge.is-completed,
    body.dark .pd-badge.is-done,
    body.dark .pd-badge.is-low { color: #6ee7b7; }
    body.dark .pd-badge.is-on-hold,
    body.dark .pd-badge.is-blocked,
    body.dark .pd-badge.is-high { color: #fda4af; }

    /* ---------- stat tiles ---------- */

    .pd-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 20px;
    }

    .pd-stat {
        padding: 20px 22px;
    }

    .pd-stat span {
        display: block;
        color: var(--pd-muted);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
    }

    .pd-stat strong {
        display: block;
        margin-top: 10px;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--pd-text);
    }

    .pd-stat small {
        display: block;
        margin-top: 4px;
        color: var(--pd-muted);
        font-size: 12px;
    }

    /* ---------- main grid ---------- */

    .pd-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 20px;
        align-items: start;
    }

    .pd-col {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }

    .pd-card {
        padding: 26px;
    }

    .pd-card h2 {
        margin: 0 0 14px;
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.2px;
        color: var(--pd-text);
    }

    .pd-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .pd-card-head h2 {
        margin: 0;
    }

    .pd-desc {
        margin: 0;
        color: var(--pd-muted);
        font-size: 15px;
        line-height: 1.75;
        white-space: pre-line;
    }

    /* ---------- details list ---------- */

    .pd-details {
        display: grid;
        grid-template-columns: 110px 1fr;
        gap: 0;
        margin: 0;
    }

    .pd-details dt,
    .pd-details dd {
        margin: 0;
        padding: 14px 0;
        border-top: 1px solid var(--pd-line);
    }

    .pd-details dt:first-of-type,
    .pd-details dt:first-of-type + dd {
        border-top: 0;
        padding-top: 4px;
    }

    .pd-details dt {
        color: var(--pd-muted);
        font-size: 13px;
    }

    .pd-details dd {
        color: var(--pd-text);
        font-weight: 600;
        font-size: 14px;
    }

    /* ---------- progress ---------- */

    .pd-progress-wrap {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .pd-ring {
        --p: 0;
        position: relative;
        flex: 0 0 auto;
        width: 118px;
        height: 118px;
        border-radius: 50%;
        background: conic-gradient(
            var(--pd-accent) calc(var(--p) * 1%),
            var(--pd-track) 0
        );
        filter: drop-shadow(0 0 14px rgba(99, 102, 241, 0.45));
    }

    .pd-ring::before {
        content: '';
        position: absolute;
        inset: 11px;
        border-radius: 50%;
        background: var(--pd-glass-strong);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    body.dark .pd-ring::before {
        background: #1c263a;
    }

    .pd-ring b {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--pd-text);
    }

    .pd-progress-text {
        flex: 1;
        min-width: 0;
    }

    .pd-progress-text p {
        margin: 0 0 14px;
        color: var(--pd-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .pd-bar {
        height: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--pd-track);
    }

    .pd-bar i {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--pd-accent), var(--pd-accent-2));
        box-shadow: 0 0 12px rgba(34, 211, 238, 0.55);
    }

    .pd-bar-label {
        display: flex;
        justify-content: space-between;
        margin-top: 8px;
        color: var(--pd-muted);
        font-size: 12px;
    }

    /* ---------- tasks ---------- */

    .pd-task {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 16px;
        margin-top: 10px;
        border: 1px solid var(--pd-border);
        border-radius: 14px;
        background: var(--pd-glass);
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .pd-task:hover {
        transform: translateX(3px);
        background: var(--pd-glass-strong);
    }

    .pd-task b {
        display: block;
        color: var(--pd-text);
        font-size: 14px;
    }

    .pd-task small {
        display: block;
        margin-top: 3px;
        color: var(--pd-muted);
        font-size: 12px;
    }

    .pd-empty {
        margin: 6px 0 18px;
        padding: 30px 16px;
        border: 1px dashed var(--pd-border);
        border-radius: 14px;
        text-align: center;
        color: var(--pd-muted);
        background: var(--pd-glass);
    }

    /* ---------- team members ---------- */

    .pd-count {
        padding: 3px 10px;
        border-radius: 999px;
        background: var(--pd-glass-strong);
        border: 1px solid var(--pd-border);
        color: var(--pd-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .pd-member {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        margin-top: 10px;
        border: 1px solid var(--pd-border);
        border-radius: 14px;
        background: var(--pd-glass);
    }

    .pd-avatar {
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    .pd-member-info {
        flex: 1;
        min-width: 0;
    }

    .pd-member-info b {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--pd-text);
        font-size: 14px;
    }

    .pd-member-info small {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--pd-muted);
        font-size: 12px;
    }

    .pd-remove {
        border: 0;
        background: transparent;
        color: var(--pd-muted);
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 8px;
    }

    .pd-remove:hover {
        background: rgba(225, 29, 72, 0.14);
        color: #e11d48;
    }

    .pd-invite {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid var(--pd-line);
    }

    .pd-invite h3 {
        margin: 0 0 10px;
        font-size: 14px;
        font-weight: 800;
        color: var(--pd-text);
    }

    .pd-invite-row {
        display: grid;
        grid-template-columns: 1fr 140px;
        gap: 10px;
        margin-bottom: 10px;
    }

    .pd-invite select {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--pd-border);
        border-radius: 12px;
        background: var(--pd-glass-strong);
        color: var(--pd-text);
        font: inherit;
        outline: none;
    }

    .pd-invite select:focus {
        border-color: var(--pd-accent);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .pd-invite select option {
        color: #1e2a44;
    }

    .pd-invite p {
        margin: 0;
        color: var(--pd-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .pd-invite a {
        color: var(--pd-accent);
        font-weight: 700;
    }

    .pd-invite input[type="email"],
    .pd-invite input[type="text"] {
        width: 100%;
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid var(--pd-border);
        border-radius: 12px;
        background: var(--pd-glass-strong);
        color: var(--pd-text);
        font: inherit;
        outline: none;
        box-sizing: border-box;
    }

    .pd-invite input:focus {
        border-color: var(--pd-accent);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }

    .pd-other-email {
        margin-bottom: 10px;
    }

    .pd-share {
        margin-top: 18px;
        padding-top: 18px;
        border-top: 1px solid var(--pd-line);
    }

    .pd-share-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pd-share-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
        padding: 10px;
        border: 1px solid var(--pd-border);
        border-radius: 14px;
        background: var(--pd-glass-strong);
    }

    .pd-share-menu[hidden] {
        display: none;
    }

    .pd-share-menu a,
    .pd-share-menu button {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 0 14px;
        border: 1px solid var(--pd-line);
        border-radius: 999px;
        background: transparent;
        color: var(--pd-text);
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .pd-share-menu a:hover,
    .pd-share-menu button:hover {
        border-color: var(--pd-accent);
        color: var(--pd-accent);
    }

    /* ---------- responsive ---------- */

    @media (max-width: 1100px) {
        .pd-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .pd-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .pd-head {
            flex-direction: column;
        }

        .pd-actions {
            width: 100%;
        }

        .pd-actions > *,
        .pd-actions .pd-btn {
            flex: 1;
        }

        .pd-stats {
            grid-template-columns: 1fr;
        }

        .pd-progress-wrap {
            flex-direction: column;
            align-items: flex-start;
        }

        .pd-details {
            grid-template-columns: 96px 1fr;
        }
    }

    /* ---------- workspace enhancements ---------- */
    .pd-health {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 16px;
        padding: 14px 16px;
        border: 1px solid var(--pd-line);
        border-radius: 14px;
        background: rgba(255,255,255,.28);
    }
    body.dark .pd-health { background: rgba(255,255,255,.04); }
    .pd-health-dot { width: 10px; height: 10px; margin-top: 5px; border-radius: 50%; background: currentColor; box-shadow: 0 0 12px currentColor; flex: 0 0 auto; }
    .pd-health.is-on-track { color: #047857; }
    .pd-health.is-at-risk { color: #b45309; }
    .pd-health.is-critical { color: #be123c; }
    .pd-health.is-completed { color: #047857; }
    .pd-health strong { display:block; font-size:14px; color:var(--pd-text); }
    .pd-health p { margin:4px 0 0; color:var(--pd-muted); font-size:12px; line-height:1.5; }
    .pd-workspace-nav { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 20px; }
    .pd-workspace-nav a { display:inline-flex; align-items:center; min-height:36px; padding:0 12px; border:1px solid var(--pd-line); border-radius:10px; color:var(--pd-muted); background:rgba(255,255,255,.25); text-decoration:none; font-size:12px; font-weight:700; }
    .pd-workspace-nav a:hover { color:var(--pd-text); border-color:rgba(99,102,241,.35); background:rgba(99,102,241,.08); }
    .pd-mini-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-top:16px; }
    .pd-mini-card { padding:14px; border:1px solid var(--pd-line); border-radius:14px; background:rgba(255,255,255,.24); }
    body.dark .pd-mini-card { background:rgba(255,255,255,.035); }
    .pd-mini-card span { display:block; color:var(--pd-muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    .pd-mini-card strong { display:block; margin-top:5px; color:var(--pd-text); font-size:20px; }
    .pd-mini-card small { display:block; margin-top:3px; color:var(--pd-muted); font-size:11px; }
    .pd-insight-list { margin:12px 0 0; padding:0; list-style:none; }
    .pd-insight-list li { padding:9px 0; border-bottom:1px solid var(--pd-line); color:var(--pd-muted); font-size:13px; }
    .pd-insight-list li:last-child { border-bottom:0; }
    .pd-insight-list b { color:var(--pd-text); }
    .pd-link-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; margin-top:14px; }
    .pd-link-card { display:block; padding:13px 14px; border:1px solid var(--pd-line); border-radius:12px; color:var(--pd-text); text-decoration:none; background:rgba(255,255,255,.22); }
    .pd-link-card:hover { border-color:rgba(99,102,241,.35); transform:translateY(-1px); }
    .pd-link-card strong { display:block; font-size:13px; }
    .pd-link-card span { display:block; margin-top:3px; color:var(--pd-muted); font-size:11px; }
    @media (max-width: 900px) { .pd-mini-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width: 640px) { .pd-mini-grid,.pd-link-grid { grid-template-columns:1fr; } .pd-workspace-nav { overflow-x:auto; flex-wrap:nowrap; padding-bottom:4px; } .pd-workspace-nav a { white-space:nowrap; } }

</style>


<div class="pd-page">

    <div class="pd-head">
        <div>
            <h1>{{ $project->name }}</h1>
            <div class="pd-head-meta">
                <span>{{ $project->team ?: 'Project workspace' }}</span>
                <span class="pd-badge is-{{ $statusSlug }}">{{ $project->status ?: 'Planning' }}</span>
                <span class="pd-badge is-{{ $prioritySlug }}">{{ $project->priority ?: 'Medium' }}</span>
                <span class="pd-badge is-{{ $healthSlug }}">{{ $health['label'] ?? 'On Track' }}</span>
            </div>
            @if($healthReasons)
                <div class="pd-health is-{{ $health['slug'] ?? 'on-track' }}">
                    <span class="pd-health-dot"></span>
                    <div>
                        <strong>{{ $health['label'] ?? 'On Track' }}</strong>
                        <p>{{ implode(' • ', $healthReasons) }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="pd-actions">
            <a class="pd-btn" href="{{ route('projects.report', $project) }}" target="_blank" rel="noopener">Report</a>
            <a class="pd-btn" href="{{ route('projects.edit', $project) }}">Edit</a>
            <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?')">
                @csrf
                @method('DELETE')
                <button class="pd-btn pd-btn-danger" type="submit">Delete</button>
            </form>
        </div>
    </div>

    <nav class="pd-workspace-nav" aria-label="Project workspace">
        <a href="#overview">Overview</a>
        <a href="#tasks">Tasks</a>
        <a href="#team">Team</a>
        <a href="#financials">Financials</a>
        <a href="{{ route('web.milestones.index') }}">Milestones</a>
        <a href="{{ route('web.risks.index') }}">Risks</a>
        <a href="{{ route('web.documents.index') }}">Documents</a>
        <a href="{{ route('web.stakeholders.index') }}">Stakeholders</a>
    </nav>

    <div class="pd-stats">
        <div class="pd-glass pd-stat">
            <span>Progress</span>
            <strong>{{ $progress }}%</strong>
            <small>Project completion</small>
        </div>
        <div class="pd-glass pd-stat">
            <span>Tasks</span>
            <strong>{{ $completedTaskCount }}/{{ $taskCount }}</strong>
            <small>{{ $taskCompletion }}% completed</small>
        </div>
        <div class="pd-glass pd-stat">
            <span>Budget</span>
            <strong>${{ number_format($budget, 2) }}</strong>
            <small>{{ $budgetUsed }}% used</small>
        </div>
        <div class="pd-glass pd-stat">
            <span>Remaining</span>
            <strong>${{ number_format($remainingBudget, 2) }}</strong>
            <small>{{ $daysLeft === null ? 'No end date' : ($daysLeft < 0 ? abs($daysLeft).' days overdue' : $daysLeft.' days left') }}</small>
        </div>
    </div>

    <div class="pd-mini-grid">
        <div class="pd-mini-card"><span>Overdue tasks</span><strong>{{ $overdueTaskCount }}</strong><small>Incomplete and past due</small></div>
        <div class="pd-mini-card"><span>Milestones</span><strong>{{ $milestoneCount }}</strong><small>{{ $upcomingMilestoneCount }} upcoming</small></div>
        <div class="pd-mini-card"><span>Open risks</span><strong>{{ $openRiskCount }}</strong><small>Requires attention</small></div>
        <div class="pd-mini-card"><span>Stakeholders</span><strong>{{ $stakeholderCount }}</strong><small>{{ $documentCount }} documents</small></div>
    </div>

    <div class="pd-grid">
        <div class="pd-col">
            <section class="pd-glass pd-card" id="overview">
                <div class="pd-card-head">
                    <h2>Project overview</h2>
                    <span class="pd-count">{{ $health['label'] ?? 'On Track' }}</span>
                </div>
                <p class="pd-desc">{{ $project->description ?: 'No description provided.' }}</p>

                <div class="pd-mini-grid" style="grid-template-columns:repeat(2,minmax(0,1fr));">
                    <div class="pd-mini-card">
                        <span>Schedule</span>
                        <strong>{{ $project->start_date?->format('M j') ?? '—' }} – {{ $project->end_date?->format('M j, Y') ?? '—' }}</strong>
                        <small>{{ $daysLeft === null ? 'No end date set' : ($daysLeft < 0 ? 'Project end date passed' : 'Current delivery window') }}</small>
                    </div>
                    <div class="pd-mini-card">
                        <span>Budget items</span>
                        <strong>{{ $budgetItemCount }}</strong>
                        <small>Tracked budget categories</small>
                    </div>
                </div>

                @if($healthReasons)
                    <ul class="pd-insight-list">
                        @foreach($healthReasons as $reason)
                            <li><b>Health signal:</b> {{ $reason }}</li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="pd-glass pd-card" id="tasks">
                <div class="pd-card-head">
                    <h2>Recent tasks</h2>
                    <span class="pd-count">{{ $taskCount }}</span>
                </div>

                @forelse($project->tasks as $task)
                    @php
                        $taskDue = $task->due_date ? $task->due_date->format('M j, Y') : 'No due date';
                        $taskOverdue = $task->due_date && $task->due_date->isPast() && ($task->status ?? '') !== 'completed';
                    @endphp
                    <div class="pd-task">
                        <div>
                            <b>{{ $task->title }}</b>
                            <small>{{ $task->priority ? ucfirst($task->priority).' • ' : '' }}{{ $taskDue }}</small>
                        </div>
                        <span class="pd-badge {{ $taskOverdue ? 'is-high' : '' }}">
                            {{ $taskOverdue ? 'Overdue' : ($task->status ?: 'Pending') }}
                        </span>
                    </div>
                @empty
                    <div class="pd-empty">No tasks on this project yet.</div>
                @endforelse

                <div style="margin-top:18px;display:flex;gap:10px;flex-wrap:wrap;">
                    <a class="pd-btn pd-btn-primary" href="{{ route('web.tasks.create') }}">+ Add task</a>
                    <a class="pd-btn" href="{{ route('web.tasks.index') }}">View all tasks</a>
                </div>
            </section>

            <section class="pd-glass pd-card" id="financials">
                <div class="pd-card-head">
                    <h2>Financial summary</h2>
                    <span class="pd-count">{{ $budgetUsed }}% used</span>
                </div>
                <div class="pd-details">
                    <dt>Original project budget</dt><dd>${{ number_format($budget, 2) }}</dd>
                    <dt>Spent</dt><dd>${{ number_format($spent, 2) }}</dd>
                    <dt>Remaining</dt><dd>${{ number_format($remainingBudget, 2) }}</dd>
                    <dt>Budget categories</dt><dd>{{ $budgetItemCount }}</dd>
                </div>
                <div class="pd-bar" style="margin-top:16px;"><i style="width:{{ $budgetUsed }}%"></i></div>
            </section>
        </div>

        <div class="pd-col">
            <section class="pd-glass pd-card">
                <h2>Details</h2>
                <dl class="pd-details">
                    <dt>Client</dt><dd>{{ $project->client ?: '—' }}</dd>
                    <dt>Status</dt><dd><span class="pd-badge is-{{ $statusSlug }}">{{ $project->status ?: 'Planning' }}</span></dd>
                    <dt>Priority</dt><dd><span class="pd-badge is-{{ $prioritySlug }}">{{ $project->priority ?: 'Medium' }}</span></dd>
                    <dt>Health</dt><dd><span class="pd-badge is-{{ $healthSlug }}">{{ $health['label'] ?? 'On Track' }}</span></dd>
                    @if($project->project_type)<dt>Type</dt><dd>{{ $project->project_type }}</dd>@endif
                    @if($project->methodology)<dt>Method</dt><dd>{{ $project->methodology }}</dd>@endif
                    <dt>Timeline</dt><dd>{{ $project->start_date?->format('M j, Y') ?? '—' }} – {{ $project->end_date?->format('M j, Y') ?? '—' }}</dd>
                </dl>
            </section>

            <section class="pd-glass pd-card" id="team">
                <div class="pd-card-head"><h2>Team members</h2><span class="pd-count">{{ $members->count() }}</span></div>
                @forelse($members as $member)
                    <div class="pd-member">
                        <span class="pd-avatar" @if($member->avatar_url) style="overflow:hidden;" @endif>
                            @if($member->avatar_url)
                                <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                            @else
                                {{ \Illuminate\Support\Str::of($member->name)->explode(' ')->map(fn ($p) => \Illuminate\Support\Str::substr($p, 0, 1))->take(2)->implode('') }}
                            @endif
                        </span>
                        <div class="pd-member-info"><b>{{ $member->name }}</b><small>{{ $member->job_title ?: $member->email }}</small></div>
                        <span class="pd-badge">{{ str_replace('-', ' ', $member->pivot->role) }}</span>
                        @if($canManageTeam)
                            <form method="POST" action="{{ route('projects.members.destroy', [$project, $member]) }}" onsubmit="return confirm('Remove {{ addslashes($member->name) }} from this project?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button class="pd-remove" type="submit" aria-label="Remove member" title="Remove from project">&times;</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="pd-empty">No one has been added to this project yet.</div>
                @endforelse

                @if($canManageTeam)
                    <div class="pd-invite">
                        <h3>Invite people to this project</h3>
                        <form method="POST" action="{{ route('projects.members.store', $project) }}">
                            @csrf
                            <div class="pd-invite-row">
                                <select name="user_id" id="pd-person" required>
                                    <option value="">Select a person</option>
                                    @foreach($availableUsers as $person)<option value="{{ $person->id }}">{{ $person->name }} ({{ $person->email }})</option>@endforeach
                                    <option value="other">Other (enter email)…</option>
                                </select>
                                <select name="role" required>
                                    <option value="member">Member</option><option value="developer">Developer</option><option value="team-lead">Team Lead</option><option value="project-manager">Project Manager</option>
                                </select>
                            </div>
                            <div class="pd-other-email" id="pd-other-wrap" style="display:none;">
                                <input type="email" name="email" id="pd-other-email" placeholder="name@example.com" value="{{ old('email') }}" disabled>
                            </div>
                            <button class="pd-btn pd-btn-primary" type="submit">+ Add to project</button>
                        </form>
                        @if($me->role === 'admin')<p style="margin-top:12px;">Person not listed? <a href="{{ route('admin.users.create') }}">Create their account first</a>.</p>@endif
                        <div class="pd-share">
                            <h3>Or share an invite link</h3>
                            <div class="pd-invite-row"><input type="text" id="pd-link" readonly value="{{ $joinLinks['member'] }}" onclick="this.select()"><select id="pd-link-role"><option value="member">Member</option><option value="developer">Developer</option></select></div>
                            <div class="pd-share-actions"><button class="pd-btn pd-btn-primary" type="button" id="pd-share-btn">Share invite</button><button class="pd-btn" type="button" id="pd-copy">Copy link</button></div>
                            <div class="pd-share-menu" id="pd-share-menu" hidden><a id="pd-wa" href="#" target="_blank" rel="noopener">WhatsApp</a><a id="pd-mail" href="#">Email</a><a id="pd-tg" href="#" target="_blank" rel="noopener">Telegram</a><button type="button" id="pd-native" hidden>More…</button></div>
                            <p style="margin-top:10px;">Anyone with this link can join this project after logging in or creating an account.</p>
                        </div>
                    </div>
                @endif
            </section>

            <section class="pd-glass pd-card">
                <div class="pd-card-head"><h2>Project management</h2><span class="pd-count">Live data</span></div>
                <div class="pd-link-grid">
                    <a class="pd-link-card" href="{{ route('web.milestones.index') }}"><strong>Milestones</strong><span>{{ $milestoneCount }} total · {{ $upcomingMilestoneCount }} upcoming</span></a>
                    <a class="pd-link-card" href="{{ route('web.risks.index') }}"><strong>Risks</strong><span>{{ $openRiskCount }} open risks</span></a>
                    <a class="pd-link-card" href="{{ route('web.documents.index') }}"><strong>Documents</strong><span>{{ $documentCount }} project documents</span></a>
                    <a class="pd-link-card" href="{{ route('web.stakeholders.index') }}"><strong>Stakeholders</strong><span>{{ $stakeholderCount }} stakeholders</span></a>
                </div>
            </section>

            <section class="pd-glass pd-card">
                <h2>Progress</h2>
                <div class="pd-progress-wrap">
                    <div class="pd-ring" style="--p:{{ $progress }};"><b>{{ $progress }}%</b></div>
                    <div class="pd-progress-text">
                        <p>Delivery is {{ $progress }}% complete, with {{ $overdueTaskCount }} overdue task{{ $overdueTaskCount === 1 ? '' : 's' }}.</p>
                        <div class="pd-bar"><i style="width:{{ $progress }}%"></i></div>
                        <div class="pd-bar-label"><span>Task completion</span><span>{{ $taskCompletion }}%</span></div>
                        <div class="pd-bar" style="margin-top:6px;height:6px;"><i style="width:{{ $taskCompletion }}%;background:linear-gradient(90deg,#22c55e,#14b8a6);"></i></div>
                        <div class="pd-bar-label" style="margin-top:10px;"><span>Budget used</span><span>{{ $budgetUsed }}%</span></div>
                        <div class="pd-bar" style="margin-top:6px;height:6px;"><i style="width:{{ $budgetUsed }}%;background:linear-gradient(90deg,#f59e0b,#f472b6);"></i></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    var links = @json($joinLinks);
    var projectName = @json($project->name);
    var person = document.getElementById('pd-person');
    var wrap = document.getElementById('pd-other-wrap');
    var email = document.getElementById('pd-other-email');
    var linkBox = document.getElementById('pd-link');
    var linkRole = document.getElementById('pd-link-role');
    var copyBtn = document.getElementById('pd-copy');
    var shareBtn = document.getElementById('pd-share-btn');
    var menu = document.getElementById('pd-share-menu');
    var nativeBtn = document.getElementById('pd-native');
    if (!person) return;
    function toggleOther() { var other = person.value === 'other'; wrap.style.display = other ? 'block' : 'none'; email.disabled = !other; email.required = other; if (other) email.focus(); }
    person.addEventListener('change', toggleOther); toggleOther();
    function shareText() { return 'You have been invited to join the project "' + projectName + '" on Project Tracker. Join here: ' + linkBox.value; }
    function refreshShare() {
        var text = shareText(), subject = 'Invitation to join "' + projectName + '"';
        document.getElementById('pd-wa').href = 'https://wa.me/?text=' + encodeURIComponent(text);
        document.getElementById('pd-mail').href = 'mailto:?subject=' + encodeURIComponent(subject) + '&body=' + encodeURIComponent(text);
        document.getElementById('pd-tg').href = 'https://t.me/share/url?url=' + encodeURIComponent(linkBox.value) + '&text=' + encodeURIComponent('You have been invited to join "' + projectName + '"');
    }
    refreshShare();
    linkRole.addEventListener('change', function () { linkBox.value = links[linkRole.value]; refreshShare(); });
    shareBtn.addEventListener('click', function (e) { e.stopPropagation(); menu.hidden = !menu.hidden; });
    document.addEventListener('click', function (e) { if (!menu.hidden && !menu.contains(e.target)) menu.hidden = true; });
    if (navigator.share) { nativeBtn.hidden = false; nativeBtn.addEventListener('click', function () { navigator.share({ title:'Join "'+projectName+'"', text:'You have been invited to join the project "'+projectName+'".', url:linkBox.value }).catch(function(){}); }); }
    copyBtn.addEventListener('click', function () { function done(){ copyBtn.textContent='Copied!'; setTimeout(function(){copyBtn.textContent='Copy link';},1800); } if(navigator.clipboard && window.isSecureContext){navigator.clipboard.writeText(linkBox.value).then(done);}else{linkBox.select();document.execCommand('copy');done();} });
})();
</script>

@endsection