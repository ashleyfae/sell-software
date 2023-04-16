<?php

namespace App\Http\Controllers\Customers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductReleasesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function list(Request $request, Product $product) : View
    {
        return view('customers.releases.list', [
            'product' => $product,
            'releases' => $product->releases()->orderBy('id', 'desc')->paginate(20),
        ]);
    }
}
