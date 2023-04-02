<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Merchant Routes
|--------------------------------------------------------------------------
|
| Routes for store merchants.
|
*/

Route::get('stores/{store}/connect', \App\Http\Controllers\Stores\StripeConnect\StoreConnectionController::class)
    ->can('connect', 'store')
    ->name('stores.connect');

Route::get('connect/stripe', \App\Http\Controllers\Stores\StripeConnect\VerifyStoreConnectionController::class)
    ->name('stores.connect.callback');

Route::resource('stores', \App\Http\Controllers\Stores\StoreController::class);
