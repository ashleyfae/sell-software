<?php

namespace Tests\Feature\Actions\Checkout;

use App\Actions\Checkout\CreateStripeCheckoutSession;
use App\DataTransferObjects\CartItem;
use App\Models\ProductPrice;
use App\Models\User;
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
