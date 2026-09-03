<?php

namespace App\Actions\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class SaveProductAction
{
    public function execute(int $shopId, array $data, $imagePath = null): Product
    {
        return DB::transaction(function () use ($shopId, $data, $imagePath) {
            // 1. Resolve Category
            $categoryId = null;
            if (!empty($data['category_id'])) {
                $categoryId = $data['category_id'];
            } elseif (!empty($data['category_name']) || !empty($data['categoryId'])) {
                $categoryName = $data['category_name'] ?? $data['categoryId'];
                $category = Category::firstOrCreate(
                    ['shop_id' => $shopId, 'name' => $categoryName],
                    ['sort_order' => 0]
                );
                $categoryId = $category->id;
            }

            // 2. Prepare Product Data
            $productData = [
                'name' => $data['name'],
                'price' => $data['price'],
                'cogs' => $data['cogs'] ?? 0,
                'category_id' => $categoryId,
                'description' => $data['description'] ?? null,
            ];

            if ($imagePath) {
                $productData['image_path'] = $imagePath;
            }

            // 3. Save Product
            $product = Product::updateOrCreate(
                ['id' => $data['id'] ?? null, 'shop_id' => $shopId],
                $productData
            );

            // 4. Sync Variants (if provided)
            if (isset($data['variants']) && is_array($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            // 5. Sync Modifiers (if provided)
            if (isset($data['modifier_groups']) && is_array($data['modifier_groups'])) {
                $this->syncModifierGroups($product, $data['modifier_groups']);
            }

            return $product;
        });
    }

    private function syncVariants(Product $product, array $variants)
    {
        $keepIds = [];
        foreach ($variants as $v) {
            if (empty($v['name'])) continue;
            
            $variant = $product->variants()->updateOrCreate(
                ['id' => $v['id'] ?? null],
                [
                    'name' => $v['name'],
                    'price_adjustment' => $v['price_adjustment'] ?? 0,
                    'cogs_adjustment' => $v['cogs_adjustment'] ?? 0,
                    'is_active' => $v['is_active'] ?? true,
                ]
            );
            $keepIds[] = $variant->id;
        }
        
        // Delete variants not in the request
        $product->variants()->whereNotIn('id', $keepIds)->delete();
    }

    private function syncModifierGroups(Product $product, array $groups)
    {
        $keepGroupIds = [];
        foreach ($groups as $g) {
            if (empty($g['name'])) continue;

            $group = $product->modifierGroups()->updateOrCreate(
                ['id' => $g['id'] ?? null],
                [
                    'name' => $g['name'],
                    'is_required' => $g['is_required'] ?? false,
                    'min_choices' => $g['min_choices'] ?? 0,
                    'max_choices' => $g['max_choices'] ?? 1,
                    'is_active' => $g['is_active'] ?? true,
                ]
            );
            $keepGroupIds[] = $group->id;

            // Sync Modifiers for this group
            if (isset($g['modifiers']) && is_array($g['modifiers'])) {
                $keepModIds = [];
                foreach ($g['modifiers'] as $m) {
                    if (empty($m['name'])) continue;

                    $mod = $group->modifiers()->updateOrCreate(
                        ['id' => $m['id'] ?? null],
                        [
                            'name' => $m['name'],
                            'price_adjustment' => $m['price_adjustment'] ?? 0,
                            'cogs_adjustment' => $m['cogs_adjustment'] ?? 0,
                            'is_active' => $m['is_active'] ?? true,
                        ]
                    );
                    $keepModIds[] = $mod->id;
                }
                $group->modifiers()->whereNotIn('id', $keepModIds)->delete();
            }
        }
        
        $product->modifierGroups()->whereNotIn('id', $keepGroupIds)->delete();
    }
}
