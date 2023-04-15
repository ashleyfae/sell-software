<?php

namespace Tests\Feature\Models;

use App\Enums\LicenseStatus;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Models\User::hasActiveLicenseForProduct()
     * @dataProvider providerCanDetermineHasActiveLicenseForProduct
     */
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
    public function providerCanDetermineHasActiveLicenseForProduct(): Generator
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
}
