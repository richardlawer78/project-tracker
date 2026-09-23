<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TestCase;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestCaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $testCases = TestCase::query()
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

        $project = Project::findOrFail($validated['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $testCase = TestCase::create($validated);

        return response()->json($testCase, 201);
    }

    public function show(Request $request, TestCase $testCase): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $testCase->project),
            403
        );

        return response()->json($testCase);
    }

    public function update(Request $request, TestCase $testCase): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $testCase->project),
            403
        );

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

    public function destroy(Request $request, TestCase $testCase): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $testCase->project),
            403
        );

        $testCase->delete();

        return response()->json([
            'message' => 'Test case deleted successfully.',
        ]);
    }


}