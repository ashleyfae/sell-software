<?php

namespace Tests\Feature\Actions\Licenses;

use App\Actions\Licenses\RenewLicense;
use App\Enums\LicenseStatus;
use App\Enums\PeriodUnit;
use App\Models\License;
use App\Models\ProductPrice;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * @covers \App\Actions\Licenses\RenewLicense
 */
class RenewLicenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @covers \App\Actions\Licenses\RenewLicense::renew()
     * @dataProvider providerCanRenew
     */
    public function testCanRenew(?string $currentExpirationDate, LicenseStatus $currentStatus, string $expectedNewExpirationDate): void
    {
        /** @var ProductPrice $productPrice */
        $productPrice = ProductPrice::factory()->create([
            'license_period' => 1,
            'license_period_unit' => PeriodUnit::Year,
        ]);

        /** @var License $license */
        $license = License::factory()->create([
            'expires_at' => $currentExpirationDate ? Carbon::createFromTimestamp(strtotime($currentExpirationDate)) : null,
            'status' => $currentStatus,
            'product_price_id' => $productPrice->id,
        ]);

        $license = app(RenewLicense::class)->renew($license);

        $this->assertSame(Carbon::createFromTimestamp(strtotime($expectedNewExpirationDate))->format('Y-m-d'), $license->expires_at->format('Y-m-d'));
        $this->assertSame(LicenseStatus::Active, $license->status);
    }

    /** @see testCanRenew */
    public function providerCanRenew(): Generator
    {
        yield 'license expired' => [
            'currentExpirationDate' => '-3 months',
            'currentStatus' => LicenseStatus::Expired,
            'expectedNewExpirationDate' => '+1 year',
        ];

        yield 'license is still active' => [
            'currentExpirationDate' => '+3 months',
            'currentStatus' => LicenseStatus::Active,
            'expectedNewExpirationDate' => '+15 months',
        ];
    }
}
