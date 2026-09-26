<?php

namespace App\Http\Controllers;

use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\Milestone;
use App\Models\Project;
use App\ProjectAccess;
use App\Models\ProjectResource;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Support\ProjectHealth;
use App\Support\TrackerFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrackerController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = today();
        $weekStart = $today->copy()->startOfWeek(Carbon::SUNDAY);
        $lastWeekStart = $weekStart->copy()->subWeek();
        $months = collect(range(5, 0))->map(fn (int $offset) => $today->copy()->subMonths($offset)->startOfMonth());

        $user = $request->user();
        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        $projectCountQuery = Project::query();
        $taskCountQuery = Task::query();
        $completedTasksQuery = Task::query()->where('status', 'completed');
        $healthProjectsQuery = Project::query()->withHealthMetrics();
        $totalBudgetQuery = Project::query();
        $totalSpentQuery = Project::query();

        if ($visibleProjectIds !== null) {
            $projectCountQuery->whereIn('id', $visibleProjectIds);
            $healthProjectsQuery->whereIn('id', $visibleProjectIds);
            $totalBudgetQuery->whereIn('id', $visibleProjectIds);
            $totalSpentQuery->whereIn('id', $visibleProjectIds);
        }

        ProjectAccess::limitToVisible($taskCountQuery, $user);
        ProjectAccess::limitToVisible($completedTasksQuery, $user);

        $projectCount = $projectCountQuery->count();
        $taskCount = $taskCountQuery->count();
        $completedTasks = $completedTasksQuery->count();

        $healthProjects = $healthProjectsQuery
            ->get(['id', 'status', 'progress', 'budget', 'spent', 'start_date', 'end_date']);

        $healthSummary = ProjectHealth::summarize($healthProjects);

        $totalBudget = (float) $totalBudgetQuery->sum('budget');
        $totalSpent = (float) $totalSpentQuery->sum('spent');

        $newProjectCountQuery = Project::query()->whereBetween('created_at', [
            $today->copy()->startOfMonth(),
            $today->copy()->endOfMonth(),
        ]);

        $activeProjectCountQuery = Project::query()->where('status', 'in-progress');
        $pendingProjectCountQuery = Project::query()->whereIn('status', ['planning', 'on-hold']);
        $completedProjectCountQuery = Project::query()->where('status', 'completed');

        $overdueProjectCountQuery = Project::query()
            ->whereNotNull('end_date')
            ->where('end_date', '<', today())
            ->where('status', '!=', 'completed');

        $averageProgressQuery = Project::query();

        $projectsQuery = Project::query()
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'completed'),
                'projectResources',
            ])
            ->withHealthMetrics()
            ->latest()
            ->take(6);

        $tasksQuery = Task::query()
            ->with(['project:id,name', 'assignee:id,name'])
            ->orderByRaw('due_date asc nulls last')
            ->latest()
            ->take(6);

        $milestonesQuery = Milestone::query()
            ->with('project:id,name')
            ->whereDate('due_date', '>=', today())
            ->orderBy('due_date')
            ->take(5);

        $risksQuery = Risk::query()
            ->with('project:id,name')
            ->where('status', 'open')
            ->latest()
            ->take(4);

        $teamMembersQuery = ProjectResource::query()
            ->with([
                'user' => fn ($query) => $query
                    ->select('id', 'name', 'job_title', 'avatar', 'availability_percent')
                    ->withCount('assignedTasks'),
                'project:id,name',
            ])
            ->latest()
            ->take(5);

        if ($visibleProjectIds !== null) {
            $newProjectCountQuery->whereIn('id', $visibleProjectIds);
            $activeProjectCountQuery->whereIn('id', $visibleProjectIds);
            $pendingProjectCountQuery->whereIn('id', $visibleProjectIds);
            $completedProjectCountQuery->whereIn('id', $visibleProjectIds);
            $overdueProjectCountQuery->whereIn('id', $visibleProjectIds);
            $averageProgressQuery->whereIn('id', $visibleProjectIds);
            $projectsQuery->whereIn('id', $visibleProjectIds);
        }

        ProjectAccess::limitToVisible($tasksQuery, $user);
        ProjectAccess::limitToVisible($milestonesQuery, $user);
        ProjectAccess::limitToVisible($risksQuery, $user);
        ProjectAccess::limitToVisible($teamMembersQuery, $user, 'project_id');

        $projectActivity = $months->map(function (Carbon $month) use ($visibleProjectIds) {
            $query = Project::query()->whereBetween('created_at', [
                $month,
                $month->copy()->endOfMonth(),
            ]);

            if ($visibleProjectIds !== null) {
                $query->whereIn('id', $visibleProjectIds);
            }

            return $query->count();
        })->all();

        $taskActivity = $months->map(function (Carbon $month) use ($user) {
            $query = Task::query()->whereBetween('created_at', [
                $month,
                $month->copy()->endOfMonth(),
            ]);

            ProjectAccess::limitToVisible($query, $user);

            return $query->count();
        })->all();

        $thisWeekTasks = collect(range(0, 6))->map(function (int $day) use ($weekStart, $user) {
            $query = Task::query()->whereDate(
                'created_at',
                $weekStart->copy()->addDays($day)
            );

            ProjectAccess::limitToVisible($query, $user);

            return $query->count();
        })->all();

        $lastWeekTasks = collect(range(0, 6))->map(function (int $day) use ($lastWeekStart, $user) {
            $query = Task::query()->whereDate(
                'created_at',
                $lastWeekStart->copy()->addDays($day)
            );

            ProjectAccess::limitToVisible($query, $user);

            return $query->count();
        })->all();

        $sprintCountQuery = Sprint::query()->where('status', 'active');
        $riskCountQuery = Risk::query()->where('status', 'open');

        ProjectAccess::limitToVisible($sprintCountQuery, $user);
        ProjectAccess::limitToVisible($riskCountQuery, $user);

        return view('dashboard', [
            'projectCount' => $projectCount,
            'newProjectCount' => $newProjectCountQuery->count(),
            'activeProjectCount' => $activeProjectCountQuery->count(),
            'pendingProjectCount' => $pendingProjectCountQuery->count(),
            'completedProjectCount' => $completedProjectCountQuery->count(),
            'taskCount' => $taskCount,
            'completedTasks' => $completedTasks,
            'completionRate' => $taskCount
                ? (int) round(($completedTasks / $taskCount) * 100)
                : 0,
            'sprintCount' => $sprintCountQuery->count(),
            'riskCount' => $riskCountQuery->count(),

            'atRiskProjectCount' => $healthSummary['at_risk'],
            'criticalProjectCount' => $healthSummary['critical'],
            'overdueProjectCount' => $overdueProjectCountQuery->count(),
            'overdueTaskCount' => $healthSummary['overdue_tasks_total'],
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $totalBudget - $totalSpent,
            'overallCompletion' => (int) round($averageProgressQuery->avg('progress') ?? 0),

            'projects' => $projectsQuery->get(),
            'tasks' => $tasksQuery->get(),
            'milestones' => $milestonesQuery->get(),
            'risks' => $risksQuery->get(),
            'teamMembers' => $teamMembersQuery->get(),

            'monthlyTarget' => [
                'new' => $newProjectCountQuery->count(),
                'completed' => $completedProjectCountQuery->count(),
                'active' => Project::query()
                    ->when(
                        $visibleProjectIds !== null,
                        fn ($query) => $query->whereIn('id', $visibleProjectIds)
                    )
                    ->whereIn('status', ['in-progress', 'planning', 'on-hold'])
                    ->count(),
                'total' => $projectCount,
            ],

            'activityMonths' => $months->map(
                fn (Carbon $month) => $month->format('M')
            )->all(),

            'projectActivity' => $projectActivity,
            'taskActivity' => $taskActivity,
            'thisWeekTasks' => $thisWeekTasks,
            'lastWeekTasks' => $lastWeekTasks,
        ]);
    }
    public function projects(Request $request)
    {
        $search = trim((string) $request->query('search'));
        $user = $request->user();
        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        $query = Project::query()
            ->when($visibleProjectIds !== null, fn ($query) => $query->whereIn('id', $visibleProjectIds))
            ->when($search !== '', fn ($query) => $query->where('name', 'like', '%'.$search.'%'))
            ->withHealthMetrics()
            ->latest();

        return view('projects.index', [
            'projects' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function globalSearch(Request $request)
    {
        $data = $request->validate(['query' => ['nullable', 'string', 'max:100']]);
        $term = trim((string) ($data['query'] ?? ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['results' => []]);
        }

        $needle = Str::lower($term);
        $pages = collect($this->searchablePages())->filter(fn (array $page) => Str::contains(Str::lower(implode(' ', [$page['title'], $page['description'], $page['keywords']])), $needle))
            ->map(fn (array $page) => ['category' => 'Pages', 'type' => 'Page', 'title' => $page['title'], 'meta' => $page['description'], 'url' => route($page['route'])]);
        $projectsQuery = Project::query()
            ->where('name', 'like', '%'.$term.'%');

        $visibleProjectIds = ProjectAccess::visibleProjectIds($request->user());

        if ($visibleProjectIds !== null) {
            $projectsQuery->whereIn('id', $visibleProjectIds);
        }

        $projects = $projectsQuery
            ->limit(5)
            ->get()
            ->map(fn (Project $project) => ['category' => 'Data', 'type' => 'Project', 'title' => $project->name, 'meta' => ucfirst($project->status), 'url' => route('projects.show', $project)]);

        $tasksQuery = Task::query()
            ->with('project:id,name')
            ->where('title', 'like', '%'.$term.'%');

        ProjectAccess::limitToVisible($tasksQuery, $request->user());

        $tasks = $tasksQuery
            ->limit(5)
            ->get()
            ->map(fn (Task $task) => ['category' => 'Data', 'type' => 'Task', 'title' => $task->title, 'meta' => $task->project?->name ?? 'No project', 'url' => route('web.tasks.edit', $task->id)]);

        $sprintsQuery = Sprint::query()
            ->with('project:id,name')
            ->where('name', 'like', '%'.$term.'%');

        ProjectAccess::limitToVisible($sprintsQuery, $request->user());

        $sprints = $sprintsQuery
            ->limit(5)
            ->get()
            ->map(fn (Sprint $sprint) => ['category' => 'Data', 'type' => 'Sprint', 'title' => $sprint->name, 'meta' => $sprint->project?->name ?? 'No project', 'url' => route('web.sprints.edit', $sprint->id)]);

        $milestonesQuery = Milestone::query()
            ->with('project:id,name')
            ->where('name', 'like', '%'.$term.'%');

        ProjectAccess::limitToVisible($milestonesQuery, $request->user());

        $milestones = $milestonesQuery
            ->limit(5)
            ->get()
            ->map(fn (Milestone $milestone) => ['category' => 'Data', 'type' => 'Milestone', 'title' => $milestone->name, 'meta' => $milestone->project?->name ?? 'No project', 'url' => route('web.milestones.edit', $milestone->id)]);

        $risksQuery = Risk::query()
            ->with('project:id,name')
            ->where('title', 'like', '%'.$term.'%');

        ProjectAccess::limitToVisible($risksQuery, $request->user());

        $risks = $risksQuery
            ->limit(5)
            ->get()
            ->map(fn (Risk $risk) => ['category' => 'Data', 'type' => 'Risk', 'title' => $risk->title, 'meta' => $risk->project?->name ?? 'No project', 'url' => route('web.risks.edit', $risk->id)]);

        return response()->json(['results' => $pages->concat($projects)->concat($tasks)->concat($sprints)->concat($milestones)->concat($risks)->values()]);
    }

    private function searchablePages(): array
    {
        return [
            ['title' => 'Dashboard', 'description' => 'Portfolio overview', 'keywords' => 'overview home', 'route' => 'dashboard'], ['title' => 'Projects', 'description' => 'Manage projects', 'keywords' => 'project portfolio', 'route' => 'projects.index'], ['title' => 'Kick-Off', 'description' => 'Project initiation', 'keywords' => 'kickoff kick off', 'route' => 'web.kickoff.index'], ['title' => 'Stakeholders', 'description' => 'Stakeholder directory', 'keywords' => 'stakeholder people', 'route' => 'web.stakeholders.index'], ['title' => 'Sprints', 'description' => 'Agile sprint planning', 'keywords' => 'sprint agile', 'route' => 'web.sprints.index'], ['title' => 'Backlog', 'description' => 'Product backlog', 'keywords' => 'agile stories', 'route' => 'web.backlog.index'], ['title' => 'DoR / DoD', 'description' => 'Definition of ready and done', 'keywords' => 'definition ready done dor dod', 'route' => 'web.definitions.index'], ['title' => 'Tasks', 'description' => 'Project tasks', 'keywords' => 'task work', 'route' => 'web.tasks.index'], ['title' => 'Kanban', 'description' => 'Task board', 'keywords' => 'board task workflow', 'route' => 'kanban'], ['title' => 'Workflows', 'description' => 'Project workflows', 'keywords' => 'workflow process', 'route' => 'web.workflows.index'], ['title' => 'Team', 'description' => 'Project resources', 'keywords' => 'team resources people', 'route' => 'web.team.index'], ['title' => 'Time Tracking', 'description' => 'Track project time', 'keywords' => 'time hours', 'route' => 'web.time.index'], ['title' => 'Budget', 'description' => 'Project budget', 'keywords' => 'cost finance', 'route' => 'web.budget.index'], ['title' => 'Milestones', 'description' => 'Project milestones', 'keywords' => 'milestone deadline', 'route' => 'web.milestones.index'], ['title' => 'Gantt', 'description' => 'Project schedule', 'keywords' => 'gantt timeline schedule', 'route' => 'gantt'], ['title' => 'QA Testing', 'description' => 'Quality assurance testing', 'keywords' => 'qa quality test testing', 'route' => 'web.testing.index'], ['title' => 'Risks', 'description' => 'Project risk register', 'keywords' => 'risk issue', 'route' => 'web.risks.index'], ['title' => 'Change Log', 'description' => 'Project changes', 'keywords' => 'change changes', 'route' => 'web.changes.index'], ['title' => 'Analytics', 'description' => 'Project reports', 'keywords' => 'analytics report reports', 'route' => 'analytics'], ['title' => 'Documents', 'description' => 'Project documents', 'keywords' => 'document files', 'route' => 'web.documents.index'], ['title' => 'Lessons', 'description' => 'Lessons learned', 'keywords' => 'lesson learned retrospective', 'route' => 'web.lessons.index'], ['title' => 'Project Chat', 'description' => 'Project conversations', 'keywords' => 'chat messages communication', 'route' => 'chat'],
        ];
    }

    public function createProject(Request $request)
    {
        abort_unless(
            ProjectAccess::canCreate($request->user()),
            403,
            'Only admins and project managers can create projects.'
        );

        return view('projects.form', [
            'project' => new Project,
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'job_title']),
        ]);
    }
    public function storeProject(Request $request)
    {
        abort_unless(
            ProjectAccess::canCreate($request->user()),
            403,
            'Only admins and project managers can create projects.'
        );

        $data = $this->projectData($request);
        $memberIds = $this->projectMembersData($request);

        $data['owner_id'] = $request->user()->id;

        $project = Project::create($data);

        $project->members()->sync($memberIds);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created successfully.');
    }
    public function showPublicProject(Project $project)
    {
        $project->load('milestones');
        return \Inertia\Inertia::render('projects/PublicProjectDetails', [
            'project' => $project->only([
                'id',
                'name',
                'description',
                'status',
                'progress',
                'priority',
                'start_date',
                'end_date',
                'project_type',
                'methodology',
            ]),
            'milestones' => $project->milestones,
        ]);
    }    public function showProject(Request $request, Project $project)
    {
        abort_unless(ProjectAccess::canView($request->user(), $project), 403);

        $project->load(['tasks' => fn ($query) => $query->latest()->take(8)]);

        return view('projects.show', compact('project'));
    }

    public function editProject(Request $request, Project $project)
    {
        abort_unless(ProjectAccess::canManage($request->user(), $project), 403);

        $project->load('members');

        return view('projects.form', [
            'project' => $project,
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'job_title']),
        ]);
    }
    public function updateProject(Request $request, Project $project)
    {
        abort_unless(ProjectAccess::canManage($request->user(), $project), 403);

        $project->update($this->projectData($request));

        $memberIds = $this->projectMembersData($request);

        $project->members()->sync($memberIds);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }
    public function deleteProject(Request $request, Project $project)
    {
        abort_unless(ProjectAccess::canManage($request->user(), $project), 403);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function feature(string $feature)
    {
        $request = request();
        $definition = TrackerFeatures::get($feature);

        $query = $definition['model']::query()
            ->with($definition['with']);

        ProjectAccess::limitToVisible($query, $request->user());

        $items = $query
            ->latest()
            ->paginate(15);

        return view('features.index', [
            'title' => $definition['title'],
            'subtitle' => $definition['subtitle'],
            'columns' => $definition['columns'],
            'items' => $items,
            'feature' => $feature,
            'prefix' => $definition['prefix'],
        ]);
    }

    public function createFeature(string $feature)
    {
        $request = request();
        $definition = TrackerFeatures::get($feature);
        $user = $request->user();
        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        $projects = Project::query()
            ->when($visibleProjectIds !== null, fn ($query) => $query->whereIn('id', $visibleProjectIds))
            ->orderBy('name')
            ->get();

        $tasks = Task::query()
            ->with('project:id,name')
            ->when($visibleProjectIds !== null, fn ($query) => $query->whereIn('project_id', $visibleProjectIds))
            ->orderBy('title')
            ->get();

        return view('features.form', [
            'title' => 'Create '.$definition['title'],
            'feature' => $feature,
            'item' => new $definition['model'],
            'fields' => $definition['fields'],
            'prefix' => $definition['prefix'],
            'projects' => $projects,
            'users' => User::query()->orderBy('name')->get(),
            'tasks' => $tasks,
        ]);
    }

    public function storeFeature(Request $request, string $feature)
    {
        $definition = TrackerFeatures::get($feature);
        $data = $this->featureData($request, $feature);

        $project = !empty($data['project_id'])
            ? Project::find($data['project_id'])
            : null;

        abort_unless(
            ProjectAccess::canCreateIn($request->user(), $project),
            403
        );

        $definition['model']::query()->create($data);

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' created successfully.');
    }

    public function editFeature(string $feature, int $id)
    {
        $request = request();
        $definition = TrackerFeatures::get($feature);
        $item = $definition['model']::query()->findOrFail($id);

        abort_unless(
            ProjectAccess::canManageItem($request->user(), $this->featureProject($item)),
            403
        );

        $user = $request->user();
        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        $projects = Project::query()
            ->when($visibleProjectIds !== null, fn ($query) => $query->whereIn('id', $visibleProjectIds))
            ->orderBy('name')
            ->get();

        $tasks = Task::query()
            ->with('project:id,name')
            ->when($visibleProjectIds !== null, fn ($query) => $query->whereIn('project_id', $visibleProjectIds))
            ->orderBy('title')
            ->get();

        return view('features.form', [
            'title' => 'Edit '.$definition['title'],
            'feature' => $feature,
            'item' => $item,
            'fields' => $definition['fields'],
            'prefix' => $definition['prefix'],
            'projects' => $projects,
            'users' => User::query()->orderBy('name')->get(),
            'tasks' => $tasks,
        ]);
    }

    public function updateFeature(Request $request, string $feature, int $id)
    {
        $definition = TrackerFeatures::get($feature);
        $item = $definition['model']::query()->findOrFail($id);

        abort_unless(
            ProjectAccess::canManageItem($request->user(), $this->featureProject($item)),
            403
        );

        $data = $this->featureData($request, $feature, $item->getKey());

        if (array_key_exists('project_id', $data)) {
            $newProject = !empty($data['project_id'])
                ? Project::find($data['project_id'])
                : null;

            abort_unless(
                ProjectAccess::canCreateIn($request->user(), $newProject),
                403
            );
        }

        $item->update($data);

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' updated successfully.');
    }

    public function deleteFeature(string $feature, int $id)
    {
        $request = request();
        $definition = TrackerFeatures::get($feature);
        $item = $definition['model']::query()->findOrFail($id);

        abort_unless(
            ProjectAccess::canManageItem($request->user(), $this->featureProject($item)),
            403
        );

        $item->delete();

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' deleted.');
    }

    public function kanban(Request $request)
    {
        $query = Task::query()
            ->with('project:id,name', 'assignee:id,name');

        ProjectAccess::limitToVisible($query, $request->user());

        $tasks = $query
            ->orderBy('position')
            ->latest()
            ->get();

        return view('features.kanban', [
            'columns' => [
                'pending' => $tasks->where('status', 'pending'),
                'in-progress' => $tasks->where('status', 'in-progress'),
                'completed' => $tasks->where('status', 'completed'),
            ],
        ]);
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        abort_unless(
            ProjectAccess::canManageItem($request->user(), $task->project),
            403
        );

        $data = $request->validate([
            'status' => ['required', 'in:pending,in-progress,completed'],
        ]);

        $task->update($data);

        return back()->with('success', 'Task status updated.');
    }

    public function gantt(Request $request)
    {
        $projectsQuery = Project::query()
            ->orderBy('start_date');

        $visibleProjectIds = ProjectAccess::visibleProjectIds($request->user());

        if ($visibleProjectIds !== null) {
            $projectsQuery->whereIn('id', $visibleProjectIds);
        }

        return view('features.gantt', [
            'projects' => $projectsQuery->get(),
        ]);
    }

    public function analytics(Request $request)
    {
        $user = $request->user();

        $projectCountQuery = Project::query();
        $taskCountQuery = Task::query();
        $completedTasksQuery = Task::query()->where('status', 'completed');
        $openRisksQuery = Risk::query()->where('status', 'open');
        $projectsQuery = Project::query()->latest()->take(8);

        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        if ($visibleProjectIds !== null) {
            $projectCountQuery->whereIn('id', $visibleProjectIds);
            $projectsQuery->whereIn('id', $visibleProjectIds);
        }

        ProjectAccess::limitToVisible($taskCountQuery, $user);
        ProjectAccess::limitToVisible($completedTasksQuery, $user);
        ProjectAccess::limitToVisible($openRisksQuery, $user);

        return view('features.analytics', [
            'projectCount' => $projectCountQuery->count(),
            'taskCount' => $taskCountQuery->count(),
            'completedTasks' => $completedTasksQuery->count(),
            'openRisks' => $openRisksQuery->count(),
            'projects' => $projectsQuery->get(),
        ]);
    }

    public function chat(Request $request)
    {
        $user = $request->user();

        $channelsQuery = ChatChannel::query()
            ->with('project:id,name');

        $visibleProjectIds = ProjectAccess::visibleProjectIds($user);

        if ($visibleProjectIds === null) {
            // Admins can see all channels.
        } else {
            $channelsQuery->whereIn('project_id', $visibleProjectIds);
        }

        $channels = $channelsQuery->latest()->get();

        $active = $channels->firstWhere('id', (int) $request->query('channel')) ?? $channels->first();

        $messages = $active
            ? $active->messages()->with('user:id,name')->latest()->take(50)->get()->reverse()
            : collect();

        return view('features.chat', compact('channels', 'active', 'messages'));
    }

    public function storeChannel(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $project = !empty($data['project_id'])
            ? Project::find($data['project_id'])
            : null;

        abort_unless(
            ProjectAccess::canCreateIn($request->user(), $project),
            403
        );

        $channel = ChatChannel::query()->create($data);

        return redirect('/chat?channel='.$channel->id)->with('success', 'Channel created.');
    }

    public function storeMessage(Request $request, ChatChannel $channel)
    {
        $project = $channel->project;

        abort_unless(
            ProjectAccess::canViewItem($request->user(), $project),
            403
        );

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        ChatMessage::query()->create([
            'chat_channel_id' => $channel->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ]);

        return redirect('/chat?channel='.$channel->id);
    }

    private function featureIndex(string $prefix): string
    {
        return '/'.$prefix;
    }

    private function projectData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'client' => ['nullable', 'string', 'max:255'],
                        'project_type' => ['required', 'in:predictive,agile,hybrid'],
            'status' => ['required', 'in:planning,in-progress,on-hold,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
        ]);
    }

    private function projectMembersData(Request $request): array
    {
        return $request->validate([
            'members' => ['required', 'array', 'min:2'],
            'members.*' => ['integer', 'distinct', 'exists:users,id'],
        ])['members'];
    }

    private function teamOptions(): array
    {
        return Project::query()
            ->whereNotNull('team')
            ->where('team', '!=', '')
            ->distinct()
            ->orderBy('team')
            ->pluck('team')
            ->all();
    }

    private function featureData(Request $request, string $feature, ?int $id = null): array
    {
        $data = $request->validate($this->featureRules($feature, $id));

        if (array_key_exists('stages', $data) && is_string($data['stages'])) {
            $data['stages'] = array_values(array_filter(array_map('trim', explode(',', $data['stages']))));
        }

        if (array_key_exists('checked', $data)) {
            $data['checked'] = (bool) $data['checked'];
        }

        if ($feature === 'milestones') {
            $data['date'] = $data['due_date'] ?? $data['date'] ?? now()->toDateString();
            $data['due_date'] = $data['due_date'] ?? $data['date'];
        }

        if ($feature === 'risks') {
            $data['owner_id'] = $data['owner_id'] ?? request()->user()->id;
            $data['category'] = $data['category'] ?? 'technical';
        }

        if ($feature === 'changes') {
            $data['requestor_id'] = $data['requestor_id'] ?? request()->user()->id;
        }

        if ($feature === 'documents') {
            $data['uploaded_by'] = $data['uploaded_by'] ?? request()->user()->id;
            $data['file_path'] = $data['file_path'] ?: 'documents/'.Str::slug($data['name']);
            $data['size'] = $data['size'] ?: '0 KB';
        }

        if ($feature === 'time') {
            $data['user_id'] = $data['user_id'] ?? request()->user()->id;
        }

        if ($feature === 'team') {
            $data['user_id'] = $data['user_id'] ?? request()->user()->id;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function featureRules(string $feature, ?int $id = null): array
    {
        $project = ['nullable', 'exists:projects,id'];
        $requiredProject = ['required', 'exists:projects,id'];

        return match ($feature) {
            'kickoff' => [
                'project_id' => $requiredProject,
                'date' => ['required', 'date'],
                'attendees' => ['required', 'integer', 'min:0'],
                'status' => ['required', 'in:scheduled,completed'],
            ],
            'stakeholders' => [
                'project_id' => $project,
                'name' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
                'department' => ['nullable', 'string', 'max:255'],
                'influence' => ['required', 'in:low,medium,high'],
                'interest' => ['required', 'in:low,medium,high'],
            ],
            'sprints' => [
                'project_id' => $requiredProject,
                'name' => ['required', 'string', 'max:255'],
                'goal' => ['nullable', 'string'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'status' => ['nullable', 'in:planned,active,completed'],
            ],
            'backlog' => [
                'project_id' => $requiredProject,
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:epic,feature,story'],
                'priority' => ['nullable', 'in:low,medium,high'],
                'points' => ['nullable', 'integer', 'min:0'],
                'status' => ['nullable', 'in:backlog,ready,in-progress'],
            ],
            'definitions' => [
                'project_id' => $project,
                'list_type' => ['required', 'in:dor,dod'],
                'text' => ['required', 'string'],
                'checked' => ['nullable', 'boolean'],
            ],
            'tasks' => [
                'project_id' => $requiredProject,
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'status' => ['nullable', 'in:pending,in-progress,completed'],
                'priority' => ['nullable', 'in:low,medium,high'],
                'due_date' => ['nullable', 'date'],
                'assigned_to' => ['nullable', 'exists:users,id'],
            ],
            'workflows' => [
                'project_id' => $project,
                'name' => ['required', 'string', 'max:255'],
                'stages' => ['required', 'string'],
            ],
            'team' => [
                'project_id' => $requiredProject,
                'user_id' => ['required', 'exists:users,id', Rule::unique('project_resources', 'user_id')->ignore($id)->where('project_id', request('project_id'))],
                'allocation_percent' => ['required', 'integer', 'between:1,100'],
            ],
            'time' => [
                'project_id' => $requiredProject,
                'task_id' => ['nullable', 'exists:tasks,id'],
                'user_id' => ['required', 'exists:users,id'],
                'date' => ['required', 'date'],
                'hours' => ['required', 'numeric', 'min:0', 'max:24'],
            ],
            'budget' => [
                'project_id' => $requiredProject,
                'category' => ['required', 'string', 'max:255'],
                'allocated' => ['nullable', 'numeric', 'min:0'],
                'spent' => ['nullable', 'numeric', 'min:0'],
                'status' => ['nullable', 'in:on-track,under,over'],
            ],
            'milestones' => [
                'project_id' => $requiredProject,
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'due_date' => ['required', 'date'],
                'status' => ['nullable', 'in:upcoming,pending,in-progress,completed'],
            ],
            'testing' => [
                'project_id' => $requiredProject,
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:functional,performance,ui'],
                'status' => ['nullable', 'in:passed,failed,pending'],
                'priority' => ['nullable', 'in:low,medium,high'],
                'last_run' => ['nullable', 'date'],
            ],
            'risks' => [
                'project_id' => $requiredProject,
                'title' => ['required', 'string', 'max:255'],
                'category' => ['nullable', 'in:resource,scope,technical,external,financial'],
                'description' => ['nullable', 'string'],
                'probability' => ['nullable', 'in:low,medium,high'],
                'impact' => ['nullable', 'in:low,medium,high'],
                'status' => ['nullable', 'in:open,mitigating,mitigated,closed'],
                'mitigation' => ['nullable', 'string'],
            ],
            'changes' => [
                'project_id' => $requiredProject,
                'title' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:feature,schedule,scope,budget'],
                'status' => ['nullable', 'in:pending,approved,rejected'],
                'impact' => ['required', 'in:low,medium,high'],
                'date' => ['required', 'date'],
            ],
            'documents' => [
                'project_id' => $requiredProject,
                'name' => ['required', 'string', 'max:255'],
                'type' => ['required', 'in:pdf,doc,excel,design,other'],
                'size' => ['nullable', 'string', 'max:50'],
                'file_path' => ['nullable', 'string', 'max:255'],
            ],
            'lessons' => [
                'project_id' => $requiredProject,
                'title' => ['required', 'string', 'max:255'],
                'category' => ['required', 'in:process,technical,team,scope,quality'],
                'impact' => ['required', 'in:positive,negative'],
                'description' => ['nullable', 'string'],
                'date' => ['required', 'date'],
            ],
            'channels' => [
                'project_id' => $project,
                'name' => ['required', 'string', 'max:255'],
            ],
            default => abort(404),
        };
    }


    private function featureProject(object $item): ?Project
    {
        return !empty($item->project_id)
            ? Project::find($item->project_id)
            : null;
    }
}
