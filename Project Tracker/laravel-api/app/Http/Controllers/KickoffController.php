<?php

namespace App\Http\Controllers;

use App\Models\Kickoff;
use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KickoffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $kickoffs = Kickoff::query()
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
            ->latest('date')
            ->paginate(15);

        return response()->json($kickoffs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'attendees' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:scheduled,completed'],
        ]);

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $kickoff = Kickoff::create($validated);

        return response()->json($kickoff, 201);
    }

    public function show(Request $request, Kickoff $kickoff): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $kickoff->project),
            403
        );

        return response()->json($kickoff);
    }

    public function update(Request $request, Kickoff $kickoff): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $kickoff->project),
            403
        );

        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'date' => ['sometimes', 'date'],
            'attendees' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:scheduled,completed'],
        ]);

        $kickoff->update($validated);

        return response()->json($kickoff);
    }

    public function destroy(Request $request, Kickoff $kickoff): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $kickoff->project),
            403
        );

        $kickoff->delete();

        return response()->json([
            'message' => 'Kickoff deleted successfully.',
        ]);
    }
}