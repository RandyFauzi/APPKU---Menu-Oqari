<?php

namespace App\Actions\SuperAdmin;

use App\Models\Shop;
use App\Models\SuperAdminAuditLog;
use Illuminate\Support\Facades\DB;

class ActivateShopAction
{
    public function execute(Shop $shop): Shop
    {
        return DB::transaction(function () use ($shop) {
            $shop->update([
                'status' => 'active',
                'suspended_at' => null,
                'suspended_reason' => null,
                'suspended_by' => null,
            ]);

            SuperAdminAuditLog::record(
                action: 'shop.activated',
                targetType: 'Shop',
                targetId: $shop->id,
                targetLabel: $shop->name,
            );

            return $shop;
        });
    }
}
