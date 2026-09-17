<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $workflows = Workflow::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($workflows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stages' => ['required', 'array'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $workflow = Workflow::create($validated);

        return response()->json($workflow, 201);
    }

    public function show(Workflow $workflow): JsonResponse
    {
        return response()->json($workflow);
    }

    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'stages' => ['sometimes', 'array'],
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
        ]);

        $workflow->update($validated);

        return response()->json($workflow);
    }

    public function destroy(Workflow $workflow): JsonResponse
    {
        $workflow->delete();

        return response()->json([
            'message' => 'Workflow deleted successfully.',
        ]);
    }
}