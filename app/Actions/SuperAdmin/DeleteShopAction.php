<?php

namespace App\Actions\SuperAdmin;

use App\Models\Shop;
use App\Models\SuperAdminAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteShopAction
{
    /**
     * @throws ValidationException if the typed confirmation does not match the shop slug
     */
    public function execute(Shop $shop, string $confirmSlug): void
    {
        if ($confirmSlug !== $shop->slug) {
            throw ValidationException::withMessages([
                'confirm_slug' => 'Ketikan tidak cocok dengan slug toko. Toko tidak dihapus.',
            ]);
        }

        DB::transaction(function () use ($shop) {
            $snapshot = [
                'users_count' => $shop->users()->count(),
                'orders_count' => $shop->orders()->count(),
                'products_count' => $shop->products()->count(),
            ];

            // Log BEFORE deleting — once the row is gone we lose the ability
            // to describe what was destroyed.
            SuperAdminAuditLog::record(
                action: 'shop.deleted',
                targetType: 'Shop',
                targetId: $shop->id,
                targetLabel: $shop->name,
                meta: $snapshot,
            );

            $shop->delete();
        });
    }
}
