<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $milestones = Milestone::with('project:id,name')
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
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($milestones);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->normalized($this->validatedData($request));

        $project = Project::findOrFail($data['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $milestone = Milestone::create($data);

        return response()->json(
            $milestone->load('project:id,name'),
            201
        );
    }

    public function show(Request $request, Milestone $milestone): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $milestone->project),
            403
        );

        return response()->json(
            $milestone->load('project:id,name')
        );
    }

    public function update(Request $request, Milestone $milestone): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $milestone->project),
            403
        );

        $data = $this->validatedData($request);

        // Keep the milestone attached to its existing project.
        unset($data['project_id']);

        $milestone->update($this->normalized($data));

        return response()->json(
            $milestone->fresh()->load('project:id,name')
        );
    }

    public function destroy(Request $request, Milestone $milestone): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $milestone->project),
            403
        );

        $milestone->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:upcoming,pending,in-progress,completed'],
        ]);
    }

    private function normalized(array $data): array
    {
        $data['due_date'] = $data['due_date'] ?? $data['date'] ?? null;
        $data['date'] = $data['date'] ?? $data['due_date'];
        $data['status'] = $data['status'] ?? 'upcoming';

        return $data;
    }
}