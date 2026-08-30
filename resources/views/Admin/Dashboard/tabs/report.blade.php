<!-- Report Tab -->
<div x-show="currentTab === 'report'" class="flex-grow p-8 lg:p-10 bg-transparent overflow-y-auto hide-scroll space-y-8" x-cloak>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#777873]">Ringkasan penjualan Harian, Mingguan, dan Bulanan</p>
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
        <div class="bg-gradient-to-br from-[#164A35] to-[#1e5f44] rounded-[24px] p-8 text-white shadow-[0_12px_32px_rgba(22,74,53,0.15)] relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10 shadow-inner">
                    <i class="fas fa-sun text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Pendapatan Hari Ini</p>
                    <h3 class="text-3xl font-black tracking-tight text-white drop-shadow-sm" x-text="'Rp ' + formatNum(reportData.daily.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/10 pt-4 mt-6">
                <i class="fas fa-receipt text-xs text-white/80"></i>
                <span><strong class="text-white" x-text="reportData.daily.count"></strong> transaksi sukses</span>
            </div>
        </div>

        <!-- Mingguan -->
        <div class="bg-gradient-to-br from-[#D97A32] to-[#c76922] rounded-[24px] p-8 text-white shadow-[0_12px_32px_rgba(217,122,50,0.15)] relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10 shadow-inner">
                    <i class="fas fa-calendar-week text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Pendapatan Minggu Ini</p>
                    <h3 class="text-3xl font-black tracking-tight text-white drop-shadow-sm" x-text="'Rp ' + formatNum(reportData.weekly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/10 pt-4 mt-6">
                <i class="fas fa-receipt text-xs text-white/80"></i>
                <span><strong class="text-white" x-text="reportData.weekly.count"></strong> transaksi sukses</span>
            </div>
        </div>

        <!-- Bulanan -->
        <div class="bg-gradient-to-br from-[#1E5A7A] to-[#154660] rounded-[24px] p-8 text-white shadow-[0_12px_32px_rgba(30,90,122,0.15)] relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
            <div class="absolute -right-8 -top-8 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl group-hover:scale-125 transition-transform duration-700"></div>
            <div class="absolute -left-8 -bottom-8 w-32 h-32 bg-white opacity-5 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md border border-white/10 shadow-inner">
                    <i class="fas fa-calendar-alt text-2xl text-white"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-white/80 mb-1">Pendapatan Bulan Ini</p>
                    <h3 class="text-3xl font-black tracking-tight text-white drop-shadow-sm" x-text="'Rp ' + formatNum(reportData.monthly.total)"></h3>
                </div>
            </div>
            <div class="text-sm text-white/80 relative z-10 flex items-center gap-2 border-t border-white/10 pt-4 mt-6">
                <i class="fas fa-receipt text-xs text-white/80"></i>
                <span><strong class="text-white" x-text="reportData.monthly.count"></strong> transaksi sukses</span>
            </div>
        </div>
    </div>

    <!-- Jurnal Transaksi -->
    <div class="bg-white rounded-[24px] border border-[#E3E1DC] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)]">
        <div class="p-6 md:p-8 border-b border-[#E3E1DC] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
            <div>
                <h3 class="font-bold text-xl text-[#202522]">Jurnal Transaksi Detail</h3>
                <p class="text-sm text-[#777873] mt-1">Menampilkan transaksi berstatus Selesai</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-[#777873]">Periode:</span>
                <!-- Custom Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center justify-between w-44 text-sm font-bold border border-[#E3E1DC] bg-white rounded-xl focus:ring-4 focus:ring-[#164A35]/10 focus:border-[#164A35] py-2.5 px-5 shadow-sm transition-all hover:bg-gray-50 hover:border-gray-300">
                        <span x-text="reportPeriod === 'daily' ? 'Hari Ini' : (reportPeriod === 'weekly' ? 'Minggu Ini' : (reportPeriod === 'monthly' ? 'Bulan Ini' : 'Semua Waktu'))" class="text-[#202522]"></span>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-2 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-[0_12px_40px_rgba(0,0,0,0.12)] border border-[#E3E1DC] py-2 z-50 overflow-hidden">
                        <button type="button" @click="reportPeriod = 'daily'; open = false" class="w-full text-left px-5 py-3 text-sm font-semibold transition-colors flex items-center justify-between group" :class="reportPeriod === 'daily' ? 'bg-[#F8F7F3] text-[#164A35]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <span>Hari Ini</span>
                            <i class="fas fa-check text-[#164A35] text-xs" x-show="reportPeriod === 'daily'"></i>
                        </button>
                        <button type="button" @click="reportPeriod = 'weekly'; open = false" class="w-full text-left px-5 py-3 text-sm font-semibold transition-colors flex items-center justify-between group" :class="reportPeriod === 'weekly' ? 'bg-[#F8F7F3] text-[#164A35]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <span>Minggu Ini</span>
                            <i class="fas fa-check text-[#164A35] text-xs" x-show="reportPeriod === 'weekly'"></i>
                        </button>
                        <button type="button" @click="reportPeriod = 'monthly'; open = false" class="w-full text-left px-5 py-3 text-sm font-semibold transition-colors flex items-center justify-between group" :class="reportPeriod === 'monthly' ? 'bg-[#F8F7F3] text-[#164A35]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <span>Bulan Ini</span>
                            <i class="fas fa-check text-[#164A35] text-xs" x-show="reportPeriod === 'monthly'"></i>
                        </button>
                        <div class="h-px bg-[#E3E1DC] my-2 mx-3"></div>
                        <button type="button" @click="reportPeriod = 'all'; open = false" class="w-full text-left px-5 py-3 text-sm font-semibold transition-colors flex items-center justify-between group" :class="reportPeriod === 'all' ? 'bg-[#F8F7F3] text-[#164A35]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'">
                            <span>Semua Waktu</span>
                            <i class="fas fa-check text-[#164A35] text-xs" x-show="reportPeriod === 'all'"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#F8F7F3]/50 text-[#777873] uppercase font-bold text-[11px] tracking-wider border-b border-[#E3E1DC]">
                    <tr>
                        <th class="px-6 md:px-8 py-5">Waktu</th>
                        <th class="px-6 md:px-8 py-5">ID Transaksi</th>
                        <th class="px-6 md:px-8 py-5">Pelanggan & Item</th>
                        <th class="px-6 md:px-8 py-5">Tipe Pesanan</th>
                        <th class="px-6 md:px-8 py-5 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E3E1DC] bg-white">
                    <template x-for="order in filteredReportOrders" :key="order.id">
                        <tr class="hover:bg-[#F8F7F3] transition-colors group">
                            <td class="px-6 md:px-8 py-5 text-[#777873] whitespace-nowrap">
                                <span class="font-bold text-[#202522]" x-text="new Date(order.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})"></span><br>
                                <span class="text-xs font-medium" x-text="new Date(order.created_at).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'})"></span>
                            </td>
                            <td class="px-6 md:px-8 py-5">
                                <span class="font-black text-[#164A35] bg-[#DDEBDD] px-2.5 py-1.5 rounded-lg text-xs tracking-wide" x-text="'#' + order.id"></span>
                            </td>
                            <td class="px-6 md:px-8 py-5">
                                <div class="font-bold text-[#202522] text-[15px]" x-text="order.customer_name"></div>
                                <div class="text-xs font-medium text-[#777873] mt-1" x-text="order.items ? order.items.length + ' item menu' : '0 item'"></div>
                            </td>
                            <td class="px-6 md:px-8 py-5">
                                <span class="px-3.5 py-1.5 rounded-xl text-xs font-bold inline-flex items-center gap-2" 
                                    :class="order.type === 'Dine-in' ? 'bg-[#DDEBDD] text-[#164A35]' : 'bg-[#FFF3E0] text-[#D97A32]'">
                                    <i :class="order.type === 'Dine-in' ? 'fas fa-chair' : 'fas fa-shopping-bag'"></i>
                                    <span x-text="order.type === 'Dine-in' ? 'Meja ' + (order.table ? order.table.name : '-') : 'Takeaway'"></span>
                                </span>
                            </td>
                            <td class="px-6 md:px-8 py-5 font-black text-right text-base text-[#202522]" x-text="'Rp ' + formatNum(order.total)"></td>
                        </tr>
                    </template>
                    <tr x-show="filteredReportOrders.length === 0">
                        <td colspan="5" class="px-6 md:px-8 py-20 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#F8F7F3] text-[#E3E1DC] mb-5 shadow-inner">
                                <i class="fas fa-receipt text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-[#202522] mb-1">Tidak ada data</h4>
                            <p class="text-[#777873] font-medium text-sm">Belum ada transaksi di periode ini.</p>
                        </td>
                    </tr>
                </tbody>
                <!-- Footer Total -->
                <tfoot class="bg-[#F8F7F3]/80 border-t-2 border-[#E3E1DC]" x-show="filteredReportOrders.length > 0">
                    <tr>
                        <td colspan="4" class="px-6 md:px-8 py-5 text-right font-bold text-[#777873] uppercase tracking-wider text-[11px]">Total Pendapatan Periode Ini</td>
                        <td class="px-6 md:px-8 py-5 text-right font-black text-xl text-[#164A35]" x-text="'Rp ' + formatNum(filteredReportOrders.reduce((sum, o) => sum + Number(o.total||0), 0))"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
