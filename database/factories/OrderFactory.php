<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Complete,
            'gateway' => PaymentGateway::Manual,
            'ip' => $this->faker->ipv4,
            'subtotal' => 1000,
            'discount' => 0,
            'tax' => 0,
            'total' => 1000,
            'currency' => Currency::USD,
            'stripe_session_id' => null,
        ];
    }
}
