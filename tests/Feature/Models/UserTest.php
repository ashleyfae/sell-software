<?php

namespace Tests\Feature\Models;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Models\User::hasActiveLicenseForProduct()
     */
    #[DataProvider('providerCanDetermineHasActiveLicenseForProduct')]
    public function testCanDetermineHasActiveLicenseForProduct(bool $hasLicense, bool $licenseIsForProduct, bool $licenseIsActive, bool $expectedResult): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        if ($hasLicense) {
            $licenseArgs = ['status' => $licenseIsActive ? LicenseStatus::Active : LicenseStatus::Expired];
            if ($licenseIsForProduct) {
                $licenseArgs['product_id'] = $product->id;
            }

            License::factory()
                ->for($user)
                ->create($licenseArgs);
        }

        $this->assertSame($expectedResult, $user->hasActiveLicenseForProduct($product->id));
    }

    /** @see testCanDetermineHasActiveLicenseForProduct */
    public static function providerCanDetermineHasActiveLicenseForProduct(): Generator
    {
        yield 'no licenses' => [
            'hasLicense' => false,
            'licenseIsForProduct' => false,
            'licenseIsActive' => false,
            'expectedResult' => false,
        ];

        yield 'active license for different product' => [
            'hasLicense' => true,
            'licenseIsForProduct' => false,
            'licenseIsActive' => true,
            'expectedResult' => false,
        ];

        yield 'expired license for same product' => [
            'hasLicense' => true,
            'licenseIsForProduct' => true,
            'licenseIsActive' => false,
            'expectedResult' => false,
        ];

        yield 'active license for same product' => [
            'hasLicense' => true,
            'licenseIsForProduct' => true,
            'licenseIsActive' => true,
            'expectedResult' => true,
        ];
    }

    /**
     * @see \App\Models\Traits\HasOrders::getPurchasedProductIds()
     * @see \App\Models\Traits\HasOrders::getPurchasedProducts()
     */
    public function testCanGetPurchasedProductIds(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var ProductPrice $price */
        $price = ProductPrice::factory()->create();

        $order = Order::factory()->for($user)->create();

        OrderItem::factory()->create([
            'object_id' => $order->id,
            'object_type' => 'order',
            'product_id' => $price->product_id,
            'product_price_id' => $price->id,
        ]);

        // Create a bunch of other random order items.
        OrderItem::factory()->count(5)->create();

        $this->assertSame([$price->product_id], $user->getPurchasedProductIds());
        $this->assertSame([$price->product->toArray()], $user->getPurchasedProducts()->toArray());
    }

    /**
     * @see \App\Models\Traits\HasOrders::hasPurchasedProduct()
     */
    public function testCanDetermineHasPurchasedProduct(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var ProductPrice $price */
        $price = ProductPrice::factory()->create();

        $order = Order::factory()->for($user)->create();

        OrderItem::factory()->create([
            'object_id' => $order->id,
            'object_type' => 'order',
            'product_id' => $price->product_id,
            'product_price_id' => $price->id,
        ]);

        $unPurchasedProduct = Product::factory()->create();

        $this->assertTrue($user->hasPurchasedProduct($price->product));
        $this->assertFalse($user->hasPurchasedProduct($unPurchasedProduct));
    }
}
