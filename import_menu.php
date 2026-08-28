<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jsonPath = 'C:/laragon/www/Menu App/Bitten-Coffee-main/js/menu_gen.json';
if (!file_exists($jsonPath)) {
    die("File not found: " . $jsonPath);
}

$json = file_get_contents($jsonPath);
$items = json_decode($json, true);

if (!$items) {
    die("Failed to parse JSON");
}

$shop = \App\Models\Shop::first();
if (!$shop) {
    die("No shop found");
}

$count = 0;
foreach ($items as $item) {
    // Map JSON fields to products table columns
    $product = \App\Models\Product::updateOrCreate(
        [
            'shop_id' => $shop->id,
            'name' => $item['name']
        ],
        [
            'price' => $item['price'],
            'category_name' => $item['categoryId'] ?? 'uncategorized',
            'image_url' => $item['image'] ?? null,
            'is_sold_out' => $item['soldOut'] ?? false
        ]
    );
    $count++;
}

echo "Successfully imported $count products.\n";
