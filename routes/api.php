<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\HallController;

Route::post('/api/register', [AuthController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/movies', [MovieController::class, 'index']);
Route::get('/api/movies/{id}', [MovieController::class, 'show']);
Route::post('/api/movies', [MovieController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/api/movies/{id}', [MovieController::class, 'delete'])->middleware('auth:sanctum');

Route::get('/api/screenings', [ScreeningController::class, 'index']);
Route::get('/api/screenings/{id}', [ScreeningController::class, 'show']);
Route::post('/api/screenings', [ScreeningController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/api/screenings/{id}', [ScreeningController::class, 'delete'])->middleware('auth:sanctum');



Route::get('/api/halls', [HallController::class, 'index']);
Route::get('/api/halls/{id}', [HallController::class, 'show']);