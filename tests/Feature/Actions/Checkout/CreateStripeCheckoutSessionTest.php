<?php

namespace Tests\Feature\Actions\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Models\ProductPrice;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Tests\TestCase;

/**
 * @covers \App\Actions\Checkout\CreateStripeCheckoutSession
 */
class CreateStripeCheckoutSessionTest extends TestCase
{
    /**
     * @covers \App\Actions\Checkout\CreateStripeCheckoutSession::execute()
     */
    public function testCanExecute(): void
    {
        /** @var CreateStripeCheckoutSession&MockInterface $action */
        $action = $this->partialMock(CreateStripeCheckoutSession::class);
        $action->shouldAllowMockingProtectedMethods();

        $user = new User();
        $cartItems = [new CartItem(price: new ProductPrice())];

        $stripeSession = new Session('stripe-session-1');
        $stripeSession->url = 'https://stripe.com/checkout';

        $action->expects('createStripeCheckoutSession')
            ->once()
            ->with($user, $cartItems)
            ->andReturn($stripeSession);

        $action->expects('createLocalSession')
            ->once()
            ->with('stripe-session-1', $user, $cartItems)
            ->andReturn('https://stripe.com/checkout');

        $this->assertSame('https://stripe.com/checkout', $action->execute($user, $cartItems));
    }

    /**
     * @covers \App\Actions\Checkout\CreateStripeCheckoutSession::validateCartItemsAndSetType()
     * @dataProvider providerCanValidateCartItemsAndSetType
     *
     * @param  CartItem[]  $cartItems
     * @param  CartItem[]  $expectedItems
     * @param  OrderItemType  $expectedType
     *
     * @return void
     */
    public function testCanValidateCartItemsAndSetType(array $cartItems, array $expectedItems, OrderItemType $expectedType): void
    {
        $action = app(CreateStripeCheckoutSession::class);

        $this->assertSame($expectedItems, $this->invokeInaccessibleMethod($action, 'validateCartItemsAndSetType', $cartItems));
        $this->assertEquals($expectedType, $this->getInaccessiblePropertyValue($action, 'orderType'));
    }

    /** @see testCanValidateCartItemsAndSetType */
    public function providerCanValidateCartItemsAndSetType(): Generator
    {
        $newItems = [
            new CartItem(price: new ProductPrice(), type: OrderItemType::New),
            new CartItem(price: new ProductPrice(), type: OrderItemType::New),
        ];
        yield 'all new items' => [
            'cartItems' => $newItems,
            'expectedItems' => $newItems,
            'expectedType' => OrderItemType::New,
        ];

        $renewalItems = [
            new CartItem(price: new ProductPrice(), type: OrderItemType::Renewal),
            new CartItem(price: new ProductPrice(), type: OrderItemType::Renewal),
        ];
        yield 'all renewal items' => [
            'cartItems' => $renewalItems,
            'expectedItems' => $renewalItems,
            'expectedType' => OrderItemType::Renewal,
        ];

        $new = new CartItem(price: new ProductPrice(), type: OrderItemType::New);
        $renewal = new CartItem(price: new ProductPrice(), type: OrderItemType::Renewal);
        yield '1 new, 1 renewal' => [
            'cartItems' => [$new, $renewal],
            'expectedItems' => [$new],
            'expectedType' => OrderItemType::New,
        ];

        yield '2 renewals, 1 new' => [
            'cartItems' => [$renewal, $renewal, $new],
            'expectedItems' => [$renewal, $renewal],
            'expectedType' => OrderItemType::Renewal,
        ];
    }

    /**
     * @covers \App\Actions\Checkout\CreateStripeCheckoutSession::makeStripeLineItem()
     */
    public function testCanMakeStripeLineItem(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }

    /**
     * @covers \App\Actions\Checkout\CreateStripeCheckoutSession::createLocalSession()
     */
    public function testCanCreateLocalSession(): void
    {
        $this->markTestIncomplete(__METHOD__);
    }
}
