<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class InvestorDashboardController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'investor', 403);

        $projects = Project::query()
            ->select([
                'id',
                'name',
                'description',
                'status',
                'progress',
                'start_date',
                'end_date',
            ])
            ->latest()
            ->get();

        return view('investor.dashboard', [
            'projects' => $projects,
            'projectCount' => $projects->count(),
            'activeProjectCount' => $projects->where('status', 'in-progress')->count(),
            'completedProjectCount' => $projects->where('status', 'completed')->count(),
        ]);
    }

    public function showProject(Request $request, Project $project)
    {
        abort_unless($request->user()->role === 'investor', 403);

        $project->load([
            'milestones:id,project_id,name,status,due_date',
        ]);

        return view('investor.projects.show', compact('project'));
    }
}
