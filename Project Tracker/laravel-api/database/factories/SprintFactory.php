<?php

namespace Database\Factories;

use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sprint>
 */
class SprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'name' => 'Sprint '.fake()->numberBetween(1, 30), 'goal' => fake()->sentence(), 'start_date' => now(), 'end_date' => now()->addWeeks(2), 'status' => 'planned', 'total_points' => 30, 'completed_points' => 0,
        ];
    }
}
