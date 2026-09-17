<?php

namespace App\Http\Controllers;

use App\Models\KickoffObjective;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KickoffObjectiveController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $objectives = KickoffObjective::query()
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

        $objective = KickoffObjective::create($validated);

        return response()->json($objective, 201);
    }

    public function show(KickoffObjective $kickoffObjective): JsonResponse
    {
        return response()->json($kickoffObjective);
    }

    public function update(Request $request, KickoffObjective $kickoffObjective): JsonResponse
    {
        $validated = $request->validate([
            'kickoff_id' => ['sometimes', 'exists:kickoffs,id'],
            'text' => ['sometimes', 'string'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $kickoffObjective->update($validated);

        return response()->json($kickoffObjective);
    }

    public function destroy(KickoffObjective $kickoffObjective): JsonResponse
    {
        $kickoffObjective->delete();

        return response()->json([
            'message' => 'Kickoff objective deleted successfully.',
        ]);
    }
}