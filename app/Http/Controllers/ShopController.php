<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function show(Request $request, $slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();
        $menuItems = Product::where('shop_id', $shop->id)->where('is_sold_out', false)->get();
        $table = $request->query('table');

        return view('shop.menu', compact('shop', 'menuItems', 'table'));
    }

    public function cart($slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        return view('shop.cart', compact('shop'));
    }

    public function tracking($slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        return view('shop.tracking', compact('shop'));
    }

    public function submitOrder(Request $request, $slug, \App\Actions\Orders\CreateOrderAction $createOrder)
    {
        $shop = \App\Models\Shop::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string|exists:payment_methods,code',
            'items' => 'required|array|min:1|max:50',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:100',
            'items.*.notes' => 'nullable|string|max:200',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.modifiers' => 'nullable|array',
        ]);

        try {
            // Action Pattern / Domain Layer
            $order = $createOrder->execute($shop, $validated, 'DINE_IN');
            
            $paymentUrl = null;
            if (config('services.xendit.active')) {
                // Future Xendit Integration
            } else {
                \App\Events\OrderCreated::dispatch($order);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'payment_url' => $paymentUrl,
                'order' => $order,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
