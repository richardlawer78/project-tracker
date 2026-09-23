<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Risk;
use App\ProjectAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $risks = Risk::with('project:id,name')
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

        return response()->json($risks);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $project = Project::findOrFail($data['project_id']);

        abort_unless(
            ProjectAccess::canView($request->user(), $project),
            403
        );

        $data['owner_id'] = $data['owner_id'] ?? $request->user()->id;

        $risk = Risk::create($data);

        return response()->json(
            $risk->load('project:id,name'),
            201
        );
    }

    public function show(Request $request, Risk $risk): JsonResponse
    {
        abort_unless(
            ProjectAccess::canView($request->user(), $risk->project),
            403
        );

        return response()->json(
            $risk->load('project:id,name')
        );
    }

    public function update(Request $request, Risk $risk): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $risk->project),
            403
        );

        $risk->update($this->validatedData($request));

        return response()->json(
            $risk->fresh()->load('project:id,name')
        );
    }

    public function destroy(Request $request, Risk $risk): JsonResponse
    {
        abort_unless(
            ProjectAccess::canManage($request->user(), $risk->project),
            403
        );

        $risk->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'in:resource,scope,technical,external,financial'],
            'description' => ['nullable', 'string'],
            'probability' => ['nullable', 'in:low,medium,high'],
            'impact' => ['nullable', 'in:low,medium,high'],
            'status' => ['nullable', 'in:open,mitigating,mitigated,closed'],
            'mitigation' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'exists:users,id'],
        ]);

        $data['category'] = $data['category'] ?? 'technical';
        $data['status'] = $data['status'] ?? 'open';

        return $data;
    }
}