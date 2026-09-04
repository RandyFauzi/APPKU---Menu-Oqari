<div x-show="currentTab === 'menu'" x-cloak class="flex-grow p-4 md:p-8 lg:p-10 pt-6 overflow-y-auto hide-scroll bg-brewlybg">
            <!-- Main Menu UI -->
            <div class="flex flex-col gap-8">
                <p class="text-brewlymuted text-sm">Add your coffee, food, and drinks to create a beautiful menu for your shop.</p>
            
            <!-- Cards Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Upload Card -->
                <div class="dashed-box flex flex-col items-center justify-center p-8 text-center bg-gray-50/30">
                    <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-brewlymuted mb-3 bg-white shadow-sm">
                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1 text-brewlytext">Upload Menu Items</h3>
                    <p class="text-sm text-brewlymuted mb-6">Drag and drop images, or click to upload<br>JPG, PNG up to 10MB</p>
                    <button @click="initBulkUpload()" class="bg-brewlygreen text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-sm hover:bg-[#2A5E3E] transition">
                        Tambah Menu
                    </button>
                </div>
                <!-- Tips Card -->
                <div class="bg-[#FCF7F1] rounded-2xl p-6 relative flex items-stretch overflow-hidden h-[230px]">
                    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&display=swap" rel="stylesheet">
                    <!-- Left Content -->
                    <div class="z-20 w-1/2 flex flex-col justify-center pl-2">
                        <div class="text-[#D9652A] mb-3 flex items-center gap-1">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 1L14.8 9.2L23 12L14.8 14.8L12 23L9.2 14.8L1 12L9.2 9.2L12 1Z"/>
                            </svg>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="mt-4 -ml-1">
                                <path d="M12 1L14.8 9.2L23 12L14.8 14.8L12 23L9.2 14.8L1 12L9.2 9.2L12 1Z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-[22px] leading-[1.2] text-[#1A1A1A] mb-2 tracking-tight">A great menu<br>brings people in.</h3>
                        <p class="text-[13px] text-[#666666] mb-5 leading-relaxed pr-2 font-medium">Add clear photos and organized<br>categories to make your menu<br>shine online.</p>
                        <a href="#" class="text-[#D9652A] font-bold text-[13px] hover:underline flex items-center gap-1.5 transition-colors">
                            Tips for a great menu 
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                    </div>
                    
                    <!-- Right Content (Image & Decorations) -->
                    <div class="absolute right-0 top-0 bottom-0 w-[55%] pointer-events-none">
                        <!-- Background offset shape -->
                        <div class="absolute left-4 top-8 w-[210px] h-[155px] bg-[#F2EAE0] rounded-[32px]"></div>
                        
                        <!-- Main Image -->
                        <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&fit=crop" class="absolute left-10 top-12 z-10 w-[190px] h-[130px] object-cover rounded-[20px] shadow-sm">
                        
                        <!-- Handwritten Text (Good Food Better Days) -->
                        <div class="absolute right-6 top-10 text-[#4A443E] leading-[1] text-3xl transform -rotate-6" style="font-family: 'Alex Brush', cursive;">
                            Good<br>Food<br>Better<br>Days
                        </div>
                        
                        <!-- Sunburst bottom right -->
                        <div class="absolute right-6 bottom-8 text-[#D9652A]">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <path d="M21 9 L17 12" />
                                <path d="M22 15 L16 15" />
                                <path d="M21 21 L17 18" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Filter -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="font-bold text-lg text-brewlytext">Categories</span>
                    <div class="flex gap-2">
                        <button @click="activeMenuFilter = 'all'" :class="activeMenuFilter === 'all' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            All Items <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.length"></span>
                        </button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button @click="activeMenuFilter = cat.id" :class="activeMenuFilter === cat.id ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                                <span x-text="cat.name"></span> <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i => i.categoryId === cat.id).length"></span>
                            </button>
                        </template>
                    </div>
                </div>
                <button @click="showAddCategoryModal = true" class="bg-white border border-gray-200 text-brewlytext px-4 py-2 rounded-full font-bold text-sm shadow-sm hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>

            <!-- Table Section -->
            <div class="bg-white border border-gray-200 rounded-2xl flex flex-col shadow-sm flex-grow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-brewlytext">Menu Items <span class="text-brewlymuted font-normal text-sm" x-text="'(' + filteredMenuItems.length + ')'"></span></h3>
                    <div class="flex gap-3">
                        <select class="border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:border-brewlygreen bg-white text-brewlytext">
                            <option>Sort by: Newest</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-brewlytext">
                        <thead>
                            <tr class="border-b border-gray-100 text-sm text-brewlymuted bg-gray-50/50">
                                <th class="p-4 w-12 text-center"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brewlygreen focus:ring-brewlygreen"></th>
                                <th class="p-4 font-semibold">Item</th>
                                <th class="p-4 font-semibold">Category</th>
                                <th class="p-4 font-semibold">Price</th>
                                <th class="p-4 font-semibold">Status</th>
                                <th class="p-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in filteredMenuItems" :key="item.id">
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition group" :class="item.is_sold_out ? 'opacity-70 grayscale-[30%]' : ''">
                                    <td class="p-4 text-center align-middle"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brewlygreen focus:ring-brewlygreen"></td>
                                    <td class="p-4 flex gap-4 items-center">
                                        <img :src="item.image || item.img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541167760496-1628856ab772?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm" x-text="item.name"></span>
                                            <span class="text-xs text-brewlymuted line-clamp-1" x-text="item.desc || 'No description'"></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 bg-brewlylightgreen text-brewlygreen text-xs font-bold rounded-full capitalize" x-text="item.categoryName || '-'"></span>
                                    </td>
                                    <td class="p-4 font-semibold text-sm font-mono" x-text="formatRp(item.price)"></td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full" :class="item.is_sold_out ? 'bg-red-500' : 'bg-brewlygreen'"></div>
                                            <span class="text-sm font-semibold" :class="item.is_sold_out ? 'text-red-600' : 'text-brewlygreen'" x-text="item.is_sold_out ? 'Sold Out' : 'Published'"></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editMenu(item)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-brewlygreen hover:border-brewlygreen transition flex items-center justify-center" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                                            <button @click="toggleSoldOut(item.id)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-brewlyorange hover:border-brewlyorange transition flex items-center justify-center" title="Toggle Status"><i class="fas fa-power-off text-xs"></i></button>
                                            <button @click="deleteMenu(item.id)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-red-500 hover:border-red-500 transition flex items-center justify-center" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL: BULK UPLOAD MENU -->
