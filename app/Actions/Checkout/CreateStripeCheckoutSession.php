<?php
/**
 * CreateStripeCheckoutSession.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Checkout;

use App\DataTransferObjects\CartItem;
use App\Enums\PaymentGateway;
use App\Models\CartSession;
use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CreateStripeCheckoutSession
{
    public function __construct(protected StripeClient $stripeClient)
    {

    }

    /**
     * @param  User|null  $user
     * @param  CartItem[]  $cartItems
     *
     * @return string
     * @throws ApiErrorException
     */
    public function execute(?User $user, array $cartItems): string
    {
        $stripeSession = $this->createStripeCheckoutSession($user, $cartItems);

        $this->createLocalSession($stripeSession->id, $user, $cartItems);

        return $stripeSession->url;
    }

    /**
     * @param  User|null  $user
     * @param  CartItem[]  $cartItems
     *
     * @return Session
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function createStripeCheckoutSession(?User $user, array $cartItems): Session
    {
        return Session::create([
            'line_items' => array_map([$this, 'makeStripeLineItem'], $cartItems),
            'mode' => 'payment',
            'customer_creation' => 'always',
            'success_url' => '', // @TODO
            'cancel_url' => '', // @TODO
        ]);
    }

    protected function makeStripeLineItem(CartItem $cartItem): array
    {
        return [
            'price' => $cartItem->price->stripe_id,
            'quantity' => 1,
            'adjustable_quantity' => [
                'maximum' => 1,
            ],
        ];
    }

    /**
     * @param  string  $stripeCheckoutId
     * @param  User|null  $user
     * @param  CartItem[]  $cartItems
     *
     * @return void
     */
    protected function createLocalSession(string $stripeCheckoutId, ?User $user, array $cartItems): void
    {
        if ($user) {
            $session = $user->cartSessions()->make();
        } else {
            $session = new CartSession();
        }

        $session->session_id = $stripeCheckoutId;
        $session->cart = $cartItems;
        $session->gateway = PaymentGateway::Stripe;
        $session->save();
    }
}
