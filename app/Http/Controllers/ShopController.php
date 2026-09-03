<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\Events\OrderCreated;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected function resolveShop($slug)
    {
        $shop = Shop::where('slug', $slug)->first();
        if (!$shop) {
            $history = \App\Models\ShopSlugHistory::where('old_slug', $slug)->first();
            if ($history && $history->shop) {
                $newUrl = str_replace("/{$slug}", "/" . $history->shop->slug, request()->fullUrl());
                throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect($newUrl, 301));
            }
            abort(404);
        }
        return $shop;
    }

    public function show(Request $request, $slug)
    {
        $shop = $this->resolveShop($slug);
        $menuItems = Product::where('shop_id', $shop->id)->where('is_sold_out', false)->with('category')->get();
        
        $table = null;
        if ($request->filled('t')) {
            $tableModel = \App\Models\Table::where('shop_id', $shop->id)
                ->where('public_token', $request->query('t'))
                ->first();
            $table = $tableModel ? $tableModel->name : null;
        } elseif ($request->filled('table')) {
            // Fallback for legacy QR codes (temporary backward compatibility)
            $table = $request->query('table');
        }
        $categories = \App\Models\Category::where('shop_id', $shop->id)->orderBy('sort_order')->get();

        return view('shop.menu', compact('shop', 'menuItems', 'table', 'categories'));
    }

    public function cart($slug)
    {
        $shop = $this->resolveShop($slug);

        return view('shop.cart', compact('shop'));
    }

    public function tracking($slug)
    {
        $shop = $this->resolveShop($slug);

        return view('shop.tracking', compact('shop'));
    }

    public function submitOrder(Request $request, $slug, CreateOrderAction $createOrder)
    {
        $shop = $this->resolveShop($slug);

        $validated = $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            // TODO: Revert validation to 'required|string|exists:payment_methods,code' when Payment Gateway is active
            'payment_method' => 'required|string|max:50',
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
            
            OrderCreated::dispatch($order);

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
