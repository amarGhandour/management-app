<?php

namespace Tests\Feature\Tasks;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_update_tasks()
    {
        $this->patchJson(route('tasks.update', 87878))->assertUnauthorized();
    }

    public function test_authenticated_user_cannot_update_tasks_of_others()
    {
        $this->signIn();
        $task = Task::factory()->create();

        $this->patchJson(route('tasks.update', $task), [])->assertForbidden();
    }

    public function test_authenticated_user_can_update_his_tasks()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $task = Task::factory()->create(['user_id' => auth()->id(), 'project_id' => $project->id]);

        $attributes = ['body' => $this->faker->sentence,];

        $this->patchJson(route('tasks.update', $task), $attributes)->assertNoContent();
        $this->assertDatabaseHas('tasks', $attributes += ['user_id' => auth()->id(), 'project_id' => $project->id]);

    }

    public function test_it_require_a_body()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $task = Task::factory()->create(['user_id' => auth()->id(), 'project_id' => $project->id]);

        $attributes = ['body' => ''];

        $this->patchJson(route('tasks.update', $task), $attributes)->assertUnprocessable();
        $this->assertDatabaseMissing('tasks', $attributes += ['user_id' => auth()->id(), 'project_id' => $project->id]);
    }
}
