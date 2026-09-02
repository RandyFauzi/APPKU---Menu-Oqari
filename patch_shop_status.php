<?php
$content = file_get_contents("app/Http/Controllers/ShopController.php");

$content = preg_replace(
    "/'total_price' => \\\$total,\s*'status' => 'Masuk',/",
    "'subtotal' => \$total,
                'grand_total' => \$total,
                'order_status' => 'CONFIRMED',
                'payment_status' => 'UNPAID',
                'fulfillment_type' => 'DINE_IN',", 
    $content
);

file_put_contents("app/Http/Controllers/ShopController.php", $content);
