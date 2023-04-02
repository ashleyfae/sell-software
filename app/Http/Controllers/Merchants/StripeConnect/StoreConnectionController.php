<?php

namespace App\Http\Controllers\Merchants\StripeConnect;

use App\Actions\Stores\Connection\ConnectStoreToStripe;
use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\RedirectResponse;

class StoreConnectionController extends Controller
{
    public function __invoke(Store $store, ConnectStoreToStripe $connector) : RedirectResponse
    {
        // @TODO catch exceptions
        return redirect($connector->connect($store));
    }
}
