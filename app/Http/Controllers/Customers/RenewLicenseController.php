<?php

namespace App\Http\Controllers\Customers;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class RenewLicenseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, License $license, CreateStripeCheckoutSession $sessionCreator)
    {
        return redirect()->to(
            path: $sessionCreator->execute(
                user: $request->user(),
                cartItems: [
                    new CartItem(
                        price: $license->productPrice,
                        type: OrderItemType::Renewal,
                        license: $license,
                    )
                ]
            ),
            status: 303
        );
    }
}
