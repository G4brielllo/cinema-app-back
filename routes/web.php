<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
require base_path('routes/api.php');


Route::get('/paymentStatus', function () {
    return redirect('http://localhost:8080/paymentStatus');
});

