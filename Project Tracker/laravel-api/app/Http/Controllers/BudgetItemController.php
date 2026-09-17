<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = BudgetItem::query()
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

        $budgetItem = BudgetItem::create($validated);

        return response()->json($budgetItem, 201);
    }

    public function show(BudgetItem $budgetItem): JsonResponse
    {
        return response()->json($budgetItem);
    }

    public function update(Request $request, BudgetItem $budgetItem): JsonResponse
    {
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

    public function destroy(BudgetItem $budgetItem): JsonResponse
    {
        $budgetItem->delete();

        return response()->json([
            'message' => 'Budget item deleted successfully.',
        ]);
    }
}