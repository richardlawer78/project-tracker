<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sprints = Sprint::with('project:id,name')
            ->when($request->filled('project_id'), fn ($query) => $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($sprints);
    }

    public function store(Request $request): JsonResponse
    {
        $sprint = Sprint::create($this->validatedData($request));

        return response()->json(
            $sprint->load('project:id,name'),
            201
        );
    }

    public function show(Sprint $sprint): JsonResponse
    {
        return response()->json(
            $sprint->load('project:id,name')
        );
    }

    public function update(Request $request, Sprint $sprint): JsonResponse
    {
        $sprint->update($this->validatedData($request));

        return response()->json(
            $sprint->fresh()->load('project:id,name')
        );
    }

    public function destroy(Sprint $sprint): JsonResponse
    {
        $sprint->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:planned,active,completed'],
        ]);
    }
}
