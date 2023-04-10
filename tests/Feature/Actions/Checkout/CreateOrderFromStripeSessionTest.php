<?php

namespace Tests\Feature\Actions\Checkout;

use App\Actions\Checkout\CreateOrderFromStripeSession;
use App\Enums\Currency;
use Generator;
use Stripe\Customer;
use Tests\TestCase;

/**
 * @covers \App\Actions\Checkout\CreateOrderFromStripeSession
 */
class CreateOrderFromStripeSessionTest extends TestCase
{
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
}
