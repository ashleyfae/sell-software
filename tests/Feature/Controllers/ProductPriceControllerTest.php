<?php

namespace Tests\Feature\Controllers;

use App\Enums\PeriodUnit;
use App\Http\Controllers\Admin\ProductPriceController;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ProductPriceController::class)]
class ProductPriceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see       \App\Http\Controllers\Admin\ProductPriceController::store()
     */
    #[DataProvider('providerCanStore')]
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
    public static function providerCanStore(): \Generator
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
