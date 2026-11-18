<div x-data="{ reportTab: '{{ request('tab', ($transactionsCount === 0 && ($totalCashOut ?? 0) > 0) ? 'cash_out' : 'orders') }}' }" class="flex-grow overflow-y-auto bg-white p-4 font-['DM_Sans',sans-serif] md:p-6 lg:p-7">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-[25px] font-semibold tracking-tight">Laporan Keuangan</h1>
            <p class="mt-1.5 text-[13px] text-[#7f837e]">Penjualan yang sudah dibayar dan HPP tersimpan · {{ $timezone }}</p>
        </div>
        <div>
            <button type="button" @click="$dispatch('open-cash-out-modal')" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-[#df5826] hover:bg-[#c94d00] px-4 py-2.5 text-xs font-bold text-white shadow-xs transition cursor-pointer">
                <i class="fas fa-arrow-up-from-bracket text-xs" aria-hidden="true"></i>
                <span>Catat Pengeluaran</span>
            </button>
        </div>
    </div>
    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-xl border border-[#e9ebf0] bg-white p-5">
        <input type="hidden" name="tab" :value="reportTab">
        <div><label for="report-date" class="mb-1.5 block text-xs text-[#53565a]">Tanggal acuan</label><input id="report-date" type="date" name="date" value="{{ $date }}" required class="h-11 rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500"></div>
        <div><label for="report-period" class="mb-1.5 block text-xs text-[#53565a]">Periode</label><select id="report-period" name="period" class="h-11 rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">@foreach(['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'all' => 'Semua periode'] as $key => $label)<option value="{{ $key }}" @selected($period === $key)>{{ $label }}</option>@endforeach</select></div>
        <button type="submit" class="h-11 rounded-lg border border-[#e9ebf0] px-4 text-xs font-semibold text-[#53565a] hover:bg-gray-50">Tampilkan</button>
    </form>
    <div class="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach(['Penjualan bersih' => $netSales, 'HPP tersimpan' => $totalCogs, 'Laba kotor' => $grossProfit, 'Total tagihan dibayar' => $grandTotal] as $label => $amount)
            <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-sm"><p class="text-xs text-[#7f837e]">{{ $label }}</p><p class="mt-3 text-xl font-semibold">Rp {{ number_format($amount, 0, ',', '.') }}</p></div>
        @endforeach
    </div>
    <div class="mb-5 grid gap-5 lg:grid-cols-3">
        <section class="rounded-xl border border-[#e9ebf0] bg-white p-5">
            <h2 class="text-sm font-semibold">Ringkasan periode</h2>
            <dl class="mt-4 space-y-3 text-xs">
                <div class="flex justify-between"><dt class="text-[#7f837e]">Transaksi dibayar</dt><dd class="font-medium">{{ $transactionsCount }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#7f837e]">Rata-rata transaksi</dt><dd class="font-medium">Rp {{ number_format($aov, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-[#7f837e]">Margin laba kotor</dt><dd class="font-medium">{{ $margin }}%</dd></div>
                <div class="flex justify-between items-center border-t border-[#f0f0ee] pt-2">
                    <dt class="text-[#7f837e] flex items-center gap-1.5">
                        <span>Kas keluar tercatat</span>
                        @if(($totalCashOut ?? 0) > 0)
                            <button type="button" @click="reportTab = 'cash_out'; document.getElementById('report-tables-container')?.scrollIntoView({ behavior: 'smooth' })" class="text-[10.5px] text-[#df5826] hover:text-[#c94d00] underline font-semibold cursor-pointer">
                                (Lihat Rincian)
                            </button>
                        @endif
                    </dt>
                    <dd class="font-bold text-[#df5826]">Rp {{ number_format($totalCashOut ?? 0, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </section>
        <section class="rounded-xl border border-[#e9ebf0] bg-white p-5"><h2 class="text-sm font-semibold">Penerimaan pembayaran</h2><div class="mt-4 space-y-3 text-xs">@forelse($payments as $method => $amount)<div class="flex justify-between gap-3"><span class="text-[#7f837e]">{{ $method }}</span><span class="font-medium">Rp {{ number_format($amount, 0, ',', '.') }}</span></div>@empty<p class="text-[#7f837e]">Belum ada pembayaran.</p>@endforelse</div></section>
        <section class="rounded-xl border border-[#e9ebf0] bg-white p-5"><h2 class="text-sm font-semibold">Menu terlaris</h2><div class="mt-4 space-y-3 text-xs">@forelse($bestSellers as $item)<div class="flex justify-between gap-3"><span class="text-[#53565a]">{{ $item->product_name }}</span><span class="whitespace-nowrap text-[#7f837e]">{{ $item->total_qty }} terjual</span></div>@empty<p class="text-[#7f837e]">Belum ada penjualan.</p>@endforelse</div></section>
    </div>
    
    {{-- TABBED DATA TABLES SECTION --}}
    <section id="report-tables-container" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
        <div class="border-b border-[#e9ebf0] px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 bg-white">
            <div class="flex items-center gap-2">
                <button type="button" @click="reportTab = 'orders'" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition cursor-pointer"
                        :class="reportTab === 'orders' ? 'bg-[#fff0e6] text-[#df5826] border border-[#ffd2b8] shadow-2xs font-bold' : 'text-[#7f837e] hover:text-[#111827] hover:bg-gray-50 border border-transparent'">
                    <i class="fas fa-receipt" :class="reportTab === 'orders' ? 'text-[#df5826]' : 'text-gray-400'"></i>
                    <span>Transaksi Penjualan</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition"
                          :class="reportTab === 'orders' ? 'bg-[#df5826] text-white' : 'bg-gray-100 text-[#53565a]'">
                        {{ $transactionsCount }}
                    </span>
                </button>
                <button type="button" @click="reportTab = 'cash_out'" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold transition cursor-pointer"
                        :class="reportTab === 'cash_out' ? 'bg-[#fff0e6] text-[#df5826] border border-[#ffd2b8] shadow-2xs font-bold' : 'text-[#7f837e] hover:text-[#df5826] hover:bg-orange-50/50 border border-transparent'">
                    <i class="fas fa-arrow-up-from-bracket" :class="reportTab === 'cash_out' ? 'text-[#df5826]' : 'text-gray-400'"></i>
                    <span>Rincian Kas Keluar</span>
                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold transition"
                          :class="reportTab === 'cash_out' ? 'bg-[#df5826] text-white' : 'bg-gray-100 text-[#53565a]'">
                        {{ $cashOutTransactions->total() }}
                    </span>
                </button>
            </div>
            
            <div class="flex items-center gap-3.5 flex-wrap">
                <div class="text-xs text-[#7f837e]">
                    <span x-show="reportTab === 'orders'">Total Penjualan Bersih: <strong class="text-[#111827]">Rp {{ number_format($netSales, 0, ',', '.') }}</strong></span>
                    <span x-show="reportTab === 'cash_out'">Total Kas Keluar: <strong class="text-[#df5826] font-bold">Rp {{ number_format($totalCashOut ?? 0, 0, ',', '.') }}</strong></span>
                </div>

                {{-- Secondary Action Button: Ekspor XLS --}}
                <a :href="'{{ route('admin.reports.index', ['date' => $date, 'period' => $period, 'format' => 'xls']) }}' + (reportTab === 'cash_out' ? '&type=cash_out' : '&type=orders')" 
                   class="inline-flex items-center gap-1.5 rounded-lg border border-[#e9ebf0] bg-white px-3 py-1.5 text-xs font-semibold text-[#53565a] hover:bg-gray-50 hover:text-[#111827] hover:border-gray-300 shadow-2xs transition cursor-pointer"
                   title="Unduh laporan dalam format file spreadsheet Excel (XLS)">
                    <i class="fas fa-file-excel text-emerald-600" aria-hidden="true"></i>
                    <span>Ekspor XLS</span>
                </a>
            </div>
        </div>

        {{-- TAB 1: TRANSAKSI PENJUALAN --}}
        <div x-show="reportTab === 'orders'">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-xs">
                    <caption class="sr-only">Transaksi dibayar sesuai periode laporan</caption>
                    <thead class="bg-[#f8f9fa] text-[10px] uppercase tracking-wider text-[#7f837e]">
                        <tr>
                            <th scope="col" class="px-5 py-4">Transaksi</th>
                            <th scope="col" class="px-5 py-4">Pelanggan</th>
                            <th scope="col" class="px-5 py-4">Pembayaran</th>
                            <th scope="col" class="px-5 py-4 text-right">Penjualan bersih</th>
                            <th scope="col" class="px-5 py-4 text-right">HPP</th>
                            <th scope="col" class="px-5 py-4 text-right">Laba kotor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e9ebf0]">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold">#{{ $transaction->id }}</p>
                                    <p class="mt-1 text-[11px] text-[#7f837e]">{{ $transaction->created_at->timezone($timezone)->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-4">{{ $transaction->customer_name ?: 'Pelanggan' }}</td>
                                <td class="px-5 py-4">{{ $transaction->payment_method ?: '—' }}</td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($transaction->subtotal - $transaction->discount_amount, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($transaction->total_cogs, 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-right">Rp {{ number_format($transaction->subtotal - $transaction->discount_amount - $transaction->total_cogs, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-[#7f837e]">
                                    Belum ada transaksi dibayar pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="border-t border-[#e9ebf0] p-5">{{ $transactions->links() }}</div>
            @endif
        </div>

        {{-- TAB 2: RINCIAN KAS KELUAR --}}
        <div x-show="reportTab === 'cash_out'">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-xs">
                    <caption class="sr-only">Daftar kas keluar operasional sesuai periode laporan</caption>
                    <thead class="bg-[#f8f9fa] text-[10px] uppercase tracking-wider text-[#7f837e]">
                        <tr>
                            <th scope="col" class="px-5 py-4">ID & Waktu</th>
                            <th scope="col" class="px-5 py-4">Keperluan / Keterangan</th>
                            <th scope="col" class="px-5 py-4">Dicatat Oleh</th>
                            <th scope="col" class="px-5 py-4">Sesi Shift</th>
                            <th scope="col" class="px-5 py-4">Status</th>
                            <th scope="col" class="px-5 py-4 text-right">Nominal Kas Keluar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e9ebf0]">
                        @forelse($cashOutTransactions as $tx)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-[#111827]">#{{ $tx->id }}</p>
                                    <p class="mt-1 text-[11px] text-[#7f837e]">{{ $tx->created_at ? $tx->created_at->timezone($timezone)->format('d M Y H:i') : '—' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-[#111827]">{{ $tx->description ?: 'Pengeluaran Kas' }}</p>
                                </td>
                                <td class="px-5 py-4 text-[#53565a]">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-user text-gray-400 text-[10px]"></i>
                                        <span>{{ $tx->user?->name ?: 'Kasir / Staf' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-[#7f837e]">
                                    Shift #{{ $tx->cash_register_session_id }}
                                    @if($tx->session?->register?->name)
                                        <span class="text-[10px] text-gray-400">({{ $tx->session->register->name }})</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#fff0e6] text-[#df5826] border border-[#ffd2b8]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#df5826]"></span>
                                        KAS KELUAR
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right font-bold text-[#df5826] text-sm">
                                    - Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-[#7f837e]">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <div class="w-10 h-10 rounded-full bg-[#fff0e6] text-[#df5826] flex items-center justify-center">
                                            <i class="fas fa-arrow-up-from-bracket"></i>
                                        </div>
                                        <p class="font-semibold text-xs text-[#111827]">Belum ada kas keluar tercatat pada periode ini.</p>
                                        <p class="text-[11px] text-[#7f837e]">Gunakan tombol "Catat Pengeluaran" di atas untuk mencatat pengeluaran operasional.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($cashOutTransactions->count() > 0)
                        <tfoot class="bg-[#f8f9fa] border-t-2 border-[#e9ebf0]">
                            <tr>
                                <th colspan="5" class="px-5 py-3 text-xs font-bold text-[#53565a] uppercase">Total Kas Keluar Periode Ini</th>
                                <th class="px-5 py-3 text-right text-xs font-black text-[#df5826]">
                                    - Rp {{ number_format($totalCashOut ?? 0, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
            @if($cashOutTransactions->hasPages())
                <div class="border-t border-[#e9ebf0] p-5">{{ $cashOutTransactions->links() }}</div>
            @endif
        </div>
    </section>
    
    <p class="mt-4 text-[11px] leading-relaxed text-[#7f837e]">Periode penjualan mengikuti tanggal pesanan. Penerimaan mengikuti tanggal pembayaran. Transaksi batal dan pembayaran dibatalkan dikecualikan. HPP nol tetap ditampilkan nol; laba kotor belum dikurangi biaya operasional.</p>

    {{-- POP-UP MODAL CATAT KAS KELUAR --}}
    <x-cash-out-modal />
</div>
