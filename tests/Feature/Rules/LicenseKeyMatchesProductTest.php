<?php

namespace Tests\Feature\Rules;

use App\Models\License;
use App\Models\Product;
use App\Rules\LicenseKeyMatchesProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Rules\LicenseKeyMatchesProduct
 */
class LicenseKeyMatchesProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Rules\LicenseKeyMatchesProduct::validate()
     * @dataProvider providerCanValidate
     */
    public function testCanValidate(bool $productIdMatches, bool $shouldPass): void
    {
        /** @var License $license */
        $license = License::factory()->create();
        $passes = true;

        /** @var Product $product */
        $product = Product::factory()->create();

        (new LicenseKeyMatchesProduct($license))->validate(
            attribute: 'product_id',
            value: $productIdMatches ? $license->product->uuid : $product->uuid,
            fail: function(string $message) use (&$passes): void
            {
                $passes = false;
                $this->assertSame('The :attribute must match the license key\'s product.', $message);
            }
        );

        $this->assertSame($shouldPass, $passes);
    }

    /** @see testCanValidate */
    public function providerCanValidate(): \Generator
    {
        yield 'product does not match' => [false, false];
        yield 'product matches' => [true, true];
    }
}
