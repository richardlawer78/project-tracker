<?php

namespace Database\Factories;

use App\Models\Kickoff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kickoff>
 */
class KickoffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'date' => now(), 'attendees' => fake()->numberBetween(3, 20), 'status' => 'scheduled',
        ];
    }
}
