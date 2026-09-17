<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => \App\Models\User::factory(), 'name' => fake()->company().' Initiative', 'description' => fake()->paragraph(), 'client' => fake()->company(), 'team' => fake()->words(2, true), 'project_type' => fake()->randomElement(['predictive', 'agile', 'hybrid']), 'status' => fake()->randomElement(['planning', 'in-progress', 'on-hold', 'completed']), 'priority' => fake()->randomElement(['low', 'medium', 'high']), 'start_date' => now()->subWeek(), 'end_date' => now()->addMonths(3), 'budget' => fake()->numberBetween(10000, 100000), 'spent' => fake()->numberBetween(0, 10000), 'progress' => fake()->numberBetween(0, 100), 'settings' => ['notifications' => true],
        ];
    }
}
