<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashRegisterSession;
use App\Models\Category;
use App\Models\CrewShift;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        // Eager load everything needed for tabs
        $menuItems = Product::where('shop_id', $shop->id)->with('category', 'variants', 'modifierGroups.modifiers')->get();
        $categories = Category::where('shop_id', $shop->id)->orderBy('sort_order')->get();
        $orders = Order::where('shop_id', $shop->id)->with('items.product', 'table')->orderBy('created_at', 'desc')->limit(150)->get();
        $tables = Table::where('shop_id', $shop->id)->get();
        $users = User::where('shop_id', $shop->id)->get();
        $activeSession = CashRegisterSession::where('user_id', $user->id)->where('status', 'OPEN')->first();

        // Analytics Calculations (Real Data)
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $ordersToday = Order::where('shop_id', $shop->id)->whereDate('created_at', $today)->count();
        $ordersYesterday = Order::where('shop_id', $shop->id)->whereDate('created_at', $yesterday)->count();
        $ordersChange = $ordersYesterday > 0 ? round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100) : 0;

        $revenueToday = Order::where('shop_id', $shop->id)->where('payment_status', 'PAID')->whereDate('created_at', $today)->sum('grand_total');
        $revenueYesterday = Order::where('shop_id', $shop->id)->where('payment_status', 'PAID')->whereDate('created_at', $yesterday)->sum('grand_total');
        $revenueChange = $revenueYesterday > 0 ? round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100) : 0;

        // Top Product
        $topItem = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $shop->id)
            ->whereDate('orders.created_at', $today)
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_sold')
            ->first();

        // Sales Trend (Hourly for Today)
        $hourlySales = [];
        for ($i = 8; $i <= 22; $i += 2) {
            $count = Order::where('shop_id', $shop->id)
                ->where('payment_status', 'PAID')
                ->whereDate('created_at', $today)
                ->whereRaw('HOUR(created_at) >= ? AND HOUR(created_at) < ?', [$i, $i + 2])
                ->count();
            $hourlySales[] = $count;
        }

        $analytics = [
            'orders' => $ordersToday,
            'ordersChange' => $ordersChange,
            'revenue' => $revenueToday,
            'revenueChange' => $revenueChange,
            'topProduct' => $topItem ? [
                'name' => $topItem->product_name,
                'sold' => (int) $topItem->total_sold,
                'change' => 0, // Can implement yesterday comparison later
            ] : [
                'name' => 'Belum ada',
                'sold' => 0,
                'change' => 0,
            ],
            'returningCustomers' => 15, // Dummy for now as we don't have full CRM
            'newCustomersPct' => 85,
            'hourlySales' => $hourlySales,
        ];

        return view('Admin.Dashboard.dashboard', compact('shop', 'orders', 'menuItems', 'categories', 'tables', 'users', 'analytics', 'activeSession'));
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
        $oldStatus = $order->order_status ?? $order->status;

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

        return view('Admin.orders.print', compact('order'));
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

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->categoryId ?: null,
            'description' => $request->desc,
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }

        $menu = Product::updateOrCreate(
            ['id' => $request->id, 'shop_id' => $shopId],
            $data
        );

        $menu->load('category');

        return response()->json(['success' => true, 'menu' => $menu]);
    }

    public function saveCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $user = Auth::user();
        $shopId = $user->shop_id;
        if (! $shopId) {
            abort(403, 'Tindakan ditolak. Akun Anda tidak terikat dengan toko manapun.');
        }

        $category = Category::create([
            'shop_id' => $shopId,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => Category::where('shop_id', $shopId)->count() + 1,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
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
            if (\Illuminate\Support\Str::startsWith($key, 'images.')) {
                $request->validate([
                    $key => 'mimes:jpeg,png,jpg,webp|max:5120'
                ]);
            }
        }

        $items = $request->input('items', []);
        $savedMenus = [];

        foreach ($items as $index => $itemData) {
            $productData = [
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'category_name' => $itemData['category_name'] ?? 'Uncategorized',
                'description' => $itemData['description'] ?? null,
            ];

            $menu = null;
            if (! empty($itemData['id'])) {
                $menu = Product::where('id', $itemData['id'])->where('shop_id', $shopId)->first();
            }

            if ($request->hasFile("images.{$index}")) {
                $file = $request->file("images.{$index}");
                $extension = $file->getClientOriginalExtension();
                
                // If update, clean up old file
                if ($menu && $menu->image_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->image_path);
                }

                $productIdDir = $menu ? $menu->id : \Illuminate\Support\Str::uuid()->toString();
                $filename = 'image_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
                
                $path = $file->storeAs("media/shops/{$shopId}/products/{$productIdDir}", $filename, 'public');
                $productData['image_path'] = $path;
            }

            // Validasi sederhana
            if (empty($itemData['name']) || empty($itemData['price'])) {
                continue; // Lewati item yang kosong
            }

            if ($menu) {
                $menu->update($productData);
                $savedMenus[] = $menu;
            } else {
                $productData['shop_id'] = $shopId;
                $menu = Product::create($productData);
                
                // If we used a UUID dir for new product, we could move it to the real ID, 
                // but the DB image_path already contains the UUID path which is fine, 
                // or we can rename the directory. For now, UUID is perfectly fine.
                
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
            'logo' => 'nullable|mimes:jpeg,png,jpg,webp|max:2048',
            'theme_style' => 'nullable|string|in:grid,list',
            'is_open' => 'nullable|boolean',
            'slogan' => 'nullable|string|max:255',
            'font_family' => 'nullable|string|max:50',
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
            // Delete old file if exists
            if ($shop->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($shop->logo_path);
            }
            
            // Generate random filename
            $extension = $request->file('logo')->getClientOriginalExtension();
            $filename = 'logo_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
            
            $path = $request->file('logo')->storeAs("media/shops/{$shopId}/branding", $filename, 'public');
            $shop->logo_path = $path;
        }

        // Banners
        $banners = [];
        $existingBanners = $shop->banner_paths ?? [];
        
        for ($i = 0; $i < 3; $i++) {
            if ($request->hasFile("banner_{$i}")) {
                $extension = $request->file("banner_{$i}")->getClientOriginalExtension();
                $filename = 'banner_' . $i . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
                
                $path = $request->file("banner_{$i}")->storeAs("media/shops/{$shopId}/branding", $filename, 'public');
                $banners[] = $path;
            } elseif ($request->filled("existing_banner_{$i}")) {
                $banners[] = $request->input("existing_banner_{$i}");
            }
        }
        
        // Clean up orphan banners
        foreach ($existingBanners as $oldBanner) {
            if (!in_array($oldBanner, $banners)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldBanner);
            }
        }

        $shop->banner_paths = $banners;

        $shop->save();

        return response()->json(['success' => true, 'shop' => $shop]);
    }

    public function saveCrew(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:manager,cashier,kitchen,barista',
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
            'role' => 'required|string|in:manager,cashier,kitchen,barista',
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
                Rule::exists('users', 'id')->where('shop_id', $user->shop_id),
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
