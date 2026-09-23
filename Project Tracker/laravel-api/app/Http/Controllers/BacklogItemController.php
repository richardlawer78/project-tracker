<?php

namespace App\Http\Controllers;

use App\Models\BacklogItem;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BacklogItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = BacklogItem::query()
            ->whereHas('project', function ($query) use ($user) {
                $query->when($user->role !== 'admin', function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->where('owner_id', $user->id)
                            ->orWhereHas('members', function ($query) use ($user) {
                                $query->where('users.id', $user->id);
                            });
                    });
                });
            })
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:epic,feature,story'],
            'priority' => ['required', 'in:low,medium,high'],
            'points' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:backlog,ready,in-progress'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $item = BacklogItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(Request $request, BacklogItem $backlogItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $backlogItem->project),
            403
        );

        return response()->json($backlogItem);
    }

    public function update(Request $request, BacklogItem $backlogItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $backlogItem->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:epic,feature,story'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'points' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:backlog,ready,in-progress'],
        ]);

        $backlogItem->update($validated);

        return response()->json($backlogItem);
    }

    public function destroy(Request $request, BacklogItem $backlogItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $backlogItem->project),
            403
        );

        $backlogItem->delete();

        return response()->json([
            'message' => 'Backlog item deleted successfully.',
        ]);
    }
}