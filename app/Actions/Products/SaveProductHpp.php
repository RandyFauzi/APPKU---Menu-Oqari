<?php

namespace App\Actions\Products;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveProductHpp
{
    public function __construct(private CalculateHpp $calculator) {}

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function execute(User $user, int $productId, array $data): array
    {
        return DB::transaction(function () use ($user, $productId, $data): array {
            $product = Product::where('shop_id', $user->shop_id)->whereKey($productId)->lockForUpdate()->firstOrFail();
            $result = $this->calculator->execute($user->shop_id, $data, true);
            if (! $result['is_complete']) {
                throw ValidationException::withMessages(['recipes' => 'Lengkapi harga modal semua bahan sebelum menyimpan HPP.']);
            }
            $previous = ['cogs' => $product->cogs, 'overhead_cost' => $product->hpp_overhead_cost];
            $product->recipes()->whereNotIn('raw_material_id', array_column($data['recipes'], 'raw_material_id'))->delete();
            $quantity = $result['production_quantity'];
            foreach ($result['lines'] as $line) {
                $amount = round($line['amount'] / $quantity, 6);
                if ($amount <= 0) {
                    throw ValidationException::withMessages(['recipes' => 'Takaran per unit terlalu kecil untuk stok. Perbesar satuan output atau takaran bahan.']);
                }
                $product->recipes()->updateOrCreate(['raw_material_id' => $line['raw_material_id']], [
                    'amount' => $amount, 'batch_amount' => $line['amount'], 'notes' => $line['notes'],
                ]);
            }
            $settings = [
                'production_quantity' => $quantity, 'production_unit' => $data['production_unit'] ?? 'porsi',
                'labor_cost' => $data['labor_cost'] ?? 0,
                'overhead_mode' => $data['overhead_mode'] ?? 'fixed', 'overhead_cost' => $data['overhead_cost'], 'overhead_percent' => $data['overhead_percent'] ?? 0,
                'pricing_method' => $data['pricing_method'] ?? 'margin', 'target_markup' => $data['target_markup'] ?? null,
                'tax_mode' => $data['tax_mode'] ?? 'exclusive', 'tax_rate' => $data['tax_rate'] ?? 0,
            ];
            $product->update([
                'cogs' => $result['hpp'], 'hpp_overhead_cost' => round(($result['overhead_cost'] + $result['labor_cost']) / $quantity, 2),
                'hpp_target_margin' => $data['target_margin'] ?? null, 'hpp_settings' => $settings,
            ]);
            ActivityLog::create([
                'shop_id' => $user->shop_id, 'user_id' => $user->id, 'action' => 'product.hpp_updated',
                'entity_type' => Product::class, 'entity_id' => $product->id,
                'description' => 'Memperbarui resep dan HPP produk: '.$product->name,
                'old_values' => $previous, 'new_values' => ['cogs' => $result['hpp'], 'settings' => $settings, 'recipes' => $result['lines']],
            ]);

            return $result;
        }, 3);
    }
}
