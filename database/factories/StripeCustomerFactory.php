<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StripeCustomer>
 */
class StripeCustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'stripe_id' => $this->faker->uuid,
            'currency' => Currency::USD->value,
        ];
    }
}
