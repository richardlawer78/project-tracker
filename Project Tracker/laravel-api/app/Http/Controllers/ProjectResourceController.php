<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectResource;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $resources = ProjectResource::query()
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

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canManage($request->user(), $project),
            403
        );

        $resource = ProjectResource::create($validated);

        return response()->json($resource, 201);
    }

    public function show(Request $request, ProjectResource $projectResource): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $projectResource->project),
            403
        );

        return response()->json($projectResource);
    }

    public function update(Request $request, ProjectResource $projectResource): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $projectResource->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'allocation_percent' => ['sometimes', 'integer', 'min:0', 'max:100'],
        ]);

        $projectResource->update($validated);

        return response()->json($projectResource);
    }

    public function destroy(Request $request, ProjectResource $projectResource): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $projectResource->project),
            403
        );

        $projectResource->delete();

        return response()->json([
            'message' => 'Project resource deleted successfully.',
        ]);
    }
}