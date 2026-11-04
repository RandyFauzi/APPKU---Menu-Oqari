<div x-show="currentTab === 'menu'" x-cloak class="flex-grow p-4 md:p-6 lg:p-8 overflow-y-auto hide-scroll bg-[#f8f9fa]">
    <!-- Page Header (Figma node 78:3093) -->
    <div class="flex flex-col gap-1 mb-5">
        <h1 class="font-bold text-[20px] text-[#111827] tracking-tight">Kelola Menu</h1>
        <p class="text-[13px] text-[#53565a]">Manage your cafe's menu, organize categories, and update product information.</p>
    </div>

    <!-- UploadRow: Grid 2-column (Figma node 78:3096) -->
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-3 mb-5">
        <!-- Left: Upload Area (Dashed Box) -->
        <div class="bg-white border border-dashed border-[#df5826] rounded-[12px] p-5 flex flex-col items-center justify-center text-center shadow-[0px_2px_4px_rgba(38,39,35,0.04)] h-[180px] relative group hover:border-[#c94d00] transition">
            <!-- Cloud Icon in subtle circular badge -->
            <div class="w-10 h-10 rounded-full bg-[#fff6f0] flex items-center justify-center mb-2 shrink-0">
                <img src="/images/sparta/menu-cloud-upload.svg" alt="Upload" class="w-[18px] h-[18px]">
            </div>
            
            <h3 class="font-semibold text-[13px] text-[#111827] mb-0.5">Upload Menu Items</h3>
            <p class="text-[11px] text-[#53565a] leading-tight">Drag and drop images, or click to upload.</p>
            <p class="text-[10px] text-[#7f837e] mb-3">JPG, PNG, up to 10MB.</p>
            
            <button type="button" 
                    @click="initBulkUpload()" 
                    class="bg-[#df5826] hover:bg-[#c94d00] text-white px-5 py-2.5 rounded-[10px] text-[13px] font-semibold shadow-[0px_2px_2px_rgba(27,114,118,0.12)] flex items-center gap-2 transition cursor-pointer">
                <img src="/images/sparta/solar-add-bold.svg" alt="Plus" class="w-4 h-4">
                <span>+ Tambah Menu</span>
            </button>
        </div>

        <!-- Right: Pro Tip Card -->
        <div class="bg-[#fff6f0] border border-[#e9ebf0] rounded-[12px] p-5 relative overflow-hidden flex flex-col justify-between h-[180px]">
            <!-- Background burger image from Figma -->
            <div class="absolute right-[-10px] top-0 bottom-0 w-[180px] pointer-events-none overflow-hidden rounded-r-[12px]">
                <img src="/images/sparta/menu-tip-burger.png" alt="Burger" class="w-full h-full object-cover object-left opacity-95">
                <div class="absolute inset-0 bg-gradient-to-r from-[#fff6f0] via-[#fff6f0]/75 to-transparent"></div>
            </div>

            <!-- Content on the left -->
            <div class="relative z-10 max-w-[210px] flex flex-col justify-between h-full">
                <div>
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <img src="/images/sparta/menu-sparkle.svg" alt="Sparkle" class="w-4 h-4">
                        <span class="text-[10px] font-semibold text-[#c94d00] tracking-wide uppercase">PRO TIP</span>
                    </div>
                    <h4 class="font-bold text-[15px] text-[#111827] leading-[20px] mb-1">Make your menu stand out.</h4>
                    <p class="text-[11px] text-[#53565a] leading-[15px]">Add attractive photos and organized categories to make your menu easier to discover.</p>
                </div>

                <a href="#menu-table" @click.prevent="initBulkUpload()" class="text-[11px] font-semibold text-[#c94d00] hover:underline flex items-center gap-1 mt-2 cursor-pointer">
                    Tips for a great menu →
                </a>
            </div>
        </div>
    </div>

    <!-- Categories Row (Figma node 78:3124) -->
    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
        <div class="flex items-center gap-1.5 flex-wrap">
            <span class="font-semibold text-[13px] text-[#111827] mr-1">Categories</span>
            
            <!-- All Items Chip -->
            <button type="button" 
                    @click="setMenuFilter('all')" 
                    :class="activeMenuFilter === 'all' ? 'bg-[#df5826] text-white font-semibold shadow-xs' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                    class="rounded-full px-3 py-1 text-[11px] transition flex items-center gap-1.5 cursor-pointer">
                <span>All Items</span>
                <span class="opacity-90" x-text="'(' + menuItems.length + ')'"></span>
            </button>
            
            <!-- Dynamic Category Chips -->
            <template x-for="cat in categories" :key="cat.id">
                <button type="button" 
                        @click="setMenuFilter(cat.id)" 
                        :class="(activeMenuFilter == cat.id || String(activeMenuFilter).toLowerCase() == String(cat.name).toLowerCase()) ? 'bg-[#df5826] text-white font-semibold shadow-xs' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                        class="rounded-full px-3 py-1 text-[11px] transition flex items-center gap-1.5 cursor-pointer">
                    <span x-text="cat.name"></span>
                    <span class="opacity-80" x-text="'(' + menuItems.filter(i => i.categoryId == cat.id || String(i.categoryName).toLowerCase() == String(cat.name).toLowerCase()).length + ')'"></span>
                </button>
            </template>
        </div>

        <div class="flex items-center gap-2">
            <!-- Manage Categories Button -->
            <button type="button" 
                    @click="showManageCategoryModal = true" 
                    class="bg-white border border-[#e9ebf0] hover:border-[#df5826] hover:text-[#df5826] text-[#53565a] rounded-[8px] px-3.5 py-1.5 text-[11px] font-medium shadow-[0px_1px_2px_rgba(20,21,20,0.03)] flex items-center gap-1.5 transition cursor-pointer"
                    title="Kelola, rename, atau hapus kategori menu">
                <i class="fas fa-sliders text-[11px]"></i>
                <span>Kelola Kategori</span>
            </button>

            <!-- Add Category Button -->
            <button type="button" 
                    @click="showAddCategoryModal = true" 
                    class="bg-white border border-[#e9ebf0] hover:bg-gray-50 text-[#111827] rounded-[8px] px-3.5 py-1.5 text-[11px] font-medium shadow-[0px_1px_2px_rgba(20,21,20,0.03)] flex items-center gap-1.5 transition cursor-pointer">
                <svg class="w-3 h-3 text-[#111827]" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 1V11M1 6H11" stroke-linecap="round"/>
                </svg>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Table Container (Figma node 78:3141) -->
    <div id="menu-table" class="bg-white border border-[#e6e5e0] rounded-[16px] shadow-[0px_3px_12px_0px_rgba(38,39,35,0.04)] overflow-hidden flex flex-col">
        <!-- Table Header Bar -->
        <div class="px-4 py-3.5 border-b border-[#e9ebf0] flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold text-[14px] text-[#111827]">Menu Items</h3>
                <span class="bg-[#fff6f0] text-[#c94d00] font-semibold text-[10px] rounded-full px-2 py-0.5" x-text="filteredMenuItems.length"></span>
            </div>

            <div class="flex items-center gap-2">
                <!-- Search Box -->
                <div class="bg-[#f8f9fa] border border-[#e9ebf0] focus-within:border-[#df5826] focus-within:bg-white rounded-[8px] h-[32px] px-2.5 flex items-center gap-1.5 w-44 sm:w-56 transition">
                    <svg class="w-3 h-3 text-[#7f837e] shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="7" cy="7" r="5"/>
                        <path d="M11 11L14.5 14.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" x-model="searchQuery" @input="menuPage = 1" placeholder="Search by name…" class="bg-transparent border-none outline-none text-[11px] text-[#111827] placeholder-[#7f837e] w-full">
                </div>

                <!-- Custom Sort Dropdown -->
                <div class="relative" 
                     x-data="{ 
                         openSort: false,
                         sortOptions: [
                             { value: 'newest', label: 'Sort by: Newest', icon: 'fa-clock' },
                             { value: 'price_asc', label: 'Price: Low to High', icon: 'fa-arrow-up-wide-short' },
                             { value: 'price_desc', label: 'Price: High to Low', icon: 'fa-arrow-down-wide-short' }
                         ],
                         get currentSortLabel() {
                             const found = this.sortOptions.find(o => o.value === menuSortOrder);
                             return found ? found.label : 'Sort by: Newest';
                         },
                         selectSort(val) {
                             menuSortOrder = val;
                             menuPage = 1;
                             this.openSort = false;
                         }
                     }"
                     @click.away="openSort = false"
                     @keydown.escape.window="openSort = false">
                    
                    <button type="button" 
                            @click="openSort = !openSort"
                            class="h-[32px] pl-3 pr-2.5 bg-white border rounded-[8px] text-[11px] font-medium text-[#111827] flex items-center gap-2 cursor-pointer shadow-2xs transition-all outline-none"
                            :class="openSort ? 'border-[#df5826] ring-2 ring-[#df5826]/15' : 'border-[#e9ebf0] hover:border-gray-300 hover:bg-[#fafbfc]'">
                        <i class="fas fa-arrow-down-short-wide text-[#7f837e] text-[10px]"></i>
                        <span class="truncate" x-text="currentSortLabel"></span>
                        <i class="fas fa-chevron-down text-[9px] text-[#7f837e] transition-transform duration-200 ml-0.5"
                           :class="openSort ? 'rotate-180 text-[#df5826]' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="openSort"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         x-cloak
                         class="absolute right-0 top-full mt-1.5 z-50 bg-white border border-[#e5e7eb] rounded-[14px] shadow-[0_16px_36px_rgba(15,23,42,0.14)] p-1.5 min-w-[170px]">
                        <template x-for="opt in sortOptions" :key="opt.value">
                            <button type="button" 
                                    @click="selectSort(opt.value)"
                                    class="w-full text-left px-3 py-2 rounded-[8px] text-[11px] font-medium flex items-center justify-between transition cursor-pointer"
                                    :class="menuSortOrder === opt.value ? 'bg-[#fff6f0] text-[#df5826] font-semibold' : 'text-[#374151] hover:bg-gray-50'">
                                <span class="flex items-center gap-2">
                                    <i :class="'fas ' + opt.icon + ' text-[10px] ' + (menuSortOrder === opt.value ? 'text-[#df5826]' : 'text-gray-400')"></i>
                                    <span x-text="opt.label"></span>
                                </span>
                                <i x-show="menuSortOrder === opt.value" class="fas fa-check text-[10px] text-[#df5826] ml-2"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Responsive Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8f9fa] border-b border-[#e9ebf0]">
                        <th class="w-10 px-4 py-3.5 text-center">
                            <input type="checkbox" @change="toggleSelectAllMenus($event)" :checked="isAllMenusSelected" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                        </th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider w-[280px]">ITEM</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider">CATEGORY</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider">PRICE</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider">HPP (MODAL)</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider">STATUS</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold text-[#53565a] uppercase tracking-wider text-right pr-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-for="item in paginatedMenuItems" :key="item.id">
                        <tr class="hover:bg-[#fafafa] transition-colors" :class="item.is_sold_out ? 'bg-[#fafafa]/60 opacity-80' : ''">
                            <td class="w-10 px-4 py-3 text-center align-middle">
                                <input type="checkbox" :value="item.id" x-model="selectedMenuIds" class="w-4 h-4 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-3">
                                    <img :src="item.image || item.img || '/images/sparta/sparta-logo.png'" onerror="this.onerror=null; this.src='/images/sparta/sparta-logo.png'" class="w-[42px] h-[42px] rounded-[8px] object-cover shrink-0 border border-[#e9ebf0]">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-[13px] text-[#111827] leading-snug truncate" x-text="item.name"></span>
                                        <span class="text-[11px] text-[#7f837e] leading-snug truncate" x-text="item.desc || item.description || 'Tidak ada deskripsi'"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold inline-block capitalize" 
                                      :class="getCategoryBadgeClass(item.categoryName || (categories.find(c => c.id == item.categoryId) || {}).name)" 
                                      x-text="item.categoryName || (categories.find(c => c.id == item.categoryId) || {}).name || '-'">
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="text-[13px] font-bold text-[#111827] font-mono" x-text="formatRp(item.price)"></span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-medium text-[#53565a] font-mono" x-text="formatRp(item.cogs || 0)"></span>
                                    <template x-if="item.price > 0 && item.cogs > 0">
                                        <span class="text-[10px] text-[#7f837e] font-medium" x-text="'Margin ' + Math.round(((item.price - item.cogs)/item.price)*100) + '%'"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="flex items-center gap-1.5" x-show="!item.is_sold_out">
                                    <span class="w-[8px] h-[8px] rounded-full bg-[#00643b]"></span>
                                    <span class="text-[12px] font-semibold text-[#00643b]">Published</span>
                                </div>
                                <div class="flex items-center gap-1.5" x-show="item.is_sold_out">
                                    <span class="w-[8px] h-[8px] rounded-full bg-[#ef4444]"></span>
                                    <span class="text-[12px] font-semibold text-[#ef4444]">Sold Out</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle text-right pr-4">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Button (Solar Pen 2 Linear) -->
                                    <button type="button" 
                                            @click="editMenu(item)" 
                                            title="Edit Menu" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 9 11" fill="none" stroke="currentColor">
                                            <path d="M0.375 10.375H8.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M0.504 7.496L0.397 7.817C0.346 7.969 0.386 8.137 0.5 8.251C0.613 8.364 0.781 8.404 0.933 8.353L1.254 8.246L2.567 7.809C2.876 7.706 3.031 7.654 3.177 7.584C3.349 7.503 3.511 7.402 3.661 7.285C3.789 7.186 3.904 7.07 4.135 6.839L7.544 3.431L7.914 3.06C8.529 2.446 8.529 1.45 7.914 0.836C7.3 0.221 6.304 0.221 5.69 0.836L5.319 1.206L1.911 4.615C1.68 4.846 1.564 4.961 1.465 5.089C1.348 5.239 1.247 5.401 1.166 5.573C1.096 5.719 1.045 5.874 0.941 6.183L0.504 7.496Z" stroke-width="0.75" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Solar Trash Bin Minimalistic Linear) -->
                                    <button type="button" 
                                            @click="deleteMenu(item.id)" 
                                            title="Hapus Menu" 
                                            class="w-[28px] h-[28px] rounded-[7px] bg-white border border-[#e9ebf0] hover:border-red-500 text-[#53565a] hover:text-red-500 flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 10 11" fill="none" stroke="currentColor">
                                            <path d="M3.21 1.375C3.416 0.792 3.972 0.375 4.625 0.375C5.278 0.375 5.834 0.792 6.04 1.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M8.875 2.375H0.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M8.042 3.625L7.812 7.075C7.723 8.402 7.679 9.066 7.246 9.47C6.814 9.875 6.149 9.875 4.818 9.875H4.432C3.101 9.875 2.436 9.875 2.003 9.47C1.571 9.066 1.527 8.402 1.438 7.075L1.208 3.625" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M3.375 4.875L3.625 7.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M5.875 4.875L5.625 7.375" stroke-width="0.75" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <!-- Toggle Switch Base (Exact Figma component) -->
                                    <button type="button" 
                                            @click="toggleSoldOut(item.id)" 
                                            class="relative inline-flex h-[20px] w-[32px] shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none" 
                                            :class="!item.is_sold_out ? 'bg-[#df5826]' : 'bg-[#e2e8f0]'" 
                                            title="Toggle Sold Out">
                                        <span class="sr-only">Toggle Sold Out</span>
                                        <span aria-hidden="true" 
                                              class="pointer-events-none inline-block h-[16px] w-[16px] transform rounded-full bg-white shadow-xs transition duration-200 ease-in-out mt-[2px]" 
                                              :class="!item.is_sold_out ? 'translate-x-[14px]' : 'translate-x-[2px]'">
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <template x-if="filteredMenuItems.length === 0">
                        <tr>
                            <td colspan="7" class="text-center py-12 px-4 text-[#7f837e]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <div class="w-10 h-10 rounded-full bg-[#f8f9fa] flex items-center justify-center text-[#7f837e]">
                                        <i class="fas fa-utensils text-sm"></i>
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#111827]">Tidak ada menu ditemukan</p>
                                    <p class="text-[11px] text-[#7f837e]">Coba ganti filter kategori atau kata kunci pencarian.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer (Figma node 78:3436) -->
        <div class="bg-[#f8f9fa] border-t border-[#e9ebf0] px-4 py-3 flex items-center justify-between text-[11px]">
            <p class="text-[#7f837e]">
                Showing <span class="font-medium text-[#111827]" x-text="paginatedMenuItems.length"></span> of <span class="font-medium text-[#111827]" x-text="filteredMenuItems.length"></span> items
            </p>

            <div class="flex items-center gap-1.5" x-show="totalMenuPages > 1">
                <button type="button" 
                        @click="prevMenuPage()" 
                        :disabled="menuPage <= 1" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#7f837e] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer">
                    ←
                </button>

                <template x-for="p in totalMenuPages" :key="p">
                    <button type="button" 
                            @click="menuPage = p" 
                            :class="menuPage === p ? 'bg-[#df5826] text-white font-semibold' : 'bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50'" 
                            class="rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer" 
                            x-text="p">
                    </button>
                </template>

                <button type="button" 
                        @click="nextMenuPage()" 
                        :disabled="menuPage >= totalMenuPages" 
                        class="bg-white border border-[#e9ebf0] disabled:opacity-40 disabled:cursor-not-allowed text-[#53565a] hover:bg-gray-50 rounded-[8px] px-2.5 py-1 text-[11px] transition cursor-pointer">
                    →
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: BULK UPLOAD MENU (Sparta Caffe Theme - 100% matched to screenshot) -->
    <div x-show="showBulkUpload" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 font-sans">
        <!-- Backdrop -->
        <div @click="closeBulkUpload()" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        
        <!-- Modal Content with generous min-height to prevent dropdown cutoff -->
        <div class="relative w-full max-w-4xl mx-auto bg-white rounded-[24px] p-6 sm:p-8 shadow-[0_20px_50px_rgba(0,0,0,0.12)] flex flex-col min-h-[560px] max-h-[90vh] overflow-hidden border border-[#e9ebf0]"
             @keydown.escape.window="closeBulkUpload()" x-transition>
            
            <!-- Header -->
            <div class="flex justify-between items-start mb-5 pb-5 border-b border-dashed border-[#e9ebf0] shrink-0">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-[14px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-xs shrink-0">
                        <img src="/images/sparta/menu-cloud-upload.svg" alt="Upload" class="w-6 h-6">
                    </div>
                    <div>
                        <h2 class="text-[20px] font-bold text-[#111827] leading-tight" x-text="draftMenus.length === 1 && draftMenus[0].id ? 'Edit Menu' : 'Tambah Menu Baru'">Tambah Menu Baru</h2>
                        <p class="text-[#53565a] text-[13px]" x-text="draftMenus.length === 1 && draftMenus[0].id ? 'Perbarui detail, harga, dan foto menu toko Anda.' : 'Lengkapi foto, nama, kategori, dan harga menu toko Anda.'">Lengkapi foto, nama, kategori, dan harga menu toko Anda.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <template x-if="!(draftMenus.length === 1 && draftMenus[0].id)">
                        <div class="flex items-center gap-2">
                            <button type="button" @click="downloadCsvTemplate()" class="bg-white border border-[#e9ebf0] text-[#53565a] hover:text-[#df5826] hover:border-[#df5826] px-3.5 py-2 rounded-xl text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-1.5 cursor-pointer shadow-xs" title="Unduh format template CSV">
                                <i class="fas fa-file-download text-[#df5826]"></i>
                                <span>Template CSV</span>
                            </button>
                            <button type="button" @click="$refs.csvInput.click()" class="bg-white border border-[#e9ebf0] text-[#111827] px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer shadow-xs">
                                <i class="fas fa-file-csv text-green-600"></i>
                                <span>Bulk upload CSV</span>
                            </button>
                        </div>
                    </template>
                    <input type="file" x-ref="csvInput" @change="handleCSVUpload" accept=".csv" class="hidden">
                    <button type="button" @click="closeBulkUpload()" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 transition cursor-pointer ml-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Body with min-height and padding so dropdown never clips -->
            <div class="flex-grow overflow-y-auto py-2 pr-1.5 custom-scroll min-h-[380px]">
                <div class="flex flex-col gap-3 pb-48">
                    <template x-for="(draft, index) in draftMenus" :key="index">
                        <!-- Row Card matching screenshot -->
                        <div x-data="{ 
                                openDropdown: false, 
                                searchCat: '', 
                                isAddingNew: false, 
                                newCatName: '', 
                                isCreatingCat: false,
                                get selectedCategoryName() {
                                    const list = categories || [];
                                    const found = list.find(c => c.id == draft.categoryId || (c.name && String(c.name).toLowerCase() === String(draft.categoryId).toLowerCase()));
                                    return found ? found.name : (draft.categoryId || '');
                                },
                                get filteredCats() {
                                    const list = categories || [];
                                    if (!this.searchCat.trim()) return list;
                                    const q = this.searchCat.toLowerCase();
                                    return list.filter(c => c.name && c.name.toLowerCase().includes(q));
                                },
                                get isExactMatch() {
                                    const list = categories || [];
                                    const q = this.searchCat.trim().toLowerCase();
                                    return list.some(c => c.name && c.name.toLowerCase() === q);
                                },
                                selectCategory(cat) {
                                    draft.categoryId = cat.id;
                                    this.openDropdown = false;
                                    this.searchCat = '';
                                    this.isAddingNew = false;
                                },
                                async handleCreateNew(nameToCreate) {
                                    const name = (nameToCreate || this.newCatName || this.searchCat || '').trim();
                                    if (!name) return;
                                    this.isCreatingCat = true;
                                    const newCat = await createQuickCategory(name, draft);
                                    this.isCreatingCat = false;
                                    if (newCat) {
                                        this.newCatName = '';
                                        this.searchCat = '';
                                        this.isAddingNew = false;
                                        this.openDropdown = false;
                                    }
                                }
                             }"
                             @click.away="openDropdown = false; isAddingNew = false; searchCat = ''"
                             @keydown.escape.window="openDropdown = false"
                             :class="openDropdown ? 'z-30 relative' : 'z-10 relative'"
                             class="flex flex-col md:flex-row items-start md:items-center gap-4 p-4 bg-white rounded-[16px] border border-[#e9ebf0] shadow-xs hover:border-[#dcdfe5] transition">
                            
                            <!-- Image Box -->
                            <div class="w-full md:w-[150px] h-[100px] shrink-0 relative rounded-[14px] overflow-hidden flex flex-col items-center justify-center group cursor-pointer transition border border-dashed border-[#e9ebf0] hover:border-[#df5826] bg-[#fafbfc]">
                                <input type="file" @change="handleDraftImageUpload($event, index)" accept="image/jpeg, image/png, image/webp" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                
                                <template x-if="draft.imagePreview">
                                    <div class="w-full h-full relative">
                                        <img :src="draft.imagePreview" class="w-full h-full object-cover">
                                        <button type="button" @click.stop="removeDraftImage(index)" class="absolute top-1.5 right-1.5 z-30 w-5 h-5 rounded-full bg-black/60 text-white flex items-center justify-center text-[10px] hover:bg-red-600 transition" title="Hapus foto">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </template>
                                
                                <template x-if="!draft.imagePreview">
                                    <div class="text-center flex flex-col items-center justify-center gap-1.5 p-2">
                                        <div class="w-7 h-7 rounded-full bg-[#f0f2f5] flex items-center justify-center text-[#7f837e] group-hover:text-[#df5826] group-hover:bg-[#fff6f0] transition">
                                            <i class="fas fa-cloud-upload-alt text-xs"></i>
                                        </div>
                                        <p class="text-[11px] font-semibold text-[#111827] leading-tight">Upload foto</p>
                                        <p class="text-[9px] text-[#7f837e] font-normal leading-none">JPG, PNG, WEBP</p>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Inputs Grid -->
                            <div class="flex-grow grid grid-cols-1 md:grid-cols-[1.5fr_1.2fr_1fr] gap-3 w-full">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1.5">Item name</label>
                                    <input type="text" x-model="draft.name" placeholder="Contoh: Americano" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[13px] text-[#111827] placeholder:text-[#9ca3af] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                                </div>
                                
                                <!-- Category with Modern Custom Dropdown + Quick Add -->
                                <div class="relative">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-1.5">
                                            <label class="block text-[11px] font-semibold text-[#53565a]">Category</label>
                                            <span class="text-[10px] text-[#7f837e] font-normal" x-text="'(' + (categories ? categories.length : 0) + ')'"></span>
                                        </div>
                                        <button type="button" 
                                                @click.stop="openDropdown = true; isAddingNew = true; $nextTick(() => $refs.inlineNewCatInput?.focus())" 
                                                class="text-[11px] font-semibold text-[#df5826] hover:text-[#c94d00] flex items-center gap-1 cursor-pointer transition group"
                                                title="Buat kategori baru langsung di sini">
                                            <span class="w-3.5 h-3.5 rounded-full bg-[#df5826]/10 text-[#df5826] group-hover:bg-[#df5826] group-hover:text-white flex items-center justify-center text-[9px] transition">
                                                <i class="fas fa-plus"></i>
                                            </span>
                                            <span>Tambah Baru</span>
                                        </button>
                                    </div>

                                    <!-- Dropdown Trigger Button -->
                                    <button type="button" 
                                            @click="openDropdown = !openDropdown; if(openDropdown) { $nextTick(() => $refs.searchCatInput?.focus()); }" 
                                            class="w-full bg-white border rounded-[12px] px-3.5 py-2.5 text-[13px] text-left flex items-center justify-between transition-all cursor-pointer shadow-xs outline-none"
                                            :class="openDropdown ? 'border-[#df5826] ring-2 ring-[#df5826]/15 shadow-sm' : 'border-[#e5e7eb] hover:border-[#df5826]/70 hover:bg-[#fafbfc]'">
                                        <span class="truncate flex items-center gap-2">
                                            <template x-if="selectedCategoryName">
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[7px] bg-[#fff6f0] text-[#df5826] border border-[#ffdec9] font-medium text-[12px] truncate max-w-[190px]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] shrink-0"></span>
                                                    <span class="truncate" x-text="selectedCategoryName"></span>
                                                </span>
                                            </template>
                                            <template x-if="!selectedCategoryName">
                                                <span class="text-[#9ca3af] flex items-center gap-2 text-[12px]">
                                                    <i class="far fa-folder-open text-gray-400 text-xs"></i>
                                                    <span>-- Pilih Kategori --</span>
                                                </span>
                                            </template>
                                        </span>
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 transition-colors"
                                             :class="openDropdown ? 'bg-[#fff6f0] text-[#df5826]' : 'text-gray-400'">
                                            <i class="fas fa-chevron-down text-[11px] transition-transform duration-200" 
                                               :class="openDropdown ? 'rotate-180' : ''"></i>
                                        </div>
                                    </button>

                                    <!-- Dropdown Menu Popover -->
                                    <div x-show="openDropdown" 
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1.5 scale-[0.98]"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1.5 scale-[0.98]"
                                         x-cloak
                                         class="absolute left-0 right-0 top-full mt-2 z-50 bg-white border border-[#e5e7eb] rounded-[18px] shadow-[0_20px_48px_-8px_rgba(15,23,42,0.18),0_2px_6px_rgba(15,23,42,0.04)] p-3 min-w-[280px]">
                                        
                                        <!-- Header inside Dropdown -->
                                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-[#f1f3f5] text-[11px]">
                                            <div class="flex items-center gap-1.5 font-bold text-[#111827] uppercase tracking-wider text-[10px]">
                                                <i class="fas fa-layer-group text-[#df5826]"></i>
                                                <span>Daftar Kategori</span>
                                            </div>
                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#f8f9fa] border border-[#e9ebf0] text-[#7f837e]" 
                                                  x-text="filteredCats.length + ' item'"></span>
                                        </div>

                                        <!-- Search input inside dropdown -->
                                        <div class="relative mb-2.5">
                                            <i class="fas fa-search absolute left-3 top-2.5 text-xs text-gray-400"></i>
                                            <input type="text" 
                                                   x-ref="searchCatInput" 
                                                   x-model="searchCat" 
                                                   @keydown.enter.prevent="if(searchCat.trim() && !isExactMatch) { handleCreateNew(searchCat); }"
                                                   placeholder="Cari atau ketik nama baru..." 
                                                   class="w-full bg-[#f8f9fa] border border-[#e5e7eb] focus:border-[#df5826] focus:bg-white focus:ring-2 focus:ring-[#df5826]/10 rounded-[10px] pl-8.5 pr-8 py-2 text-[12px] text-[#111827] placeholder:text-[#9ca3af] outline-none transition-all">
                                            
                                            <!-- Clear Search Button -->
                                            <button type="button" 
                                                    x-show="searchCat.length > 0" 
                                                    @click="searchCat = ''; $refs.searchCatInput?.focus()" 
                                                    class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 w-5 h-5 rounded-full flex items-center justify-center hover:bg-gray-200 transition text-[11px] cursor-pointer">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>

                                        <!-- Dynamic Creation Card (When No Categories Found) -->
                                        <template x-if="filteredCats.length === 0 && !isAddingNew">
                                            <div class="p-3 my-1 rounded-[14px] bg-gradient-to-br from-[#ffffff] via-[#fffbf8] to-[#fff4ec] border border-[#fed7c3] shadow-xs text-left transition-all">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="w-8 h-8 rounded-[10px] bg-gradient-to-tr from-[#df5826] to-[#ff7844] text-white flex items-center justify-center shrink-0 shadow-xs mt-0.5">
                                                        <i class="fas fa-magic text-[11px]"></i>
                                                    </div>
                                                    <div class="flex-grow min-w-0">
                                                        <div class="flex items-center justify-between gap-1">
                                                            <span class="text-[12px] font-bold text-[#111827]">Kategori Baru</span>
                                                            <span class="text-[9px] text-[#7f837e] bg-white border border-[#e9ebf0] px-1.5 py-0.5 rounded font-mono font-medium">↵ Enter</span>
                                                        </div>
                                                        <p class="text-[11px] text-[#53565a] mt-0.5 leading-tight">Belum ada di daftar menu Anda.</p>
                                                    </div>
                                                </div>

                                                <!-- Live Preview Badge of the New Category -->
                                                <template x-if="searchCat.trim()">
                                                    <div class="mt-2.5 pt-2 border-t border-[#f7e6dc] flex items-center justify-between gap-2">
                                                        <span class="text-[10px] font-semibold text-[#7f837e]">Preview Label:</span>
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fff0e6] text-[#c94d00] border border-[#ffd2b8] shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] animate-pulse"></span>
                                                            <span class="truncate max-w-[130px]" x-text="searchCat.trim()"></span>
                                                        </span>
                                                    </div>
                                                </template>

                                                <!-- Action Button -->
                                                <button type="button" 
                                                        @click="handleCreateNew(searchCat)" 
                                                        :disabled="isCreatingCat || !searchCat.trim()"
                                                        class="mt-2.5 w-full bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white py-2 px-3.5 rounded-[10px] text-[12px] font-bold transition-all shadow-[0_2px_8px_rgba(223,88,38,0.28)] flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                                                    <i :class="isCreatingCat ? 'fas fa-spinner fa-spin' : 'fas fa-plus-circle text-xs'"></i>
                                                    <span x-text="isCreatingCat ? 'Menyimpan Kategori...' : 'Buat & Pilih Kategori Ini'"></span>
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Categories List (When matches exist) -->
                                        <div class="max-h-44 overflow-y-auto space-y-1 pr-1 custom-scroll" x-show="filteredCats.length > 0">
                                            <template x-for="cat in filteredCats" :key="cat.id">
                                                <button type="button" 
                                                        @click="selectCategory(cat)" 
                                                        class="w-full text-left px-3 py-2 rounded-[10px] text-[12px] flex items-center justify-between transition-all cursor-pointer group"
                                                        :class="(draft.categoryId == cat.id || String(draft.categoryId).toLowerCase() === String(cat.name).toLowerCase()) ? 'bg-[#fff6f0] text-[#df5826] font-semibold border border-[#ffdec9]' : 'text-[#374151] hover:bg-gray-50 border border-transparent'">
                                                    <span class="flex items-center gap-2.5 truncate">
                                                        <span class="w-2 h-2 rounded-full shrink-0 transition-transform group-hover:scale-125" 
                                                              :class="(draft.categoryId == cat.id || String(draft.categoryId).toLowerCase() === String(cat.name).toLowerCase()) ? 'bg-[#df5826]' : 'bg-gray-300'"></span>
                                                        <span class="truncate" x-text="cat.name"></span>
                                                    </span>
                                                    <span x-show="(draft.categoryId == cat.id || String(draft.categoryId).toLowerCase() === String(cat.name).toLowerCase())" 
                                                          class="w-4 h-4 rounded-full bg-[#df5826] text-white flex items-center justify-center text-[9px] shrink-0">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Quick create banner when search query has partial matches but not exact match -->
                                        <template x-if="searchCat.trim() && !isExactMatch && filteredCats.length > 0">
                                            <div class="mt-2.5 pt-2.5 border-t border-[#f0f2f5]">
                                                <button type="button" 
                                                        @click="handleCreateNew(searchCat)" 
                                                        :disabled="isCreatingCat"
                                                        class="w-full bg-[#fff6f0] hover:bg-[#ffede0] active:scale-[0.99] text-[#c94d00] border border-[#ffdec9] p-2.5 rounded-[12px] text-[12px] font-semibold transition-all flex items-center justify-between group cursor-pointer disabled:opacity-50 shadow-2xs">
                                                    <div class="flex items-center gap-2 truncate">
                                                        <span class="w-6 h-6 rounded-[8px] bg-[#df5826]/10 text-[#df5826] flex items-center justify-center text-xs group-hover:bg-[#df5826] group-hover:text-white transition">
                                                            <i :class="isCreatingCat ? 'fas fa-spinner fa-spin' : 'fas fa-plus'"></i>
                                                        </span>
                                                        <span class="truncate">Tambah "<strong><span x-text="searchCat.trim()"></span></strong>" Baru</span>
                                                    </div>
                                                    <span class="text-[10px] font-mono text-gray-400 group-hover:text-[#df5826] transition bg-white/70 px-1.5 py-0.5 rounded border border-[#ffd8c2]">↵ Enter</span>
                                                </button>
                                            </div>
                                        </template>

                                        <!-- Bottom Section: Add New Category Option or Inline Form -->
                                        <div class="mt-2 pt-2 border-t border-[#f0f2f5]">
                                            <template x-if="!isAddingNew">
                                                <button type="button" 
                                                        @click="isAddingNew = true; $nextTick(() => $refs.inlineNewCatInput?.focus())" 
                                                        class="w-full px-3 py-2 rounded-[10px] text-[12px] font-semibold text-[#df5826] hover:bg-[#fff6f0] flex items-center justify-between transition cursor-pointer group">
                                                    <span class="flex items-center gap-2">
                                                        <span class="w-5 h-5 rounded-full bg-[#df5826]/10 text-[#df5826] group-hover:bg-[#df5826] group-hover:text-white flex items-center justify-center text-[10px] transition">
                                                            <i class="fas fa-plus"></i>
                                                        </span>
                                                        <span>Tambah Kategori Baru</span>
                                                    </span>
                                                    <span class="text-[10px] text-[#7f837e] group-hover:text-[#df5826] transition font-normal">Form manual</span>
                                                </button>
                                            </template>

                                            <!-- Expanded Inline Form with Live Tag Preview -->
                                            <template x-if="isAddingNew">
                                                <div class="p-3 bg-[#fafbfc] rounded-[14px] border border-[#e5e7eb] space-y-2.5 shadow-2xs">
                                                    <div class="flex items-center justify-between text-[11px] font-bold text-[#111827]">
                                                        <div class="flex items-center gap-1.5">
                                                            <i class="fas fa-tag text-[#df5826] text-xs"></i>
                                                            <span>Kategori Baru</span>
                                                        </div>
                                                        <button type="button" @click="isAddingNew = false; newCatName = ''" class="text-gray-400 hover:text-gray-600 w-5 h-5 rounded-full flex items-center justify-center hover:bg-gray-100 transition cursor-pointer">
                                                            <i class="fas fa-times text-xs"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <div class="relative">
                                                        <input type="text" 
                                                               x-ref="inlineNewCatInput"
                                                               x-model="newCatName" 
                                                               @keydown.enter.prevent="handleCreateNew(newCatName)"
                                                               placeholder="Contoh: Teh, Snack, Mocktail..." 
                                                               class="w-full bg-white border border-[#e5e7eb] focus:border-[#df5826] focus:ring-2 focus:ring-[#df5826]/10 rounded-[10px] px-3 py-2 text-[12px] text-[#111827] outline-none transition">
                                                    </div>

                                                    <!-- Dynamic Preview as user types in manual form -->
                                                    <template x-if="newCatName.trim()">
                                                        <div class="flex items-center justify-between text-[11px] bg-white px-2.5 py-1.5 rounded-[8px] border border-[#f0f2f5]">
                                                            <span class="text-[10px] text-[#7f837e]">Preview:</span>
                                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#fff0e6] text-[#df5826] border border-[#ffd2b8]">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-[#df5826]"></span>
                                                                <span class="truncate max-w-[120px]" x-text="newCatName.trim()"></span>
                                                            </span>
                                                        </div>
                                                    </template>

                                                    <div class="flex items-center justify-end gap-2 pt-1">
                                                        <button type="button" 
                                                                @click="isAddingNew = false; newCatName = ''" 
                                                                class="px-3 py-1.5 bg-white border border-[#e5e7eb] text-[#53565a] hover:bg-gray-50 rounded-[8px] text-[11px] font-semibold transition cursor-pointer">
                                                            Batal
                                                        </button>
                                                        <button type="button" 
                                                                @click="handleCreateNew(newCatName)" 
                                                                :disabled="isCreatingCat || !newCatName.trim()"
                                                                class="px-3.5 py-1.5 bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white rounded-[8px] text-[11px] font-bold transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50 shadow-xs">
                                                            <i :class="isCreatingCat ? 'fas fa-spinner fa-spin' : 'fas fa-save text-[11px]'"></i>
                                                            <span x-text="isCreatingCat ? 'Menyimpan...' : 'Simpan Kategori'"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1.5">Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-[13px] text-[#7f837e] font-medium pointer-events-none">Rp</span>
                                        <input type="number" x-model="draft.price" placeholder="0" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] pl-10 pr-3.5 py-2.5 text-[13px] text-[#111827] placeholder:text-[#9ca3af] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons on the Right -->
                            <div class="shrink-0 flex items-center gap-1.5 self-end md:self-center pt-2 md:pt-4">
                                <template x-if="!(draftMenus.length === 1 && draftMenus[0].id)">
                                    <button type="button" @click="addDraftRow()" class="w-9 h-9 rounded-[10px] bg-white border border-[#df5826] text-[#df5826] hover:bg-[#fff6f0] flex items-center justify-center transition cursor-pointer shadow-xs" title="Tambah Baris Menu">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </template>
                                <template x-if="draftMenus.length > 1">
                                    <button type="button" @click="removeDraftRow(index)" class="w-9 h-9 rounded-[10px] bg-white border border-[#e9ebf0] text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition cursor-pointer shadow-xs" title="Hapus Baris">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Add More Menu Items Button -->
                    <template x-if="!(draftMenus.length === 1 && draftMenus[0].id)">
                        <div class="pt-1 flex items-center justify-between">
                            <button type="button" 
                                    @click="addDraftRow()" 
                                    class="px-4 py-2.5 bg-[#fff6f0] border border-dashed border-[#df5826] hover:bg-[#ffece0] text-[#df5826] font-semibold text-[12px] rounded-[12px] transition flex items-center gap-2 cursor-pointer shadow-2xs">
                                <i class="fas fa-plus text-xs"></i>
                                <span>+ Tambah Baris Menu</span>
                            </button>
                            <span class="text-[11px] text-[#7f837e]">Bisa tambah banyak menu sekaligus beserta kategorinya</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t border-[#e9ebf0] shrink-0 gap-3">
                <div>
                    <template x-if="draftMenus.length === 1 && draftMenus[0].id">
                        <span class="text-[#53565a] font-medium text-[13px]">Sedang mengedit menu: <strong class="text-[#111827]" x-text="draftMenus[0].name || 'Tanpa Nama'"></strong></span>
                    </template>
                    <template x-if="!(draftMenus.length === 1 && draftMenus[0].id)">
                        <div class="text-[13px] text-[#53565a] font-medium">
                            <span class="text-[#df5826] font-bold text-[15px] mr-1" x-text="draftMenus.length"></span> menu siap ditambahkan
                        </div>
                    </template>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" @click="closeBulkUpload()" class="px-5 py-2.5 rounded-[10px] font-semibold text-[13px] bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" @click="saveBulkMenu" :disabled="isSavingBulk" class="px-6 py-2.5 rounded-[10px] font-semibold text-[13px] bg-[#df5826] hover:bg-[#c94d00] text-white transition shadow-sm flex items-center gap-2 cursor-pointer disabled:opacity-50">
                        <i :class="isSavingBulk ? 'fas fa-spinner fa-spin' : (draftMenus.length === 1 && draftMenus[0].id ? 'fas fa-save' : 'fas fa-cloud-upload-alt')"></i>
                        <span x-text="isSavingBulk ? 'Menyimpan...' : (draftMenus.length === 1 && draftMenus[0].id ? 'Simpan Perubahan' : 'Publish Menu')">Publish Menu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>