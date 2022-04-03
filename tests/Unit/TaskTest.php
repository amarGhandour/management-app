<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_has_an_owner()
    {

        $task = Task::factory()->create();

        $this->assertInstanceOf(User::class, $task->owner);
    }

    public function test_it_belongs_to_a_project()
    {
        $this->signIn();

        $project = Project::factory()->create();

        $task = $project->addTask(['body' => 'foo']);

        $this->assertInstanceOf(Project::class, $task->project);
    }

    public function test_it_records_completed_task_for_activity_type()
    {
        $task = Task::factory()->create();

        $task->complete();

        $this->assertEquals('completed_task', $task->project->activity->last()->type);
    }

    public function test_it_records_uncompleted_task_for_activity_type()
    {

        $task = Task::factory()->create(['completed' => true]);

        $task->incomplete();

        $this->assertEquals('uncompleted_task', $task->project->activity->last()->type);
    }
}
