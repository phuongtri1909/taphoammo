<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});


Route::group(['middleware' => 'guest'], function () {
    Route::get('login', function () {
        return view('client.pages.auth.login');
    })->name('login');

    Route::post('login', [AuthController::class, 'login'])->name('login.post');
});
