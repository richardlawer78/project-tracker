<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Risk;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackerConversionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_core_pages_load(): void
    {
        $user = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $this->actingAs($user);

        $this->get('/')->assertOk();
        $this->get('/projects')->assertOk();
        $this->get('/projects/create')->assertOk();
        $this->get('/tasks')->assertOk();
        $this->get('/tasks/kanban')->assertOk();
        $this->get('/agile/sprints')->assertOk();
        $this->get('/resources/milestones')->assertOk();
        $this->get('/quality/risks')->assertOk();
        $this->get('/chat')->assertOk();
        $this->get('/resources/gantt')->assertOk();
        $this->get('/reports/analytics')->assertOk();
    }

    public function test_project_crud(): void
    {
        $user = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $member = User::factory()->create();

        $this->actingAs($user);

        $this->post('/projects', [
            'name' => 'Website Redesign',
            'project_type' => 'agile',
            'status' => 'planning',
            'priority' => 'high',
            'members' => [$user->id, $member->id],
        ])->assertRedirect('/projects');

        $project = Project::query()->first();

        $this->assertNotNull($project);
        $this->assertSame($user->id, $project->owner_id);

        $this->get('/projects/'.$project->id)
            ->assertOk()
            ->assertSee('Website Redesign');

        $this->put('/projects/'.$project->id, [
            'name' => 'Website Rebuild',
            'project_type' => 'agile',
            'status' => 'in-progress',
            'priority' => 'high',
            'members' => [$user->id, $member->id],
        ])->assertRedirect('/projects/'.$project->id);

        $this->delete('/projects/'.$project->id)
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_task_sprint_milestone_and_risk_crud(): void
    {
        $user = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user);

        $this->post('/tasks', [
            'project_id' => $project->id,
            'title' => 'Design homepage',
            'status' => 'pending',
            'priority' => 'high',
        ])->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'Design homepage',
        ]);

        $task = Task::query()->firstOrFail();

        $this->patch('/tasks/'.$task->id.'/status', [
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertSame('completed', $task->fresh()->status);

        $this->post('/agile/sprints', [
            'project_id' => $project->id,
            'name' => 'Sprint 1',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeeks(2)->toDateString(),
            'status' => 'active',
        ])->assertRedirect('/agile/sprints');

        $this->assertDatabaseHas('sprints', [
            'name' => 'Sprint 1',
        ]);

        $this->post('/resources/milestones', [
            'project_id' => $project->id,
            'name' => 'MVP Release',
            'due_date' => now()->addMonth()->toDateString(),
            'status' => 'upcoming',
        ])->assertRedirect('/resources/milestones');

        $this->assertDatabaseHas('milestones', [
            'name' => 'MVP Release',
        ]);

        $this->post('/quality/risks', [
            'project_id' => $project->id,
            'title' => 'Scope creep',
            'category' => 'scope',
            'probability' => 'high',
            'impact' => 'medium',
            'status' => 'open',
            'owner_id' => $user->id,
        ])->assertRedirect('/quality/risks');

        $this->assertDatabaseHas('risks', [
            'title' => 'Scope creep',
        ]);

        $sprint = Sprint::query()->firstOrFail();
        $milestone = Milestone::query()->firstOrFail();
        $risk = Risk::query()->firstOrFail();

        $this->delete('/agile/sprints/'.$sprint->id)
            ->assertRedirect('/agile/sprints');

        $this->delete('/resources/milestones/'.$milestone->id)
            ->assertRedirect('/resources/milestones');

        $this->delete('/quality/risks/'.$risk->id)
            ->assertRedirect('/quality/risks');

        $this->delete('/tasks/'.$task->id)
            ->assertRedirect('/tasks');
    }

    public function test_api_resources_still_work_for_authenticated_users(): void
    {
        $user = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $project = Project::factory()->create([
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user, 'sanctum');

        $this->getJson('/api/projects')->assertOk();
        $this->getJson('/api/tasks')->assertOk();
        $this->getJson('/api/sprints')->assertOk();
        $this->getJson('/api/milestones')->assertOk();
        $this->getJson('/api/risks')->assertOk();

        $this->postJson('/api/tasks', [
            'project_id' => $project->id,
            'title' => 'API task',
        ])->assertCreated();
    }
}
