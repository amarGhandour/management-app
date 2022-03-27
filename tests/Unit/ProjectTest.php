<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_an_owner()
    {
        $project = Project::factory()->create();
        $this->assertInstanceOf('App\Models\User', $project->owner);
    }

    public function test_it_has_tasks()
    {

        $this->signIn();

        $project = Project::factory()->create();
        $this->assertCount(0, $project->tasks);

        $project->addTask(['body' => 'foo']);
        $this->assertCount(1, $project->fresh()->tasks);
    }

    public function test_it_add_tasks()
    {

        $this->signIn();

        $project = Project::factory()->create();
        $this->assertCount(0, $project->tasks);

        $task = $project->addTask(['body' => 'foo']);

        $this->assertCount(1, $project->fresh()->tasks);
        $this->assertTrue($project->fresh()->tasks->contains($task));

    }

    public function test_when_delete_a_project_it_delete_its_tasks()
    {
        $this->signIn();
        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $project->addTask($attributes = ['body' => 'foo']);

        $this->assertDatabaseHas('tasks', $attributes += ['project_id' => $project->id]);

        $project->delete();

        $this->assertDatabaseMissing('tasks', $attributes);
    }

}
