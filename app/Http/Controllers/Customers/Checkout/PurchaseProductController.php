<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Http\Controllers\Controller;
use App\Models\ProductPrice;
use Illuminate\Http\Request;

class PurchaseProductController extends Controller
{
    public function __invoke(Request $request, ProductPrice $productPrice, CreateStripeCheckoutSession $sessionCreator)
    {
        return redirect()->to(
            path: $sessionCreator->execute(
                user: $request->user(),
                cartItems: [
                    new CartItem(
                        price: $productPrice,
                        type: OrderItemType::New,
                    )
                ]
            ),
            status: 303
        );
    }
}
