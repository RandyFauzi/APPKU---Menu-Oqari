<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Products\CalculateHpp;
use App\Actions\Products\CreateHppMaterial;
use App\Actions\Products\SaveProductHpp;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateHppRequest;
use App\Http\Requests\StoreHppMaterialRequest;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HppCalculatorController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('manage-hpp');
        abort_unless($request->user()->shop_id, 403);
        $filters = $request->validate(['product_id' => ['nullable', 'integer']]);
        $shopId = $request->user()->shop_id;
        $product = ! empty($filters['product_id'])
            ? Product::where('shop_id', $shopId)->with('recipes')->findOrFail($filters['product_id']) : null;
        $products = Product::where('shop_id', $shopId)->orderBy('name')->get(['id', 'name']);
        $materials = RawMaterial::where('shop_id', $shopId)->orderBy('name')->get(['id', 'name', 'unit', 'cost_per_unit']);
        $settings = $product?->hpp_settings ?? [];
        $quantity = (float) ($settings['production_quantity'] ?? 1);
        $initial = array_merge([
            'production_quantity' => 1, 'production_unit' => 'porsi', 'labor_cost' => 0,
            'overhead_mode' => 'fixed', 'overhead_percent' => 0, 'pricing_method' => 'margin', 'target_markup' => null,
            'tax_rate' => 0, 'tax_mode' => 'exclusive',
            'overhead_cost' => (float) ($product?->hpp_overhead_cost ?? 0),
        ], $settings, [
            'recipes' => $product?->recipes->map(fn ($recipe): array => ['raw_material_id' => $recipe->raw_material_id, 'amount' => $recipe->batch_amount ?? (float) $recipe->amount * $quantity, 'unit' => '', 'notes' => $recipe->notes])->values()->all() ?: [['raw_material_id' => '', 'amount' => 1, 'unit' => '', 'notes' => '']],
            'selling_price' => (float) ($product?->price ?? 0), 'target_margin' => $product?->hpp_target_margin,
        ]);

        return view('Admin.Hpp.index', compact('product', 'products', 'materials', 'initial'));
    }

    public function calculate(CalculateHppRequest $request, CalculateHpp $calculator): JsonResponse
    {
        return response()->json(['success' => true, 'result' => $calculator->execute($request->user()->shop_id, $request->validated())]);
    }

    public function storeMaterial(StoreHppMaterialRequest $request, CreateHppMaterial $createMaterial): JsonResponse
    {
        $material = $createMaterial->execute($request->user(), $request->validated());

        return response()->json(['success' => true, 'material' => $material->only(['id', 'name', 'unit', 'cost_per_unit'])], 201);
    }

    public function update(CalculateHppRequest $request, int $product, SaveProductHpp $saveHpp): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Resep dan HPP disimpan. Harga jual tetap.',
            'result' => $saveHpp->execute($request->user(), $product, $request->validated())]);
    }
}
