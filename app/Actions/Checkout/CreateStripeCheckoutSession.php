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
use App\Enums\OrderItemType;
use App\Enums\OrderType;
use App\Enums\PaymentGateway;
use App\Models\CartSession;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class CreateStripeCheckoutSession
{
    protected OrderItemType $orderType = OrderItemType::New;

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
        $cartItems = $this->validateCartItemsAndSetType($cartItems);

        $stripeSession = $this->createStripeCheckoutSession($user, $cartItems);

        $this->createLocalSession($stripeSession->id, $user, $cartItems);

        return $stripeSession->url;
    }

    /**
     * Ensures we don't have a "mixed cart" (mixture of new and renewals).
     *
     * @param  CartItem[]  $cartItems
     *
     * @return CartItem[]
     */
    protected function validateCartItemsAndSetType(array $cartItems): array
    {
        $types = array_map(fn(OrderItemType $type) => $type->value, Arr::pluck($cartItems, 'type'));


        $firstType = OrderItemType::from(reset($types));
        $this->orderType = $firstType;

        if (count(array_unique($types)) === 1) {
            return $cartItems;
        }

        // return all items that match the first type
        return array_filter($cartItems, fn(CartItem $cartItem) => $cartItem->type === $firstType);
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
        return $this->stripeClient->checkout->sessions->create($this->makeStripeCheckoutSessionArgs($cartItems));
    }

    protected function makeStripeCheckoutSessionArgs(array $cartItems): array
    {
        $args = [
            'line_items' => array_map([$this, 'makeStripeLineItem'], $cartItems),
            'mode' => 'payment',
            'customer_creation' => 'always',
            'success_url' => route('checkout.confirm').'?session_id={CHECKOUT_SESSION_ID}',
            //'cancel_url' => '',
        ];

        if ($this->orderType === OrderItemType::Renewal && $couponId = Config::get('services.stripe.renewalCouponId')) {
            $args['discounts'] = [
                ['coupon' => $couponId]
            ];
        }

        return $args;
    }

    protected function makeStripeLineItem(CartItem $cartItem): array
    {
        return [
            'price' => $cartItem->price->stripe_id,
            'quantity' => 1,
            'adjustable_quantity' => [
                'enabled' => false,
                //'maximum' => 1,
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
        $session->ip = Request::getClientIp();
        $session->save();
    }
}
