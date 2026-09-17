<?php

namespace App\Http\Controllers;

use App\Models\ChangeLogEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChangeLogEntryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $entries = ChangeLogEntry::query()
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
            'requestor_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'impact' => ['required', 'in:low,medium,high'],
            'date' => ['required', 'date'],
        ]);

        $entry = ChangeLogEntry::create($validated);

        return response()->json($entry, 201);
    }

    public function show(ChangeLogEntry $changeLogEntry): JsonResponse
    {
        return response()->json($changeLogEntry);
    }

    public function update(Request $request, ChangeLogEntry $changeLogEntry): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:feature,schedule,scope,budget'],
            'requestor_id' => ['sometimes', 'exists:users,id'],
            'status' => ['sometimes', 'in:pending,approved,rejected'],
            'impact' => ['sometimes', 'in:low,medium,high'],
            'date' => ['sometimes', 'date'],
        ]);

        $changeLogEntry->update($validated);

        return response()->json($changeLogEntry);
    }

    public function destroy(ChangeLogEntry $changeLogEntry): JsonResponse
    {
        $changeLogEntry->delete();

        return response()->json([
            'message' => 'Change log entry deleted successfully.',
        ]);
    }
}