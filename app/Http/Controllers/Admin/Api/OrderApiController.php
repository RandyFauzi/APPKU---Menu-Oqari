<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (! $shopId) {
            return response()->json([]);
        }

        $filters = $request->validate(['per_page' => ['sometimes', 'integer', 'min:1', 'max:150'], 'page' => ['sometimes', 'integer', 'min:1']]);
        $perPage = (int) ($filters['per_page'] ?? 25);

        $orders = Order::where('shop_id', $shopId)
            ->with(['items.product', 'table'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($orders);
    }
}
