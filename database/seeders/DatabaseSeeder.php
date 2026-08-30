<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $shop1 = Shop::create(['name' => 'Bitten Coffee', 'slug' => 'bitten', 'primary_color' => '#1E5A7A', 'theme_style' => 'modern']);
        User::create(['name' => 'Admin Bitten', 'email' => 'admin@bitten.com', 'password' => Hash::make('password'), 'shop_id' => $shop1->id]);
        Product::create(['shop_id' => $shop1->id, 'category_name' => 'Coffee', 'name' => 'Vanilla Latte', 'price' => 25000]);

        $shop2 = Shop::create(['name' => 'Goodwill Coffee', 'slug' => 'goodwill', 'primary_color' => '#276749', 'theme_style' => 'minimalist']);
        User::create(['name' => 'Admin Goodwill', 'email' => 'admin@goodwill.com', 'password' => Hash::make('password'), 'shop_id' => $shop2->id]);
        Product::create(['shop_id' => $shop2->id, 'category_name' => 'Signature', 'name' => 'Goodwill Matcha', 'price' => 28000]);

        $shop3 = Shop::create(['name' => 'Mada Coffee', 'slug' => 'mada', 'primary_color' => '#744210', 'theme_style' => 'classic']);
        User::create(['name' => 'Admin Mada', 'email' => 'admin@mada.com', 'password' => Hash::make('password'), 'shop_id' => $shop3->id]);
        Product::create(['shop_id' => $shop3->id, 'category_name' => 'Espresso Based', 'name' => 'Mada Americano', 'price' => 20000]);
    }
}
