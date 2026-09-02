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

    public function submitOrder(Request $request, $slug)
    {
        $shop = Shop::where('slug', $slug)->firstOrFail();

        $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
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
            $product = Product::where('id', $item['id'])
                ->where('shop_id', $shop->id)
                ->first();

            if (! $product) {
                continue;
            }

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

        $order = DB::transaction(function () use ($shop, $request, $total, $orderItems, &$paymentUrl, $slug) {
            // Resolve Table
            $tableModel = Table::where('shop_id', $shop->id)->where('name', $request->table_id)->first();
            if (! $tableModel) {
                $tableModel = Table::create(['shop_id' => $shop->id, 'name' => $request->table_id]);
            }

            // Create Order
            $order = Order::create([
                'shop_id' => $shop->id,
                'table_id' => $tableModel->id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
                'total_price' => $total,
                'status' => 'Masuk',
            ]);

            // Simpan Items
            foreach ($orderItems as $orderItem) {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            // Load relasi agar return datanya lengkap (untuk socket/response)
            $order->load('items.product');

            // INTEGRASI PAYMENT GATEWAY (XENDIT)
            // Saat ini XENDIT_ACTIVE=false, maka akan langsung sukses.
            // Jika true, kita akan melakukan request ke API Xendit untuk membuat Invoice (QRIS/E-Wallet).
            $paymentUrl = null;
            if (config('services.xendit.active')) {
                // Contoh implementasi API Xendit (Create Invoice):
                /*
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode(config('services.xendit.api_key') . ':')
                ])->post('https://api.xendit.co/v2/invoices', [
                    'external_id' => 'order_' . $order->id,
                    'amount' => $total,
                    'payer_email' => $request->customer_email,
                    'description' => 'Pembayaran Pesanan #' . $order->id,
                    'success_redirect_url' => route('shop.tracking', ['slug' => $slug])
                ]);
                $paymentUrl = $response->json('invoice_url');
                $order->update(['payment_status' => 'PENDING']);
                */
            }
            
            return $order;
        });
        
        if (!config('services.xendit.active')) {
            // Jika testing/bypassed, pesanan langsung masuk ke kasir via Reverb
            OrderCreated::dispatch($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'payment_url' => $paymentUrl,
            'order' => $order,
        ]);
    }
}
