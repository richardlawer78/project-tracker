<?php

namespace App\Providers;

use App\Models\Milestone;
use App\Models\Risk;
use App\Models\Task;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $openRisks = Risk::query()->with('project:id,name')->where('status', 'open')->latest()->take(2)->get()
                ->map(fn (Risk $risk) => ['title' => 'Open risk: '.$risk->title, 'detail' => $risk->project?->name ?? 'Project risk', 'url' => route('web.risks.index')]);
            $milestones = Milestone::query()->with('project:id,name')->whereDate('due_date', '>=', today())->orderBy('due_date')->take(2)->get()
                ->map(fn (Milestone $milestone) => ['title' => 'Milestone: '.$milestone->name, 'detail' => ($milestone->project?->name ?? 'Project').' · '.$milestone->due_date?->format('M j'), 'url' => route('web.milestones.index')]);
            $pendingTasks = Task::query()->with('project:id,name')->where('status', 'pending')->latest()->take(1)->get()
                ->map(fn (Task $task) => ['title' => 'Pending task: '.$task->title, 'detail' => $task->project?->name ?? 'No project', 'url' => route('web.tasks.edit', $task->id)]);

            $view->with('headerNotifications', $openRisks->concat($milestones)->concat($pendingTasks)->take(5));
        });
    }
}
