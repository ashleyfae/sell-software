<?php

namespace Database\Factories;

use App\Enums\LicenseStatus;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\License>
 */
class LicenseFactory extends Factory
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
            'license_key' => $this->faker->uuid,
            'status' => LicenseStatus::Active,
            'product_id' => Product::factory(),
            'product_price_id' => ProductPrice::factory(),
            'activation_limit' => 10,
        ];
    }
}
