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

Route::middleware(['auth'])->group(function() {
    Route::get('stores/{store}/connect', \App\Http\Controllers\Stores\StripeConnect\StoreConnectionController::class)
        ->can('connect', 'store')
        ->name('stores.connect');
    Route::get('connect/stripe', \App\Http\Controllers\Stores\StripeConnect\VerifyStoreConnectionController::class)
        ->name('stores.connect.callback');
});

Route::resource('stores', \App\Http\Controllers\Stores\StoreController::class);