<div x-show="showBulkUpload" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-10 font-sans">
    <!-- Backdrop -->
    <div @click="showBulkUpload = false" class="absolute inset-0 bg-[#202522]/30 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-5xl mx-auto bg-[#FFFFFF] rounded-[28px] p-10 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col max-h-[90vh] overflow-hidden border border-[#E3E1DC]">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-[#E3E1DC] shrink-0">
            <div class="flex gap-5">
                <div class="w-20 h-20 rounded-[18px] bg-[#164A35] text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <h2 class="text-[32px] text-[#164A35] leading-tight mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">Upload Menu</h2>
                    <p class="text-[#777873] text-[16px]">Add your coffee and pastry items with images, prices, and categories.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="$refs.csvInput.click()" class="bg-transparent border border-[#164A35] text-[#164A35] px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#DDEBDD] transition-colors flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Bulk upload CSV
                </button>
                <input type="file" x-ref="csvInput" @change="handleCSVUpload" accept=".csv" class="hidden">
            </div>
        </div>

        <!-- Scrollable Body -->
        <div class="flex-grow overflow-y-auto hide-scroll py-2">
            <div class="flex flex-col gap-3 pb-[240px]">
                <template x-for="(draft, index) in draftMenus" :key="index">
                    <!-- Row -->
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-5 p-4 bg-white rounded-[16px] border border-[#E3E1DC] shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <!-- Image Box -->
                        <div class="w-full md:w-[218px] h-[160px] md:h-[130px] shrink-0 relative rounded-xl overflow-hidden flex items-center justify-center group cursor-pointer transition-colors"
                             :class="!draft.imagePreview ? 'border-2 border-dashed border-[#E3E1DC] bg-[#F8F7F3] hover:border-[#164A35]' : 'bg-gray-100'">
                            <input type="file" @change="handleDraftImageUpload($event, index)" accept="image/jpeg, image/png, image/webp" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                            
                            <template x-if="draft.imagePreview">
                                <img :src="draft.imagePreview" class="w-full h-full object-cover">
                            </template>
                            
                            <template x-if="!draft.imagePreview">
                                <div class="text-center flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-cloud-upload-alt text-[#777873] text-2xl group-hover:text-[#164A35] transition-colors"></i>
                                    <p class="text-[12px] text-[#777873] font-medium leading-snug">Upload image<br>JPG, PNG or WEBP</p>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Inputs -->
                        <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5 w-full">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Item name</label>
                                <input type="text" x-model="draftMenus[index].name" placeholder="e.g. Blueberry Muffin" class="w-full bg-white border border-[#E3E1DC] rounded-[11px] px-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none placeholder-[#C4C4C4]">
                            </div>
                            
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Category</label>
                                <div class="relative">
                                    <div :class="open ? 'relative z-50' : 'relative'" x-data="{ open: false }">
                                        <!-- Trigger -->
                                        <button @click="open = !open" @click.away="open = false" type="button" 
                                                class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[11px] px-3 py-1.5 h-[46px] flex items-center justify-between focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] transition-colors outline-none cursor-pointer">
                                            
                                            <!-- Dynamic Badge for Selected -->
                                            <template x-for="cat in categories">
                                                <template x-if="draftMenus[index].categoryId === cat.id">
                                                    <div class="bg-gray-100 text-gray-700 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg font-semibold text-sm border border-gray-200">
                                                        <i class="fas fa-tag"></i> <span x-text="cat.name"></span>
                                                    </div>
                                                </template>
                                            </template>
                                            
                                            <!-- Fallback if not found -->
                                            <template x-if="!categories.find(o => o.id === draftMenus[index].categoryId)">
                                                <span class="text-[#777873] font-medium text-sm px-2">Select category</span>
                                            </template>

                                            <i class="fas fa-chevron-down text-xs text-[#777873] mr-1 transition-transform" :class="open ? 'rotate-180' : ''"></i>
                                        </button>
                                        
                                        <!-- Dropdown Menu -->
                                        <div x-show="open" x-cloak 
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute z-50 w-full mt-2 bg-white border border-[#E3E1DC] rounded-[12px] shadow-[0_10px_35px_rgba(0,0,0,0.08)] py-2 flex flex-col gap-1 max-h-56 overflow-y-auto">
                                            <template x-for="cat in categories" :key="cat.id">
                                                <button @click="draftMenus[index].categoryId = cat.id; open = false" type="button" 
                                                        class="mx-2 px-2 py-1.5 rounded-[10px] text-left hover:bg-[#F8F7F3] transition-colors flex items-center">
                                                    <div class="bg-gray-100 text-gray-700 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg font-semibold text-sm border border-gray-200">
                                                        <i class="fas fa-tag"></i> <span x-text="cat.name"></span>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Price</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-[16px] text-[#202522] font-medium">Rp</span>
                                    <input type="number" x-model="draftMenus[index].price" placeholder="0" class="w-full bg-white border border-[#E3E1DC] rounded-[11px] pl-11 pr-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="shrink-0 w-12 flex justify-center ml-2">
                            <template x-if="index === draftMenus.length - 1">
                                <button @click="addDraftRow()" class="w-11 h-11 rounded-[10px] bg-transparent border border-[#DDEBDD] text-[#164A35] hover:bg-[#DDEBDD] flex items-center justify-center transition-colors">
                                    <i class="fas fa-plus text-lg"></i>
                                </button>
                            </template>
                            <template x-if="index !== draftMenus.length - 1">
                                <button @click="removeDraftRow(index)" class="w-11 h-11 rounded-[10px] bg-white border border-[#F7E5D2] text-[#D97A32] hover:bg-[#F7E5D2] flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div> <!-- End Scrollable Body -->

        <!-- Action Buttons -->
        <div class="flex flex-col md:flex-row justify-between items-center pt-6 mt-2 shrink-0 gap-4">
            <div class="text-[15px] text-[#777873] font-medium"><span class="text-[#164A35] font-bold text-lg mr-1" x-text="draftMenus.length"></span> items added</div>
            <div class="flex flex-col md:flex-row gap-3 md:gap-4 w-full md:w-auto">
                <button @click="showBulkUpload = false" class="w-full md:w-auto px-6 py-3.5 rounded-[12px] font-semibold text-[15px] bg-white border border-[#E3E1DC] text-[#202522] hover:bg-[#F8F7F3] transition-colors shadow-sm">Save draft</button>
                <button @click="saveBulkMenu" class="w-full md:w-auto px-7 py-3.5 rounded-[12px] font-semibold text-[15px] bg-[#D97A32] text-white hover:bg-[#c26d2d] transition-colors shadow-md flex justify-center items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Publish menu
                </button>
            </div>
        </div>
        
        <div class="text-center mt-5 shrink-0">
            <p class="text-[14px] text-[#777873] font-medium flex items-center justify-center gap-2">
                <i class="fas fa-shield-check text-[#DDEBDD] text-lg"></i> 
                Customers will see your updated menu immediately after publishing.
            </p>
        </div>
    </div>
</div>
</div>
</div>

