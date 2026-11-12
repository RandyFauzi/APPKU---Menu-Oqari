<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function getDashboardAnalytics(int $shopId): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // 1. Get aggregated stats for today and yesterday in a single query
        $stats = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$yesterday->copy()->startOfDay(), $today->copy()->endOfDay()])
            ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
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

        if ($ordersYesterday > 0) {
            $ordersChange = round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 1);
        } elseif ($ordersToday > 0) {
            $ordersChange = 100.0;
        } else {
            $ordersChange = 0.0;
        }

        if ($revenueYesterday > 0) {
            $revenueChange = round((($revenueToday - $revenueYesterday) / $revenueYesterday) * 100, 1);
        } elseif ($revenueToday > 0) {
            $revenueChange = 100.0;
        } else {
            $revenueChange = 0.0;
        }

        // 2. Get Hourly Sales for Today
        $hourExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%H', created_at) AS INTEGER)",
            'pgsql' => 'EXTRACT(HOUR FROM created_at)',
            default => 'HOUR(created_at)',
        };

        $hourlyPaidData = Order::where('shop_id', $shopId)
            ->where('payment_status', 'PAID')
            ->whereDate('created_at', $today)
            ->select(
                DB::raw($hourExpression.' as hour'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy(DB::raw($hourExpression))
            ->pluck('count', 'hour');

        $hourlySales = [];
        for ($i = 8; $i <= 22; $i += 2) {
            $count = (int) $hourlyPaidData->get($i, 0) + (int) $hourlyPaidData->get($i + 1, 0);
            $hourlySales[] = $count;
        }

        // Busy Hours (All non-cancelled orders today by hour, 09:00 - 20:00)
        $hourlyAllData = Order::where('shop_id', $shopId)
            ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
            ->whereDate('created_at', $today)
            ->select(
                DB::raw($hourExpression.' as hour'),
                DB::raw('COUNT(id) as count')
            )
            ->groupBy(DB::raw($hourExpression))
            ->pluck('count', 'hour');

        $maxHourlyCount = 0;
        $peakHour = null;
        foreach ($hourlyAllData as $hr => $cnt) {
            if ((int) $cnt > $maxHourlyCount) {
                $maxHourlyCount = (int) $cnt;
                $peakHour = (int) $hr;
            }
        }

        $busyHoursBars = [];
        for ($h = 9; $h <= 20; $h++) {
            $cnt = (int) $hourlyAllData->get($h, 0);
            $height = $maxHourlyCount > 0
                ? ($cnt > 0 ? max(16, (int) round(($cnt / $maxHourlyCount) * 100)) : 6)
                : 6;
            $isPeak = $maxHourlyCount > 0 && $cnt > 0 && $cnt >= ($maxHourlyCount * 0.7);

            $busyHoursBars[] = [
                'label' => sprintf('%02d:00', $h),
                'visitors' => $cnt,
                'height' => $height,
                'isPeak' => $isPeak,
            ];
        }

        $busyHourSummary = [
            'peakHour' => $peakHour !== null ? sprintf('%02d:00', $peakHour) : '—',
            'peakVisitors' => $maxHourlyCount,
            'hasData' => $maxHourlyCount > 0,
            'bars' => $busyHoursBars,
        ];

        // 3. Top Products By Period (Today, This Week, This Month - Top 5)
        $fetchTopItems = function (?Carbon $specificDate = null, ?Carbon $startDate = null, ?Carbon $endDate = null) use ($shopId) {
            $query = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.shop_id', $shopId)
                ->whereNotIn('orders.order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled']);

            if ($specificDate) {
                $query->whereDate('orders.created_at', $specificDate);
            } elseif ($startDate && $endDate) {
                $query->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            return $query->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
                ->groupBy('order_items.product_id', 'order_items.product_name')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();
        };

        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek()->startOfDay();
        $endOfWeek = $now->copy()->endOfWeek()->endOfDay();
        $startOfMonth = $now->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $now->copy()->endOfMonth()->endOfDay();

        $top5Today = $fetchTopItems(specificDate: $today);
        $top5Week = $fetchTopItems(startDate: $startOfWeek, endDate: $endOfWeek);
        $top5Month = $fetchTopItems(startDate: $startOfMonth, endDate: $endOfMonth);

        $allProductIds = $top5Today->pluck('product_id')
            ->concat($top5Week->pluck('product_id'))
            ->concat($top5Month->pluck('product_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $productsById = ! empty($allProductIds)
            ? Product::where('shop_id', $shopId)->whereIn('id', $allProductIds)->get()->keyBy('id')
            : collect();

        $formatTopList = function ($collection) use ($productsById) {
            return $collection->map(function ($item, $index) use ($productsById) {
                $product = $productsById->get($item->product_id);

                return [
                    'rank' => $index + 1,
                    'name' => $item->product_name,
                    'sold' => (int) $item->total_sold,
                    'revenue' => (float) $item->total_revenue,
                    'image' => $product?->image_url,
                ];
            })->values()->toArray();
        };

        $topProductsListToday = $formatTopList($top5Today);
        $topProductsListWeek = $formatTopList($top5Week);
        $topProductsListMonth = $formatTopList($top5Month);

        $topItem = $top5Today->first();
        $topProduct = $topItem?->product_id ? $productsById->get($topItem->product_id) : null;

        // 4. Customer Retention (Today & Monthly Comparison)
        $todayCustomers = DB::table('orders')
            ->where('shop_id', $shopId)
            ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
            ->whereDate('created_at', $today)
            ->select(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"))
            ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IS NOT NULL")
            ->distinct()
            ->pluck('identifier')
            ->toArray();

        $todayReturningCount = 0;
        if (! empty($todayCustomers)) {
            $placeholders = implode(',', array_fill(0, count($todayCustomers), '?'));
            $todayLifetimeCounts = DB::table('orders')
                ->where('shop_id', $shopId)
                ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
                ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IN ($placeholders)", $todayCustomers)
                ->select(
                    DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"),
                    DB::raw('COUNT(id) as total_orders')
                )
                ->groupBy(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, ''))"))
                ->pluck('total_orders', 'identifier');

            $todayReturningCount = $todayLifetimeCounts->filter(fn ($cnt) => (int) $cnt > 1)->count();
        }
        $todayVisitors = max(count($todayCustomers), $ordersToday);

        // Monthly Retention ("Perbandingan bulan ini")
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $monthlyCustomerIds = DB::table('orders')
            ->where('shop_id', $shopId)
            ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->select(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"))
            ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IS NOT NULL")
            ->distinct()
            ->pluck('identifier')
            ->toArray();

        $monthlyReturningCount = 0;
        $identifiedMonthlyNewCount = 0;

        if (! empty($monthlyCustomerIds)) {
            $placeholders = implode(',', array_fill(0, count($monthlyCustomerIds), '?'));
            $monthlyLifetimeCounts = DB::table('orders')
                ->where('shop_id', $shopId)
                ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
                ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IN ($placeholders)", $monthlyCustomerIds)
                ->select(
                    DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) as identifier"),
                    DB::raw('COUNT(id) as total_orders')
                )
                ->groupBy(DB::raw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, ''))"))
                ->pluck('total_orders', 'identifier');

            $monthlyReturningCount = $monthlyLifetimeCounts->filter(fn ($cnt) => (int) $cnt > 1)->count();
            $identifiedMonthlyNewCount = $monthlyLifetimeCounts->filter(fn ($cnt) => (int) $cnt === 1)->count();
        }

        $anonymousMonthlyOrders = DB::table('orders')
            ->where('shop_id', $shopId)
            ->whereNotIn('order_status', ['Dibatalkan', 'CANCELLED', 'Cancelled'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereRaw("COALESCE(NULLIF(customer_phone, ''), NULLIF(customer_name, '')) IS NULL")
            ->count();

        $monthlyNewCount = $identifiedMonthlyNewCount + $anonymousMonthlyOrders;
        $monthlyTotalCustomers = $monthlyNewCount + $monthlyReturningCount;

        $newCustomersPct = $monthlyTotalCustomers > 0 ? (int) round(($monthlyNewCount / $monthlyTotalCustomers) * 100) : 0;
        $returningCustomersPct = $monthlyTotalCustomers > 0 ? (100 - $newCustomersPct) : 0;

        // 5. Monthly Revenue Trend (Last 6 Months)
        $sixMonthStart = Carbon::now()->subMonths(5)->startOfMonth();
        $dailyRevenues = Order::where('shop_id', $shopId)
            ->where('payment_status', 'PAID')
            ->whereBetween('created_at', [$sixMonthStart, Carbon::now()->endOfMonth()])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total_revenue')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        $revenueByMonth = [];
        foreach ($dailyRevenues as $row) {
            $ym = substr((string) $row->date, 0, 7);
            $revenueByMonth[$ym] = ($revenueByMonth[$ym] ?? 0.0) + (float) $row->total_revenue;
        }

        $monthNamesId = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $monthsData = [];
        $maxMonthRev = 0.0;
        for ($i = 5; $i >= 0; $i--) {
            $mDate = Carbon::now()->subMonths($i)->startOfMonth();
            $ym = $mDate->format('Y-m');
            $rev = $revenueByMonth[$ym] ?? 0.0;
            if ($rev > $maxMonthRev) {
                $maxMonthRev = $rev;
            }
            $monthsData[] = [
                'ym' => $ym,
                'label' => $monthNamesId[(int) $mDate->month],
                'year' => $mDate->year,
                'revenue' => $rev,
            ];
        }

        $useMillions = $maxMonthRev >= 1000000 || $maxMonthRev == 0;
        $divisor = $useMillions ? 1000000 : 1000;
        $unitLabel = $useMillions ? 'Rp juta' : 'Rp ribu';

        $scaledMax = $maxMonthRev > 0 ? ceil(($maxMonthRev / $divisor) / 5) * 5 : 100;
        if ($scaledMax < 5) {
            $scaledMax = 5;
        }

        $yAxisLabels = [];
        for ($step = 5; $step >= 0; $step--) {
            $val = ($scaledMax / 5) * $step;
            $yAxisLabels[] = is_float($val) && floor($val) != $val ? number_format($val, 1, ',', '.') : (string) (int) $val;
        }

        $trendPoints = [];
        foreach ($monthsData as $idx => $m) {
            $x = $idx * 100;
            $scaledVal = $m['revenue'] / $divisor;
            $ratio = $scaledMax > 0 ? min(1, max(0, $scaledVal / $scaledMax)) : 0;
            // SVG viewBox height is 180; y=10 is top (100%), y=170 is bottom (0%)
            $y = (int) round(170 - ($ratio * 160));
            $trendPoints[] = [
                'label' => $m['label'],
                'revenue' => $m['revenue'],
                'x' => $x,
                'y' => $y,
            ];
        }

        $firstM = $monthsData[0];
        $lastM = $monthsData[count($monthsData) - 1];
        $periodLabel = $firstM['label'].'–'.$lastM['label'].' '.$lastM['year'];

        return [
            'orders' => $ordersToday,
            'ordersYesterday' => $ordersYesterday,
            'ordersChange' => $ordersChange,
            'revenue' => $revenueToday,
            'revenueYesterday' => $revenueYesterday,
            'revenueChange' => $revenueChange,
            'topProduct' => $topItem ? [
                'name' => $topItem->product_name,
                'sold' => (int) $topItem->total_sold,
                'change' => 0,
                'image' => $topProduct?->image_url,
                'description' => $topProduct?->description ?? 'Tidak ada deskripsi',
            ] : [
                'name' => 'Belum ada',
                'sold' => 0,
                'change' => 0,
                'image' => null,
                'description' => 'Belum ada transaksi hari ini',
            ],
            'todayReturningCount' => $todayReturningCount,
            'todayVisitors' => $todayVisitors,
            'returningCustomers' => $returningCustomersPct,
            'newCustomersPct' => $newCustomersPct,
            'returningCount' => $monthlyReturningCount,
            'newCount' => $monthlyNewCount,
            'totalCustomers' => $todayVisitors,
            'topProducts' => $topProductsListToday,
            'topProductsByPeriod' => [
                'today' => $topProductsListToday,
                'week' => $topProductsListWeek,
                'month' => $topProductsListMonth,
            ],
            'hourlySales' => $hourlySales,
            'busyHours' => $busyHourSummary,
            'monthlyTrend' => [
                'periodLabel' => $periodLabel,
                'unitLabel' => $unitLabel,
                'yAxisLabels' => $yAxisLabels,
                'points' => $trendPoints,
                'hasData' => $maxMonthRev > 0,
            ],
        ];
    }
}
