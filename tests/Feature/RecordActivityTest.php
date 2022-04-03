<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecordActivityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_when_project_is_created()
    {
        $this->assertDatabaseCount('activities', 0);

        $project = Project::factory()->create();

        $this->assertDatabaseHas('activities', [
            'type' => 'created_project',
            'project_id' => $project->id,
            'user_id' => $project->user_id,
        ]);
        $this->assertDatabaseCount('activities', 1);
        tap($project->fresh()->activity, function ($activity) {

            $this->assertCount(1, $activity);
            $this->assertNull($activity->last()->changes);
        });

    }

    public function test_when_project_is_updated()
    {
        $project = Project::factory()->create();
        $oldTitle = $project->title;

        $this->assertCount(1, $project->activity);

        $project->update(['title' => 'changed']);

        $this->assertDatabaseHas('activities', [
            'type' => 'updated_project',
            'project_id' => $project->id,
        ]);
        tap($project->fresh()->activity, function ($activity) use ($oldTitle) {
            $expected = [
                'before' => ['title' => $oldTitle],
                'after' => ['title' => 'changed'],
            ];
            $this->assertCount(2, $activity);
            $this->assertEquals($expected, $activity->last()->changes);
        });

    }

    public function test_when_task_is_created()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->assertCount(1, $project->activity);

        $task = $project->addTask(['body' => 'foo']);

        $this->assertDatabaseHas('activities', [
            'type' => 'created_task',
            'project_id' => $project->id,
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
        ]);
        $this->assertCount(2, $project->fresh()->activity);
    }

    public function test_when_task_is_completed()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->assertCount(1, $project->activity);

        $task = $project->addTask(['body' => 'foo']);

        $this->assertCount(2, $project->fresh()->activity);

        $this->patchJson(route('tasks.update', $task), ['completed' => true]);

        $this->assertDatabaseHas('activities', [
            'type' => 'completed_task',
            'project_id' => $project->id,
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
        ]);
        $this->assertCount(3, $project->fresh()->activity);
    }

    public function test_when_task_is_incomplete()
    {
        $this->signIn();

        $project = Project::factory()->create(['user_id' => auth()->id()]);

        $this->assertCount(1, $project->activity);

        $task = $project->addTask(['body' => 'foo', 'completed' => true]);

        $this->assertCount(2, $project->fresh()->activity);

        $this->patchJson(route('tasks.update', $task), ['completed' => false]);

        $this->assertDatabaseHas('activities', [
            'type' => 'uncompleted_task',
            'project_id' => $project->id,
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
        ]);
        $this->assertCount(3, $project->fresh()->activity);
    }

    public function test_when_task_is_deleted()
    {
        $this->signIn();

        $project = Project::factory()->hasTasks(1)->create(['user_id' => auth()->id()]);

        $this->assertCount(2, $project->fresh()->activity);

        $task = $project->tasks()->latest()->first();

        $task->delete();

        $this->assertDatabaseHas('activities', [
            'type' => 'deleted_task',
            'project_id' => $project->id,
            'subject_type' => get_class($task),
            'subject_id' => $task->id,
        ]);

        $this->assertCount(3, $project->fresh()->activity);
    }

    public function test_when_model_is_deleted_its_activities_are_deleted_too()
    {
        $this->signIn();

        $project = Project::factory()->hasTasks(1)->create(['user_id' => auth()->id()]);

        $this->assertCount(2, $project->activity);
        $this->assertDatabaseCount('activities', 2);

        $project->delete();

        $this->assertDatabaseCount('activities', 0);

    }

}
