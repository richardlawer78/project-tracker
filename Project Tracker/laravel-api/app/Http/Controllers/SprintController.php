<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\ProjectAccess;

class SprintController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $sprints = Sprint::with('project:id,name')
            ->whereHas('project', function ($query) use ($user) {
                $query->when(
                    $user->role !== 'admin',
                    function ($query) use ($user) {
                        $query->where(function ($query) use ($user) {
                            $query->where('owner_id', $user->id)
                                ->orWhereHas('members', function ($query) use ($user) {
                                    $query->where('users.id', $user->id);
                                });
                        });
                    }
                );
            })
            ->when(
                $request->filled('project_id'),
                fn ($query) => $query->where('project_id', $request->project_id)
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($sprints);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $project = Project::findOrFail($data['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $sprint = Sprint::create($data);

        return response()->json(
            $sprint->load('project:id,name'),
            201
        );
    }

    public function show(Request $request, Sprint $sprint): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $sprint->project),
            403
        );

        return response()->json(
            $sprint->load('project:id,name')
        );
    }

    public function update(Request $request, Sprint $sprint): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $sprint->project),
            403
        );

        $sprint->update($this->validatedData($request));

        return response()->json(
            $sprint->fresh()->load('project:id,name')
        );
    }

    public function destroy(Request $request, Sprint $sprint): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $sprint->project),
            403
        );

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