<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Order\CreateOrder;
use App\Actions\POS\CalculateCart;
use App\Actions\POS\SubmitPosOrder;
use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\HoldPosOrderRequest;
use App\Http\Requests\SubmitPosOrderRequest;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    // ──────────────────────────────────────────────
    //  VIEW
    // ──────────────────────────────────────────────

    public function index()
    {
        $user = Auth::user();
        $shop = $user->shop;

        $activeSession = CashRegisterSession::where('user_id', $user->id)
            ->whereHas('register', fn ($query) => $query->where('shop_id', $shop->id))
            ->where('status', 'OPEN')
            ->latest()
            ->first();

        $products = Product::where('shop_id', $shop->id)
            ->where('is_sold_out', false)
            ->with(['variants', 'modifierGroups.modifiers'])
            ->orderBy('category_id')
            ->get();

        // Unique category list for filter tabs
        $categories = $products
            ->where('category_id', '!=', null)
            ->map(fn ($p) => ['id' => $p->category_id, 'name' => $p->category?->name ?? 'Uncategorized'])
            ->unique('id')
            ->values();

        // Active held orders — show in recall bar
        $heldOrders = Order::where('shop_id', $shop->id)
            ->where('order_status', 'HOLD')
            ->with('items')
            ->latest()
            ->take(10)
            ->get();

        $heldOrderSummaries = $heldOrders->map(fn ($order): array => ['id' => $order->id, 'total' => $order->grand_total]);

        $registers = CashRegister::where('shop_id', $shop->id)->where('is_active', true)->get();
        $tables = Table::where('shop_id', $shop->id)->get();

        return view('Admin.pos.index', compact(
            'products', 'categories', 'activeSession', 'heldOrders', 'heldOrderSummaries', 'registers', 'shop', 'tables'
        ));
    }

    // ──────────────────────────────────────────────
    //  API — Submit order + payment in one shot
    // ──────────────────────────────────────────────

    public function submitOrder(SubmitPosOrderRequest $request, SubmitPosOrder $submitOrder): JsonResponse
    {
        $order = $submitOrder->execute($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'change' => $order->change_amount,
            'total' => $order->grand_total,
        ]);
    }

    public function holdOrder(HoldPosOrderRequest $request, CreateOrder $createOrder)
    {
        $validated = $request->validated();

        $shop = Auth::user()->shop;

        $calcCart = app(CalculateCart::class);
        $cart = $calcCart->execute($shop, $validated['items']);

        // CreateOrder puts it as CONFIRMED; we override to HOLD
        $data = $validated;
        $data['payment_method'] = 'CASH'; // placeholder; real method set at recall
        $data['fulfillment_type'] = 'DINE_IN';

        $order = $createOrder->execute($shop, $cart, $data);
        $order->update(['order_status' => 'HOLD', 'payment_status' => 'UNPAID']);

        OrderCreated::dispatch($order);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'total' => $order->grand_total,
        ]);
    }

    // ──────────────────────────────────────────────
    //  API — Get all HOLD orders for this shop
    // ──────────────────────────────────────────────

    public function heldOrders()
    {
        $shopId = Auth::user()->shop_id;

        $orders = Order::where('shop_id', $shopId)
            ->where('order_status', 'HOLD')
            ->with('items')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'total' => $o->grand_total,
                'items' => $o->items->map(fn ($i) => [
                    'name' => $i->product_name,
                    'qty' => $i->quantity,
                    'price' => $i->unit_price,
                    'subtotal' => $i->subtotal,
                    'variant' => $i->variant_name,
                    'modifiers' => $i->modifiers ?? [],
                    'notes' => $i->notes,
                ]),
            ]);

        return response()->json($orders);
    }

    // ──────────────────────────────────────────────
    //  API — Recall a HOLD order back to active cart
    // ──────────────────────────────────────────────

    public function recallOrder(Order $order)
    {
        $shopId = Auth::user()->shop_id;

        // Tenant check
        if ($order->shop_id !== $shopId || $order->order_status !== 'HOLD') {
            return response()->json(['error' => 'Order tidak ditemukan atau tidak bisa di-recall.'], 404);
        }

        $order->load('items');

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'total' => $order->grand_total,
                'items' => $order->items->map(fn ($i) => [
                    'id' => $i->product_id,
                    'name' => $i->product_name,
                    'qty' => $i->quantity,
                    'price' => $i->unit_price,
                    'subtotal' => $i->subtotal,
                    'variant_id' => $i->variant_id,
                    'variant' => $i->variant_name,
                    'modifiers' => $i->modifiers ?? [],
                    'notes' => $i->notes,
                ]),
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    //  API — Get products (for search/filter)
    // ──────────────────────────────────────────────

    public function products(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $query = Product::where('shop_id', $shopId)
            ->where('is_sold_out', false)
            ->with(['variants', 'modifierGroups.modifiers']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        return response()->json($query->get());
    }
}
