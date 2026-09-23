<?php

namespace App\Http\Controllers;

use App\Models\DorDodItem;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DorDodItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = DorDodItem::query()
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
            ->when($request->filled('list_type'), fn ($query) =>
                $query->where('list_type', $request->list_type)
            )
            ->latest()
            ->paginate(15);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'list_type' => ['required', 'in:dor,dod'],
            'text' => ['required', 'string'],
            'checked' => ['sometimes', 'boolean'],
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

        $item = DorDodItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(Request $request, DorDodItem $dorDodItem): JsonResponse
    {
        if ($dorDodItem->project_id) {
            abort_unless(
                ProjectAccess::canView($request->user(), $dorDodItem->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        return response()->json($dorDodItem);
    }

    public function update(Request $request, DorDodItem $dorDodItem): JsonResponse
    {
        if ($dorDodItem->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $dorDodItem->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $validated = $request->validate([
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'list_type' => ['sometimes', 'in:dor,dod'],
            'text' => ['sometimes', 'string'],
            'checked' => ['sometimes', 'boolean'],
        ]);

        $dorDodItem->update($validated);

        return response()->json($dorDodItem);
    }

    public function destroy(Request $request, DorDodItem $dorDodItem): JsonResponse
    {
        if ($dorDodItem->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $dorDodItem->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $dorDodItem->delete();

        return response()->json([
            'message' => 'DoR/DoD item deleted successfully.',
        ]);
    }
}