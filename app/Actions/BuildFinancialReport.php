<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class BuildFinancialReport
{
    /** @param array{date?: ?string, period?: ?string, format?: ?string, page?: int|string} $filters */
    private function range(array $filters): array
    {
        $date = CarbonImmutable::parse($filters['date'] ?? CarbonImmutable::today(config('attendance.timezone'))->toDateString(), config('attendance.timezone'));
        $period = $filters['period'] ?? 'daily';
        $start = match ($period) {
            'weekly' => $date->startOfWeek(), 'monthly' => $date->startOfMonth(), default => $date->startOfDay()
        };
        $end = match ($period) {
            'weekly' => $date->endOfWeek(), 'monthly' => $date->endOfMonth(), default => $date->endOfDay()
        };

        return [$date->toDateString(), $period, $start->utc(), $end->utc()];
    }

    private function query(User $user, array $filters): Builder
    {
        [, $period, $start, $end] = $this->range($filters);

        return Order::where('shop_id', $user->shop_id)->where('payment_status', 'PAID')->whereNotIn('order_status', ['CANCELLED', 'Dibatalkan', 'Cancelled'])
            ->when($period !== 'all', fn (Builder $query) => $query->whereBetween('created_at', [$start, $end]));
    }

    /** @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function execute(User $user, array $filters): array
    {
        [$date, $period, $start, $end] = $this->range($filters);
        $query = $this->query($user, $filters);
        $totals = (clone $query)->selectRaw('COUNT(*) AS transactions_count, COALESCE(SUM(subtotal), 0) AS gross_sales, COALESCE(SUM(discount_amount), 0) AS discount_total, COALESCE(SUM(grand_total), 0) AS grand_total_sum, COALESCE(SUM(total_cogs), 0) AS cogs_total')->first();
        $transactionsCount = (int) $totals->transactions_count;
        $grossSales = (float) $totals->gross_sales;
        $netSales = $grossSales - (float) $totals->discount_total;
        $totalCogs = (float) $totals->cogs_total;
        $grossProfit = $netSales - $totalCogs;
        $grandTotal = (float) $totals->grand_total_sum;
        $aov = $transactionsCount > 0 ? $grandTotal / $transactionsCount : 0;
        $margin = $netSales > 0 ? round($grossProfit / $netSales * 100, 2) : 0;
        $payments = Payment::where('shop_id', $user->shop_id)->whereNull('cancelled_at')
            ->whereHas('order', fn (Builder $order) => $order->where('shop_id', $user->shop_id)->where('payment_status', 'PAID')->whereNotIn('order_status', ['CANCELLED', 'Dibatalkan', 'Cancelled']))
            ->when($period !== 'all', fn (Builder $query) => $query->whereBetween('created_at', [$start, $end]))
            ->select('payment_method', DB::raw('SUM(amount) AS total'))->groupBy('payment_method')->pluck('total', 'payment_method');
        $transactions = (clone $query)->with('table:id,name')->orderByDesc('created_at')->orderByDesc('id')->paginate(25)->withQueryString();
        $bestSellers = DB::table('order_items')->joinSub((clone $query)->select('id'), 'paid_orders', 'paid_orders.id', '=', 'order_items.order_id')
            ->select('product_name', DB::raw('SUM(quantity) AS total_qty'), DB::raw('SUM(subtotal) AS total_revenue'))
            ->groupBy('product_name')->orderByDesc('total_qty')->limit(5)->get();
        $timezone = config('attendance.timezone');

        return compact('date', 'period', 'transactionsCount', 'grossSales', 'netSales', 'totalCogs', 'grossProfit', 'grandTotal', 'aov', 'margin', 'payments', 'transactions', 'bestSellers', 'timezone');
    }

    /** @param array<string, mixed> $filters */
    public function rows(User $user, array $filters): LazyCollection
    {
        return $this->query($user, $filters)->orderBy('id')->cursor()->map(function (Order $order): array {
            $name = (string) $order->customer_name;
            if (preg_match('/^[=+@\-\t\r\n]/', $name)) {
                $name = "'".$name;
            }
            $net = (float) $order->subtotal - (float) $order->discount_amount;

            return [$order->id, $order->created_at->timezone(config('attendance.timezone'))->format('Y-m-d H:i:s'), $name, $order->payment_method, $net, $order->total_cogs, $net - (float) $order->total_cogs, $order->grand_total];
        });
    }
}
