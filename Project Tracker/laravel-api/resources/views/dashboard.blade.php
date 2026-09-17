@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="page-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Portfolio overview and performance</p>
    </div>
    <a class="button" href="{{ route('projects.create') }}">+ New project</a>
</div>
<div class="stats">
    <article><span>Active projects</span><strong>{{ $projectCount }}</strong></article>
    <article><span>Total tasks</span><strong>{{ $taskCount }}</strong></article>
    <article><span>Completed tasks</span><strong>{{ $completedTasks }}</strong></article>
    <article><span>Open risks</span><strong>{{ $riskCount }}</strong></article>
</div>
<div class="grid">
    <section class="card">
        <h2>Recent projects</h2>
        @forelse($projects as $project)
            <a class="row" href="{{ route('projects.show',$project) }}">
                <div><b>{{ $project->name }}</b><small>{{ ucfirst($project->status) }}</small></div>
                <div class="progress"><i style="width:{{ $project->progress }}%"></i></div>
                <em>{{ $project->progress }}%</em>
            </a>
        @empty
            <p class="empty">No projects yet. Create your first project to begin.</p>
        @endforelse
    </section>
    <section class="card">
        <h2>Latest tasks</h2>
        @forelse($tasks as $task)
            <div class="row">
                <div><b>{{ $task->title }}</b><small>{{ $task->project?->name }}</small></div>
                <span class="badge">{{ $task->status }}</span>
            </div>
        @empty
            <p class="empty">No tasks yet.</p>
        @endforelse
        <h2>Upcoming milestones</h2>
        @forelse($milestones as $milestone)
            <div class="row">
                <div><b>{{ $milestone->name }}</b><small>{{ $milestone->project?->name }}</small></div>
                <em>{{ $milestone->due_date?->format('M j') ?? $milestone->date?->format('M j') }}</em>
            </div>
        @empty
            <p class="empty">No milestones yet.</p>
        @endforelse
    </section>
</div>
@endsection
