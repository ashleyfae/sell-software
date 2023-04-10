<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderReceiptNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
        // @TODO investigate why this causes transaction errors in tests when using {@see OrderFactory}
        if (! $event->order->id || $event->order->orderItems->isEmpty()) {
            $this->release(10);
        }

        // @TODO actually send email
    }
}
