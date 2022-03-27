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

class ReadProjectsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_retrieve_projects()
    {
        $this->getJson(route('projects.index'))->assertUnauthorized();
    }

    public function test_an_authenticated_user_can_not_retrieve_projects_of_others()
    {
        $this->signIn();
        Project::factory(3)->create();

        $this->getJson(route('projects.index'))->assertOk()->assertJsonCount(0);
    }

    public function test_authenticated_user_can_retrieve_his_projects()
    {

        $this->signIn();
        Project::factory()->create(['user_id' => auth()->id()]);

        $this->getJson(route('projects.index'))->assertOk()->assertJsonCount(1);
    }

    public function test_guest_user_cannot_retrieve_a_single_project()
    {
        $this->getJson(route('projects.show', 4353))->assertUnauthorized();
    }

    public function test_an_authenticated_user_cannot_retrieve_a_single_project_from_projects_of_other()
    {
        Sanctum::actingAs(User::factory()->create());
        $notOwnProject = Project::factory()->create();

        $this->getJson(route('projects.show', $notOwnProject))->assertForbidden();
    }

    public function test_authenticated_user_can_retrieve_single_projects_from_he_is_own()
    {
        Sanctum::actingAs(User::factory()->create());
        $ownProject = Project::factory()->create(['user_id' => auth()->id()]);

        $this->getJson(route('projects.show', $ownProject))->assertOk()
            ->assertJson([
                'title' => $ownProject->title,
                'description' => $ownProject->description,
            ]);
    }


}
