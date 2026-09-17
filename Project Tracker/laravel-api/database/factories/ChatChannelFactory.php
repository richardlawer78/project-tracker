<?php
namespace Database\Factories;
use App\Models\ChatChannel;
use Illuminate\Database\Eloquent\Factories\Factory;
class ChatChannelFactory extends Factory { protected $model = ChatChannel::class; public function definition(): array { return ['name' => fake()->slug(2), 'project_id' => \App\Models\Project::factory()]; } }
