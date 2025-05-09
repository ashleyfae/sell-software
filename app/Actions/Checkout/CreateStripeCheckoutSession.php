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
use Exception;
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
     * @throws ApiErrorException
     * @throws Exception
     */
    protected function createStripeCheckoutSession(?User $user, array $cartItems): Session
    {
        return $this->stripeClient->checkout->sessions->create(
            $this->makeStripeCheckoutSessionArgs($user, $cartItems)
        );
    }

    /**
     * @param  CartItem[]  $cartItems
     *
     * @throws Exception
     */
    protected function makeStripeCheckoutSessionArgs(?User $user, array $cartItems): array
    {
        $args = [
            'line_items' => array_map([$this, 'makeStripeLineItem'], $cartItems),
            'mode' => 'payment',
            'success_url' => route('checkout.confirm').'?session_id={CHECKOUT_SESSION_ID}',
            //'cancel_url' => '',
        ];

        $userStripeCustomerId = $this->getUserStripeId($user, $cartItems);
        if ($userStripeCustomerId) {
            $args['customer'] = $userStripeCustomerId;
        } else {
            $args['customer_creation'] = 'always';
        }

        if ($this->orderType === OrderItemType::Renewal && $couponId = Config::get('services.stripe.renewalCouponId')) {
            $args['discounts'] = [
                ['coupon' => $couponId]
            ];
        }

        return $args;
    }

    /**
     * @param  CartItem[]  $cartItems
     *
     * @throws Exception
     */
    protected function getUserStripeId(?User $user, array $cartItems) : ?string
    {
        return $user?->stripeCustomers()
            ->where('currency', $this->getCartCurrency($cartItems))
            ->value('stripe_id');

    }

    /**
     * @param  CartItem[]  $cartItems
     *
     * @throws Exception
     */
    protected function getCartCurrency(array $cartItems) : string
    {
        foreach($cartItems as $cartItem) {
            if ($cartItem->price->currency) {
                return $cartItem->price->currency;
            }
        }

        throw new Exception('Currency could not be determined.');
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
