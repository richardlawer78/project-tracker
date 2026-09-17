<?php

namespace App\Support;

use App\Models\BacklogItem;
use App\Models\BudgetItem;
use App\Models\ChangeLogEntry;
use App\Models\ChatChannel;
use App\Models\Document;
use App\Models\DorDodItem;
use App\Models\Kickoff;
use App\Models\LessonLearned;
use App\Models\Milestone;
use App\Models\ProjectResource;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Stakeholder;
use App\Models\Task;
use App\Models\TestCase;
use App\Models\TimeEntry;
use App\Models\Workflow;

class TrackerFeatures
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'kickoff' => [
                'title' => 'Project Kick-Off',
                'subtitle' => 'Initialize and launch new projects',
                'model' => Kickoff::class,
                'prefix' => 'initiation/kickoff',
                'with' => ['project:id,name'],
                'columns' => ['project', 'date', 'attendees', 'status'],
                'fields' => ['project_id', 'date', 'attendees', 'status'],
            ],
            'stakeholders' => [
                'title' => 'Stakeholders',
                'subtitle' => 'Manage project stakeholders and communication',
                'model' => Stakeholder::class,
                'prefix' => 'initiation/stakeholders',
                'with' => ['project:id,name'],
                'columns' => ['name', 'role', 'department', 'influence', 'interest'],
                'fields' => ['project_id', 'name', 'role', 'department', 'influence', 'interest'],
            ],
            'sprints' => [
                'title' => 'Sprints',
                'subtitle' => 'Manage sprint cycles and iterations',
                'model' => Sprint::class,
                'prefix' => 'agile/sprints',
                'with' => ['project:id,name'],
                'columns' => ['name', 'goal', 'start_date', 'end_date', 'status'],
                'fields' => ['project_id', 'name', 'goal', 'start_date', 'end_date', 'status'],
            ],
            'backlog' => [
                'title' => 'Product Backlog',
                'subtitle' => 'Prioritize and manage backlog items',
                'model' => BacklogItem::class,
                'prefix' => 'agile/backlog',
                'with' => ['project:id,name'],
                'columns' => ['title', 'type', 'priority', 'points', 'status'],
                'fields' => ['project_id', 'title', 'type', 'priority', 'points', 'status'],
            ],
            'definitions' => [
                'title' => 'DoR / DoD Framework',
                'subtitle' => 'Definition of Ready and Done checklists',
                'model' => DorDodItem::class,
                'prefix' => 'agile/definitions',
                'with' => ['project:id,name'],
                'columns' => ['list_type', 'text', 'checked'],
                'fields' => ['project_id', 'list_type', 'text', 'checked'],
            ],
            'tasks' => [
                'title' => 'Task List',
                'subtitle' => 'Manage all tasks across projects',
                'model' => Task::class,
                'prefix' => 'tasks',
                'with' => ['project:id,name', 'assignee:id,name'],
                'columns' => ['title', 'project', 'status', 'priority', 'due_date'],
                'fields' => ['project_id', 'title', 'description', 'status', 'priority', 'due_date', 'assigned_to'],
            ],
            'workflows' => [
                'title' => 'Workflows',
                'subtitle' => 'Manage task workflows and stages',
                'model' => Workflow::class,
                'prefix' => 'tasks/workflows',
                'with' => ['project:id,name'],
                'columns' => ['name', 'stages'],
                'fields' => ['project_id', 'name', 'stages'],
            ],
            'team' => [
                'title' => 'Team Resources',
                'subtitle' => 'Manage team members and allocations',
                'model' => ProjectResource::class,
                'prefix' => 'resources/team',
                'with' => ['project:id,name', 'user:id,name'],
                'columns' => ['user', 'project', 'allocation_percent'],
                'fields' => ['project_id', 'user_id', 'allocation_percent'],
            ],
            'time' => [
                'title' => 'Time Tracking',
                'subtitle' => 'Track time spent on projects and tasks',
                'model' => TimeEntry::class,
                'prefix' => 'resources/time-tracking',
                'with' => ['project:id,name', 'task:id,title', 'user:id,name'],
                'columns' => ['date', 'hours', 'project', 'user'],
                'fields' => ['project_id', 'task_id', 'user_id', 'date', 'hours'],
            ],
            'budget' => [
                'title' => 'Budget Management',
                'subtitle' => 'Track project budgets and spending',
                'model' => BudgetItem::class,
                'prefix' => 'resources/budget',
                'with' => ['project:id,name'],
                'columns' => ['category', 'allocated', 'spent', 'status'],
                'fields' => ['project_id', 'category', 'allocated', 'spent', 'status'],
            ],
            'milestones' => [
                'title' => 'Milestones',
                'subtitle' => 'Track project milestones and deliverables',
                'model' => Milestone::class,
                'prefix' => 'resources/milestones',
                'with' => ['project:id,name'],
                'columns' => ['name', 'due_date', 'status', 'project'],
                'fields' => ['project_id', 'name', 'description', 'due_date', 'status'],
            ],
            'testing' => [
                'title' => 'QA & Testing',
                'subtitle' => 'Manage test cases and quality metrics',
                'model' => TestCase::class,
                'prefix' => 'quality/qa-testing',
                'with' => ['project:id,name'],
                'columns' => ['name', 'type', 'status', 'priority', 'last_run'],
                'fields' => ['project_id', 'name', 'type', 'status', 'priority', 'last_run'],
            ],
            'risks' => [
                'title' => 'Risks & Issues',
                'subtitle' => 'Track and manage project risks',
                'model' => Risk::class,
                'prefix' => 'quality/risks',
                'with' => ['project:id,name'],
                'columns' => ['title', 'probability', 'impact', 'status'],
                'fields' => ['project_id', 'title', 'category', 'description', 'probability', 'impact', 'status', 'mitigation'],
            ],
            'changes' => [
                'title' => 'Change Log',
                'subtitle' => 'Track change requests and approvals',
                'model' => ChangeLogEntry::class,
                'prefix' => 'quality/change-log',
                'with' => ['project:id,name'],
                'columns' => ['title', 'type', 'status', 'impact', 'date'],
                'fields' => ['project_id', 'title', 'type', 'status', 'impact', 'date'],
            ],
            'documents' => [
                'title' => 'Documents',
                'subtitle' => 'Manage project documents and files',
                'model' => Document::class,
                'prefix' => 'reports/documents',
                'with' => ['project:id,name'],
                'columns' => ['name', 'type', 'size', 'created_at'],
                'fields' => ['project_id', 'name', 'type', 'size', 'file_path'],
            ],
            'lessons' => [
                'title' => 'Lessons Learned',
                'subtitle' => 'Capture and share project insights',
                'model' => LessonLearned::class,
                'prefix' => 'reports/lessons-learned',
                'with' => ['project:id,name'],
                'columns' => ['title', 'category', 'impact', 'date'],
                'fields' => ['project_id', 'title', 'category', 'impact', 'description', 'date'],
            ],
            'channels' => [
                'title' => 'Project Chat',
                'subtitle' => 'Team communication channels',
                'model' => ChatChannel::class,
                'prefix' => 'chat/channels',
                'with' => ['project:id,name'],
                'columns' => ['name', 'created_at'],
                'fields' => ['project_id', 'name'],
            ],
        ];
    }

    public static function get(string $feature): array
    {
        $all = self::all();
        abort_unless(isset($all[$feature]), 404);

        return $all[$feature];
    }
}
