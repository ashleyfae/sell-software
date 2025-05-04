<?php

namespace Tests\Feature\Jobs;

use App\Actions\Orders\ProvisionNewOrderItem;
use App\Actions\Orders\RenewOrderItem;
use App\Enums\OrderItemType;
use App\Jobs\ProvisionOrderItemJob;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ProvisionOrderItemJob::class)]
class ProvisionOrderItemJobTest extends TestCase
{
    /**
     * @see \App\Jobs\ProvisionOrderItemJob::Handle()
     */
    #[DataProvider('providerCanHandle')]
    public function testCanHandle(OrderItemType $type, bool $shouldProvision, bool $shouldRenew): void
    {
        $orderItem = new OrderItem();
        $orderItem->type = $type;

        $provisioner = $this->mock(ProvisionNewOrderItem::class);
        $provisioner->expects('execute')
            ->times((int) $shouldProvision)
            ->with($orderItem);

        $renewal = $this->mock(RenewOrderItem::class);
        $renewal->expects('execute')
            ->times((int) $shouldRenew)
            ->with($orderItem);

        (new ProvisionOrderItemJob($orderItem))->handle();
    }

    /** @see testCanHandle */
    public static function providerCanHandle(): \Generator
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
