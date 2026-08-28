<div x-show="currentTab === 'menu'" x-cloak class="flex-grow p-10 pt-6 overflow-y-auto hide-scroll bg-brewlybg">
            <!-- Main Menu UI -->
            <div x-show="!showBulkUpload" class="flex flex-col gap-8">
                <p class="text-brewlymuted text-sm">Add your coffee, food, and drinks to create a beautiful menu for your shop.</p>
            
            <!-- Cards Section -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Upload Card -->
                <div class="dashed-box flex flex-col items-center justify-center p-8 text-center bg-gray-50/30">
                    <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-brewlymuted mb-3 bg-white shadow-sm">
                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1 text-brewlytext">Upload Menu Items</h3>
                    <p class="text-sm text-brewlymuted mb-6">Drag and drop images, or click to upload<br>JPG, PNG up to 10MB</p>
                    <button @click="initBulkUpload()" class="bg-brewlygreen text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-sm hover:bg-[#2A5E3E] transition">
                        Upload Files
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
                        <button @click="activeMenuFilter = 'beverages'" :class="activeMenuFilter === 'beverages' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-coffee opacity-50"></i> Beverages <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='beverages').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'foods'" :class="activeMenuFilter === 'foods' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-utensils opacity-50"></i> Foods <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='foods').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'snacks'" :class="activeMenuFilter === 'snacks' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-cookie opacity-50"></i> Snacks <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='snacks').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'sweets'" :class="activeMenuFilter === 'sweets' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-ice-cream opacity-50"></i> Sweets <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='sweets').length"></span>
                        </button>
                    </div>
                </div>
                <button class="bg-white border border-gray-200 text-brewlytext px-4 py-2 rounded-full font-bold text-sm shadow-sm hover:bg-gray-50 transition flex items-center gap-2">
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
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition group" :class="item.soldOut ? 'opacity-70 grayscale-[30%]' : ''">
                                    <td class="p-4 text-center align-middle"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brewlygreen focus:ring-brewlygreen"></td>
                                    <td class="p-4 flex gap-4 items-center">
                                        <img :src="item.image || item.img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541167760496-1628856ab772?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm" x-text="item.name"></span>
                                            <span class="text-xs text-brewlymuted line-clamp-1" x-text="item.desc || 'No description'"></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 bg-brewlylightgreen text-brewlygreen text-xs font-bold rounded-full capitalize" x-text="item.categoryId"></span>
                                    </td>
                                    <td class="p-4 font-semibold text-sm font-mono" x-text="formatRp(item.price)"></td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full" :class="item.soldOut ? 'bg-red-500' : 'bg-brewlygreen'"></div>
                                            <span class="text-sm font-semibold" :class="item.soldOut ? 'text-red-600' : 'text-brewlygreen'" x-text="item.soldOut ? 'Sold Out' : 'Published'"></span>
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
            </div> <!-- End of !showBulkUpload -->

            <!-- Bulk Upload Container -->
            <div x-show="showBulkUpload" x-cloak class="w-full max-w-4xl mx-auto bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-6 border-b border-gray-200 pb-6 border-dashed">
                    <div class="flex gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-brewlygreen text-white flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="font-heading font-extrabold text-2xl text-brewlytext mb-1">Upload Menu</h2>
                            <p class="text-brewlymuted text-sm">Add your coffee and pastry items with images, prices, and categories.</p>
                        </div>
                    </div>
                    <button class="border border-gray-300 bg-white text-gray-700 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-gray-50 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt"></i> Bulk upload CSV
                    </button>
                </div>

                <div class="flex flex-col gap-4 mb-8">
                    <template x-for="(draft, index) in draftMenus" :key="index">
                        <div class="flex items-center gap-4">
                            <!-- Image Box -->
                            <div class="w-[120px] h-[80px] shrink-0 relative rounded-xl border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center group cursor-pointer hover:border-brewlygreen">
                                <input type="file" @change="handleDraftImageUpload($event, index)" accept="image/jpeg, image/png, image/webp" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                <template x-if="draft.imagePreview">
                                    <img :src="draft.imagePreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!draft.imagePreview">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-lg mb-1 group-hover:text-brewlygreen transition-colors"></i>
                                        <p class="text-[10px] text-gray-500 font-medium leading-tight">Upload image<br>JPG, PNG or WEBP</p>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Inputs -->
                            <div class="flex-grow grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Item name</label>
                                    <input type="text" x-model="draft.name" placeholder="e.g. Blueberry Muffin" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-sm focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Category</label>
                                    <div class="relative">
                                        <select x-model="draft.categoryId" class="w-full bg-gray-100 border border-transparent rounded-xl px-3 py-2 text-sm focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen outline-none appearance-none font-medium">
                                            <template x-for="cat in categories">
                                                <option :value="cat" x-text="cat"></option>
                                            </template>
                                        </select>
                                        <i class="fas fa-chevron-down absolute right-3 top-3 text-xs text-gray-500 pointer-events-none"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Price</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-sm text-gray-500">Rp</span>
                                        <input type="number" x-model="draft.price" placeholder="0" class="w-full bg-white border border-gray-300 rounded-xl pl-9 pr-3 py-2 text-sm focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="shrink-0 mt-5 w-10 flex justify-center">
                                <template x-if="index === draftMenus.length - 1">
                                    <button @click="addDraftRow()" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 border border-green-100 hover:bg-green-100 flex items-center justify-center transition-colors">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </template>
                                <template x-if="index !== draftMenus.length - 1">
                                    <button @click="removeDraftRow(index)" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 border border-red-100 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition-colors">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500 font-bold"><span class="text-brewlygreen font-extrabold text-lg" x-text="draftMenus.length"></span> items added</div>
                    <div class="flex gap-3">
                        <button @click="showBulkUpload = false" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-gray-200 text-gray-600 hover:bg-gray-300 transition-colors">Save draft</button>
                        <button @click="saveBulkMenu" class="px-5 py-2.5 rounded-xl font-bold text-sm bg-[#D9652A] text-white hover:bg-[#c25a25] transition-colors shadow-md flex items-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Publish menu
                        </button>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <p class="text-xs text-gray-400 font-medium flex items-center justify-center gap-2"><i class="fas fa-shield-check text-brewlygreen text-sm"></i> Customers will see your updated menu immediately after publishing.</p>
                </div>
            </div>
        </div>
</div>