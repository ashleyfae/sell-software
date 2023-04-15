<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Actions\Checkout\ConfirmStripePayment;
use App\Exceptions\Checkout\Stripe\MissingStripeSessionIdException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfirmCheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ConfirmStripePayment $confirmStripePayment)
    {
        try {
            $order = $confirmStripePayment->executeFromRequest($request);

            return redirect()->route('customer.orders.confirmation', $order);
        } catch(MissingStripeSessionIdException $e) {
            // need to confirm via webhook
        }
    }
}
