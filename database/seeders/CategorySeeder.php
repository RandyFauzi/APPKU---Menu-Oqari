<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Shop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = Shop::all();
        $defaultCategories = ['Coffee', 'Tea', 'Beverages', 'Foods', 'Snacks', 'Sweets'];

        foreach ($shops as $shop) {
            // Check if shop already has categories to avoid duplication
            if (Category::where('shop_id', $shop->id)->exists()) {
                continue;
            }

            foreach ($defaultCategories as $index => $categoryName) {
                Category::create([
                    'shop_id' => $shop->id,
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
