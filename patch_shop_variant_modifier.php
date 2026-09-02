<?php
$content = file_get_contents("app/Http/Controllers/ShopController.php");

$old_calc = "            \$subtotal = \$product->price * \$item['qty'];
            \$total += \$subtotal;";
            
$new_calc = "            // Validasi Varian jika dikirimkan
            \$variantId = \$item['variant_id'] ?? null;
            \$variantName = null;
            \$price = \$product->price;
            
            if (\$variantId) {
                \$variant = \\App\\Models\\ProductVariant::where('id', \$variantId)
                    ->where('product_id', \$product->id)
                    ->where('is_active', true)
                    ->first();
                    
                if (!\$variant) {
                    return response()->json(['success' => false, 'message' => 'Varian produk tidak valid atau sudah tidak aktif.'], 400);
                }
                
                \$variantName = \$variant->name;
                \$price += \$variant->price_adjustment;
            }
            
            // Validasi Modifiers jika dikirimkan
            \$modifiersSnapshot = [];
            if (!empty(\$item['modifiers']) && is_array(\$item['modifiers'])) {
                foreach (\$item['modifiers'] as \$modId) {
                    \$modifier = \\App\\Models\\Modifier::where('id', \$modId)
                        ->whereHas('group', function(\$q) use (\$product) {
                            \$q->where('product_id', \$product->id);
                        })
                        ->where('is_active', true)
                        ->first();
                        
                    if (\$modifier) {
                        \$price += \$modifier->price_adjustment;
                        \$modifiersSnapshot[] = [
                            'id' => \$modifier->id,
                            'name' => \$modifier->name,
                            'price_adjustment' => \$modifier->price_adjustment
                        ];
                    }
                }
            }

            \$subtotal = \$price * \$item['qty'];
            \$total += \$subtotal;";
            
$content = str_replace($old_calc, $new_calc, $content);

// Update order item insertion
$old_insert = "                'product_id' => \$product->id,
                'product_name' => \$product->name, // Snapshot name
                'quantity' => \$item['qty'],
                'price' => \$product->price,
                'subtotal' => \$subtotal, // Snapshot item subtotal";

$new_insert = "                'product_id' => \$product->id,
                'product_name' => \$product->name, // Snapshot name
                'variant_id' => \$variantId,
                'variant_name' => \$variantName,
                'modifiers' => empty(\$modifiersSnapshot) ? null : json_encode(\$modifiersSnapshot),
                'quantity' => \$item['qty'],
                'price' => \$price, // Adjusted price
                'subtotal' => \$subtotal, // Snapshot item subtotal";

$content = str_replace($old_insert, $new_insert, $content);
file_put_contents("app/Http/Controllers/ShopController.php", $content);
