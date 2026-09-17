<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::with([
            'project:id,name',
            'assignee:id,name',
        ])
            ->when($request->filled('project_id'), fn ($query) =>
                $query->where('project_id', $request->project_id)
            )
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status)
            )
            ->orderBy('position')
            ->latest()
            ->paginate(15);

        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    {
        $task = Task::create($this->validatedData($request));

        return response()->json($task->load('project:id,name', 'assignee:id,name'), 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json($task->load('project:id,name', 'assignee:id,name'));
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $task->update($this->validatedData($request));

        return response()->json($task->fresh()->load('project:id,name', 'assignee:id,name'));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:pending,in-progress,completed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'estimated_hours' => ['nullable', 'numeric', 'min:0'],
            'actual_hours' => ['nullable', 'numeric', 'min:0'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['status'] = $data['status'] ?? 'pending';
        $data['priority'] = $data['priority'] ?? 'medium';
        $data['estimated_hours'] = $data['estimated_hours'] ?? 0;
        $data['actual_hours'] = $data['actual_hours'] ?? 0;
        $data['position'] = $data['position'] ?? 0;

        return $data;
    }
}