@extends('layouts.app')

@section('title', $project->name)

@section('content')

<div class="page-heading">
    <div>
        <h1>{{ $project->name }}</h1>
        <p>{{ $project->team ?: 'Project workspace' }}</p>
    </div>

```
<div class="actions">
    <a class="button secondary" href="{{ route('projects.edit', $project) }}">
        Edit
    </a>

    <form
        method="POST"
        action="{{ route('projects.destroy', $project) }}"
        onsubmit="return confirm('Delete this project?')"
    >
        @csrf
        @method('DELETE')

        <button class="button danger" type="submit">
            Delete
        </button>
    </form>
</div>
```

</div>

<div class="grid">

```
<section class="card">
    <h2>Project overview</h2>

    <p>
        {{ $project->description ?: 'No description provided.' }}
    </p>

    <dl>
        <dt>Client</dt>
        <dd>{{ $project->client ?: '—' }}</dd>

        <dt>Status</dt>
        <dd>
            <span class="badge">
                {{ $project->status }}
            </span>
        </dd>

        <dt>Priority</dt>
        <dd>{{ $project->priority }}</dd>

        <dt>Timeline</dt>
        <dd>
            {{ $project->start_date?->format('M j, Y') ?? '—' }}
            –
            {{ $project->end_date?->format('M j, Y') ?? '—' }}
        </dd>

        <dt>Budget</dt>
        <dd>
            ${{ number_format((float) $project->budget, 2) }}
        </dd>
    </dl>
</section>

<section class="card">
    <h2>Progress</h2>

    <strong class="big">
        {{ $project->progress }}%
    </strong>

    <div class="progress large">
        <i style="width:{{ $project->progress }}%"></i>
    </div>

    <p class="muted">
        Use Tasks, Sprints, Milestones and Risks to manage delivery.
    </p>

    <h2>Recent tasks</h2>

    @forelse($project->tasks as $task)
        <div class="row">
            <div>
                <b>{{ $task->title }}</b>
                <small>{{ $task->status }}</small>
            </div>
        </div>
    @empty
        <p class="empty">
            No tasks on this project yet.
        </p>
    @endforelse

    <a class="button" href="{{ route('web.tasks.create') }}">
        Add task
    </a>
</section>
```

</div>

@endsection
