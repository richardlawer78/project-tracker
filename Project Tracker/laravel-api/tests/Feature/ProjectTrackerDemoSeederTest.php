<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\ProjectTrackerDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTrackerDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_data_is_repeat_safe_and_keeps_user_records(): void
    {
        $this->seed(ProjectTrackerDemoSeeder::class);

        $projectCount = Project::query()->count();
        $taskCount = Task::query()->count();
        $userCount = User::query()->count();
        $this->post('/projects', [
            'name' => 'University Project Tracker',
            'client' => 'Accra Technical University',
            'team' => 'Student Development Team',
            'project_type' => 'agile',
            'status' => 'in-progress',
            'priority' => 'high',
        ])->assertRedirect('/projects');

        $userProject = Project::query()->where('name', 'University Project Tracker')->firstOrFail();

        $this->seed(ProjectTrackerDemoSeeder::class);

        $this->assertSame($projectCount + 1, Project::query()->count());
        $this->assertSame($taskCount, Task::query()->count());
        $this->assertSame($userCount, User::query()->count());
        $this->assertDatabaseHas('projects', ['id' => $userProject->id, 'name' => 'University Project Tracker']);
    }

    public function test_seeded_people_are_available_in_the_task_assignment_dropdown(): void
    {
        $this->seed(ProjectTrackerDemoSeeder::class);

        $this->get('/tasks/create')
            ->assertOk()
            ->assertSee('Richard Dom')
            ->assertSee('Nikki Jey')
            ->assertSee('Emanuel Gen');
    }
}
