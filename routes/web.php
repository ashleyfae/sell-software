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

});

// buy a single item by uuid
Route::get('buy/{productPrice}', \App\Http\Controllers\Customers\Checkout\PurchaseProductController::class)
    ->name('buy');

// query args to buy multiple items
Route::get('buy', \App\Http\Controllers\Customers\Checkout\PurchaseProductsController::class);

Route::get('checkout/confirm', \App\Http\Controllers\Customers\Checkout\ConfirmCheckoutController::class)
    ->name('checkout.confirm');

Route::get('orders/{order}/confirmation', \App\Http\Controllers\Customers\Orders\ShowOrderConfirmationController::class)
    ->name('customer.orders.confirmation');
