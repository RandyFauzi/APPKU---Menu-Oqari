<?php

namespace App\Actions\Products;

use App\Models\ActivityLog;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateHppMaterial
{
    /** @param array<string, mixed> $data */
    public function execute(User $user, array $data): RawMaterial
    {
        return DB::transaction(function () use ($user, $data): RawMaterial {
            $cost = round((float) $data['purchase_total'] / (float) $data['purchase_quantity'], 6);
            if ($cost <= 0) {
                throw ValidationException::withMessages(['purchase_quantity' => 'Harga per satuan terlalu kecil. Gunakan satuan pembelian yang lebih besar.']);
            }
            $material = RawMaterial::create(['shop_id' => $user->shop_id, 'name' => trim($data['name']), 'unit' => $data['unit'], 'cost_per_unit' => $cost, 'current_stock' => 0, 'minimum_stock' => 0]);
            ActivityLog::create(['shop_id' => $user->shop_id, 'user_id' => $user->id, 'action' => 'material.hpp_created', 'entity_type' => RawMaterial::class, 'entity_id' => $material->id, 'description' => 'Membuat bahan dari kalkulator HPP', 'new_values' => $data + ['cost_per_unit' => $cost]]);

            return $material;
        });
    }
}
