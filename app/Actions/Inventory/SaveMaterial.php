<?php

namespace App\Actions\Inventory;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\StockMutation;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveMaterial
{
    public function __construct(private InventoryService $inventory) {}

    /** @param array<string, mixed> $data */
    public function execute(User $user, array $data): RawMaterial
    {
        return DB::transaction(function () use ($user, $data): RawMaterial {
            $isNew = empty($data['id']);
            $material = $isNew ? new RawMaterial(['shop_id' => $user->shop_id])
                : RawMaterial::where('shop_id', $user->shop_id)->whereKey($data['id'])->lockForUpdate()->firstOrFail();
            $previous = $isNew ? null : $material->only(['unit', 'name', 'cost_per_unit', 'minimum_stock']);
            if (! $isNew && strcasecmp(trim($material->unit), $data['unit']) !== 0 && ((float) $material->current_stock != 0 || $material->recipes()->exists() || $material->mutations()->exists())) {
                throw ValidationException::withMessages(['unit' => 'Satuan tidak dapat diubah karena bahan sudah memiliki stok, resep, atau riwayat mutasi. Buat bahan baru untuk satuan berbeda.']);
            }
            $material->fill(['name' => $data['name'], 'unit' => $data['unit'], 'sku' => $data['sku'] ?? null,
                'minimum_stock' => $data['minimum_stock'], 'cost_per_unit' => $data['cost_per_unit']]);
            if ($isNew) {
                $material->current_stock = $data['current_stock'];
            }
            $material->save();
            if ($isNew && (float) $material->current_stock > 0) {
                StockMutation::create(['shop_id' => $user->shop_id, 'raw_material_id' => $material->id, 'unit' => $material->unit,
                    'type' => 'in', 'amount' => $material->current_stock, 'balance_after' => $material->current_stock,
                    'reference_type' => 'InitialStock', 'notes' => 'Saldo Awal Bahan Baku', 'user_id' => $user->id]);
            }
            if (! $isNew && $material->wasChanged('cost_per_unit')) {
                $products = Product::where('shop_id', $user->shop_id)->whereHas('recipes', fn ($query) => $query->where('raw_material_id', $material->id))
                    ->with('recipes.rawMaterial')->lockForUpdate()->get();
                foreach ($products as $product) {
                    $this->inventory->syncProductHpp($product);
                }
            }
            ActivityLog::create(['shop_id' => $user->shop_id, 'user_id' => $user->id, 'action' => $isNew ? 'inventory.material_created' : 'inventory.material_updated',
                'entity_type' => RawMaterial::class, 'entity_id' => $material->id, 'description' => 'Menyimpan bahan baku',
                'old_values' => $previous, 'new_values' => $material->only(['name', 'unit', 'current_stock', 'minimum_stock', 'cost_per_unit'])]);

            return $material;
        }, 3);
    }
}
