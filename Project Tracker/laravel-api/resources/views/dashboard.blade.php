@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-heading dashboard-heading"><div><h1>Dashboard</h1><p>Portfolio overview and delivery performance</p></div></div>

<section class="dashboard-hero">
    <div><span class="eyebrow">PROJECT CONTROL CENTER</span><h2>Keep every project moving forward.</h2><p>Plan work, stay ahead of delivery risks, and keep your team focused on the next milestone.</p><a class="button" href="{{ route('projects.index') }}">Manage projects <span aria-hidden="true">&rarr;</span></a></div>
    <div class="hero-art" aria-hidden="true"><div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div><img class="hero-illustration" src="{{ asset('assets/images/project-hero.png') }}" alt=""><div class="hero-panel"><span>Delivery health</span><strong>{{ $completedTasks }} completed</strong><i><b style="width: {{ $completionRate }}%"></b></i></div></div>
</section>

<div class="stats dashboard-stats">
    <article><span class="stat-icon blue">&#9632;</span><span>New projects</span><strong>{{ $newProjectCount }}</strong><small>Created this month</small></article>
    <article><span class="stat-icon green">&#10003;</span><span>Completed projects</span><strong>{{ $completedProjectCount }}</strong><small>{{ $projectCount }} total in portfolio</small></article>
    <article><span class="stat-icon purple">&#9679;</span><span>Ongoing projects</span><strong>{{ $activeProjectCount }}</strong><small>{{ $pendingProjectCount }} planning or on hold</small></article>
    <article><span class="stat-icon rose">!</span><span>Delivery watch</span><strong>{{ $riskCount }}</strong><small>{{ $sprintCount }} active sprints</small></article>
</div>

<div class="dashboard-analytics">
    <section class="card activity-card"><div class="card-heading"><div><h2>Project statistics</h2><p>Projects and tasks created in the last six months.</p></div><a href="{{ route('analytics') }}">Reports</a></div>
        @php($activityMax = max(1, ...$projectActivity, ...$taskActivity))
        <div class="activity-chart" role="img" aria-label="Projects and tasks created over the last six months">
            @foreach($activityMonths as $index => $month)<div class="activity-month"><div class="activity-bars"><i class="project-bar" style="height: {{ max(4, ($projectActivity[$index] / $activityMax) * 100) }}%" title="{{ $projectActivity[$index] }} projects"></i><i class="task-bar" style="height: {{ max(4, ($taskActivity[$index] / $activityMax) * 100) }}%" title="{{ $taskActivity[$index] }} tasks"></i></div><span>{{ $month }}</span></div>@endforeach
        </div><div class="chart-key"><span><i class="key-project"></i>Projects</span><span><i class="key-task"></i>Tasks</span></div>
    </section>
    <section class="card target-card"><div class="card-heading"><div><h2>Monthly targets</h2><p>Current project measures from live records.</p></div></div>
        @php($targetBase = max(1, $monthlyTarget['new'] + $monthlyTarget['completed'] + $monthlyTarget['active']))
        <div class="target-ring" style="--new: {{ ($monthlyTarget['new'] / $targetBase) * 100 }}; --completed: {{ ($monthlyTarget['completed'] / $targetBase) * 100 }}; --active: {{ ($monthlyTarget['active'] / $targetBase) * 100 }}"><div><strong>{{ $monthlyTarget['total'] }}</strong><span>projects</span></div></div>
        <div class="target-legend"><span><i class="new-dot"></i>New <b>{{ $monthlyTarget['new'] }}</b></span><span><i class="completed-dot"></i>Completed <b>{{ $monthlyTarget['completed'] }}</b></span><span><i class="active-dot"></i>Active / pending <b>{{ $monthlyTarget['active'] }}</b></span></div>
    </section>
</div>

<div class="dashboard-grid">
    <section class="card"><div class="card-heading"><div><h2>Daily tasks</h2><p>Work due next across your projects.</p></div><a href="{{ route('web.tasks.index') }}">All tasks</a></div>
        @forelse($tasks as $task)<a class="row task-row" href="{{ route('web.tasks.edit', $task->id) }}"><span class="task-marker {{ $task->status === 'completed' ? 'done' : '' }}">{{ $task->status === 'completed' ? '✓' : '' }}</span><div class="row-main"><b>{{ $task->title }}</b><small>{{ $task->project?->name ?? 'Unassigned project' }} &middot; {{ $task->assignee?->name ?? 'Unassigned' }} &middot; {{ $task->due_date?->format('M j') ?? 'No due date' }}</small></div><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></a>@empty <p class="empty">No tasks yet. <a href="{{ route('web.tasks.create') }}">Create a task</a> to start tracking work.</p> @endforelse
    </section>
    <section class="card"><div class="card-heading"><div><h2>Upcoming milestones</h2><p>Next dates on the delivery calendar.</p></div><a href="{{ route('web.milestones.index') }}">All milestones</a></div>
        @forelse($milestones as $milestone)<div class="row"><div class="row-main"><b>{{ $milestone->name }}</b><small>{{ $milestone->project?->name }}</small></div><time>{{ $milestone->due_date?->format('M j') }}</time></div>@empty <p class="empty">No upcoming milestones yet.</p> @endforelse
    </section>
