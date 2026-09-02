<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cek apakah ada sesi kasir yang aktif untuk user ini atau toko ini
        $activeSession = CashRegisterSession::where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        // Ambil produk dan kategorinya
        $products = Product::where('shop_id', $user->shop_id)
            ->with(['variants', 'modifierGroups.modifiers'])
            ->get();

        $registers = CashRegister::where('shop_id', $user->shop_id)->where('is_active', true)->get();

        return view('Admin.pos.index', compact('products', 'activeSession', 'registers'));
    }
}
