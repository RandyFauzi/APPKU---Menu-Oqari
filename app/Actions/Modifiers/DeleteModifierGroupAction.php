<?php

namespace App\Actions\Modifiers;

use App\Models\ActivityLog;
use App\Models\ModifierGroup;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class DeleteModifierGroupAction
{
    public function execute(Shop $shop, int $groupId): bool
    {
        return DB::transaction(function () use ($shop, $groupId) {
            $group = ModifierGroup::where('shop_id', $shop->id)->findOrFail($groupId);
            $groupName = $group->name;

            // Detach products from pivot
            $group->products()->detach();

            // Delete modifiers
            $group->modifiers()->delete();

            // Delete group
            $group->delete();

            ActivityLog::create([
                'shop_id' => $shop->id,
                'user_id' => auth()->id(),
                'action' => 'DELETE_MODIFIER_GROUP',
                'description' => 'Menghapus grup addon: '.$groupName,
                'entity_type' => 'ModifierGroup',
                'entity_id' => $groupId,
            ]);

            return true;
        });
    }
}
