<?php

namespace App\Actions\Products;

use App\Models\Category;
use App\Models\ModifierGroup;
use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SaveBulkMenuAction
{
    /**
     * Menyimpan banyak item menu sekaligus (Bulk Save / Import) untuk sebuah toko.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<string, mixed>  $uploadedFiles
     * @return array{saved: Collection<int, Product>, categories: Collection<int, Category>}
     */
    public function execute(int $shopId, array $items, array $uploadedFiles = []): array
    {
        $savedMenus = DB::transaction(function () use ($items, $shopId, $uploadedFiles) {
            $saved = [];

            foreach ($items as $index => $itemData) {
                if (empty($itemData['name']) || ! isset($itemData['price'])) {
                    continue;
                }

                $catId = null;
                $catName = null;
                $rawCat = $itemData['category_id'] ?? $itemData['category_name'] ?? null;

                if ($rawCat) {
                    if (is_numeric($rawCat)) {
                        $cat = Category::where('shop_id', $shopId)->find((int) $rawCat);
                        if ($cat) {
                            $catId = $cat->id;
                            $catName = $cat->name;
                        }
                    } else {
                        $trimmed = trim((string) $rawCat);
                        if ($trimmed !== '') {
                            $cat = Category::firstOrCreate(
                                ['shop_id' => $shopId, 'name' => $trimmed],
                                ['shop_id' => $shopId, 'name' => $trimmed]
                            );
                            $catId = $cat->id;
                            $catName = $cat->name;
                        }
                    }
                }

                if (! $catName) {
                    $defaultCat = Category::where('shop_id', $shopId)->first();
                    if ($defaultCat) {
                        $catId = $defaultCat->id;
                        $catName = $defaultCat->name;
                    } else {
                        $cat = Category::firstOrCreate(
                            ['shop_id' => $shopId, 'name' => 'Coffee'],
                            ['shop_id' => $shopId, 'name' => 'Coffee']
                        );
                        $catId = $cat->id;
                        $catName = $cat->name;
                    }
                }

                $productData = [
                    'name' => trim((string) $itemData['name']),
                    'price' => (int) $itemData['price'],
                    'category_id' => $catId,
                    'category_name' => $catName,
                    'description' => $itemData['description'] ?? ($itemData['desc'] ?? null),
                ];

                if (array_key_exists('discount_price', $itemData)) {
                    $discPrice = $itemData['discount_price'];
                    $productData['discount_price'] = ($discPrice !== null && $discPrice !== '') ? (float) $discPrice : null;
                }

                if (isset($itemData['is_discount_active'])) {
                    $productData['is_discount_active'] = filter_var($itemData['is_discount_active'], FILTER_VALIDATE_BOOLEAN);
                }

                $menu = null;
                if (! empty($itemData['id']) && is_numeric($itemData['id']) && (int) $itemData['id'] > 0) {
                    $menu = Product::where('id', (int) $itemData['id'])->where('shop_id', $shopId)->first();
                }

                // Handle image upload if present
                $fileKey = "images.{$index}";
                if (isset($uploadedFiles['images'][$index])) {
                    $file = $uploadedFiles['images'][$index];

                    if ($menu && $menu->image_path) {
                        Storage::disk('public')->delete($menu->image_path);
                    }

                    $productIdDir = $menu ? $menu->id : Str::uuid()->toString();
                    $path = app(MediaService::class)->storeProductImage($file, $shopId, $productIdDir);
                    $productData['image_path'] = $path;
                }

                if ($menu) {
                    $menu->update($productData);
                    $saved[] = $menu;
                } else {
                    $productData['shop_id'] = $shopId;
                    $menu = Product::create($productData);
                    $saved[] = $menu;
                }

                if (isset($itemData['modifier_group_ids'])) {
                    $groupIds = $itemData['modifier_group_ids'];
                    if (is_string($groupIds)) {
                        $groupIds = json_decode($groupIds, true);
                    }
                    if (is_array($groupIds)) {
                        $validGroupIds = ModifierGroup::where('shop_id', $shopId)->whereIn('id', $groupIds)->pluck('id')->all();
                        $menu->modifierGroups()->sync($validGroupIds);
                    } else {
                        $menu->modifierGroups()->detach();
                    }
                }
            }

            return $saved;
        });

        $savedIds = collect($savedMenus)->pluck('id');
        $freshMenus = Product::whereIn('id', $savedIds)
            ->with(['recipes.rawMaterial', 'category', 'modifierGroups.modifiers'])
            ->get();

        $allCategories = Category::where('shop_id', $shopId)->get();

        return [
            'saved' => $freshMenus,
            'categories' => $allCategories,
        ];
    }
}
