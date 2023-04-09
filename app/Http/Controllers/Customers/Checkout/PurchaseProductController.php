<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Http\Controllers\Controller;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class PurchaseProductController extends Controller
{
    public function __invoke(Request $request, ProductPrice $productPrice)
    {
        //
    }
}
