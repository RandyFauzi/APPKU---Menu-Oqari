<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\StockMutation;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            ->withCount('recipes')
            ->orderBy('name')
            ->get();

        $lowStockCount = $materials->filter(fn ($m) => (float) $m->current_stock <= (float) $m->minimum_stock)->count();
        $totalAssetValue = $materials->sum(fn ($m) => (float) $m->current_stock * (float) $m->cost_per_unit);

        $mutations = StockMutation::where('shop_id', $shopId)
            ->with(['rawMaterial', 'user'])
            ->latest()
            ->limit(100)
            ->get();

        return response()->json([
            'materials' => $materials,
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
    public function saveMaterial(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (! $shopId) {
            abort(403);
        }

        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'cost_per_unit' => ['required', 'numeric', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100'],
        ]);

        $material = DB::transaction(function () use ($shopId, $validated) {
            $isNew = empty($validated['id']);
            $material = RawMaterial::updateOrCreate(
                ['id' => $validated['id'] ?? null, 'shop_id' => $shopId],
                [
                    'name' => $validated['name'],
                    'sku' => $validated['sku'] ?? null,
                    'unit' => $validated['unit'],
                    'current_stock' => $validated['current_stock'],
                    'minimum_stock' => $validated['minimum_stock'],
                    'cost_per_unit' => $validated['cost_per_unit'],
                ]
            );

            // Jika item baru & ada saldo awal, catat mutasi masuk
            if ($isNew && (float) $validated['current_stock'] > 0) {
                StockMutation::create([
                    'shop_id' => $shopId,
                    'raw_material_id' => $material->id,
                    'type' => 'in',
                    'amount' => $validated['current_stock'],
                    'balance_after' => $validated['current_stock'],
                    'reference_type' => 'InitialStock',
                    'notes' => 'Saldo Awal Bahan Baku',
                    'user_id' => Auth::id(),
                ]);
            }

            return $material;
        });

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil disimpan.',
            'material' => $material,
        ]);
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
    public function adjustStock(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (! $shopId) {
            abort(403);
        }

        $validated = $request->validate([
            'raw_material_id' => ['required', 'integer'],
            'type' => ['required', 'in:in,adjustment,waste'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:255'],
            'new_cost_per_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $material = RawMaterial::where('shop_id', $shopId)->findOrFail($validated['raw_material_id']);

        $updated = DB::transaction(function () use ($shopId, $material, $validated) {
            $type = $validated['type'];
            $amount = (float) $validated['amount'];
            $current = (float) $material->current_stock;

            if ($type === 'in') {
                // Restok / Belanja Masuk
                $newStock = $current + $amount;
                $change = $amount;
                // Jika input harga modal baru pada restok, update cost_per_unit
                if (isset($validated['new_cost_per_unit']) && (float) $validated['new_cost_per_unit'] > 0) {
                    $material->cost_per_unit = (float) $validated['new_cost_per_unit'];
                }
            } elseif ($type === 'adjustment') {
                // Stok Opname (Nilai yang diinput adalah stok riil di gudang)
                $newStock = $amount;
                $change = $newStock - $current;
            } else {
                // Waste / Tumpah / Kadaluarsa
                $newStock = max(0, $current - $amount);
                $change = -$amount;
            }

            $material->current_stock = $newStock;
            $material->save();

            StockMutation::create([
                'shop_id' => $shopId,
                'raw_material_id' => $material->id,
                'type' => $type,
                'amount' => $change,
                'balance_after' => $newStock,
                'reference_type' => 'ManualAdjustment',
                'notes' => $validated['notes'] ?? ($type === 'in' ? 'Restok Bahan' : ($type === 'waste' ? 'Barang Rusak/Basi' : 'Stok Opname')),
                'user_id' => Auth::id(),
            ]);

            // Sinkronisasi ulang HPP semua produk yang memakai bahan ini
            $material->load('recipes.product');
            foreach ($material->recipes as $recipe) {
                if ($recipe->product) {
                    $this->inventoryService->syncProductHpp($recipe->product);
                }
            }

            return $material;
        });

        return response()->json([
            'success' => true,
            'message' => 'Stok bahan berhasil diperbarui.',
            'material' => $updated,
        ]);
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
            'recipes.*.raw_material_id' => ['required', 'integer'],
            'recipes.*.amount' => ['required', 'numeric', 'min:0.001'],
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
