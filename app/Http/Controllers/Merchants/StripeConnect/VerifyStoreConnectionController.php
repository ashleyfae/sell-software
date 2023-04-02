<?php

namespace App\Http\Controllers\Merchants\StripeConnect;

use App\Actions\Stores\Connection\CompleteStripeConnection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyStoreConnectionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, CompleteStripeConnection $connector)
    {
        $store = $connector->complete($request);

        return redirect(route('stores.edit', $store));
    }
}
