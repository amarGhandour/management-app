<?php

namespace Tests\Feature\Tasks;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReadTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_a_project_view_tasks()
    {
        $this->signIn();
        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->getJson(route('projects.show', $project))->assertJson($project->toArray());

        $task = $project->addTask(['body' => 'foo']);

        $this->getJson(route('projects.show', $project))->assertJsonFragment($task->fresh()->toArray());

    }
}
