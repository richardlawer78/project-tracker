@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="page-heading">
    <div>
        <h1>Reports & Analytics</h1>
        <p>Portfolio health and delivery metrics</p>
    </div>
</div>

<div class="stats">
    <article><span>Projects</span><strong>{{ $projectCount }}</strong></article>
    <article><span>Tasks</span><strong>{{ $taskCount }}</strong></article>
    <article><span>Completed</span><strong>{{ $completedTasks }}</strong></article>
    <article><span>Open risks</span><strong>{{ $openRisks }}</strong></article>
</div>

<section class="card table-wrap">
    <h2>Project performance</h2>
    <table>
        <thead><tr><th>Project</th><th>Status</th><th>Progress</th><th>Budget</th></tr></thead>
        <tbody>
            @forelse ($projects as $project)
                <tr>
                    <td><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></td>
                    <td><span class="badge">{{ $project->status }}</span></td>
                    <td>{{ $project->progress }}%</td>
                    <td>${{ number_format((float) $project->budget, 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No project data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
