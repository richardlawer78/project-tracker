<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} · @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<aside class="sidebar">
    <a class="brand" href="{{ route('dashboard') }}">Project<span>Tracker</span></a>
    <nav>
        <p>OVERVIEW</p>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}">Projects</a>

        <p>PROJECT DELIVERY</p>
        <a href="{{ route('kickoff.index') }}" class="{{ request()->is('initiation/kickoff*') ? 'active' : '' }}">Kick-Off</a>
        <a href="{{ route('stakeholders.index') }}" class="{{ request()->is('initiation/stakeholders*') ? 'active' : '' }}">Stakeholders</a>
        <a href="{{ route('sprints.index') }}" class="{{ request()->is('agile/sprints*') ? 'active' : '' }}">Sprints</a>
        <a href="{{ route('backlog.index') }}" class="{{ request()->is('agile/backlog*') ? 'active' : '' }}">Backlog</a>
        <a href="{{ route('definitions.index') }}" class="{{ request()->is('agile/definitions*') ? 'active' : '' }}">DoR / DoD</a>
        <a href="{{ route('tasks.index') }}" class="{{ request()->is('tasks') || request()->is('tasks/create') || request()->is('tasks/*/edit') ? 'active' : '' }}">Tasks</a>
        <a href="{{ route('kanban') }}" class="{{ request()->is('tasks/kanban') ? 'active' : '' }}">Kanban</a>
        <a href="{{ route('workflows.index') }}" class="{{ request()->is('tasks/workflows*') ? 'active' : '' }}">Workflows</a>

        <p>RESOURCES & QUALITY</p>
        <a href="{{ route('team.index') }}" class="{{ request()->is('resources/team*') ? 'active' : '' }}">Team</a>
        <a href="{{ route('time.index') }}" class="{{ request()->is('resources/time-tracking*') ? 'active' : '' }}">Time Tracking</a>
        <a href="{{ route('budget.index') }}" class="{{ request()->is('resources/budget*') ? 'active' : '' }}">Budget</a>
        <a href="{{ route('milestones.index') }}" class="{{ request()->is('resources/milestones*') ? 'active' : '' }}">Milestones</a>
        <a href="{{ route('gantt') }}" class="{{ request()->is('resources/gantt') ? 'active' : '' }}">Gantt</a>
        <a href="{{ route('testing.index') }}" class="{{ request()->is('quality/qa-testing*') ? 'active' : '' }}">QA Testing</a>
        <a href="{{ route('risks.index') }}" class="{{ request()->is('quality/risks*') ? 'active' : '' }}">Risks</a>
        <a href="{{ route('changes.index') }}" class="{{ request()->is('quality/change-log*') ? 'active' : '' }}">Change Log</a>

        <p>INSIGHTS</p>
        <a href="{{ route('analytics') }}" class="{{ request()->is('reports/analytics') ? 'active' : '' }}">Analytics</a>
        <a href="{{ route('documents.index') }}" class="{{ request()->is('reports/documents*') ? 'active' : '' }}">Documents</a>
        <a href="{{ route('lessons.index') }}" class="{{ request()->is('reports/lessons-learned*') ? 'active' : '' }}">Lessons</a>
        <a href="{{ route('chat') }}" class="{{ request()->is('chat*') ? 'active' : '' }}">Project Chat</a>
    </nav>
</aside>
<main>
    <header>
        <button id="theme-toggle" type="button" aria-label="Toggle theme">◐</button>
        <span>Project management workspace</span>
        <div class="avatar">PT</div>
    </header>
    <section class="content">
        @if (session('success'))
            <div class="notice">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice notice-error">Please correct the highlighted fields and try again.</div>
        @endif
        @yield('content')
    </section>
</main>
</body>
</html>
