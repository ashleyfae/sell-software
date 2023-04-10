<?php
/**
 * ConfirmStripePayment.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Checkout;

use App\Exceptions\Checkout\MissingStripeSessionIdException;
use App\Exceptions\Checkout\PaymentNotCompletedException;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class ConfirmStripePayment
{
    public function __construct(protected StripeClient $stripeClient, protected CreateOrderFromStripeSession $orderCreator)
    {

    }

    /**
     * @throws ApiErrorException|PaymentNotCompletedException
     * @throws MissingStripeSessionIdException
     */
    public function executeFromRequest(Request $request): Order
    {
        $session = $this->getStripeSession($request);

        if (! $this->isPaid($session)) {
            throw new PaymentNotCompletedException();
        }

        return $this->orderCreator->execute($session);
    }

    /**
     * @throws MissingStripeSessionIdException
     * @throws ApiErrorException
     */
    protected function getStripeSession(Request $request): Session
    {
        if ($sessionId = $request->input('session_id')) {
            return $this->stripeClient->checkout->sessions->retrieve($sessionId, [
                'expand' => [
                    'line_items',
                    'customer',
                    'payment_intent',
                ],
            ]);
        } else {
            throw new MissingStripeSessionIdException();
        }
    }

    protected function isPaid(Session $session): bool
    {
        return $session->payment_intent->status === 'succeeded';
    }
}
