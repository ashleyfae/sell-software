<?php

namespace Database\Factories;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        /** @var ProductPrice $productPrice */
        $productPrice = ProductPrice::factory()->create();

        return [
            'object_type' => 'order',
            'object_id' => Order::factory(),
            'product_id' => $productPrice->product->id,
            'product_price_id' => $productPrice->id,
            'product_name' => $productPrice->product->name,
            'status' => OrderStatus::Complete,
            'type' => OrderItemType::New,
            'subtotal' => 1000,
            'discount' => 0,
            'tax' => 0,
            'total' => 1000,
        ];
    }
}
