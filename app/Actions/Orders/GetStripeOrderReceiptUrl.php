<?php
/**
 * RedirectToStripeOrderReceipt.php
 *
 * @package   software
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Orders;

use App\Exceptions\Orders\StripeReceiptNotAvailableException;
use App\Models\Order;
use Stripe\StripeClient;

class GetStripeOrderReceiptUrl
{
    public function __construct(
        protected StripeClient $stripeClient
    )
    {
    }

    /**
     * @param  Order  $order
     *
     * @return string
     * @throws StripeReceiptNotAvailableException|\Stripe\Exception\ApiErrorException
     */
    public function execute(Order $order) : string
    {
        if (! $order->stripe_payment_intent_id) {
            throw new StripeReceiptNotAvailableException();
        }

        $receiptUrl = $this->stripeClient->paymentIntents->retrieve(
            $order->stripe_payment_intent_id,
            ['expand' => ['latest_charge']]
        )
            ->latest_charge
            ?->receipt_url;

        if ($receiptUrl) {
            return $receiptUrl;
        }

        throw new StripeReceiptNotAvailableException();
    }
}
