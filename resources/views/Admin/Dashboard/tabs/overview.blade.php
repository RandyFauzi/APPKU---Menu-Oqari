<div x-show="currentTab === 'dashboard'" x-cloak class="flex-grow p-4 md:p-6 lg:p-7 overflow-y-auto hide-scroll bg-white font-['DM_Sans',sans-serif]" style="font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';">

    <!-- Top Action / Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-[25px] font-semibold text-[#111827] leading-[30px] tracking-tight">Dashboard</h1>
            <p class="text-[13px] text-[#7f837e] font-normal mt-1 leading-[18px]">Pantau penjualan, pesanan, dan aktivitas kafe hari ini.</p>
        </div>
        <div>
            <a href="{{ route('admin.pos.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#f9731f] to-[#ea580c] hover:from-[#ea580c] hover:to-[#c2410c] text-white text-[12px] font-semibold rounded-[8px] shadow-[0px_2px_4px_rgba(234,88,12,0.2)] transition-all transform active:scale-95">
                <img src="{{ asset('images/sparta/cart-btn-icon.svg') }}" class="w-[18px] h-[18px]" alt="">
                <span>Buat Pesanan</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-6">
        
        <!-- Card 1: Omzet Hari Ini -->
        <div class="relative bg-gradient-to-br from-[#f9731f] to-[#ea580c] border border-[#e9ebf0] rounded-[12px] p-4 text-white overflow-hidden shadow-[0px_4px_16px_rgba(249,115,31,0.25)] flex flex-col justify-between h-[116px]">
            <!-- Watermark illustrations -->
            <img src="{{ asset('images/sparta/cart-watermark.svg') }}" class="absolute -right-3 -top-2 w-36 h-36 opacity-15 pointer-events-none rotate-12" alt="">
            <img src="{{ asset('images/sparta/omzet-cup-bag.png') }}" class="absolute right-0 bottom-0 h-28 object-contain opacity-25 pointer-events-none" alt="">
            
            <div class="relative z-10">
                <p class="text-[12px] font-normal text-white/90 leading-[15px]">Omzet Hari Ini</p>
            </div>
            
            <div class="relative z-10 flex flex-col gap-1.5">
                <div class="text-[24px] font-medium text-white leading-tight">
                    <span x-text="formatOmzetShort(analytics?.revenue ?? 0)">Rp0</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold leading-tight"
                          :class="(analytics?.revenueChange ?? 0) >= 0 ? 'bg-[#eafbf2] text-[#00643b]' : 'bg-[#fff0f4] text-[#b91f50]'"
                          x-text="formatPctChange(analytics?.revenueChange ?? 0)">0%</span>
                    <span class="text-[12px] text-white/90 leading-tight"
                          x-text="'vs. kemarin (' + formatOmzetShort(analytics?.revenueYesterday ?? 0) + ')'">vs. kemarin (Rp0)</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Pesanan -->
        <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.03)] flex flex-col justify-between h-[116px] transition-all hover:border-[#f9731f]/40">
            <div class="flex items-center justify-between">
                <p class="text-[12px] font-normal text-[#53565a] leading-[15px]">Total Pesanan</p>
                <div class="w-6 h-6 flex items-center justify-center">
                    <img src="{{ asset('images/sparta/box-bold-duotone.svg') }}" class="w-5 h-5" alt="">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <div class="text-[24px] font-medium text-[#111827] leading-tight" x-text="analytics?.orders ?? 0">0</div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold leading-tight"
                          :class="(analytics?.ordersChange ?? 0) >= 0 ? 'bg-[#eafbf2] text-[#00643b]' : 'bg-[#fff0f4] text-[#b91f50]'"
                          x-text="formatPctChange(analytics?.ordersChange ?? 0)">0%</span>
                    <span class="text-[12px] text-[#53565a] leading-tight"
                          x-text="'vs. kemarin (' + (analytics?.ordersYesterday ?? 0) + ')'">vs. kemarin (0)</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Menu Terlaris -->
        <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.03)] flex flex-col justify-between h-[116px] transition-all hover:border-[#f9731f]/40">
            <div class="flex items-center justify-between">
                <p class="text-[12px] font-normal text-[#53565a] leading-[15px]">Menu Terlaris</p>
                <div class="w-6 h-6 flex items-center justify-center">
                    <img src="{{ asset('images/sparta/tag-price-bold-duotone.svg') }}" class="w-5 h-5" alt="">
                </div>
            </div>

            <!-- State: Ada penjualan hari ini -->
            <template x-if="(analytics?.topProduct?.sold ?? 0) > 0">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <template x-if="analytics?.topProduct?.image">
                            <img :src="analytics.topProduct.image" class="w-6 h-6 rounded-[6px] object-cover border border-gray-100 shrink-0" alt="">
                        </template>
                        <template x-if="!analytics?.topProduct?.image">
                            <div class="w-6 h-6 rounded-[6px] bg-[#fff6f0] border border-[#ffd6bd] flex items-center justify-center text-[#c94d00] text-[11px] font-bold shrink-0"
                                 x-text="(analytics?.topProduct?.name || 'M').charAt(0).toUpperCase()"></div>
                        </template>
                        <span class="text-[17px] font-medium text-[#111827] truncate leading-tight" x-text="analytics?.topProduct?.name"></span>
                    </div>
                    <p class="text-[12px] text-[#5b6b6b] leading-tight truncate">
                        <span x-text="analytics?.topProduct?.name"></span> · terjual <strong class="font-semibold text-[#5b6b6b]" x-text="(analytics?.topProduct?.sold ?? 0) + '×'">0×</strong>
                    </p>
                </div>
            </template>

            <!-- State: Belum ada penjualan hari ini -->
            <template x-if="(analytics?.topProduct?.sold ?? 0) === 0">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-[6px] bg-[#f8f9fa] border border-[#e9ebf0] flex items-center justify-center text-[#7f837e] shrink-0">
                            <i class="fas fa-mug-hot text-[11px]"></i>
                        </div>
                        <span class="text-[16px] font-medium text-[#7f837e] truncate leading-tight">Belum ada</span>
                    </div>
                    <p class="text-[12px] text-[#7f837e] leading-tight truncate">Belum ada transaksi hari ini</p>
                </div>
            </template>
        </div>

        <!-- Card 4: Pelanggan Setia -->
        <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.03)] flex flex-col justify-between h-[116px] transition-all hover:border-[#f9731f]/40">
            <div class="flex items-center justify-between">
                <p class="text-[12px] font-normal text-[#53565a] leading-[15px]">Pelanggan Setia</p>
                <div class="w-6 h-6 flex items-center justify-center">
                    <img src="{{ asset('images/sparta/users-group-bold-duotone.svg') }}" class="w-5 h-5" alt="">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <div class="text-[24px] font-medium text-[#111827] leading-tight" x-text="analytics?.todayReturningCount ?? 0">0</div>
                <p class="text-[12px] text-[#53565a] leading-tight" x-text="'dari ' + (analytics?.todayVisitors ?? 0) + ' pengunjung hari ini'">dari 0 pengunjung hari ini</p>
            </div>
        </div>

    </div>

    <!-- Middle Section: Antrean + Retensi Pelanggan (Left) & Menu Terlaris Hari Ini (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
        
        <!-- Left Column (Antrean + Retensi) -->
        <div class="lg:col-span-6 flex flex-col gap-4">
            
            <!-- Antrean Card -->
            <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-[#e9ebf0]">
                    <div>
                        <h2 class="text-[14px] font-semibold text-[#1e211f] leading-tight">
                            Antrean <span class="text-[#898989] font-normal" x-text="'(' + getLiveKitchenQueue().length + ')'">(0)</span>
                        </h2>
                        <p class="text-[12px] text-[#7f837e] font-normal mt-0.5">Status pesanan di dapur</p>
                    </div>
                    <button type="button" @click="navigateToPage('orders')" class="inline-flex items-center gap-1 text-[11px] text-[#c94d00] hover:text-[#ea580c] font-normal transition-colors cursor-pointer">
                        <span>Semua Pesanan</span>
                        <img src="{{ asset('images/sparta/chevron-right.svg') }}" class="w-3 h-3" alt="">
                    </button>
                </div>

                <!-- State: Ada Antrean Aktif -->
                <template x-if="getLiveKitchenQueue().length > 0">
                    <div class="mt-3 flex gap-3">
                        <!-- Progress track bar -->
                        <div class="w-1 bg-[#f1f3f5] rounded-full overflow-hidden shrink-0 self-stretch relative my-1">
                            <div class="w-full bg-[#f9731f] rounded-full h-10 transition-all duration-300"></div>
                        </div>

                        <!-- Order Cards List (Scrollable) -->
                        <div class="flex-1 flex flex-col gap-3 max-h-[350px] overflow-y-auto pr-1 [scrollbar-width:thin] [scrollbar-color:#ffd6bd_transparent]">
                            <template x-for="(item, idx) in getLiveKitchenQueue()" :key="item.id || idx">
                                <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-3 shadow-[0px_4px_7.5px_rgba(0,0,0,0.03)] hover:shadow-md transition-shadow">
                                    <!-- Order Header -->
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-[30px] h-[30px] rounded-[8px] bg-[#fff6f0] border border-[#ffd6bd] flex items-center justify-center shrink-0">
                                                <span class="text-[11px] font-bold text-[#c94d00]" x-text="item.badgeText">01</span>
                                            </div>
                                            <div>
                                                <p class="text-[12px] font-bold text-[#111827] leading-tight" x-text="item.titleText">Meja 01</p>
                                                <div class="flex items-center gap-1 text-[11px] text-[#7f837e] mt-0.5">
                                                    <img src="{{ asset('images/sparta/bill-check.svg') }}" class="w-3 h-3" alt="">
                                                    <span x-text="item.totalQty + ' item'">1 item</span>
                                                    <span class="text-[8px]">•</span>
                                                    <span x-text="formatRp(item.total)">Rp0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-[8px] bg-[#fff6f0] text-[#c94d00] text-[10px] font-medium" x-text="item.status">Memasak</span>
                                    </div>

                                    <!-- Items Preview -->
                                    <div class="mt-2.5 text-[12px] text-[#111827] font-normal leading-[15px] truncate">
                                        <span x-text="item.itemsText"></span>
                                        <span x-show="item.extraItemText" class="text-[#c94d00] font-semibold" x-text="item.extraItemText"></span>
                                    </div>

                                    <!-- Progress bar & Timer -->
                                    <div class="mt-2.5 flex items-center gap-2">
                                        <div class="flex-1 bg-[#f1f3f5] h-[5px] rounded-full overflow-hidden">
                                            <div class="bg-[#f9731f] h-full rounded-full transition-all duration-300" :style="{ width: item.progressPct + '%' }"></div>
                                        </div>
                                        <div class="flex items-center gap-1 text-[11px] font-normal text-[#111827] shrink-0">
                                            <img src="{{ asset('images/sparta/clock.svg') }}" class="w-3.5 h-3.5" alt="">
                                            <span x-text="item.displayTime">—</span>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button" @click="openOrderDetail(item)" class="flex-1 py-1.5 px-3 rounded-[8px] border border-[#e9ebf0] hover:bg-orange-50/40 text-[12px] font-medium text-[#c94d00] text-center transition-colors cursor-pointer">
                                            Lihat Detail
                                        </button>
                                        <button type="button" @click="markOrderReady(item)" class="flex-1 py-1.5 px-3 rounded-[8px] bg-gradient-to-r from-[#f9731f] to-[#ea580c] hover:from-[#ea580c] hover:to-[#c2410c] text-[12px] font-medium text-white flex items-center justify-center gap-1.5 shadow-[0_2px_4px_rgba(249,115,31,0.2)] transition-all cursor-pointer">
                                            <img src="{{ asset('images/sparta/check-bold.svg') }}" class="w-3.5 h-3.5" alt="">
                                            <span>Siap Disajikan</span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- State: Antrean Kosong -->
                <template x-if="getLiveKitchenQueue().length === 0">
                    <div class="py-10 px-4 flex flex-col items-center justify-center text-center">
                        <div class="w-11 h-11 rounded-full bg-[#fff6f0] border border-[#ffd6bd] flex items-center justify-center text-[#c94d00] mb-2.5">
                            <i class="fas fa-utensils text-sm"></i>
                        </div>
                        <p class="text-[13px] font-medium text-[#111827]">Tidak ada antrean pesanan di dapur</p>
                        <p class="text-[11px] text-[#7f837e] mt-0.5 max-w-xs">Pesanan aktif yang sedang diproses akan muncul secara real-time di sini.</p>
                    </div>
                </template>
            </div>

            <!-- Retensi Pelanggan Card -->
            <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
                <div class="pb-3 border-b border-[#e9ebf0]">
                    <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Retensi Pelanggan</h2>
                    <p class="text-[12px] text-[#53565a] font-normal mt-0.5">Perbandingan bulan ini</p>
                </div>
                
                <div class="pt-3.5 flex flex-col gap-3">
                    <!-- Pelanggan Baru Row -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-24 shrink-0 px-2 py-0.5 rounded-[4px] border border-[#e9ebf0] bg-white text-center">
                            <span class="text-[11px] font-medium text-[#53565a]">Pelanggan Baru</span>
                        </div>
                        <!-- 30 micro blocks -->
                        <div class="flex-1 flex gap-0.5 items-center h-2.5">
                            <template x-for="i in 30" :key="'new-'+i">
                                <div class="flex-1 h-2 rounded-[1px] transition-colors"
                                     :class="i <= Math.round(((analytics?.newCustomersPct ?? 0) / 100) * 30) ? 'bg-[#485bd7]' : 'bg-[#f1f3f5]'"></div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 text-right shrink-0 min-w-[54px] justify-end">
                            <span class="text-[11px] font-normal text-[#53565a]" x-text="analytics?.newCount ?? 0">0</span>
                            <span class="text-[11px] font-normal text-[#7f837e]" x-text="(analytics?.newCustomersPct ?? 0) + '%'">0%</span>
                        </div>
                    </div>

                    <!-- Pelanggan Setia Row -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="w-24 shrink-0 px-2 py-0.5 rounded-[4px] border border-[#e9ebf0] bg-white text-center">
                            <span class="text-[11px] font-medium text-[#53565a]">Pelanggan Setia</span>
                        </div>
                        <!-- 30 micro blocks -->
                        <div class="flex-1 flex gap-0.5 items-center h-2.5">
                            <template x-for="i in 30" :key="'ret-'+i">
                                <div class="flex-1 h-2 rounded-[1px] transition-colors"
                                     :class="i <= Math.round(((analytics?.returningCustomers ?? 0) / 100) * 30) ? 'bg-[#e83c72]' : 'bg-[#f1f3f5]'"></div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 text-right shrink-0 min-w-[54px] justify-end">
                            <span class="text-[11px] font-normal text-[#53565a]" x-text="analytics?.returningCount ?? 0">0</span>
                            <span class="text-[11px] font-normal text-[#7f837e]" x-text="(analytics?.returningCustomers ?? 0) + '%'">0%</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Menu Terlaris (Hari Ini / Minggu Ini / Bulan Ini) -->
        <div class="lg:col-span-6">
            <div class="bg-white border border-[#e9ebf0] rounded-[12px] shadow-[0px_3px_12px_rgba(38,39,35,0.04)] h-full flex flex-col overflow-hidden">
                <div class="p-3.5 sm:p-4 border-b border-[#e9ebf0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-[14px] font-semibold text-[#111827] leading-tight"
                            x-text="topProductsFilter === 'week' ? 'Menu Terlaris Minggu Ini' : (topProductsFilter === 'month' ? 'Menu Terlaris Bulan Ini' : 'Menu Terlaris Hari Ini')">
                            Menu Terlaris Hari Ini
                        </h2>
                        <p class="text-[12px] text-[#7f837e] font-normal mt-0.5"
                           x-text="topProductsFilter === 'week' ? '5 menu terlaris minggu ini' : (topProductsFilter === 'month' ? '5 menu terlaris bulan ini' : '5 menu terlaris hari ini')">
                            5 menu terlaris
                        </p>
                    </div>

                    <!-- Filter Tabs: Hari Ini / Minggu Ini / Bulan Ini -->
                    <div class="inline-flex items-center p-1 bg-[#f4f5f7] rounded-[10px] border border-[#e9ebf0] self-start sm:self-auto gap-0.5">
                        <button type="button"
                                @click="topProductsFilter = 'today'"
                                :class="topProductsFilter === 'today' ? 'bg-white text-[#df5826] font-bold shadow-2xs border border-[#ffdec9]' : 'text-[#7f837e] hover:text-[#111827] font-medium border border-transparent'"
                                class="px-2.5 py-1 rounded-[7px] text-[11px] transition cursor-pointer">
                            Hari Ini
                        </button>
                        <button type="button"
                                @click="topProductsFilter = 'week'"
                                :class="topProductsFilter === 'week' ? 'bg-white text-[#df5826] font-bold shadow-2xs border border-[#ffdec9]' : 'text-[#7f837e] hover:text-[#111827] font-medium border border-transparent'"
                                class="px-2.5 py-1 rounded-[7px] text-[11px] transition cursor-pointer">
                            Minggu Ini
                        </button>
                        <button type="button"
                                @click="topProductsFilter = 'month'"
                                :class="topProductsFilter === 'month' ? 'bg-white text-[#df5826] font-bold shadow-2xs border border-[#ffdec9]' : 'text-[#7f837e] hover:text-[#111827] font-medium border border-transparent'"
                                class="px-2.5 py-1 rounded-[7px] text-[11px] transition cursor-pointer">
                            Bulan Ini
                        </button>
                    </div>
                </div>
                
                <!-- Table -->
                <div class="flex-1 flex flex-col divide-y divide-[#e9ebf0]">
                    <!-- Table Header -->
                    <div class="bg-[#f8f9fa] px-4 py-2 flex items-center text-[12px] text-[#7f837e] font-normal">
                        <div class="w-8 shrink-0 text-center">#</div>
                        <div class="flex-1 pl-2">Nama Produk</div>
                        <div class="w-20 text-center">Terjual</div>
                        <div class="w-28 text-right pr-2">Pendapatan</div>
                    </div>

                    <!-- State: Ada Produk Terjual -->
                    <template x-if="getTopProductsList().length > 0">
                        <div class="divide-y divide-[#e9ebf0]">
                            <template x-for="(item, idx) in getTopProductsList()" :key="idx">
                                <div class="px-4 py-3 flex items-center hover:bg-gray-50/70 transition-colors">
                                    <div class="w-8 shrink-0 flex items-center justify-center">
                                        <span class="w-7 h-7 rounded-[8px] bg-[#f8f9fa] text-[#111827] text-[12px] font-medium flex items-center justify-center" x-text="item.rank || (idx + 1)">1</span>
                                    </div>
                                    <div class="flex-1 pl-2 flex items-center gap-2.5 min-w-0">
                                        <template x-if="item.image">
                                            <img :src="item.image" class="w-7 h-7 rounded-[6px] object-cover border border-gray-100 shrink-0" alt="">
                                        </template>
                                        <template x-if="!item.image">
                                            <div class="w-7 h-7 rounded-[6px] bg-[#fff6f0] border border-[#ffd6bd] flex items-center justify-center text-[#c94d00] text-[11px] font-bold shrink-0"
                                                 x-text="(item.name || 'M').charAt(0).toUpperCase()"></div>
                                        </template>
                                        <span class="text-[13px] font-medium text-[#111827] truncate" x-text="item.name"></span>
                                    </div>
                                    <div class="w-20 text-center text-[13px] font-normal text-[#111827]" x-text="item.sold">0</div>
                                    <div class="w-28 text-right pr-2 text-[13px] font-medium text-[#00643b]" x-text="formatRp(item.revenue)">Rp0</div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- State: Belum Ada Produk Terjual -->
                    <template x-if="getTopProductsList().length === 0">
                        <div class="flex-1 py-12 px-4 flex flex-col items-center justify-center text-center">
                            <div class="w-11 h-11 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] flex items-center justify-center text-[#7f837e] mb-2.5">
                                <i class="fas fa-award text-sm"></i>
                            </div>
                            <p class="text-[13px] font-medium text-[#111827]"
                               x-text="topProductsFilter === 'week' ? 'Belum ada penjualan menu minggu ini' : (topProductsFilter === 'month' ? 'Belum ada penjualan menu bulan ini' : 'Belum ada penjualan menu hari ini')">
                                Belum ada penjualan menu hari ini
                            </p>
                            <p class="text-[11px] text-[#7f837e] mt-0.5 max-w-xs"
                               x-text="topProductsFilter === 'week' ? 'Peringkat 5 menu terlaris akan otomatis dihitung dari pesanan minggu ini.' : (topProductsFilter === 'month' ? 'Peringkat 5 menu terlaris akan otomatis dihitung dari pesanan bulan ini.' : 'Peringkat 5 menu terlaris akan otomatis dihitung dari pesanan hari ini.')">
                                Peringkat 5 menu terlaris akan otomatis dihitung dari pesanan hari ini.
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Row: Tren Pendapatan (Left) & Jam Tersibuk (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        
        <!-- Left: Tren Pendapatan Card -->
        <div class="lg:col-span-7 bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)] flex flex-col justify-between">
            <div class="pb-3 border-b border-[#e9ebf0] flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Tren Pendapatan</h2>
                    <p class="text-[12px] text-[#7f837e] font-normal mt-0.5"
                       x-text="'Pendapatan bulanan (' + (analytics?.monthlyTrend?.unitLabel || 'Rp juta') + ') · ' + (analytics?.monthlyTrend?.periodLabel || '6 Bulan Terakhir')">
                        Pendapatan bulanan (Rp juta)
                    </p>
                </div>
                <template x-if="analytics && !analytics?.monthlyTrend?.hasData">
                    <span class="px-2.5 py-1 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] text-[10px] text-[#7f837e]">Belum ada transaksi lunas</span>
                </template>
            </div>

            <!-- Line Curve SVG Chart -->
            <div class="pt-4 flex-1 flex flex-col justify-between">
                <div class="flex items-stretch gap-3 h-[210px]">
                    <!-- Y Axis -->
                    <div class="flex flex-col justify-between text-[11px] text-gray-400 font-normal py-1 pr-1 select-none text-right w-8 shrink-0">
                        <template x-for="(yLabel, yIdx) in (analytics?.monthlyTrend?.yAxisLabels || ['100', '80', '60', '40', '20', '0'])" :key="yIdx">
                            <span x-text="yLabel"></span>
                        </template>
                    </div>

                    <!-- SVG Curve Area Chart -->
                    <div class="flex-1 relative h-full">
                        <!-- Horizontal Grid Lines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                            <div class="border-b border-[#e9ebf0]/60 w-full"></div>
                            <div class="border-b border-[#e9ebf0]/60 w-full"></div>
                            <div class="border-b border-[#e9ebf0]/60 w-full"></div>
                            <div class="border-b border-[#e9ebf0]/60 w-full"></div>
                            <div class="border-b border-[#e9ebf0]/60 w-full"></div>
                            <div class="border-b border-[#e9ebf0] w-full"></div>
                        </div>

                        <!-- Dynamic Area + Line SVG -->
                        <svg class="absolute inset-0 w-full h-full overflow-visible" viewBox="0 0 500 180" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="spartaAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.32" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.01" />
                                </linearGradient>
                            </defs>

                            <!-- Area Path -->
                            <path :d="getTrendAreaPath()" fill="url(#spartaAreaGrad)" />

                            <!-- Line Stroke -->
                            <path :d="getTrendLinePath()" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                            <!-- Data Points -->
                            <template x-for="(pt, pIdx) in getTrendPoints()" :key="pIdx">
                                <circle :cx="pt.x" :cy="pt.y" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" class="cursor-pointer">
                                    <title x-text="pt.label + ': ' + formatRp(pt.revenue)"></title>
                                </circle>
                            </template>
                        </svg>
                    </div>
                </div>

                <!-- X Axis Labels -->
                <div class="flex justify-between pl-11 pr-1 pt-2.5 text-[11px] text-gray-500 font-normal">
                    <template x-for="(pt, pIdx) in getTrendPoints()" :key="'lbl-'+pIdx">
                        <span x-text="pt.label"></span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Right: Jam Tersibuk Card -->
        <div class="lg:col-span-5 bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)] flex flex-col justify-between">
            <div class="pb-3 border-b border-[#e9ebf0] flex items-center justify-between">
                <div>
                    <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Jam Tersibuk</h2>
                </div>
                <div class="text-gray-400">
                    <span class="text-[11px] font-normal text-[#7f837e]">Hari ini</span>
                </div>
            </div>

            <!-- Stats Subheader -->
            <div class="pt-3 flex items-baseline justify-between">
                <span class="text-[24px] font-medium text-[#111827] leading-none" x-text="analytics?.busyHours?.peakHour || '—'">—</span>
                <span class="text-[11px] text-[#7f837e] font-normal"
                      x-text="(analytics?.busyHours?.peakVisitors ?? 0) > 0 ? (analytics.busyHours.peakVisitors + ' pesanan/jam') : 'Belum ada pesanan hari ini'">
                    Belum ada pesanan hari ini
                </span>
            </div>

            <!-- Vertical Bar Chart (with Striped Peak Pattern) -->
            <div class="pt-4 flex-1 flex flex-col justify-end">
                <div class="h-[155px] flex items-end gap-[4px] sm:gap-[6px] w-full px-1">
                    <!-- 12 hourly bars representation (09:00 - 20:00) -->
                    <template x-for="(bar, bIdx) in getBusyHoursBars()" :key="bIdx">
                        <div class="flex-1 flex flex-col items-center justify-end h-full group relative cursor-pointer">
                            <!-- Tooltip on hover -->
                            <div class="absolute -top-7 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-900 text-white text-[9px] font-bold py-0.5 px-1.5 rounded pointer-events-none whitespace-nowrap z-20"
                                 x-text="bar.label + ': ' + bar.visitors + ' pesanan'"></div>
                            
                            <!-- Bar pill -->
                            <div class="w-full rounded-t-[3px] transition-all"
                                 :style="{ height: bar.height + '%' }"
                                 :class="bar.isPeak ? 'sparta-striped-peak' : (bar.visitors > 0 ? 'bg-[#cbd5e1] hover:bg-[#94a3b8]' : 'bg-[#f1f3f5]')">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- X Axis Time Labels -->
                <div class="flex justify-between pt-3 text-[10px] text-[#7f837e] font-normal px-1">
                    <span>09:00</span>
                    <span>12:00</span>
                    <span>15:00</span>
                    <span>18:00</span>
                    <span>20:00</span>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Inline Custom Style for Striped Peak Pattern & Smooth Aesthetics -->
<style>
.sparta-striped-peak {
    background: repeating-linear-gradient(
        -45deg,
        #ed501a,
        #ed501a 2.5px,
        #f79e72 2.5px,
        #f79e72 5.5px
    );
}
</style>
