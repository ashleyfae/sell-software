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

Route::get('stores/{store}/connect', \App\Http\Controllers\Merchants\StripeConnect\StoreConnectionController::class)
    ->can('connect', 'store')
    ->name('stores.connect');

Route::get('connect/stripe', \App\Http\Controllers\Merchants\StripeConnect\VerifyStoreConnectionController::class)
    ->name('stores.connect.callback');

Route::resource('stores', \App\Http\Controllers\Merchants\StoreController::class);
Route::resource('products', \App\Http\Controllers\Merchants\ProductController::class);
Route::resource('products.prices', \App\Http\Controllers\ProductPriceController::class)
    ->except(['index'])
    ->scoped();
