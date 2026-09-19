<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} &middot; @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<aside class="sidebar" id="project-sidebar" aria-label="Main navigation">
    <a class="brand" href="{{ route('dashboard') }}" aria-label="Kedebah ERP home"><img src="{{ asset('assets/logo/kedebah-logo.png') }}" alt="Kedebah ERP"></a>
    <nav>
        <p>OVERVIEW</p>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="nav-icon ri-home-line" aria-hidden="true"></i>Dashboard</a>
        <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}"><i class="nav-icon ri-folder-line" aria-hidden="true"></i>Projects</a>
        <p>PROJECT DELIVERY</p>
        <a href="{{ route('web.kickoff.index') }}" class="{{ request()->is('initiation/kickoff*') ? 'active' : '' }}"><i class="nav-icon ri-rocket-line" aria-hidden="true"></i>Kick-Off</a>
        <a href="{{ route('web.stakeholders.index') }}" class="{{ request()->is('initiation/stakeholders*') ? 'active' : '' }}"><i class="nav-icon ri-team-line" aria-hidden="true"></i>Stakeholders</a>
        <a href="{{ route('web.sprints.index') }}" class="{{ request()->is('agile/sprints*') ? 'active' : '' }}"><i class="nav-icon ri-loop-left-line" aria-hidden="true"></i>Sprints</a>
        <a href="{{ route('web.backlog.index') }}" class="{{ request()->is('agile/backlog*') ? 'active' : '' }}"><i class="nav-icon ri-task-line" aria-hidden="true"></i>Backlog</a>
        <a href="{{ route('web.definitions.index') }}" class="{{ request()->is('agile/definitions*') ? 'active' : '' }}"><i class="nav-icon ri-checkbox-circle-line" aria-hidden="true"></i>DoR / DoD</a>
        <a href="{{ route('web.tasks.index') }}" class="{{ request()->is('tasks') || request()->is('tasks/create') || request()->is('tasks/*/edit') ? 'active' : '' }}"><i class="nav-icon ri-checkbox-circle-line" aria-hidden="true"></i>Tasks</a>
        <a href="{{ route('kanban') }}" class="{{ request()->is('tasks/kanban') ? 'active' : '' }}"><i class="nav-icon ri-task-line" aria-hidden="true"></i>Kanban</a>
        <a href="{{ route('web.workflows.index') }}" class="{{ request()->is('tasks/workflows*') ? 'active' : '' }}"><i class="nav-icon ri-git-branch-line" aria-hidden="true"></i>Workflows</a>
        <p>RESOURCES &amp; QUALITY</p>
        <a href="{{ route('web.team.index') }}" class="{{ request()->is('resources/team*') ? 'active' : '' }}"><i class="nav-icon ri-team-line" aria-hidden="true"></i>Team</a>
        <a href="{{ route('web.time.index') }}" class="{{ request()->is('resources/time-tracking*') ? 'active' : '' }}"><i class="nav-icon ri-time-line" aria-hidden="true"></i>Time Tracking</a>
        <a href="{{ route('web.budget.index') }}" class="{{ request()->is('resources/budget*') ? 'active' : '' }}"><i class="nav-icon ri-money-dollar-circle-line" aria-hidden="true"></i>Budget</a>
        <a href="{{ route('web.milestones.index') }}" class="{{ request()->is('resources/milestones*') ? 'active' : '' }}"><i class="nav-icon ri-calendar-event-line" aria-hidden="true"></i>Milestones</a>
        <a href="{{ route('gantt') }}" class="{{ request()->is('resources/gantt') ? 'active' : '' }}"><i class="nav-icon ri-road-map-line" aria-hidden="true"></i>Gantt</a>
        <a href="{{ route('web.testing.index') }}" class="{{ request()->is('quality/qa-testing*') ? 'active' : '' }}"><i class="nav-icon ri-bug-line" aria-hidden="true"></i>QA Testing</a>
        <a href="{{ route('web.risks.index') }}" class="{{ request()->is('quality/risks*') ? 'active' : '' }}"><i class="nav-icon ri-shield-check-line" aria-hidden="true"></i>Risks</a>
        <a href="{{ route('web.changes.index') }}" class="{{ request()->is('quality/change-log*') ? 'active' : '' }}"><i class="nav-icon ri-git-branch-line" aria-hidden="true"></i>Change Log</a>
        <p>INSIGHTS</p>
        <a href="{{ route('analytics') }}" class="{{ request()->is('reports/analytics') ? 'active' : '' }}"><i class="nav-icon ri-bar-chart-box-line" aria-hidden="true"></i>Analytics</a>
        <a href="{{ route('web.documents.index') }}" class="{{ request()->is('reports/documents*') ? 'active' : '' }}"><i class="nav-icon ri-file-text-line" aria-hidden="true"></i>Documents</a>
        <a href="{{ route('web.lessons.index') }}" class="{{ request()->is('reports/lessons-learned*') ? 'active' : '' }}"><i class="nav-icon ri-book-open-line" aria-hidden="true"></i>Lessons</a>
        <a href="{{ route('chat') }}" class="{{ request()->is('chat*') ? 'active' : '' }}"><i class="nav-icon ri-chat-3-line" aria-hidden="true"></i>Project Chat</a>
    </nav>
