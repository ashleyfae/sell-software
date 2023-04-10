<?php

namespace App\Jobs;

use App\Actions\Orders\ProvisionNewOrderItem;
use App\Actions\Orders\RenewOrderItem;
use App\Enums\OrderItemType;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionOrderItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public OrderItem $orderItem)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProvisionNewOrderItem $newProvisioner, RenewOrderItem $renewal): void
    {
        if ($this->orderItem->type === OrderItemType::New) {
            $newProvisioner->execute($this->orderItem);
        } else {
            $renewal->execute($this->orderItem);
        }
    }
}
