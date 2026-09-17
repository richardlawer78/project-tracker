<?php

namespace Database\Factories;

use App\Models\BacklogItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BacklogItem>
 */
class BacklogItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'title' => fake()->sentence(4), 'type' => fake()->randomElement(['epic', 'feature', 'story']), 'priority' => fake()->randomElement(['low', 'medium', 'high']), 'points' => fake()->randomElement([1, 2, 3, 5, 8, 13]), 'status' => fake()->randomElement(['backlog', 'ready', 'in-progress']),
        ];
    }
}
