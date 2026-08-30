<!-- Report Tab -->
<div x-show="currentTab === 'report'" class="space-y-6" x-cloak>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#164A35]">Laporan Keuangan</h2>
            <p class="text-sm text-[#777873] mt-1">Ringkasan penjualan Harian, Mingguan, dan Bulanan</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-[#E3E1DC] text-[#164A35] rounded-xl font-bold hover:bg-gray-50 transition-colors flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Jurnal
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Harian -->
        <div class="bg-white rounded-2xl p-6 border border-[#E3E1DC] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#DDEBDD] flex items-center justify-center text-[#164A35]">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#777873]">Hari Ini</p>
                    <h3 class="text-2xl font-black text-[#202522]" x-text="'Rp ' + formatNum(reportData.daily.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-[#777873]">
                <span class="font-bold text-[#164A35]" x-text="reportData.daily.count"></span> transaksi berhasil
            </div>
        </div>

        <!-- Mingguan -->
        <div class="bg-white rounded-2xl p-6 border border-[#E3E1DC] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#FFF3E0] flex items-center justify-center text-[#D97A32]">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#777873]">Minggu Ini</p>
                    <h3 class="text-2xl font-black text-[#202522]" x-text="'Rp ' + formatNum(reportData.weekly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-[#777873]">
                <span class="font-bold text-[#D97A32]" x-text="reportData.weekly.count"></span> transaksi berhasil
            </div>
        </div>

        <!-- Bulanan -->
        <div class="bg-white rounded-2xl p-6 border border-[#E3E1DC] shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-[#E8F0FE] flex items-center justify-center text-[#1A73E8]">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[#777873]">Bulan Ini</p>
                    <h3 class="text-2xl font-black text-[#202522]" x-text="'Rp ' + formatNum(reportData.monthly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-[#777873]">
                <span class="font-bold text-[#1A73E8]" x-text="reportData.monthly.count"></span> transaksi berhasil
            </div>
        </div>
    </div>

    <!-- Jurnal Transaksi -->
    <div class="bg-white rounded-2xl border border-[#E3E1DC] overflow-hidden shadow-sm">
        <div class="p-6 border-b border-[#E3E1DC] flex flex-col sm:flex-row justify-between gap-4">
            <h3 class="font-bold text-lg text-[#202522]">Jurnal Transaksi (Selesai)</h3>
            <div class="flex gap-2">
                <select x-model="reportPeriod" class="text-sm border-[#E3E1DC] rounded-xl focus:ring-[#164A35] focus:border-[#164A35]">
                    <option value="all">Semua Waktu</option>
                    <option value="daily">Hari Ini</option>
                    <option value="weekly">Minggu Ini</option>
                    <option value="monthly">Bulan Ini</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F8F7F3] text-[#777873] uppercase font-bold text-xs">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">Pemesan</th>
                        <th class="px-6 py-4">Tipe / Meja</th>
                        <th class="px-6 py-4 text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E1DC]">
                    <template x-for="order in filteredReportOrders" :key="order.id">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-[#777873]" x-text="formatDate(order.created_at)"></td>
                            <td class="px-6 py-4 font-bold text-[#164A35]" x-text="'#' + order.id"></td>
                            <td class="px-6 py-4 font-semibold text-[#202522]" x-text="order.customer_name"></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg text-xs font-bold" 
                                    :class="order.type === 'Dine-in' ? 'bg-[#DDEBDD] text-[#164A35]' : 'bg-[#FFF3E0] text-[#D97A32]'"
                                    x-text="order.type === 'Dine-in' ? 'Meja ' + (order.table ? order.table.name : '-') : 'Takeaway'">
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-right text-[#202522]" x-text="formatNum(order.total)"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredReportOrders.length === 0">
                        <td colspan="5" class="px-6 py-12 text-center text-[#777873]">Belum ada transaksi di periode ini.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
