<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AlumniController;

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Public or Protected Auth Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        
        // Alumni Routes
        Route::get('/alumni', [AlumniController::class, 'index']);
        Route::get('/alumni/{identifier}', [AlumniController::class, 'show']);
    });
});
