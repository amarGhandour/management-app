<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectInvitationRequest;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Response;
use function response;

class ProjectInvitationsController extends Controller
{
    public function store(ProjectInvitationRequest $request, Project $project)
    {
        $user = User::whereEmail($request->email)->firstOrFail();

        $project->invite($user);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
