<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $messages = ChatMessage::query()
            ->whereHas('chatChannel', function ($query) use ($user) {
                $query->where(function ($query) use ($user) {
                    $query->whereHas('project', function ($query) use ($user) {
                        $query->when($user->role !== 'admin', function ($query) use ($user) {
                            $query->where(function ($query) use ($user) {
                                $query->where('owner_id', $user->id)
                                    ->orWhereHas('members', function ($query) use ($user) {
                                        $query->where('users.id', $user->id);
                                    });
                            });
                        });
                    })
                    ->orWhere(function ($query) use ($user) {
                        $query->whereNull('project_id')
                            ->when($user->role !== 'admin', fn ($query) =>
                                $query->whereRaw('1 = 0')
                            );
                    });
                });
            })
            ->when($request->filled('chat_channel_id'), fn ($query) =>
                $query->where('chat_channel_id', $request->chat_channel_id)
            )
            ->when($request->filled('user_id'), fn ($query) =>
                $query->where('user_id', $request->user_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_channel_id' => ['required', 'exists:chat_channels,id'],
            'message' => ['required', 'string'],
        ]);

        $channel = \App\Models\ChatChannel::findOrFail($validated['chat_channel_id']);

        if ($channel->project_id) {
            abort_unless(
                ProjectAccess::canView($request->user(), $channel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $validated['user_id'] = $request->user()->id;

        $message = ChatMessage::create($validated);

        return response()->json($message, 201);
    }

    public function show(Request $request, ChatMessage $chatMessage): JsonResponse
    {
        $channel = $chatMessage->chatChannel;

        if ($channel->project_id) {
            abort_unless(
                ProjectAccess::canView($request->user(), $channel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        return response()->json($chatMessage);
    }

    public function update(Request $request, ChatMessage $chatMessage): JsonResponse
    {
        $channel = $chatMessage->chatChannel;

        if ($channel->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $channel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $validated = $request->validate([
            'chat_channel_id' => ['sometimes', 'exists:chat_channels,id'],
            'message' => ['sometimes', 'string'],
        ]);

        $chatMessage->update($validated);

        return response()->json($chatMessage);
    }

    public function destroy(Request $request, ChatMessage $chatMessage): JsonResponse
    {
        $channel = $chatMessage->chatChannel;

        if ($channel->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $channel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $chatMessage->delete();

        return response()->json([
            'message' => 'Chat message deleted successfully.',
        ]);
    }
}