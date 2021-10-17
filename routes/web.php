<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SocialMediaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes();

Route::get("auth/{provider}", [SocialMediaController::class, 'providerRedirect'])->name("provider.redirect");
Route::get("auth/{provider}/callback", [SocialMediaController::class, 'providerCallback']);

Route::get("hotmail/auth/", [SocialMediaController::class, 'hotMailRedirect'])->name("hotmail.redirect");
Route::get("hotmail/auth/callback", [SocialMediaController::class, 'hotMailCallback']);

Route::middleware("auth")->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('home');
    Route::get('last-login-datatables', [DashboardController::class, 'index'])->name('last-login.datatables');
});
