<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function show(Request $request, $slug)
    {
        $shop = \App\Models\Shop::where('slug', $slug)->firstOrFail();
        $menuItems = \App\Models\Product::where('shop_id', $shop->id)->where('is_sold_out', false)->get();
        $table = $request->query('table');
        return view('shop.menu', compact('shop', 'menuItems', 'table'));
    }

    public function cart($slug)
    {
        $shop = \App\Models\Shop::where('slug', $slug)->firstOrFail();
        return view('shop.cart', compact('shop'));
    }

    public function tracking($slug)
    {
        $shop = \App\Models\Shop::where('slug', $slug)->firstOrFail();
        return view('shop.tracking', compact('shop'));
    }

    public function submitOrder(Request $request, $slug)
    {
        $shop = \App\Models\Shop::where('slug', $slug)->firstOrFail();

        $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);

        $total = 0;
        $orderItems = [];

        // Hitung total dan verifikasi produk
        foreach ($request->items as $item) {
            $product = \App\Models\Product::where('id', $item['id'])
                                          ->where('shop_id', $shop->id)
                                          ->first();
            
            if (!$product) continue;

            $subtotal = $product->price * $item['qty'];
            $total += $subtotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['qty'],
                'price' => $product->price,
                'notes' => $item['notes'] ?? null,
            ];
        }

        if (empty($orderItems)) {
            return response()->json(['success' => false, 'message' => 'Items are invalid.'], 400);
        }

        // Resolve Table
        $tableModel = \App\Models\Table::where('shop_id', $shop->id)->where('name', $request->table_id)->first();
        if (!$tableModel) {
            $tableModel = \App\Models\Table::create(['shop_id' => $shop->id, 'name' => $request->table_id]);
        }

        // Generate Order Number (if you want to save it, but there's no order_number column in your schema. I will put it in customer_name or skip it)

        // Create Order
        $order = \App\Models\Order::create([
            'shop_id' => $shop->id,
            'table_id' => $tableModel->id,
            'customer_name' => $request->customer_name,
            'total_price' => $total,
            'status' => 'process'
        ]);

        // Simpan Items
        foreach ($orderItems as $orderItem) {
            $orderItem['order_id'] = $order->id;
            \App\Models\OrderItem::create($orderItem);
        }

        // Load relasi agar return datanya lengkap (untuk socket/response)
        $order->load('items.product');

        return response()->json([
            'success' => true, 
            'message' => 'Order placed successfully',
            'order' => $order
        ]);
    }
}
