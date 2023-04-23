<?php

namespace App\Http\Controllers\Customers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowOrderConfirmationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, Order $order) : View
    {
        $order->load(['orderItems', 'orderItems.license', 'orderItems.license.product.latestStableRelease']);

        return view( 'customers.orders.confirmation', [
            'order' => $order,
        ]);
    }
}
