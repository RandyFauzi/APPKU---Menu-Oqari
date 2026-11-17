<!-- Tab Inventori & Bahan Baku (Figma node 210:12000 / 207:1119 / 208:1262) -->
<div x-show="currentTab === 'inventory'" x-cloak class="flex-grow p-4 md:p-6 lg:p-8 overflow-y-auto hide-scroll bg-white">
    
    <!-- Page Header (Figma node 208:1263) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
        <div class="flex flex-col gap-1">
            <h1 class="font-bold text-[20px] text-[#111827] tracking-tight">Manajemen Stok & Bahan Baku</h1>
            <p class="text-[13px] text-[#53565a]">Pantau ketersediaan bahan, harga modal (HPP), dan riwayat pergerakan stok cafe.</p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <!-- Catat Pergerakan Stok Button (Figma node 208:1268) -->
            <button type="button" 
                    @click="openStockAdjustModal(null, 'in')" 
                    class="bg-white border border-[#e9ebf0] hover:bg-gray-50 text-[#111827] rounded-[10px] h-[36px] px-3.5 text-[12px] font-medium shadow-[0px_1px_2px_rgba(20,21,20,0.03)] flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4 text-[#53565a]" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M12 2.25H6C4.34315 2.25 3 3.59315 3 5.25V13.5C3 15.1569 4.34315 16.5 6 16.5H12C13.6569 16.5 15 15.1569 15 13.5V5.25C15 3.59315 13.6569 2.25 12 2.25Z" stroke-linecap="round"/>
                    <path d="M6.75 2.25V1.5C6.75 1.08579 7.08579 0.75 7.5 0.75H10.5C10.9142 0.75 11.25 1.08579 11.25 1.5V2.25" stroke-linecap="round"/>
                    <path d="M9 6.75V12" stroke-linecap="round"/>
                    <path d="M6.375 9.375H11.625" stroke-linecap="round"/>
                </svg>
                <span>Catat Pergerakan Stok</span>
            </button>

            <!-- Tambah Bahan Baku Button (Figma node 208:1275) -->
            <button type="button" 
                    @click="openAddMaterialModal()" 
                    class="bg-[#df5826] hover:bg-[#c94d00] text-white rounded-[10px] h-[36px] px-3.5 text-[12px] font-semibold flex items-center gap-1.5 shadow-[0px_2px_4px_rgba(223,88,38,0.25)] transition cursor-pointer">
                <svg class="w-4 h-4 text-white" viewBox="0 0 16 16" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 2a.75.75 0 01.75.75v4.5h4.5a.75.75 0 010 1.5h-4.5v4.5a.75.75 0 01-1.5 0v-4.5h-4.5a.75.75 0 010-1.5h4.5v-4.5A.75.75 0 018 2z" clip-rule="evenodd"/>
                </svg>
                <span>Tambah Bahan Baku</span>
            </button>
        </div>
    </div>

    <!-- KPICards: Grid 3 Kolom (Figma node 208:1279) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 mb-5">
        <!-- Card 1: Stok Menipis (Orange alert card with warning watermark - Figma node 208:1280) -->
        <div class="bg-[#f9731f] border border-[#e9ebf0] rounded-[14px] p-4 text-white relative overflow-hidden h-[112px] flex justify-between items-start shadow-[0px_4px_14px_rgba(249,115,31,0.18)]">
            <!-- Duotone Warning Chat Watermark on right -->
            <div class="absolute -right-3 -top-2 w-[140px] h-[140px] pointer-events-none rotate-[12deg] opacity-25">
                <img src="/images/sparta/kpi-warning-chat.svg" alt="Warning" class="w-full h-full">
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between h-full">
                <span class="text-[12px] text-white/95 font-medium leading-[15px]">Stok Menipis</span>
                <div class="flex flex-col gap-0.5">
                    <span class="text-[26px] font-bold text-white leading-tight" x-text="lowStockCount">0</span>
                    <span class="text-[11px] text-white/90 leading-[14px]">Perlu segera direstok dari supplier</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Stok Bahan (White card with box icon - Figma node 208:1296) -->
        <div class="bg-white border border-[#e9ebf0] rounded-[14px] p-4 drop-shadow-[0px_2px_4px_rgba(38,39,35,0.04)] h-[112px] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[12px] text-[#53565a] font-medium">Total Stok Bahan</span>
                <div class="w-7 h-7 shrink-0">
                    <img src="/images/sparta/kpi-box-duotone.svg" alt="Box" class="w-full h-full">
                </div>
            </div>
            <div class="flex flex-col">
                <span class="text-[24px] font-bold text-[#111827] leading-[28px]" x-text="rawMaterials.length">0</span>
                <span class="text-[11px] text-[#7f837e] leading-[14px]">Bahan aktif untuk menu & racikan</span>
            </div>
        </div>

        <!-- Card 3: Stok Habis (White card with cart-cross icon - Figma node 208:1307) -->
        <div class="bg-white border border-[#e9ebf0] rounded-[14px] p-4 drop-shadow-[0px_2px_4px_rgba(38,39,35,0.04)] h-[112px] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[12px] text-[#53565a] font-medium">Stok Habis</span>
                <div class="w-7 h-7 shrink-0 text-[#e11d48]">
                    <svg class="w-full h-full" viewBox="0 0 28 28" fill="none">
                        <circle cx="14" cy="14" r="12" fill="#ffe4e6" stroke="#f43f5e" stroke-width="1.5"/>
                        <path d="M10 10L18 18M18 10L10 18" stroke="#e11d48" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
            <div class="flex flex-col">
                <span class="text-[24px] font-bold text-[#111827] leading-[28px]" x-text="outOfStockCount">0</span>
                <span class="text-[11px] text-[#7f837e] leading-[14px]">Bahan tidak tersedia</span>
            </div>
        </div>
    </div>

    <!-- Tabbing Buttons (Figma node 208:1319) -->
    <div class="flex items-center border-b border-[#e9ebf0] mb-4 gap-6">
        <!-- Tab 1: Daftar Bahan Baku -->
        <button type="button" 
                @click="inventorySubTab = 'materials'" 
                :class="inventorySubTab === 'materials' ? 'border-[#df5826] text-[#df5826] font-bold' : 'border-transparent text-[#53565a] hover:text-[#111827] font-medium'" 
                class="border-b-2 pb-3 pt-1 text-[13px] flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M2.25 5.25L9 1.5L15.75 5.25M2.25 5.25L9 9M2.25 5.25V12.75L9 16.5M15.75 5.25L9 9M15.75 5.25V12.75L9 16.5M9 9V16.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Daftar Bahan Baku</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="inventorySubTab === 'materials' ? 'bg-[#fff6f0] text-[#df5826] border border-[#ffdec9]' : 'bg-[#f1f3f5] text-[#53565a]'"
                  x-text="rawMaterials.length">
            </span>
        </button>

        <!-- Tab 2: Riwayat Mutasi Stok -->
        <button type="button" 
                @click="inventorySubTab = 'mutations'" 
                :class="inventorySubTab === 'mutations' ? 'border-[#df5826] text-[#df5826] font-bold' : 'border-transparent text-[#53565a] hover:text-[#111827] font-medium'" 
                class="border-b-2 pb-3 pt-1 text-[13px] flex items-center gap-2 transition cursor-pointer">
            <svg class="w-4 h-4" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M9 4.5V9L12 10.5M16.5 9A7.5 7.5 0 119 1.5a7.5 7.5 0 017.5 7.5z" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Riwayat Mutasi Stok</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold"
                  :class="inventorySubTab === 'mutations' ? 'bg-[#fff6f0] text-[#df5826] border border-[#ffdec9]' : 'bg-[#f1f3f5] text-[#53565a]'"
                  x-text="stockMutations.length">
            </span>
        </button>
    </div>

    <!-- Filters & Search Controls Row (Figma node 208:1341 & 208:1361) -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3" x-show="inventorySubTab === 'materials'">
        <!-- Left: Status Filter Pills (Matching Figma Frame 208:1341) -->
        <div class="flex items-center gap-1.5 flex-wrap">
            <!-- Semua Stok -->
            <button type="button" 
                    @click="materialFilterStatus = 'all'; materialPage = 1" 
                    :class="materialFilterStatus === 'all' ? 'bg-[#fff6f0] text-[#df5826] border-[#ffdec9] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] flex items-center gap-2 transition cursor-pointer">
                <span>Semua Stok</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]"
                      :class="materialFilterStatus === 'all' ? 'bg-[#df5826] text-white font-bold' : 'bg-gray-100 text-gray-600'"
                      x-text="rawMaterials.length"></span>
            </button>

            <!-- Stok Aman -->
            <button type="button" 
                    @click="materialFilterStatus = 'Aman'; materialPage = 1" 
                    :class="materialFilterStatus === 'Aman' ? 'bg-[#ecfdf5] text-[#00643b] border-[#a7f3d0] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] flex items-center gap-2 transition cursor-pointer">
                <span>Stok Aman</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]"
                      :class="materialFilterStatus === 'Aman' ? 'bg-[#00643b] text-white font-bold' : 'bg-gray-100 text-gray-600'"
                      x-text="safeStockCount"></span>
            </button>

            <!-- Stok Menipis -->
            <button type="button" 
                    @click="materialFilterStatus = 'Menipis'; materialPage = 1" 
                    :class="materialFilterStatus === 'Menipis' ? 'bg-[#fff6f0] text-[#c94d00] border-[#ffd2b8] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] flex items-center gap-2 transition cursor-pointer">
                <span>Stok Menipis</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]"
                      :class="materialFilterStatus === 'Menipis' ? 'bg-[#c94d00] text-white font-bold' : 'bg-gray-100 text-gray-600'"
                      x-text="lowStockCount"></span>
            </button>

            <!-- Stok Habis -->
            <button type="button" 
                    @click="materialFilterStatus = 'Habis'; materialPage = 1" 
                    :class="materialFilterStatus === 'Habis' ? 'bg-[#fff0f4] text-[#b91f50] border-[#fecdd3] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] flex items-center gap-2 transition cursor-pointer">
                <span>Stok Habis</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px]"
                      :class="materialFilterStatus === 'Habis' ? 'bg-[#b91f50] text-white font-bold' : 'bg-gray-100 text-gray-600'"
                      x-text="outOfStockCount"></span>
            </button>
        </div>

        <!-- Right: Search Box + Category Filter + Sort + Refresh -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Search Input (Figma node 208:1361) -->
            <div class="bg-white border border-[#e9ebf0] focus-within:border-[#df5826] rounded-[10px] h-[36px] px-3 flex items-center gap-2 w-44 sm:w-60 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="7" cy="7" r="5"/>
                    <path d="M11 11L14.5 14.5" stroke-linecap="round"/>
                </svg>
                <input type="text" x-model="materialSearch" @input="materialPage = 1" placeholder="Cari nama bahan, SKU..." class="bg-transparent border-none outline-none text-[12px] text-[#111827] placeholder-[#7f837e] w-full">
                <button type="button" x-show="materialSearch.length > 0" @click="materialSearch = ''; materialPage = 1" class="text-gray-400 hover:text-gray-600 text-xs cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Category Dropdown with tuning icon (Figma node 208:1370) -->
            <div class="relative">
                <select x-model="materialFilterCategory" @change="materialPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[10px] h-[36px] pl-8 pr-7 text-[12px] font-medium text-[#111827] cursor-pointer outline-none transition shadow-2xs flex items-center">
                    <option value="all">Semua Kategori</option>
                    <option value="Coffee">Coffee</option>
                    <option value="Dairy">Dairy</option>
                    <option value="Sweetener">Sweetener</option>
                    <option value="Powder">Powder</option>
                    <option value="Meat">Meat</option>
                    <option value="Frozen Food">Frozen Food</option>
                    <option value="Cooking">Cooking</option>
                    <option value="Bahan Baku">Lainnya</option>
                </select>
                <svg class="w-3.5 h-3.5 text-[#53565a] absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="5" cy="5" r="2"/>
                    <circle cx="11" cy="11" r="2"/>
                    <path d="M1 5H3M7 5H15M1 11H9M13 11H15" stroke-linecap="round"/>
                </svg>
                <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Sort Dropdown -->
            <div class="relative">
                <select x-model="materialSortOrder" @change="materialPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[10px] h-[36px] pl-3 pr-7 text-[12px] font-medium text-[#111827] cursor-pointer outline-none transition shadow-2xs flex items-center">
                    <option value="name_asc">Urutkan: Nama A-Z</option>
                    <option value="name_desc">Urutkan: Nama Z-A</option>
                    <option value="stock_asc">Stok: Paling Sedikit</option>
                    <option value="stock_desc">Stok: Paling Banyak</option>
                    <option value="cost_desc">Harga: Tertinggi</option>
                    <option value="cost_asc">Harga: Terendah</option>
                </select>
                <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Refresh Button -->
            <button type="button" 
                    @click="fetchInventory()" 
                    class="bg-white border border-[#e9ebf0] hover:bg-gray-50 rounded-[10px] w-[36px] h-[36px] flex items-center justify-center transition cursor-pointer shadow-2xs" 
                    title="Refresh Data">
                <img src="/images/sparta/solar-refresh.svg" alt="Refresh" class="w-3.5 h-3.5" :class="isLoadingInventory ? 'animate-spin' : ''">
            </button>
        </div>
    </div>

    <!-- VIEW 1: TABEL DAFTAR BAHAN BAKU (Figma node 208:1384) -->
    <div x-show="inventorySubTab === 'materials'" class="bg-white border border-[#e6e5e0] rounded-[16px] shadow-[0px_3px_12px_0px_rgba(38,39,35,0.04)] overflow-hidden flex flex-col mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8f9fa] border-b border-[#e9ebf0]">
                        <th class="w-[36px] pl-4 py-3.5 text-center">
                            <input type="checkbox" @change="toggleSelectAllMaterials($event)" :checked="isAllMaterialsSelected" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                        </th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[230px]">NAMA BAHAN</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[100px]">SKU / KODE</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[105px]">KATEGORI</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[80px]">SATUAN</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[120px]">HARGA BELI/UNIT</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[105px]">SISA STOK</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[95px]">MIN. STOK</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[95px]">STATUS</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider text-right pr-4">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-for="item in paginatedMaterials" :key="item.id">
                        <tr class="transition-colors hover:bg-[#fafafa]">
                            <!-- Checkbox -->
                            <td class="w-[36px] pl-4 py-3 text-center align-middle">
                                <input type="checkbox" :value="item.id" x-model="selectedMaterialIds" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                            </td>

                            <!-- Nama Bahan -->
                            <td class="px-3 py-3 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <!-- Icon Box (Figma node 208:1410) -->
                                    <div class="w-[26px] h-[26px] rounded-[6px] flex items-center justify-center shrink-0"
                                         :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#fff0f4] text-[#b91f50]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#fff6f0] text-[#c94d00]' : 'bg-[#f1f3f5] text-[#53565a]')">
                                        <template x-if="getMaterialStatus(item) === 'Aman'">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M2 5L8 2L14 5M2 5L8 8M2 5V11L8 14M14 5L8 8M14 5V11L8 14M8 8V14" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </template>
                                        <template x-if="getMaterialStatus(item) === 'Menipis'">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <path d="M8 2L14.5 13.5H1.5L8 2Z" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M8 6V9M8 11.5V12" stroke-linecap="round"/>
                                            </svg>
                                        </template>
                                        <template x-if="getMaterialStatus(item) === 'Habis'">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <circle cx="8" cy="8" r="6"/>
                                                <path d="M6 6L10 10M10 6L6 10" stroke-linecap="round"/>
                                            </svg>
                                        </template>
                                    </div>
                                    <span class="font-semibold text-[13px] text-[#111827] leading-snug truncate" x-text="item.name"></span>
                                </div>
                            </td>

                            <!-- SKU / Kode -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-medium text-[12px] text-[#53565a] font-mono" x-text="item.sku || '-'"></span>
                            </td>

                            <!-- Kategori -->
                            <td class="px-3 py-3 align-middle">
                                <span class="px-2.5 py-1 rounded-[6px] text-[11px] font-semibold inline-block capitalize" 
                                      :class="getMaterialCategoryBadgeClass(getMaterialCategory(item))" 
                                      x-text="getMaterialCategory(item)">
                                </span>
                            </td>

                            <!-- Satuan -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-medium text-[12px] text-[#53565a] capitalize" x-text="item.unit_label || stockUnitLabel(item.unit)"></span>
                            </td>

                            <!-- Harga Beli / Unit -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-bold text-[13px] text-[#111827] font-mono" x-text="formatRp(item.cost_per_unit)"></span>
                            </td>

                            <!-- Sisa Stok -->
                            <td class="px-3 py-3 align-middle">
                                <span class="text-[13px]" 
                                      :class="getMaterialStatus(item) === 'Habis' ? 'font-bold text-[#b91f50]' : (getMaterialStatus(item) === 'Menipis' ? 'font-bold text-[#c94d00]' : 'font-bold text-[#00643b]')" 
                                      x-text="formatStock(item.current_stock) + ' ' + stockUnitShort(item.unit)">
                                </span>
                            </td>

                            <!-- Min. Stok -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-medium text-[12px] text-[#53565a]" x-text="formatStock(item.minimum_stock) + ' ' + stockUnitShort(item.unit)"></span>
                            </td>

                            <!-- Status (Figma node 208:1430) -->
                            <td class="px-3 py-3 align-middle">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.8 rounded-full border text-[11px] font-semibold"
                                     :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#ffe8ef] text-[#b91f50] border-[#fecdd3]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#fff3e8] text-[#c94d00] border-[#ffd2b8]' : 'bg-[#eafbf2] text-[#00643b] border-[#c6f6d5]')">
                                    <span class="w-[5px] h-[5px] rounded-full"
                                          :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#b91f50]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#c94d00]' : 'bg-[#00643b]')"></span>
                                    <span x-text="getMaterialStatus(item)"></span>
                                </div>
                            </td>

                            <!-- Aksi (Figma node 208:1433) -->
                            <td class="px-4 py-3 align-middle text-right pr-4">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Kelola (if Aman) or Restok (if Menipis/Habis) Button -->
                                    <template x-if="getMaterialStatus(item) === 'Aman'">
                                        <button type="button" 
                                                @click="openManageStock(item)" 
                                                class="px-3 py-1 rounded-[7px] text-[11.5px] font-medium bg-white border border-[#e9ebf0] hover:bg-gray-50 text-[#111827] transition cursor-pointer shadow-2xs">
                                            Kelola
                                        </button>
                                    </template>
                                    <template x-if="getMaterialStatus(item) !== 'Aman'">
                                        <button type="button" 
                                                @click="quickRestock(item)" 
                                                class="px-3 py-1 rounded-[7px] text-[11.5px] font-bold bg-[#df5826] hover:bg-[#c94d00] text-white transition cursor-pointer shadow-xs">
                                            Restok
                                        </button>
                                    </template>

                                    <!-- Edit Button (Figma node 208:1437) -->
                                    <button type="button" 
                                            @click="editMaterial(item)" 
                                            title="Edit Bahan" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] flex items-center justify-center transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M11.5 2.5L13.5 4.5M1.5 14.5L4.5 13.5L13 5L11 3L2.5 11.5L1.5 14.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Figma node 208:1442) -->
                                    <button type="button" 
                                            @click="deleteMaterial(item.id)" 
                                            title="Hapus Bahan" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-red-500 text-[#53565a] hover:text-red-500 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <path d="M5.5 2.5C5.5 1.94772 5.94772 1.5 6.5 1.5H9.5C10.0523 1.5 10.5 1.94772 10.5 2.5V3.5H13.5M2.5 3.5H13.5M3.5 3.5V13.5C3.5 14.0523 3.94772 14.5 4.5 14.5H11.5C12.0523 14.5 12.5 14.0523 12.5 13.5V3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredMaterials.length === 0">
                        <tr>
                            <td colspan="10" class="p-12 text-center text-[#7f837e]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-[#f8f9fa] flex items-center justify-center text-gray-400">
                                        <i class="fas fa-box-open text-xl"></i>
                                    </div>
                                    <span class="font-medium text-sm text-[#111827]">Tidak ada bahan baku ditemukan</span>
                                    <p class="text-xs text-[#7f837e]">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination (Figma node 208:1406) -->
        <div class="p-4 border-t border-[#e9ebf0] bg-[#f8f9fa] flex flex-col sm:flex-row justify-between items-center gap-3">
            <span class="text-[12px] text-[#53565a]">
                Menampilkan <span class="font-semibold text-[#111827]" x-text="filteredMaterials.length === 0 ? 0 : Math.min(filteredMaterials.length, (materialPage - 1) * materialPerPage + 1)"></span> - <span class="font-semibold text-[#111827]" x-text="Math.min(filteredMaterials.length, materialPage * materialPerPage)"></span> dari <span class="font-semibold text-[#111827]" x-text="filteredMaterials.length"></span> bahan baku
            </span>

            <div class="flex items-center gap-1.5" x-show="totalMaterialPages > 1">
                <button type="button" 
                        @click="prevMaterialPage()" 
                        :disabled="materialPage <= 1" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#53565a] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] font-medium transition cursor-pointer">
                    ←
                </button>

                <template x-for="p in totalMaterialPages" :key="p">
                    <button type="button" 
                            @click="materialPage = p" 
                            class="rounded-[8px] px-2.5 py-1 text-[11px] font-medium transition cursor-pointer" 
                            :class="materialPage === p ? 'bg-[#df5826] text-white font-bold shadow-2xs' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                            x-text="p">
                    </button>
                </template>

                <button type="button" 
                        @click="nextMaterialPage()" 
                        :disabled="materialPage >= totalMaterialPages" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#53565a] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] font-medium transition cursor-pointer">
                    →
                </button>
            </div>
        </div>
    </div>

    <!-- VIEW 2: RIWAYAT MUTASI STOK -->
    <div x-show="inventorySubTab === 'mutations'" class="bg-white border border-[#e6e5e0] rounded-[16px] shadow-[0px_3px_12px_0px_rgba(38,39,35,0.04)] overflow-hidden flex flex-col mb-6">
        <div class="p-4 border-b border-[#e9ebf0] bg-[#f8f9fa] flex justify-between items-center">
            <div>
                <h3 class="font-bold text-[13px] text-[#111827]">100 Mutasi Stok Terakhir</h3>
                <p class="text-[11px] text-[#7f837e]">Audit trail keluar-masuk barang otomatis</p>
            </div>
            <button type="button" @click="fetchInventory()" class="p-1.5 rounded-lg border border-[#e9ebf0] bg-white text-[#53565a] hover:text-[#df5826] transition shadow-xs" title="Refresh">
                <img src="/images/sparta/solar-refresh.svg" alt="Refresh" class="w-3.5 h-3.5" :class="isLoadingInventory ? 'animate-spin' : ''">
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#e9ebf0] text-[11px] font-bold text-[#53565a] uppercase tracking-wider bg-[#f8f9fa]">
                        <th class="py-3.5 px-3 pl-4">WAKTU</th>
                        <th class="py-3.5 px-3">NAMA BAHAN</th>
                        <th class="py-3.5 px-3">TIPE</th>
                        <th class="py-3.5 px-3">JUMLAH</th>
                        <th class="py-3.5 px-3">SISA AKHIR</th>
                        <th class="py-3.5 px-3">KETERANGAN</th>
                        <th class="py-3.5 px-3 pr-4 text-right">PETUGAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-for="mut in stockMutations" :key="mut.id">
                        <tr class="hover:bg-[#fafafa] transition">
                            <td class="py-3 px-3 pl-4 text-[12px] text-[#53565a] font-mono" x-text="new Date(mut.created_at).toLocaleString('id-ID')"></td>
                            <td class="py-3 px-3 font-bold text-[13px] text-[#111827]" x-text="mut.raw_material ? mut.raw_material.name : '-'"></td>
                            <td class="py-3 px-3">
                                <template x-if="mut.type === 'in'">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#eafbf2] text-[#00643b]">Restok Masuk</span>
                                </template>
                                <template x-if="mut.type === 'out' || mut.type === 'out_order'">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#eef0ff] text-[#2a3ba2]">Stok Keluar</span>
                                </template>
                                <template x-if="mut.type === 'adjustment'">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#f3e8ff] text-[#6b21a8]">Stok Opname</span>
                                </template>
                                <template x-if="mut.type === 'waste'">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#ffe8ef] text-[#b91f50]">Rusak/Expired</span>
                                </template>
                            </td>
                            <td class="py-3 px-3 font-mono font-bold text-[12px]">
                                <span :class="Number(mut.amount) > 0 ? 'text-[#00643b]' : 'text-[#b91f50]'" x-text="(Number(mut.amount) > 0 ? '+' : '') + formatStock(mut.amount)"></span>
                                <span class="text-[11px] text-[#7f837e] font-normal" x-text="stockUnitShort(mut.unit || mut.raw_material?.unit)"></span>
                            </td>
                            <td class="py-3 px-3 font-mono text-[12px] font-semibold text-[#111827]">
                                <span x-text="formatStock(mut.balance_after)"></span>
                                <span class="text-[10px] text-[#7f837e] font-normal" x-text="stockUnitShort(mut.unit || mut.raw_material?.unit)"></span>
                            </td>
                            <td class="py-3 px-3 text-[12px] text-[#53565a]" x-text="mut.notes || '-'"></td>
                            <td class="py-3 px-3 pr-4 text-right text-[12px] text-[#53565a] font-medium" x-text="mut.user ? mut.user.name : 'Sistem'"></td>
                        </tr>
                    </template>

                    <template x-if="stockMutations.length === 0">
                        <tr>
                            <td colspan="7" class="p-8 text-center text-[#7f837e] text-xs">Belum ada catatan mutasi stok.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FLOATING BULK ACTIONS BAR (Muncul saat ada bahan yang dipilih) -->
    <div x-show="selectedMaterialIds.length > 0 && inventorySubTab === 'materials'" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-[#111827]/95 backdrop-blur-md text-white px-5 py-3 rounded-full shadow-[0_16px_40px_-6px_rgba(0,0,0,0.35)] flex items-center gap-4 border border-gray-700/60 font-sans">
        
        <div class="flex items-center gap-2 font-medium text-[12.5px]">
            <span class="w-5 h-5 rounded-full bg-[#df5826] text-white flex items-center justify-center text-[10px] font-bold shadow-xs" x-text="selectedMaterialIds.length"></span>
            <span>Bahan Terpilih</span>
        </div>

        <div class="h-4 w-px bg-gray-700"></div>

        <button type="button" 
                @click="deleteSelectedMaterials()" 
                class="text-red-400 hover:text-red-300 font-bold text-[12px] flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-red-950/40 transition cursor-pointer">
            <i class="fas fa-trash-alt text-xs"></i>
            <span>Hapus Terpilih</span>
        </button>

        <button type="button" 
                @click="selectedMaterialIds = []" 
                class="text-gray-400 hover:text-white font-medium text-[12px] px-2.5 py-1 rounded-full hover:bg-white/10 transition cursor-pointer">
            Batal
        </button>
    </div>

    <!-- MODAL 1: TAMBAH / EDIT BAHAN BAKU (Figma Section 209:11780) -->
    <div x-show="showMaterialModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showMaterialModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        <div class="relative bg-white w-full max-w-[580px] rounded-[20px] p-6 shadow-[0_20px_60px_rgba(0,0,0,0.12)] flex flex-col border border-[#e9ebf0]" @keydown.escape.window="showMaterialModal = false" x-transition>
            
            <!-- Modal Header (Figma node 209:11781) -->
            <div class="flex justify-between items-start mb-5 pb-4 border-b border-[#f1f3f5]">
                <div class="flex gap-3.5 items-center">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-2xs">
                        <img src="/images/sparta/kpi-box-duotone.svg" alt="Material" class="w-5 h-5">
                    </div>
                    <div>
                        <h2 class="text-[17px] font-bold text-[#111827]" x-text="materialForm.id ? 'Edit Bahan Baku' : 'Tambah Bahan Baku'"></h2>
                        <p class="text-[12px] text-[#53565a]">Tentukan takaran, satuan, dan harga modal beli.</p>
                    </div>
                </div>
                <button type="button" @click="showMaterialModal = false" class="text-gray-400 hover:text-[#111827] w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Modal Form (Figma node 209:11794) -->
            <div class="flex flex-col gap-4">
                <!-- Nama Bahan Baku -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Nama Bahan Baku <span class="text-red-500">*</span></label>
                    <input type="text" x-model="materialForm.name" placeholder="cth: Susu Freshmilk" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition">
                </div>

                <!-- SKU Field -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">SKU / Kode Bahan (Opsional)</label>
                    <input type="text" x-model="materialForm.sku" placeholder="cth: RK499-3" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition font-mono">
                </div>

                <!-- Satuan Dasar & Stok Awal -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Satuan Dasar Custom Searchable Dropdown -->
                    <div x-data="{
                            openDropdown: false,
                            searchUnit: '',
                            isAddingNew: false,
                            newUnitLabel: '',
                            isCreatingUnit: false,
                            dropdownPos: { top: 0, bottom: 0, left: 0, width: 300, dropUp: false },
                            get dropdownStyle() {
                                if (this.dropdownPos.dropUp) {
                                    return `position: fixed; bottom: calc(100vh - ${this.dropdownPos.bottom}px); left: ${this.dropdownPos.left}px; width: ${this.dropdownPos.width}px; z-index: 99999;`;
                                }
                                return `position: fixed; top: ${this.dropdownPos.top}px; left: ${this.dropdownPos.left}px; width: ${this.dropdownPos.width}px; z-index: 99999;`;
                            },
                            updatePosition() {
                                if (!this.$refs.unitTrigger) return;
                                const rect = this.$refs.unitTrigger.getBoundingClientRect();
                                const dropdownHeight = 350;
                                const spaceBelow = window.innerHeight - rect.bottom;
                                const dropUp = spaceBelow < dropdownHeight && rect.top > dropdownHeight;
                                const width = Math.max(rect.width, 280);
                                const left = Math.min(Math.max(16, rect.left), window.innerWidth - width - 16);
                                this.dropdownPos = {
                                    top: Math.round(rect.bottom + 6),
                                    bottom: Math.round(rect.top - 6),
                                    left: Math.round(left),
                                    width: Math.round(width),
                                    dropUp: dropUp
                                };
                            },
                            toggleDropdown() {
                                if (editingUnitLocked) return;
                                if (this.openDropdown) {
                                    this.openDropdown = false;
                                    return;
                                }
                                this.isAddingNew = false;
                                this.updatePosition();
                                this.openDropdown = true;
                                this.$nextTick(() => {
                                    this.$refs.searchUnitInput?.focus();
                                });
                            },
                            get unitList() {
                                const list = [];
                                for (const [key, label] of Object.entries(stockUnits || {})) {
                                    let icon = 'fas fa-tag';
                                    const lk = key.toLowerCase();
                                    if (['gram', 'g', 'kg'].includes(lk)) icon = 'fas fa-weight-hanging';
                                    else if (['ml', 'liter', 'l'].includes(lk)) icon = 'fas fa-glass-water';
                                    else if (['cup'].includes(lk)) icon = 'fas fa-mug-hot';
                                    else if (['botol'].includes(lk)) icon = 'fas fa-wine-bottle';
                                    else if (['dus'].includes(lk)) icon = 'fas fa-box-archive';
                                    else if (['pack'].includes(lk)) icon = 'fas fa-box';
                                    else if (['pcs'].includes(lk)) icon = 'fas fa-shapes';
                                    else if (['shot'].includes(lk)) icon = 'fas fa-whiskey-glass';
                                    else if (['slice'].includes(lk)) icon = 'fas fa-pizza-slice';
                                    else if (['oz'].includes(lk)) icon = 'fas fa-scale-balanced';
                                    list.push({ key, label, icon });
                                }
                                if (materialForm.unit && !stockUnits[materialForm.unit]) {
                                    list.unshift({ key: materialForm.unit, label: materialForm.unit + ' (satuan lama)', icon: 'fas fa-history' });
                                }
                                return list;
                            },
                            get filteredUnits() {
                                if (!this.searchUnit.trim()) return this.unitList;
                                const q = this.searchUnit.toLowerCase();
                                return this.unitList.filter(u => u.label.toLowerCase().includes(q) || u.key.toLowerCase().includes(q));
                            },
                            get isExactMatch() {
                                const q = this.searchUnit.trim().toLowerCase();
                                return this.unitList.some(u => u.label.toLowerCase() === q || u.key.toLowerCase() === q);
                            },
                            selectUnit(unitKey) {
                                materialForm.unit = unitKey;
                                this.openDropdown = false;
                                this.searchUnit = '';
                                this.isAddingNew = false;
                            },
                            async handleCreateUnit(nameOrKey) {
                                const raw = (nameOrKey || this.newUnitLabel || this.searchUnit || '').trim();
                                if (!raw) return;
                                this.isCreatingUnit = true;
                                const createdKey = await createQuickUnit(raw);
                                this.isCreatingUnit = false;
                                if (createdKey) {
                                    materialForm.unit = createdKey;
                                    this.searchUnit = '';
                                    this.isAddingNew = false;
                                    this.openDropdown = false;
                                }
                            }
                        }"
                        @close-all-dropdowns.window="openDropdown = false"
                        @scroll.window="if(openDropdown) openDropdown = false"
                        @resize.window="if(openDropdown) openDropdown = false"
                        @keydown.escape.window="openDropdown = false"
                        class="relative">

                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-1.5">
                                <label class="block text-[12px] font-semibold text-[#111827]">Satuan Bahan <span class="text-red-500">*</span></label>
                            </div>
                            <template x-if="editingUnitLocked">
                                <span class="text-[9.5px] text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 font-medium flex items-center gap-1">
                                    <i class="fas fa-lock text-[8.5px]"></i> Terkunci
                                </span>
                            </template>
                        </div>

                        <!-- Dropdown Trigger Button -->
                        <button type="button" 
                                x-ref="unitTrigger"
                                @click.stop="toggleDropdown()"
                                :disabled="editingUnitLocked"
                                class="w-full bg-[#f8f9fa] border rounded-[10px] px-3.5 py-2.5 text-[12.5px] text-left flex items-center justify-between transition-all cursor-pointer outline-none shadow-2xs"
                                :class="editingUnitLocked ? 'opacity-60 cursor-not-allowed bg-gray-100 border-[#e9ebf0]' : (openDropdown ? 'border-[#df5826] bg-white ring-2 ring-[#df5826]/15 shadow-2xs' : 'border-[#e9ebf0] hover:border-gray-300 hover:bg-white')">
                            <span class="truncate flex items-center gap-2">
                                <template x-if="materialForm.unit">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[7px] bg-[#fff6f0] text-[#df5826] border border-[#ffdec9] font-bold text-[11.5px] truncate max-w-[190px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] shrink-0"></span>
                                        <span class="truncate" x-text="stockUnitLabel(materialForm.unit)"></span>
                                    </span>
                                </template>
                                <template x-if="!materialForm.unit">
                                    <span class="text-[#9ca3af] flex items-center gap-2 text-[12px]">
                                        <i class="fas fa-boxes-stacked text-gray-400 text-xs"></i>
                                        <span>-- Pilih Satuan --</span>
                                    </span>
                                </template>
                            </span>
                            <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 transition-colors"
                                 :class="openDropdown ? 'bg-[#fff6f0] text-[#df5826]' : 'text-gray-400'">
                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" 
                                   :class="openDropdown ? 'rotate-180' : ''"></i>
                            </div>
                        </button>

                        <!-- Hidden select for form binding & fallback -->
                        <select id="stock-unit" x-model="materialForm.unit" :disabled="editingUnitLocked" class="hidden">
                            <template x-for="(label, unit) in stockUnits" :key="unit"><option :value="unit" x-text="label"></option></template>
                        </select>

                        <!-- Dropdown Menu Popover (Teleported to body) -->
                        <template x-teleport="body">
                            <div x-show="openDropdown" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
                                 x-cloak
                                 @click.outside="if (!$refs.unitTrigger?.contains($event.target)) { openDropdown = false; isAddingNew = false; searchUnit = ''; }"
                                 @keydown.escape.window="openDropdown = false"
                                 :style="dropdownStyle"
                                 class="bg-white border border-[#e5e7eb] rounded-[18px] shadow-[0_24px_60px_-12px_rgba(15,23,42,0.28),0_6px_20px_rgba(15,23,42,0.08)] p-3 min-w-[290px] font-sans">
                            
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#f1f3f5] text-[11px]">
                                    <div class="flex items-center gap-1.5 font-bold text-[#111827] uppercase tracking-wider text-[10px]">
                                        <i class="fas fa-boxes-stacked text-[#df5826]"></i>
                                        <span>Daftar Satuan</span>
                                    </div>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] text-[#7f837e]" 
                                          x-text="filteredUnits.length + ' item'"></span>
                                </div>

                                <div class="relative mb-2.5">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
                                    <input type="text" 
                                           x-ref="searchUnitInput" 
                                           x-model="searchUnit" 
                                           @keydown.enter.prevent="if(searchUnit.trim() && !isExactMatch) { handleCreateUnit(searchUnit); }"
                                           placeholder="Cari atau ketik nama baru..." 
                                           style="padding-left: 36px; padding-right: 32px;"
                                           class="w-full bg-[#f8f9fa] border border-[#e5e7eb] focus:border-[#df5826] focus:bg-white focus:ring-2 focus:ring-[#df5826]/10 rounded-[10px] pl-9 pr-8 py-2 text-[12px] text-[#111827] placeholder:text-[#9ca3af] outline-none transition-all">
                                    
                                    <button type="button" 
                                            x-show="searchUnit.length > 0" 
                                            @click="searchUnit = ''; $refs.searchUnitInput?.focus()" 
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 w-5 h-5 rounded-full flex items-center justify-center hover:bg-gray-200 transition text-[11px] cursor-pointer">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="max-h-48 overflow-y-auto space-y-1 pr-1 custom-scroll" x-show="filteredUnits.length > 0">
                                    <template x-for="item in filteredUnits" :key="item.key">
                                        <button type="button" 
                                                @click="selectUnit(item.key)" 
                                                class="w-full text-left px-3 py-2 rounded-[10px] text-[12px] flex items-center justify-between transition-all cursor-pointer group"
                                                :class="(materialForm.unit === item.key || String(materialForm.unit).toLowerCase() === String(item.key).toLowerCase()) ? 'bg-[#fff6f0] text-[#df5826] font-semibold border border-[#ffdec9]' : 'text-[#374151] hover:bg-gray-50 border border-transparent'">
                                            <span class="flex items-center gap-2.5 truncate">
                                                <span class="w-2 h-2 rounded-full shrink-0 transition-transform group-hover:scale-125" 
                                                      :class="(materialForm.unit === item.key || String(materialForm.unit).toLowerCase() === String(item.key).toLowerCase()) ? 'bg-[#df5826]' : 'bg-gray-300'"></span>
                                                <i :class="item.icon + ' text-xs opacity-70 w-4 text-center'"></i>
                                                <span class="truncate" x-text="item.label"></span>
                                            </span>
                                            <span x-show="(materialForm.unit === item.key || String(materialForm.unit).toLowerCase() === String(item.key).toLowerCase())" 
                                                  class="w-4 h-4 rounded-full bg-[#df5826] text-white flex items-center justify-center text-[9px] shrink-0">
                                                <i class="fas fa-check"></i>
                                            </span>
                                        </button>
                                    </template>
                                </div>

                                <div class="mt-2 pt-2 border-t border-[#f0f2f5]">
                                    <template x-if="!isAddingNew">
                                        <button type="button" 
                                                @click="isAddingNew = true; newUnitLabel = ''; $nextTick(() => $refs.inlineNewUnitInput?.focus())" 
                                                class="w-full px-3 py-2 rounded-[10px] text-[12px] font-semibold text-[#df5826] hover:bg-[#fff6f0] flex items-center justify-between transition cursor-pointer group">
                                            <span class="flex items-center gap-2">
                                                <span class="w-5 h-5 rounded-full bg-[#df5826]/10 text-[#df5826] group-hover:bg-[#df5826] group-hover:text-white flex items-center justify-center text-[10px] transition">
                                                    <i class="fas fa-plus"></i>
                                                </span>
                                                <span>Tambah Satuan Baru</span>
                                            </span>
                                            <span class="text-[10px] text-[#7f837e] group-hover:text-[#df5826] transition font-normal">Form manual</span>
                                        </button>
                                    </template>

                                    <template x-if="isAddingNew">
                                        <div class="p-2.5 rounded-[12px] bg-[#f8f9fa] border border-[#e9ebf0] space-y-2 text-left">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] font-bold text-[#111827]">Tambah Satuan Manual</span>
                                                <button type="button" @click="isAddingNew = false" class="text-gray-400 hover:text-gray-600 text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <input type="text" 
                                                       x-ref="inlineNewUnitInput"
                                                       x-model="newUnitLabel" 
                                                       placeholder="Contoh: Ikat, Butir, Porsi..." 
                                                       @keydown.enter.prevent="if(newUnitLabel.trim()) handleCreateUnit(newUnitLabel)"
                                                       class="w-full bg-white border border-[#e5e7eb] focus:border-[#df5826] rounded-[8px] px-2.5 py-1.5 text-[11px] text-[#111827] outline-none">
                                            </div>
                                            <div class="flex items-center justify-end gap-2 pt-1">
                                                <button type="button" @click="isAddingNew = false" class="px-2.5 py-1 text-[11px] text-[#53565a] hover:bg-gray-200 rounded-[6px] transition">Batal</button>
                                                <button type="button" 
                                                        @click="handleCreateUnit(newUnitLabel)"
                                                        :disabled="isCreatingUnit || !newUnitLabel.trim()" 
                                                        class="px-3 py-1 bg-[#df5826] hover:bg-[#c94d00] disabled:opacity-50 text-white text-[11px] font-bold rounded-[6px] transition flex items-center gap-1.5 cursor-pointer">
                                                    <i :class="isCreatingUnit ? 'fas fa-spinner fa-spin' : 'fas fa-save'"></i>
                                                    <span x-text="isCreatingUnit ? 'Menyimpan...' : 'Simpan'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Jumlah Stok Awal (Hanya untuk buat bahan baru) -->
                    <div>
                        <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">
                            <span x-text="materialForm.id ? 'Sisa Stok Saat Ini' : 'Jumlah Stok Awal *'"></span>
                        </label>
                        <div class="relative">
                            <input type="number" step="0.000001" min="0" x-model="materialForm.current_stock" :disabled="!!materialForm.id" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-3.5 pr-14 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[12px] font-medium text-[#7f837e]" x-text="stockUnitShort(materialForm.unit)"></span>
                        </div>
                    </div>
                </div>

                <!-- Batas Stok Minimum & Harga Beli -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Batas Stok Minimum -->
                    <div>
                        <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Batas Stok Minimum <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" step="0.000001" min="0" x-model="materialForm.minimum_stock" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-3.5 pr-14 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-[12px] font-medium text-[#7f837e]" x-text="stockUnitShort(materialForm.unit)"></span>
                        </div>
                    </div>

                    <!-- Harga Beli Baru / Satuan (Opsional) -->
                    <div>
                        <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Harga Beli / Satuan (Opsional)</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[12.5px] font-medium text-[#7f837e]">Rp.</span>
                            <input type="number" step="0.000001" min="0" x-model="materialForm.cost_per_unit" placeholder="50.000" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-10 pr-3.5 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition font-mono">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Figma node 209:11917) -->
            <div class="mt-6 pt-4 border-t border-[#f1f3f5] flex items-center justify-between gap-3">
                <button type="button" 
                        @click="showMaterialModal = false" 
                        class="w-1/2 h-[42px] rounded-[10px] border border-[#e9ebf0] text-[#53565a] font-semibold text-[13px] hover:bg-gray-50 transition cursor-pointer flex items-center justify-center">
                    Batalkan
                </button>
                <button type="button" 
                        @click="saveMaterial()" 
                        :disabled="isSavingMaterial" 
                        class="w-1/2 h-[42px] rounded-[10px] bg-[#df5826] hover:bg-[#c94d00] text-white font-semibold text-[13px] transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingMaterial"></i>
                    <span x-text="isSavingMaterial ? 'Menyimpan...' : 'Simpan Bahan'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: KELOLA STOK / CATAT PERGERAKAN (Figma Section 207:1121 & 209:10738) -->
    <div x-show="showAdjustModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showAdjustModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        <div class="relative bg-white w-full max-w-[620px] rounded-[20px] p-6 shadow-[0_20px_60px_rgba(0,0,0,0.12)] flex flex-col border border-[#e9ebf0] max-h-[90vh] overflow-y-auto" @keydown.escape.window="showAdjustModal = false" x-transition>
            
            <!-- Modal Header (Figma node 207:1122) -->
            <div class="flex justify-between items-start mb-5 pb-4 border-b border-[#f1f3f5]">
                <div class="flex gap-3.5 items-center">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-2xs">
                        <svg class="w-5 h-5 text-[#df5826]" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 2.25H6C4.34315 2.25 3 3.59315 3 5.25V13.5C3 15.1569 4.34315 16.5 6 16.5H12C13.6569 16.5 15 15.1569 15 13.5V5.25C15 3.59315 13.6569 2.25 12 2.25Z" stroke-linecap="round"/>
                            <path d="M6.75 2.25V1.5C6.75 1.08579 7.08579 0.75 7.5 0.75H10.5C10.9142 0.75 11.25 1.08579 11.25 1.5V2.25" stroke-linecap="round"/>
                            <path d="M9 6.75V12" stroke-linecap="round"/>
                            <path d="M6.375 9.375H11.625" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-[17px] font-bold text-[#111827]">
                            <span x-text="selectedAdjustMaterial ? 'Kelola Stok ' + selectedAdjustMaterial.name : 'Catat Pergerakan Stok'"></span>
                        </h2>
                        <p class="text-[12px] text-[#53565a]">Kelola catatan pergerakan masuk dan keluar stok</p>
                    </div>
                </div>
                <button type="button" @click="showAdjustModal = false" class="text-gray-400 hover:text-[#111827] w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col gap-4">
                <!-- Dropdown Pilih Bahan Baku (Shown when opening from Header button) -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Pilih Bahan Baku <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select x-model="adjustForm.raw_material_id" class="appearance-none w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-3.5 pr-8 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition cursor-pointer">
                            <option value="" disabled>-- Pilih Bahan Baku --</option>
                            <template x-for="m in rawMaterials" :key="m.id">
                                <option :value="m.id" x-text="m.name + ' (Sisa saat ini: ' + formatStock(m.current_stock) + ' ' + stockUnitShort(m.unit) + ')'"></option>
                            </template>
                        </select>
                        <svg class="w-3 h-3 text-[#53565a] absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>

                <!-- Jenis Pergerakan Stok (Figma node 208:2042) -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Jenis Pergerakan Stok <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-2">
                        <!-- Option 1: Catat Stok Masuk -->
                        <button type="button" 
                                @click="adjustForm.type = 'in'" 
                                :class="adjustForm.type === 'in' ? 'bg-[#fff6f0] text-[#df5826] border-[#ffdec9] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                                class="border rounded-[10px] py-2 px-2 text-[12px] flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path d="M8 2V12M8 12L4.5 8.5M8 12L11.5 8.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.5 14H13.5" stroke-linecap="round"/>
                            </svg>
                            <span class="truncate">Catat Stok Masuk</span>
                        </button>

                        <!-- Option 2: Catat Stok Keluar -->
                        <button type="button" 
                                @click="adjustForm.type = 'out'" 
                                :class="adjustForm.type === 'out' || adjustForm.type === 'waste' ? 'bg-[#fff0f4] text-[#b91f50] border-[#fecdd3] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                                class="border rounded-[10px] py-2 px-2 text-[12px] flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path d="M8 12V2M8 2L4.5 5.5M8 2L11.5 5.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M2.5 14H13.5" stroke-linecap="round"/>
                            </svg>
                            <span class="truncate">Catat Stok Keluar</span>
                        </button>

                        <!-- Option 3: Koreksi Jumlah Stok -->
                        <button type="button" 
                                @click="adjustForm.type = 'adjustment'" 
                                :class="adjustForm.type === 'adjustment' ? 'bg-[#f3e8ff] text-[#6b21a8] border-[#e9d5ff] font-bold shadow-2xs' : 'bg-white border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 font-medium'" 
                                class="border rounded-[10px] py-2 px-2 text-[12px] flex items-center justify-center gap-1.5 transition cursor-pointer">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path d="M11.5 2.5L13.5 4.5M1.5 14.5L4.5 13.5L13 5L11 3L2.5 11.5L1.5 14.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span class="truncate">Koreksi Jumlah Stok</span>
                        </button>
                    </div>
                </div>

                <!-- Jumlah Stok Input (Figma node 208:2060) -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">
                        <span x-text="adjustForm.type === 'in' ? 'Jumlah Stok Bahan Masuk *' : (adjustForm.type === 'adjustment' ? 'Koreksi Jumlah Stok Fisik Akhir *' : 'Jumlah Stok Bahan Keluar *')"></span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.000001" min="0" x-model="adjustForm.amount" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-3.5 pr-20 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition font-mono">
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 px-2.5 py-1 rounded-[6px] bg-[#fff6f0] border border-[#ffdec9] text-[#df5826] font-semibold text-[11px] flex items-center gap-1">
                            <i class="fas fa-box text-[10px]"></i>
                            <span x-text="adjustUnitShort || 'Satuan'"></span>
                        </div>
                    </div>
                </div>

                <!-- Harga Beli Baru / Satuan (Opsional - Figma node 208:2077) -->
                <div x-show="adjustForm.type === 'in'">
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Harga Beli Baru / Satuan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[12.5px] font-medium text-[#7f837e]">Rp.</span>
                        <input type="number" step="0.000001" min="0" x-model="adjustForm.new_cost_per_unit" placeholder="50.000" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-10 pr-3.5 py-2.5 text-[12.5px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition font-mono">
                    </div>
                    <p class="text-[10px] text-[#7f837e] mt-1">Kosongkan jika harga beli modal tidak berubah.</p>
                </div>

                <!-- Catatan (Opsional - Figma node 208:2094) -->
                <div>
                    <label class="block text-[12px] font-semibold text-[#111827] mb-1.5">Catatan (Opsional)</label>
                    <textarea x-model="adjustForm.notes" rows="2" placeholder="Tambahkan keterangan pergerakan stok (cth: Restok dari Supplier, Bahan basi, Penyesuaian fisik)" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[12px] text-[#111827] focus:border-[#df5826] focus:bg-white outline-none transition resize-none"></textarea>
                </div>

                <!-- Live Summary Cards: Perubahan Stok & Perubahan Harga Modal (Figma node 209:8859) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Box 1: Perubahan Stok (Figma node 209:8860) -->
                    <div class="bg-[#f8f9fa] border border-[#e9ebf0] rounded-[12px] p-3.5 flex flex-col justify-between">
                        <span class="text-[12px] font-bold text-[#111827] mb-2.5">Perubahan Stok</span>
                        <div class="flex flex-col gap-1.5 text-[11.5px]">
                            <div class="flex items-center justify-between text-[#53565a]">
                                <span>Stok saat ini</span>
                                <span class="font-semibold text-[#111827]" x-text="formatStock(adjustCurrentStock) + ' ' + adjustUnitShort"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[#53565a]" x-text="adjustForm.type === 'in' ? 'Stok masuk' : (adjustForm.type === 'adjustment' ? 'Target stok' : 'Stok keluar')"></span>
                                <span class="font-bold" 
                                      :class="adjustForm.type === 'in' ? 'text-[#00643b]' : (adjustForm.type === 'adjustment' ? 'text-[#6b21a8]' : 'text-[#b91f50]')" 
                                      x-text="!adjustForm.amount ? '-' : ((adjustForm.type === 'in' ? '+' : (adjustForm.type === 'adjustment' ? '=' : '-')) + formatStock(adjustForm.amount) + ' ' + adjustUnitShort)">
                                </span>
                            </div>
                            <div class="border-t border-[#e9ebf0] pt-1.5 flex items-center justify-between text-[#111827]">
                                <span class="font-medium text-[11px]">Stok setelah transaksi</span>
                                <span class="font-bold text-[12.5px]" x-text="formatStock(adjustCalculatedStockAfter) + ' ' + adjustUnitShort"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Perubahan Harga Modal (Figma node 209:8874) -->
                    <div class="bg-[#f8f9fa] border border-[#e9ebf0] rounded-[12px] p-3.5 flex flex-col justify-between">
                        <span class="text-[12px] font-bold text-[#111827] mb-2.5">Perubahan Harga Modal</span>
                        <div class="flex flex-col gap-1.5 text-[11.5px]">
                            <div class="flex items-center justify-between text-[#53565a]">
                                <span>Harga sebelumnya</span>
                                <span class="font-semibold text-[#111827]" x-text="formatRp(adjustOldCost) + '/' + adjustUnitShort"></span>
                            </div>
                            <div class="flex items-center justify-between text-[#53565a]">
                                <span>Harga baru</span>
                                <span class="font-semibold" 
                                      :class="adjustCostDiff !== 0 ? 'text-[#df5826]' : 'text-[#111827]'" 
                                      x-text="Number(adjustForm.new_cost_per_unit) > 0 ? (formatRp(adjustNewCost) + '/' + adjustUnitShort) : '-'">
                                </span>
                            </div>
                            <div class="border-t border-[#e9ebf0] pt-1.5 flex items-center justify-between text-[#111827]">
                                <span class="font-medium text-[11px]">Perubahan Harga</span>
                                <span class="font-bold text-[12px]" 
                                      :class="adjustCostDiff > 0 ? 'text-[#c94d00]' : (adjustCostDiff < 0 ? 'text-[#00643b]' : 'text-[#7f837e]')" 
                                      x-text="adjustCostDiff > 0 ? ('+' + formatRp(adjustCostDiff) + '/' + adjustUnitShort) : (adjustCostDiff < 0 ? ('-' + formatRp(Math.abs(adjustCostDiff)) + '/' + adjustUnitShort) : 'Tidak Berubah')">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Figma node 209:11940) -->
            <div class="mt-6 pt-4 border-t border-[#f1f3f5] flex items-center justify-between gap-3">
                <button type="button" 
                        @click="showAdjustModal = false" 
                        class="w-1/2 h-[42px] rounded-[10px] border border-[#e9ebf0] text-[#53565a] font-semibold text-[13px] hover:bg-gray-50 transition cursor-pointer flex items-center justify-center">
                    Batalkan
                </button>
                <button type="button" 
                        @click="submitStockAdjustment()" 
                        :disabled="isSavingAdjustment" 
                        class="w-1/2 h-[42px] rounded-[10px] bg-[#df5826] hover:bg-[#c94d00] text-white font-semibold text-[13px] transition shadow-xs flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingAdjustment"></i>
                    <span x-text="isSavingAdjustment ? 'Memproses...' : 'Simpan Bahan'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
