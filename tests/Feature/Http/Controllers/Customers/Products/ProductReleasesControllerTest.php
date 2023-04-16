<?php

namespace Tests\Feature\Http\Controllers\Customers\Products;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductPrice;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReleasesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Http\Controllers\Customers\Products\ProductReleasesController::list()
     * @dataProvider providerCanListWhenLoggedIn
     */
    public function testCanListWhenLoggedIn(bool $userHasPurchased, int $expectedResponseCode): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var ProductPrice $price */
        $price = ProductPrice::factory()->create();

        if ($userHasPurchased) {
            $order = Order::factory()->for($user)->create();

            OrderItem::factory()->create([
                'object_id' => $order->id,
                'object_type' => 'order',
                'product_id' => $price->product_id,
                'product_price_id' => $price->id,
            ]);

            $this->assertTrue($user->hasPurchasedProduct($price->product));
        }

        $response = $this->actingAs($user)->get(route('customer.products.releases', $price->product));

        $this->assertSame($expectedResponseCode, $response->status());
    }

    /** @see testCanListWhenLoggedIn */
    public function providerCanListWhenLoggedIn(): Generator
    {
        yield [false, 403];
        yield [true, 200];
    }

    /**
     * @covers \App\Http\Controllers\Customers\Products\ProductReleasesController::list()
     */
    public function testRedirectedWhenNotLoggedIn(): void
    {
        /** @var ProductPrice $price */
        $price = ProductPrice::factory()->create();

        $response = $this->get(route('customer.products.releases', $price->product));

        $response->assertRedirect(route('login'));
    }
}
