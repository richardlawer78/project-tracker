<?php

namespace Database\Factories;

use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stakeholder>
 */
class StakeholderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'name' => fake()->name(), 'role' => fake()->jobTitle(), 'department' => fake()->word(), 'influence' => 'medium', 'interest' => 'high',
        ];
    }
}
