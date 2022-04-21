<?php

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

Route::get('/', \App\Http\Controllers\Auth\LoginController::class)->middleware(\App\Http\Middleware\RedirectIfAuthenticated::class);
Route::get('/stemmen', \App\Http\Controllers\VoteController::class)->middleware(\Illuminate\Auth\Middleware\Authenticate::class);
Route::post('/stem', \App\Http\Controllers\CastVoteController::class)->middleware(\Illuminate\Auth\Middleware\Authenticate::class)->name('castVote');

Route::group(['prefix' => 'auth'], function () {
    Route::get('nonce', \App\Http\Controllers\Auth\GetNonceForSigningController::class);
    Route::post('verify', \App\Http\Controllers\Auth\VerifySignatureAndAuthenticateUserController::class);
    Route::post('logout', \App\Http\Controllers\Auth\LogoutController::class)->middleware(\Illuminate\Auth\Middleware\Authenticate::class)->name('logout');
});
