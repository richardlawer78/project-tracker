<?php

namespace Database\Factories;

use App\Models\TestCase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestCase>
 */
class TestCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'name' => fake()->sentence(3), 'type' => fake()->randomElement(['functional', 'performance', 'ui']), 'status' => 'pending', 'priority' => 'medium', 'last_run' => null,
        ];
    }
}
