<?php

namespace Database\Factories;

use App\Models\DorDodItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DorDodItem>
 */
class DorDodItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'list_type' => fake()->randomElement(['dor', 'dod']), 'text' => fake()->sentence(), 'checked' => fake()->boolean(),
        ];
    }
}
