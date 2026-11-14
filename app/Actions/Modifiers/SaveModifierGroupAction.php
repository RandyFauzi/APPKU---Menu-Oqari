<?php

namespace App\Actions\Modifiers;

use App\Models\ActivityLog;
use App\Models\Modifier;
use App\Models\ModifierGroup;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class SaveModifierGroupAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Shop $shop, array $data): ModifierGroup
    {
        return DB::transaction(function () use ($shop, $data) {
            $isNew = empty($data['id']);

            if ($isNew) {
                $group = new ModifierGroup;
                $group->shop_id = $shop->id;
            } else {
                $group = ModifierGroup::where('shop_id', $shop->id)->findOrFail($data['id']);
            }

            $group->name = $data['name'];
            $group->selection_type = $data['selection_type'] ?? 'MULTIPLE';
            $group->category_target = $data['category_target'] ?? 'all';
            $group->is_required = (bool) ($data['is_required'] ?? false);
            $group->min_selections = (int) ($data['min_selections'] ?? 0);
            $group->max_selections = isset($data['max_selections']) ? (int) $data['max_selections'] : null;
            $group->is_active = (bool) ($data['is_active'] ?? true);
            $group->save();

            // Sync modifiers items
            $keptItemIds = [];
            foreach ($data['items'] ?? [] as $itemData) {
                if (! empty($itemData['id'])) {
                    $modifier = Modifier::where('modifier_group_id', $group->id)->find($itemData['id']);
                } else {
                    $modifier = new Modifier(['modifier_group_id' => $group->id]);
                }

                if ($modifier) {
                    $modifier->name = trim((string) $itemData['name']);
                    $modifier->price_adjustment = (float) ($itemData['price_adjustment'] ?? 0);
                    $modifier->cogs_adjustment = (float) ($itemData['cogs_adjustment'] ?? 0);
                    $modifier->is_active = isset($itemData['is_active']) ? (bool) $itemData['is_active'] : true;
                    $modifier->save();
                    $keptItemIds[] = $modifier->id;
                }
            }

            // Remove modifiers no longer in the list
            Modifier::where('modifier_group_id', $group->id)
                ->whereNotIn('id', $keptItemIds)
                ->delete();

            // Sync products if specified
            if (isset($data['product_ids']) && is_array($data['product_ids'])) {
                $validProductIds = Product::where('shop_id', $shop->id)
                    ->whereIn('id', $data['product_ids'])
                    ->pluck('id')
                    ->all();
                $group->products()->sync($validProductIds);
            }

            // Audit activity log
            ActivityLog::create([
                'shop_id' => $shop->id,
                'user_id' => auth()->id(),
                'action' => $isNew ? 'CREATE_MODIFIER_GROUP' : 'UPDATE_MODIFIER_GROUP',
                'description' => ($isNew ? 'Membuat grup addon: ' : 'Memperbarui grup addon: ').$group->name,
                'entity_type' => 'ModifierGroup',
                'entity_id' => $group->id,
            ]);

            return $group->load(['modifiers', 'products:id,name']);
        });
    }
}
