<?php

namespace App\Actions\Inventory;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\StockMutation;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdjustStock
{
    public function __construct(private InventoryService $inventory) {}

    /** @param array<string, mixed> $data */
    public function execute(User $user, array $data): RawMaterial
    {
        return DB::transaction(function () use ($user, $data): RawMaterial {
            $material = RawMaterial::where('shop_id', $user->shop_id)->whereKey($data['raw_material_id'])->lockForUpdate()->firstOrFail();
            $amount = (int) round((float) $data['amount'] * 1000000);
            $current = (int) round((float) $material->current_stock * 1000000);
            if ($data['type'] === 'waste' && $amount > $current) {
                throw ValidationException::withMessages(['amount' => 'Jumlah barang rusak tidak boleh melebihi stok saat ini.']);
            }
            $newStock = match ($data['type']) {
                'in' => $current + $amount, 'adjustment' => $amount, default => $current - $amount
            };
            if ($newStock > 999999999999999) {
                throw ValidationException::withMessages(['amount' => 'Saldo stok melampaui batas penyimpanan.']);
            }
            if ($data['type'] === 'in' && isset($data['new_cost_per_unit']) && (float) $data['new_cost_per_unit'] > 0) {
                $material->cost_per_unit = $data['new_cost_per_unit'];
            }
            $material->current_stock = $newStock / 1000000;
            $material->save();
            StockMutation::create(['shop_id' => $user->shop_id, 'raw_material_id' => $material->id, 'unit' => $material->unit,
                'type' => $data['type'], 'amount' => ($newStock - $current) / 1000000, 'balance_after' => $material->current_stock,
                'reference_type' => 'ManualAdjustment', 'notes' => $data['notes'] ?? match ($data['type']) {
                    'in' => 'Restok Bahan', 'waste' => 'Barang Rusak/Basi', default => 'Stok Opname'
                },
                'user_id' => $user->id]);
            $products = Product::where('shop_id', $user->shop_id)->whereHas('recipes', fn ($query) => $query->where('raw_material_id', $material->id))
                ->with('recipes.rawMaterial')->lockForUpdate()->get();
            foreach ($products as $product) {
                $this->inventory->syncProductHpp($product);
            }

            return $material;
        }, 3);
    }
}
