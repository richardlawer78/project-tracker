<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $entries = TimeEntry::query()
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
            ->when($request->filled('user_id'), fn ($query) =>
                $query->where('user_id', $request->user_id)
            )
            ->latest('date')
            ->paginate(15);

        return response()->json($entries);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0'],
        ]);

        $project = \App\Models\Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $entry = TimeEntry::create($validated);

        return response()->json($entry, 201);
    }

    public function show(TimeEntry $timeEntry): JsonResponse
    {
        $project = $timeEntry->project;

        abort_unless(
            $project && ProjectAccess::canView(request()->user(), $project),
            403
        );

        return response()->json($timeEntry);
    }

    public function update(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        $project = $timeEntry->project;

        abort_unless(
            $project && ProjectAccess::canManage($request->user(), $project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'task_id' => ['sometimes', 'nullable', 'exists:tasks,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'date' => ['sometimes', 'date'],
            'hours' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $timeEntry->update($validated);

        return response()->json($timeEntry);
    }

    public function destroy(TimeEntry $timeEntry): JsonResponse
    {
        $project = $timeEntry->project;

        abort_unless(
            $project && ProjectAccess::canManage(request()->user(), $project),
            403
        );

        $timeEntry->delete();

        return response()->json([
            'message' => 'Time entry deleted successfully.',
        ]);
    }
}