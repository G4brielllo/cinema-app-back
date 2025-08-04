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
use App\Http\Controllers\HomePageSlideController;
use App\Http\Controllers\PromotionController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HallSeatController;





Route::post('/api/register', [AuthController::class, 'register']);

Route::post('/api/login', [AuthController::class, 'login']);

Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/api/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/api/users', [UserController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/api/users/{id}', [UserController::class, 'delete'])->middleware('auth:sanctum');
Route::put('/api/users/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::get('/api/verify-email/{id}/{token}', [UserController::class, 'verifyEmail']);


Route::get('/api/movies', [MovieController::class, 'index']);
Route::get('/api/movies/{id}', [MovieController::class, 'show']);
Route::post('/api/movies', [MovieController::class, 'store'])->middleware('auth:sanctum', 'is_admin');
Route::put('/api/movies/{id}', [MovieController::class, 'update'])->middleware('auth:sanctum', 'is_admin');
Route::delete('/api/movies/{id}', [MovieController::class, 'delete'])->middleware('auth:sanctum', 'is_admin');

Route::get('/api/screenings', [ScreeningController::class, 'index']);
Route::get('/api/screenings/{id}', [ScreeningController::class, 'show']);
Route::post('/api/screenings', [ScreeningController::class, 'store'])->middleware('auth:sanctum', 'is_admin');
Route::put('/api/screenings/{id}', [ScreeningController::class, 'update'])->middleware('auth:sanctum', 'is_admin');
Route::delete('/api/screenings/{id}', [ScreeningController::class, 'delete'])->middleware('auth:sanctum', 'is_admin');

Route::post('/api/reservations', [ReservationController::class, 'store'])->middleware('auth:sanctum');
Route::get('/api/reservations', [ReservationController::class, 'index'])->middleware('auth:sanctum');
Route::delete('/api/reservations/{id}', [ReservationController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/api/reservations/{code}', [ReservationController::class, 'showByCode'])->middleware('auth:sanctum', 'is_admin');
Route::get('/api/reservations/{id}', [ReservationController::class, 'show'])->middleware('auth:sanctum');
Route::get('/api/reservations/user/{userId}', [ReservationController::class, 'checkUsersReservations'])->middleware('auth:sanctum');

Route::get('/api/reservation-by-code/{code}', [ReservationController::class, 'showByCode']);
Route::get('/api/reservations-with-movie', [ReservationController::class, 'getReservationSeatsWithMovie']);



Route::get('/api/halls', [HallController::class, 'index'])->middleware('auth:sanctum');
Route::get('/api/halls/{id}', [HallController::class, 'show'])->middleware('auth:sanctum');


Route::post('/api/halls', [HallController::class, 'store'])->middleware('auth:sanctum');
Route::post('/api/halls/{hall}/layout', [HallController::class, 'storeLayout'])->middleware('auth:sanctum');
// Route::post('/api/halls/{hall}/layout', [HallController::class, 'storeLayout']);

Route::get('/api/hall-seats/{hall}', [HallSeatController::class, 'getSeatsByHall']);
Route::get('/api/halls/{hallId}/available-seats', [HallSeatController::class, 'getAvailableSeats']);


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

Route::get('/api/auto-archive-screenings', function () {
    Artisan::call('screenings:auto-archive-screenings');
    return response()->json(['status' => 'OK']);
});

Route::get('/api/slides', [HomePageSlideController::class, 'index']);
Route::post('/api/slides', [HomePageSlideController::class, 'store'])->middleware('auth:sanctum', 'is_admin');
Route::delete('/api/slides/{id}', [HomePageSlideController::class, 'delete'])->middleware('auth:sanctum', 'is_admin');
Route::put('/api/slides/{id}', [HomePageSlideController::class, 'update'])->middleware('auth:sanctum', 'is_admin');

Route::get('/api/promotions', [PromotionController::class, 'index'])->middleware('auth:sanctum');
Route::post('/api/promotions', [PromotionController::class, 'store'])->middleware('auth:sanctum', 'is_admin');
Route::delete('/api/promotions/{id}', [PromotionController::class, 'delete'])->middleware('auth:sanctum', 'is_admin');
Route::put('/api/promotions/{id}', [PromotionController::class, 'update'])->middleware('auth:sanctum', 'is_admin');



Route::get('/api/reservations_seat_detailed', [ReservationController::class, 'getReservationSeatsWithMovie'])->middleware('auth:sanctum');
Route::get('/api/reservations/{id}/booked-seats', [ReservationController::class, 'getBookedSeats']);
