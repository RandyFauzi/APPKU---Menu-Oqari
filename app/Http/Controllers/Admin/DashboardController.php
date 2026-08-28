<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $shopId = $user->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        if (!$user->shop_id) $user->update(['shop_id' => $shopId]);
        $shop = \App\Models\Shop::find($shopId);

        $menuItems = \App\Models\Product::where('shop_id', $shop->id)->get();
        $orders = \App\Models\Order::where('shop_id', $shop->id)->with('items.product', 'table')->orderBy('created_at', 'desc')->get();
        $tables = \App\Models\Table::where('shop_id', $shop->id)->get();
        $users = \App\Models\User::where('shop_id', $shop->id)->get();

        return view('Admin.Dashboard.dashboard', compact('shop', 'menuItems', 'orders', 'tables', 'users'));
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = \App\Models\Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();
        return response()->json(['success' => true]);
    }

    public function deleteMenu($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $menu = \App\Models\Product::where('shop_id', $user->shop_id)->where('id', $id)->first();
        if ($menu) {
            $menu->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Menu not found'], 404);
    }

    public function toggleMenuStatus(Request $request, $menuId)
    {
        $menu = \App\Models\Product::findOrFail($menuId);
        $menu->is_sold_out = !$menu->is_sold_out;
        $menu->save();
        return response()->json(['success' => true, 'is_sold_out' => $menu->is_sold_out]);
    }

        public function saveMenu(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $shopId = $user->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_name' => $request->categoryId,
            'description' => $request->desc,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menus', 'public');
        }

        $menu = \App\Models\Product::updateOrCreate(
            ['id' => $request->id, 'shop_id' => $shopId],
            $data
        );
        return response()->json(['success' => true, 'menu' => $menu]);
    }

    public function saveMenuBulk(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $shopId = $user->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;

        $items = $request->input('items', []);
        $savedMenus = [];

        foreach ($items as $index => $itemData) {
            // Check if there's an image file for this specific index
            $imagePath = null;
            if ($request->hasFile("images.{$index}")) {
                $imagePath = $request->file("images.{$index}")->store('menus', 'public');
            }

            // Validasi sederhana
            if (empty($itemData['name']) || empty($itemData['price'])) {
                continue; // Lewati item yang kosong
            }

            $productData = [
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'category_name' => $itemData['category_name'] ?? 'Uncategorized',
                'description' => $itemData['description'] ?? null,
            ];

            if ($imagePath) {
                $productData['image_url'] = $imagePath;
            }

            // Jika ada id, berarti update, jika tidak, create
            if (!empty($itemData['id'])) {
                $menu = \App\Models\Product::where('id', $itemData['id'])->where('shop_id', $shopId)->first();
                if ($menu) {
                    $menu->update($productData);
                    $savedMenus[] = $menu;
                }
            } else {
                $productData['shop_id'] = $shopId;
                $menu = \App\Models\Product::create($productData);
                $savedMenus[] = $menu;
            }
        }

        return response()->json(['success' => true, 'menus' => $savedMenus]);
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $shopId = $user->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
        if (!$user->shop_id) {
            $user->update(['shop_id' => $shopId]);
        }

        $shop = \App\Models\Shop::find($shopId);
        if (!$shop) {
            return response()->json(['success' => false, 'message' => 'Shop not found.']);
        }

        $shop->name = $request->name;
        $shop->slug = \Illuminate\Support\Str::slug($request->slug);
        
        if ($request->filled('primary_color')) {
            $shop->primary_color = $request->primary_color;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $shop->logo_url = $path;
        }

        $shop->save();

        return response()->json([
            'success' => true,
            'logo_url' => $shop->logo_url
        ]);
    }
    public function saveCrew(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $newCrew = new \App\Models\User();
        $newCrew->name = $request->name;
        $newCrew->email = $request->email;
        $newCrew->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $newCrew->shop_id = $user->shop_id;
        $newCrew->role = $request->role;
        $newCrew->save();

        return response()->json(['success' => true, 'user' => $newCrew]);
    }

    public function updateCrew(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string'
        ]);

        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $crew = \App\Models\User::where('shop_id', $currentUser->shop_id)->where('id', $id)->first();
        
        if (!$crew) {
            return response()->json(['success' => false, 'message' => 'Crew not found'], 404);
        }

        // Prevent changing admin role or self-role logic if necessary
        if ($crew->role === 'admin' && $request->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot change admin role'], 403);
        }

        $crew->name = $request->name;
        $crew->email = $request->email;
        if ($request->filled('password')) {
            $crew->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $crew->role = $request->role;
        $crew->save();

        return response()->json(['success' => true, 'user' => $crew]);
    }

    public function deleteCrew($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $crew = \App\Models\User::where('shop_id', $user->shop_id)->where('id', $id)->first();
        
        if ($crew && $crew->role !== 'admin') {
            $crew->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot delete this user']);
    }
    public function saveTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'qr_code_url' => 'required|string'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $table = new \App\Models\Table();
        $table->shop_id = $user->shop_id;
        $table->name = $request->name;
        $table->qr_code_url = $request->qr_code_url;
        $table->save();

        return response()->json(['success' => true, 'table' => $table]);
    }

    public function updateTableQR(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'qr_code_url' => 'required|string'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $table = \App\Models\Table::where('shop_id', $user->shop_id)->where('name', $request->name)->first();
        
        if ($table) {
            $table->qr_code_url = $request->qr_code_url;
            $table->save();
            return response()->json(['success' => true, 'table' => $table]);
        }
        
        return response()->json(['success' => false, 'message' => 'Table not found']);
    }
}


