<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Customers
|
*/

Route::get('downloads', [\App\Http\Controllers\Customers\DownloadController::class, 'list'])
    ->name('customer.downloads.list');

Route::get('products/{product}/licenses', [\App\Http\Controllers\Customers\Products\ProductLicensesController::class, 'list'])
    ->name('customer.products.licenses');

Route::get('licenses/{license}/renew', \App\Http\Controllers\Customers\RenewLicenseController::class)
    ->can('renew', 'license')
    ->name('customer.licenses.renew');

Route::get('licenses/{license}', [\App\Http\Controllers\Customers\LicensesController::class, 'show'])
    ->can('view', 'license')
    ->name('customer.licenses.show');

Route::get('products/{product}/releases', [\App\Http\Controllers\Customers\Products\ProductReleasesController::class, 'list'])
    ->can('viewReleases', 'product')
    ->name('customer.products.releases');
