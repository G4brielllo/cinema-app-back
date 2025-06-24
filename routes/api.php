<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PayUController;
use Illuminate\Support\Facades\Artisan;




Route::post('/api/register', [AuthController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/api/users/{id}', [UserController::class, 'delete'])->middleware('auth:sanctum');


Route::get('/api/movies', [MovieController::class, 'index']);
Route::get('/api/movies/{id}', [MovieController::class, 'show']);
Route::post('/api/movies', [MovieController::class, 'store'])->middleware('auth:sanctum');
Route::put('/api/movies/{id}', [MovieController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/api/movies/{id}', [MovieController::class, 'delete'])->middleware('auth:sanctum');

Route::get('/api/screenings', [ScreeningController::class, 'index']);
Route::get('/api/screenings/{id}', [ScreeningController::class, 'show']);
Route::post('/api/screenings', [ScreeningController::class, 'store'])->middleware('auth:sanctum');
Route::put('/api/screenings/{id}', [ScreeningController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/api/screenings/{id}', [ScreeningController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/api/reservations', [ReservationController::class, 'store'])->middleware('auth:sanctum');
Route::get('/api/reservations', [ReservationController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/api/reservations/{id}', [ReservationController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/api/reservations/{code}', [ReservationController::class, 'showByCode']);
Route::get('/api/reservations/{id}', [ReservationController::class, 'show']);
Route::get('/api/reservations/user/{userId}', [ReservationController::class, 'checkUsersReservations']);



Route::get('/api/halls', [HallController::class, 'index']);
Route::get('/api/halls/{id}', [HallController::class, 'show']);

// Route::get('/screenings/{screeningId}/seats', [SeatController::class, 'getSeatsForScreening']);
Route::get('/api/screenings/{screening}/seats', [SeatController::class, 'getSeatsForScreening']);

Route::post('/api/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/api/reset-password', [PasswordResetController::class, 'reset']);
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');



Route::post('/api/payu/create-order', [PayUController::class, 'createOrder']);

Route::post('/api/payu/notify', [PayUController::class, 'notify'])->name('payu.notify');

// Route::get('/payment-status', function () {
//     return redirect('http://localhost:8080/paymentStatus');
// });

Route::get('/api/delete-expired-reservations', function () {
    Artisan::call('reservations:delete-expired');
    return response()->json(['status' => 'OK']);
});

Route::get('/api/auto-archive-movies', function () {
    Artisan::call('movies:auto-archive-movies');
    return response()->json(['status' => 'OK']);
});
