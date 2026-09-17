<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SprintController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\BacklogItemController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\BudgetItemController;
use App\Http\Controllers\TimeEntryController;
use App\Http\Controllers\ProjectResourceController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\KickoffController;
use App\Http\Controllers\KickoffObjectiveController;
use App\Http\Controllers\DorDodItemController;
use App\Http\Controllers\TestCaseController;
use App\Http\Controllers\ChangeLogEntryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LessonLearnedController;
use App\Http\Controllers\ChatChannelController;
use App\Http\Controllers\ChatMessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('projects', ProjectController::class);
Route::apiResource('tasks', TaskController::class);
Route::apiResource('sprints', SprintController::class);
Route::apiResource('milestones', MilestoneController::class);
Route::apiResource('risks', RiskController::class);
Route::apiResource('backlog-items', BacklogItemController::class);
Route::apiResource('workflows', WorkflowController::class);
Route::apiResource('budget-items', BudgetItemController::class);
Route::apiResource('time-entries', TimeEntryController::class);
Route::apiResource('project-resources', ProjectResourceController::class);
Route::apiResource('stakeholders', StakeholderController::class);
Route::apiResource('kickoffs', KickoffController::class);
Route::apiResource('kickoff-objectives', KickoffObjectiveController::class);
Route::apiResource('dor-dod-items', DorDodItemController::class);
Route::apiResource('test-cases', TestCaseController::class);
Route::apiResource('change-log-entries', ChangeLogEntryController::class);
Route::apiResource('documents', DocumentController::class);
Route::apiResource('lessons-learned', LessonLearnedController::class);
Route::apiResource('chat-channels', ChatChannelController::class);
Route::apiResource('chat-messages', ChatMessageController::class);