<?php

namespace Database\Factories;

use App\Models\ProjectResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectResource>
 */
class ProjectResourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'user_id' => \App\Models\User::factory(), 'allocation_percent' => fake()->numberBetween(10, 100),
        ];
    }
}
