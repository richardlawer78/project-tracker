<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')
            ->assertRedirect('/login');
    }

    public function test_authenticated_users_can_access_the_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertOk();
    }
}
