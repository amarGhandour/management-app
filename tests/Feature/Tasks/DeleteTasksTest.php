<?php

namespace Tests\Feature\Tasks;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_guest_user_cannot_delete_tasks()
    {
        $this->deleteJson(route('tasks.destroy', 3334))->assertUnauthorized();
    }

    public function test_authenticated_user_cannot_delete_tasks_of_others()
    {
        $this->signIn();
        $task = Task::factory()->create();

        $this->deleteJson(route('tasks.destroy', $task))->assertForbidden();
    }

    public function test_authenticated_user_can_delete_his_tasks()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $task = Task::factory()->create(['user_id' => auth()->id(), 'project_id' => $project->id]);

        $this->deleteJson(route('tasks.destroy', $task))->assertNoContent();

        $this->assertDatabaseMissing('tasks', $task->toArray());
    }
}
