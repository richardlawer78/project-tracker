<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\ProjectAccess;
use Illuminate\Http\Request;

class ProjectReportController extends Controller
{
    /**
     * Printable / save-as-PDF report for one project.
     */
    public function show(Request $request, Project $project)
    {
        abort_unless(ProjectAccess::canView($request->user(), $project), 403);

        $tasks = $project->tasks()
            ->with('assignee:id,name')
            ->orderBy('id')
            ->get();

        $stats = [
            'total' => $tasks->count(),
            'completed' => $tasks->where('status', 'completed')->count(),
            'in_progress' => $tasks->where('status', 'in-progress')->count(),
            'pending' => $tasks->where('status', 'pending')->count(),
            'overdue' => $tasks->filter(
                fn ($task) => $task->status !== 'completed'
                    && $task->due_date
                    && $task->due_date->isPast()
            )->count(),
        ];

        // Overall progress is calculated from real task completion rather than
        // a manually-typed project field, so it always reflects what's actually
        // been finished.
        $overallProgress = $stats['total'] > 0
            ? round(($stats['completed'] / $stats['total']) * 100)
            : 0;

        $members = $project->members()->orderBy('name')->get();

        // Progress broken down by whoever tasks are assigned to, so it's
        // clear who's driving completion and who still has open work.
        $memberRoles = $members->pluck('pivot.role', 'id');

        $teamProgress = $tasks
            ->groupBy(fn ($task) => $task->assignee?->id ?? 0)
            ->map(function ($assigneeTasks) use ($memberRoles) {
                $assignee = $assigneeTasks->first()->assignee;
                $total = $assigneeTasks->count();
                $completed = $assigneeTasks->where('status', 'completed')->count();

                return [
                    'name' => $assignee?->name ?? 'Unassigned',
                    'role' => $assignee ? ($memberRoles[$assignee->id] ?? '—') : '—',
                    'total' => $total,
                    'completed' => $completed,
                    'percent' => $total > 0 ? round(($completed / $total) * 100) : 0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        $budgetItems = $project->budgetItems()->orderBy('category')->get();

        // The project's own budget/spent columns are entered manually and can
        // drift out of date. Whenever real budget line items exist (entered
        // by users on the project's Budget page), the report trusts those
        // totals instead — so the figures are always what the system actually
        // has on record, not a stale manual value.
        $totalAllocated = $budgetItems->isNotEmpty()
            ? (float) $budgetItems->sum('allocated')
            : (float) $project->budget;

        $totalSpent = $budgetItems->isNotEmpty()
            ? (float) $budgetItems->sum('spent')
            : (float) $project->spent;

        $percentUsed = $totalAllocated > 0
            ? round(($totalSpent / $totalAllocated) * 100)
            : 0;

        $budgetStatus = match (true) {
            $totalAllocated <= 0 => 'no-budget',
            $totalSpent > $totalAllocated => 'over',
            $percentUsed >= 90 => 'at-risk',
            default => 'on-track',
        };

        return view('projects.report', [
            'project' => $project,
            'tasks' => $tasks,
            'stats' => $stats,
            'overallProgress' => $overallProgress,
            'teamProgress' => $teamProgress,
            'members' => $members,
            'milestones' => $project->milestones()->orderBy('due_date')->get(),
            'risks' => $project->risks()->latest()->get(),
            'budgetItems' => $budgetItems,
            'totalAllocated' => $totalAllocated,
            'totalSpent' => $totalSpent,
            'percentUsed' => $percentUsed,
            'budgetStatus' => $budgetStatus,
            'generatedAt' => now(),
        ]);
    }
}