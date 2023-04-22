<?php
/**
 * CreateOrderFromStripeSession.php
 *
 * @package   software
 * @copyright Copyright (c) 2023, Ashley Gibson
 * @license   MIT
 */

namespace App\Actions\Checkout;

use App\Actions\Users\GetOrCreateUser;
use App\DataTransferObjects\CartItem;
use App\DataTransferObjects\Customer;
use App\Enums\Currency;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Events\OrderCreated;
use App\Exceptions\Checkout\Stripe\InvalidStripeLineItemException;
use App\Exceptions\Checkout\Stripe\MissingStripeLineItemsException;
use App\Models\CartSession;
use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\LineItem;

class CreateOrderFromStripeSession
{
    public function __construct(protected GetOrCreateUser $userCreator)
    {

    }

    /**
     * @throws MissingStripeLineItemsException
     */
    public function execute(Session $stripeSession): Order
    {
        if (empty($stripeSession->line_items?->data)) {
            throw new MissingStripeLineItemsException();
        }

        /** @var CartSession $cartSession */
        $cartSession = CartSession::query()
            ->where('session_id', $stripeSession->id)
            ->firstOrFail();

        // don't complete an order more than once
        if ($cartSession->order) {
            return $cartSession->order;
        }

        /** @var Order $order */
        $order = DB::transaction(function() use($stripeSession, $cartSession) {
            $user = $this->userCreator->execute($this->getCustomerFromSession($stripeSession->customer, $stripeSession->currency));

            if (! $cartSession->user) {
                $cartSession->user()->associate($user)->save();
            }

            $order = $this->createOrderFromSession($stripeSession, $user, $cartSession);

            $this->createOrderItems($stripeSession->line_items->data, $order, $cartSession);

            $cartSession->order()->associate($order)->save();

            return $order;
        });

        OrderCreated::dispatch($order);

        return $order;
    }

    protected function getCustomerFromSession(\Stripe\Customer $stripeCustomer, string $currency): Customer
    {
        return new Customer(
            email: $stripeCustomer->email,
            stripeCustomerId: $stripeCustomer->id,
            name: $stripeCustomer->name,
            currency: Currency::from($stripeCustomer->currency ?: $currency)
        );
    }

    protected function createOrderFromSession(Session $session, User $user, CartSession $cartSession): Order
    {
        $order = new Order();
        $order->status = OrderStatus::Complete;
        $order->gateway = PaymentGateway::Stripe;
        $order->ip = $cartSession->ip;
        $order->subtotal = (int) $session->amount_subtotal;
        $order->discount = $session->total_details->amount_discount ?? 0;
        $order->tax = $session->total_details->amount_tax ?? 0;
        $order->total = (int) $session->amount_total;
        $order->currency = Currency::from($session->currency);
        $order->completed_at = Carbon::now();
        $order->stripe_session_id = $session->id;

        $user->orders()->save($order);

        return $order;
    }

    /**
     * @param  LineItem[]  $stripeLineItems
     * @param  Order  $order
     * @param  CartSession  $cartSession
     *
     * @return void
     */
    protected function createOrderItems(array $stripeLineItems, Order $order, CartSession $cartSession): void
    {
        $orderItems = [];
        $cart = $cartSession->cart;

        foreach($stripeLineItems as $stripeItem) {
            try {
                $orderItems[] = $this->makeOrderItem($stripeItem, $this->getCartItem($stripeItem, $cart), $order->currency);
            } catch(InvalidStripeLineItemException $e) {
                Log::error(sprintf('Invalid Stripe Line Item: %s', $stripeItem->toJSON()));
            }
        }

        $order->orderItems()->saveMany($orderItems);
    }

    /**
     * @param  LineItem  $stripeItem
     * @param  CartItem[]  $cartItems
     *
     * @return CartItem
     * @throws InvalidStripeLineItemException
     */
    protected function getCartItem(LineItem $stripeItem, array $cartItems): CartItem
    {
        $cartItem = Arr::first($cartItems, function(CartItem $item) use($stripeItem) {
           return $item->price->stripe_id === $stripeItem->price->id;
        });

        return $cartItem ?? throw new InvalidStripeLineItemException("Unknown Stripe line item ID: {$stripeItem->id}");
    }

    protected function makeOrderItem(LineItem $stripeItem, CartItem $cartItem, Currency $currency): OrderItem
    {
        $productName = $cartItem->price->product->name;
        if ($cartItem->price->name) {
            $productName .= ' - '.$cartItem->price->name;
        }

        $orderItem = new OrderItem();
        $orderItem->product_id = $cartItem->price->product_id;
        $orderItem->product_price_id = $cartItem->price->id;
        $orderItem->product_name = $productName;
        $orderItem->status = OrderStatus::Complete;
        $orderItem->type = $cartItem->type;
        $orderItem->subtotal = $stripeItem->amount_subtotal;
        $orderItem->discount = $stripeItem->amount_discount;
        $orderItem->tax = $stripeItem->amount_tax;
        $orderItem->total = $stripeItem->amount_total;
        $orderItem->currency = $currency;

        if ($cartItem->license) {
            $orderItem->license_id = $cartItem->license->id;
        }

        return $orderItem;
    }
}
