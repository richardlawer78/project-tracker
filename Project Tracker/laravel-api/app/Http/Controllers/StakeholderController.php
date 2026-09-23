<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Stakeholder;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StakeholderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $stakeholders = Stakeholder::query()
            ->where(function ($query) use ($user) {
                $query->whereHas('project', function ($query) use ($user) {
                    $query->when($user->role !== 'admin', function ($query) use ($user) {
                        $query->where(function ($query) use ($user) {
                            $query->where('owner_id', $user->id)
                                ->orWhereHas('members', function ($query) use ($user) {
                                    $query->where('users.id', $user->id);
                                });
                        });
                    });
                })
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('project_id')
                        ->when($user->role !== 'admin', fn ($query) =>
                            $query->whereRaw('1 = 0')
                        );
                });
            })
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->latest()
            ->paginate(15);

        return response()->json($stakeholders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'influence' => ['required', 'in:low,medium,high'],
            'interest' => ['required', 'in:low,medium,high'],
        ]);

        if ($validated['project_id'] ?? null) {
            $project = Project::findOrFail($validated['project_id']);

            abort_unless(
                ProjectAccess::canView($request->user(), $project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $stakeholder = Stakeholder::create($validated);

        return response()->json($stakeholder, 201);
    }

    public function show(Request $request, Stakeholder $stakeholder): JsonResponse
    {
        if ($stakeholder->project_id) {
            abort_unless(
                ProjectAccess::canView($request->user(), $stakeholder->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        return response()->json($stakeholder);
    }

    public function update(Request $request, Stakeholder $stakeholder): JsonResponse
    {
        if ($stakeholder->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $stakeholder->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $validated = $request->validate([
            'project_id' => ['sometimes', 'nullable', 'exists:projects,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'string', 'max:255'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'influence' => ['sometimes', 'in:low,medium,high'],
            'interest' => ['sometimes', 'in:low,medium,high'],
        ]);

        $stakeholder->update($validated);

        return response()->json($stakeholder);
    }

    public function destroy(Request $request, Stakeholder $stakeholder): JsonResponse
    {
        if ($stakeholder->project_id) {
            abort_unless(
                ProjectAccess::canManage($request->user(), $stakeholder->project),
                403
            );
        } else {
            abort_unless($request->user()->role === 'admin', 403);
        }

        $stakeholder->delete();

        return response()->json([
            'message' => 'Stakeholder deleted successfully.',
        ]);
    }


}