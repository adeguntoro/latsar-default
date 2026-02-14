<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(
    ['register' => false, 'reset' => false, 'verify' => false]
);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/table', function () {
    return view('tabel');
});
