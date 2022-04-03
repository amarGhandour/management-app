<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_many_projects()
    {

        Sanctum::actingAs(User::factory()->create());
        $this->assertCount(0, auth()->user()->projects);

        Project::factory()->create(['user_id' => auth()->id()]);
        $this->assertCount(1, auth()->user()->fresh()->projects);
    }

//    public function test_knows_his_activities()
//    {
//        $this->signIn();
//
//        auth()->user()->projects()->create(Project::factory()->raw());
//
//        $this->assertCount(1, auth()->user()->activity);
//    }
}
