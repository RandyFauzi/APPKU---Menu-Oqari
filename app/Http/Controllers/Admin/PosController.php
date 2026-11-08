<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Order\CreateOrder;
use App\Actions\POS\ApplyDiscount;
use App\Actions\POS\CalculateCart;
use App\Actions\POS\SubmitPosOrder;
use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\HoldPosOrderRequest;
use App\Http\Requests\SubmitPosOrderRequest;
use App\Models\ActivityLog;
use App\Models\CashRegister;
use App\Models\CashRegisterSession;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $soldCounts = OrderItem::query()
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $shop->id)
            ->whereNotIn('orders.order_status', ['CANCELLED', 'HOLD'])
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $products = Product::where('shop_id', $shop->id)
            ->where('is_sold_out', false)
            ->with(['category', 'variants', 'modifierGroups.modifiers'])
            ->orderBy('category_id')
            ->get()
            ->each(function (Product $product) use ($soldCounts): void {
                $product->setAttribute('sold_count', (int) ($soldCounts[$product->id] ?? 0));
                $product->setAttribute('category_name', $product->category?->name ?? 'Menu');
            });

        // Unique category list for filter tabs
        $categories = $products
            ->where('category_id', '!=', null)
            ->map(fn ($p) => ['id' => $p->category_id, 'name' => $p->category?->name ?? 'Uncategorized'])
            ->unique('id')
            ->values();

        // Active held orders — show in recall bar
        $heldOrders = Order::where('shop_id', $shop->id)
            ->where('order_status', 'HOLD')
            ->with(['items', 'table'])
            ->latest()
            ->take(10)
            ->get();

        $heldOrderSummaries = $heldOrders->map(fn ($order): array => [
            'id' => $order->id,
            'total' => (float) $order->grand_total,
            'customer_name' => $order->customer_name ?: 'Walk-in',
            'table' => $order->table?->name ?? ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja'),
            'time' => $order->created_at?->format('H:i') ?? '',
            'items_count' => (int) $order->items->sum('quantity'),
        ]);

        // Initial live queue orders for the top Antrean section
        $activeQueueOrders = Order::where('shop_id', $shop->id)
            ->whereIn('order_status', ['PENDING', 'CONFIRMED', 'PREPARING', 'READY'])
            ->with(['items', 'table'])
            ->latest()
            ->take(15)
            ->get()
            ->map(function (Order $order): array {
                $statusMap = [
                    'PENDING' => 'Masuk',
                    'CONFIRMED' => 'Masuk',
                    'PREPARING' => 'Diproses',
                    'READY' => 'Siap',
                ];

                return [
                    'id' => $order->id,
                    'table' => $order->table?->name ?? ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : ($order->fulfillment_type === 'DELIVERY' ? 'Delivery' : 'Tanpa Meja')),
                    'customer_name' => $order->customer_name ?: 'Walk-in Customer',
                    'total' => (float) $order->grand_total,
                    'status' => $statusMap[$order->order_status] ?? $order->order_status,
                    'order_status' => $order->order_status,
                    'fulfillment_type' => $order->fulfillment_type ?? 'DINE_IN',
                    'time' => $order->created_at?->format('H:i') ?? '--:--',
                    'items' => $order->items->map(fn ($item): array => [
                        'name' => $item->product_name,
                        'qty' => (int) $item->quantity,
                    ])->values()->all(),
                ];
            })
            ->values();

        $registers = CashRegister::where('shop_id', $shop->id)->where('is_active', true)->get();
        $tables = Table::where('shop_id', $shop->id)->get();

        return view('Admin.pos.index', compact(
            'products', 'categories', 'activeSession', 'heldOrders', 'heldOrderSummaries', 'activeQueueOrders', 'registers', 'shop', 'tables'
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
        $cart = app(ApplyDiscount::class)->execute($shop, $cart, $validated);

        // Check if updating an existing Open Bill / Unpaid Order
        if (! empty($validated['recalled_order_id'])) {
            $order = Order::where('shop_id', $shop->id)
                ->whereKey($validated['recalled_order_id'])
                ->where('payment_status', 'UNPAID')
                ->whereNotIn('order_status', ['COMPLETED', 'CANCELLED'])
                ->first();

            if ($order) {
                $order->items()->delete();
                $hasPriceColumn = Schema::hasColumn('order_items', 'price');
                $hasServingTypeColumn = Schema::hasColumn('order_items', 'serving_type');
                foreach ($cart['items'] as $item) {
                    if ($hasPriceColumn && ! isset($item['price'])) {
                        $item['price'] = $item['unit_price'] ?? 0;
                    }
                    if (! $hasServingTypeColumn) {
                        unset($item['serving_type']);
                    }
                    $order->items()->create($item);
                }

                $tableId = $order->table_id;
                if (! empty($validated['table'])) {
                    $tableModel = Table::where('shop_id', $shop->id)
                        ->where('name', $validated['table'])
                        ->first()
                        ?? Table::create(['shop_id' => $shop->id, 'name' => $validated['table']]);
                    $tableId = $tableModel->id;
                }

                $order->update([
                    'table_id' => $tableId,
                    'customer_name' => $validated['customer_name'] ?? $order->customer_name,
                    'subtotal' => $cart['subtotal'],
                    'grand_total' => $cart['net_sales'] ?? $cart['subtotal'],
                    'total_cogs' => $cart['total_cogs'],
                    'discount_amount' => $cart['discount_amount'] ?? 0,
                    'voucher_code' => $cart['voucher_code'] ?? null,
                    'discount_reason' => $cart['discount_reason'] ?? null,
                    'tax_amount' => 0,
                    'service_charge_amount' => 0,
                    'order_status' => 'HOLD',
                    'payment_status' => 'UNPAID',
                    'fulfillment_type' => $validated['fulfillment_type'] ?? $order->fulfillment_type ?? 'DINE_IN',
                ]);

                OrderCreated::dispatch($order);

                if (class_exists(ActivityLog::class)) {
                    $tableLabel = $order->table?->name ? 'Meja '.$order->table->name : ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja');
                    ActivityLog::create([
                        'shop_id' => $shop->id,
                        'user_id' => Auth::id(),
                        'action' => 'pos.order_held_updated',
                        'entity_type' => Order::class,
                        'entity_id' => $order->id,
                        'description' => 'Memperbarui pesanan Open Bill #'.$order->id.' ('.$tableLabel.' · '.($order->customer_name ?: 'Walk-in').') - Total: Rp '.number_format((float) $order->grand_total, 0, ',', '.'),
                        'ip_address' => request()->ip(),
                        'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'total' => (float) $order->grand_total,
                    'table' => $order->table?->name ?? ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja'),
                    'customer_name' => $order->customer_name ?: 'Walk-in',
                    'time' => $order->created_at?->format('H:i') ?? '',
                    'items_count' => (int) $order->items()->sum('quantity'),
                    'updated' => true,
                ]);
            }
        }

        // New Open Bill
        $data = $validated;
        $data['payment_method'] = 'CASH'; // placeholder; real method set at recall & payment
        $data['fulfillment_type'] = $validated['fulfillment_type'] ?? 'DINE_IN';

        $order = $createOrder->execute($shop, $cart, $data);
        $order->update(['order_status' => 'HOLD', 'payment_status' => 'UNPAID']);

        OrderCreated::dispatch($order);

        $order->load('table');

        if (class_exists(ActivityLog::class)) {
            $tableLabel = $order->table?->name ? 'Meja '.$order->table->name : ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja');
            ActivityLog::create([
                'shop_id' => $shop->id,
                'user_id' => Auth::id(),
                'action' => 'pos.order_held',
                'entity_type' => Order::class,
                'entity_id' => $order->id,
                'description' => 'Membuat pesanan Open Bill baru #'.$order->id.' ('.$tableLabel.' · '.($order->customer_name ?: 'Walk-in').') - Total: Rp '.number_format((float) $order->grand_total, 0, ',', '.'),
                'ip_address' => request()->ip(),
                'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'total' => (float) $order->grand_total,
            'table' => $order->table?->name ?? ($order->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja'),
            'customer_name' => $order->customer_name ?: 'Walk-in',
            'time' => $order->created_at?->format('H:i') ?? '',
            'items_count' => (int) $order->items()->sum('quantity'),
            'updated' => false,
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
            ->with(['items', 'table'])
            ->latest()
            ->take(20)
            ->get()
            ->map(fn ($o) => [
                'id' => $o->id,
                'total' => (float) $o->grand_total,
                'customer_name' => $o->customer_name ?: 'Walk-in',
                'table' => $o->table?->name ?? ($o->fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Tanpa Meja'),
                'time' => $o->created_at?->format('H:i') ?? '',
                'items_count' => (int) $o->items->sum('quantity'),
                'items' => $o->items->map(fn ($i) => [
                    'name' => $i->product_name,
                    'qty' => $i->quantity,
                    'price' => $i->unit_price,
                    'subtotal' => $i->subtotal,
                    'variant' => $i->variant_name,
                    'modifiers' => $i->modifiers ?? [],
                    'notes' => $i->notes,
                    'serving_type' => $i->serving_type ?? (str_contains($i->notes ?? '', '[TAKEAWAY]') ? 'TAKEAWAY' : ($order->fulfillment_type ?? 'DINE_IN')),
                ]),
            ]);

        return response()->json($orders);
    }

    // ──────────────────────────────────────────────
    //  API — Cancel / Void an Open Bill (HOLD)
    // ──────────────────────────────────────────────

    public function cancelHeldOrder(Order $order)
    {
        $shopId = Auth::user()->shop_id;

        if ($order->shop_id !== $shopId || $order->order_status !== 'HOLD') {
            return response()->json(['error' => 'Pesanan tidak ditemukan atau tidak dapat dibatalkan.'], 404);
        }

        $order->update(['order_status' => 'CANCELLED']);

        return response()->json(['success' => true, 'message' => 'Open bill berhasil dibatalkan.']);
    }

    // ──────────────────────────────────────────────
    //  API — Recall a HOLD order back to active cart
    // ──────────────────────────────────────────────

    public function recallOrder(Order $order)
    {
        $shopId = Auth::user()->shop_id;

        // Tenant check and ensure order is unpaid and active
        if ($order->shop_id !== $shopId || $order->payment_status === 'PAID' || in_array($order->order_status, ['COMPLETED', 'CANCELLED'], true)) {
            return response()->json(['error' => 'Pesanan tidak ditemukan, sudah lunas, atau sudah selesai.'], 404);
        }

        $order->load(['items.product', 'table']);

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'total' => (float) $order->grand_total,
                'subtotal' => (float) $order->subtotal,
                'discount_amount' => (float) $order->discount_amount,
                'voucher_code' => $order->voucher_code,
                'discount_reason' => $order->discount_reason,
                'customer_name' => $order->customer_name,
                'table' => $order->table?->name,
                'fulfillment_type' => $order->fulfillment_type ?? 'DINE_IN',
                'items' => $order->items->map(fn ($i) => [
                    'id' => $i->product_id,
                    'name' => $i->product_name,
                    'image' => $i->product?->image_url,
                    'qty' => $i->quantity,
                    'price' => $i->unit_price,
                    'subtotal' => $i->subtotal,
                    'variant_id' => $i->variant_id,
                    'variant' => $i->variant_name,
                    'modifiers' => $i->modifiers ?? [],
                    'notes' => $i->notes,
                    'serving_type' => $i->serving_type ?? (str_contains($i->notes ?? '', '[TAKEAWAY]') ? 'TAKEAWAY' : ($order->fulfillment_type ?? 'DINE_IN')),
                ]),
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    //  API — Get products (for search/filter)
    // ──────────────────────────────────────────────

    public function products(Request $request): JsonResponse
    {
        abort_unless(Auth::user()?->canAny(['access-pos', 'view-orders', 'manage-menu']), 403);

        $shopId = Auth::user()->shop_id;

        $soldCounts = OrderItem::query()
            ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $shopId)
            ->whereNotIn('orders.order_status', ['CANCELLED', 'HOLD'])
            ->groupBy('order_items.product_id')
            ->pluck('total_sold', 'product_id');

        $query = Product::where('shop_id', $shopId)
            ->where('is_sold_out', false)
            ->with(['category', 'variants', 'modifierGroups.modifiers'])
            ->orderBy('category_id')
            ->orderBy('name');

        if ($request->filled('category_id') && ! in_array($request->category_id, ['all', 'best_seller'], true)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->input('category_id') === 'best_seller') {
            $query->where('is_best_seller', true);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $products = $query->get()->each(function (Product $product) use ($soldCounts): void {
            $product->setAttribute('sold_count', (int) ($soldCounts[$product->id] ?? 0));
            $product->setAttribute('category_name', $product->category?->name ?? 'Menu');
        });

        return response()->json($products);
    }

    // ──────────────────────────────────────────────
    //  API — Save POS discount settings / presets
    // ──────────────────────────────────────────────

    public function saveDiscountSettings(Request $request)
    {
        $shop = Auth::user()->shop;
        if (! $shop) {
            return response()->json(['success' => false, 'message' => 'Shop not found'], 404);
        }

        $validated = $request->validate([
            'is_enabled' => 'nullable|boolean',
            'allow_custom' => 'nullable|boolean',
            'presets' => 'nullable|array',
            'presets.*.id' => 'nullable|string',
            'presets.*.name' => 'required_with:presets|string|max:50',
            'presets.*.type' => 'required_with:presets|in:percentage,fixed',
            'presets.*.value' => 'required_with:presets|numeric|min:0',
        ]);

        $current = is_array($shop->discount_settings) ? $shop->discount_settings : [];
        $updated = array_merge($current, $validated);

        $shop->update(['discount_settings' => $updated]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan diskon kasir berhasil disimpan.',
            'discount_settings' => $shop->discount_settings,
        ]);
    }
}
