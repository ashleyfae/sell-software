<?php

namespace Tests\Feature\Casts;

use App\Casts\CartItemsCast;
use App\DataTransferObjects\CartItem;
use App\Enums\OrderItemType;
use App\Models\CartSession;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Casts\CartItemsCast
 */
class CartItemsCastTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @covers \App\Casts\CartItemsCast::get();
     */
    public function testCanGet(): void
    {
        /** @var ProductPrice $price1 */
        $price1 = ProductPrice::factory()->create();
        /** @var ProductPrice $price2 */
        $price2 = ProductPrice::factory()->create();

        $items = [
            [
                'price' => $price1->id,
                'type' => OrderItemType::New,
            ],
            [
                'price' => $price2->id,
                'type' => OrderItemType::Renewal,
                'licenseKey' => 'license-1',
            ]
        ];

        /** @var CartItem[] $cartData */
        $cartData = (new CartItemsCast())->get(
            model: new CartSession(),
            key: 'cart',
            value: json_encode($items),
            attributes: []
        );

        $this->assertSame($price1->id, $cartData[0]->price->id);
        $this->assertEquals(OrderItemType::New, $cartData[0]->type);
        $this->assertNull($cartData[0]->licenseKey);

        $this->assertSame($price2->id, $cartData[1]->price->id);
        $this->assertEquals(OrderItemType::Renewal, $cartData[1]->type);
        $this->assertSame('license-1', $cartData[1]->licenseKey);
    }

    /**
     * @covers \App\Casts\CartItemsCast::set()
     */
    public function testCanSet(): void
    {
        /** @var ProductPrice $price1 */
        $price1 = ProductPrice::factory()->create();
        /** @var ProductPrice $price2 */
        $price2 = ProductPrice::factory()->create();

        $items = [
            new CartItem(price: $price1, type: OrderItemType::New),
            new CartItem(price: $price2, type: OrderItemType::Renewal, licenseKey: 'license-1')
        ];

        $cartData = (new CartItemsCast())->set(
            model: new CartSession(),
            key: 'cart',
            value: $items,
            attributes: []
        );

        $this->assertEquals(
            '[{"price":'.$price1->id.',"type":"new","licenseKey":null},{"price":'.$price2->id.',"type":"renewal","licenseKey":"license-1"}]',
            $cartData
        );
    }
}
