@extends('layouts.app')

@section('title', 'Kanban Board')

@section('content')
<div class="page-heading">
    <div>
        <h1>Kanban Board</h1>
        <p>Visualize your workflow</p>
    </div>
    <a class="button" href="{{ route('tasks.create') }}">+ Add task</a>
</div>

<div class="kanban">
    @foreach (['pending' => 'To Do', 'in-progress' => 'In Progress', 'completed' => 'Done'] as $status => $label)
        <section class="card">
            <h2>{{ $label }} <small>{{ $columns[$status]->count() }}</small></h2>
            @forelse ($columns[$status] as $task)
                <article class="task">
                    <b>{{ $task->title }}</b>
                    <small>{{ $task->project?->name }} · {{ $task->assignee?->name ?: 'Unassigned' }}</small>
                    <form method="POST" action="{{ route('tasks.status', $task) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            @foreach (['pending' => 'To Do', 'in-progress' => 'In Progress', 'completed' => 'Done'] as $value => $option)
                                <option value="{{ $value }}" @selected($task->status === $value)>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                </article>
            @empty
                <p class="empty">No tasks</p>
            @endforelse
        </section>
    @endforeach
</div>
@endsection
