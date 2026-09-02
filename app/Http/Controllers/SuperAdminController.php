<?php

namespace App\Http\Controllers;

use App\Actions\SuperAdmin\ActivateShopAction;
use App\Actions\SuperAdmin\DeleteShopAction;
use App\Actions\SuperAdmin\SuspendShopAction;
use App\Models\Order;
use App\Models\Shop;
use App\Models\SuperAdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $since30d = now()->subDays(30);

        $totalShops = Shop::count();
        $activeShops = Shop::active()->count();
        $totalUsers = User::count();

        // Metrik dengan rentang waktu jelas, bukan count() mentah sepanjang masa.
        $ordersLast30d = Order::where('created_at', '>=', $since30d)->count();
        $revenueLast30d = Order::where('created_at', '>=', $since30d)->sum('total_price');

        $shops = Shop::withCount(['users', 'orders'])
            ->search($request->query('search'))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $users = User::with('shop')
            ->when($request->query('user_search'), function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15, ['*'], 'users_page')
            ->withQueryString();

        $recentActivity = SuperAdminAuditLog::with('actor')->latest()->take(10)->get();

        return view('SuperAdmin.dashboard', compact(
            'totalShops', 'activeShops', 'totalUsers',
            'ordersLast30d', 'revenueLast30d',
            'shops', 'users', 'recentActivity'
        ));
    }

    public function suspendShop(Request $request, Shop $shop, SuspendShopAction $action)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $action->execute($shop, $request->string('reason'));

        return back()->with('success', "{$shop->name} berhasil di-suspend.");
    }

    public function activateShop(Shop $shop, ActivateShopAction $action)
    {
        $action->execute($shop);

        return back()->with('success', "{$shop->name} berhasil diaktifkan kembali.");
    }

    public function deleteShop(Request $request, Shop $shop, DeleteShopAction $action)
    {
        $request->validate(['confirm_slug' => 'required|string']);

        try {
            $action->execute($shop, $request->string('confirm_slug'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->with('error', $e->getMessage());
        }

        return back()->with('success', 'Toko berhasil dihapus permanen.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak bisa menghapus super admin.');
        }

        SuperAdminAuditLog::record(
            action: 'user.deleted',
            targetType: 'User',
            targetId: $user->id,
            targetLabel: "{$user->name} ({$user->email})",
            meta: ['role' => $user->role, 'shop_id' => $user->shop_id],
        );

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
