<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function auth;
use function route;

class UpdateProjectsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_update_projects()
    {
        $this->patchJson(route('projects.update', 8553))->assertUnauthorized();
    }

    public function test_authenticated_user_cannot_update_projects_of_others()
    {
        $notOwnedProject = Project::factory()->create();
        Sanctum::actingAs(User::factory()->create());

        $this->patchJson(route('projects.update', $notOwnedProject), [])->assertForbidden();

        $this->assertDatabaseHas('projects', $notOwnedProject->toArray());

    }

    public function test_authenticated_user_can_update_his_projects()
    {

        Sanctum::actingAs(User::factory()->create());
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $attributes = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
        ];

        $this->patchJson(route('projects.update', $project), $attributes)->assertNoContent();

        $this->assertDatabaseHas('projects', $attributes + ['user_id' => auth()->id(), 'id' => $project->id]);
    }

    public function test_a_project_requires_a_title()
    {

        Sanctum::actingAs(User::factory()->create());
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $attributes = Project::factory()->raw(['title' => '']);

        $this->patchJson(route('projects.update', $project), $attributes)->assertUnprocessable();

        $this->assertDatabaseMissing('projects', $attributes);

    }

    public function test_a_project_requires_a_description()
    {
        Sanctum::actingAs(User::factory()->create());
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $attributes = Project::factory()->raw(['description' => null]);

        $this->patchJson(route('projects.update', $project), $attributes)->assertUnprocessable();

        $this->assertDatabaseMissing('projects', $attributes);
    }


}
