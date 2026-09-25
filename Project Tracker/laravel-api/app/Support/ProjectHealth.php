<?php

namespace App\Support;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Derives a project's delivery health from data that already exists on the
 * project (schedule, progress, budget) and its tasks — never a stored or
 * manually-set value, and never fabricated.
 *
 * Signals used:
 *  - Overdue tasks (incomplete tasks whose due_date has passed)
 *  - Schedule slippage (% of the timeline elapsed vs. % progress logged)
 *  - Budget burn outpacing delivery (% of budget spent vs. % progress)
 *  - The project's own end_date having passed while not completed
 */
class ProjectHealth
{
    public const ON_TRACK = 'on_track';

    public const AT_RISK = 'at_risk';

    public const CRITICAL = 'critical';

    public const COMPLETED = 'completed';

    /**
     * Eager-load the counts this class needs so evaluating a list of
     * projects doesn't trigger N+1 queries. Chain onto any Project query.
     */
    public static function eagerLoad(Builder $query): Builder
    {
        return $query->withCount([
            'tasks',
            'tasks as overdue_tasks_count' => fn ($q) => $q
                ->whereNotNull('due_date')
                ->where('due_date', '<', today())
                ->where('status', '!=', 'completed'),
        ]);
    }

    /**
     * Evaluate a single project. Works whether or not eagerLoad() was used
     * (falls back to a direct query for the overdue count if not).
     *
     * @return array{key: string, label: string, slug: string, reasons: array<int, string>}
     */
    public static function evaluate(Project $project): array
    {
        if (($project->status ?? null) === 'completed') {
            return [
                'key' => self::COMPLETED,
                'label' => 'Completed',
                'slug' => 'completed',
                'reasons' => [],
            ];
        }

        $reasons = [];

        $overdueTasks = $project->overdue_tasks_count
            ?? $project->tasks()
                ->whereNotNull('due_date')
                ->where('due_date', '<', today())
                ->where('status', '!=', 'completed')
                ->count();

        if ($overdueTasks > 0) {
            $reasons[] = $overdueTasks === 1
                ? '1 overdue task'
                : "{$overdueTasks} overdue tasks";
        }

        $progress = (int) ($project->progress ?? 0);

        if ($project->start_date && $project->end_date) {
            $totalDays = max(1, $project->start_date->diffInDays($project->end_date));
            $elapsedDays = min($totalDays, max(0, $project->start_date->diffInDays(today(), false)));
            $schedulePct = (int) round(($elapsedDays / $totalDays) * 100);

            if ($schedulePct - $progress >= 20) {
                $reasons[] = "behind schedule ({$progress}% done, {$schedulePct}% of timeline elapsed)";
            }
        }

        $budget = (float) ($project->budget ?? 0);
        $spent = (float) ($project->spent ?? 0);
        $budgetUsedPct = $budget > 0 ? (int) round(($spent / $budget) * 100) : 0;

        if ($budget > 0 && $budgetUsedPct - $progress >= 20) {
            $reasons[] = "spend outpacing delivery ({$budgetUsedPct}% of budget used)";
        }

        $pastEndDate = $project->end_date && $project->end_date->isPast();

        if ($pastEndDate) {
            $reasons[] = 'past its scheduled end date';
        }

        if ($pastEndDate || $overdueTasks >= 3 || ($budget > 0 && $budgetUsedPct >= 100)) {
            $key = self::CRITICAL;
            $label = 'Critical';
        } elseif (! empty($reasons)) {
            $key = self::AT_RISK;
            $label = 'At Risk';
        } else {
            $key = self::ON_TRACK;
            $label = 'On Track';
        }

        return [
            'key' => $key,
            'label' => $label,
            'slug' => str_replace('_', '-', $key),
            'reasons' => $reasons,
        ];
    }

    /**
     * Tally health across a collection of projects (e.g. for dashboard KPIs).
     * Pass a collection built from a query that used eagerLoad() to avoid N+1.
     *
     * @param  Collection<int, Project>  $projects
     * @return array{on_track: int, at_risk: int, critical: int, completed: int, overdue_tasks_total: int}
     */
    public static function summarize(Collection $projects): array
    {
        $evaluated = $projects->map(fn (Project $project) => self::evaluate($project));

        return [
            'on_track' => $evaluated->where('key', self::ON_TRACK)->count(),
            'at_risk' => $evaluated->where('key', self::AT_RISK)->count(),
            'critical' => $evaluated->where('key', self::CRITICAL)->count(),
            'completed' => $evaluated->where('key', self::COMPLETED)->count(),
            'overdue_tasks_total' => (int) $projects->sum(fn (Project $project) => $project->overdue_tasks_count ?? 0),
        ];
    }
}