</aside>
<div class="sidebar-backdrop" id="sidebar-backdrop"></div>
<main>
    <header class="app-header">
        <a class="header-brand" href="{{ route('dashboard') }}" aria-label="Kedebah ERP home"><img src="{{ asset('assets/logo/kedebah-logo.png') }}" alt="Kedebah ERP"></a>
        <button id="sidebar-toggle" class="header-icon-button" type="button" aria-label="Toggle navigation" aria-controls="project-sidebar" aria-expanded="true"><span class="hamburger" aria-hidden="true"></span></button>
        <form class="header-search" action="{{ route('projects.index') }}" method="GET" role="search">
            <label class="sr-only" for="global-search">Search projects or tasks</label><span class="search-mark" aria-hidden="true"></span>
            <input id="global-search" name="search" placeholder="Search anything here ..." type="search" autocomplete="off" data-search-url="{{ route('search.global') }}">
            <div id="global-search-results" class="search-results" hidden></div>
        </form>
        <div class="header-actions">
            <button id="mobile-search-toggle" class="header-icon-button mobile-only" type="button" aria-label="Open search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><path d="m16 16 4 4"></path></svg></button>
            <button id="theme-toggle" class="header-icon-button" type="button" aria-label="Toggle color theme" title="Toggle color theme"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.4 15.6A8.6 8.6 0 0 1 8.4 3.6 8.7 8.7 0 1 0 20.4 15.6Z"></path></svg></button>
            <button id="fullscreen-toggle" class="header-icon-button desktop-action" type="button" aria-label="Toggle fullscreen" title="Toggle fullscreen"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3H3v5M16 3h5v5M21 16v5h-5M3 16v5h5"></path></svg></button>
            <div class="header-popover-wrap"><button id="notifications-toggle" class="header-icon-button bell-button" type="button" aria-label="Show notifications" aria-expanded="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"></path></svg><span class="notification-dot"></span></button><div id="notifications-panel" class="header-popover notifications-panel" hidden><strong>Notifications</strong>@forelse($headerNotifications as $notification)<a class="notification-item" href="{{ $notification['url'] }}"><b>{{ $notification['title'] }}</b><span>{{ $notification['detail'] }}</span></a>@empty<p class="notification-empty">No open risks, upcoming milestones, or pending tasks.</p>@endforelse</div></div>
            <div class="header-popover-wrap"><button id="profile-toggle" class="profile-button" type="button" aria-label="Open workspace menu" aria-expanded="false"><span class="avatar workspace-avatar" aria-hidden="true">PM</span><span class="profile-copy"><b>Project Manager</b><small>Project Tracker workspace</small></span><span class="chevron" aria-hidden="true"></span></button><div id="profile-panel" class="header-popover profile-panel" hidden><div class="profile-menu-heading"><span class="avatar workspace-avatar" aria-hidden="true">PM</span><div><strong>Project Manager</strong><span>Workspace profile</span></div></div><a href="{{ route('projects.index') }}">My projects <span>&rarr;</span></a><a href="{{ route('analytics') }}">Reports <span>&rarr;</span></a><p class="workspace-note">This workspace does not use account authentication.</p></div></div>
        </div>
    </header>
    <section class="content">
        @if (session('success'))<div class="notice">{{ session('success') }}</div>@endif
        @if ($errors->any())<div class="notice notice-error">Please correct the highlighted fields and try again.</div>@endif
        @yield('content')
    </section>
</main>
<script>
(() => {
    const sidebar = document.getElementById('project-sidebar');
    const storageKey = 'project-tracker-sidebar-scroll';
    const save = () => { if (sidebar && window.innerWidth > 900) sessionStorage.setItem(storageKey, String(sidebar.scrollTop)); };
    const restore = () => { const value = sessionStorage.getItem(storageKey); if (sidebar && window.innerWidth > 900 && value !== null) sidebar.scrollTop = parseInt(value, 10); };
    document.addEventListener('DOMContentLoaded', () => { restore(); sidebar?.addEventListener('scroll', save); sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', save)); });
})();
</script>
</body>
</html>
