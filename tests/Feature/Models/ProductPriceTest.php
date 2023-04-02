<?php

namespace Tests\Feature\Models;

use App\Enums\Currency;
use App\Helpers\Money;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Models\ProductPrice
 */
class ProductPriceTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetPrices(): void
    {
        /** @var ProductPrice $productPrice */
        $productPrice = ProductPrice::factory()->create([
            'price' => 10,
            'renewal_price' => 5,
            'currency' => Currency::GBP->value,
        ]);

        $this->assertInstanceOf(Money::class, $productPrice->price);
        $this->assertEquals(10, $productPrice->price->amount);
        $this->assertEquals(Currency::GBP, $productPrice->price->currency);

        $this->assertInstanceOf(Money::class, $productPrice->renewal_price);
        $this->assertEquals(5, $productPrice->renewal_price->amount);
        $this->assertEquals(Currency::GBP, $productPrice->renewal_price->currency);
    }

    public function testCanSetPrices(): void
    {
        /** @var ProductPrice $productPrice */
        $productPrice = ProductPrice::factory()->create([
            'price' => 10,
            'renewal_price' => 5,
            'currency' => Currency::GBP->value,
        ]);

        $productPrice->price = '15.99';
        $productPrice->renewal_price = 17;
        $productPrice->save();

        $this->assertDatabaseHas(ProductPrice::class, [
            'price' => 1599,
            'renewal_price' => 17,
            'currency' => 'gbp',
        ]);

        $productPrice->price = 18.50;
        $productPrice->renewal_price = new Money(currency: Currency::GBP, amount: 599);
        $productPrice->save();

        $this->assertDatabaseHas(ProductPrice::class, [
            'price' => 1850,
            'renewal_price' => 599,
            'currency' => 'gbp',
        ]);
    }
}
