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
            
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="showAddMenuModal = true" 
                        class="bg-[#df5826] hover:bg-[#c94d00] text-white px-4 py-2 rounded-[9px] text-[12px] font-medium shadow-[0px_2px_2px_rgba(27,114,118,0.12)] flex items-center gap-1.5 transition cursor-pointer">
                    <img src="/images/sparta/solar-add-bold.svg" alt="Plus" class="w-3.5 h-3.5">
                    <span>+ Tambah Menu Baru</span>
                </button>
                <button type="button" 
                        @click="initBulkUpload()" 
                        class="bg-white border border-[#e9ebf0] hover:bg-[#fff6f0] hover:border-[#df5826] text-[#df5826] px-3 py-2 rounded-[9px] text-[11px] font-medium transition cursor-pointer flex items-center gap-1">
                    <i class="fas fa-file-csv text-xs"></i>
                    <span>Bulk Upload</span>
                </button>
            </div>
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

                <!-- Sort Dropdown -->
                <div class="relative">
                    <select x-model="menuSortOrder" @change="menuPage = 1" class="appearance-none bg-white border border-[#e9ebf0] hover:border-gray-300 rounded-[8px] h-[32px] pl-2.5 pr-7 text-[11px] font-medium text-[#111827] cursor-pointer outline-none transition">
                        <option value="newest">Sort by: Newest</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                    <svg class="w-2.5 h-2.5 text-[#53565a] absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" viewBox="0 0 10 6" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M1 1L5 5L9 1" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Table Responsive Wrapper -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f8f9fa] border-b border-[#e9ebf0]">
                        <th class="w-10 px-4 py-2.5 text-center">
                            <input type="checkbox" @change="toggleSelectAllMenus($event)" :checked="isAllMenusSelected" class="w-3.5 h-3.5 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                        </th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider w-[280px]">ITEM</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider">CATEGORY</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider">PRICE</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider">HPP (MODAL)</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider">STATUS</th>
                        <th class="px-4 py-2.5 text-[10px] font-semibold text-[#7f837e] uppercase tracking-wider text-right pr-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-for="item in paginatedMenuItems" :key="item.id">
                        <tr class="hover:bg-[#fafafa] transition-colors" :class="item.is_sold_out ? 'bg-[#fafafa]/60 opacity-80' : ''">
                            <td class="w-10 px-4 py-2.5 text-center align-middle">
                                <input type="checkbox" :value="item.id" x-model="selectedMenuIds" class="w-3.5 h-3.5 rounded border-[#d8dce2] text-[#df5826] focus:ring-[#df5826] cursor-pointer">
                            </td>
                            <td class="px-4 py-2.5 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <img :src="item.image || item.img || '/images/sparta/sparta-logo.png'" onerror="this.onerror=null; this.src='/images/sparta/sparta-logo.png'" class="w-[36px] h-[36px] rounded-[6px] object-cover shrink-0 border border-[#e9ebf0]">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-semibold text-[12px] text-[#111827] leading-[17px] truncate" x-text="item.name"></span>
                                        <span class="text-[10px] text-[#7f837e] leading-[14px] truncate" x-text="item.desc || item.description || 'Tidak ada deskripsi'"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 align-middle">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium inline-block capitalize" 
                                      :class="getCategoryBadgeClass(item.categoryName || (categories.find(c => c.id == item.categoryId) || {}).name)" 
                                      x-text="item.categoryName || (categories.find(c => c.id == item.categoryId) || {}).name || '-'">
                                </span>
                            </td>
                            <td class="px-4 py-2.5 align-middle">
                                <span class="text-[12px] font-medium text-[#111827]" x-text="formatRp(item.price)"></span>
                            </td>
                            <td class="px-4 py-2.5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[12px] text-[#53565a]" x-text="formatRp(item.cogs || 0)"></span>
                                    <template x-if="item.price > 0 && item.cogs > 0">
                                        <span class="text-[9px] text-[#7f837e]" x-text="'Margin ' + Math.round(((item.price - item.cogs)/item.price)*100) + '%'"></span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 align-middle">
                                <div class="flex items-center gap-1.5" x-show="!item.is_sold_out">
                                    <span class="w-[7px] h-[7px] rounded-full bg-[#00643b]"></span>
                                    <span class="text-[11px] font-normal text-[#00643b]">Published</span>
                                </div>
                                <div class="flex items-center gap-1.5" x-show="item.is_sold_out">
                                    <span class="w-[7px] h-[7px] rounded-full bg-[#ef4444]"></span>
                                    <span class="text-[11px] font-normal text-[#ef4444]">Sold Out</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 align-middle text-right pr-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Button (Solar Pen 2 Linear) -->
                                    <button type="button" 
                                            @click="editMenu(item)" 
                                            title="Edit Menu" 
                                            class="w-[26px] h-[26px] rounded-[6px] bg-white border border-[#e9ebf0] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3 h-3" viewBox="0 0 9 11" fill="none" stroke="currentColor">
                                            <path d="M0.375 10.375H8.375" stroke-width="0.75" stroke-linecap="round"/>
                                            <path d="M0.504 7.496L0.397 7.817C0.346 7.969 0.386 8.137 0.5 8.251C0.613 8.364 0.781 8.404 0.933 8.353L1.254 8.246L2.567 7.809C2.876 7.706 3.031 7.654 3.177 7.584C3.349 7.503 3.511 7.402 3.661 7.285C3.789 7.186 3.904 7.07 4.135 6.839L7.544 3.431L7.914 3.06C8.529 2.446 8.529 1.45 7.914 0.836C7.3 0.221 6.304 0.221 5.69 0.836L5.319 1.206L1.911 4.615C1.68 4.846 1.564 4.961 1.465 5.089C1.348 5.239 1.247 5.401 1.166 5.573C1.096 5.719 1.045 5.874 0.941 6.183L0.504 7.496Z" stroke-width="0.75" stroke-linecap="round"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Button (Solar Trash Bin Minimalistic Linear) -->
                                    <button type="button" 
                                            @click="deleteMenu(item.id)" 
                                            title="Hapus Menu" 
                                            class="w-[26px] h-[26px] rounded-[6px] bg-white border border-[#e9ebf0] hover:border-red-500 text-[#53565a] hover:text-red-500 flex items-center justify-center transition shadow-xs cursor-pointer">
                                        <svg class="w-3 h-3" viewBox="0 0 10 11" fill="none" stroke="currentColor">
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

    <!-- MODAL: BULK UPLOAD MENU (Preserved & Enhanced to match theme) -->
    <div x-show="showBulkUpload" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-10 font-sans">
        <!-- Backdrop -->
        <div @click="showBulkUpload = false" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-5xl mx-auto bg-white rounded-[24px] p-6 sm:p-8 shadow-[0_10px_35px_rgba(0,0,0,0.08)] flex flex-col max-h-[90vh] overflow-hidden border border-[#e9ebf0]">
            
            <!-- Header -->
            <div class="flex justify-between items-start mb-5 pb-5 border-b border-dashed border-[#e9ebf0] shrink-0">
                <div class="flex gap-4 items-center">
                    <div class="w-12 h-12 rounded-[14px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center shadow-xs shrink-0">
                        <img src="/images/sparta/menu-cloud-upload.svg" alt="Upload" class="w-6 h-6">
                    </div>
                    <div>
                        <h2 class="text-[20px] font-bold text-[#111827] leading-tight">Upload Menu</h2>
                        <p class="text-[#53565a] text-[13px]">Add your coffee and food items with images, prices, and categories.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="$refs.csvInput.click()" class="bg-white border border-[#e9ebf0] text-[#111827] px-4 py-2 rounded-xl text-xs font-semibold hover:bg-gray-50 transition flex items-center gap-2 cursor-pointer shadow-xs">
                        <i class="fas fa-file-csv text-green-600"></i> Bulk upload CSV
                    </button>
                    <input type="file" x-ref="csvInput" @change="handleCSVUpload" accept=".csv" class="hidden">
                    <button type="button" @click="showBulkUpload = false" class="text-gray-400 hover:text-gray-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-100 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div class="flex-grow overflow-y-auto hide-scroll py-2 pr-1">
                <div class="flex flex-col gap-3 pb-6">
                    <template x-for="(draft, index) in draftMenus" :key="index">
                        <!-- Row -->
                        <div class="flex flex-col md:flex-row items-start md:items-center gap-4 p-4 bg-[#f8f9fa] rounded-[16px] border border-[#e9ebf0]">
                            <!-- Image Box -->
                            <div class="w-full md:w-[180px] h-[130px] md:h-[110px] shrink-0 relative rounded-xl overflow-hidden flex items-center justify-center group cursor-pointer transition border border-dashed border-[#e9ebf0] hover:border-[#df5826] bg-white">
                                <input type="file" @change="handleDraftImageUpload($event, index)" accept="image/jpeg, image/png, image/webp" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                
                                <template x-if="draft.imagePreview">
                                    <img :src="draft.imagePreview" class="w-full h-full object-cover">
                                </template>
                                
                                <template x-if="!draft.imagePreview">
                                    <div class="text-center flex flex-col items-center justify-center gap-1.5 p-2">
                                        <i class="fas fa-cloud-upload-alt text-[#7f837e] text-xl group-hover:text-[#df5826] transition"></i>
                                        <p class="text-[11px] text-[#7f837e] font-medium leading-snug">Upload foto<br><span class="text-[9px]">JPG, PNG, WEBP</span></p>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Inputs -->
                            <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Item name</label>
                                    <input type="text" x-model="draftMenus[index].name" placeholder="Contoh: Americano" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[13px] text-[#111827] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Category</label>
                                    <select x-model="draftMenus[index].categoryId" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[13px] text-[#111827] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                                        <option value="" disabled>-- Pilih Kategori --</option>
                                        <template x-for="cat in categories" :key="cat.id">
                                            <option :value="cat.id" x-text="cat.name"></option>
                                        </template>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#53565a] mb-1">Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-2.5 text-[13px] text-[#7f837e] font-medium">Rp</span>
                                        <input type="number" x-model="draftMenus[index].price" placeholder="0" class="w-full bg-white border border-[#e9ebf0] rounded-[10px] pl-10 pr-3.5 py-2.5 text-[13px] text-[#111827] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="shrink-0 flex items-center justify-center self-end md:self-center">
                                <template x-if="index === draftMenus.length - 1">
                                    <button type="button" @click="addDraftRow()" class="w-9 h-9 rounded-[10px] bg-white border border-[#df5826] text-[#df5826] hover:bg-[#fff6f0] flex items-center justify-center transition cursor-pointer shadow-xs" title="Tambah Baris">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </template>
                                <template x-if="index !== draftMenus.length - 1">
                                    <button type="button" @click="removeDraftRow(index)" class="w-9 h-9 rounded-[10px] bg-white border border-[#e9ebf0] text-red-500 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition cursor-pointer shadow-xs" title="Hapus Baris">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t border-[#e9ebf0] shrink-0 gap-3">
                <div class="text-[13px] text-[#53565a] font-medium">
                    <span class="text-[#df5826] font-bold text-[15px] mr-1" x-text="draftMenus.length"></span> menu siap ditambahkan
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" @click="showBulkUpload = false" class="px-5 py-2.5 rounded-[10px] font-semibold text-[13px] bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 transition cursor-pointer">
                        Tutup
                    </button>
                    <button type="button" @click="saveBulkMenu" class="px-6 py-2.5 rounded-[10px] font-semibold text-[13px] bg-[#df5826] hover:bg-[#c94d00] text-white transition shadow-sm flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Publish Menu</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
