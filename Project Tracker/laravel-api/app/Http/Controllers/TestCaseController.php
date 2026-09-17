<?php

namespace App\Http\Controllers;

use App\Models\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $testCases = TestCase::query()
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->latest()
            ->paginate(15);

        return response()->json($testCases);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:functional,performance,ui'],
            'status' => ['required', 'in:passed,failed,pending'],
            'priority' => ['required', 'in:low,medium,high'],
            'last_run' => ['nullable', 'date'],
        ]);

        $testCase = TestCase::create($validated);

        return response()->json($testCase, 201);
    }

    public function show(TestCase $testCase): JsonResponse
    {
        return response()->json($testCase);
    }

    public function update(Request $request, TestCase $testCase): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['sometimes', 'exists:projects,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:functional,performance,ui'],
            'status' => ['sometimes', 'in:passed,failed,pending'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'last_run' => ['sometimes', 'nullable', 'date'],
        ]);

        $testCase->update($validated);

        return response()->json($testCase);
    }

    public function destroy(TestCase $testCase): JsonResponse
    {
        $testCase->delete();

        return response()->json([
            'message' => 'Test case deleted successfully.',
        ]);
    }
}