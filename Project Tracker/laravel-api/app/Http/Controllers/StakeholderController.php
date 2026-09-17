<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StakeholderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stakeholders = Stakeholder::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($stakeholders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'influence' => ['required', 'in:low,medium,high'],
            'interest' => ['required', 'in:low,medium,high'],
        ]);

        $stakeholder = Stakeholder::create($validated);

        return response()->json($stakeholder, 201);
    }

    public function show(Stakeholder $stakeholder): JsonResponse
    {
        return response()->json($stakeholder);
    }

    public function update(Request $request, Stakeholder $stakeholder): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'string', 'max:255'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'influence' => ['sometimes', 'in:low,medium,high'],
            'interest' => ['sometimes', 'in:low,medium,high'],
        ]);

        $stakeholder->update($validated);

        return response()->json($stakeholder);
    }

    public function destroy(Stakeholder $stakeholder): JsonResponse
    {
        $stakeholder->delete();

        return response()->json([
            'message' => 'Stakeholder deleted successfully.',
        ]);
    }
}