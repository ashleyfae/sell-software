<?php

namespace Tests\Feature\Actions\Checkout;

use App\Actions\Checkout\CreateOrderFromStripeSession;
use App\Actions\Users\GetOrCreateUser;
use App\DataTransferObjects\CartItem;
use App\Enums\Currency;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Events\OrderCreated;
use App\Exceptions\Checkout\Stripe\InvalidStripeLineItemException;
use App\Models\CartSession;
use App\Models\Order;
use App\Models\ProductPrice;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\LineItem;
use Stripe\Price;
use Tests\TestCase;

/**
 * @covers \App\Actions\Checkout\CreateOrderFromStripeSession
 */
class CreateOrderFromStripeSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::execute()
     */
    public function testCanExecute(): void
    {
        Event::fake();

        $stripeSession = new Session('sess-123');
        $stripeCustomer = new Customer('cus_123');
        $stripeSession->customer = $stripeCustomer;
        $stripeSession->currency = 'gbp';
        $stripeSession->line_items = (object) [
            'data' => ['line']
        ];

        /** @var CartSession $cartSession */
        $cartSession = CartSession::factory()->create([
            'session_id' => 'sess-123',
        ])->refresh();

        /** @var CreateOrderFromStripeSession&MockInterface $creator */
        $creator = $this->partialMock(CreateOrderFromStripeSession::class);
        $creator->shouldAllowMockingProtectedMethods();

        $customer = new \App\DataTransferObjects\Customer(
            email: 'janedoe@exapmle.com',
            stripeCustomerId: 'cus_123',
            name: 'Jane Doe',
        );

        $creator->expects('getCustomerFromSession')
            ->once()
            ->with($stripeCustomer, 'gbp')
            ->andReturn($customer);

        $user = User::factory()->create()->refresh();

        $getOrCreateUser = Mockery::mock(GetOrCreateUser::class);
        $getOrCreateUser->expects('execute')
            ->once()
            ->with($customer)
            ->andReturn($user);

        $this->setInaccessibleProperty($creator, 'userCreator', $getOrCreateUser);

        /** @var Order $order */
        $order = Order::factory()->create()->refresh();

        $creator->expects('createOrderFromSession')
            ->once()
            ->withArgs(function($stripeSessionArg, $userArg, $cartSessionArg) use($stripeSession, $user, $cartSession) {
                return $stripeSessionArg === $stripeSession &&
                    $userArg === $user &&
                    $cartSessionArg->id === $cartSession->id;
            })
            ->andReturn($order);

        $creator->expects('createOrderItems')
            ->once()
            ->withArgs(function($lineItemArg, $orderArg, $cartSessionArg) use($order, $cartSession) {
                return $lineItemArg === ['line'] &&
                    $orderArg->id === $order->id &&
                    $cartSessionArg->id === $cartSession->id;
            });

        $this->assertSame($order, $creator->execute($stripeSession));

        Event::assertDispatched(OrderCreated::class);
    }

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::getCustomerFromSession()
     * @dataProvider providerCanGetCustomerFromSession
     */
    public function testCanGetCustomerFromSession(?string $customerCurrency, string $sessionCurrency, Currency $expectedCurrency): void
    {
        $stripeCustomer = new Customer('cus_123');
        $stripeCustomer->email = 'test@example.com';
        $stripeCustomer->name = 'Jane Doe';
        $stripeCustomer->currency = $customerCurrency;

        /** @var \App\DataTransferObjects\Customer $appCustomer */
        $appCustomer = $this->invokeInaccessibleMethod(app(CreateOrderFromStripeSession::class), 'getCustomerFromSession', $stripeCustomer, $sessionCurrency);

        $this->assertSame('test@example.com', $appCustomer->email);
        $this->assertSame('Jane Doe', $appCustomer->name);
        $this->assertSame('cus_123', $appCustomer->stripeCustomerId);
        $this->assertEquals($expectedCurrency, $appCustomer->currency);
    }

    /** @see testCanGetCustomerFromSession */
    public function providerCanGetCustomerFromSession(): Generator
    {
        yield 'customer has no currency' => [
            'customerCurrency' => null,
            'sessionCurrency' => 'usd',
            'expectedCurrency' => Currency::USD,
        ];

        yield 'customer has currency that differs from session' => [
            'customerCurrency' => 'gbp',
            'sessionCurrency' => 'usd',
            'expectedCurrency' => Currency::GBP,
        ];
    }

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::createOrderFromSession()
     */
    public function testCanCreateOrderFromSession(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var CartSession $cartSession */
        $cartSession = CartSession::factory()->for($user)->create();

        $session = new Session('ses_123');
        $session->amount_subtotal = 3500;
        $session->amount_total = 4200;
        $session->total_details = (object) [
            'amount_tax' => 700,
        ];
        $session->currency = 'usd';

        $this->assertDatabaseMissing(Order::class, ['user_id' => $user->id]);

        /** @var Order $order */
        $order = $this->invokeInaccessibleMethod(app(CreateOrderFromStripeSession::class), 'createOrderFromSession', $session, $user, $cartSession);

        $this->assertDatabaseHas(Order::class, ['user_id' => $user->id]);

        $this->assertSame(OrderStatus::Complete, $order->status);
        $this->assertSame(PaymentGateway::Stripe, $order->gateway);
        $this->assertSame($cartSession->ip, $order->ip);
        $this->assertSame(3500, $order->subtotal->amount);
        $this->assertSame(0, $order->discount->amount);
        $this->assertSame(700, $order->tax->amount);
        $this->assertSame(4200, $order->total->amount);
        $this->assertSame(Currency::USD, $order->currency);
        $this->assertSame('ses_123', $order->stripe_session_id);
    }

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::createOrderItems()
     */
    public function testCanCreateOrderItems(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::getCartItem()
     * @dataProvider providerCanGetCartItem
     */
    public function testCanGetCartItem(bool $hasMatchingPrice): void
    {
        $stripePrice = new Price('price_123');
        $stripeItem = new LineItem();
        $stripeItem->price = $stripePrice;

        $cartItem1 = new CartItem(
            price: ProductPrice::factory()->create(['stripe_id' => 'price_456'])
        );

        $cartItem2 = new CartItem(
            price: ProductPrice::factory()->create(['stripe_id' => $hasMatchingPrice ? 'price_123' : 'price_789'])
        );

        if (! $hasMatchingPrice) {
            $this->expectException(InvalidStripeLineItemException::class);
        }

        $this->assertSame(
            $cartItem2,
            $this->invokeInaccessibleMethod(app(CreateOrderFromStripeSession::class), 'getCartItem', $stripeItem, [$cartItem1, $cartItem2])
        );
    }

    /** @see testCanGetCartItem */
    public function providerCanGetCartItem(): Generator
    {
        yield [true];
        yield [false];
    }

    /**
     * @covers \App\Actions\Checkout\CreateOrderFromStripeSession::makeOrderItem()
     */
    public function testCanMakeOrderItem(): void
    {

    }
}
