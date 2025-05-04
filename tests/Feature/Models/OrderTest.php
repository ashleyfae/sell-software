<?php

namespace Tests\Feature\Models;

use App\Enums\OrderItemType;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Order::class)]
class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @see \App\Models\Order::isRenewal()
     */
    public function testCanDetermineIsNotRenewal(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create();
        OrderItem::factory()->count(2)->create([
            'object_id' => $order->id,
            'type' =>  OrderItemType::New,
        ]);

        $order->refresh();

        $this->assertFalse($order->isRenewal());
    }

    /**
     * @see \App\Models\Order::isRenewal()
     */
    public function testCanDetermineIsRenewal(): void
    {
        /** @var Order $order */
        $order = Order::factory()->create();
        OrderItem::factory()->count(2)->create([
            'object_id' => $order->id,
            'type' =>  OrderItemType::Renewal,
        ]);

        $order->refresh();

        $this->assertTrue($order->isRenewal());
    }
}
