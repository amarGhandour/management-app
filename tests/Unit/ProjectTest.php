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
}
