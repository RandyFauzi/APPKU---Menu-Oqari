<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ActivityLogs\ListActivityLogs;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\MediaService;
use App\Services\Receipt\ReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    private function logActivity($action, $description)
    {
        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'shop_id' => Auth::user()->shop_id,
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => request()->ip(),
            ]);
        }
    }

    public function getLiveOrders()
    {
        $shopId = auth()->user()->shop_id;
        if (! $shopId) {
            return response()->json([]);
        }

        $orders = Order::where('shop_id', $shopId)
            ->with(['items.product', 'table'])
            ->orderBy('created_at', 'desc')
            ->limit(150)
            ->get();

        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::where('shop_id', Auth::user()->shop_id)->findOrFail($orderId);
        $oldStatus = $order->order_status;

        $newStatus = $request->status;
        // Translate legacy status to new schema if needed
        $statusMap = [
            'Masuk' => 'CONFIRMED',
            'In Progress' => 'PREPARING',
            'Ready' => 'READY',
            'Completed' => 'COMPLETED',
            'Dibatalkan' => 'CANCELLED',
        ];
        if (array_key_exists($newStatus, $statusMap)) {
            $newStatus = $statusMap[$newStatus];
        }

        $order->order_status = $newStatus;
        $order->save();

        if (class_exists(ActivityLog::class)) {
            $desc = in_array($newStatus, ['Batal', 'CANCELLED'])
                ? 'Membatalkan pesanan #'.$order->id
                : 'Mengubah status pesanan #'.$order->id.' dari '.$oldStatus.' menjadi '.$newStatus;

            ActivityLog::create([
                'shop_id' => auth()->user()->shop_id,
                'user_id' => auth()->id(),
                'action' => 'update_order_status',
                'description' => $desc,
                'ip_address' => request()->ip(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function printOrder($orderId)
    {
        $order = Order::with('items.product', 'table', 'shop')->where('shop_id', Auth::user()->shop_id)->findOrFail($orderId);

        // Ensure the logged in user belongs to the same shop
        if (auth()->user()->shop_id !== $order->shop_id) {
            abort(403, 'Unauthorized');
        }

        $receiptData = app(ReceiptService::class)->build($order);
        $shop = $order->shop;

        return view('receipts.thermal', compact('receiptData', 'shop'));
    }

    public function deleteMenu($id)
    {
        $user = Auth::user();
        $menu = Product::where('shop_id', $user->shop_id)->where('id', $id)->first();
        if ($menu) {
            $menuName = $menu->name;
            $menu->delete();

            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'shop_id' => $user->shop_id,
                    'user_id' => $user->id,
                    'action' => 'delete_menu',
                    'description' => 'Menghapus menu: '.$menuName,
                    'ip_address' => request()->ip(),
                ]);
            }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Menu not found'], 404);
    }

    public function toggleMenuStatus(Request $request, $menuId)
    {
        $menu = Product::where('shop_id', Auth::user()->shop_id)->findOrFail($menuId);
        $menu->is_sold_out = ! $menu->is_sold_out;
        $menu->save();

        return response()->json(['success' => true, 'is_sold_out' => $menu->is_sold_out]);
    }

    public function saveMenu(Request $request)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        if ($request->has('recipes')) {
            $recipes = $request->input('recipes');
            if (is_string($recipes)) {
                $recipes = json_decode($recipes, true);
            }
            if (is_array($recipes)) {
                $filteredRecipes = array_values(array_filter($recipes, function ($item) {
                    return is_array($item) && ! empty($item['raw_material_id']);
                }));
                $request->merge(['recipes' => $filteredRecipes]);
            }
            if (! empty($request->input('recipes'))) {
                $request->validate([
                    'recipes' => ['array'],
                    'recipes.*.raw_material_id' => ['required', 'integer', 'distinct', Rule::exists('raw_materials', 'id')->where('shop_id', $shopId)->whereNull('deleted_at')],
                    'recipes.*.amount' => ['required', 'numeric', 'min:0.000001', 'max:999999.99'],
                ], [
                    'recipes.*.raw_material_id.required' => 'Bahan baku pada baris resep wajib dipilih.',
                    'recipes.*.raw_material_id.integer' => 'Bahan baku yang dipilih tidak valid.',
                    'recipes.*.raw_material_id.distinct' => 'Bahan baku tidak boleh dipilih ganda.',
                    'recipes.*.raw_material_id.exists' => 'Bahan baku tidak ditemukan di inventori toko.',
                    'recipes.*.amount.required' => 'Jumlah takaran bahan wajib diisi.',
                    'recipes.*.amount.min' => 'Jumlah takaran minimal 0.000001.',
                ]);
            }
        }
        if ($request->filled('id')) {
            Product::where('shop_id', $shopId)->findOrFail($request->input('id'));
        }

        $categoryId = null;
        $categoryName = null;

        if ($request->categoryId) {
            if (is_numeric($request->categoryId)) {
                $categoryId = (int) $request->categoryId;
                $cat = Category::where('shop_id', $shopId)->find($categoryId);
                if ($cat) {
                    $categoryName = $cat->name;
                }
            } else {
                // In case string name was passed
                $cat = Category::where('shop_id', $shopId)->where('name', $request->categoryId)->first();
                if ($cat) {
                    $categoryId = $cat->id;
                    $categoryName = $cat->name;
                } else {
                    $categoryName = $request->categoryId;
                }
            }
        }

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $categoryId,
            'category_name' => $categoryName,
            'description' => $request->desc,
        ];

        if ($request->has('cogs')) {
            $data['cogs'] = $request->cogs;
        }

        // Ensure we have a product ID for the directory
        $productId = $request->id ?: Str::uuid()->toString();

        if ($request->hasFile('image')) {
            $data['image_path'] = app(MediaService::class)
                ->storeProductImage($request->file('image'), $shopId, $productId);
        }

        $menu = Product::updateOrCreate(
            ['id' => $request->id, 'shop_id' => $shopId],
            $data
        );

        // Simpan resep bahan baku jika dikirim
        if ($request->has('recipes')) {
            $recipes = is_string($request->recipes) ? json_decode($request->recipes, true) : $request->recipes;
            if (is_array($recipes)) {
                ProductRecipe::where('product_id', $menu->id)->delete();
                foreach ($recipes as $r) {
                    if (! empty($r['raw_material_id']) && ! empty($r['amount'])) {
                        ProductRecipe::create([
                            'product_id' => $menu->id,
                            'raw_material_id' => $r['raw_material_id'],
                            'amount' => $r['amount'],
                        ]);
                    }
                }
                app(InventoryService::class)->syncProductHpp($menu);
                $menu->refresh();
            }
        }

        $menu->load(['category', 'recipes.rawMaterial']);

        $duplicateExists = Product::where('shop_id', $shopId)
            ->where('name', $request->name)
            ->where('id', '!=', $menu->id)
            ->exists();

        return response()->json([
            'success' => true,
            'menu' => $menu,
            'has_duplicate_name' => $duplicateExists,
        ]);
    }

    public function saveCategory(Request $request)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $trimmedName = trim($request->name);

        // Check if category already exists for this shop (case-insensitive)
        $existing = Category::where('shop_id', $shopId)
            ->whereRaw('LOWER(name) = ?', [strtolower($trimmedName)])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'category' => $existing,
                'already_exists' => true,
                'message' => 'Kategori sudah ada di toko Anda.',
            ]);
        }

        // Pastikan slug juga unik per shop_id
        $baseSlug = Str::slug($trimmedName);
        $slug = $baseSlug ?: 'kategori';
        $counter = 1;
        while (Category::where('shop_id', $shopId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $category = Category::create([
            'shop_id' => $shopId,
            'name' => $trimmedName,
            'slug' => $slug,
            'sort_order' => Category::where('shop_id', $shopId)->count() + 1,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $trimmed = trim($request->name);
        $category = Category::where('shop_id', $shopId)->findOrFail($id);

        // Check unique for other categories in the same shop (case-insensitive)
        $duplicate = Category::where('shop_id', $shopId)
            ->where('id', '!=', $category->id)
            ->whereRaw('LOWER(name) = ?', [strtolower($trimmed)])
            ->first();

        if ($duplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori dengan nama "'.$trimmed.'" sudah ada di toko Anda.',
            ], 422);
        }

        $oldName = $category->name;
        $category->name = $trimmed;
        $baseSlug = Str::slug($trimmed) ?: 'kategori';
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('shop_id', $shopId)->where('id', '!=', $category->id)->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        $category->slug = $slug;
        $category->save();

        // Update category_name in products for consistency
        Product::where('shop_id', $shopId)->where('category_id', $category->id)->update([
            'category_name' => $category->name,
        ]);

        $this->logActivity('category_updated', 'Mengubah kategori "'.$oldName.'" menjadi "'.$category->name.'"');

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Kategori berhasil diperbarui!',
        ]);
    }

    public function deleteCategory($id)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $category = Category::where('shop_id', $shopId)->findOrFail($id);
        $categoryName = $category->name;

        // Find fallback category if exists
        $fallbackCat = Category::where('shop_id', $shopId)->where('id', '!=', $category->id)->first();

        Product::where('shop_id', $shopId)->where('category_id', $category->id)->update([
            'category_id' => $fallbackCat ? $fallbackCat->id : null,
            'category_name' => $fallbackCat ? $fallbackCat->name : 'Uncategorized',
        ]);

        $category->delete();

        $this->logActivity('category_deleted', 'Menghapus kategori: '.$categoryName);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus!',
            'fallback_category_id' => $fallbackCat ? $fallbackCat->id : null,
            'fallback_category_name' => $fallbackCat ? $fallbackCat->name : 'Uncategorized',
        ]);
    }

    public function saveMenuBulk(Request $request)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        // Validate all uploaded images if they exist
        foreach ($request->allFiles() as $key => $file) {
            if (Str::startsWith($key, 'images.')) {
                $request->validate([
                    $key => 'mimes:jpeg,png,jpg,webp|max:5120',
                ]);
            }
        }

        $items = $request->input('items', []);
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada item menu yang dikirim.'], 422);
        }

        $savedMenus = DB::transaction(function () use ($items, $shopId, $request) {
            $saved = [];

            foreach ($items as $index => $itemData) {
                if (empty($itemData['name']) || ! isset($itemData['price'])) {
                    continue; // Lewati item yang kosong
                }

                $catId = null;
                $catName = null;
                $rawCat = $itemData['category_id'] ?? $itemData['category_name'] ?? null;
                if ($rawCat) {
                    if (is_numeric($rawCat)) {
                        $cat = Category::where('shop_id', $shopId)->find((int) $rawCat);
                        if ($cat) {
                            $catId = $cat->id;
                            $catName = $cat->name;
                        }
                    } else {
                        $trimmed = trim($rawCat);
                        if ($trimmed !== '') {
                            $cat = Category::firstOrCreate(
                                ['shop_id' => $shopId, 'name' => $trimmed],
                                ['shop_id' => $shopId, 'name' => $trimmed]
                            );
                            $catId = $cat->id;
                            $catName = $cat->name;
                        }
                    }
                }

                if (! $catName) {
                    $defaultCat = Category::where('shop_id', $shopId)->first();
                    if ($defaultCat) {
                        $catId = $defaultCat->id;
                        $catName = $defaultCat->name;
                    } else {
                        $cat = Category::firstOrCreate(
                            ['shop_id' => $shopId, 'name' => 'Coffee'],
                            ['shop_id' => $shopId, 'name' => 'Coffee']
                        );
                        $catId = $cat->id;
                        $catName = $cat->name;
                    }
                }

                $productData = [
                    'name' => $itemData['name'],
                    'price' => (int) $itemData['price'],
                    'category_id' => $catId,
                    'category_name' => $catName,
                    'description' => $itemData['description'] ?? ($itemData['desc'] ?? null),
                ];

                $menu = null;
                if (! empty($itemData['id']) && is_numeric($itemData['id']) && (int) $itemData['id'] > 0) {
                    $menu = Product::where('id', (int) $itemData['id'])->where('shop_id', $shopId)->first();
                }

                if ($request->hasFile("images.{$index}")) {
                    $file = $request->file("images.{$index}");

                    // If update, clean up old file
                    if ($menu && $menu->image_path) {
                        Storage::disk('public')->delete($menu->image_path);
                    }

                    $productIdDir = $menu ? $menu->id : Str::uuid()->toString();

                    $path = app(MediaService::class)
                        ->storeProductImage($file, $shopId, $productIdDir);

                    $productData['image_path'] = $path;
                }

                if ($menu) {
                    $menu->update($productData);
                    $saved[] = $menu;
                } else {
                    $productData['shop_id'] = $shopId;
                    $menu = Product::create($productData);
                    $saved[] = $menu;
                }
            }

            return $saved;
        });

        if (count($savedMenus) > 0) {
            $this->logActivity('menu_bulk_saved', 'Menyimpan '.count($savedMenus).' item menu.');
        }

        $savedIds = collect($savedMenus)->pluck('id');
        $freshMenus = Product::whereIn('id', $savedIds)
            ->with(['recipes.rawMaterial', 'category'])
            ->get();

        $allCategories = Category::where('shop_id', $shopId)->get();

        return response()->json([
            'success' => true,
            'menus' => $freshMenus,
            'categories' => $allCategories,
        ]);
    }

    public function saveSettings(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|mimes:jpeg,png,jpg,webp|max:2048',
            'theme_style' => 'nullable|string|in:grid,list',
            'is_open' => 'nullable|boolean',
            'slogan' => 'nullable|string|max:255',
            'font_family' => 'nullable|string|max:50',
            'title_font_family' => 'nullable|string|max:50',
            'receipt_header' => 'nullable|string|max:500',
            'receipt_footer' => 'nullable|string|max:500',
            'instagram_link' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:50',
            'maps_link' => 'nullable|string|max:500',
            'operating_hours' => 'nullable|string',
            'is_banner_active' => 'nullable|boolean',
            'banner_0' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
            'banner_1' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
            'banner_2' => 'nullable|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $shop = Shop::findOrFail($shopId);

        $shop->name = $request->name;

        if ($request->has('theme_style')) {
            $shop->theme_style = $request->theme_style;
        }
        if ($request->has('is_open')) {
            $shop->is_open = $request->boolean('is_open');
        }
        if ($request->has('slogan')) {
            $shop->slogan = $request->slogan;
        }
        if ($request->has('font_family')) {
            $shop->font_family = $request->font_family;
        }
        if ($request->has('title_font_family')) {
            $shop->title_font_family = $request->title_font_family;
        }
        if ($request->has('receipt_header')) {
            $shop->receipt_header = $request->receipt_header;
        }
        if ($request->has('receipt_footer')) {
            $shop->receipt_footer = $request->receipt_footer;
        }
        if ($request->has('instagram_link')) {
            $shop->instagram_link = $request->instagram_link;
        }
        if ($request->has('whatsapp_number')) {
            $shop->whatsapp_number = $request->whatsapp_number;
        }
        if ($request->has('maps_link')) {
            $shop->maps_link = $request->maps_link;
        }
        if ($request->has('is_banner_active')) {
            $shop->is_banner_active = $request->boolean('is_banner_active');
        }

        if ($request->has('operating_hours')) {
            $val = $request->operating_hours;
            $shop->operating_hours = is_array($val) ? $val : json_decode($val, true);
        }

        if ($request->filled('primary_color')) {
            $shop->primary_color = $request->primary_color;
        }

        if ($request->hasFile('logo')) {
            // Delete old file if exists
            if ($shop->logo_path) {
                Storage::disk('public')->delete($shop->logo_path);
            }

            $path = app(MediaService::class)
                ->storeShopBranding($request->file('logo'), $shopId, 'logo');
            $shop->logo_path = $path;
        }

        // Banners
        $banners = [];
        $existingBanners = $shop->banner_paths ?? [];

        for ($i = 0; $i < 3; $i++) {
            if ($request->hasFile("banner_{$i}")) {
                $path = app(MediaService::class)
                    ->storeShopBranding($request->file("banner_{$i}"), $shopId, "banner_{$i}");
                $banners[] = $path;
            } elseif ($request->filled("existing_banner_{$i}")) {
                $existingUrl = $request->input("existing_banner_{$i}");
                $storageUrl = rtrim(Storage::disk(env('MEDIA_DISK', 'public'))->url(''), '/');
                if ($storageUrl) {
                    while (Str::startsWith($existingUrl, $storageUrl)) {
                        $existingUrl = substr($existingUrl, strlen($storageUrl));
                    }
                }
                $existingUrl = ltrim($existingUrl, '/');
                $banners[] = $existingUrl;
            }
        }

        // Clean up orphan banners
        foreach ($existingBanners as $oldBanner) {
            if (! in_array($oldBanner, $banners)) {
                Storage::disk('public')->delete($oldBanner);
            }
        }

        $shop->banner_paths = $banners;

        $shop->save();

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function saveOperatingHours(Request $request)
    {
        $request->validate([
            'operating_hours' => 'required',
        ]);

        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            return response()->json(['success' => false, 'message' => 'Toko tidak ditemukan'], 403);
        }

        $shop = Shop::findOrFail($shopId);
        $val = $request->input('operating_hours');
        $shop->operating_hours = is_array($val) ? $val : json_decode($val, true);
        $shop->save();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal operasional berhasil disimpan!',
            'operating_hours' => $shop->operating_hours,
        ]);
    }

    public function saveTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        $table = new Table;
        $table->shop_id = $user->shop_id;
        $table->name = $request->name;
        $table->public_token = strtolower(Str::random(8));
        $table->save();

        return response()->json(['success' => true, 'table' => $table]);
    }

    public function updateTableQR(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $table = Table::where('shop_id', $user->shop_id)->where('name', $request->name)->first();

        if ($table) {
            $table->public_token = strtolower(Str::random(8));
            $table->save();

            return response()->json(['success' => true, 'table' => $table]);
        }

        return response()->json(['success' => false, 'message' => 'Table not found'], 404);
    }

    public function getLogs(Request $request, ListActivityLogs $listLogs): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        return response()->json($listLogs->execute($request->user(), $filters));
    }
}
