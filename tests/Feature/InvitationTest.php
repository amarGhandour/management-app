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

    public function test_only_the_project_owner_can_invite_new_member()
    {
        $this->signIn();

        $project = Project::factory()->create();
        $invitedUser = User::factory()->create();

        $this->postJson(route('project-invitations.store', $project), ['email' => $invitedUser->email])->assertForbidden();

        $project->invite(auth()->user());

        $this->postJson(route('project-invitations.store', $project), ['email' => $invitedUser->email])->assertForbidden();
        $this->assertFalse($project->members->contains($invitedUser));
    }

    public function test_a_project_can_invite_a_user()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $invitedUser = User::factory()->create();

        $this->postJson(route('project-invitations.store', $project), ['email' => $invitedUser->email])->assertNoContent();

        $this->assertTrue($project->members->contains($invitedUser));

    }

    public function test_invited_member_can_update_a_project()
    {
        $project = Project::factory()->create();

        $project->invite($newUser = User::factory()->create());

        $this->signIn($newUser);
        $this->postJson(route('tasks.store', $project), $task = ['body' => 'foo'])->assertCreated();

        $this->assertDatabaseHas('tasks', $task + ['user_id' => $newUser->id]);
    }

    public function test_invited_member_cannot_delete_the_project()
    {

        $project = Project::factory()->create();

        $project->invite($newUser = User::factory()->create());

        $this->signIn($newUser);
        $this->deleteJson(route('projects.destroy', $project))->assertForbidden();

        $this->assertDatabaseHas('projects', $project->toArray());
    }

    public function test_it_requires_an_email()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->postJson(route('project-invitations.store', $project), [])->assertUnprocessable();
    }

    public function test_it_requires_a_valid_email()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->postJson(route('project-invitations.store', $project), ['email' => 'foo'])->assertUnprocessable();
    }

    public function test_function_invited_email_address_must_be_associated_with_a_valid_app_account()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->postJson(route('project-invitations.store', $project), ['email' => 'foo@example.com'])
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('email');
    }
}
