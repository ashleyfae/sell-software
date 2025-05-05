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

Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
Route::resource('products.prices', \App\Http\Controllers\Admin\ProductPriceController::class)
    ->except(['index', 'show'])
    ->scoped();

Route::get('licenses', [\App\Http\Controllers\Admin\LicensesController::class, 'index'])
    ->name('admin.licenses.index');

Route::get('orders', [\App\Http\Controllers\Admin\OrdersController::class, 'index'])
    ->name('admin.orders.index');
Route::get('orders/{order}', [\App\Http\Controllers\Admin\OrdersController::class, 'show'])
    ->name('admin.orders.show');
