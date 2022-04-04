<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function abort_if;
use function auth;
use function response;

class ProjectsController extends Controller
{

    public function index()
    {
        $projects = auth()->user()->accessibleProjects();

        return response()->json($projects);
    }

    public function show(Project $project)
    {

        abort_if(auth()->user()->isNot($project->owner), Response::HTTP_FORBIDDEN);

        return $project->load('tasks');
    }

    public function store(Request $request)
    {

        $attributes = $request->validate([
            'title' => ['required'],
            'description' => ['required'],
            'notes' => ['sometimes']
        ]);

        $project = auth()->user()->projects()->create($attributes);

        return response()->json($project, Response::HTTP_CREATED);
    }

    public function update(Request $request, Project $project)
    {

        $this->authorize('update', $project);

        $attributes = $request->validate([
            'title' => ['sometimes', 'required'],
            'description' => ['sometimes', 'required'],
            'notes' => ['nullable'],
        ]);

        $project->updateOrFail($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


}
