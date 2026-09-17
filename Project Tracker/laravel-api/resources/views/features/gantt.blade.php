@extends('layouts.app')

@section('title', 'Gantt Chart')

@section('content')
<div class="page-heading">
    <div>
        <h1>Gantt Chart</h1>
        <p>Project timeline across the portfolio</p>
    </div>
</div>

<section class="card">
    @forelse ($projects as $project)
        @php
            $start = $project->start_date ?: now();
            $end = $project->end_date ?: $start->copy()->addMonth();
            $span = max(1, $start->diffInDays($end));
            $width = min(100, max(12, $span / 2));
        @endphp
        <div class="gantt-row">
            <div>
                <b>{{ $project->name }}</b>
                <small>{{ $start->format('M j') }} – {{ $end->format('M j, Y') }}</small>
            </div>
            <div class="gantt-track">
                <i style="width: {{ $width }}%;"></i>
            </div>
            <em>{{ $project->progress }}%</em>
        </div>
    @empty
        <p class="empty">Create a project with start and end dates to see the timeline.</p>
    @endforelse
</section>
@endsection
