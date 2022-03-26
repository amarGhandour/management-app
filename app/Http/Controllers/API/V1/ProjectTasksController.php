<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectTasksController extends Controller
{

    public function store(Request $request, Project $project)
    {

        abort_if(auth()->user()->isNot($project->owner), Response::HTTP_FORBIDDEN);

        $attributes = $request->validate(['body' => ['required']]);

        $project->addTask($attributes);

        $project->load('tasks');

        return response()->json($project, Response::HTTP_CREATED);
    }

    public function update(Request $request, Task $task)
    {
        abort_if(auth()->user()->isNot($task->owner), Response::HTTP_FORBIDDEN);

        $attributes = $request->validate(['body' => ['required']]);

        $task->updateOrFail($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Task $task)
    {
        abort_if(auth()->user()->isNot($task->owner), Response::HTTP_FORBIDDEN);

        $task->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
