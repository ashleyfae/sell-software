<?php

namespace Tests\Feature\Models;

use App\Casts\CartItemsCast;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Enums\PaymentGateway;
use App\Models\CartSession;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Models\CartSession
 */
class CartSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests that the {@see CartItemsCast} is applied.
     * {@see CartSession::$casts}
     */
    public function testCanSetCart(): void
    {
        /** @var ProductPrice $price1 */
        $price1 = ProductPrice::factory()->create();

        $items = [
            new CartItem(price: $price1, type: OrderItemType::New)
        ];

        $session = new CartSession();
        $session->session_id = 'sess_123';
        $session->cart = $items;
        $session->gateway = PaymentGateway::Stripe;
        $session->save();

        $this->assertSame(
            '[{"price":'.$price1->id.',"type":"new","license":null}]',
            $session->getRawOriginal('cart')
        );
    }
}
