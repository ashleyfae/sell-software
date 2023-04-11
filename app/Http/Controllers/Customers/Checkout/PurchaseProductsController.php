<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\Actions\Checkout\RequestToCartItemsAdapter;
use App\Exceptions\Checkout\InvalidProductsToPurchaseException;
use App\Exceptions\Checkout\MissingProductsToPurchaseException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseProductsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, CreateStripeCheckoutSession $sessionCreator, RequestToCartItemsAdapter $adapter)
    {
        try {
            return redirect()->to(
                path: $sessionCreator->execute(
                    user: $request->user(),
                    cartItems: $adapter->execute($request)
                ),
                status: 303
            );
        } catch(MissingProductsToPurchaseException|InvalidProductsToPurchaseException $e) {
            abort(404);
        }
    }
}
