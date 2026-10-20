<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Inventory\AdjustStock;
use App\Actions\Inventory\SaveMaterial;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustInventoryStockRequest;
use App\Http\Requests\SaveInventoryMaterialRequest;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\StockMutation;
use App\Services\InventoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {
        $this->inventoryService->ensureTablesExist();
    }

    /**
     * Ambil data ringkasan inventori (daftar bahan, metrik, & riwayat mutasi).
     */
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (! $shopId) {
            return response()->json(['materials' => [], 'metrics' => [], 'mutations' => []]);
        }

        $materials = RawMaterial::where('shop_id', $shopId)
            ->withCount(['recipes', 'mutations'])
            ->orderBy('name')
            ->get();

        $materials->each(function (RawMaterial $material): void {
            $material->setAttribute('unit_locked', (float) $material->current_stock != 0 || $material->recipes_count > 0 || $material->mutations_count > 0);
        });

        $lowStockCount = $materials->filter(fn ($m) => (float) $m->current_stock <= (float) $m->minimum_stock)->count();
        $totalAssetValue = $materials->sum(fn ($m) => (float) $m->current_stock * (float) $m->cost_per_unit);

        $mutations = StockMutation::where('shop_id', $shopId)
            ->with(['rawMaterial', 'user'])
            ->latest()
            ->limit(100)
            ->get();

        return response()->json([
            'materials' => $materials,
            'units' => config('inventory.units'),
            'metrics' => [
                'total_materials' => $materials->count(),
                'low_stock_count' => $lowStockCount,
                'total_asset_value' => $totalAssetValue,
            ],
            'mutations' => $mutations,
        ]);
    }

    /**
     * Simpan bahan baku baru atau update yang sudah ada.
     */
    public function saveMaterial(SaveInventoryMaterialRequest $request, SaveMaterial $saveMaterial): JsonResponse
    {
        $material = $saveMaterial->execute($request->user(), $request->validated());

        return response()->json(['success' => true, 'message' => 'Bahan baku berhasil disimpan.', 'material' => $material]);
    }

    /**
     * Hapus bahan baku (Soft Delete).
     */
    public function deleteMaterial($id)
    {
        $shopId = Auth::user()->shop_id;
        $material = RawMaterial::where('shop_id', $shopId)->findOrFail($id);
        $material->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil dihapus.',
        ]);
    }

    /**
     * Penyesuaian stok (Restok / Opname / Rusak-Waste).
     */
    public function adjustStock(AdjustInventoryStockRequest $request, AdjustStock $adjustStock): JsonResponse
    {
        $material = $adjustStock->execute($request->user(), $request->validated());

        return response()->json(['success' => true, 'message' => 'Stok bahan berhasil diperbarui.', 'material' => $material]);
    }

    /**
     * Ambil resep komposisi bahan dari suatu menu.
     */
    public function getProductRecipes($productId)
    {
        $shopId = Auth::user()->shop_id;
        $product = Product::where('shop_id', $shopId)->findOrFail($productId);

        $recipes = ProductRecipe::where('product_id', $product->id)
            ->with('rawMaterial')
            ->get();

        return response()->json([
            'recipes' => $recipes,
            'calculated_hpp' => $product->calculateHppFromRecipes(),
        ]);
    }

    /**
     * Simpan / Perbarui Resep Komposisi Bahan untuk Menu (BOM).
     */
    public function saveProductRecipes(Request $request, $productId)
    {
        $shopId = Auth::user()->shop_id;
        $product = Product::where('shop_id', $shopId)->findOrFail($productId);

        $validated = $request->validate([
            'recipes' => ['array'],
            'recipes.*.raw_material_id' => ['required', 'integer', 'distinct', Rule::exists('raw_materials', 'id')->where('shop_id', $shopId)->whereNull('deleted_at')],
            'recipes.*.amount' => ['required', 'numeric', 'decimal:0,6', 'min:0.000001', 'max:999999.99'],
        ]);

        $calculatedHpp = DB::transaction(function () use ($product, $validated) {
            ProductRecipe::where('product_id', $product->id)->delete();

            if (! empty($validated['recipes'])) {
                foreach ($validated['recipes'] as $item) {
                    ProductRecipe::create([
                        'product_id' => $product->id,
                        'raw_material_id' => $item['raw_material_id'],
                        'amount' => $item['amount'],
                    ]);
                }
            }

            // Hitung & update kolom cogs di product
            return $this->inventoryService->syncProductHpp($product);
        });

        return response()->json([
            'success' => true,
            'message' => 'Resep bahan baku berhasil disimpan.',
            'cogs' => $calculatedHpp,
        ]);
    }
}
