<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->numberBetween(10000, 50000),
            'is_sold_out' => false,
            'cogs' => $this->faker->numberBetween(5000, 20000),
        ];
    }
}
