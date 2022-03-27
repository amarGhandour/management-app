<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

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
}
