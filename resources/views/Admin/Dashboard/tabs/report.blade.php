<!-- Report Tab -->
<div x-show="currentTab === 'report'" class="space-y-6" x-cloak>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#164A35]">Laporan Keuangan</h2>
            <p class="text-sm text-[#777873] mt-1">Ringkasan penjualan Harian, Mingguan, dan Bulanan</p>
        </div>
        <div class="flex gap-2">
            <button @click="exportCSV" class="px-4 py-2 bg-[#F8F7F3] border border-[#E3E1DC] text-[#777873] rounded-xl font-bold hover:bg-gray-100 transition-colors flex items-center gap-2">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-[#164A35] text-white rounded-xl font-bold hover:bg-[#0f3526] transition-colors shadow-[0_4px_12px_rgba(22,74,53,0.2)] flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Jurnal
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Harian -->
        <div class="bg-gradient-to-br from-[#164A35] to-[#1e5a42] rounded-2xl p-6 text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-sun text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl font-black tracking-tight" x-text="'Rp ' + formatNum(reportData.daily.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/20 pt-3 mt-2">
                <i class="fas fa-receipt text-xs"></i>
                <span><strong class="text-white" x-text="reportData.daily.count"></strong> transaksi sukses</span>
            </div>
        </div>

        <!-- Mingguan -->
        <div class="bg-gradient-to-br from-[#D97A32] to-[#e88a42] rounded-2xl p-6 text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80">Pendapatan Minggu Ini</p>
                    <h3 class="text-2xl font-black tracking-tight" x-text="'Rp ' + formatNum(reportData.weekly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/20 pt-3 mt-2">
                <i class="fas fa-receipt text-xs"></i>
                <span><strong class="text-white" x-text="reportData.weekly.count"></strong> transaksi sukses</span>
            </div>
        </div>

        <!-- Bulanan -->
        <div class="bg-gradient-to-br from-[#1E5A7A] to-[#287095] rounded-2xl p-6 text-white shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="flex items-center gap-4 mb-4 relative z-10">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80">Pendapatan Bulan Ini</p>
                    <h3 class="text-2xl font-black tracking-tight" x-text="'Rp ' + formatNum(reportData.monthly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/20 pt-3 mt-2">
                <i class="fas fa-receipt text-xs"></i>
                <span><strong class="text-white" x-text="reportData.monthly.count"></strong> transaksi sukses</span>
            </div>
        </div>
    </div>

    <!-- Jurnal Transaksi -->
    <div class="bg-white rounded-2xl border border-[#E3E1DC] overflow-hidden shadow-sm">
        <div class="p-6 border-b border-[#E3E1DC] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[#F8F7F3]/50">
            <div>
                <h3 class="font-bold text-lg text-[#202522]">Jurnal Transaksi Detail</h3>
                <p class="text-xs text-[#777873] mt-0.5">Menampilkan transaksi berstatus Selesai</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-[#777873]">Periode:</span>
                <select x-model="reportPeriod" class="text-sm font-semibold border-[#E3E1DC] bg-white rounded-xl focus:ring-[#164A35] focus:border-[#164A35] py-2 px-4 shadow-sm">
                    <option value="daily">Hari Ini</option>
                    <option value="weekly">Minggu Ini</option>
                    <option value="monthly">Bulan Ini</option>
                    <option value="all">Semua Waktu</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white text-[#777873] uppercase font-bold text-[11px] tracking-wider border-b border-[#E3E1DC]">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">ID Transaksi</th>
                        <th class="px-6 py-4">Pelanggan & Item</th>
                        <th class="px-6 py-4">Tipe Pesanan</th>
                        <th class="px-6 py-4 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E1DC] bg-white">
                    <template x-for="order in filteredReportOrders" :key="order.id">
                        <tr class="hover:bg-[#F8F7F3] transition-colors group">
                            <td class="px-6 py-4 text-[#777873] whitespace-nowrap">
                                <span class="font-semibold text-[#202522]" x-text="new Date(order.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})"></span><br>
                                <span class="text-xs" x-text="new Date(order.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-[#164A35] bg-[#DDEBDD] px-2 py-1 rounded-md" x-text="'#' + order.id"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-[#202522]" x-text="order.customer_name"></div>
                                <div class="text-xs text-[#777873] mt-1" x-text="order.items ? order.items.length + ' item menu' : '0 item'"></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5" 
                                    :class="order.type === 'Dine-in' ? 'bg-[#DDEBDD] text-[#164A35]' : 'bg-[#FFF3E0] text-[#D97A32]'">
                                    <i :class="order.type === 'Dine-in' ? 'fas fa-chair' : 'fas fa-shopping-bag'"></i>
                                    <span x-text="order.type === 'Dine-in' ? 'Meja ' + (order.table ? order.table.name : '-') : 'Takeaway'"></span>
                                </span>
                            </td>
                            <td class="px-6 py-4 font-black text-right text-[15px] text-[#202522]" x-text="'Rp ' + formatNum(order.total)"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredReportOrders.length === 0">
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#F8F7F3] text-[#E3E1DC] mb-4">
                                <i class="fas fa-receipt text-2xl"></i>
                            </div>
                            <p class="text-[#777873] font-medium">Belum ada transaksi di periode ini.</p>
                        </td>
                    </tr>
                </tbody>
                <!-- Footer Total -->
                <tfoot class="bg-[#F8F7F3] border-t-2 border-[#E3E1DC]" x-show="filteredReportOrders.length > 0">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-bold text-[#777873] uppercase tracking-wider text-xs">Total Pendapatan Periode Ini</td>
                        <td class="px-6 py-4 text-right font-black text-lg text-[#164A35]" x-text="'Rp ' + formatNum(filteredReportOrders.reduce((sum, o) => sum + Number(o.total||0), 0))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
