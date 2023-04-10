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

Route::resource('products', \App\Http\Controllers\Merchants\ProductController::class);
Route::resource('products.prices', \App\Http\Controllers\ProductPriceController::class)
    ->except(['index'])
    ->scoped();
