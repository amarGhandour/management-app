<?php

namespace Tests\Feature\Tasks;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_create_tasks()
    {
        $this->postJson(route('tasks.store', 34343), [])->assertUnauthorized();
    }

    public function test_authenticated_user_cannot_add_tasks_to_projects_of_others()
    {
        Sanctum::actingAs(User::factory()->create());
        $attributes = [
            'body' => $this->faker->sentence,
            'project_id' => $project = Project::factory()->create(),
        ];

        $this->postJson(route('tasks.store', $project), $attributes)->assertForbidden();

        $this->assertDatabaseMissing('tasks', $attributes + ['user_id' => auth()->id()]);
    }

    public function test_authenticated_user_can_add_tasks_to_his_projects()
    {
        Sanctum::actingAs(User::factory()->create());
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $attributes = [
            'body' => $this->faker->sentence,
            'project_id' => $project->id,
        ];

        $this->postJson(route('tasks.store', $project), $attributes)->assertCreated()
            ->assertJsonFragment($attributes);

        $this->assertDatabaseHas('tasks', $attributes + ['user_id' => auth()->id()]);
    }

    public function test_task_requires_a_body()
    {

        Sanctum::actingAs(User::factory()->create());
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $attributes = [
            'body' => '',
        ];

        $this->postJson(route('tasks.store', $project), $attributes)->assertUnprocessable();

    }

}
