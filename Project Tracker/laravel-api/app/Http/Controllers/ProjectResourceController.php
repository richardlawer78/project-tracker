<?php

namespace App\Http\Controllers;

use App\Models\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $resources = ProjectResource::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('user_id'), fn ($query) =>
                $query->where('user_id', $request->user_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($resources);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'user_id' => ['required', 'exists:users,id'],
            'allocation_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $resource = ProjectResource::create($validated);

        return response()->json($resource, 201);
    }

    public function show(ProjectResource $projectResource): JsonResponse
    {
        return response()->json($projectResource);
    }

    public function update(Request $request, ProjectResource $projectResource): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'allocation_percent' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $projectResource->update($validated);

        return response()->json($projectResource);
    }

    public function destroy(ProjectResource $projectResource): JsonResponse
    {
        $projectResource->delete();

        return response()->json([
            'message' => 'Project resource deleted successfully.',
        ]);
    }
}