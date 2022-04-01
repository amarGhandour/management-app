<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_records_activity_when_project_is_created()
    {

        $project = Project::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_project',
            'user_id' => $project->user_id,
            'subject_type' => get_class($project),
            'subject_id' => $project->id,
        ]);
        $this->assertCount(1, $project->activity);

    }

    public function test_it_records_activity_when_task_is_created()
    {

        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->assertCount(1, auth()->user()->activity);

        $task = $project->addTask(['body' => 'foo']);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_task',
            'user_id' => $task->user_id,
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
        ]);
        $this->assertCount(2, auth()->user()->fresh()->activity);

    }

    public function test_when_model_is_deleted_its_activities_are_deleted_too()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);
        $project->addTask(['body' => 'foo']);

        $this->assertCount(2, auth()->user()->activity);

        $project->delete();

        $this->assertCount(0, auth()->user()->fresh()->activity);

    }

}
