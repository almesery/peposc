<?php

use App\Http\Controllers\SocialMediaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get("auth/{provider}", [SocialMediaController::class, 'providerRedirect'])->name("provider.redirect");
Route::get("auth/{provider}/callback", [SocialMediaController::class, 'providerCallback'])->name("provider.callback");

Route::get("hotmail/auth/", [SocialMediaController::class, 'signin'])->name("hotmail.redirect");
Route::get("hotmail/auth/callback", [SocialMediaController::class, 'callback']);
