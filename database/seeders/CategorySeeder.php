<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = \App\Models\Shop::all();
        $defaultCategories = ['Coffee', 'Tea', 'Beverages', 'Foods', 'Snacks', 'Sweets'];

        foreach ($shops as $shop) {
            // Check if shop already has categories to avoid duplication
            if (\App\Models\Category::where('shop_id', $shop->id)->exists()) {
                continue;
            }

            foreach ($defaultCategories as $index => $categoryName) {
                \App\Models\Category::create([
                    'shop_id' => $shop->id,
                    'name' => $categoryName,
                    'slug' => \Illuminate\Support\Str::slug($categoryName),
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}
