<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/api/register', [AuthController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

