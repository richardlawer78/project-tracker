<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectInterest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProjectInterestTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_express_interest_in_a_project(): void
    {
        $owner = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $response = $this->post(
            route('projects.public.interest', $project),
            [
                'name' => 'Kwame Mensah',
                'email' => 'kwame@example.com',
                'phone' => '+233 24 123 4567',
                'company' => 'Mensah Ventures',
                'message' => 'I would like to learn more about this project.',
            ]
        );

        $response
            ->assertRedirect()
            ->assertSessionHas(
                'success',
                'Your interest has been submitted successfully.'
            );

        $this->assertDatabaseHas('project_interests', [
            'project_id' => $project->id,
            'name' => 'Kwame Mensah',
            'email' => 'kwame@example.com',
            'phone' => '+233 24 123 4567',
            'company' => 'Mensah Ventures',
            'message' => 'I would like to learn more about this project.',
            'status' => 'new',
        ]);
    }

    public function test_interest_requires_name_and_email(): void
    {
        $owner = User::factory()->create([
            'role' => 'project-manager',
        ]);

        $project = Project::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $response = $this->from(
            route('projects.public.show', $project)
        )->post(
            route('projects.public.interest', $project),
            [
                'name' => '',
                'email' => '',
            ]
        );

        $response
            ->assertRedirect()
            ->assertSessionHasErrors(['name', 'email']);

        $this->assertDatabaseCount('project_interests', 0);
    }
}
