<?php
namespace Database\Factories;
use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
class ChatMessageFactory extends Factory { protected $model = ChatMessage::class; public function definition(): array { return ['chat_channel_id' => \App\Models\ChatChannel::factory(), 'user_id' => \App\Models\User::factory(), 'message' => fake()->sentence()]; } }
