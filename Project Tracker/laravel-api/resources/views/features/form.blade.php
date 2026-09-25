@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="page-heading">
    <div>
        <h1>{{ $title }}</h1>
        <p>Complete the form and save to update the tracker.</p>
    </div>
</div>

<form class="card form" method="POST" action="{{ $item->exists ? '/'.$prefix.'/'.$item->id : '/'.$prefix }}">
    @csrf
    @if ($item->exists)
        @method('PUT')
    @endif

    <div class="form-grid">
        @foreach ($fields as $field)
            @include('features.partials.field', ['field' => $field, 'item' => $item])
        @endforeach
    </div>

    <div class="actions">
        <a href="/{{ $prefix }}">Cancel</a>
        <button class="button" type="submit">{{ $item->exists ? 'Save changes' : 'Create' }}</button>
    </div>
</form>
@endsection
