<?php

namespace Tests\Feature\Controllers;

use App\Enums\PeriodUnit;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\ProductPriceController
 */
class ProductPriceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Http\Controllers\ProductPriceController::store()
     */
    public function testCanStore(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $response = $this->actingAs($product->store->user)
            ->post(route('products.prices.store', $product), [
                'name' => '1 Site',
                'currency' => 'usd',
                'price' => '35',
                'renewal_price' => '25.50',
                'license_period' => 1,
                'license_period_unit' => PeriodUnit::Year->value,
                'activation_limit' => 1,
                'stripe_id' => 'prod_price_123',
            ]);

        $response->assertRedirect(route('products.show', $product));

        $this->assertDatabaseHas(ProductPrice::class, [
            'product_id' => $product->id,
            'name' => '1 Site',
            'currency' => 'usd',
            'price' => 3500,
            'renewal_price' => 2550,
            'license_period' => 1,
            'license_period_unit' => PeriodUnit::Year->value,
            'activation_limit' => 1,
            'stripe_id' => 'prod_price_123',
        ]);
    }
}