</div>

<div class="dashboard-grid lower-grid">
    <section class="card team-snapshot"><div class="card-heading"><div><h2>Team snapshot</h2><p>People currently assigned to project work.</p></div><a href="{{ route('web.team.index') }}">Manage team</a></div>
        @forelse($teamMembers as $index => $resource)<div class="team-person">@if($resource->user?->avatar)<img class="member-avatar" src="{{ asset($resource->user->avatar) }}" alt="{{ $resource->user->name }}">@else<span class="member-avatar avatar-tone-{{ $index % 5 }}" aria-hidden="true">{{ strtoupper(collect(explode(' ', $resource->user?->name ?? '?'))->map(fn ($part) => substr($part, 0, 1))->take(2)->implode('')) }}</span>@endif<div class="row-main"><b>{{ $resource->user?->name ?? 'Unknown user' }}</b><small>{{ $resource->user?->job_title ?: 'Team member' }}</small></div><span class="team-workload">{{ $resource->user?->assigned_tasks_count ?? 0 }} tasks</span></div>@empty <p class="empty">No project resources have been added yet.</p> @endforelse
    </section>
    <section class="card"><div class="card-heading"><div><h2>Open risks</h2><p>Priority items needing follow-up.</p></div><a href="{{ route('web.risks.index') }}">Risk register</a></div>
        @forelse($risks as $risk)<a class="row" href="{{ route('web.risks.edit', $risk->id) }}"><div class="row-main"><b>{{ $risk->title }}</b><small>{{ $risk->project?->name ?? 'Portfolio risk' }} &middot; {{ $risk->category }}</small></div><span class="badge badge-{{ $risk->impact }}">{{ $risk->impact }} impact</span></a>@empty <p class="empty">No open risks. Keep monitoring your portfolio.</p> @endforelse
    </section>
</div>

<section class="card project-overview project-summary"><div class="card-heading"><div><h2>Projects summary</h2><p>Current portfolio progress, work, resources, and delivery dates.</p></div><a href="{{ route('projects.index') }}">View all</a></div><div class="table-wrap"><table><thead><tr><th>Project</th><th>Tasks</th><th>Progress</th><th>Resources</th><th>Status</th><th>Deadline</th><th></th></tr></thead><tbody>
    @forelse($projects as $project)<tr><td><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a><small>{{ $project->client ?: 'Internal project' }}</small></td><td>{{ $project->completed_tasks_count }}/{{ $project->tasks_count }}<small>completed</small></td><td><div class="progress-label"><div class="progress"><i style="width:{{ $project->progress }}%"></i></div><span>{{ $project->progress }}%</span></div></td><td>{{ $project->project_resources_count }}<small>assigned</small></td><td><span class="badge badge-{{ $project->status }}">{{ str_replace('-', ' ', $project->status) }}</span></td><td>{{ $project->end_date?->format('M j, Y') ?? 'Not scheduled' }}</td><td><a class="table-action" href="{{ route('projects.edit', $project) }}">Edit</a></td></tr>@empty<tr><td class="empty" colspan="7">No projects yet. <a href="{{ route('projects.create') }}">Create your first project</a> to begin.</td></tr>@endforelse
    </tbody></table></div></section>

<section class="card task-summary"><div class="card-heading"><div><h2>Task summary</h2><p>Weekly task activity from Laravel records.</p></div><a href="{{ route('web.tasks.index') }}">View all</a></div><div class="task-rate-overview"><div><h3>Tasks completed rate</h3><p>Based on completed tasks in this workspace</p></div><div><strong>{{ $completionRate }}%</strong><span>{{ $completedTasks }} of {{ $taskCount }}</span></div></div><div id="tasks-report" class="task-report-chart" aria-label="This week and last week task activity chart"></div><script id="tasks-report-data" type="application/json">@json(['thisWeek' => $thisWeekTasks, 'lastWeek' => $lastWeekTasks])</script><p class="chart-note">Activity uses task creation dates because completion timestamps are not stored.</p></section>

<section class="quick-actions"><div><h2>Quick actions</h2><p>Start common project-management work.</p></div><div><a class="button" href="{{ route('projects.create') }}">New project</a><a class="button secondary" href="{{ route('web.tasks.create') }}">New task</a><a class="button secondary" href="{{ route('web.sprints.create') }}">New sprint</a><a class="button secondary" href="{{ route('web.milestones.create') }}">New milestone</a><a class="button secondary" href="{{ route('analytics') }}">View reports</a></div></section>
@endsection
