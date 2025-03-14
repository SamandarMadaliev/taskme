<?php
declare(strict_types=1);

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// Tasks routes
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::apiResource('/tasks', TaskController::class);
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('tasks.complete');
});
