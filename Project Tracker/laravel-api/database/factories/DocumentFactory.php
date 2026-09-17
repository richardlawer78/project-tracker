<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(), 'name' => fake()->word().'.pdf', 'file_path' => 'documents/'.fake()->uuid().'.pdf', 'type' => 'pdf', 'size' => '1.2 MB', 'uploaded_by' => \App\Models\User::factory(),
        ];
    }
}
