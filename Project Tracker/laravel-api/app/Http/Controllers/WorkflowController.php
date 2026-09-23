<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $workflows = Workflow::query()
            ->when(
                $user->role !== 'admin',
                fn ($query) => $query->whereHas('project', function ($query) use ($user) {
                    $query->where('owner_id', $user->id)
                        ->orWhereHas('members', fn ($query) =>
                            $query->where('users.id', $user->id)
                        );
                })
            )
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
            'project_id' => ['required', 'exists:projects,id'],
        ]);

        $project = \App\Models\Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $workflow = Workflow::create($validated);

        return response()->json($workflow, 201);
    }

    public function show(Workflow $workflow): JsonResponse
    {
        $project = $workflow->project;

        abort_unless(
            $project && ProjectAccess::canView(request()->user(), $project),
            403
        );

        return response()->json($workflow);
    }

    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $project = $workflow->project;

        abort_unless(
            $project && ProjectAccess::canManage($request->user(), $project),
            403
        );

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'stages' => ['sometimes', 'array'],
            'project_id' => ['sometimes', 'exists:projects,id'],
        ]);

        $workflow->update($validated);

        return response()->json($workflow);
    }

    public function destroy(Workflow $workflow): JsonResponse
    {
        $project = $workflow->project;

        abort_unless(
            $project && ProjectAccess::canManage(request()->user(), $project),
            403
        );

        $workflow->delete();

        return response()->json([
            'message' => 'Workflow deleted successfully.',
        ]);
    }
}