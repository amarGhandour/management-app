<?php

use App\Http\Controllers\API\V1\ProjectInvitationsController;
use App\Http\Controllers\API\V1\ProjectsController;
use App\Http\Controllers\API\V1\ProjectTasksController;
use App\Http\Controllers\API\V1\TasksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // projects
        Route::apiResource('projects', ProjectsController::class);

        // project-invitation
        Route::post('/projects/{project}/invitation', [ProjectInvitationsController::class, 'store'])->name('project-invitations.store');

        // tasks
        Route::post('/projects/{project}/tasks', [ProjectTasksController::class, 'store'])->name('tasks.store');
        Route::delete('/tasks/{task}', [ProjectTasksController::class, 'destroy'])->name('tasks.destroy');
        Route::patch('/tasks/{task}', [ProjectTasksController::class, 'update'])->name('tasks.update');
    });

});


