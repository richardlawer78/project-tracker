@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="page-heading">
    <div>
        <h1>{{ $title }}</h1>
        <p>{{ $subtitle }}</p>
    </div>
    <a class="button" href="/{{ $prefix }}/create">+ New</a>
</div>

<section class="card table-wrap">
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ Str::headline($column) }}</th>
                @endforeach
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    @foreach ($columns as $column)
                        <td>{{ \App\Support\TrackerPresenter::value($item, $column) }}</td>
                    @endforeach
                    <td class="row-actions">
                        <a href="/{{ $prefix }}/{{ $item->id }}/edit">Edit</a>
                        <form method="POST" action="/{{ $prefix }}/{{ $item->id }}" onsubmit="return confirm('Delete this record?')">
                            @csrf
                            @method('DELETE')
                            <button class="link-button" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="empty">No {{ strtolower($title) }} records yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $items->links() }}
</section>
@endsection
