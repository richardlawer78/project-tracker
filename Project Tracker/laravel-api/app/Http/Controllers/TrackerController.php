<?php

namespace App\Http\Controllers;

use App\Models\ChatChannel;
use App\Models\ChatMessage;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use App\Support\TrackerFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TrackerController extends Controller
{
    public function dashboard()
    {
        return view('dashboard', [
            'projectCount' => Project::count(),
            'taskCount' => Task::count(),
            'completedTasks' => Task::where('status', 'completed')->count(),
            'sprintCount' => Sprint::count(),
            'riskCount' => Risk::query()->where('status', 'open')->count(),
            'projects' => Project::latest()->take(5)->get(),
            'tasks' => Task::with('project')->latest()->take(6)->get(),
            'milestones' => Milestone::with('project')->latest()->take(5)->get(),
        ]);
    }

    public function projects()
    {
        return view('projects.index', ['projects' => Project::latest()->paginate(12)]);
    }

    public function createProject()
    {
        return view('projects.form', ['project' => new Project]);
    }

    public function storeProject(Request $request)
    {
        Project::create($this->projectData($request));

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function showProject(Project $project)
    {
        $project->load(['tasks' => fn ($query) => $query->latest()->take(8)]);

        return view('projects.show', compact('project'));
    }

    public function editProject(Project $project)
    {
        return view('projects.form', compact('project'));
    }

    public function updateProject(Request $request, Project $project)
    {
        $project->update($this->projectData($request));

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function deleteProject(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    public function feature(string $feature)
    {
        $definition = TrackerFeatures::get($feature);
        $items = $definition['model']::query()
            ->with($definition['with'])
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
        $definition = TrackerFeatures::get($feature);

        return view('features.form', [
            'title' => 'Create '.$definition['title'],
            'feature' => $feature,
            'item' => new $definition['model'],
            'fields' => $definition['fields'],
            'prefix' => $definition['prefix'],
            'projects' => Project::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'tasks' => Task::query()->orderBy('title')->get(),
        ]);
    }

    public function storeFeature(Request $request, string $feature)
    {
        $definition = TrackerFeatures::get($feature);
        $definition['model']::query()->create($this->featureData($request, $feature));

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' created successfully.');
    }

    public function editFeature(string $feature, int $id)
    {
        $definition = TrackerFeatures::get($feature);
        $item = $definition['model']::query()->findOrFail($id);

        return view('features.form', [
            'title' => 'Edit '.$definition['title'],
            'feature' => $feature,
            'item' => $item,
            'fields' => $definition['fields'],
            'prefix' => $definition['prefix'],
            'projects' => Project::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
            'tasks' => Task::query()->orderBy('title')->get(),
        ]);
    }

    public function updateFeature(Request $request, string $feature, int $id)
    {
        $definition = TrackerFeatures::get($feature);
        $item = $definition['model']::query()->findOrFail($id);
        $item->update($this->featureData($request, $feature, $item->getKey()));

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' updated successfully.');
    }

    public function deleteFeature(string $feature, int $id)
    {
        $definition = TrackerFeatures::get($feature);
        $definition['model']::query()->findOrFail($id)->delete();

        return redirect($this->featureIndex($definition['prefix']))
            ->with('success', Str::headline($feature).' deleted.');
    }

    public function kanban()
    {
        $tasks = Task::query()->with('project:id,name', 'assignee:id,name')->orderBy('position')->latest()->get();

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
        $data = $request->validate([
            'status' => ['required', 'in:pending,in-progress,completed'],
        ]);
        $task->update($data);

        return back()->with('success', 'Task status updated.');
    }

    public function gantt()
    {
        return view('features.gantt', [
            'projects' => Project::query()->orderBy('start_date')->get(),
        ]);
    }

    public function analytics()
    {
        return view('features.analytics', [
            'projectCount' => Project::count(),
            'taskCount' => Task::count(),
            'completedTasks' => Task::where('status', 'completed')->count(),
            'openRisks' => Risk::query()->where('status', 'open')->count(),
            'projects' => Project::query()->latest()->take(8)->get(),
        ]);
    }

    public function chat(Request $request)
    {
        $channels = ChatChannel::query()->with('project:id,name')->latest()->get();
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
        $channel = ChatChannel::query()->create($data);

        return redirect('/chat?channel='.$channel->id)->with('success', 'Channel created.');
    }

    public function storeMessage(Request $request, ChatChannel $channel)
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        ChatMessage::query()->create([
            'chat_channel_id' => $channel->id,
            'user_id' => $this->defaultUserId(),
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
            'team' => ['nullable', 'string', 'max:255'],
            'project_type' => ['required', 'in:predictive,agile,hybrid'],
            'status' => ['required', 'in:planning,in-progress,on-hold,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'between:0,100'],
        ]);
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
            $data['owner_id'] = $data['owner_id'] ?? $this->defaultUserId();
            $data['category'] = $data['category'] ?? 'technical';
        }

        if ($feature === 'changes') {
            $data['requestor_id'] = $data['requestor_id'] ?? $this->defaultUserId();
        }

        if ($feature === 'documents') {
            $data['uploaded_by'] = $data['uploaded_by'] ?? $this->defaultUserId();
            $data['file_path'] = $data['file_path'] ?: 'documents/'.Str::slug($data['name']);
            $data['size'] = $data['size'] ?: '0 KB';
        }

        if ($feature === 'time') {
            $data['user_id'] = $data['user_id'] ?? $this->defaultUserId();
        }

        if ($feature === 'team') {
            $data['user_id'] = $data['user_id'] ?? $this->defaultUserId();
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

    private function defaultUserId(): int
    {
        $id = User::query()->value('id');
        if ($id) {
            return (int) $id;
        }

        return User::query()->create([
            'name' => 'Project Tracker',
            'email' => 'tracker@example.com',
            'password' => 'password',
            'role' => 'admin',
        ])->id;
    }
}
