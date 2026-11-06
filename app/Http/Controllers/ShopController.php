<?php

namespace App\Http\Controllers;

use App\Actions\Orders\CreateOrderAction;
use App\Events\OrderCreated;
use App\Jobs\SendOrderReceiptEmail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopSlugHistory;
use App\Models\Table;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected function resolveShop($slug)
    {
        $shop = Shop::where('slug', $slug)->first();
        if (! $shop) {
            $history = ShopSlugHistory::where('old_slug', $slug)->first();
            if ($history && $history->shop) {
                $newUrl = str_replace("/{$slug}", '/'.$history->shop->slug, request()->fullUrl());
                throw new HttpResponseException(redirect($newUrl, 301));
            }
            abort(404);
        }

        abort_if($shop->isSuspended(), 404);

        return $shop;
    }

    public function show(Request $request, $slug)
    {
        $shop = $this->resolveShop($slug);

        $categories = Category::where('shop_id', $shop->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $validCategoryIds = $categories->pluck('id')->all();

        // Data reconciliation: ensure orphaned products from categories deleted in DB are cleaned up
        if (empty($validCategoryIds)) {
            Product::where('shop_id', $shop->id)
                ->where(function ($q): void {
                    $q->whereNotNull('category_id')->orWhereNotNull('category_name');
                })
                ->update(['category_id' => null, 'category_name' => null]);
        } else {
            Product::where('shop_id', $shop->id)
                ->whereNotNull('category_id')
                ->whereNotIn('category_id', $validCategoryIds)
                ->update(['category_id' => null, 'category_name' => null]);

            Product::where('shop_id', $shop->id)
                ->whereNull('category_id')
                ->whereNotNull('category_name')
                ->whereNotIn('category_name', $categories->pluck('name')->all())
                ->update(['category_name' => null]);
        }

        $menuItems = Product::where('shop_id', $shop->id)
            ->where('is_sold_out', false)
            ->with('category')
            ->get()
            ->each(function ($product) use ($validCategoryIds): void {
                if (! $product->category || ! in_array($product->category_id, $validCategoryIds, true)) {
                    $product->category_id = null;
                    $product->category_name = null;
                }
            });

        $table = null;
        if ($request->filled('t')) {
            $tableModel = Table::where('shop_id', $shop->id)
                ->where('public_token', $request->query('t'))
                ->first();
            $table = $tableModel ? $tableModel->name : null;
        } elseif ($request->filled('table')) {
            // Fallback for legacy QR codes (temporary backward compatibility)
            $table = $request->query('table');
        }

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
            'table_id' => 'nullable|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'fulfillment_type' => 'nullable|string|in:DINE_IN,TAKEAWAY,DELIVERY',
            // TODO: Revert validation to 'required|string|exists:payment_methods,code' when Payment Gateway is active
            'payment_method' => 'required|string|max:50',
            'items' => 'required|array|min:1|max:50',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:100',
            'items.*.notes' => 'nullable|string|max:200',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.modifiers' => 'nullable|array',
        ]);

        $fulfillmentType = $validated['fulfillment_type'] ?? 'DINE_IN';

        if ($fulfillmentType === 'DINE_IN' && empty($validated['table_id'])) {
            return response()->json(['success' => false, 'message' => 'Nomor meja wajib diisi untuk pesanan Dine In.'], 422);
        }

        try {
            // Action Pattern / Domain Layer
            $order = $createOrder->execute($shop, $validated, $fulfillmentType);

            $paymentUrl = null;

            OrderCreated::dispatch($order);

            if ($order->customer_email) {
                dispatch(new SendOrderReceiptEmail($order->id));
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
