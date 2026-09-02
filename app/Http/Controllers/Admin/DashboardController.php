<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CrewShift;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function index(Request $request)
    {
        if (Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        $user = Auth::user();

        // JIKA USER BARU DAFTAR & BELUM PUNYA SHOP
        if (! $user->shop_id) {
            $newShop = Shop::create([
                'name' => $user->name.' Shop',
                'slug' => Str::slug($user->name.'-'.uniqid()),
                'primary_color' => '#1E5A7A', // Default color
            ]);

            $user->update([
                'shop_id' => $newShop->id,
                'role' => 'owner', // Jadikan dia owner dari tokonya sendiri
            ]);
        }

        $shopId = $user->shop_id;
        $shop = Shop::find($shopId);

        $menuItems = Product::where('shop_id', $shop->id)->get();
        $orders = Order::where('shop_id', $shop->id)->with('items.product', 'table')->orderBy('created_at', 'desc')->get();
        $tables = Table::where('shop_id', $shop->id)->get();
        $users = User::where('shop_id', $shop->id)->get();

        return view('Admin.Dashboard.dashboard', compact('shop', 'orders', 'menuItems', 'tables', 'users'));
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
            ->get();

        return response()->json($orders);
    }

    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::where('shop_id', Auth::user()->shop_id)->findOrFail($orderId);
        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        if (class_exists(ActivityLog::class)) {
            $desc = in_array($request->status, ['Batal', 'CANCELLED'])
                ? 'Membatalkan pesanan #'.$order->id
                : 'Mengubah status pesanan #'.$order->id.' dari '.$oldStatus.' menjadi '.$request->status;

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

        return view('admin.orders.print', compact('order'));
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
        if (!$shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_name' => $request->categoryId,
            'description' => $request->desc,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Product::updateOrCreate(
            ['id' => $request->id, 'shop_id' => $shopId],
            $data
        );

        return response()->json(['success' => true, 'menu' => $menu]);
    }

    public function saveMenuBulk(Request $request)
    {
        $user = Auth::user();
        $shopId = $user->shop_id;
        if (!$shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

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
            if (! empty($itemData['id'])) {
                $menu = Product::where('id', $itemData['id'])->where('shop_id', $shopId)->first();
                if ($menu) {
                    $menu->update($productData);
                    $savedMenus[] = $menu;
                }
            } else {
                $productData['shop_id'] = $shopId;
                $menu = Product::create($productData);
                $savedMenus[] = $menu;
            }
        }

        return response()->json(['success' => true, 'menus' => $savedMenus]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function saveSettings(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'theme_style' => 'nullable|string|in:grid,list',
            'is_open' => 'nullable|boolean',
            'slogan' => 'nullable|string|max:255',
            'font_family' => 'nullable|string|max:50',
            'instagram_link' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:50',
            'maps_link' => 'nullable|string|max:500',
            'operating_hours' => 'nullable|string',
            'is_banner_active' => 'nullable|boolean',
            'banner_0' => 'nullable|image|max:5120',
            'banner_1' => 'nullable|image|max:5120',
            'banner_2' => 'nullable|image|max:5120',
        ]);

        $user = Auth::user();
        $shopId = $user->shop_id;
        if (!$shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        if (! $user->shop_id) {
            $user->update(['shop_id' => $shopId]);
        }

        $shop = Shop::find($shopId);
        if (! $shop) {
            return response()->json(['success' => false, 'message' => 'Shop not found.']);
        }

        $shop->name = $request->name;
        $shop->slug = Str::slug($request->slug);

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
            $shop->operating_hours = json_decode($request->operating_hours, true);
        }

        if ($request->filled('primary_color')) {
            $shop->primary_color = $request->primary_color;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('shops', 'public');
            $shop->logo_url = $path;
        }

        // Banners
        $banners = [];
        for ($i = 0; $i < 3; $i++) {
            if ($request->hasFile("banner_{$i}")) {
                $banners[] = $request->file("banner_{$i}")->store('shops/banners', 'public');
            } elseif ($request->filled("existing_banner_{$i}")) {
                $banners[] = $request->input("existing_banner_{$i}");
            }
        }
        $shop->banners = $banners;

        $shop->save();

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function saveCrew(Request $request)
    {
        

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:manager,cashier,kitchen,waiter',
        ]);

        $user = Auth::user();

        $newCrew = new User;
        $newCrew->name = $request->name;
        $newCrew->email = $request->email;
        $newCrew->password = Hash::make($request->password);
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
            'role' => 'required|string|in:manager,cashier,kitchen,waiter',
        ]);

        $currentUser = Auth::user();
        $crew = User::where('shop_id', $currentUser->shop_id)->where('id', $id)->first();

        if (! $crew) {
            return response()->json(['success' => false, 'message' => 'Crew not found'], 404);
        }

        // Prevent changing own role or deleting own account
        if ($crew->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot modify yourself'], 403);
        }

        $crew->name = $request->name;
        $crew->email = $request->email;
        if ($request->filled('password')) {
            $crew->password = Hash::make($request->password);
        }
        $crew->role = $request->role;
        $crew->save();

        return response()->json(['success' => true, 'user' => $crew]);
    }

    public function deleteCrew($id)
    {
        
        $user = Auth::user();
        $crew = User::where('shop_id', $user->shop_id)->where('id', $id)->first();

        if ($crew && $crew->id !== Auth::id()) {
            $crew->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Cannot delete this user']);
    }

    public function saveTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'qr_code_url' => 'required|string',
        ]);

        $user = Auth::user();

        $table = new Table;
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
            'qr_code_url' => 'required|string',
        ]);

        $user = Auth::user();
        $table = Table::where('shop_id', $user->shop_id)->where('name', $request->name)->first();

        if ($table) {
            $table->qr_code_url = $request->qr_code_url;
            $table->save();

            return response()->json(['success' => true, 'table' => $table]);
        }

        return response()->json(['success' => false, 'message' => 'Table not found']);
    }

    public function getLogs()
    {
        $user = Auth::user();
        
        $logs = ActivityLog::with('user')
            ->where('shop_id', $user->shop_id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->name : 'Sistem',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->format('d M Y H:i:s'),
                ];
            });

        return response()->json($logs);
    }

    public function getShifts()
    {
        $user = Auth::user();
        $shifts = CrewShift::with('user')
            ->whereHas('user', function ($q) use ($user) {
                $q->where('shop_id', $user->shop_id);
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'user_id' => $s->user_id,
                    'user_name' => $s->user ? $s->user->name : '-',
                    'date' => $s->date->format('Y-m-d'),
                    'start_time' => Carbon::parse($s->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($s->end_time)->format('H:i'),
                    'notes' => $s->notes,
                ];
            });

        return response()->json($shifts);
    }

    public function saveShift(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'user_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('shop_id', $user->shop_id)
            ],
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'shop_id' => $user->shop_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
        ];

        if ($request->filled('id')) {
            $shift = CrewShift::where('shop_id', $user->shop_id)->findOrFail($request->id);
            $shift->update($data);
        } else {
            CrewShift::create($data);
        }

        return response()->json(['success' => true]);
    }

    }

