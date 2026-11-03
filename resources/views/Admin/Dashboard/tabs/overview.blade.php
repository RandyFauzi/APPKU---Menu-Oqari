<div x-show="currentTab === 'dashboard'" x-cloak class="flex-grow p-4 md:p-6 lg:p-7 overflow-y-auto hide-scroll bg-[#f8f9fa] font-['DM_Sans',sans-serif]" style="font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';">

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
                    <span x-text="formatOmzetShort(analytics?.revenue ?? 4250000)">Rp4,25 jt</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#eafbf2] text-[#00643b] leading-tight">+8%</span>
                    <span class="text-[12px] text-white/90 leading-tight">vs. kemarin</span>
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
                <div class="text-[24px] font-medium text-[#111827] leading-tight" x-text="analytics?.orders ?? 87">87</div>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#fff0f4] text-[#b91f50] leading-tight">−4,4%</span>
                    <span class="text-[12px] text-[#53565a] leading-tight">vs. kemarin (91)</span>
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
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <img :src="analytics?.topProduct?.image || '{{ asset('images/sparta/mont-blanc.png') }}'" class="w-6 h-6 rounded-[6px] object-cover border border-gray-100" alt="">
                    <span class="text-[17px] font-medium text-[#111827] truncate leading-tight" x-text="analytics?.topProduct?.name || 'Mont Blanc'">Mont Blanc</span>
                </div>
                <p class="text-[12px] text-[#5b6b6b] leading-tight truncate">
                    <span x-text="analytics?.topProduct?.name || 'Mont Blanc'">Mont Blanc</span> · terjual <strong class="font-semibold text-[#5b6b6b]" x-text="(analytics?.topProduct?.sold || 34) + '×'">34×</strong>
                </p>
            </div>
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
                <div class="text-[24px] font-medium text-[#111827] leading-tight" x-text="analytics?.returningCount ?? 52">52</div>
                <p class="text-[12px] text-[#53565a] leading-tight" x-text="'dari ' + (analytics?.totalCustomers ?? 87) + ' pengunjung hari ini'">dari 87 pengunjung hari ini</p>
            </div>
        </div>

    </div>

    <!-- Middle Section: Antrean (15) + Retensi Pelanggan (Left) & Menu Terlaris Hari Ini (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
        
        <!-- Left Column (Antrean + Retensi) -->
        <div class="lg:col-span-6 flex flex-col gap-4">
            
            <!-- Antrean Card -->
            <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3.5 border-b border-[#e9ebf0]">
                    <div>
                        <h2 class="text-[14px] font-semibold text-[#1e211f] leading-tight">
                            Antrean <span class="text-[#898989] font-normal" x-text="'(' + (getLiveKitchenQueue().length || 15) + ')'">(15)</span>
                        </h2>
                        <p class="text-[12px] text-[#7f837e] font-normal mt-0.5">Status pesanan di dapur</p>
                    </div>
                    <button type="button" @click="currentTab = 'orders'" class="inline-flex items-center gap-1 text-[11px] text-[#c94d00] hover:text-[#ea580c] font-normal transition-colors">
                        <span>Semua Pesanan</span>
                        <img src="{{ asset('images/sparta/chevron-right.svg') }}" class="w-3 h-3" alt="">
                    </button>
                </div>

                <!-- Orders Container with Track Indicator -->
                <div class="mt-3 flex gap-3">
                    <!-- Progress track bar -->
                    <div class="w-1 bg-[#f1f3f5] rounded-full overflow-hidden shrink-0 self-stretch relative my-1">
                        <div class="w-full bg-[#f9731f] rounded-full h-10 transition-all duration-300"></div>
                    </div>

                    <!-- Order Cards List -->
                    <div class="flex-1 flex flex-col gap-3">
                        <template x-for="(item, idx) in getLiveKitchenQueue().slice(0, 2)" :key="item.id || idx">
                            <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-3 shadow-[0px_4px_7.5px_rgba(0,0,0,0.03)] hover:shadow-md transition-shadow">
                                <!-- Order Header -->
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-[30px] h-[30px] rounded-[8px] bg-[#fff6f0] border border-[#ffd6bd] flex items-center justify-center shrink-0">
                                            <span class="text-[11px] font-bold text-[#c94d00]" x-text="String(item.table || (idx + 1)).padStart(2, '0')">01</span>
                                        </div>
                                        <div>
                                            <p class="text-[12px] font-bold text-[#111827] leading-tight" x-text="item.table && !item.table.toLowerCase().includes('meja') ? ('Meja ' + item.table) : (item.table || ('Meja ' + String(idx + 1).padStart(2, '0')))">Meja 01</p>
                                            <div class="flex items-center gap-1 text-[11px] text-[#7f837e] mt-0.5">
                                                <img src="{{ asset('images/sparta/bill-check.svg') }}" class="w-3 h-3" alt="">
                                                <span x-text="(item.items ? item.items.length : 5) + ' item'">5 item</span>
                                                <span class="text-[8px]">•</span>
                                                <span x-text="formatRp(item.total || 167000)">Rp167.000</span>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-[8px] bg-[#fff6f0] text-[#c94d00] text-[10px] font-medium" x-text="item.status || 'Memasak'">Memasak</span>
                                </div>

                                <!-- Items Preview -->
                                <div class="mt-2.5 text-[12px] text-[#111827] font-normal leading-[15px] truncate">
                                    <span x-text="item.itemsText || 'Carbonara ×3, Nasi Goreng ×1, '">Carbonara ×3, Nasi Goreng ×1, </span>
                                    <span class="text-[#c94d00] font-semibold" x-text="item.extraItemText || '+1 item'">+1 item</span>
                                </div>

                                <!-- Progress bar & Timer -->
                                <div class="mt-2.5 flex items-center gap-2">
                                    <div class="flex-1 bg-[#f1f3f5] h-[5px] rounded-full overflow-hidden">
                                        <div class="bg-[#f9731f] h-full rounded-full" :style="{ width: (idx === 0 ? '65%' : '40%') }"></div>
                                    </div>
                                    <div class="flex items-center gap-1 text-[11px] font-normal text-[#111827] shrink-0">
                                        <img src="{{ asset('images/sparta/clock.svg') }}" class="w-3.5 h-3.5" alt="">
                                        <span x-text="item.displayTime || (idx === 0 ? '18:20' : '18:25')">18:20</span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-3 flex items-center gap-2">
                                    <button type="button" @click="openOrderDetail(item)" class="flex-1 py-1.5 px-3 rounded-[8px] border border-[#e9ebf0] hover:bg-orange-50/40 text-[12px] font-medium text-[#c94d00] text-center transition-colors">
                                        Lihat Detail
                                    </button>
                                    <button type="button" @click="markOrderReady(item)" class="flex-1 py-1.5 px-3 rounded-[8px] bg-gradient-to-r from-[#f9731f] to-[#ea580c] hover:from-[#ea580c] hover:to-[#c2410c] text-[12px] font-medium text-white flex items-center justify-center gap-1.5 shadow-[0_2px_4px_rgba(249,115,31,0.2)] transition-all">
                                        <img src="{{ asset('images/sparta/check-bold.svg') }}" class="w-3.5 h-3.5" alt="">
                                        <span>Siap Disajikan</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
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
                                <div class="flex-1 h-2 rounded-[1px] transition-colors" :class="i <= 17 ? 'bg-[#485bd7]' : 'bg-[#f1f3f5]'"></div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 text-right shrink-0 min-w-[50px] justify-end">
                            <span class="text-[11px] font-normal text-[#53565a]" x-text="analytics?.newCount ?? 148">148</span>
                            <span class="text-[11px] font-normal text-[#7f837e]" x-text="(analytics?.newCustomersPct ?? 55) + '%'">55%</span>
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
                                <div class="flex-1 h-2 rounded-[1px] transition-colors" :class="i <= 13 ? 'bg-[#e83c72]' : 'bg-[#f1f3f5]'"></div>
                            </template>
                        </div>
                        <div class="flex items-center gap-1.5 text-right shrink-0 min-w-[50px] justify-end">
                            <span class="text-[11px] font-normal text-[#53565a]" x-text="analytics?.returningCount ?? 122">122</span>
                            <span class="text-[11px] font-normal text-[#7f837e]" x-text="(analytics?.returningCustomers ?? 45) + '%'">45%</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Menu Terlaris Hari Ini -->
        <div class="lg:col-span-6">
            <div class="bg-white border border-[#e9ebf0] rounded-[12px] shadow-[0px_3px_12px_rgba(38,39,35,0.04)] h-full flex flex-col overflow-hidden">
                <div class="p-4 border-b border-[#e9ebf0]">
                    <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Menu Terlaris Hari Ini</h2>
                    <p class="text-[12px] text-[#7f837e] font-normal mt-0.5">5 menu terlaris</p>
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

                    <!-- 5 Rows -->
                    <template x-for="(item, idx) in getTopProductsList()" :key="idx">
                        <div class="px-4 py-3 flex items-center hover:bg-gray-50/70 transition-colors">
                            <div class="w-8 shrink-0 flex items-center justify-center">
                                <span class="w-7 h-7 rounded-[8px] bg-[#f8f9fa] text-[#111827] text-[12px] font-medium flex items-center justify-center" x-text="idx + 1">1</span>
                            </div>
                            <div class="flex-1 pl-2 flex items-center gap-2.5 min-w-0">
                                <img :src="item.image" class="w-7 h-7 rounded-[6px] object-cover border border-gray-100 shrink-0" alt="">
                                <span class="text-[13px] font-medium text-[#111827] truncate" x-text="item.name">Mont Blanc</span>
                            </div>
                            <div class="w-20 text-center text-[13px] font-normal text-[#111827]" x-text="item.sold">34</div>
                            <div class="w-28 text-right pr-2 text-[13px] font-medium text-[#00643b]" x-text="formatRp(item.revenue)">Rp952.000</div>
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
            <div class="pb-3 border-b border-[#e9ebf0]">
                <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Tren Pendapatan</h2>
                <p class="text-[12px] text-[#7f837e] font-normal mt-0.5">Pendapatan bulanan (Rp juta) · Jan–Jun 2026</p>
            </div>

            <!-- Line Curve SVG Chart -->
            <div class="pt-4 flex-1 flex flex-col justify-between">
                <div class="flex items-stretch gap-3 h-[210px]">
                    <!-- Y Axis -->
                    <div class="flex flex-col justify-between text-[11px] text-gray-400 font-normal py-1 pr-1 select-none text-right w-6 shrink-0">
                        <span>100</span>
                        <span>80</span>
                        <span>60</span>
                        <span>40</span>
                        <span>20</span>
                        <span>0</span>
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

                        <!-- Smooth Bezier Area + Line SVG -->
                        <svg class="absolute inset-0 w-full h-full overflow-visible" viewBox="0 0 500 180" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="spartaAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#10b981" stop-opacity="0.32" />
                                    <stop offset="100%" stop-color="#10b981" stop-opacity="0.01" />
                                </linearGradient>
                            </defs>

                            <!-- Area Path -->
                            <path d="M 0 162 L 100 86 L 200 62 L 300 115 L 400 9 L 500 7 L 500 180 L 0 180 Z" fill="url(#spartaAreaGrad)" />

                            <!-- Line Stroke -->
                            <path d="M 0 162 L 100 86 L 200 62 L 300 115 L 400 9 L 500 7" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                            <!-- Data Points -->
                            <circle cx="0" cy="162" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                            <circle cx="100" cy="86" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                            <circle cx="200" cy="62" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                            <circle cx="300" cy="115" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                            <circle cx="400" cy="9" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                            <circle cx="500" cy="7" r="3.5" fill="#10b981" stroke="#ffffff" stroke-width="2" />
                        </svg>
                    </div>
                </div>

                <!-- X Axis Labels -->
                <div class="flex justify-between pl-9 pr-1 pt-2.5 text-[11px] text-gray-500 font-normal">
                    <span>Jan</span>
                    <span>Feb</span>
                    <span>Mar</span>
                    <span>Apr</span>
                    <span>Mei</span>
                    <span>Jun</span>
                </div>
            </div>
        </div>

        <!-- Right: Jam Tersibuk Card -->
        <div class="lg:col-span-5 bg-white border border-[#e9ebf0] rounded-[12px] p-4 shadow-[0px_3px_12px_rgba(38,39,35,0.04)] flex flex-col justify-between">
            <div class="pb-3 border-b border-[#e9ebf0] flex items-center justify-between">
                <div>
                    <h2 class="text-[14px] font-semibold text-[#111827] leading-tight">Jam Tersibuk</h2>
                </div>
                <div class="text-gray-400 hover:text-gray-600 cursor-pointer">
                    <span class="text-xs font-bold leading-none tracking-widest">•••</span>
                </div>
            </div>

            <!-- Stats Subheader -->
            <div class="pt-3 flex items-baseline justify-between">
                <span class="text-[24px] font-medium text-[#111827] leading-none">18:00</span>
                <span class="text-[11px] text-[#7f837e] font-normal">18 pengunjung/jam</span>
            </div>

            <!-- Vertical Bar Chart (with Striped Peak Pattern) -->
            <div class="pt-4 flex-1 flex flex-col justify-end">
                <div class="h-[155px] flex items-end gap-[3px] sm:gap-[4px] w-full px-1">
                    <!-- 24 hourly bars representation -->
                    <template x-for="(bar, bIdx) in busyHoursData" :key="bIdx">
                        <div class="flex-1 flex flex-col items-center justify-end h-full group relative cursor-pointer">
                            <!-- Tooltip on hover -->
                            <div class="absolute -top-7 opacity-0 group-hover:opacity-100 transition-opacity bg-gray-900 text-white text-[9px] font-bold py-0.5 px-1.5 rounded pointer-events-none whitespace-nowrap z-20" x-text="bar.label + ': ' + bar.visitors + ' org'"></div>
                            
                            <!-- Bar pill -->
                            <div class="w-full rounded-t-[3px] transition-all"
                                 :style="{ height: bar.height + '%' }"
                                 :class="bar.isPeak ? 'sparta-striped-peak' : 'bg-[#f1f3f5] hover:bg-gray-300'">
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
