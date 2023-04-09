<?php

namespace App\Http\Controllers\Customers\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Http\Controllers\Controller;
use App\Models\ProductPrice;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Request;

class PurchaseProductController extends Controller
{
    public function __invoke(Request $request, ProductPrice $productPrice, CreateStripeCheckoutSession $sessionCreator)
    {
        try {
            $license = $request->input('license');

            return redirect()->to(
                path: $sessionCreator->execute(
                    user: $request->user(),
                    cartItems: [
                        new CartItem(
                            price: $productPrice,
                            type: $license ? OrderItemType::Renewal : OrderItemType::New,
                            licenseKey: $license
                        )
                    ]
                ),
                status: 303
            );
        } catch(\Exception $e) {
            throw new HttpClientException(message: 'An unexpected error has occurred.', code: 500);
        }
    }
}
