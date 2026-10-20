<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KitchenController extends Controller
{
    public function index()
    {
        $shopId = Auth::user()->shop_id;

        $orders = Order::where('shop_id', $shopId)
            ->whereIn('order_status', ['CONFIRMED', 'PREPARING', 'READY'])
            ->with('items')
            ->latest()
            ->limit(150)
            ->get();

        $kitchenOrders = $orders->map(fn ($o) => [
            'id' => $o->id,
            'status' => $o->order_status,
            'time' => $o->created_at->format('H:i'),
            'items' => $o->items->map(fn ($i) => [
                'id' => $i->id,
                'name' => $i->product_name,
                'qty' => $i->quantity,
                'variant' => $i->variant_name,
                'modifiers' => $i->modifiers ?? [],
                'notes' => $i->notes,
            ]),
        ]);

        return view('Admin.kitchen.index', compact('orders', 'kitchenOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update-kitchen-status');

        $shopId = Auth::user()->shop_id;

        if ($order->shop_id !== $shopId) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:PREPARING,READY,COMPLETED'],
        ]);

        $order->update(['order_status' => $validated['status']]);

        return response()->json(['success' => true, 'status' => $order->order_status]);
    }
}
