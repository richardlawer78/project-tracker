<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $messages = ChatMessage::query()
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
            'user_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string'],
        ]);

        $message = ChatMessage::create($validated);

        return response()->json($message, 201);
    }

    public function show(ChatMessage $chatMessage): JsonResponse
    {
        return response()->json($chatMessage);
    }

    public function update(Request $request, ChatMessage $chatMessage): JsonResponse
    {
        $validated = $request->validate([
            'chat_channel_id' => ['sometimes', 'exists:chat_channels,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'message' => ['sometimes', 'string'],
        ]);

        $chatMessage->update($validated);

        return response()->json($chatMessage);
    }

    public function destroy(ChatMessage $chatMessage): JsonResponse
    {
        $chatMessage->delete();

        return response()->json([
            'message' => 'Chat message deleted successfully.',
        ]);
    }
}