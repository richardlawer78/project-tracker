<?php

use App\Http\Controllers\TrackerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [TrackerController::class, 'dashboard'])->name('dashboard');

Route::get('/projects', [TrackerController::class, 'projects'])->name('projects.index');
Route::get('/projects/create', [TrackerController::class, 'createProject'])->name('projects.create');
Route::post('/projects', [TrackerController::class, 'storeProject'])->name('projects.store');
Route::get('/projects/{project}', [TrackerController::class, 'showProject'])->name('projects.show');
Route::get('/projects/{project}/edit', [TrackerController::class, 'editProject'])->name('projects.edit');
Route::put('/projects/{project}', [TrackerController::class, 'updateProject'])->name('projects.update');
Route::delete('/projects/{project}', [TrackerController::class, 'deleteProject'])->name('projects.destroy');

Route::get('/tasks/kanban', [TrackerController::class, 'kanban'])->name('kanban');
Route::patch('/tasks/{task}/status', [TrackerController::class, 'updateTaskStatus'])->name('tasks.status');
Route::get('/resources/gantt', [TrackerController::class, 'gantt'])->name('gantt');
Route::get('/reports/analytics', [TrackerController::class, 'analytics'])->name('analytics');
Route::get('/chat', [TrackerController::class, 'chat'])->name('chat');
Route::post('/chat/channels', [TrackerController::class, 'storeChannel'])->name('chat.channels.store');
Route::post('/chat/{channel}/messages', [TrackerController::class, 'storeMessage'])->name('chat.messages.store');

$features = [
    'kickoff' => 'initiation/kickoff',
    'stakeholders' => 'initiation/stakeholders',
    'sprints' => 'agile/sprints',
    'backlog' => 'agile/backlog',
    'definitions' => 'agile/definitions',
    'tasks' => 'tasks',
    'workflows' => 'tasks/workflows',
    'team' => 'resources/team',
    'time' => 'resources/time-tracking',
    'budget' => 'resources/budget',
    'milestones' => 'resources/milestones',
    'testing' => 'quality/qa-testing',
    'risks' => 'quality/risks',
    'changes' => 'quality/change-log',
    'documents' => 'reports/documents',
    'lessons' => 'reports/lessons-learned',
];

foreach ($features as $feature => $prefix) {
    Route::get("/{$prefix}", fn () => app(TrackerController::class)->feature($feature))->name("{$feature}.index");
    Route::get("/{$prefix}/create", fn () => app(TrackerController::class)->createFeature($feature))->name("{$feature}.create");
    Route::post("/{$prefix}", fn (Request $request) => app(TrackerController::class)->storeFeature($request, $feature))->name("{$feature}.store");
    Route::get("/{$prefix}/{id}/edit", fn (int $id) => app(TrackerController::class)->editFeature($feature, $id))->whereNumber('id')->name("{$feature}.edit");
    Route::put("/{$prefix}/{id}", fn (Request $request, int $id) => app(TrackerController::class)->updateFeature($request, $feature, $id))->whereNumber('id')->name("{$feature}.update");
    Route::delete("/{$prefix}/{id}", fn (int $id) => app(TrackerController::class)->deleteFeature($feature, $id))->whereNumber('id')->name("{$feature}.destroy");
}
