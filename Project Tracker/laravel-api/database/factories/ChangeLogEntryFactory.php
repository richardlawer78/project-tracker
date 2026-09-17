<?php

namespace Database\Factories;

use App\Models\ChangeLogEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChangeLogEntry>
 */
class ChangeLogEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'title' => fake()->sentence(3), 'type' => 'feature', 'requestor_id' => \App\Models\User::factory(), 'status' => 'pending', 'impact' => 'medium', 'date' => now(),
        ];
    }
}
