<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;

Route::post('/api/register', [AuthController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/api/movies', [MovieController::class, 'index']);
    Route::post('/api/movies', [MovieController::class, 'store']);
    Route::delete('/api/movies/{id}', [MovieController::class, 'delete']);
});


