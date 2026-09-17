<?php
namespace Database\Factories;
use App\Models\LessonLearned;
use Illuminate\Database\Eloquent\Factories\Factory;
class LessonLearnedFactory extends Factory { protected $model = LessonLearned::class; public function definition(): array { return ['project_id' => \App\Models\Project::factory(), 'title' => fake()->sentence(4), 'category' => 'process', 'impact' => 'positive', 'description' => fake()->paragraph(), 'date' => now()]; } }
