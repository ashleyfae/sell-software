<?php

namespace Tests\Feature\Controllers;

use App\Enums\PeriodUnit;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\ProductPriceController
 */
class ProductPriceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers       \App\Http\Controllers\ProductPriceController::store()
     * @dataProvider providerCanStore
     */
    public function testCanStore(bool $userIsAdmin, bool $shouldCreate): void
    {
        /** @var User $user */
        $user = User::factory()
            ->when($userIsAdmin, fn(Factory $factory) => $factory->admin())
            ->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('products.prices.store', $product), [
                'name'                => '1 Site',
                'currency'            => 'usd',
                'price'               => '35',
                'renewal_price'       => '25.50',
                'license_period'      => 1,
                'license_period_unit' => PeriodUnit::Year->value,
                'activation_limit'    => 1,
                'stripe_id'           => 'prod_price_123',
            ]);

        if ($shouldCreate) {
            $response->assertRedirect(route('products.show', $product));

            $this->assertDatabaseHas(ProductPrice::class, [
                'product_id'          => $product->id,
                'name'                => '1 Site',
                'currency'            => 'usd',
                'price'               => 3500,
                'renewal_price'       => 2550,
                'license_period'      => 1,
                'license_period_unit' => PeriodUnit::Year->value,
                'activation_limit'    => 1,
                'stripe_id'           => 'prod_price_123',
            ]);
        } else {
            $response->assertForbidden();

            $this->assertDatabaseMissing(ProductPrice::class, [
                'product_id' => $product->id,
            ]);
        }
    }

    /** @see testCanStore */
    public function providerCanStore(): \Generator
    {
        yield 'admin can create' => [
            'userIsAdmin'  => true,
            'shouldCreate' => true,
        ];

        yield 'non-admin cannot create' => [
            'userIsAdmin'  => false,
            'shouldCreate' => false,
        ];
    }
}
