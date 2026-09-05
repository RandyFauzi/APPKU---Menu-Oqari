<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function getDashboardAnalytics(int $shopId): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Get aggregated stats for today and yesterday in a single query
        // Using whereBetween is index-friendly on created_at
        $stats = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$yesterday->startOfDay(), $today->endOfDay()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as total_orders'),
                DB::raw("SUM(CASE WHEN payment_status = 'PAID' THEN grand_total ELSE 0 END) as total_revenue")
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('date');

        $todayStr = $today->toDateString();
        $yesterdayStr = $yesterday->toDateString();

        $ordersToday = (int) $stats->where('date', $todayStr)->sum('total_orders');
        $ordersYesterday = (int) $stats->where('date', $yesterdayStr)->sum('total_orders');
        $revenueToday = (float) $stats->where('date', $todayStr)->sum('total_revenue');
        $revenueYesterday = (float) $stats->where('date', $yesterdayStr)->sum('total_revenue');

        $ordersChange = $ordersYesterday > 0 ? round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100) : 0;
        $revenueChange = $revenueYesterday > 0 ? round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100) : 0;

        // 2. Get Hourly Sales for Today in a single query
        // Grouping by hour
        $hourlyData = Order::where('shop_id', $shopId)
            ->where('payment_status', 'PAID')
            ->whereDate('created_at', $today)
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->pluck('count', 'hour');

        $hourlySales = [];
        for ($i = 8; $i <= 22; $i += 2) {
            // Sum the counts for the 2-hour bucket
            $count = $hourlyData->get($i, 0) + $hourlyData->get($i + 1, 0);
            $hourlySales[] = $count;
        }

        // 3. Top Product Today
        $topItem = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.shop_id', $shopId)
            ->whereDate('orders.created_at', $today)
            ->select('order_items.product_name', 'products.image_url', 'products.description', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_name', 'products.image_url', 'products.description')
            ->orderByDesc('total_sold')
            ->first();

        // 4. Customer Retention Today
        // Identify customers who ordered today
        $todayCustomers = DB::table('orders')
            ->where('shop_id', $shopId)
            ->whereDate('created_at', $today)
            ->select(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"))
            ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IS NOT NULL")
            ->distinct()
            ->pluck('identifier')
            ->toArray();
            
        $returningIdentifiers = [];
        if (!empty($todayCustomers)) {
            $placeholders = implode(',', array_fill(0, count($todayCustomers), '?'));
            $returningIdentifiers = DB::table('orders')
                ->where('shop_id', $shopId)
                ->whereDate('created_at', '<', $today)
                ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IN ($placeholders)", $todayCustomers)
                ->select(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"))
                ->distinct()
                ->pluck('identifier')
                ->toArray();
        }
        
        $returningCount = count($returningIdentifiers);
        $newCount = count($todayCustomers) - $returningCount;
        $totalCustomers = count($todayCustomers);
        
        $returningCustomersPct = $totalCustomers > 0 ? round(($returningCount / $totalCustomers) * 100) : null;
        $newCustomersPct = $totalCustomers > 0 ? round(($newCount / $totalCustomers) * 100) : null;

        return [
            'orders' => $ordersToday,
            'ordersChange' => $ordersChange,
            'revenue' => $revenueToday,
            'revenueChange' => $revenueChange,
            'topProduct' => $topItem ? [
                'name' => $topItem->product_name,
                'sold' => (int) $topItem->total_sold,
                'change' => 0,
                'image' => $topItem->image_url ?? null,
                'description' => $topItem->description ?? 'Tidak ada deskripsi',
            ] : [
                'name' => 'Belum ada',
                'sold' => 0,
                'change' => 0,
                'image' => null,
                'description' => 'Belum ada transaksi hari ini',
            ],
            'returningCustomers' => $returningCustomersPct,
            'newCustomersPct' => $newCustomersPct,
            'returningCount' => $returningCount,
            'newCount' => $newCount,
            'totalCustomers' => $totalCustomers,
            'hourlySales' => $hourlySales,
        ];
    }
}
