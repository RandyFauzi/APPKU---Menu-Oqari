<!-- Tab Inventori & Bahan Baku (Figma node 78:4096 / 78:1381) -->
<div x-show="currentTab === 'inventory'" x-cloak class="flex-grow p-4 md:p-6 lg:p-8 overflow-y-auto hide-scroll bg-[#f8f9fa]">
    
    <!-- Page Header (Figma node 78:1382) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
        <div class="flex flex-col gap-1">
            <h1 class="font-bold text-[20px] text-[#111827] tracking-tight">Manajemen Stok & Bahan Baku</h1>
            <p class="text-[13px] text-[#53565a]">Pantau ketersediaan bahan, harga modal (HPP), dan riwayat pergerakan stok cafe.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <!-- Sesuaikan Stok / Opname Button -->
            <button type="button" 
                    @click="openStockAdjustModal()" 
                    class="bg-white border border-[#e9ebf0] hover:bg-gray-50 text-[#111827] rounded-[8px] px-3.5 py-1.5 text-[11px] font-medium shadow-[0px_1px_2px_rgba(20,21,20,0.03)] flex items-center gap-1.5 transition cursor-pointer">
                <img src="/images/sparta/solar-sliders.svg" alt="Opname" class="w-3.5 h-3.5">
                <span>Sesuaikan Stok / Opname</span>
            </button>

            <!-- Tambah Bahan Baku Button -->
            <button type="button" 
                    @click="openAddMaterialModal()" 
                    class="bg-[#df5826] hover:bg-[#c94d00] text-white rounded-[8px] px-3.5 py-1.5 text-[11px] font-semibold flex items-center gap-1.5 shadow-[0px_2px_2px_rgba(249,115,31,0.2)] transition cursor-pointer">
                <img src="/images/sparta/solar-add-bold.svg" alt="Plus" class="w-3 h-3">
                <span>Tambah Bahan Baku</span>
            </button>
        </div>
    </div>

    <!-- KPICards: Grid 3 Kolom (Figma node 78:1396) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
        <!-- Card 1: Stok Menipis (Orange alert card with warning watermark) -->
        <div class="bg-[#f9731f] border border-[#e9ebf0] rounded-[12px] p-4 text-white relative overflow-hidden h-[112px] flex justify-between items-start shadow-[0px_4px_12px_rgba(249,115,31,0.16)]">
            <!-- Duotone Warning Chat Watermark on right -->
            <div class="absolute -right-3 -top-2 w-[140px] h-[140px] pointer-events-none rotate-[12deg] opacity-25">
                <img src="/images/sparta/kpi-warning-chat.svg" alt="Warning" class="w-full h-full">
            </div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-between h-full">
                <span class="text-[12px] text-white/95 font-normal leading-[15px]">Stok Menipis</span>
                <div class="flex flex-col gap-0.5">
                    <span class="text-[24px] font-medium text-white leading-tight" x-text="inventoryMetrics.low_stock_count">0</span>
                    <span class="text-[10px] text-white/90 leading-[14px]">Perlu segera direstok dari supplier</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Item Bahan (White card with box icon) -->
        <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 drop-shadow-[0px_2px_4px_rgba(38,39,35,0.04)] h-[112px] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-[#53565a]">Total Item Bahan</span>
                <div class="w-7 h-7 shrink-0">
                    <img src="/images/sparta/kpi-box-duotone.svg" alt="Box" class="w-full h-full">
                </div>
            </div>
            <div class="flex flex-col">
                <span class="text-[22px] font-bold text-[#111827] leading-[28px]" x-text="inventoryMetrics.total_materials">0</span>
                <span class="text-[10px] text-[#7f837e] leading-[14px]">Bahan aktif untuk menu & racikan</span>
            </div>
        </div>

        <!-- Card 3: Nilai Modal Aset Bahan (White card with wallet icon) -->
        <div class="bg-white border border-[#e9ebf0] rounded-[12px] p-4 drop-shadow-[0px_2px_4px_rgba(38,39,35,0.04)] h-[112px] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-[#53565a]">Nilai Modal Aset Bahan</span>
                <div class="w-7 h-7 shrink-0">
                    <img src="/images/sparta/kpi-wallet-duotone.svg" alt="Wallet" class="w-full h-full">
                </div>
            </div>
            <div class="flex flex-col">
                <span class="text-[22px] font-bold text-[#111827] leading-[28px]" x-text="formatRp(inventoryMetrics.total_asset_value)">Rp 0</span>
                <span class="text-[10px] text-[#7f837e] leading-[14px]">Estimasi nilai modal seluruh stok tersedia</span>
            </div>
        </div>
    </div>

    <!-- TabsControlsRow (Figma node 78:1427) -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2.5">
        <!-- Sub-tabs Left -->
        <div class="flex items-center gap-1">
            <!-- Tab 1: Daftar Bahan Baku -->
            <button type="button" 
                    @click="inventorySubTab = 'materials'" 
                    :class="inventorySubTab === 'materials' ? 'bg-[#df5826] text-white font-semibold shadow-xs' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                    class="rounded-[8px] px-3.5 py-1.5 text-[12px] transition cursor-pointer">
                <span>Daftar Bahan Baku</span>
                <span class="opacity-90" x-text="'(' + rawMaterials.length + ')'"></span>
            </button>

            <!-- Tab 2: Riwayat Mutasi Stok -->
            <button type="button" 
                    @click="inventorySubTab = 'mutations'" 
                    :class="inventorySubTab === 'mutations' ? 'bg-[#df5826] text-white font-semibold shadow-xs' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                    class="rounded-[8px] px-3.5 py-1.5 text-[12px] flex items-center gap-1.5 transition cursor-pointer">
                <img src="/images/sparta/solar-history.svg" alt="History" class="w-3 h-3" :class="inventorySubTab === 'mutations' ? 'brightness-0 invert' : ''">
                <span>Riwayat Mutasi Stok</span>
            </button>
        </div>

        <!-- Table Controls Right (Visible on materials sub-tab) -->
        <div class="flex items-center gap-1.5 flex-wrap" x-show="inventorySubTab === 'materials'">
            <!-- Search Box -->
            <div class="bg-[#f8f9fa] border border-[#e9ebf0] focus-within:border-[#df5826] focus-within:bg-white rounded-[8px] h-[30px] px-2.5 flex items-center gap-1.5 w-36 sm:w-44 transition">
                <img src="/images/sparta/solar-magnifer.svg" alt="Search" class="w-3 h-3 shrink-0">
                <input type="text" x-model="materialSearch" @input="materialPage = 1" placeholder="Cari nama bahan..." class="bg-transparent border-none outline-none text-[11px] text-[#111827] placeholder-[#7f837e] w-full">
            </div>

            <!-- Kategori Dropdown -->
            <div class="relative">
                <select x-model="materialFilterCategory" @change="materialPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[8px] h-[30px] pl-2.5 pr-6 text-[11px] font-medium text-[#111827] cursor-pointer outline-none transition">
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
                <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Status Dropdown -->
            <div class="relative">
                <select x-model="materialFilterStatus" @change="materialPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[8px] h-[30px] pl-2.5 pr-6 text-[11px] font-medium text-[#111827] cursor-pointer outline-none transition">
                    <option value="all">Semua Status</option>
                    <option value="Aman">Aman</option>
                    <option value="Menipis">Menipis</option>
                    <option value="Habis">Habis</option>
                </select>
                <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Sort Dropdown -->
            <div class="relative">
                <select x-model="materialSortOrder" @change="materialPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[8px] h-[30px] pl-2.5 pr-6 text-[11px] font-medium text-[#111827] cursor-pointer outline-none transition">
                    <option value="name_asc">Urutkan: Nama A-Z</option>
                    <option value="name_desc">Urutkan: Nama Z-A</option>
                    <option value="stock_asc">Stok: Paling Sedikit</option>
                    <option value="stock_desc">Stok: Paling Banyak</option>
                    <option value="cost_desc">Harga: Tertinggi</option>
                    <option value="cost_asc">Harga: Terendah</option>
                </select>
                <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <!-- Refresh Button -->
            <button type="button" 
                    @click="fetchInventory()" 
                    class="bg-white border border-[#e9ebf0] hover:bg-gray-50 rounded-[8px] w-[30px] h-[30px] flex items-center justify-center transition cursor-pointer shadow-xs" 
                    title="Refresh Data">
                <img src="/images/sparta/solar-refresh.svg" alt="Refresh" class="w-3 h-3" :class="isLoadingInventory ? 'animate-spin' : ''">
            </button>
        </div>
    </div>

    <!-- VIEW 1: TABEL DAFTAR BAHAN BAKU (Figma node 78:1461) -->
    <div x-show="inventorySubTab === 'materials'" class="bg-white border border-[#e6e5e0] rounded-[16px] shadow-[0px_3px_12px_0px_rgba(38,39,35,0.04)] overflow-hidden flex flex-col mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8f9fa] border-b border-[#e9ebf0]">
                        <th class="w-[36px] pl-4 py-3.5 text-center">
                            <input type="checkbox" @change="toggleSelectAllMaterials($event)" :checked="isAllMaterialsSelected" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                        </th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[220px]">NAMA BAHAN</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[100px]">SKU / KODE</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[105px]">KATEGORI</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[75px]">SATUAN</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[120px]">HARGA BELI/UNIT</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[105px]">SISA STOK</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[95px]">MIN. STOK</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[90px]">STATUS</th>
                        <th class="px-3 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[95px]">MENU TERKAIT</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider text-right pr-4">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-for="item in paginatedMaterials" :key="item.id">
                        <tr class="transition-colors" 
                            :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#fff0f4]/80 hover:bg-[#fff0f4]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#fff6f0]/70 hover:bg-[#fff6f0]' : 'bg-white hover:bg-[#fafafa]')">
                            <!-- Checkbox -->
                            <td class="w-[36px] pl-4 py-3 text-center align-middle">
                                <input type="checkbox" :value="item.id" x-model="selectedMaterialIds" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                            </td>

                            <!-- Nama Bahan -->
                            <td class="px-3 py-3 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <!-- Icon Box -->
                                    <div class="w-[28px] h-[28px] rounded-[6px] flex items-center justify-center shrink-0"
                                         :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#fff0f4]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#fff6f0]' : 'bg-[#f1f3f5]')">
                                        <template x-if="getMaterialStatus(item) === 'Aman'">
                                            <img src="/images/sparta/solar-box-linear.svg" alt="Aman" class="w-3.5 h-3.5">
                                        </template>
                                        <template x-if="getMaterialStatus(item) === 'Menipis'">
                                            <img src="/images/sparta/solar-danger-triangle.svg" alt="Menipis" class="w-3.5 h-3.5">
                                        </template>
                                        <template x-if="getMaterialStatus(item) === 'Habis'">
                                            <img src="/images/sparta/solar-danger-circle.svg" alt="Habis" class="w-3.5 h-3.5">
                                        </template>
                                    </div>
                                    <span class="font-bold text-[13px] leading-snug truncate" 
                                          :class="getMaterialStatus(item) === 'Habis' ? 'text-[#b91f50]' : 'text-[#111827]'" 
                                          x-text="item.name">
                                    </span>
                                </div>
                            </td>

                            <!-- SKU / Kode -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-medium text-[12px] text-[#53565a] font-mono" x-text="item.sku || '-'"></span>
                            </td>

                            <!-- Kategori -->
                            <td class="px-3 py-3 align-middle">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold inline-block capitalize" 
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
                                      x-text="formatStock(item.current_stock) + ' ' + (item.unit_label || stockUnitLabel(item.unit))">
                                </span>
                            </td>

                            <!-- Min. Stok -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-medium text-[12px] text-[#53565a]" x-text="formatStock(item.minimum_stock) + ' ' + (item.unit_label || stockUnitLabel(item.unit))"></span>
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-3 align-middle">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                                     :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#ffe8ef]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#fff3e8]' : 'bg-[#eafbf2]')">
                                    <span class="w-[6px] h-[6px] rounded-full"
                                          :class="getMaterialStatus(item) === 'Habis' ? 'bg-[#b91f50]' : (getMaterialStatus(item) === 'Menipis' ? 'bg-[#c94d00]' : 'bg-[#00643b]')"></span>
                                    <span class="text-[11px] font-semibold leading-[14px]"
                                          :class="getMaterialStatus(item) === 'Habis' ? 'text-[#b91f50]' : (getMaterialStatus(item) === 'Menipis' ? 'text-[#c94d00]' : 'text-[#00643b]')"
                                          x-text="getMaterialStatus(item)">
                                    </span>
                                </div>
                            </td>

                            <!-- Menu Terkait -->
                            <td class="px-3 py-3 align-middle">
                                <span class="font-bold text-[12px] text-[#df5826]" x-text="(item.recipes_count || 0) + ' Menu'"></span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3 align-middle text-right pr-4">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Restok Button -->
                                    <button type="button" 
                                            @click="quickRestock(item)" 
                                            class="px-3 py-1.5 rounded-[6px] text-[11px] font-bold transition cursor-pointer shadow-xs"
                                            :class="getMaterialStatus(item) === 'Menipis' || getMaterialStatus(item) === 'Habis' ? 'bg-[#df5826] hover:bg-[#c94d00] text-white' : 'border border-[#ffd6bd] bg-white text-[#c94d00] hover:bg-[#fff6f0]'">
                                        Restok
                                    </button>

                                    <!-- Edit Button (Solar Pen 2 Linear) -->
                                    <button type="button" 
                                            @click="editMaterial(item)" 
                                            title="Edit Bahan" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 9 11" fill="none" stroke="currentColor">
                                            <path d="M0.375 10.375H8.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M0.504 7.496L0.397 7.817C0.346 7.969 0.386 8.137 0.5 8.251C0.613 8.364 0.781 8.404 0.933 8.353L1.254 8.246L2.567 7.809C2.876 7.706 3.031 7.654 3.177 7.584C3.349 7.503 3.511 7.402 3.661 7.285C3.789 7.186 3.904 7.07 4.135 6.839L7.544 3.431L7.914 3.06C8.529 2.446 8.529 1.45 7.914 0.836C7.3 0.221 6.304 0.221 5.69 0.836L5.319 1.206L1.911 4.615C1.68 4.846 1.564 4.961 1.465 5.089C1.348 5.239 1.247 5.401 1.166 5.573C1.096 5.719 1.045 5.874 0.941 6.183L0.504 7.496Z" stroke-width="0.75" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Solar Trash Bin Minimalistic Linear) -->
                                    <button type="button" 
                                            @click="deleteMaterial(item.id)" 
                                            title="Hapus Bahan" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-red-500 text-[#53565a] hover:text-red-500 flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 10 11" fill="none" stroke="currentColor">
                                            <path d="M3.21 1.375C3.416 0.792 3.972 0.375 4.625 0.375C5.278 0.375 5.834 0.792 6.04 1.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M8.875 2.375H0.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M8.042 3.625L7.812 7.075C7.723 8.402 7.679 9.066 7.246 9.47C6.814 9.875 6.149 9.875 4.818 9.875H4.432C3.101 9.875 2.436 9.875 2.003 9.47C1.571 9.066 1.527 8.402 1.438 7.075L1.208 3.625" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M3.375 4.875L3.625 7.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M5.875 4.875L5.625 7.375" stroke-width="0.75" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredMaterials.length === 0">
                        <tr>
                            <td colspan="11" class="text-center py-12 px-4 text-[#7f837e]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-10 h-10 rounded-full bg-[#f8f9fa] flex items-center justify-center text-[#7f837e]">
                                        <i class="fas fa-boxes-stacked text-sm"></i>
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#111827]">Belum ada data bahan baku</p>
                                    <p class="text-[11px] text-[#7f837e]">Tambahkan bahan racikan seperti Kopi, Susu, Sirup untuk mulai kalkulasi HPP otomatis.</p>
                                    <button type="button" @click="openAddMaterialModal()" class="mt-2 px-3.5 py-1.5 bg-[#df5826] hover:bg-[#c94d00] text-white rounded-[8px] text-[11px] font-semibold transition cursor-pointer">
                                        + Tambah Bahan Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer (Figma node 78:2027) -->
        <div class="bg-[#f8f9fa] border-t border-[#e9ebf0] px-4 py-3 flex items-center justify-between text-[11px]">
            <p class="text-[#7f837e]">
                Menampilkan <span class="font-medium text-[#111827]" x-text="paginatedMaterials.length"></span> dari <span class="font-medium text-[#111827]" x-text="filteredMaterials.length"></span> bahan baku
            </p>

            <div class="flex items-center gap-1.5" x-show="totalMaterialPages > 1">
                <button type="button" 
                        @click="prevMaterialPage()" 
                        :disabled="materialPage <= 1" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#7f837e] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer">
                    ←
                </button>

                <template x-for="p in totalMaterialPages" :key="p">
                    <button type="button" 
                            @click="materialPage = p" 
                            :class="materialPage === p ? 'bg-[#df5826] text-white font-semibold' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                            class="rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer" 
                            x-text="p">
                    </button>
                </template>

                <button type="button" 
                        @click="nextMaterialPage()" 
                        :disabled="materialPage >= totalMaterialPages" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#53565a] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer">
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
                                <template x-if="mut.type === 'out_order'">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#eef0ff] text-[#2a3ba2]">Terpakai Order</span>
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
                                <span class="text-[11px] text-[#7f837e] font-normal" x-text="mut.unit_label || stockUnitLabel(mut.unit || mut.raw_material?.unit)"></span>
                            </td>
                            <td class="py-3 px-3 font-mono text-[12px] font-semibold text-[#111827]">
                                <span x-text="formatStock(mut.balance_after)"></span>
                                <span class="text-[10px] text-[#7f837e] font-normal" x-text="mut.unit_label || stockUnitLabel(mut.unit || mut.raw_material?.unit)"></span>
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

    <!-- MODAL: TAMBAH / EDIT BAHAN BAKU -->
    <div x-show="showMaterialModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showMaterialModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[24px] p-6 sm:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.08)] flex flex-col border border-[#e9ebf0]" @keydown.escape.window="showMaterialModal = false" x-transition>
            
            <div class="flex justify-between items-start mb-5 pb-4 border-b border-dashed border-[#e9ebf0]">
                <div class="flex gap-3.5 items-center">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-xs">
                        <img src="/images/sparta/kpi-box-duotone.svg" alt="Material" class="w-5 h-5">
                    </div>
                    <div>
                        <h2 class="text-[17px] font-bold text-[#111827]" x-text="materialForm.id ? 'Edit Bahan Baku' : 'Tambah Bahan Baku'"></h2>
                        <p class="text-[11px] text-[#53565a]">Tentukan takaran, satuan, dan harga modal beli.</p>
                    </div>
                </div>
                <button type="button" @click="showMaterialModal = false" class="text-[#7f837e] hover:text-[#111827] w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="flex flex-col gap-3.5">
                <div>
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Nama Bahan Baku *</label>
                    <input type="text" x-model="materialForm.name" placeholder="cth: Susu Fresh Milk Greenfields, Biji Arabika Gayo" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="stock-unit" class="block text-[11px] font-semibold text-[#53565a] mb-1">Satuan Dasar *</label>
                        <select id="stock-unit" x-model="materialForm.unit" :disabled="editingUnitLocked" aria-describedby="stock-unit-help" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white disabled:opacity-60 outline-none">
                            <template x-for="(label, unit) in stockUnits" :key="unit"><option :value="unit" x-text="label"></option></template>
                            <template x-if="materialForm.unit && !stockUnits[materialForm.unit]"><option :value="materialForm.unit" x-text="materialForm.unit + ' (satuan lama)'"></option></template>
                        </select>
                        <p id="stock-unit-help" class="text-[9px] text-[#7f837e] mt-1" x-text="editingUnitLocked ? 'Satuan dikunci karena bahan sudah memiliki stok/resep.' : 'Satuan untuk modal dan racikan resep.'"></p>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#53565a] mb-1">SKU / Kode Bahan</label>
                        <input type="text" x-model="materialForm.sku" placeholder="cth: RM-COF-01" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Harga Beli / Satuan (Rp) *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-[11px] text-[#7f837e]">Rp</span>
                            <input type="number" step="0.000001" min="0" x-model="materialForm.cost_per_unit" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-8 pr-3 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                        </div>
                        <span class="text-[9px] text-[#7f837e] mt-0.5 block">Modal per 1 <span x-text="stockUnitLabel(materialForm.unit)"></span></span>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Batas Stok Minimum *</label>
                        <input type="number" step="0.000001" min="0" x-model="materialForm.minimum_stock" :aria-label="'Batas stok minimum dalam ' + stockUnitLabel(materialForm.unit)" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                        <span class="text-[9px] text-[#7f837e] mt-0.5 block">Alert jika di bawah batas ini</span>
                    </div>
                </div>

                <div x-show="!materialForm.id">
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Saldo Stok Awal</label>
                    <input type="number" step="0.000001" min="0" x-model="materialForm.current_stock" :aria-label="'Stok awal dalam ' + stockUnitLabel(materialForm.unit)" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#e9ebf0] flex justify-end gap-2.5">
                <button type="button" @click="showMaterialModal = false" class="px-4 py-2 rounded-[8px] border border-[#e9ebf0] text-[#53565a] font-semibold text-[11px] hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" @click="saveMaterial()" :disabled="isSavingMaterial" class="px-5 py-2 rounded-[8px] bg-[#df5826] hover:bg-[#c94d00] text-white font-semibold text-[11px] transition shadow-xs flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingMaterial"></i>
                    <span x-text="isSavingMaterial ? 'Menyimpan...' : 'Simpan Bahan'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: PENYESUAIAN STOK (RESTOK / OPNAME / WASTE) -->
    <div x-show="showAdjustModal" x-cloak class="fixed inset-0 z-[120] flex items-center justify-center p-4 font-sans">
        <div @click="showAdjustModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        <div class="relative bg-white w-full max-w-lg rounded-[24px] p-6 sm:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.08)] flex flex-col border border-[#e9ebf0]" @keydown.escape.window="showAdjustModal = false" x-transition>
            
            <div class="flex justify-between items-start mb-5 pb-4 border-b border-dashed border-[#e9ebf0]">
                <div class="flex gap-3.5 items-center">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-xs">
                        <img src="/images/sparta/solar-sliders.svg" alt="Adjust" class="w-5 h-5">
                    </div>
                    <div>
                        <h2 class="text-[17px] font-bold text-[#111827]">Penyesuaian Stok</h2>
                        <p class="text-[11px] text-[#53565a]">Restok belanja, stok opname, atau catat barang tumpah/rusak.</p>
                    </div>
                </div>
                <button type="button" @click="showAdjustModal = false" class="text-[#7f837e] hover:text-[#111827] w-7 h-7 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="flex flex-col gap-3.5">
                <div>
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Pilih Bahan Baku *</label>
                    <select x-model="adjustForm.raw_material_id" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                        <option value="" disabled>-- Pilih Bahan --</option>
                        <template x-for="m in rawMaterials" :key="m.id">
                            <option :value="m.id" x-text="m.name + ' (Sisa saat ini: ' + formatStock(m.current_stock) + ' ' + (m.unit_label || stockUnitLabel(m.unit)) + ')'"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Tipe Mutasi *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" @click="adjustForm.type = 'in'" :class="adjustForm.type === 'in' ? 'bg-[#df5826] text-white font-semibold' : 'bg-[#f8f9fa] border border-[#e9ebf0] text-[#53565a] font-medium hover:bg-gray-50'" class="py-2 rounded-[8px] text-[11px] transition">
                            <i class="fas fa-plus-circle mr-1"></i> Restok Masuk
                        </button>
                        <button type="button" @click="adjustForm.type = 'adjustment'" :class="adjustForm.type === 'adjustment' ? 'bg-[#df5826] text-white font-semibold' : 'bg-[#f8f9fa] border border-[#e9ebf0] text-[#53565a] font-medium hover:bg-gray-50'" class="py-2 rounded-[8px] text-[11px] transition">
                            <i class="fas fa-clipboard-check mr-1"></i> Stok Opname
                        </button>
                        <button type="button" @click="adjustForm.type = 'waste'" :class="adjustForm.type === 'waste' ? 'bg-red-600 text-white font-semibold' : 'bg-[#f8f9fa] border border-[#e9ebf0] text-[#53565a] font-medium hover:bg-gray-50'" class="py-2 rounded-[8px] text-[11px] transition">
                            <i class="fas fa-trash-alt mr-1"></i> Rusak/Basi
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">
                        <span x-text="adjustForm.type === 'adjustment' ? 'Jumlah Riil Stok Fisik di Gudang *' : 'Jumlah Bahan *'"></span>
                    </label>
                    <input type="number" step="0.000001" min="0" :min="adjustForm.type === 'adjustment' ? 0 : 0.000001" x-model="adjustForm.amount" placeholder="0" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                    <p class="text-[10px] text-[#df5826] mt-1" x-text="'Semua jumlah dalam ' + getMaterialUnit(adjustForm.raw_material_id) + '.'"></p>
                </div>

                <div x-show="adjustForm.type === 'in'">
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Harga Beli Baru / Satuan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-[11px] text-[#7f837e]">Rp</span>
                        <input type="number" step="0.000001" min="0" x-model="adjustForm.new_cost_per_unit" placeholder="Kosongkan jika harga modal tidak berubah" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] pl-8 pr-3 py-2 text-[12px] font-medium text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Catatan / Alasan</label>
                    <input type="text" x-model="adjustForm.notes" placeholder="cth: Belanja dari Toko Jaya, Gelas retak, Opname akhir bulan" class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2 text-[12px] text-[#111827] focus:border-[#df5826] focus:bg-white outline-none">
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#e9ebf0] flex justify-end gap-2.5">
                <button type="button" @click="showAdjustModal = false" class="px-4 py-2 rounded-[8px] border border-[#e9ebf0] text-[#53565a] font-semibold text-[11px] hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" @click="submitStockAdjustment()" :disabled="isSavingAdjustment" class="px-5 py-2 rounded-[8px] bg-[#df5826] hover:bg-[#c94d00] text-white font-semibold text-[11px] transition shadow-xs flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin" x-show="isSavingAdjustment"></i>
                    <span x-text="isSavingAdjustment ? 'Memproses...' : 'Terapkan Penyesuaian'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
