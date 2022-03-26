<?php

use App\Http\Controllers\ProjectsController;
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

        Route::get('/projects', [ProjectsController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectsController::class, 'show'])->name('projects.show');
        Route::post('/projects', [ProjectsController::class, 'store'])->name('projects.store');
        Route::patch('/projects/{project}', [ProjectsController::class, 'update'])->name('projects.update');
        Route::delete('/projects/{project}', [ProjectsController::class, 'destroy'])->name('projects.destroy');

    });

});


