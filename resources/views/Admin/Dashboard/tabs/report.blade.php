<!-- Report Tab -->
<div x-show="currentTab === 'report'" class="flex-grow p-4 md:p-[24px] bg-[#f8f9fa] overflow-y-auto hide-scroll space-y-6 min-h-screen" x-cloak>
    
    <!-- Top Notice Alert (Mint Banner) -->
    <div class="bg-[#ecfdf5] border border-[#a7f3d0] rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-[#065f46] shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-[#d1fae5] flex items-center justify-center shrink-0 text-emerald-600">
                <i class="fas fa-check-circle text-base"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-[#065f46]">Laporan Keuangan & Rekonsiliasi Otomatis Aktif</p>
                <p class="text-xs text-emerald-700/80 mt-0.5">Data penjualan, margin keuntungan, dan estimasi beban HPP disinkronkan langsung dari kasir POS dan resep bahan baku.</p>
            </div>
        </div>
        <a href="{{ route('admin.hpp.index') }}" class="inline-flex items-center gap-2 text-xs font-bold bg-white text-emerald-800 hover:text-emerald-900 border border-emerald-200 px-3.5 py-2 rounded-xl shadow-xs hover:bg-emerald-50 transition-all shrink-0">
            <i class="fas fa-calculator text-emerald-600"></i>
            <span>Kelola Kalkulator HPP</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <!-- Filter & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-[#e9ebf0] shadow-xs">
        <!-- Period Selector Dropdown -->
        <div class="flex items-center gap-3 flex-wrap">
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" type="button" 
                    class="flex items-center gap-2.5 px-4 py-2.5 bg-[#f8f9fa] border border-[#e2e8f0] hover:border-gray-300 rounded-xl text-sm font-bold text-gray-800 transition-all shadow-xs">
                    <i class="far fa-calendar-alt text-[#df5826]"></i>
                    <span x-text="reportPeriodLabel()"></span>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 ml-1 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition.opacity.duration.200ms x-cloak 
                    class="absolute left-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-[#e9ebf0] py-2 z-50">
                    <div class="px-4 py-1.5 text-[10px] font-black uppercase tracking-wider text-gray-400">Pilih Rentang Waktu</div>
                    <button type="button" @click="reportPeriod = 'daily'; open = false" 
                        class="w-full text-left px-4 py-2.5 text-xs font-bold transition-colors flex items-center justify-between"
                        :class="reportPeriod === 'daily' ? 'bg-[#fff7ed] text-[#df5826]' : 'text-gray-700 hover:bg-gray-50'">
                        <span>Hari Ini</span>
                        <i class="fas fa-check text-xs" x-show="reportPeriod === 'daily'"></i>
                    </button>
                    <button type="button" @click="reportPeriod = 'weekly'; open = false" 
                        class="w-full text-left px-4 py-2.5 text-xs font-bold transition-colors flex items-center justify-between"
                        :class="reportPeriod === 'weekly' ? 'bg-[#fff7ed] text-[#df5826]' : 'text-gray-700 hover:bg-gray-50'">
                        <span>Minggu Ini</span>
                        <i class="fas fa-check text-xs" x-show="reportPeriod === 'weekly'"></i>
                    </button>
                    <button type="button" @click="reportPeriod = 'monthly'; open = false" 
                        class="w-full text-left px-4 py-2.5 text-xs font-bold transition-colors flex items-center justify-between"
                        :class="reportPeriod === 'monthly' ? 'bg-[#fff7ed] text-[#df5826]' : 'text-gray-700 hover:bg-gray-50'">
                        <span>Bulan Ini (30 Hari)</span>
                        <i class="fas fa-check text-xs" x-show="reportPeriod === 'monthly'"></i>
                    </button>
                    <div class="h-px bg-gray-100 my-1"></div>
                    <button type="button" @click="reportPeriod = 'all'; open = false" 
                        class="w-full text-left px-4 py-2.5 text-xs font-bold transition-colors flex items-center justify-between"
                        :class="reportPeriod === 'all' ? 'bg-[#fff7ed] text-[#df5826]' : 'text-gray-700 hover:bg-gray-50'">
                        <span>Semua Waktu</span>
                        <i class="fas fa-check text-xs" x-show="reportPeriod === 'all'"></i>
                    </button>
                </div>
            </div>

            <!-- Real-time badge -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Live Sinkron</span>
            </span>
        </div>

        <!-- Action Buttons (Export & Print) -->
        <div class="flex items-center gap-2.5">
            <button @click="exportCSV" type="button" 
                class="px-4 py-2.5 bg-white border border-[#e2e8f0] text-gray-700 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition-all shadow-xs flex items-center gap-2 text-xs">
                <i class="fas fa-download text-emerald-600"></i>
                <span>Export CSV</span>
            </button>
            <button onclick="window.print()" type="button" 
                class="px-4 py-2.5 bg-[#df5826] text-white rounded-xl font-bold hover:bg-[#c74a1d] transition-all shadow-xs flex items-center gap-2 text-xs">
                <i class="fas fa-print"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Cards (Sparta Themed) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pendapatan -->
        <div class="bg-white rounded-[20px] p-5 border border-[#e9ebf0] shadow-xs hover:border-[#df5826]/30 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pendapatan</span>
                <div class="w-9 h-9 rounded-xl bg-[#fff7ed] text-[#df5826] flex items-center justify-center text-sm shadow-inner group-hover:scale-105 transition-transform">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight" x-text="'Rp ' + formatNum(reportMetrics.revenue)">Rp 0</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-gray-100 pt-3">
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                    <i class="fas fa-arrow-up text-[10px]"></i> +18.4%
                </span>
                <span class="text-gray-400 font-medium">vs bulan lalu</span>
            </div>
        </div>

        <!-- Card 2: Estimasi HPP & Beban -->
        <div class="bg-white rounded-[20px] p-5 border border-[#e9ebf0] shadow-xs hover:border-slate-300 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Estimasi HPP & Beban</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm shadow-inner group-hover:scale-105 transition-transform">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight" x-text="'Rp ' + formatNum(reportMetrics.cogs)">Rp 0</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-gray-100 pt-3">
                <span class="inline-flex items-center gap-1 font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                    <span x-text="reportMetrics.cogsPercent + '%'">0%</span> Rasio Biaya
                </span>
                <span class="text-gray-400 font-medium">Bahan & Overhead</span>
            </div>
        </div>

        <!-- Card 3: Estimasi Laba Bersih -->
        <div class="bg-white rounded-[20px] p-5 border border-[#e9ebf0] shadow-xs hover:border-emerald-300 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Estimasi Laba Bersih</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shadow-inner group-hover:scale-105 transition-transform">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1">
                <h3 class="text-2xl font-black text-emerald-700 tracking-tight" x-text="'Rp ' + formatNum(reportMetrics.profit)">Rp 0</h3>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-gray-100 pt-3">
                <span class="inline-flex items-center gap-1 font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                    <span x-text="reportMetrics.marginPercent + '%'">0%</span> Net Margin
                </span>
                <span class="text-gray-400 font-medium">Profitabilitas</span>
            </div>
        </div>

        <!-- Card 4: Total Transaksi Selesai -->
        <div class="bg-white rounded-[20px] p-5 border border-[#e9ebf0] shadow-xs hover:border-amber-300 transition-all group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Transaksi Selesai</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shadow-inner group-hover:scale-105 transition-transform">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight" x-text="reportMetrics.count">0</h3>
                <span class="text-sm font-bold text-gray-400">Order</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs border-t border-gray-100 pt-3">
                <span class="text-gray-500 font-semibold truncate">
                    AOV: <strong class="text-gray-800" x-text="'Rp ' + formatNum(reportMetrics.aov)">Rp 0</strong>
                </span>
                <span class="text-gray-400 font-medium">Rata-rata/Order</span>
            </div>
        </div>
    </div>

    <!-- Visual Analytics Grid: Bar Chart (Left) + Donut Gauge (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Financial Overview Bar Chart (~65% width) -->
        <div class="lg:col-span-8 bg-white rounded-[20px] border border-[#e9ebf0] p-6 shadow-xs flex flex-col justify-between relative">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Ikhtisar Arus Pendapatan vs Beban HPP</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Perbandingan pertumbuhan omset penjualan dengan estimasi biaya pokok bahan per bulan</p>
                    </div>
                    <!-- Legend -->
                    <div class="flex items-center gap-4 text-xs font-bold">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#df5826]"></span>
                            <span class="text-gray-700">Pendapatan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#cbd5e1]"></span>
                            <span class="text-gray-500">Estimasi HPP</span>
                        </div>
                    </div>
                </div>

                <!-- Interactive Bar Graph Container -->
                <div class="relative pt-6 pb-2">
                    
                    <!-- Chart Graphic with 9 Month Columns -->
                    <div class="h-64 flex items-end justify-between gap-2 sm:gap-3 px-2 border-b border-gray-100">
                        <template x-for="(bar, index) in financialOverviewBars" :key="index">
                            <div class="flex-1 flex flex-col items-center h-full justify-end group cursor-pointer relative"
                                @mouseenter="reportActiveBarIndex = index"
                                @mouseleave="reportActiveBarIndex = null">
                                
                                <!-- Floating Tooltip Card on Hover or Active -->
                                <div x-show="reportActiveBarIndex === index || (reportActiveBarIndex === null && bar.isCurrent)"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    class="absolute -top-28 z-30 bg-white rounded-2xl shadow-xl border border-gray-100 p-3 min-w-[170px] pointer-events-none transform -translate-x-1/2 left-1/2">
                                    <div class="text-[11px] font-black text-gray-900 mb-1 flex items-center justify-between">
                                        <span x-text="bar.fullLabel"></span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700" x-text="bar.marginPercent + '% Margin'"></span>
                                    </div>
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 text-gray-600">
                                                <span class="w-2 h-2 rounded-full bg-[#df5826]"></span>
                                                <span class="font-medium">Omset:</span>
                                            </div>
                                            <span class="font-bold text-gray-900" x-text="'Rp ' + formatNum(bar.revenue)"></span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-1.5 text-gray-500">
                                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                                <span class="font-medium">HPP:</span>
                                            </div>
                                            <span class="font-semibold text-slate-700" x-text="'Rp ' + formatNum(bar.cogs)"></span>
                                        </div>
                                    </div>
                                    <!-- Tooltip caret -->
                                    <div class="w-2.5 h-2.5 bg-white border-r border-b border-gray-100 transform rotate-45 absolute -bottom-1.5 left-1/2 -translate-x-1/2"></div>
                                </div>

                                <!-- Bar Column Pair -->
                                <div class="w-full flex items-end justify-center gap-1 sm:gap-1.5 h-full">
                                    <!-- Revenue Bar (Orange) -->
                                    <div class="w-full max-w-[18px] sm:max-w-[24px] rounded-t-lg transition-all duration-300"
                                        :class="bar.isCurrent ? 'bg-[#df5826] shadow-md shadow-[#df5826]/30' : 'bg-[#df5826]/75 group-hover:bg-[#df5826]'"
                                        :style="'height: ' + Math.max(12, Math.round((bar.revenue / 10000000) * 100)) + '%'">
                                    </div>
                                    <!-- HPP Bar (Slate) -->
                                    <div class="w-full max-w-[18px] sm:max-w-[24px] rounded-t-lg transition-all duration-300"
                                        :class="bar.isCurrent ? 'bg-[#94a3b8]' : 'bg-[#cbd5e1] group-hover:bg-[#94a3b8]'"
                                        :style="'height: ' + Math.max(8, Math.round((bar.cogs / 10000000) * 100)) + '%'">
                                    </div>
                                </div>

                                <!-- Month Label -->
                                <span class="text-[11px] font-bold mt-2 transition-colors"
                                    :class="bar.isCurrent ? 'text-[#df5826]' : 'text-gray-400 group-hover:text-gray-700'"
                                    x-text="bar.month"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Chart Gridlines & Baseline Info -->
                    <div class="flex items-center justify-between text-[10px] text-gray-400 mt-2 px-2">
                        <span>Rp 0</span>
                        <span>Estimasi Skala Rp 10.000.000+</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Insight Pill -->
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <i class="fas fa-info-circle text-[#df5826]"></i>
                    <span>Arahkan kursor pada batang grafik untuk melihat rincian omset dan rasio beban per bulan.</span>
                </div>
                <span class="font-bold text-[#df5826] cursor-pointer hover:underline" @click="reportPeriod = 'monthly'">
                    Lihat Bulan Berjalan &rarr;
                </span>
            </div>
        </div>

        <!-- Right: Profit Margin & Cost Breakdown (Donut Gauge) (~35% width) -->
        <div class="lg:col-span-4 bg-white rounded-[20px] border border-[#e9ebf0] p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-black text-gray-900 tracking-tight">Rasio Margin & Profit</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Efisiensi laba bersih terhadap omset kotor</p>
                    </div>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                        <i class="fas fa-pie-chart"></i>
                    </span>
                </div>

                <!-- Circular Donut Chart -->
                <div class="relative py-4 flex flex-col items-center justify-center">
                    <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 160 160">
                        <!-- Background Circle Track -->
                        <circle cx="80" cy="80" r="62" stroke="#f1f5f9" stroke-width="14" fill="transparent" />
                        <!-- Secondary Track (HPP) -->
                        <circle cx="80" cy="80" r="62" stroke="#fed7aa" stroke-width="14" fill="transparent"
                            stroke-dasharray="390"
                            :stroke-dashoffset="390 - (390 * reportMetrics.cogsPercent / 100)"
                            class="transition-all duration-700 ease-out" />
                        <!-- Active Stroke Ring (Profit Margin) -->
                        <circle cx="80" cy="80" r="62" stroke="#df5826" stroke-width="14" fill="transparent"
                            stroke-linecap="round"
                            stroke-dasharray="390"
                            :stroke-dashoffset="390 - (390 * reportMetrics.marginPercent / 100)"
                            class="transition-all duration-700 ease-out" />
                    </svg>

                    <!-- Center Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Laba</span>
                        <h4 class="text-xl font-black text-gray-900 tracking-tight mt-0.5" x-text="'Rp ' + formatNum(reportMetrics.profit)">Rp 0</h4>
                        <span class="mt-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-200"
                            x-text="reportMetrics.marginPercent + '% Margin'">62% Margin</span>
                    </div>
                </div>

                <!-- Sub-metrics Breakdown Underneath -->
                <div class="grid grid-cols-2 gap-3 mt-4 pt-4 border-t border-gray-100">
                    <div class="bg-[#f8f9fa] p-3 rounded-xl border border-gray-100">
                        <div class="text-[11px] font-medium text-gray-500">HPP Bahan Baku</div>
                        <div class="text-sm font-black text-gray-900 mt-0.5" x-text="'Rp ' + formatNum(reportMetrics.cogs)">Rp 0</div>
                        <div class="text-[10px] font-bold text-slate-500 mt-1" x-text="reportMetrics.cogsPercent + '% dari omset'">38%</div>
                    </div>
                    <div class="bg-[#f8f9fa] p-3 rounded-xl border border-gray-100">
                        <div class="text-[11px] font-medium text-gray-500">Rata-rata Order</div>
                        <div class="text-sm font-black text-gray-900 mt-0.5" x-text="'Rp ' + formatNum(reportMetrics.aov)">Rp 0</div>
                        <div class="text-[10px] font-bold text-emerald-600 mt-1">Nilai per Transaksi</div>
                    </div>
                </div>
            </div>

            <!-- Kalkulator HPP Link Button -->
            <div class="mt-5">
                <a href="{{ route('admin.hpp.index') }}" class="w-full py-2.5 px-4 rounded-xl text-center text-xs font-bold bg-[#fff7ed] text-[#df5826] border border-[#fed7aa] hover:bg-[#ffedd5] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-calculator"></i>
                    <span>Buka Detail Kalkulator HPP & Resep</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Jurnal & Riwayat Transaksi Table (Full Section) -->
    <div class="bg-white rounded-[20px] border border-[#e9ebf0] shadow-xs overflow-hidden">
        
        <!-- Table Header & Filter Bar -->
        <div class="p-5 border-b border-[#e9ebf0] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-black text-lg text-gray-900 tracking-tight">Jurnal & Riwayat Transaksi</h3>
                <p class="text-xs text-gray-500 mt-0.5">Daftar transaksi kasir berserta rincian HPP dan estimasi margin keuntungan</p>
            </div>

            <!-- Search and Filter Inputs -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Search Box -->
                <div class="relative w-full sm:w-60">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" x-model="reportSearch" placeholder="Cari ID, nama, item..." 
                        class="w-full pl-9 pr-3.5 py-2 bg-[#f8f9fa] border border-[#e2e8f0] rounded-xl text-xs font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] transition-all">
                </div>

                <!-- Filter Tipe Pesanan -->
                <select x-model="reportTypeFilter" 
                    class="py-2 px-3 bg-[#f8f9fa] border border-[#e2e8f0] rounded-xl text-xs font-bold text-gray-700 focus:bg-white focus:border-[#df5826] transition-all cursor-pointer">
                    <option value="all">Semua Tipe</option>
                    <option value="dine_in">Dine In</option>
                    <option value="takeaway">Takeaway</option>
                </select>

                <!-- Filter Metode Bayar -->
                <select x-model="reportPaymentFilter" 
                    class="py-2 px-3 bg-[#f8f9fa] border border-[#e2e8f0] rounded-xl text-xs font-bold text-gray-700 focus:bg-white focus:border-[#df5826] transition-all cursor-pointer">
                    <option value="all">Semua Pembayaran</option>
                    <option value="cash">Tunai (Cash)</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f8f9fa] text-gray-500 uppercase font-black text-[10px] tracking-wider border-b border-[#e9ebf0]">
                    <tr>
                        <th class="px-5 py-4">No. Transaksi</th>
                        <th class="px-5 py-4">Pelanggan & Kasir</th>
                        <th class="px-5 py-4">Tipe & Metode</th>
                        <th class="px-5 py-4">Waktu</th>
                        <th class="px-5 py-4 text-right">Pendapatan</th>
                        <th class="px-5 py-4 text-right">Estimasi HPP</th>
                        <th class="px-5 py-4 text-right">Estimasi Laba</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-5 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    <template x-for="order in filteredReportOrders" :key="order.id">
                        <tr class="hover:bg-orange-50/20 transition-colors group">
                            <!-- No. Transaksi -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-black text-[#df5826] bg-[#fff7ed] px-2.5 py-1 rounded-lg text-xs tracking-wide" x-text="'#' + order.id"></span>
                            </td>

                            <!-- Pelanggan & Kasir -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-black text-[11px] flex items-center justify-center shrink-0 border border-slate-200"
                                        x-text="(order.customer || 'P').slice(0, 2).toUpperCase()">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900" x-text="order.customer || 'Pelanggan Umum'"></div>
                                        <div class="text-[11px] text-gray-400 max-w-[200px] truncate" 
                                            x-text="order.items && order.items.length ? order.items.map(i => i.qty + 'x ' + i.name).join(', ') : 'Pesanan Kasir'"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Tipe & Metode -->
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold"
                                        :class="order.type === 'Dine In' ? 'bg-[#fff7ed] text-[#df5826]' : 'bg-slate-100 text-slate-700'"
                                        x-text="order.type === 'Dine In' ? 'Dine In (M-' + (order.table || 'TA') + ')' : 'Takeaway'">
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-700" 
                                        x-text="order.payment_method || 'Tunai'"></span>
                                </div>
                            </td>

                            <!-- Waktu -->
                            <td class="px-5 py-4 whitespace-nowrap text-gray-500">
                                <div class="font-bold text-gray-800" x-text="new Date((order.time || '').replace(' ', 'T')).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric'})"></div>
                                <div class="text-[10px] text-gray-400" x-text="new Date((order.time || '').replace(' ', 'T')).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) + ' WIB'"></div>
                            </td>

                            <!-- Total Pendapatan -->
                            <td class="px-5 py-4 font-black text-right text-gray-900 whitespace-nowrap text-sm" x-text="'Rp ' + formatNum(order.total)"></td>

                            <!-- Estimasi HPP -->
                            <td class="px-5 py-4 font-semibold text-right text-slate-500 whitespace-nowrap" 
                                x-text="'Rp ' + formatNum(Number(order.total_cogs || 0) || Math.round(Number(order.total || 0) * 0.38))"></td>

                            <!-- Estimasi Laba -->
                            <td class="px-5 py-4 font-black text-right text-emerald-600 whitespace-nowrap" 
                                x-text="'+ Rp ' + formatNum(Math.max(0, Number(order.total || 0) - (Number(order.total_cogs || 0) || Math.round(Number(order.total || 0) * 0.38))))"></td>

                            <!-- Status -->
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Selesai
                                </span>
                            </td>

                            <!-- Aksi: Cetak Struk -->
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <button type="button" @click="window.open('/admin/api/orders/' + order.id + '/print', '_blank')" 
                                    title="Cetak Struk Transaksi"
                                    class="p-2 text-gray-500 hover:text-[#df5826] hover:bg-[#fff7ed] rounded-lg transition-colors">
                                    <i class="fas fa-print"></i>
                                </button>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredReportOrders.length === 0">
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#fff7ed] text-[#df5826] mb-3 shadow-inner">
                                <i class="fas fa-receipt text-2xl"></i>
                            </div>
                            <h4 class="text-base font-bold text-gray-900">Tidak ada catatan transaksi</h4>
                            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Tidak ditemukan transaksi yang sesuai dengan filter atau periode yang Anda tentukan.</p>
                        </td>
                    </tr>
                </tbody>

                <!-- Table Summary Footer -->
                <tfoot class="bg-[#f8f9fa] border-t-2 border-[#e9ebf0]" x-show="filteredReportOrders.length > 0">
                    <tr>
                        <td colspan="4" class="px-5 py-4 text-right font-black text-gray-500 uppercase tracking-wider text-[11px]">Total Pada Periode Ini:</td>
                        <td class="px-5 py-4 text-right font-black text-sm text-gray-900 whitespace-nowrap" x-text="'Rp ' + formatNum(reportMetrics.revenue)"></td>
                        <td class="px-5 py-4 text-right font-black text-xs text-slate-700 whitespace-nowrap" x-text="'Rp ' + formatNum(reportMetrics.cogs)"></td>
                        <td class="px-5 py-4 text-right font-black text-sm text-emerald-600 whitespace-nowrap" x-text="'Rp ' + formatNum(reportMetrics.profit)"></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
