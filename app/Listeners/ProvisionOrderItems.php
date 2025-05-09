<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderCreated;
use App\Jobs\ProvisionOrderItemJob;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProvisionOrderItems
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        foreach($event->order->orderItems as $orderItem) {
            $this->maybeProvisionItem($orderItem);
        }
    }

    protected function maybeProvisionItem(OrderItem $orderItem): void
    {
        if ($orderItem->needsProvisioning()) {
            ProvisionOrderItemJob::dispatchSync($orderItem);
        }
    }
}
