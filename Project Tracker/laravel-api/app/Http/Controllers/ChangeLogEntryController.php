<?php

namespace App\Http\Controllers;

use App\Models\ChangeLogEntry;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChangeLogEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $entries = ChangeLogEntry::query()
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
            ->latest('date')
            ->paginate(15);

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:feature,schedule,scope,budget'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'impact' => ['required', 'in:low,medium,high'],
            'date' => ['required', 'date'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $validated['requestor_id'] = $request->user()->id;

        $entry = ChangeLogEntry::create($validated);

        return response()->json($entry, 201);
    }

    public function show(Request $request, ChangeLogEntry $changeLogEntry): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $changeLogEntry->project),
            403
        );

        return response()->json($changeLogEntry);
    }

    public function update(Request $request, ChangeLogEntry $changeLogEntry): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $changeLogEntry->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:feature,schedule,scope,budget'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
            'impact' => ['sometimes', 'in:low,medium,high'],
            'date' => ['sometimes', 'date'],
        ]);

        $changeLogEntry->update($validated);

        return response()->json($changeLogEntry);
    }

    public function destroy(Request $request, ChangeLogEntry $changeLogEntry): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $changeLogEntry->project),
            403
        );

        $changeLogEntry->delete();

        return response()->json([
            'message' => 'Change log entry deleted successfully.',
        ]);
    }
}