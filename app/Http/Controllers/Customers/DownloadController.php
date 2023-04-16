<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function list(Request $request): View
    {
        $products = $request->user()->getPurchasedProducts();
        if ($products->isNotEmpty()) {
            $products->load('latestStableRelease');
        }

        return view('customers.downloads.list', [
            'products' => $products
        ]);

    }
}
