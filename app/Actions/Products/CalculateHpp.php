<?php

namespace App\Actions\Products;

use App\Models\RawMaterial;
use Illuminate\Validation\ValidationException;

class CalculateHpp
{
    public function __construct(private ConvertRecipeQuantity $convertQuantity) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function execute(int $shopId, array $data, bool $lockMaterials = false): array
    {
        $ids = array_column($data['recipes'], 'raw_material_id');
        $query = RawMaterial::where('shop_id', $shopId)->whereIn('id', $ids)->orderBy('id');
        if ($lockMaterials) {
            $query->lockForUpdate();
        }
        $materials = $query->get()->keyBy('id');
        if ($materials->count() !== count($ids)) {
            throw ValidationException::withMessages(['recipes' => 'Bahan tidak tersedia atau bukan milik cafe ini.']);
        }
        $lines = [];
        $missingCosts = [];
        $ingredientCents = 0;
        foreach ($data['recipes'] as $recipe) {
            $material = $materials->get($recipe['raw_material_id']);
            $normalizedAmount = $this->convertQuantity->execute((float) $recipe['amount'], $recipe['unit'] ?? null, $material->unit);
            $cost = round((float) $material->cost_per_unit * $normalizedAmount, 2);
            if (! is_finite($cost) || $cost > 99999999.99) {
                throw ValidationException::withMessages(['recipes' => 'Biaya komposisi melebihi batas HPP yang dapat disimpan.']);
            }
            if ((float) $material->cost_per_unit <= 0) {
                $missingCosts[] = $material->name;
            }
            $ingredientCents += (int) round($cost * 100);
            $lines[] = [
                'raw_material_id' => $material->id, 'name' => $material->name, 'unit' => $material->unit,
                'amount' => $normalizedAmount, 'notes' => $recipe['notes'] ?? null, 'unit_cost' => (float) $material->cost_per_unit, 'cost' => $cost,
            ];
        }
        $ingredientsCost = $ingredientCents / 100;
        $quantity = (float) ($data['production_quantity'] ?? 1);
        $labor = round((float) ($data['labor_cost'] ?? 0), 2);
        $overheadCost = ($data['overhead_mode'] ?? 'fixed') === 'percentage'
            ? round(($ingredientsCost + $labor) * (float) ($data['overhead_percent'] ?? 0) / 100, 2)
            : round((float) $data['overhead_cost'], 2);
        $totalCost = round($ingredientsCost + $labor + $overheadCost, 2);
        $knownCost = round($totalCost / $quantity, 2);
        if ($totalCost > 99999999.99 || $knownCost > 99999999.99) {
            throw ValidationException::withMessages(['overhead_cost' => 'Total HPP atau HPP per unit melebihi batas 99.999.999,99.']);
        }
        $price = round((float) $data['selling_price'], 2);
        $complete = $missingCosts === [];
        $hpp = $complete ? $knownCost : null;
        $profit = $complete ? round($price - $hpp, 2) : null;
        $target = $data['target_margin'] ?? null;

        $suggested = null;
        if ($complete) {
            $suggested = ($data['pricing_method'] ?? 'margin') === 'markup'
                ? (isset($data['target_markup']) ? ceil(round($hpp * (1 + (float) $data['target_markup'] / 100) * 100, 6)) / 100 : null)
                : ($target !== null ? ceil(round(($hpp / (1 - (float) $target / 100)) * 100, 6)) / 100 : null);
        }
        $basePrice = $suggested ?? $price;
        $rate = (float) ($data['tax_rate'] ?? 0);
        $inclusive = ($data['tax_mode'] ?? 'exclusive') === 'inclusive';
        $tax = round($inclusive ? $basePrice - $basePrice / (1 + $rate / 100) : $basePrice * $rate / 100, 2);
        $finalPrice = $inclusive ? $basePrice : round($basePrice + $tax, 2);
        $netRevenue = $inclusive ? round($basePrice - $tax, 2) : $basePrice;
        $netProfit = $complete ? round($netRevenue - $hpp, 2) : null;

        return [
            'production_quantity' => $quantity, 'production_unit' => $data['production_unit'] ?? 'porsi',
            'labor_cost' => $labor, 'total_hpp' => $complete ? $totalCost : null,
            'tax_amount' => $complete ? $tax : null, 'tax_mode' => $data['tax_mode'] ?? 'exclusive',
            'price_before_tax' => $complete ? ($inclusive ? round($basePrice - $tax, 2) : $basePrice) : null,
            'final_price' => $complete ? $finalPrice : null,
            'total_final_price' => $complete ? round($finalPrice * $quantity, 2) : null,
            'profit_after_tax' => $netProfit,
            'margin_after_tax' => $complete && $netRevenue > 0 ? round($netProfit / $netRevenue * 100, 2) : null,
            'lines' => $lines, 'ingredients_cost' => (float) $ingredientsCost, 'overhead_cost' => $overheadCost,
            'known_cost' => $knownCost, 'hpp' => $hpp, 'selling_price' => $price, 'gross_profit' => $profit,
            'margin_percent' => $complete && $price > 0 ? round($profit / $price * 100, 2) : null,
            'markup_percent' => $complete && $hpp > 0 ? round($profit / $hpp * 100, 2) : null,
            'suggested_price' => $suggested,
            'is_complete' => $complete, 'missing_costs' => $missingCosts,
        ];
    }
}
