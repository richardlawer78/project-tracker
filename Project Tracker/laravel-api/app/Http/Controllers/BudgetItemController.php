<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = BudgetItem::query()
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
            ->latest()
            ->paginate(15);

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'category' => ['required', 'string', 'max:255'],
            'allocated' => ['required', 'numeric', 'min:0'],
            'spent' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:on-track,under,over'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $budgetItem = BudgetItem::create($validated);

        return response()->json($budgetItem, 201);
    }

    public function show(Request $request, BudgetItem $budgetItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $budgetItem->project),
            403
        );

        return response()->json($budgetItem);
    }

    public function update(Request $request, BudgetItem $budgetItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $budgetItem->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'category' => ['sometimes', 'string', 'max:255'],
            'allocated' => ['sometimes', 'numeric', 'min:0'],
            'spent' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:on-track,under,over'],
        ]);

        $budgetItem->update($validated);

        return response()->json($budgetItem);
    }

    public function destroy(Request $request, BudgetItem $budgetItem): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $budgetItem->project),
            403
        );

        $budgetItem->delete();

        return response()->json([
            'message' => 'Budget item deleted successfully.',
        ]);
    }
}