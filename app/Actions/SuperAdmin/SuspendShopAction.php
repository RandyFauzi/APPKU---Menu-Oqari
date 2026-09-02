<?php

namespace App\Actions\SuperAdmin;

use App\Models\Shop;
use App\Models\SuperAdminAuditLog;
use Illuminate\Support\Facades\DB;

class SuspendShopAction
{
    public function execute(Shop $shop, string $reason): Shop
    {
        return DB::transaction(function () use ($shop, $reason) {
            $shop->update([
                'status' => 'suspended',
                'suspended_at' => now(),
                'suspended_reason' => $reason,
                'suspended_by' => auth()->id(),
            ]);

            SuperAdminAuditLog::record(
                action: 'shop.suspended',
                targetType: 'Shop',
                targetId: $shop->id,
                targetLabel: $shop->name,
                meta: ['reason' => $reason],
            );

            return $shop;
        });
    }
}
