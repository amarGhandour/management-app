<?php

namespace Tests\Feature\Projects;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function auth;
use function route;

class DeleteProjectsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_delete_project()
    {
        $project = Project::factory()->create();
        $this->deleteJson(route('projects.destroy', $project))->assertUnauthorized();
    }

    public function test_user_cannot_delete_projects_of_others()
    {
        $notOwnedProject = Project::factory()->create();
        $this->signIn();

        $this->deleteJson(route('projects.destroy', $notOwnedProject))->assertForbidden();

        $this->assertDatabaseHas('projects', $notOwnedProject->toArray());
    }

    public function test_authenticated_user_can_delete_his_projects()
    {

        $this->signIn();
        $ownedProject = Project::factory()->create(['user_id' => auth()->id()]);

        $this->deleteJson(route('projects.destroy', $ownedProject))->assertNoContent();

        $this->assertDatabaseMissing('projects', $ownedProject->toArray());
    }

}
