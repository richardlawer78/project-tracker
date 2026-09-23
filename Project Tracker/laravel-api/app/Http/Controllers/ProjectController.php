<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $projects = Project::query()
            ->when(
                $user->role !== 'admin',
                function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->where('owner_id', $user->id)
                            ->orWhereHas('members', function ($query) use ($user) {
                                $query->where('users.id', $user->id);
                            });
                    });
                }
            )
            ->latest()
            ->paginate(15);

        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Project::class);

        $data = $this->validatedData($request);

        // The logged-in user automatically becomes the owner.
        $data['owner_id'] = $request->user()->id;

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    public function show(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return response()->json($project);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $data = $this->validatedData($request);

        // Prevent changing the project owner through the request.
        unset($data['owner_id']);

        $project->update($data);

        return response()->json($project->fresh());
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client' => ['nullable', 'string', 'max:255'],
            'team' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'in:predictive,agile,hybrid'],
            'status' => ['nullable', 'in:planning,in-progress,on-hold,completed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
            'settings' => ['nullable', 'array'],
        ]);

        $data['project_type'] = $data['project_type'] ?? 'agile';
        $data['status'] = $data['status'] ?? 'planning';
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['budget'] = $data['budget'] ?? 0;
        $data['spent'] = $data['spent'] ?? 0;
        $data['progress'] = $data['progress'] ?? 0;

        return $data;
    }
}