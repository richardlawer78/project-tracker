<?php

namespace Database\Factories;

use App\Models\BudgetItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BudgetItem>
 */
class BudgetItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'category' => fake()->randomElement(['Development', 'Design', 'Testing']), 'allocated' => 10000, 'spent' => 5000, 'status' => 'on-track',
        ];
    }
}
