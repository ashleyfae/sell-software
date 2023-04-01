<?php

namespace App\Http\Controllers\Stores\StripeConnect;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class VerifyStoreConnectionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Store $store)
    {
        
    }
}
