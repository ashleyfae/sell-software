<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Jobs\ProvisionOrderItemJob;
use App\Models\OrderItem;

class OrderItemObserver
{

    /**
     * Handle the OrderItem "saved" event.
     */
    public function saved(OrderItem $orderItem): void
    {
        if ($orderItem->status === OrderStatus::Complete && ! $orderItem->provisioned_at) {
            ProvisionOrderItemJob::dispatchSync($orderItem);
        }
    }
}
