<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Auth::routes();

Route::get("auth/{provider}", [SocialMediaController::class, 'providerRedirect'])->name("provider.redirect");
Route::get("auth/{provider}/callback", [SocialMediaController::class, 'providerCallback']);

Route::middleware("auth")->group(function () {
    Route::get('/dashboard', [UsersController::class, 'index'])->name('home');
    Route::get('last-login-datatables', [UsersController::class, 'index'])->name('last-login.datatables');

    Route::resource("user", AdminController::class);
});
