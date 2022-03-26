<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectsController extends Controller
{

    public function index()
    {

        $projects = Project::where('user_id', auth()->id())->get();

        return response()->json($projects);
    }

    public function show(Project $project)
    {

        abort_if(auth()->user()->isNot($project->owner), Response::HTTP_FORBIDDEN);

        return $project;
    }

    public function store(Request $request)
    {

        $attributes = $request->validate([
            'title' => ['required'],
            'description' => ['required'],
        ]);

        $project = auth()->user()->projects()->create($attributes);

        return response()->json($project, Response::HTTP_CREATED);
    }

    public function update(Request $request, Project $project)
    {

        abort_if(auth()->user()->isNot($project->owner), Response::HTTP_FORBIDDEN);

        $attributes = $request->validate([
            'title' => ['required'],
            'description' => ['required'],
        ]);

        $project->updateOrFail($attributes);

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    public function destroy(Project $project)
    {

        abort_if(auth()->user()->isNot($project->owner), Response::HTTP_FORBIDDEN);

        $project->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


}
