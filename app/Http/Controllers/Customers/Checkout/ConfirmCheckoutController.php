<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Actions\Checkout\ConfirmStripePayment;
use App\Exceptions\Checkout\MissingStripeSessionIdException;
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
            $confirmStripePayment->executeFromRequest($request);
        } catch(MissingStripeSessionIdException $e) {
            // need to confirm via webhook
        }
    }
}
