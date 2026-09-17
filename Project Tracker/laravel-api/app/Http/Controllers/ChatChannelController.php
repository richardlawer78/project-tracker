<?php

namespace App\Http\Controllers;

use App\Models\ChatChannel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatChannelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $channels = ChatChannel::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($channels);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $channel = ChatChannel::create($validated);

        return response()->json($channel, 201);
    }

    public function show(ChatChannel $chatChannel): JsonResponse
    {
        return response()->json($chatChannel);
    }

    public function update(Request $request, ChatChannel $chatChannel): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
        ]);

        $chatChannel->update($validated);

        return response()->json($chatChannel);
    }

    public function destroy(ChatChannel $chatChannel): JsonResponse
    {
        $chatChannel->delete();

        return response()->json([
            'message' => 'Chat channel deleted successfully.',
        ]);
    }
}