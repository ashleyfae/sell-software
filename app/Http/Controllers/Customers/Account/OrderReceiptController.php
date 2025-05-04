<?php

namespace App\Http\Controllers\Customers\Account;

use App\Actions\Orders\GetStripeOrderReceiptUrl;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class OrderReceiptController extends Controller
{
    public function get(Order $order, GetStripeOrderReceiptUrl $getStripeOrderReceiptUrl)
    {
        try {
            $receipt = $getStripeOrderReceiptUrl->execute($order);

            return redirect()->to(
                path: $receipt
            );
        } catch(ApiErrorException $e) {
            Log::error(sprintf(
                'Failed to get Stripe receipt URL for order #%d. Error: %s',
                $order->id,
                $e->getMessage()
            ));
            Log::error($e->getTraceAsString());

            abort(500);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            abort(500);
        }
    }
}
