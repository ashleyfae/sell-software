<?php

namespace Database\Factories;

use App\Enums\PaymentGateway;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartSession>
 */
class CartSessionFactory extends Factory
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
            'session_id' => $this->faker->uuid,
            'cart' => json_encode([]),
            'gateway' => PaymentGateway::Stripe,
            'ip' => $this->faker->ipv4,
        ];
    }
}
