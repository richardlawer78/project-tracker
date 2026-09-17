<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'assigned_to' => \App\Models\User::factory(), 'title' => fake()->sentence(4), 'description' => fake()->paragraph(), 'status' => fake()->randomElement(['pending', 'in-progress', 'completed']), 'priority' => fake()->randomElement(['low', 'medium', 'high']), 'start_date' => now(), 'due_date' => now()->addWeek(), 'estimated_hours' => fake()->randomFloat(2, 1, 40), 'actual_hours' => 0, 'position' => fake()->numberBetween(0, 20),
        ];
    }
}
