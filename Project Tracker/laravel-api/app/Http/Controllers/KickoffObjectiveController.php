<?php

namespace App\Http\Controllers;

use App\Models\KickoffObjective;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KickoffObjectiveController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $objectives = KickoffObjective::query()
            ->whereHas('kickoff.project', function ($query) use ($user) {
                $query->when($user->role !== 'admin', function ($query) use ($user) {
                    $query->where(function ($query) use ($user) {
                        $query->where('owner_id', $user->id)
                            ->orWhereHas('members', function ($query) use ($user) {
                                $query->where('users.id', $user->id);
                            });
                    });
                });
            })
            ->when($request->filled('kickoff_id'), fn ($query) =>
                $query->where('kickoff_id', $request->kickoff_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($objectives);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kickoff_id' => ['required', 'exists:kickoffs,id'],
            'text' => ['required', 'string'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $objective = KickoffObjective::with('kickoff.project')
            ->findOrFail($validated['kickoff_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $objective->kickoff->project),
            403
        );

        $created = KickoffObjective::create($validated);

        return response()->json($created, 201);
    }

    public function show(Request $request, KickoffObjective $kickoffObjective): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView(
                $request->user(),
                $kickoffObjective->kickoff->project
            ),
            403
        );

        return response()->json($kickoffObjective);
    }

    public function update(Request $request, KickoffObjective $kickoffObjective): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage(
                $request->user(),
                $kickoffObjective->kickoff->project
            ),
            403
        );

        $validated = $request->validate([
            'kickoff_id' => ['sometimes', 'exists:kickoffs,id'],
            'text' => ['sometimes', 'string'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $kickoffObjective->update($validated);

        return response()->json($kickoffObjective);
    }

    public function destroy(Request $request, KickoffObjective $kickoffObjective): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage(
                $request->user(),
                $kickoffObjective->kickoff->project
            ),
            403
        );

        $kickoffObjective->delete();

        return response()->json([
            'message' => 'Kickoff objective deleted successfully.',
        ]);
    }
}