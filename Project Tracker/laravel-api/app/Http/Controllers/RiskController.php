<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $risks = Risk::with('project:id,name')
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($risks);
    }

    public function store(Request $request): JsonResponse
    {
        $risk = Risk::create($this->validatedData($request));

        return response()->json(
            $risk->load('project:id,name'),
            201
        );
    }

    public function show(Risk $risk): JsonResponse
    {
        return response()->json(
            $risk->load('project:id,name')
        );
    }

    public function update(Request $request, Risk $risk): JsonResponse
    {
        $risk->update($this->validatedData($request));

        return response()->json(
            $risk->fresh()->load('project:id,name')
        );
    }

    public function destroy(Risk $risk): JsonResponse
    {
        $risk->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'probability' => ['nullable', 'in:low,medium,high'],
            'impact' => ['nullable', 'in:low,medium,high'],
            'status' => ['nullable', 'in:open,mitigated,closed'],
            'mitigation' => ['nullable', 'string'],
        ]);
    }
}