<?php

namespace Database\Factories;

use App\Enums\Currency;
use App\Enums\PeriodUnit;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->name,
            'currency' => Currency::USD->value,
            'price' => $this->faker->numberBetween(10, 100),
            'renewal_price' => $this->faker->numberBetween(10, 100),
            'license_period' => 1,
            'license_period_unit' => PeriodUnit::Year->value,
            'activation_limit' => 1,
            'stripe_id' => $this->faker->uuid,
            'uuid' => $this->faker->uuid,
        ];
    }
}
