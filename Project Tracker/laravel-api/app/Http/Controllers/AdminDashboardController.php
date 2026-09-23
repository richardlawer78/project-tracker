<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::query()->count(),
            'adminCount' => User::query()->where('role', 'admin')->count(),
            'projectCount' => Project::query()->count(),
            'activeProjectCount' => Project::query()->where('status', 'in-progress')->count(),
            'completedProjectCount' => Project::query()->where('status', 'completed')->count(),
            'taskCount' => Task::query()->count(),
            'completedTaskCount' => Task::query()->where('status', 'completed')->count(),
            'activeSprintCount' => Sprint::query()->where('status', 'active')->count(),
            'openRiskCount' => Risk::query()->where('status', 'open')->count(),
            'recentUsers' => User::query()->latest()->take(5)->get(),
            'recentProjects' => Project::query()->latest()->take(5)->get(),
        ]);
    }
}