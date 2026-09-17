<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $milestones = Milestone::with('project:id,name')
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($milestones);
    }

    public function store(Request $request): JsonResponse
    {
        $milestone = Milestone::create($this->validatedData($request));

        return response()->json(
            $milestone->load('project:id,name'),
            201
        );
    }

    public function show(Milestone $milestone): JsonResponse
    {
        return response()->json(
            $milestone->load('project:id,name')
        );
    }

    public function update(Request $request, Milestone $milestone): JsonResponse
    {
        $milestone->update($this->validatedData($request));

        return response()->json(
            $milestone->fresh()->load('project:id,name')
        );
    }

    public function destroy(Milestone $milestone): JsonResponse
    {
        $milestone->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,in-progress,completed'],
        ]);
    }
}