<?php

namespace App\Http\Controllers;

use App\Models\ChatChannel;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatChannelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $channels = ChatChannel::query()
            ->where(function ($query) use ($user) {
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
            })
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

        if ($validated['project_id'] ?? null) {
            $project = Project::findOrFail($validated['project_id']);

            abort_unless(
                ProjectAccess::canView($request->user(), $project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $channel = ChatChannel::create($validated);

        return response()->json($channel, 201);
    }

    public function show(Request $request, ChatChannel $chatChannel): JsonResponse
    {
        if ($chatChannel->project_id) {
            abort_unless(
                ProjectAccess::canView($request->user(), $chatChannel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        return response()->json($chatChannel);
    }

    public function update(Request $request, ChatChannel $chatChannel): JsonResponse
    {
        if ($chatChannel->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $chatChannel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
        ]);

        $chatChannel->update($validated);

        return response()->json($chatChannel);
    }

    public function destroy(Request $request, ChatChannel $chatChannel): JsonResponse
    {
        if ($chatChannel->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $chatChannel->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $chatChannel->delete();

        return response()->json([
            'message' => 'Chat channel deleted successfully.',
        ]);
    }
}