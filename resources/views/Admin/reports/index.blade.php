@extends('Admin.Layouts.admin-auth-master')

@section('title', 'Analytics & Reporting')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Analytics Dashboard</h1>
            <p class="text-sm text-gray-500">Overview of your shop's performance</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700">Filter</button>
        </form>
    </div>

    <!-- Top Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">Gross Sales</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($grossSales, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">Net Sales</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($netSales, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">Transactions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($transactionsCount) }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">Avg Order Value</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($aov, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">COGS</p>
            <p class="text-2xl font-bold text-red-500 mt-1">Rp {{ number_format($totalCogs, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <p class="text-sm font-semibold text-gray-500 uppercase">Gross Profit ({{ $margin }}%)</p>
            <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($grossProfit, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Hourly Sales</h2>
            <div class="h-64 flex items-end gap-1">
                @php $max = max($chartData) > 0 ? max($chartData) : 1; @endphp
                @foreach($chartData as $hour => $total)
                    <div class="flex-1 flex flex-col justify-end group relative">
                        <div class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded whitespace-nowrap z-10">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <div class="bg-green-500 rounded-t-sm hover:bg-green-600 transition-all w-full" style="height: {{ ($total / $max) * 100 }}%"></div>
                        <div class="text-[10px] text-gray-400 text-center mt-2 rotate-45 origin-left">{{ $hour }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payments -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Payment Methods</h2>
            <div class="space-y-4">
                @forelse($payments as $method => $total)
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-600">{{ $method }}</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-4">No payments today</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Product Analytics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4 text-green-600">Top 5 Best Sellers</h2>
            <div class="space-y-4">
                @foreach($bestSellers as $item)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-bold text-gray-800">{{ $item->product->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-gray-500">{{ $item->total_qty }} items sold</p>
                        </div>
                        <span class="font-bold text-green-600">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4 text-red-500">Most Cancelled Items</h2>
            <div class="space-y-4">
                @foreach($cancelledItems as $item)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <p class="font-bold text-gray-800">{{ $item->product->name ?? 'Unknown' }}</p>
                            <p class="text-xs text-red-400">{{ $item->total_qty }} items cancelled</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
