<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shop;
use App\Models\Order;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $totalShops = Shop::count();
        $totalUsers = User::count();
        $totalOrders = Order::count();
        
        $shops = Shop::withCount('users', 'orders')->latest()->get();
        $users = User::with('shop')->latest()->get();

        return view('SuperAdmin.dashboard', compact('totalShops', 'totalUsers', 'totalOrders', 'shops', 'users'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'superadmin') {
            return back()->with('error', 'Tidak bisa menghapus super admin.');
        }
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function deleteShop($id)
    {
        Shop::findOrFail($id)->delete();
        return back()->with('success', 'Toko berhasil dihapus.');
    }
}
