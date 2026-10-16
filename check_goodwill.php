<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$s = Shop::where('slug', 'goodwill')->first();
if (! $s) {
    echo 'No shop';
    exit;
} echo json_encode(['id' => $s->id, 'products' => Product::where('shop_id', $s->id)->count(), 'categories' => Category::where('shop_id', $s->id)->count(), 'sold_out' => Product::where('shop_id', $s->id)->where('is_sold_out', true)->count()]);
