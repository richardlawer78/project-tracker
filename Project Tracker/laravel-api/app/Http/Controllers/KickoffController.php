<?php

namespace App\Http\Controllers;

use App\Models\Kickoff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KickoffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $kickoffs = Kickoff::query()
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

        $kickoff = Kickoff::create($validated);

        return response()->json($kickoff, 201);
    }

    public function show(Kickoff $kickoff): JsonResponse
    {
        return response()->json($kickoff);
    }

    public function update(Request $request, Kickoff $kickoff): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'date' => ['sometimes', 'date'],
            'attendees' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:scheduled,completed'],
        ]);

        $kickoff->update($validated);

        return response()->json($kickoff);
    }

    public function destroy(Kickoff $kickoff): JsonResponse
    {
        $kickoff->delete();

        return response()->json([
            'message' => 'Kickoff deleted successfully.',
        ]);
    }
}