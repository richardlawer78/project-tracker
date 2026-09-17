@extends('layouts.app')

@section('title', 'Project Chat')

@section('content')
<div class="page-heading">
    <div>
        <h1>Project Chat</h1>
        <p>Team communication</p>
    </div>
</div>

<div class="chat-layout">
    <section class="card">
        <h2>Channels</h2>
        <form class="compact-form" method="POST" action="{{ route('chat.channels.store') }}">
            @csrf
            <input name="name" placeholder="New channel" required>
            <button class="button" type="submit">Add</button>
        </form>
        @forelse ($channels as $channel)
            <a class="row {{ $active?->id === $channel->id ? 'active-row' : '' }}" href="{{ route('chat', ['channel' => $channel->id]) }}">
                <div>
                    <b># {{ $channel->name }}</b>
                    <small>{{ $channel->project?->name ?: 'General' }}</small>
                </div>
            </a>
        @empty
            <p class="empty">No channels yet.</p>
        @endforelse
    </section>

    <section class="card chat-panel">
        @if ($active)
            <h2># {{ $active->name }}</h2>
            <div class="messages">
                @forelse ($messages as $message)
                    <article class="message">
                        <b>{{ $message->user?->name ?: 'Teammate' }}</b>
                        <small>{{ $message->created_at?->format('M j, g:i A') }}</small>
                        <p>{{ $message->message }}</p>
                    </article>
                @empty
                    <p class="empty">No messages in this channel.</p>
                @endforelse
            </div>
            <form class="compact-form" method="POST" action="{{ route('chat.messages.store', $active) }}">
                @csrf
                <input name="message" placeholder="Write a message..." required>
                <button class="button" type="submit">Send</button>
            </form>
        @else
            <p class="empty">Create a channel to start chatting.</p>
        @endif
    </section>
</div>
@endsection
