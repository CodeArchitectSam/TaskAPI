<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('tasks', TaskController::class);

        Route::prefix('tasks/{task_id}')->group(function () {
            Route::apiResource('comments', CommentController::class)->only(['index', 'store']);
        });
    });
});
