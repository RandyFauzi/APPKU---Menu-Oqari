<?php

use App\Models\Product;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
echo Product::where('shop_id', 1)->count();
