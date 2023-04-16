<?php

namespace App\Http\Controllers\Customers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductLicensesController extends Controller
{
    public function list(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $request->user()->licenses()->where('product_id', $product->id)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }
}
