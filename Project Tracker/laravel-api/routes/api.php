<?php

use App\Http\Controllers\BacklogItemController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\ChangeLogEntryController;
use App\Http\Controllers\ChatChannelController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DorDodItemController;
use App\Http\Controllers\KickoffController;
use App\Http\Controllers\KickoffObjectiveController;
use App\Http\Controllers\LessonLearnedController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectResourceController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    // Password
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Users
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);

    // Projects
    Route::apiResource('projects', ProjectController::class)
        ->names('api.projects');

    // Tasks
    Route::apiResource('tasks', TaskController::class)
        ->names('api.tasks');

    // Sprints
    Route::apiResource('sprints', SprintController::class)
        ->names('api.sprints');

    // Milestones
    Route::apiResource('milestones', MilestoneController::class)
        ->names('api.milestones');

    // Risks
    Route::apiResource('risks', RiskController::class)
        ->names('api.risks');

    // Backlog
    Route::apiResource('backlog-items', BacklogItemController::class);

    // Workflows
    Route::apiResource('workflows', WorkflowController::class);

    // Budget
    Route::apiResource('budget-items', BudgetItemController::class);

    // Time Entries
    Route::apiResource('time-entries', TimeEntryController::class);

    // Resources
    Route::apiResource('project-resources', ProjectResourceController::class);

    // Stakeholders
    Route::apiResource('stakeholders', StakeholderController::class);

    // Kickoffs
    Route::apiResource('kickoffs', KickoffController::class);

    // Kickoff Objectives
    Route::apiResource('kickoff-objectives', KickoffObjectiveController::class);

    // Definition of Ready / Definition of Done
    Route::apiResource('dor-dod-items', DorDodItemController::class);

    // Test Cases
    Route::apiResource('test-cases', TestCaseController::class);

    // Change Log
    Route::apiResource('change-log-entries', ChangeLogEntryController::class);

    // Documents
    Route::apiResource('documents', DocumentController::class);

    // Lessons Learned
    Route::apiResource('lessons-learned', LessonLearnedController::class);

    // Chat Channels
    Route::apiResource('chat-channels', ChatChannelController::class);

    // Chat Messages
    Route::apiResource('chat-messages', ChatMessageController::class);
});