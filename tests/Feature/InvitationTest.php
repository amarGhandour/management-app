<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_project_can_invite_a_user()
    {

        $project = Project::factory()->create();

        $project->invite($newUser = User::factory()->create());

        $this->signIn($newUser);
        $this->postJson(route('tasks.store', $project), $task = ['body' => 'foo'])->assertCreated();

        $this->assertDatabaseHas('tasks', $task + ['user_id' => $newUser->id]);
    }

    public function test_a_project_member_cannot_delete_the_project()
    {

        $project = Project::factory()->create();

        $project->invite($newUser = User::factory()->create());

        $this->signIn($newUser);
        $this->deleteJson(route('projects.destroy', $project))->assertForbidden();

        $this->assertDatabaseHas('projects', $project->toArray());
    }
}
