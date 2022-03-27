<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function auth;
use function route;

class CreateProjectsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_unauthenticated_user_cannot_create_a_project()
    {
        $this->postJson(route('projects.store'), [])->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_project()
    {

        $this->signIn();
        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->sentence,
            'notes' => $this->faker->paragraph,
        ];

        $this->postJson(route('projects.store'), $attributes)
            ->assertCreated()->assertJson($attributes += ['user_id' => auth()->id()]);

        $this->assertDatabaseHas('projects', $attributes);

    }

    public function test_a_project_requires_a_title()
    {
        $this->signIn();
        $attributes = Project::factory()->raw(['title' => '']);

        $this->postJson(route('projects.store'), $attributes)->assertUnprocessable();

        $this->assertDatabaseMissing('projects', $attributes);

    }

    public function test_a_project_requires_a_description()
    {

        $this->signIn();
        $attributes = Project::factory()->raw(['description' => '']);

        $this->postJson(route('projects.store'), $attributes)->assertUnprocessable();

        $this->assertDatabaseMissing('projects', $attributes);

    }


}
