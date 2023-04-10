<?php

namespace Tests\Feature\Jobs;

use App\Actions\Orders\ProvisionNewOrderItem;
use App\Actions\Orders\RenewOrderItem;
use App\Enums\OrderItemType;
use App\Jobs\ProvisionOrderItemJob;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @covers \App\Jobs\ProvisionOrderItemJob
 */
class ProvisionOrderItemJobTest extends TestCase
{
    /**
     * @covers \App\Jobs\ProvisionOrderItemJob::Handle()
     * @dataProvider providerCanHandle
     */
    public function testCanHandle(OrderItemType $type, bool $shouldProvision, bool $shouldRenew): void
    {
        $orderItem = new OrderItem();
        $orderItem->type = $type;

        $provisioner = \Mockery::mock(ProvisionNewOrderItem::class);
        $provisioner->expects('execute')
            ->times((int) $shouldProvision)
            ->with($orderItem);

        $renewal = \Mockery::mock(RenewOrderItem::class);
        $renewal->expects('execute')
            ->times((int) $shouldRenew)
            ->with($orderItem);

        (new ProvisionOrderItemJob($orderItem))->handle($provisioner, $renewal);
    }

    /** @see testCanHandle */
    public function providerCanHandle(): \Generator
    {
        yield 'new order' => [
            'type' => OrderItemType::New,
            'shouldProvision' => true,
            'shouldRenew' => false,
        ];

        yield 'renewal order' => [
            'type' => OrderItemType::Renewal,
            'shouldProvision' => false,
            'shouldRenew' => true,
        ];
    }
}
