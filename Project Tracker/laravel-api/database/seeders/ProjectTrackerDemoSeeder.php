<?php

namespace Database\Seeders;

use App\Models\BacklogItem;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\ProjectResource;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Stakeholder;
use App\Models\Task;
use App\Models\User;
use App\Models\Workflow;
use Illuminate\Database\Seeder;

class ProjectTrackerDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = collect([
            ['name' => 'Richard Dom', 'email' => 'richard.dom@project-tracker.demo', 'role' => 'team-lead', 'job_title' => 'Team Leader', 'avatar' => 'assets/avatars/richard-dom.jpg'],
            ['name' => 'Nikki Jey', 'email' => 'nikki.jey@project-tracker.demo', 'role' => 'developer', 'job_title' => 'UI Developer', 'avatar' => 'assets/avatars/nikki-jey.jpg'],
            ['name' => 'Arifa Zed', 'email' => 'arifa.zed@project-tracker.demo', 'role' => 'developer', 'job_title' => 'Web Developer', 'avatar' => 'assets/avatars/arifa-zed.jpg'],
            ['name' => 'Xiong Yu', 'email' => 'xiong.yu@project-tracker.demo', 'role' => 'team-member', 'job_title' => 'Team Member', 'avatar' => 'assets/avatars/xiong-yu.jpg'],
            ['name' => 'Emanuel Gen', 'email' => 'emanuel.gen@project-tracker.demo', 'role' => 'project-manager', 'job_title' => 'Project Manager', 'avatar' => 'assets/avatars/emanuel-gen.jpg'],
        ])->mapWithKeys(function (array $attributes): array {
            $user = User::query()->firstOrCreate(
                ['email' => $attributes['email']],
                $attributes + ['password' => 'password', 'availability_percent' => 100],
            );

            if (! $user->avatar) {
                $user->update(['avatar' => $attributes['avatar']]);
            }

            return [$attributes['name'] => $user];
        });

        $projects = collect([
            ['name' => 'E-Commerce Website Development', 'client' => 'Retail Collective', 'team' => 'Digital Commerce Team', 'project_type' => 'agile', 'status' => 'in-progress', 'priority' => 'high', 'progress' => 68, 'owner' => 'Emanuel Gen', 'start' => now()->subMonths(2), 'end' => now()->addMonths(2)],
            ['name' => 'Platform Development', 'client' => 'Internal Products', 'team' => 'Platform Team', 'project_type' => 'agile', 'status' => 'in-progress', 'priority' => 'high', 'progress' => 46, 'owner' => 'Richard Dom', 'start' => now()->subMonth(), 'end' => now()->addMonths(3)],
            ['name' => 'Website Redesign', 'client' => 'Kedebah', 'team' => 'Experience Team', 'project_type' => 'predictive', 'status' => 'planning', 'priority' => 'medium', 'progress' => 24, 'owner' => 'Nikki Jey', 'start' => now(), 'end' => now()->addMonths(4)],
            ['name' => 'Mobile Application Development', 'client' => 'Field Services', 'team' => 'Mobile Team', 'project_type' => 'hybrid', 'status' => 'in-progress', 'priority' => 'high', 'progress' => 58, 'owner' => 'Arifa Zed', 'start' => now()->subMonths(3), 'end' => now()->addMonth()],
            ['name' => 'Project Management System', 'client' => 'Internal Operations', 'team' => 'Delivery Operations', 'project_type' => 'agile', 'status' => 'completed', 'priority' => 'medium', 'progress' => 100, 'owner' => 'Richard Dom', 'start' => now()->subMonths(5), 'end' => now()->subWeek()],
        ])->mapWithKeys(function (array $attributes) use ($users): array {
            $project = Project::query()->firstOrCreate(['name' => $attributes['name']], [
                'owner_id' => $users[$attributes['owner']]->id,
                'description' => 'Default Project Tracker demonstration project.',
                'client' => $attributes['client'],
                'team' => $attributes['team'],
                'project_type' => $attributes['project_type'],
                'status' => $attributes['status'],
                'priority' => $attributes['priority'],
                'start_date' => $attributes['start']->toDateString(),
                'end_date' => $attributes['end']->toDateString(),
                'budget' => 0,
                'spent' => 0,
                'progress' => $attributes['progress'],
            ]);

            return [$attributes['name'] => $project];
        });

        foreach ($projects as $project) {
            foreach ($users as $user) {
                ProjectResource::query()->firstOrCreate([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ], ['allocation_percent' => $project->owner_id === $user->id ? 80 : 45]);
            }
        }

        foreach ([
            ['project' => 'E-Commerce Website Development', 'title' => 'Complete checkout experience', 'description' => 'Finish the secure checkout flow and order confirmation screens.', 'status' => 'in-progress', 'priority' => 'high', 'assignee' => 'Nikki Jey', 'due' => now()->addDays(5)],
            ['project' => 'E-Commerce Website Development', 'title' => 'Validate payment integration', 'description' => 'Run payment provider acceptance checks.', 'status' => 'pending', 'priority' => 'high', 'assignee' => 'Arifa Zed', 'due' => now()->addDays(8)],
            ['project' => 'Platform Development', 'title' => 'Publish API integration guide', 'description' => 'Document the API contracts for consuming teams.', 'status' => 'pending', 'priority' => 'medium', 'assignee' => 'Xiong Yu', 'due' => now()->addDays(12)],
            ['project' => 'Platform Development', 'title' => 'Configure delivery monitoring', 'description' => 'Set up dashboards and alert thresholds.', 'status' => 'completed', 'priority' => 'medium', 'assignee' => 'Richard Dom', 'due' => now()->subDays(3)],
            ['project' => 'Website Redesign', 'title' => 'Approve homepage direction', 'description' => 'Review the first visual direction with stakeholders.', 'status' => 'pending', 'priority' => 'medium', 'assignee' => 'Nikki Jey', 'due' => now()->addDays(15)],
            ['project' => 'Mobile Application Development', 'title' => 'Complete mobile usability testing', 'description' => 'Record and prioritize findings from the pilot group.', 'status' => 'in-progress', 'priority' => 'high', 'assignee' => 'Arifa Zed', 'due' => now()->addDays(3)],
            ['project' => 'Project Management System', 'title' => 'Close delivery retrospective', 'description' => 'Capture actions and lessons from the completed project.', 'status' => 'completed', 'priority' => 'low', 'assignee' => 'Emanuel Gen', 'due' => now()->subWeek()],
        ] as $attributes) {
            Task::query()->firstOrCreate([
                'project_id' => $projects[$attributes['project']]->id,
                'title' => $attributes['title'],
            ], [
                'assigned_to' => $users[$attributes['assignee']]->id,
                'description' => $attributes['description'],
                'status' => $attributes['status'],
                'priority' => $attributes['priority'],
                'start_date' => now()->subWeek()->toDateString(),
                'due_date' => $attributes['due']->toDateString(),
                'estimated_hours' => 8,
                'actual_hours' => $attributes['status'] === 'completed' ? 8 : 0,
            ]);
        }

        foreach ([
            ['project' => 'E-Commerce Website Development', 'name' => 'Commerce Launch Sprint', 'goal' => 'Deliver a secure, complete checkout experience.', 'status' => 'active', 'start' => now()->subDays(4), 'end' => now()->addDays(10)],
            ['project' => 'Platform Development', 'name' => 'API Foundation Sprint', 'goal' => 'Stabilize platform integrations and observability.', 'status' => 'active', 'start' => now()->subDays(2), 'end' => now()->addDays(12)],
            ['project' => 'Website Redesign', 'name' => 'Design Discovery Sprint', 'goal' => 'Validate the new information architecture.', 'status' => 'planned', 'start' => now()->addWeeks(2), 'end' => now()->addWeeks(4)],
        ] as $attributes) {
            Sprint::query()->firstOrCreate(['project_id' => $projects[$attributes['project']]->id, 'name' => $attributes['name']], [
                'goal' => $attributes['goal'], 'status' => $attributes['status'], 'start_date' => $attributes['start']->toDateString(), 'end_date' => $attributes['end']->toDateString(),
            ]);
        }

        foreach ([
            ['project' => 'E-Commerce Website Development', 'name' => 'Checkout release candidate', 'status' => 'in-progress', 'due' => now()->addDays(14)],
            ['project' => 'Platform Development', 'name' => 'API integration beta', 'status' => 'upcoming', 'due' => now()->addDays(21)],
            ['project' => 'Mobile Application Development', 'name' => 'Pilot application release', 'status' => 'upcoming', 'due' => now()->addDays(10)],
        ] as $attributes) {
            Milestone::query()->firstOrCreate(['project_id' => $projects[$attributes['project']]->id, 'name' => $attributes['name']], [
                'description' => 'Default Project Tracker delivery milestone.', 'status' => $attributes['status'], 'due_date' => $attributes['due']->toDateString(), 'date' => $attributes['due']->toDateString(),
            ]);
        }

        foreach ([
            ['project' => 'E-Commerce Website Development', 'title' => 'Payment provider approval delay', 'category' => 'external', 'probability' => 'medium', 'impact' => 'high', 'owner' => 'Emanuel Gen'],
            ['project' => 'Platform Development', 'title' => 'Integration capacity constraint', 'category' => 'resource', 'probability' => 'medium', 'impact' => 'medium', 'owner' => 'Richard Dom'],
        ] as $attributes) {
            Risk::query()->firstOrCreate(['project_id' => $projects[$attributes['project']]->id, 'title' => $attributes['title']], [
                'category' => $attributes['category'], 'probability' => $attributes['probability'], 'impact' => $attributes['impact'], 'status' => 'open', 'owner_id' => $users[$attributes['owner']]->id,
            ]);
        }

        foreach ([
            ['project' => 'E-Commerce Website Development', 'title' => 'Checkout epic', 'type' => 'epic', 'priority' => 'high', 'points' => 13, 'status' => 'in-progress'],
            ['project' => 'Platform Development', 'title' => 'API documentation', 'type' => 'story', 'priority' => 'medium', 'points' => 5, 'status' => 'ready'],
        ] as $attributes) {
            BacklogItem::query()->firstOrCreate(['project_id' => $projects[$attributes['project']]->id, 'title' => $attributes['title']], array_diff_key($attributes, ['project' => true, 'title' => true]));
        }

        foreach ($projects->take(2) as $project) {
            Workflow::query()->firstOrCreate(['project_id' => $project->id, 'name' => 'Delivery workflow'], ['stages' => ['To do', 'In progress', 'Review', 'Done']]);
        }

        Stakeholder::query()->firstOrCreate(['project_id' => $projects['Website Redesign']->id, 'name' => 'Product Steering Group'], ['role' => 'Sponsor', 'department' => 'Operations', 'influence' => 'high', 'interest' => 'high']);
    }
}
