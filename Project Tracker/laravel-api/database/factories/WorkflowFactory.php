<?php

namespace Database\Factories;

use App\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workflow>
 */
class WorkflowFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Workflow', 'stages' => ['Backlog', 'In Progress', 'Review', 'Done'], 'project_id' => \App\Models\Project::factory(),
        ];
    }
}
