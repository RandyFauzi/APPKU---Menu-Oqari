<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        // 1. Basic Dashboard Metrics
        $orders = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->whereIn('payment_status', ['PAID', 'PENDING']) // Exclude FAILED/CANCELLED for sales
            ->get();

        $transactionsCount = $orders->count();
        $grossSales = $orders->sum('grand_total');
        $totalTax = $orders->sum('tax_amount');
        $totalDiscount = $orders->sum('discount_amount');
        
        $netSales = $grossSales - $totalTax; // Simplified Net Sales
        $aov = $transactionsCount > 0 ? $grossSales / $transactionsCount : 0;

        // 2. Split by Payment Methods
        $payments = Payment::where('shop_id', $shopId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'SUCCESS')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        // 3. Product Analytics
        // Best Seller
        $bestSellers = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
            ->where('orders.order_status', '!=', 'CANCELLED')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product')
            ->take(5)
            ->get();

        // Most Cancelled
        $cancelledItems = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.shop_id', $shopId)
            ->whereBetween('orders.created_at', [$startOfDay, $endOfDay])
            ->where('orders.order_status', 'CANCELLED')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product')
            ->take(5)
            ->get();

        // 4. Time Analytics (Hourly) - DB Agnostic mapping
        $chartData = array_fill_keys(array_map(fn($i) => str_pad($i, 2, '0', STR_PAD_LEFT).':00', range(0, 23)), 0);
        
        foreach ($orders as $order) {
            $hour = $order->created_at->format('H:00');
            $chartData[$hour] += $order->grand_total;
        }

        return view('Admin.reports.index', compact(
            'date', 'transactionsCount', 'grossSales', 'netSales', 'aov',
            'payments', 'bestSellers', 'cancelledItems', 'chartData'
        ));
    }
}
