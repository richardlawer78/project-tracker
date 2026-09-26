 <?php

use App\Http\Controllers\TrackerController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public pages (no login needed)
|--------------------------------------------------------------------------
*/

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');

Route::post('/login', [WebAuthController::class, 'login']);

Route::get('/signup', [WebAuthController::class, 'showRegister'])->name('signup');

Route::post('/signup', [WebAuthController::class, 'register']);

Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Everything below needs a logged-in user.
|
| IMPORTANT: every route the app has must live inside this one group.
| Do not add ->middleware('auth') to individual routes outside it and
| do not add new routes after the closing "});" below without moving
| them inside first - a route outside this group has NO login check
| at all, no matter what else is attached to it.
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Project Tracker Dashboard

    Route::get('/', [TrackerController::class, 'dashboard'])->name('dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('admin')
        ->name('admin.dashboard');

    // Admin User Management

    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('users', AdminUserController::class)
                ->except(['show']);
        });

    // My profile (profile picture, name, job title)

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/search', [TrackerController::class, 'globalSearch'])
        ->name('search.global');

    // Projects

    Route::get('/projects', [TrackerController::class, 'projects'])
        ->name('projects.index');

    Route::get('/projects/create', [TrackerController::class, 'createProject'])
        ->name('projects.create');

    Route::post('/projects', [TrackerController::class, 'storeProject'])
        ->name('projects.store');

    Route::get('/projects/{project}', [TrackerController::class, 'showProject'])
        ->name('projects.show');

    Route::get('/projects/{project}/edit', [TrackerController::class, 'editProject'])
        ->name('projects.edit');

    Route::put('/projects/{project}', [TrackerController::class, 'updateProject'])
        ->name('projects.update');

    Route::delete('/projects/{project}', [TrackerController::class, 'deleteProject'])
        ->name('projects.destroy');

    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store'])
        ->name('projects.members.store');

    Route::delete('/projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])
        ->name('projects.members.destroy');

    // Invite link (signed URL). Logged-out people are sent to log in first,
    // then brought back to this link automatically.
    Route::get('/projects/{project}/join', [ProjectMemberController::class, 'join'])
        ->middleware('signed')
        ->name('projects.join');

    Route::get('/projects/{project}/report', [ProjectReportController::class, 'show'])
        ->name('projects.report');

    // Tasks

    Route::get('/tasks/kanban', [TrackerController::class, 'kanban'])
        ->name('kanban');

    Route::patch('/tasks/{task}/status', [TrackerController::class, 'updateTaskStatus'])
        ->name('tasks.status');

    // Resources and Reports

    Route::get('/resources/gantt', [TrackerController::class, 'gantt'])
        ->name('gantt');

    Route::get('/reports/analytics', [TrackerController::class, 'analytics'])
        ->name('analytics');

    // Chat

    Route::get('/chat', [TrackerController::class, 'chat'])
        ->name('chat');

    Route::post('/chat/channels', [TrackerController::class, 'storeChannel'])
        ->name('chat.channels.store');

    Route::post('/chat/{channel}/messages', [TrackerController::class, 'storeMessage'])
        ->name('chat.messages.store');

    // Feature routes

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
        Route::get(
            "/{$prefix}",
            fn () => app(TrackerController::class)->feature($feature)
        )->name("web.{$feature}.index");

        Route::get(
            "/{$prefix}/create",
            fn () => app(TrackerController::class)->createFeature($feature)
        )->name("web.{$feature}.create");

        Route::post(
            "/{$prefix}",
            fn (Request $request) => app(TrackerController::class)->storeFeature($request, $feature)
        )->name("web.{$feature}.store");

        Route::get(
            "/{$prefix}/{id}/edit",
            fn (int $id) => app(TrackerController::class)->editFeature($feature, $id)
        )->whereNumber('id')
            ->name("web.{$feature}.edit");

        Route::put(
            "/{$prefix}/{id}",
            fn (Request $request, int $id) => app(TrackerController::class)->updateFeature($request, $feature, $id)
        )->whereNumber('id')
            ->name("web.{$feature}.update");

        Route::delete(
            "/{$prefix}/{id}",
            fn (int $id) => app(TrackerController::class)->deleteFeature($feature, $id)
        )->whereNumber('id')
            ->name("web.{$feature}.destroy");
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [WebAuthController::class, 'showChangePassword'])
        ->name('password.change');

    Route::post('/change-password', [WebAuthController::class, 'changePassword'])
        ->name('password.change.update');
});
