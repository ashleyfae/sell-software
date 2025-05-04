<?php

namespace Tests\Feature\Actions\Orders;

use App\Actions\Orders\ProvisionNewOrderItem;
use App\Enums\LicenseStatus;
use App\Enums\PeriodUnit;
use App\Models\License;
use App\Models\OrderItem;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProvisionNewOrderItem::class)]
class ProvisionNewOrderItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Actions\Orders\ProvisionNewOrderItem::execute()
     */
    public function testCanExecute(): void
    {
        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::withoutEvents(function () {
            return OrderItem::factory()->create();
        });

        /** @var License $license */
        $license = License::factory()->create();

        /** @var ProvisionNewOrderItem&MockInterface $provisioner */
        $provisioner = $this->partialMock(ProvisionNewOrderItem::class);

        $provisioner->shouldAllowMockingProtectedMethods();

        $provisioner->expects('createLicense')
            ->once()
            ->with($orderItem)
            ->andReturn($license);

        $this->assertNull($orderItem->license_id);

        $provisioner->execute($orderItem);

        $orderItem->refresh();

        $this->assertSame($license->id, $orderItem->license_id);
    }

    /**
     * @see \App\Actions\Orders\ProvisionNewOrderItem::createLicense()
     */
    public function testCanCreateLicense(): void
    {
        $productPrice = ProductPrice::factory()->create([
            'activation_limit'    => 10,
            'license_period'      => 1,
            'license_period_unit' => PeriodUnit::Year,
        ]);

        /** @var OrderItem $orderItem */
        $orderItem = OrderItem::withoutEvents(function () use ($productPrice) {
            return OrderItem::factory()->create([
                'product_price_id' => $productPrice->id,
            ]);
        });

        /** @var License $license */
        $license = $this->invokeInaccessibleMethod(app(ProvisionNewOrderItem::class), 'createLicense', $orderItem);

        $this->assertSame($orderItem->object->user_id, $license->user_id);
        $this->assertSame(LicenseStatus::Active, $license->status);
        $this->assertSame($orderItem->product_id, $license->product_id);
        $this->assertSame($orderItem->product_price_id, $license->product_price_id);
        $this->assertSame(10, $license->activation_limit);
        $this->assertSame(date('Y-m-d', strtotime('+1 year')), $license->expires_at->format('Y-m-d'));
    }
}
