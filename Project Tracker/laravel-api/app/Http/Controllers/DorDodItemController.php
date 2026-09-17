<?php

namespace App\Http\Controllers;

use App\Models\DorDodItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DorDodItemController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = DorDodItem::query()
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

        $item = DorDodItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(DorDodItem $dorDodItem): JsonResponse
    {
        return response()->json($dorDodItem);
    }

    public function update(Request $request, DorDodItem $dorDodItem): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'list_type' => ['sometimes', 'in:dor,dod'],
            'text' => ['sometimes', 'string'],
            'checked' => ['sometimes', 'boolean'],
        ]);

        $dorDodItem->update($validated);

        return response()->json($dorDodItem);
    }

    public function destroy(DorDodItem $dorDodItem): JsonResponse
    {
        $dorDodItem->delete();

        return response()->json([
            'message' => 'DoR/DoD item deleted successfully.',
        ]);
    }
}