<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageLabel }} - {{ $shop->name ?? config('app.name', 'Oqari') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Favicon -->

    
    <!-- View Transitions API -->
    <meta name="view-transition" content="same-origin" />
    <style>
        body { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
    
    <!-- Alpine JS -->
    <!-- Realtime Config -->
    <script>
        window.OQARI_REALTIME = {
            shopId: @json(auth()->check() ? auth()->user()->shop_id : null)
        };
    </script>
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fonts (Curated Cafe & Typography Options) -->
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Cinzel:wght@600;700;800&family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,600&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Inter:wght@400;500;600;700;800&family=Lora:ital,wght@0,500;0,600;0,700;1,500&family=Montserrat:wght@400;500;600;700;800&family=Nunito:wght@400;500;600;700;800&family=Oswald:wght@500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Quicksand:wght@500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    
    <!-- Auth Guard removed, using Laravel middleware instead -->
    
    <!-- Data Scripts removed -->

    <style>
        body { background-color: #ffffff; color: var(--oq-ink); }
        .hide-scroll::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
        .dashed-box { background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%23CFCFCF' stroke-width='2' stroke-dasharray='8%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e"); border-radius: 16px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="font-sans text-textdark h-screen flex overflow-hidden bg-white" x-data="dashboardApp()" @new-order-received.window="handleNewOrderFromSocket($event.detail)" @refresh-live-orders.window="fetchLiveOrders(false)">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>
    
    <!-- Audio untuk notifikasi pesanan masuk -->
    <audio id="chime-sound" src="{{ asset('Assest/notif_orderan_masuk.mp3') }}" preload="auto"></audio>

    <x-cafe-sidebar :dashboard="true" />

    <!-- Main Content -->
    <main class="flex-grow min-w-0 flex flex-col h-full bg-white overflow-hidden">
        
        <!-- Top Header for Main Area -->
        <!-- Top Header for Main Area -->
        <header class="h-16 md:h-[58px] flex justify-between items-center px-4 md:px-7 shrink-0 border-b border-[#e9ebf0] bg-white relative z-50 font-['DM_Sans',sans-serif]">
            <div class="flex items-center gap-3">
                <button type="button" @click="window.innerWidth < 768 ? mobileNavOpen = !mobileNavOpen : isMinimized = !isMinimized" aria-label="Ubah ukuran sidebar" aria-controls="app-sidebar" :aria-expanded="!isMinimized" class="w-9 h-9 rounded-lg bg-gray-50 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors md:hidden">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <div x-show="currentTab === 'dashboard'">
                    <p class="font-['DM_Sans',sans-serif] font-semibold text-[13px] text-[#111827]">Dashboard</p>
                </div>
                <div x-show="currentTab !== 'dashboard'">
                    <p class="font-['DM_Sans',sans-serif] font-semibold text-[13px] text-[#111827]" x-text="tabs.find(t => t.id === currentTab)?.name"></p>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 md:gap-4 ml-auto">
                <!-- Search Bar (Sparta Figma Style with ⌘K badge) -->
                <div class="relative items-center hidden sm:flex">
                    <div class="bg-[#f8f9fa] border border-[#e9ebf0] rounded-[8px] flex items-center h-8 px-2.5 gap-2 w-56 md:w-64 focus-within:border-[#f9731f] focus-within:ring-1 focus-within:ring-[#f9731f]/20 transition-all">
                        <img src="{{ asset('images/sparta/search-icon.svg') }}" class="w-3.5 h-3.5 opacity-60" alt="">
                        <input type="text" id="global-search-input" x-model="searchQuery" placeholder="Cari menu atau pesanan…" class="bg-transparent border-0 p-0 text-[11px] text-[#111827] placeholder:text-[#7f837e] focus:ring-0 focus:outline-none w-full">
                        <kbd class="px-1.5 py-0.5 rounded bg-gray-200/60 text-[#7f837e] text-[9px] font-mono leading-none">⌘K</kbd>
                    </div>
                </div>

                <!-- Topbar Datetime (Standardized Sparta Component) -->
                <x-topbar-datetime />

                <!-- Bell Notifications -->
                <div class="relative cursor-pointer p-1.5 text-gray-500 hover:text-primary transition-colors" @click="navigateToPage('orders')" title="Pesanan Masuk">
                    <i class="fas fa-bell text-base md:text-lg"></i>
                    <template x-if="(orders || []).filter(o => o.status === 'Masuk' || o.status === 'CONFIRMED' || o.order_status === 'CONFIRMED').length > 0">
                        <span class="absolute 0 right-0 bg-[#ea580c] text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border-2 border-white" x-text="(orders || []).filter(o => o.status === 'Masuk' || o.status === 'CONFIRMED' || o.order_status === 'CONFIRMED').length"></span>
                    </template>
                </div>
                
                <!-- User Profile -->
                <div class="relative pl-2.5 md:pl-3.5 border-l border-gray-200" x-data="{ open: false }">
                    <div @click="open = !open" @click.away="open = false" class="flex items-center gap-2 cursor-pointer group">
                        <div class="w-8 h-8 rounded-full bg-[#dce8ec] text-[#111827] flex items-center justify-center font-bold text-xs shrink-0 border border-gray-100">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                        </div>
                        <div class="hidden lg:flex flex-col">
                            <span class="text-xs font-semibold text-[#111827] leading-tight">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <span class="text-[9px] text-[#7f837e] uppercase font-medium">{{ auth()->user()->role ?? 'Manager' }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-primary transition-colors hidden md:block"></i>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-3 w-48 bg-white rounded-[14px] shadow-[0_10px_35px_rgba(0,0,0,0.08)] border border-line overflow-hidden z-50">
                        <div class="p-1.5">
                            <a href="{{ route('profile.edit') }}" class="w-full text-left px-3.5 py-2 rounded-[8px] text-xs font-semibold text-ink hover:bg-gray-50 hover:text-primary transition-colors flex items-center gap-2.5">
                                <i class="fas fa-user w-4 text-gray-400" aria-hidden="true"></i> Profil Saya
                            </a>
                            <div class="h-px bg-gray-100 my-1 mx-1.5"></div>
                            <button @click="logout()" class="w-full text-left px-3.5 py-2 rounded-[8px] text-xs font-bold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2.5">
                                <i class="fas fa-sign-out-alt w-4"></i> Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @include($pageView)

        <!-- MODAL: ADD CATEGORY -->
        <div x-show="showAddCategoryModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-sans bg-black/40 backdrop-blur-xs" x-transition.opacity>
            <div style="max-width: 420px; width: 100%;" class="bg-white rounded-[24px] w-full max-w-md p-6 sm:p-7 shadow-[0_24px_60px_rgba(0,0,0,0.18)] relative overflow-hidden border border-[#e9ebf0] mx-auto my-auto" @click.away="showAddCategoryModal = false" x-transition>
                
                <div class="absolute top-5 right-5 z-10">
                    <button @click="showAddCategoryModal = false" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex items-center gap-3.5 mb-5 relative z-10">
                    <div class="w-11 h-11 rounded-[14px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center text-lg shrink-0 shadow-xs">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-bold text-[#111827] leading-tight">Tambah Kategori</h2>
                        <p class="text-[12px] text-[#53565a]">Buat kategori menu baru untuk toko Anda</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#53565a] mb-1.5 uppercase tracking-wider">Nama Kategori</label>
                        <input type="text" x-model="newCategoryName" @keydown.enter="saveCategory" placeholder="Contoh: Coffee, Pastry, Signature..." class="w-full bg-[#f8f9fa] border border-[#e9ebf0] rounded-[10px] px-3.5 py-2.5 text-[13px] font-medium text-[#111827] focus:bg-white focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] outline-none">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-[11px] font-semibold text-[#53565a] uppercase tracking-wider">Pilih Icon Kategori (9 Pilihan)</label>
                            <span class="text-[10px] text-[#7f837e] font-medium">Tampil di Menu QR</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="item in categoryIcons" :key="item.icon">
                                <button type="button" 
                                        @click="newCategoryIcon = item.icon"
                                        :class="newCategoryIcon === item.icon ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] ring-2 ring-[#df5826]/20 font-bold shadow-2xs' : 'border-[#e9ebf0] bg-[#f8f9fa] text-[#53565a] hover:border-gray-300 hover:bg-gray-50'"
                                        class="flex flex-col items-center justify-center p-2.5 rounded-[12px] border transition cursor-pointer relative group">
                                    <div class="w-8 h-8 rounded-[8px] flex items-center justify-center mb-1 text-base transition group-hover:scale-110"
                                         :class="newCategoryIcon === item.icon ? 'text-[#df5826]' : 'text-gray-500'">
                                        <i :class="'fas ' + item.icon"></i>
                                    </div>
                                    <span class="text-[11px] truncate w-full text-center" x-text="item.label"></span>
                                    <template x-if="newCategoryIcon === item.icon">
                                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#df5826]"></span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="showAddCategoryModal = false" class="px-4 py-2.5 bg-white border border-[#e9ebf0] text-[#53565a] hover:bg-gray-50 rounded-[10px] text-[13px] font-semibold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveCategory" class="px-5 py-2.5 bg-[#df5826] text-white rounded-[10px] text-[13px] font-semibold hover:bg-[#c94d00] transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                        <i class="fas fa-save"></i>
                        <span>Simpan Kategori</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: MANAGE CATEGORIES (Kelola Kategori) - Perfectly Centered & Compact Desktop View -->
        <div x-show="showManageCategoryModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-sans bg-black/50 backdrop-blur-xs" x-transition.opacity>
            <div style="max-width: 520px; width: 100%;" class="bg-white rounded-[24px] w-full max-w-lg p-6 sm:p-7 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.22)] relative overflow-hidden border border-[#e9ebf0] max-h-[85vh] flex flex-col mx-auto my-auto" @click.away="showManageCategoryModal = false" x-transition>
                
                <!-- Close Button -->
                <div class="absolute top-5 right-5 z-10">
                    <button type="button" @click="showManageCategoryModal = false" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition cursor-pointer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Header -->
                <div class="flex items-center gap-3.5 mb-5 shrink-0">
                    <div class="w-11 h-11 rounded-[14px] bg-[#fff6f0] text-[#df5826] flex items-center justify-center text-lg shrink-0 shadow-xs border border-[#ffdec9]">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-[18px] font-bold text-[#111827] leading-tight">Kelola Kategori Menu</h2>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#fff6f0] text-[#df5826] border border-[#ffdec9]" x-text="(categories ? categories.length : 0) + ' kategori'"></span>
                        </div>
                        <p class="text-[12px] text-[#53565a] mt-0.5">Ubah nama atau hapus kategori toko Anda dengan mudah.</p>
                    </div>
                </div>

                <!-- Quick Add Category Box Inside Manager -->
                <div class="mb-4 p-3.5 bg-gradient-to-br from-[#fafbfc] to-[#fff8f5] rounded-[16px] border border-[#fed7c3] shadow-2xs shrink-0">
                    <label class="block text-[11px] font-bold text-[#111827] mb-2 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-plus-circle text-[#df5826]"></i>
                        <span>Tambah Kategori Baru</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="text" 
                               x-model="newCategoryName" 
                               @keydown.enter="saveCategory" 
                               placeholder="Ketik nama kategori baru..." 
                               class="flex-grow bg-white border border-[#e5e7eb] focus:border-[#df5826] focus:ring-2 focus:ring-[#df5826]/10 rounded-[10px] px-3.5 py-2 text-[12px] text-[#111827] placeholder:text-[#9ca3af] outline-none transition">
                        <button type="button" 
                                @click="saveCategory" 
                                :disabled="!newCategoryName.trim()"
                                class="px-4 py-2 bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white rounded-[10px] text-[12px] font-bold transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50 shadow-xs shrink-0">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Tambah</span>
                        </button>
                    </div>
                </div>

                <!-- Category List Header -->
                <div class="flex items-center justify-between pb-2 px-1 text-[11px] font-semibold text-[#7f837e] uppercase tracking-wider border-b border-[#f1f3f5] shrink-0 mb-2">
                    <span>Nama Kategori</span>
                    <span>Tindakan</span>
                </div>

                <!-- Category List (Scrollable) -->
                <div class="flex-grow overflow-y-auto pr-1 space-y-2 custom-scroll min-h-[160px] max-h-[300px]">
                    <template x-for="cat in categories" :key="cat.id">
                        <div class="p-3 bg-white rounded-[14px] border border-[#e9ebf0] hover:border-[#df5826]/40 hover:shadow-2xs transition-all">
                            <!-- View Mode -->
                            <template x-if="editingCategoryId !== cat.id">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-[10px] bg-[#fff6f0] border border-[#ffdec9] flex items-center justify-center text-[#df5826] shrink-0">
                                            <i :class="'fas ' + (cat.icon || 'fa-utensils') + ' text-xs'"></i>
                                        </div>
                                        <div class="truncate">
                                            <p class="text-[13px] font-bold text-[#111827] truncate" x-text="cat.name"></p>
                                            <p class="text-[11px] text-[#7f837e] flex items-center gap-1 mt-0.5">
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span x-text="(menuItems.filter(i => i.categoryId == cat.id || String(i.categoryName).toLowerCase() == String(cat.name).toLowerCase()).length) + ' menu terdaftar'"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button" 
                                                @click="startEditCategory(cat)" 
                                                class="w-8 h-8 rounded-[9px] bg-white border border-[#e9ebf0] hover:border-[#df5826] hover:bg-[#fff6f0] text-gray-500 hover:text-[#df5826] flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                                title="Edit nama & icon kategori">
                                            <i class="fas fa-pen text-xs"></i>
                                        </button>
                                        <button type="button" 
                                                @click="deleteCategory(cat.id, cat.name)" 
                                                class="w-8 h-8 rounded-[9px] bg-white border border-[#e9ebf0] hover:border-red-400 hover:bg-red-50 text-gray-400 hover:text-red-500 flex items-center justify-center transition cursor-pointer shadow-2xs" 
                                                title="Hapus kategori">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <!-- Edit/Rename Mode -->
                            <template x-if="editingCategoryId === cat.id">
                                <div class="space-y-2.5 p-2 bg-[#fbfcfd] rounded-[12px] border border-[#df5826]/30">
                                    <div class="flex items-center justify-between text-[11px] font-semibold text-[#53565a]">
                                        <span class="flex items-center gap-1"><i class="fas fa-pen-to-square text-[#df5826]"></i> Edit Kategori & Icon</span>
                                        <span class="text-[10px] text-[#7f837e] font-mono">↵ Enter untuk simpan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="text" 
                                               x-model="editingCategoryName" 
                                               @keydown.enter.prevent="updateCategory(cat.id)"
                                               class="flex-grow bg-white border border-[#df5826] focus:ring-1 focus:ring-[#df5826] rounded-[9px] px-3 py-1.5 text-[12px] text-[#111827] outline-none font-medium">
                                        <button type="button" 
                                                @click="cancelEditCategory()" 
                                                class="px-2.5 py-1.5 bg-white border border-[#e9ebf0] text-gray-600 rounded-[9px] text-[11px] font-medium hover:bg-gray-50 transition cursor-pointer">
                                            Batal
                                        </button>
                                        <button type="button" 
                                                @click="updateCategory(cat.id)" 
                                                :disabled="isSavingCategoryAction || !editingCategoryName.trim()"
                                                class="px-3.5 py-1.5 bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white rounded-[9px] text-[11px] font-bold transition flex items-center gap-1 cursor-pointer disabled:opacity-50 shadow-2xs">
                                            <i :class="isSavingCategoryAction ? 'fas fa-spinner fa-spin' : 'fas fa-check text-xs'"></i>
                                            <span>Simpan</span>
                                        </button>
                                    </div>

                                    <!-- 9 Curated Category Icons Selector -->
                                    <div class="pt-1 border-t border-[#f1f3f5]">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="text-[10px] font-semibold text-[#53565a] uppercase">Pilih Icon (9 Pilihan):</label>
                                            <span class="text-[10px] text-[#df5826] font-semibold flex items-center gap-1">
                                                <i :class="'fas ' + editingCategoryIcon"></i>
                                                <span x-text="(categoryIcons.find(i => i.icon === editingCategoryIcon) || {}).label || 'Dipilih'"></span>
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-9 gap-1">
                                            <template x-for="item in categoryIcons" :key="item.icon">
                                                <button type="button"
                                                        @click="editingCategoryIcon = item.icon"
                                                        :title="item.label"
                                                        :class="editingCategoryIcon === item.icon ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] ring-1 ring-[#df5826] shadow-2xs font-bold' : 'border-gray-200 bg-white text-gray-500 hover:border-gray-300 hover:bg-gray-50'"
                                                        class="h-7 rounded-[7px] border flex items-center justify-center transition cursor-pointer text-xs">
                                                    <i :class="'fas ' + item.icon"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="categories.length === 0">
                        <div class="py-10 text-center text-gray-400 text-xs">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2 text-gray-300">
                                <i class="fas fa-folder-open text-base"></i>
                            </div>
                            <p class="font-medium text-gray-500">Belum ada kategori yang dibuat.</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Ketik nama di atas untuk membuat kategori pertama.</p>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="mt-4 pt-3.5 border-t border-[#e9ebf0] flex items-center justify-between shrink-0">
                    <span class="text-[11px] text-[#7f837e]">Perubahan langsung disimpan ke toko</span>
                    <button type="button" @click="showManageCategoryModal = false" class="px-5 py-2 bg-white border border-[#e9ebf0] hover:border-gray-300 text-[#111827] hover:bg-gray-50 rounded-[10px] text-[12px] font-semibold transition cursor-pointer shadow-2xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD TABLE -->
        <div x-show="showAddTableModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-primary/40 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-white rounded-[24px] w-full max-w-[420px] p-8 shadow-[0_20px_60px_rgba(30,90,122,0.15)] relative overflow-hidden" @click.away="showAddTableModal = false" x-transition>
                
                <!-- Decorative Circle -->
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-ivory rounded-full pointer-events-none"></div>
                <div class="absolute top-4 right-4 z-10">
                    <button @click="showAddTableModal = false" class="text-muted hover:text-ink bg-ivory hover:bg-line transition-colors w-8 h-8 flex items-center justify-center rounded-full">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-full bg-brand-100 text-primary flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-[24px] font-bold text-primary leading-tight" style="font-family: var(--oq-font-heading);">Tambah Meja Baru</h3>
                        <p class="text-[13px] text-muted">Buat QR code instan untuk meja baru.</p>
                    </div>
                </div>

                <div class="mb-8 relative z-10">
                    <label class="block text-[12px] font-bold text-muted mb-2 uppercase tracking-widest">Nama / Nomor Meja</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-brand-200"></i>
                        </div>
                        <input x-ref="tableInput" type="text" x-model="newTableId" @keydown.enter="addTableFromModal" placeholder="Misal: Meja 07" class="w-full bg-ivory border border-line rounded-[16px] pl-10 pr-4 py-3.5 text-[15px] font-bold text-ink placeholder:font-medium placeholder:text-brand-200 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
                    </div>
                </div>

                <div class="flex gap-3 relative z-10">
                    <button @click="showAddTableModal = false" class="w-1/3 py-3.5 rounded-[14px] text-[14px] font-bold text-muted bg-white border border-line hover:bg-ivory transition-colors text-center">Batal</button>
                    <button @click="addTableFromModal" class="flex-grow py-3.5 rounded-[14px] text-[14px] font-bold bg-primary text-white shadow-[0_4px_12px_rgba(30,90,122,0.2)] hover:bg-primary-dark hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        Generate QR <i class="fas fa-arrow-right text-[12px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: RESET QR CONFIRM -->
        <div x-show="showResetQRModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-primary/40 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-white rounded-[24px] w-full max-w-[400px] p-8 shadow-[0_20px_60px_rgba(30,90,122,0.15)] relative overflow-hidden" @click.away="showResetQRModal = false" x-transition>
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-gold-600/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-primary/5 rounded-full blur-2xl"></div>
                
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-gold-600/10 text-gold-600 rounded-full flex items-center justify-center text-2xl mb-4 border-4 border-white shadow-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-[22px] font-bold text-primary mb-2" style="font-family: var(--oq-font-heading);">Reset QR Code?</h3>
                    <p class="text-[14px] text-muted leading-relaxed mb-6">Yakin ingin mereset/mengganti URL QR Code untuk <strong class="text-ink" x-text="qrTableToReset?.id"></strong>? <br> <span class="text-red-500 font-medium">URL lama tidak akan bisa diakses lagi.</span></p>
                    
                    <div class="flex gap-3 w-full">
                        <button @click="showResetQRModal = false" class="w-1/2 py-3 rounded-[14px] text-[14px] font-bold text-muted bg-ivory hover:bg-line transition-colors">Batal</button>
                        <button @click="confirmResetQR" :disabled="isResettingQR" class="w-1/2 py-3 rounded-[14px] text-[14px] font-bold bg-gold-600 text-white shadow-sm hover:bg-[#b8662a] transition-all flex items-center justify-center gap-2 disabled:opacity-70">
                            <i class="fas fa-spinner fa-spin" x-show="isResettingQR" x-cloak></i>
                            <span x-show="!isResettingQR">Ya, Reset</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: ORDER DETAIL (Redesigned Sparta Style) -->
        <div x-show="showOrderDetailModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4 font-sans bg-black/40 backdrop-blur-xs" x-transition.opacity>
            <!-- Backdrop -->
            <div @click="showOrderDetailModal = false" class="absolute inset-0"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white w-full sm:w-[500px] rounded-[24px] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.22)] flex flex-col border border-[#e9ebf0] overflow-hidden mx-auto my-auto" 
                 @keydown.escape.window="showOrderDetailModal = false" 
                 x-show="selectedOrder"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-5 sm:p-6 bg-white border-b border-[#f0f0ee] flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-[14px] bg-[#fff6f0] border border-[#ffdec9] text-[#df5826] flex items-center justify-center text-lg shrink-0 shadow-2xs">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h2 class="text-[17px] font-bold text-[#111827] leading-tight flex items-center gap-1.5">
                                <span>Detail Pesanan</span>
                                <span class="text-[#df5826]" x-text="'#' + (selectedOrder ? selectedOrder.id : '')"></span>
                            </h2>
                            <p class="text-[11.5px] text-[#7f837e] flex items-center gap-1.5 mt-0.5">
                                <i class="far fa-clock text-[10.5px]"></i>
                                <span x-text="selectedOrder ? formatOrderTime(selectedOrder.time) : ''"></span>
                            </p>
                        </div>
                    </div>

                    <button type="button" 
                            @click="showOrderDetailModal = false" 
                            class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer"
                            title="Tutup Modal">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Customer & Fulfillment Card -->
                <div class="p-5 sm:p-6 pb-2 bg-white flex flex-col gap-3">
                    <div class="bg-[#fafaf9] rounded-[14px] p-3.5 border border-[#e9ebf0] flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-[10px] bg-white border border-[#e6e5e0] text-[#df5826] flex items-center justify-center text-sm shrink-0 shadow-2xs">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-[13.5px] font-bold text-[#111827] truncate leading-tight" x-text="selectedOrder?.customer || 'Walk-in Customer'"></h3>
                                <p class="text-[11px] text-[#7f837e] mt-0.5 truncate" x-text="(selectedOrder?.type || 'Takeaway') + ((selectedOrder?.type === 'Dine-in' || selectedOrder?.type === 'Dine In') && selectedOrder?.table && selectedOrder.table !== '-' && selectedOrder.table !== 'TA' ? ' (' + selectedOrder.table + ')' : '')"></p>
                            </div>
                        </div>

                        <!-- Fulfillment Pill -->
                        <span class="px-2.5 py-1 rounded-[8px] text-[10.5px] font-bold border shadow-2xs flex items-center gap-1.5 shrink-0"
                              :class="selectedOrder?.type === 'Delivery' ? 'bg-[#eff6ff] text-[#1d4ed8] border-[#bfdbfe]' : (selectedOrder?.type === 'Takeaway' ? 'bg-[#fff6f0] text-[#df5826] border-[#ffdec9]' : 'bg-[#eafbf2] text-[#00643b] border-[#a6f0c6]')">
                            <i :class="selectedOrder?.type === 'Delivery' ? 'fas fa-truck' : (selectedOrder?.type === 'Takeaway' ? 'fas fa-bag-shopping' : 'fas fa-utensils')" class="text-[10px]"></i>
                            <span x-text="selectedOrder?.type || 'Dine In'"></span>
                        </span>
                    </div>

                    <!-- Section Title -->
                    <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider text-[#7f837e] px-1 pt-1">
                        <span>Daftar Menu</span>
                        <span x-text="(selectedOrder?.items ? selectedOrder.items.length : 0) + ' Item'"></span>
                    </div>
                </div>

                <!-- Items Scrollable List -->
                <div class="px-5 sm:px-6 py-2 flex-grow overflow-y-auto max-h-[35vh] custom-scroll space-y-2 bg-white">
                    <template x-for="(item, idx) in selectedOrder?.items" :key="idx">
                        <div class="p-2.5 rounded-[12px] border border-[#f0f0ee] bg-white hover:border-[#df5826]/30 transition flex items-center gap-3">
                            <!-- Thumbnail / Icon -->
                            <div class="w-10 h-10 rounded-[10px] bg-[#f8f9fa] border border-[#e9ebf0] overflow-hidden shrink-0 flex items-center justify-center">
                                <img x-show="item.image" :src="item.image" class="w-full h-full object-cover">
                                <span x-show="!item.image" class="text-[11px] font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                            </div>

                            <!-- Details -->
                            <div class="flex-grow min-w-0">
                                <h4 class="text-[13px] font-bold text-[#111827] truncate leading-tight" x-text="item.name"></h4>
                                <p x-show="item.notes" class="text-[10.5px] text-[#df5826] font-medium flex items-center gap-1 mt-0.5">
                                    <i class="fas fa-comment-dots text-[9px]"></i>
                                    <span x-text="item.notes"></span>
                                </p>
                            </div>

                            <!-- Qty & Price -->
                            <div class="text-[11.5px] font-bold text-[#7f837e] px-2 py-0.5 bg-gray-100 rounded-[6px] shrink-0" x-text="item.qty + 'x'"></div>
                            <div class="text-[13px] font-extrabold text-[#111827] w-24 text-right shrink-0" x-text="formatRp(item.price * item.qty)"></div>
                        </div>
                    </template>
                </div>

                <!-- Total & Payment Summary -->
                <div class="px-5 sm:px-6 py-3.5 bg-[#fafaf9] border-t border-[#e9ebf0] flex items-center justify-between">
                    <div>
                        <p class="text-[10.5px] uppercase font-bold text-[#7f837e] tracking-wider mb-0.5">Total Harga</p>
                        <p class="text-[20px] font-extrabold text-[#df5826] leading-none" x-text="selectedOrder ? formatRp(selectedOrder.total) : '0'"></p>
                    </div>

                    <!-- Payment Status Badge -->
                    <div class="bg-white rounded-[10px] px-3 py-1.5 flex items-center gap-2 border border-[#e9ebf0] shadow-2xs">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]"
                             :class="(selectedOrder?.payment_status === 'PAID' || selectedOrder?.status === 'COMPLETED' || selectedOrder?.order_status === 'COMPLETED') ? 'bg-[#00643b] text-white' : 'bg-[#df5826] text-white'">
                            <i :class="(selectedOrder?.payment_status === 'PAID' || selectedOrder?.status === 'COMPLETED' || selectedOrder?.order_status === 'COMPLETED') ? 'fas fa-check' : 'fas fa-clock'"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-[#111827] leading-none mb-0.5" x-text="(selectedOrder?.payment_status === 'PAID' || selectedOrder?.status === 'COMPLETED') ? 'Pembayaran Lunas' : 'Belum Lunas'"></p>
                            <p class="text-[9.5px] font-medium text-[#7f837e] leading-none" x-text="selectedOrder?.payment_method || 'QRIS / Online'"></p>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 sm:p-5 bg-white border-t border-[#e9ebf0] flex items-center gap-3 shrink-0">
                    <button type="button" 
                            @click="showOrderDetailModal = false" 
                            class="w-1/3 py-2.5 px-4 rounded-[12px] text-[13px] font-semibold text-[#53565a] bg-white border border-[#e9ebf0] hover:bg-gray-50 hover:text-[#111827] transition cursor-pointer text-center">
                        Tutup
                    </button>
                    <button type="button" 
                            @click="window.open('/admin/api/orders/' + selectedOrder.id + '/print', '_blank')" 
                            class="w-2/3 py-2.5 px-4 rounded-[12px] text-[13px] font-bold bg-[#df5826] hover:bg-[#c94d00] active:scale-[0.98] text-white shadow-xs transition flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-print text-xs"></i>
                        <span>Cetak Struk</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: INCOMING ORDER ALERT -->
        <div x-show="activeIncomingOrder" 
             x-cloak 
             @keydown.escape.window="if (activeIncomingOrder) autoCloseOrder()"
             class="fixed inset-0 z-[200] flex items-center justify-center p-4 font-sans sm:items-center sm:p-0">
            <!-- Backdrop (Clicking outside safely dismisses popup to waiting list) -->
            <div @click="autoCloseOrder()" class="absolute inset-0 bg-black/40 backdrop-blur-xs transition-opacity cursor-pointer"></div>
            
            <!-- Modal Content -->
            <div class="relative z-10 bg-white w-full sm:w-[500px] rounded-[24px] shadow-[0_25px_60px_-15px_rgba(0,0,0,0.22)] flex flex-col border border-[#e9ebf0] overflow-hidden mx-auto my-auto"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-6 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header: Bell Icon, Title, Timer, and Close (X) Button -->
                <div class="p-5 sm:p-6 bg-white border-b border-[#e9ebf0] flex justify-between items-center">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-[14px] bg-[#fff6f0] border border-[#fbd4c0] text-[#df5826] flex items-center justify-center shadow-2xs shrink-0">
                            <i class="fas fa-bell text-lg animate-pulse"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#df5826] animate-ping"></span>
                                <h2 class="text-[17px] font-extrabold text-[#111827] leading-tight">Pesanan Baru Masuk!</h2>
                            </div>
                            <p class="text-[12px] text-[#7f837e] mt-0.5">Baru saja diterima</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2.5">
                        <!-- Timer Badge -->
                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-[10px] text-[13px] font-mono font-bold shrink-0 transition-colors"
                             :class="{
                                'bg-[#f4f4f5] text-gray-700 border border-gray-200': incomingTimer > 15,
                                'bg-red-50 text-red-600 border border-red-200 animate-pulse': incomingTimer <= 15
                             }">
                            <i class="far fa-clock text-xs"></i>
                            <span x-text="formatTime(incomingTimer)"></span>
                        </div>

                        <!-- Close (X) Button -->
                        <button type="button" 
                                @click="autoCloseOrder()" 
                                class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer"
                                title="Tutup ke Daftar Tunggu">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-5 sm:p-6 bg-[#fafaf9] space-y-4">
                    <!-- Queue info -->
                    <div x-show="incomingOrderQueue.length > 0" class="bg-amber-50 text-amber-700 border border-amber-200 text-[12px] font-bold py-1.5 px-3 rounded-[10px] text-center">
                        <span x-text="incomingOrderQueue.length"></span> pesanan lain menunggu antrean
                    </div>

                    <!-- Order Card -->
                    <div class="bg-white rounded-[16px] p-4 border border-[#e9ebf0] shadow-2xs space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-[12px] bg-[#f4f4f5] border border-gray-100 flex items-center justify-center text-gray-600 shrink-0">
                                    <i :class="activeIncomingOrder?.type === 'Dine In' ? 'fas fa-utensils' : 'fas fa-shopping-bag'" class="text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-[16px] font-extrabold text-[#111827]">
                                        Pesanan #<span x-text="activeIncomingOrder?.id"></span>
                                    </h3>
                                    <p class="text-[12px] text-[#7f837e]">
                                        <span x-text="activeIncomingOrder?.type || 'Takeaway'"></span> • <span x-text="activeIncomingOrder?.customer || 'Walk-in Customer'"></span>
                                    </p>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-[8px] text-[11px] font-bold bg-[#f4f4f5] text-gray-700 border border-gray-200" x-text="activeIncomingOrder?.table ? ('Meja ' + activeIncomingOrder.table) : 'TA'"></span>
                        </div>

                        <!-- Items List -->
                        <div class="border-t border-dashed border-[#e9ebf0] pt-3 max-h-[28vh] overflow-y-auto space-y-2">
                            <template x-for="(item, idx) in activeIncomingOrder?.items" :key="idx">
                                <div class="flex items-center justify-between gap-3 text-[13px]">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-[8px] bg-[#f4f4f5] border border-gray-100 overflow-hidden shrink-0 flex items-center justify-center text-[10px] font-bold text-[#df5826]">
                                            <template x-if="item.image">
                                                <img :src="item.image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image">
                                                <span x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                                            </template>
                                        </div>
                                        <span class="font-semibold text-gray-800 truncate" x-text="item.name"></span>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-xs font-bold text-gray-400" x-text="(item.qty || item.quantity || 1) + 'x'"></span>
                                        <span class="font-bold text-[#111827] text-xs w-20 text-right" x-text="formatRp(item.price * (item.qty || item.quantity || 1))"></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Total & Payment Badge -->
                        <div class="border-t border-[#e9ebf0] pt-3 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7f837e] block">Total Pesanan</span>
                                <span class="text-[20px] font-extrabold text-[#111827]" x-text="activeIncomingOrder ? formatRp(activeIncomingOrder.total) : 'Rp 0'"></span>
                            </div>

                            <div class="flex items-center gap-2 bg-[#f0fdf4] border border-[#bbf7d0] rounded-[10px] px-3 py-1.5 text-[#166534]">
                                <i class="fas fa-check-circle text-xs"></i>
                                <span class="text-[11px] font-bold">Lunas (QRIS)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-5 sm:p-6 bg-white border-t border-[#e9ebf0]">
                    <!-- State: Confirm Reject -->
                    <div x-show="incomingOrderState === 'reject_confirm'" class="flex flex-col gap-3">
                        <p class="text-center text-[14px] font-bold text-gray-800">Tolak pesanan #<span x-text="activeIncomingOrder?.id"></span>?</p>
                        <div class="flex gap-3">
                            <button type="button" @click="incomingOrderState = 'new'" class="flex-1 py-3 rounded-[12px] font-bold text-[13px] bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition cursor-pointer">Batal</button>
                            <button type="button" @click="confirmRejectOrder(false)" class="flex-1 py-3 rounded-[12px] font-bold text-[13px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition cursor-pointer">Ya, Tolak Pesanan</button>
                        </div>
                    </div>

                    <!-- State: New/Normal -->
                    <div x-show="incomingOrderState === 'new'" class="flex gap-3">
                        <button type="button" 
                                @click="incomingOrderState = 'reject_confirm'" 
                                class="w-[130px] shrink-0 py-3.5 rounded-[12px] font-bold text-[13px] bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-200 hover:bg-red-50 transition cursor-pointer">
                            Tolak Pesanan
                        </button>
                        <button type="button" 
                                @click="acceptOrder()" 
                                class="flex-1 py-3.5 rounded-[12px] font-bold text-[14px] bg-[#1a4d66] hover:bg-[#143d52] active:scale-[0.98] text-white transition flex items-center justify-center gap-2 shadow-xs cursor-pointer">
                            <i class="fas fa-coffee text-xs"></i>
                            <span>Terima & Siapkan</span>
                        </button>
                    </div>

                    <!-- State: Accepting -->
                    <div x-show="incomingOrderState === 'accepting'" class="flex justify-center items-center py-3">
                        <i class="fas fa-spinner fa-spin text-[#1a4d66] text-xl"></i>
                        <span class="ml-2.5 font-bold text-[14px] text-[#1a4d66]">Menerima pesanan...</span>
                    </div>

                    <!-- State: Accepted -->
                    <div x-show="incomingOrderState === 'accepted'" class="flex flex-col justify-center items-center py-1">
                        <div class="w-9 h-9 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-1">
                            <i class="fas fa-check text-base"></i>
                        </div>
                        <span class="font-extrabold text-[14px] text-emerald-700">Pesanan Diterima!</span>
                        <span class="text-[12px] text-[#7f837e]">Sedang disiapkan dapur...</span>
                    </div>
                    
                    <!-- State: Rejected -->
                    <div x-show="incomingOrderState === 'rejected'" class="flex flex-col justify-center items-center py-1">
                        <div class="w-9 h-9 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-1">
                            <i class="fas fa-times text-base"></i>
                        </div>
                        <span class="font-extrabold text-[14px] text-red-600">Pesanan Ditolak</span>
                    </div>

                    <!-- Timeout Notice (Accurate copy) -->
                    <div class="text-center mt-3" x-show="['new', 'reject_confirm'].includes(incomingOrderState)">
                        <p class="text-[11.5px] font-medium text-[#7f837e] flex items-center justify-center gap-1.5">
                            <i class="far fa-clock text-xs"></i> 
                            <span>Pop-up otomatis ditutup ke daftar antrean dalam <strong x-text="incomingTimer" class="font-mono text-gray-800"></strong> detik</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        window.INITIAL_DATA = {
            shop: {{ Illuminate\Support\Js::from($dashboardShop) }},
            user: {{ Illuminate\Support\Js::from(auth()->user()->only(['id', 'name', 'email', 'role'])) }},
            permissions: {{ Illuminate\Support\Js::from(auth()->user()->effectivePermissions()) }},
            pages: {{ Illuminate\Support\Js::from($pageLinks) }},
            categories: @json($categories ?? []),
            tables: @json($tables ?? []),
            shopProducts: {{ Illuminate\Support\Js::from($shopProducts ?? []) }},
            modifierGroups: {{ Illuminate\Support\Js::from($modifierGroups ?? []) }}
        };

        const formatRp = (num) => {
            if (num === null || num === undefined || num === '') return 'Rp 0';
            if (typeof num === 'string') {
                const trimmed = num.trim();
                if (trimmed.startsWith('Rp')) {
                    const stripped = trimmed.replace(/^Rp\s*/i, '').replace(/\./g, '').replace(/,/g, '.');
                    const parsed = parseFloat(stripped);
                    if (!isNaN(parsed)) return 'Rp ' + Math.round(parsed).toLocaleString('id-ID');
                    return trimmed;
                }
                const parsed = parseFloat(trimmed);
                if (!isNaN(parsed)) return 'Rp ' + Math.round(parsed).toLocaleString('id-ID');
            }
            const cleanNum = Number(num);
            return 'Rp ' + (isNaN(cleanNum) ? 0 : Math.round(cleanNum)).toLocaleString('id-ID');
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                formatRp(n) { return formatRp(n); },
                user: window.INITIAL_DATA.user || {},
                can(ability) { return window.INITIAL_DATA.permissions.includes(ability); },
                isMinimized: window.innerWidth < 1024,
                mobileNavOpen: false,
                isLoadingSummary: true,
                isLoadingMenus: true,
                isLoadingAnalytics: true,
                currentTab: '{{ $page }}',
                showAddCategoryModal: false,
                showManageCategoryModal: false,
                editingCategoryId: null,
                editingCategoryName: '',
                editingCategoryIcon: 'fa-mug-hot',
                isSavingCategoryAction: false,
                newCategoryName: '',
                newCategoryIcon: 'fa-mug-hot',
                categoryIcons: [
                    { icon: 'fa-mug-hot', label: 'Kopi' },
                    { icon: 'fa-glass-water', label: 'Minuman' },
                    { icon: 'fa-leaf', label: 'Teh' },
                    { icon: 'fa-utensils', label: 'Makanan' },
                    { icon: 'fa-bowl-rice', label: 'Nasi' },
                    { icon: 'fa-hamburger', label: 'Burger' },
                    { icon: 'fa-bread-slice', label: 'Roti' },
                    { icon: 'fa-ice-cream', label: 'Dessert' },
                    { icon: 'fa-layer-group', label: 'Lainnya' }
                ],
                showAddMenuModal: false,
                showAddTableModal: false,
                showResetQRModal: false,
                qrTableToReset: null,
                isResettingQR: false,
                users: [],
                categories: [],
                tables: (window.INITIAL_DATA.tables || []).map(t => typeof t === 'string' ? { id: t, name: t } : { id: t.name || t.id, name: t.name || t.id }),
                menuItems: [],
                openBillModal: {
                    show: false,
                    isLoading: false,
                    isSaving: false,
                    recalledOrderId: null,
                    customerName: '',
                    selectedTable: '',
                    fulfillmentType: 'DINE_IN',
                    searchQuery: '',
                    selectedCategory: 'all',
                    showPriceBreakdown: true,
                    products: [],
                    categories: [],
                    cart: [],
                    modifierModal: {
                        show: false,
                        product: null,
                        selectedVariant: null,
                        selectedModifiers: [],
                        notes: '',
                        qty: 1,
                    }
                },
                orders: [],
                analytics: null,
                topProductsFilter: 'today',
                isSaving: false,
                showOrderDetailModal: false,
                selectedOrder: null,
                incomingOrderQueue: [],
                activeIncomingOrder: null,
                incomingOrderState: 'idle',
                incomingTimer: 45,
                incomingTimerInterval: null,
                formatTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                },
                newTableId: '',
                activeMenuFilter: 'all',
                menuSortOrder: 'newest',
                menuPage: 1,
                menuPerPage: 8,
                selectedMenuIds: [],
                activeOrderFilter: 'all',
                subOrderStatusFilter: 'all',
                orderFulfillmentFilter: 'all',
                searchQuery: '',
                newMenu: { id: null, name: '', price: '', desc: '', categoryId: '', imagePreview: null, cogs: 0, recipes: [] },
                
                // Inventory & Recipes State
                rawMaterials: [],
                stockMutations: [],
                inventoryMetrics: { total_materials: 0, low_stock_count: 0, total_asset_value: 0 },
                inventorySubTab: 'materials',
                materialSearch: '',
                materialFilterCategory: 'all',
                materialFilterStatus: 'all',
                materialSortOrder: 'name_asc',
                materialPage: 1,
                materialPerPage: 12,
                selectedMaterialIds: [],
                isLoadingInventory: false,
                showMaterialModal: false,
                isSavingMaterial: false,
                stockUnits: {{ Illuminate\Support\Js::from(config('inventory.units')) }},
                editingUnitLocked: false,
                formatStock(value) { return Number(value || 0).toLocaleString('id-ID', {maximumFractionDigits: 6}); },
                stockUnitLabel(unit) { return this.stockUnits[unit] || unit || '—'; },
                stockUnitShort(unit) {
                    if (!unit) return '—';
                    const lower = String(unit).toLowerCase();
                    if (lower === 'pcs') return 'Pcs';
                    if (lower === 'dus') return 'Dus';
                    if (lower === 'cup') return 'Cup';
                    const label = this.stockUnits[lower] || unit;
                    return String(label).split(' ')[0] || label;
                },
                materialForm: { id: null, name: '', sku: '', unit: 'pcs', current_stock: 0, minimum_stock: 0, cost_per_unit: 0 },
                showAdjustModal: false,
                isSavingAdjustment: false,
                adjustForm: { raw_material_id: '', type: 'in', amount: '', notes: '', new_cost_per_unit: '' },

                getMaterialCategory(item) {
                    if (item.category) return item.category;
                    const name = (item.name || '').toLowerCase();
                    if (name.includes('kopi') || name.includes('coffee') || name.includes('beans') || name.includes('espresso')) return 'Coffee';
                    if (name.includes('susu') || name.includes('milk') || name.includes('cream') || name.includes('cheese') || name.includes('keju') || name.includes('dairy')) return 'Dairy';
                    if (name.includes('gula') || name.includes('sugar') || name.includes('aren') || name.includes('sirup') || name.includes('syrup') || name.includes('sweetener')) return 'Sweetener';
                    if (name.includes('powder') || name.includes('bubuk') || name.includes('matcha') || name.includes('cokelat') || name.includes('chocolate') || name.includes('tea') || name.includes('teh')) return 'Powder';
                    if (name.includes('daging') || name.includes('beef') || name.includes('chicken') || name.includes('patty') || name.includes('fillet') || name.includes('ayam') || name.includes('meat')) return 'Meat';
                    if (name.includes('fries') || name.includes('kentang') || name.includes('frozen') || name.includes('nugget') || name.includes('sosis')) return 'Frozen Food';
                    if (name.includes('minyak') || name.includes('oil') || name.includes('garam') || name.includes('salt') || name.includes('sauce') || name.includes('saos') || name.includes('bumbu')) return 'Cooking';
                    return 'Bahan Baku';
                },

                getMaterialCategoryBadgeClass(cat) {
                    switch (cat) {
                        case 'Coffee': return 'bg-[#fdf8f3] text-[#74472d]';
                        case 'Dairy': return 'bg-[#eef0ff] text-[#2a3ba2]';
                        case 'Sweetener': return 'bg-[#e8fbff] text-[#086d87]';
                        case 'Powder': return 'bg-[#f3e8ff] text-[#6b21a8]';
                        case 'Meat': return 'bg-[#fff0f4] text-[#b91f50]';
                        case 'Frozen Food': return 'bg-[#e8fbff] text-[#086d87]';
                        case 'Cooking': return 'bg-[#fef9c3] text-[#854d0e]';
                        default: return 'bg-[#f1f3f5] text-[#53565a]';
                    }
                },

                getMaterialStatus(item) {
                    const current = Number(item.current_stock || 0);
                    const min = Number(item.minimum_stock || 0);
                    if (current <= 0) return 'Habis';
                    if (current <= min) return 'Menipis';
                    return 'Aman';
                },

                get filteredMaterials() {
                    let items = [...this.rawMaterials];
                    if (this.materialSearch) {
                        const q = this.materialSearch.toLowerCase();
                        items = items.filter(m => (m.name && m.name.toLowerCase().includes(q)) || (m.sku && m.sku.toLowerCase().includes(q)));
                    }
                    if (this.materialFilterCategory !== 'all') {
                        items = items.filter(m => this.getMaterialCategory(m) === this.materialFilterCategory);
                    }
                    if (this.materialFilterStatus !== 'all') {
                        items = items.filter(m => this.getMaterialStatus(m) === this.materialFilterStatus);
                    }
                    if (this.materialSortOrder === 'name_asc') {
                        items.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                    } else if (this.materialSortOrder === 'name_desc') {
                        items.sort((a, b) => (b.name || '').localeCompare(a.name || ''));
                    } else if (this.materialSortOrder === 'stock_asc') {
                        items.sort((a, b) => Number(a.current_stock || 0) - Number(b.current_stock || 0));
                    } else if (this.materialSortOrder === 'stock_desc') {
                        items.sort((a, b) => Number(b.current_stock || 0) - Number(a.current_stock || 0));
                    } else if (this.materialSortOrder === 'cost_asc') {
                        items.sort((a, b) => Number(a.cost_per_unit || 0) - Number(b.cost_per_unit || 0));
                    } else if (this.materialSortOrder === 'cost_desc') {
                        items.sort((a, b) => Number(b.cost_per_unit || 0) - Number(a.cost_per_unit || 0));
                    }
                    return items;
                },

                get paginatedMaterials() {
                    const start = (this.materialPage - 1) * this.materialPerPage;
                    return this.filteredMaterials.slice(start, start + this.materialPerPage);
                },

                get totalMaterialPages() {
                    return Math.max(1, Math.ceil(this.filteredMaterials.length / this.materialPerPage));
                },

                get isAllMaterialsSelected() {
                    return this.paginatedMaterials.length > 0 && this.paginatedMaterials.every(m => this.selectedMaterialIds.includes(m.id));
                },

                toggleSelectAllMaterials(e) {
                    if (e.target.checked) {
                        const pageIds = this.paginatedMaterials.map(m => m.id);
                        this.selectedMaterialIds = Array.from(new Set([...this.selectedMaterialIds, ...pageIds]));
                    } else {
                        const pageIds = new Set(this.paginatedMaterials.map(m => m.id));
                        this.selectedMaterialIds = this.selectedMaterialIds.filter(id => !pageIds.has(id));
                    }
                },

                prevMaterialPage() {
                    if (this.materialPage > 1) this.materialPage--;
                },

                nextMaterialPage() {
                    if (this.materialPage < this.totalMaterialPages) this.materialPage++;
                },

                getMaterialUnit(materialId) {
                    const m = this.rawMaterials.find(x => x.id == materialId);
                    return m ? (m.unit_label || this.stockUnitLabel(m.unit)) : '';
                },

                calculateSingleRecipeCost(rec) {
                    if (!rec.raw_material_id || !rec.amount) return 0;
                    const m = this.rawMaterials.find(x => x.id == rec.raw_material_id);
                    return m ? Math.round(Number(m.cost_per_unit || 0) * Number(rec.amount || 0)) : 0;
                },

                get calculatedMenuHpp() {
                    if (!this.newMenu.recipes || this.newMenu.recipes.length === 0) return Number(this.newMenu.cogs || 0);
                    return this.newMenu.recipes.reduce((sum, r) => sum + this.calculateSingleRecipeCost(r), 0);
                },

                get estimatedProfitAmount() {
                    const price = Number(this.newMenu.price || 0);
                    return Math.max(0, price - this.calculatedMenuHpp);
                },

                get estimatedProfitMargin() {
                    const price = Number(this.newMenu.price || 0);
                    if (price <= 0) return 0;
                    return (this.estimatedProfitAmount / price) * 100;
                },

                addMenuRecipeRow() {
                    if (!this.newMenu.recipes) this.newMenu.recipes = [];
                    const defaultMat = this.rawMaterials.length > 0 ? this.rawMaterials[0].id : '';
                    this.newMenu.recipes.push({ raw_material_id: defaultMat, amount: 1 });
                },

                removeMenuRecipeRow(index) {
                    this.newMenu.recipes.splice(index, 1);
                },

                fetchInventory() {
    if (!this.can('manage-inventory')) { this.isLoadingInventory = false; return; }
                    this.isLoadingInventory = true;
                    fetch('/admin/api/inventory', {
                        headers: { 'Accept': 'application/json' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoadingInventory = false;
                        this.rawMaterials = data.materials || [];
                        this.stockMutations = data.mutations || [];
                        this.inventoryMetrics = data.metrics || { total_materials: 0, low_stock_count: 0, total_asset_value: 0 };
                        if (data.units) this.stockUnits = data.units;
                    })
                    .catch(err => {
                        this.isLoadingInventory = false;
                    });
                },

                openAddMaterialModal() {
                    this.editingUnitLocked = false;
                    this.materialForm = { id: null, name: '', sku: '', unit: 'pcs', current_stock: 0, minimum_stock: 10, cost_per_unit: 0 };
                    this.showMaterialModal = true;
                },

                editMaterial(item) {
                    this.editingUnitLocked = !!item.unit_locked;
                    this.materialForm = {
                        id: item.id,
                        name: item.name,
                        sku: item.sku || '',
                        unit: item.unit || 'pcs',
                        current_stock: Number(item.current_stock),
                        minimum_stock: Number(item.minimum_stock),
                        cost_per_unit: Number(item.cost_per_unit)
                    };
                    this.showMaterialModal = true;
                },

                saveMaterial() {
                    if (!this.materialForm.name || !this.materialForm.unit) {
                        this.addToast('Nama bahan dan satuan wajib diisi', 'error');
                        return;
                    }
                    this.isSavingMaterial = true;
                    fetch('/admin/api/inventory/material', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.materialForm)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingMaterial = false;
                        if (data.success) {
                            this.addToast(data.message, 'success');
                            this.showMaterialModal = false;
                            this.fetchInventory();
                        } else {
                            this.addToast(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Gagal menyimpan bahan', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingMaterial = false;
                        this.addToast('Terjadi kesalahan koneksi', 'error');
                    });
                },

                deleteMaterial(id) {
                    Swal.fire({
                        title: 'Hapus Bahan Baku?',
                        text: 'Bahan ini akan dihapus dari daftar inventori.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/admin/api/inventory/material/' + id, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.addToast(data.message, 'success');
                                    this.fetchInventory();
                                }
                            });
                        }
                    });
                },

                deleteSelectedMaterials() {
                    if (!this.selectedMaterialIds || this.selectedMaterialIds.length === 0) return;
                    const count = this.selectedMaterialIds.length;
                    Swal.fire({
                        title: `Hapus ${count} Bahan Baku?`,
                        text: `Sebanyak ${count} bahan terpilih akan dihapus dari daftar inventori.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#df5826',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: `Ya, Hapus (${count})`,
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/admin/api/inventory/materials/bulk-delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ ids: this.selectedMaterialIds })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    this.addToast(data.message, 'success');
                                    this.selectedMaterialIds = [];
                                    this.fetchInventory();
                                } else {
                                    this.addToast(data.message || 'Gagal menghapus bahan terpilih', 'error');
                                }
                            })
                            .catch(err => {
                                this.addToast('Terjadi kesalahan koneksi', 'error');
                            });
                        }
                    });
                },

                openStockAdjustModal(materialId = null) {
                    this.adjustForm = {
                        raw_material_id: materialId || (this.rawMaterials.length > 0 ? this.rawMaterials[0].id : ''),
                        type: 'in',
                        amount: '',
                        notes: '',
                        new_cost_per_unit: ''
                    };
                    this.showAdjustModal = true;
                },

                quickRestock(item) {
                    this.openStockAdjustModal(item.id);
                },

                submitStockAdjustment() {
                    if (!this.adjustForm.raw_material_id || this.adjustForm.amount === '' || !Number.isFinite(Number(this.adjustForm.amount)) || Number(this.adjustForm.amount) < 0 || (this.adjustForm.type !== 'adjustment' && Number(this.adjustForm.amount) === 0)) {
                        this.addToast('Pilih bahan dan masukkan jumlah yang valid', 'error');
                        return;
                    }
                    this.isSavingAdjustment = true;
                    fetch('/admin/api/inventory/adjust', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.adjustForm)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingAdjustment = false;
                        if (data.success) {
                            this.addToast(data.message, 'success');
                            this.showAdjustModal = false;
                            this.fetchInventory();
                            this.loadMenu();
                        } else {
                            this.addToast(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Gagal menyesuaikan stok', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingAdjustment = false;
                        this.addToast('Terjadi kesalahan koneksi', 'error');
                    });
                },

                showBulkUpload: false,
                isSavingBulk: false,
                draftMenus: [],
                categories: window.INITIAL_DATA.categories || [],
                initBulkUpload() {
                    // Always clear previous draft/edit state so "+ Tambah Menu" is never stuck in Edit mode
                    this.draftMenus = [];
                    this.addDraftRow();
                    this.showBulkUpload = true;
                },
                closeBulkUpload() {
                    this.showBulkUpload = false;
                    this.draftMenus = [];
                },
                addDraftRow() {
                    this.draftMenus.push({ 
                        id: null, 
                        name: '', 
                        price: '', 
                        discount_price: null,
                        discount_type: 'nominal',
                        discount_percent: null,
                        is_discount_active: false,
                        categoryId: this.categories.length > 0 ? this.categories[0].id : '', 
                        desc: '',
                        imagePreview: null, 
                        imageFile: null,
                        has_addons: false,
                        modifier_group_ids: [] 
                    });
                },
                setDiscountType(draft, type) {
                    draft.discount_type = type;
                    const basePrice = Number(draft.price) || 0;
                    if (type === 'percent') {
                        if (basePrice > 0 && draft.discount_price !== null && draft.discount_price !== '' && Number(draft.discount_price) < basePrice) {
                            draft.discount_percent = Math.round(((basePrice - Number(draft.discount_price)) / basePrice) * 100);
                        } else if (draft.discount_percent) {
                            this.handleDiscountPercentInput(draft);
                        }
                    } else {
                        if (basePrice > 0 && draft.discount_percent > 0 && draft.discount_percent < 100) {
                            draft.discount_price = Math.round(basePrice * (1 - (draft.discount_percent / 100)));
                        }
                    }
                },
                handleDiscountPercentInput(draft) {
                    const basePrice = Number(draft.price) || 0;
                    const percent = Number(draft.discount_percent);
                    if (basePrice > 0 && percent > 0 && percent < 100) {
                        draft.discount_price = Math.round(basePrice * (1 - (percent / 100)));
                    } else if (!percent || percent <= 0) {
                        draft.discount_price = null;
                    }
                },
                handleDiscountPriceInput(draft) {
                    const basePrice = Number(draft.price) || 0;
                    const discPrice = Number(draft.discount_price);
                    if (basePrice > 0 && discPrice > 0 && discPrice < basePrice) {
                        draft.discount_percent = Math.round(((basePrice - discPrice) / basePrice) * 100);
                    } else {
                        draft.discount_percent = null;
                    }
                },
                handleBasePriceChange(draft) {
                    const basePrice = Number(draft.price) || 0;
                    if (draft.discount_type === 'percent' && draft.discount_percent > 0 && draft.discount_percent < 100) {
                        draft.discount_price = Math.round(basePrice * (1 - (draft.discount_percent / 100)));
                    } else if (draft.discount_price > 0 && basePrice > 0 && Number(draft.discount_price) < basePrice) {
                        draft.discount_percent = Math.round(((basePrice - Number(draft.discount_price)) / basePrice) * 100);
                    }
                },
                removeDraftRow(index) {
                    this.draftMenus.splice(index, 1);
                    if (this.draftMenus.length === 0) this.addDraftRow();
                },
                removeDraftImage(index) {
                    if (this.draftMenus[index]) {
                        this.draftMenus[index].imagePreview = null;
                        this.draftMenus[index].imageFile = null;
                    }
                },
                handleDraftImageUpload(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (file.size > 5 * 1024 * 1024) {
                        this.addToast('Ukuran gambar maksimal 5MB', 'error');
                        return;
                    }
                    this.draftMenus[index].imageFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.draftMenus[index].imagePreview = e.target.result; };
                    reader.readAsDataURL(file);
                },
                downloadCsvTemplate() {
                    const csvContent = "\ufeff"
                        + "Nama Menu,Kategori,Harga,Deskripsi\n"
                        + "Americano,Coffee,22000,Espresso dengan air panas\n"
                        + "Caffe Latte,Coffee,28000,Espresso dengan steamed milk lembut\n"
                        + "Matcha Latte,Drinks,30000,Matcha Jepang murni dengan susu segar\n"
                        + "Butter Croissant,Pastry,24000,Pastry renyah dengan butter premium\n"
                        + "Beef Burger,Main Course,45000,Patty daging sapi gurih dengan saus spesial\n";
                    
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement("a");
                    link.setAttribute("href", url);
                    link.setAttribute("download", "template_menu_sparta.csv");
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    this.addToast('Template CSV berhasil diunduh. Silakan isi dan upload kembali.', 'info');
                },
                handleCSVUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        try {
                            const text = e.target.result;
                            if (!text || text.trim() === '') {
                                this.addToast('File CSV kosong', 'error');
                                return;
                            }
                            
                            const lines = text.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
                            if (lines.length < 2) {
                                this.addToast('File CSV harus memiliki baris judul dan minimal 1 baris data', 'error');
                                return;
                            }
                            
                            const headerLine = lines[0];
                            const commaCount = (headerLine.match(/,/g) || []).length;
                            const semiCount = (headerLine.match(/;/g) || []).length;
                            const delimiter = semiCount > commaCount ? ';' : ',';
                            
                            const parseLine = (lineStr) => {
                                const row = [];
                                let current = '';
                                let inQuotes = false;
                                for (let i = 0; i < lineStr.length; i++) {
                                    const c = lineStr[i];
                                    if (c === '"') {
                                        if (inQuotes && lineStr[i + 1] === '"') {
                                            current += '"';
                                            i++;
                                        } else {
                                            inQuotes = !inQuotes;
                                        }
                                    } else if (c === delimiter && !inQuotes) {
                                        row.push(current.trim());
                                        current = '';
                                    } else {
                                        current += c;
                                    }
                                }
                                row.push(current.trim());
                                return row;
                            };
                            
                            const headers = parseLine(headerLine).map(h => h.toLowerCase().replace(/[^a-z0-9]/g, ''));
                            
                            let nameIdx = headers.findIndex(h => h.includes('nama') || h.includes('name') || h.includes('menu') || h.includes('item'));
                            let catIdx = headers.findIndex(h => h.includes('kategori') || h.includes('category') || h.includes('cat'));
                            let priceIdx = headers.findIndex(h => h.includes('harga') || h.includes('price') || h.includes('nominal'));
                            let descIdx = headers.findIndex(h => h.includes('deskripsi') || h.includes('desc') || h.includes('description') || h.includes('ket'));
                            
                            if (nameIdx === -1) nameIdx = 0;
                            if (catIdx === -1) catIdx = 1;
                            if (priceIdx === -1) priceIdx = 2;
                            if (descIdx === -1) descIdx = 3;
                            
                            const newDrafts = [];
                            for (let i = 1; i < lines.length; i++) {
                                const cols = parseLine(lines[i]);
                                const name = cols[nameIdx] ? cols[nameIdx].replace(/^["']|["']$/g, '').trim() : '';
                                if (!name) continue;
                                
                                const catRaw = (cols[catIdx] ? cols[catIdx].replace(/^["']|["']$/g, '').trim() : '') || 'Coffee';
                                const priceRaw = cols[priceIdx] ? String(cols[priceIdx]).replace(/[^0-9]/g, '') : '0';
                                const price = parseInt(priceRaw) || 0;
                                const desc = (descIdx !== -1 && cols[descIdx]) ? cols[descIdx].replace(/^["']|["']$/g, '').trim() : '';
                                
                                let matchedCatId = '';
                                const foundCat = this.categories.find(c => c.name && c.name.toLowerCase() === catRaw.toLowerCase());
                                if (foundCat) {
                                    matchedCatId = foundCat.id;
                                } else {
                                    matchedCatId = catRaw;
                                }
                                
                                newDrafts.push({
                                    id: null,
                                    name: name,
                                    categoryId: matchedCatId,
                                    price: price,
                                    desc: desc,
                                    imagePreview: null,
                                    imageFile: null
                                });
                            }
                            
                            if (newDrafts.length > 0) {
                                if (this.draftMenus.length === 1 && !this.draftMenus[0].name && !this.draftMenus[0].price) {
                                    this.draftMenus = [];
                                }
                                this.draftMenus.push(...newDrafts);
                                this.addToast(`Berhasil memuat ${newDrafts.length} menu dari CSV! Periksa data dan klik Publish Menu.`, 'success');
                            } else {
                                this.addToast('Tidak ada data menu yang valid ditemukan di dalam file CSV.', 'error');
                            }
                        } catch (err) {
                            console.error('CSV parse error:', err);
                            this.addToast('Gagal memproses file CSV: Format tidak didukung', 'error');
                        } finally {
                            event.target.value = '';
                        }
                    };
                    reader.readAsText(file);
                },
                saveBulkMenu() {
                    const validItems = this.draftMenus.filter(m => m.name && m.name.trim() !== '' && m.price !== '' && !isNaN(Number(m.price)));
                    if (validItems.length === 0) {
                        this.addToast('Mohon isi minimal Nama dan Harga menu', 'error');
                        return;
                    }
                    
                    this.isSavingBulk = true;
                    let formData = new FormData();
                    validItems.forEach((item, index) => {
                        if (item.id) formData.append(`items[${index}][id]`, item.id);
                        formData.append(`items[${index}][name]`, item.name.trim());
                        formData.append(`items[${index}][price]`, item.price);
                        formData.append(`items[${index}][category_id]`, item.categoryId);
                        formData.append(`items[${index}][category_name]`, item.categoryId);
                        formData.append(`items[${index}][discount_price]`, (item.discount_price !== undefined && item.discount_price !== null && item.discount_price !== '') ? item.discount_price : '');
                        formData.append(`items[${index}][is_discount_active]`, item.is_discount_active ? '1' : '0');
                        if (item.desc) formData.append(`items[${index}][description]`, item.desc);
                        if (item.imageFile) formData.append(`images[${index}]`, item.imageFile);
                        if (item.has_addons && Array.isArray(item.modifier_group_ids)) {
                            item.modifier_group_ids.forEach((gid, gIdx) => {
                                formData.append(`items[${index}][modifier_group_ids][${gIdx}]`, gid);
                            });
                        } else {
                            formData.append(`items[${index}][modifier_group_ids]`, '[]');
                        }
                    });

                    fetch('/admin/api/menu/bulk', {
                        method: 'POST',
                        headers: { 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingBulk = false;
                        if (data.success) {
                            if (data.categories && Array.isArray(data.categories)) {
                                this.categories = data.categories;
                            }
                            if (data.menus && Array.isArray(data.menus)) {
                                data.menus.forEach(savedItem => {
                                    const formatted = {
                                        id: savedItem.id,
                                        name: savedItem.name,
                                        price: Number(savedItem.price),
                                        discount_price: savedItem.discount_price !== null && savedItem.discount_price !== undefined ? Number(savedItem.discount_price) : null,
                                        is_discount_active: Boolean(savedItem.is_discount_active),
                                        effective_price: Number(savedItem.effective_price || savedItem.price),
                                        discount_percentage: savedItem.discount_percentage || null,
                                        has_active_discount: Boolean(savedItem.has_active_discount),
                                        category_name: savedItem.category ? savedItem.category.name : (savedItem.category_name || 'Coffee'),
                                        categoryId: savedItem.category_id,
                                        category_id: savedItem.category_id,
                                        cogs: Number(savedItem.cogs || 0),
                                        is_sold_out: savedItem.is_sold_out,
                                        image: savedItem.image_url ? (savedItem.image_url + '?v=' + Date.now()) : null,
                                        desc: savedItem.description || ''
                                    };
                                    const existingIdx = this.menuItems.findIndex(m => m.id === savedItem.id);
                                    if (existingIdx !== -1) {
                                        this.menuItems[existingIdx] = Object.assign({}, this.menuItems[existingIdx], formatted);
                                    } else {
                                        this.menuItems.unshift(formatted);
                                    }
                                });
                            }
                            this.addToast('Berhasil menyimpan menu!', 'success');
                            this.showBulkUpload = false;
                            this.draftMenus = [];
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan menu', 'error');
                        }
                    }).catch(err => {
                        this.isSavingBulk = false;
                        this.addToast('Terjadi kesalahan saat menyimpan menu', 'error');
                    });
                },
                toasts: [],
                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 3000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },
                brandColorOptions: window.OQARI_THEME.primaryOptions,
                fontOptions: [
                    { id: 'poppins', name: 'Poppins', category: 'Modern Sans', family: "'Poppins', sans-serif", preview: 'Modern, bersih & mudah dibaca' },
                    { id: 'playfair', name: 'Playfair Display', category: 'Elegan Serif', family: "'Playfair Display', serif", preview: 'Elegan, mewah & klasik' },
                    { id: 'nunito', name: 'Nunito', category: 'Rounded Sans', family: "'Nunito', sans-serif", preview: 'Ramah, hangat & santai' },
                    { id: 'jakarta', name: 'Plus Jakarta Sans', category: 'Trendy Sans', family: "'Plus Jakarta Sans', sans-serif", preview: 'Dinamis, bersih & profesional' },
                    { id: 'lora', name: 'Lora', category: 'Bistro Serif', family: "'Lora', serif", preview: 'Artisan bistro & coffeehouse' },
                    { id: 'cinzel', name: 'Cinzel', category: 'Luxury Display', family: "'Cinzel', serif", preview: 'Eksklusif, megah & berkelas' },
                    { id: 'montserrat', name: 'Montserrat', category: 'Urban Sans', family: "'Montserrat', sans-serif", preview: 'Tegas, urban & kontemporer' },
                    { id: 'oswald', name: 'Oswald', category: 'Industrial Display', family: "'Oswald', sans-serif", preview: 'BOLD ROASTERY & ESPRESSO' },
                    { id: 'caveat', name: 'Caveat', category: 'Handwritten', family: "'Caveat', cursive", preview: 'Sentuhan kapur tulis papan cafe' },
                    { id: 'quicksand', name: 'Quicksand', category: 'Soft Casual', family: "'Quicksand', sans-serif", preview: 'Lembut, manis & dessert bakery' },
                    { id: 'cormorant', name: 'Cormorant Garamond', category: 'Vintage Serif', family: "'Cormorant Garamond', serif", preview: 'Klasik Eropa & warisan cafe' },
                    { id: 'inter', name: 'Inter', category: 'Minimalist Sans', family: "'Inter', sans-serif", preview: 'Ultra minimalis & presisi tinggi' },
                ],
                getFontFamilyCss(key) {
                    const found = this.fontOptions.find(f => f.id === key);
                    return found ? found.family : "'Poppins', sans-serif";
                },
                getFontName(key) {
                    const found = this.fontOptions.find(f => f.id === key);
                    return found ? found.name : (key ? key.charAt(0).toUpperCase() + key.slice(1) : 'Poppins');
                },
                getFontCategory(key) {
                    const found = this.fontOptions.find(f => f.id === key);
                    return found ? found.category : 'Sans-serif';
                },
                allShopProducts: (window.INITIAL_DATA.shopProducts || []).map(p => {
                    const price = Number(p.price || 0);
                    const discPrice = (p.discount_price !== null && p.discount_price !== undefined && p.discount_price !== '') ? Number(p.discount_price) : null;
                    const isDiscActive = Boolean(p.is_discount_active);
                    const hasDiscount = isDiscActive && discPrice !== null && discPrice > 0 && discPrice < price;
                    const effectivePrice = hasDiscount ? discPrice : price;
                    const discountPct = hasDiscount ? Math.round(((price - discPrice) / price) * 100) : null;
                    return {
                        ...p,
                        price,
                        discount_price: discPrice,
                        is_discount_active: isDiscActive,
                        effective_price: effectivePrice,
                        discount_percentage: discountPct,
                        has_active_discount: hasDiscount,
                        image_url: p.image_url || (p.image_path ? (p.image_path.startsWith('http') || p.image_path.startsWith('/') ? p.image_path : '/storage/' + p.image_path) : null),
                        is_best_seller: Boolean(p.is_best_seller)
                    };
                }),
                bestSellerSearch: '',
                bestSellerFilter: 'all',
                get filteredBestSellerProducts() {
                    let prods = this.allShopProducts || [];
                    if (this.bestSellerFilter === 'selected') {
                        prods = prods.filter(p => this.settings.best_seller_ids.includes(p.id));
                    }
                    if (this.bestSellerSearch && this.bestSellerSearch.trim() !== '') {
                        const q = this.bestSellerSearch.toLowerCase().trim();
                        prods = prods.filter(p => p.name.toLowerCase().includes(q) || (p.category && p.category.name && p.category.name.toLowerCase().includes(q)));
                    }
                    return prods;
                },
                toggleBestSeller(productId) {
                    const id = Number(productId);
                    const idx = this.settings.best_seller_ids.indexOf(id);
                    if (idx > -1) {
                        this.settings.best_seller_ids.splice(idx, 1);
                    } else {
                        this.settings.best_seller_ids.push(id);
                    }
                    const prod = this.allShopProducts.find(p => p.id === id);
                    if (prod) {
                        prod.is_best_seller = this.settings.best_seller_ids.includes(id);
                    }
                },
                handleBgImageUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (file.size > 5 * 1024 * 1024) {
                        this.addToast('Ukuran gambar latar maksimal 5MB!', 'error');
                        return;
                    }
                    this.settings.backgroundImageFile = file;
                    this.settings.removeBackgroundImage = false;
                    this.settings.background_type = 'image';
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.settings.backgroundImagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },
                removeBgImage() {
                    this.settings.backgroundImageFile = null;
                    this.settings.backgroundImagePreview = null;
                    this.settings.removeBackgroundImage = true;
                    if (this.settings.background_type === 'image') {
                        this.settings.background_type = 'pattern';
                    }
                },
                settings: {
                    name: window.INITIAL_DATA.shop?.name || '',
                    slug: window.INITIAL_DATA.shop?.slug || '',
                    primary_color: window.INITIAL_DATA.shop?.primary_color || window.OQARI_THEME.primary,
                    logoPreview: window.INITIAL_DATA.shop?.logo_url ? window.INITIAL_DATA.shop.logo_url : '',
                    logoFile: null,
                    slogan: window.INITIAL_DATA.shop?.slogan || '',
                    is_open: window.INITIAL_DATA.shop?.is_open ?? true,
                    theme_style: ['list', 'grid', 'compact', 'cards'].includes(window.INITIAL_DATA.shop?.theme_style) ? window.INITIAL_DATA.shop?.theme_style : 'list',
                    font_family: window.INITIAL_DATA.shop?.font_family || 'poppins',
                    title_font_family: window.INITIAL_DATA.shop?.title_font_family || 'poppins',
                    receipt_header: window.INITIAL_DATA.shop?.receipt_header || '',
                    receipt_footer: window.INITIAL_DATA.shop?.receipt_footer || 'Terima kasih atas kunjungan Anda!\nSilakan datang kembali.',
                    receipt_settings: Object.assign({
                        show_logo: true,
                        show_address: true,
                        show_phone: true,
                        show_social: true,
                        show_wifi: false,
                        wifi_ssid: '',
                        wifi_password: '',
                        show_qr: false,
                        qr_type: 'menu',
                        paper_width: '58mm',
                        kitchen_ticket_title: 'TIKET PESANAN DAPUR',
                        kitchen_show_prices: false,
                        kitchen_font_size: 'normal',
                        kitchen_highlight_notes: true,
                        kitchen_show_cashier: true
                    }, window.INITIAL_DATA.shop?.receipt_settings || {}),
                    printers: (window.INITIAL_DATA.shop?.printers && window.INITIAL_DATA.shop?.printers.length > 0)
                        ? window.INITIAL_DATA.shop.printers
                        : ((window.INITIAL_DATA.shop?.printer_settings && window.INITIAL_DATA.shop?.printer_settings.length > 0)
                            ? window.INITIAL_DATA.shop.printer_settings
                            : [
                                { id: 1, name: 'Printer Kasir Utama', location: 'cashier', connection_type: 'browser', ip_address: '', port: 9100, paper_width: '58mm', category_ids: [], is_active: true, auto_print: true },
                                { id: 2, name: 'Printer Dapur / Bar', location: 'kitchen', connection_type: 'browser', ip_address: '', port: 9100, paper_width: '58mm', category_ids: [], is_active: true, auto_print: false }
                            ]),
                    receiptPreviewTab: 'customer',
                    discount_settings: Object.assign({
                        is_enabled: true,
                        allow_custom: true,
                        show_highlight_menu: true,
                        show_discount_section: true,
                        show_discount_badge: true,
                        highlight_title: 'Menu Rekomendasi Pilihan',
                        presets: [
                            { id: 'p1', name: '5%', type: 'percentage', value: 5 },
                            { id: 'p2', name: '10%', type: 'percentage', value: 10 },
                            { id: 'p3', name: '15%', type: 'percentage', value: 15 },
                            { id: 'p4', name: '20%', type: 'percentage', value: 20 },
                            { id: 'p5', name: 'Rp 5.000', type: 'fixed', value: 5000 },
                            { id: 'p6', name: 'Rp 10.000', type: 'fixed', value: 10000 }
                        ]
                    }, window.INITIAL_DATA.shop?.discount_settings || {}),
                    instagram_link: window.INITIAL_DATA.shop?.instagram_link || '',
                    tiktok_link: window.INITIAL_DATA.shop?.tiktok_link || '',
                    social_settings: Object.assign({
                        show_whatsapp: true,
                        show_instagram: true,
                        show_tiktok: true,
                        show_maps: true
                    }, window.INITIAL_DATA.shop?.social_settings || {}),
                    whatsapp_number: window.INITIAL_DATA.shop?.whatsapp_number || '',
                    maps_link: window.INITIAL_DATA.shop?.maps_link || '',
                    is_banner_active: window.INITIAL_DATA.shop?.is_banner_active ?? true,
                    banners: (window.INITIAL_DATA.shop?.banners || []).map(b => b ? (b.startsWith('http') || b.startsWith('/') ? b : '/storage/' + b) : null).concat([null, null, null]).slice(0, 3),
                    bannerFiles: [null, null, null],
                    bannerPaths: (window.INITIAL_DATA.shop?.banners || []).concat([null, null, null]).slice(0, 3),
                    background_type: window.INITIAL_DATA.shop?.background_type || 'pattern',
                    background_pattern: window.INITIAL_DATA.shop?.background_pattern || 'dots',
                    background_color: window.INITIAL_DATA.shop?.background_color || '#fdfbf7',
                    background_overlay_opacity: window.INITIAL_DATA.shop?.background_overlay_opacity ?? 85,
                    backgroundImagePreview: window.INITIAL_DATA.shop?.background_image_url || null,
                    backgroundImageFile: null,
                    removeBackgroundImage: false,
                    best_seller_ids: (window.INITIAL_DATA.shopProducts || []).filter(p => p.is_best_seller).map(p => p.id),
                    operating_hours: window.INITIAL_DATA.shop?.operating_hours || {
                        monday: { open: '08:00', close: '22:00', is_closed: false },
                        tuesday: { open: '08:00', close: '22:00', is_closed: false },
                        wednesday: { open: '08:00', close: '22:00', is_closed: false },
                        thursday: { open: '08:00', close: '22:00', is_closed: false },
                        friday: { open: '08:00', close: '22:00', is_closed: false },
                        saturday: { open: '08:00', close: '22:00', is_closed: false },
                        sunday: { open: '08:00', close: '22:00', is_closed: false },
                    }
                },
                isSavingSettings: false,
                operatingHours: (() => {
                    const raw = window.INITIAL_DATA.shop?.operating_hours;
                    const defWeekly = {
                        monday: { open: '08:00', close: '22:00', is_closed: false },
                        tuesday: { open: '08:00', close: '22:00', is_closed: false },
                        wednesday: { open: '08:00', close: '22:00', is_closed: false },
                        thursday: { open: '08:00', close: '21:00', is_closed: false },
                        friday: { open: '08:00', close: '23:00', is_closed: false },
                        saturday: { open: '08:00', close: '23:00', is_closed: false },
                        sunday: { open: '08:00', close: '22:00', is_closed: true }
                    };
                    let weekly = { ...defWeekly };
                    if (raw?.weekly && typeof raw.weekly === 'object') {
                        weekly = { ...defWeekly, ...raw.weekly };
                    } else if (raw?.monday) {
                        weekly = { ...defWeekly, ...raw };
                    }
                    return {
                        weekly: weekly,
                        holidays: Array.isArray(raw?.holidays) ? JSON.parse(JSON.stringify(raw.holidays)) : [
                            { date: '2026-08-17', name: 'Hari Kemerdekaan', is_closed: true },
                            { date: '2026-12-25', name: 'Hari Natal', is_closed: true }
                        ],
                        timezone: raw?.timezone || 'Asia/Jakarta',
                        auto_close: raw?.auto_close ?? true,
                        allow_preorder: raw?.allow_preorder ?? false
                    };
                })(),
                originalOperatingHours: null,
                isSavingHours: false,
                showHolidayModal: false,
                newHoliday: { date: '', name: '', is_closed: true },
                timeOptions: [
                    '06:00', '06:30', '07:00', '07:30', '08:00', '08:30', '09:00', '09:30',
                    '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '13:00', '13:30',
                    '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30',
                    '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30',
                    '22:00', '22:30', '23:00', '23:30', '23:59', '00:00', '01:00', '02:00'
                ],
                daysList: [
                    { key: 'monday', label: 'Senin' },
                    { key: 'tuesday', label: 'Selasa' },
                    { key: 'wednesday', label: 'Rabu' },
                    { key: 'thursday', label: 'Kamis' },
                    { key: 'friday', label: 'Jumat' },
                    { key: 'saturday', label: 'Sabtu' },
                    { key: 'sunday', label: 'Minggu' }
                ],
                get todayFormatted() {
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const now = new Date();
                    return days[now.getDay()] + ', ' + String(now.getDate()).padStart(2, '0') + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
                },
                get currentDayKey() {
                    const dayKeys = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    return dayKeys[new Date().getDay()];
                },
                get todaySchedule() {
                    return this.operatingHours?.weekly?.[this.currentDayKey] || { open: '08:00', close: '22:00', is_closed: false };
                },
                get tzShort() {
                    if (this.operatingHours?.timezone === 'Asia/Makassar') return 'WITA';
                    if (this.operatingHours?.timezone === 'Asia/Jayapura') return 'WIT';
                    return 'WIB';
                },
                formatHolidayDate(dateStr) {
                    if (!dateStr) return '';
                    try {
                        const parts = dateStr.split('-');
                        if (parts.length === 3) {
                            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                            const day = parseInt(parts[2], 10);
                            const m = parseInt(parts[1], 10) - 1;
                            const y = parts[0];
                            return day + ' ' + (months[m] || parts[1]) + ' ' + y;
                        }
                        return dateStr;
                    } catch(e) {
                        return dateStr;
                    }
                },
                openAddHolidayModal() {
                    this.newHoliday = {
                        date: new Date().toISOString().split('T')[0],
                        name: '',
                        is_closed: true
                    };
                    this.showHolidayModal = true;
                },
                addHoliday() {
                    if (!this.newHoliday.date || !this.newHoliday.name.trim()) {
                        this.addToast('Tanggal dan keterangan wajib diisi!', 'error');
                        return;
                    }
                    if (!this.operatingHours.holidays) {
                        this.operatingHours.holidays = [];
                    }
                    this.operatingHours.holidays.push({
                        date: this.newHoliday.date,
                        name: this.newHoliday.name.trim(),
                        is_closed: Boolean(this.newHoliday.is_closed)
                    });
                    this.operatingHours.holidays.sort((a, b) => new Date(a.date) - new Date(b.date));
                    this.showHolidayModal = false;
                    this.addToast('Tanggal khusus berhasil ditambahkan!', 'success');
                },
                removeHoliday(idx) {
                    this.operatingHours.holidays.splice(idx, 1);
                    this.addToast('Tanggal khusus dihapus', 'info');
                },
                resetOperatingHours() {
                    if (this.originalOperatingHours) {
                        this.operatingHours = JSON.parse(JSON.stringify(this.originalOperatingHours));
                        this.addToast('Jadwal operasional dikembalikan ke kondisi sebelumnya', 'info');
                    }
                },
                saveOperatingHours() {
                    this.isSavingHours = true;
                    fetch('/admin/api/operating-hours', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ operating_hours: this.operatingHours })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingHours = false;
                        if (data.success) {
                            this.addToast('Jadwal operasional berhasil disimpan!', 'success');
                            this.originalOperatingHours = JSON.parse(JSON.stringify(this.operatingHours));
                            if (window.INITIAL_DATA.shop) {
                                window.INITIAL_DATA.shop.operating_hours = JSON.parse(JSON.stringify(this.operatingHours));
                            }
                            this.settings.operating_hours = this.operatingHours;
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan jadwal operasional', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingHours = false;
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });
                },
                get filteredMenuItems() {
                    let items = [...this.menuItems];
                    if (this.activeMenuFilter !== 'all') {
                        items = items.filter(m => {
                            if (m.categoryId == this.activeMenuFilter) return true;
                            if (String(m.categoryId).toLowerCase() === String(this.activeMenuFilter).toLowerCase()) return true;
                            if (m.categoryName && String(m.categoryName).toLowerCase() === String(this.activeMenuFilter).toLowerCase()) return true;
                            return false;
                        });
                    }
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(m => {
                            const name = (m.name || '').toLowerCase();
                            const desc = (m.desc || m.description || '').toLowerCase();
                            return name.includes(q) || desc.includes(q);
                        });
                    }
                    if (this.menuSortOrder === 'price_asc') {
                        items.sort((a, b) => (Number(a.price) || 0) - (Number(b.price) || 0));
                    } else if (this.menuSortOrder === 'price_desc') {
                        items.sort((a, b) => (Number(b.price) || 0) - (Number(a.price) || 0));
                    } else {
                        items.sort((a, b) => (b.id || 0) - (a.id || 0));
                    }
                    return items;
                },
                get paginatedMenuItems() {
                    const start = (this.menuPage - 1) * this.menuPerPage;
                    return this.filteredMenuItems.slice(start, start + this.menuPerPage);
                },
                get totalMenuPages() {
                    return Math.max(1, Math.ceil(this.filteredMenuItems.length / this.menuPerPage));
                },
                get isAllMenusSelected() {
                    return this.paginatedMenuItems.length > 0 && this.paginatedMenuItems.every(m => this.selectedMenuIds.includes(m.id));
                },
                toggleSelectAllMenus(e) {
                    if (e.target.checked) {
                        const pageIds = this.paginatedMenuItems.map(m => m.id);
                        this.selectedMenuIds = Array.from(new Set([...this.selectedMenuIds, ...pageIds]));
                    } else {
                        const pageIds = new Set(this.paginatedMenuItems.map(m => m.id));
                        this.selectedMenuIds = this.selectedMenuIds.filter(id => !pageIds.has(id));
                    }
                },
                setMenuFilter(filter) {
                    this.activeMenuFilter = filter;
                    this.menuPage = 1;
                },
                prevMenuPage() {
                    if (this.menuPage > 1) this.menuPage--;
                },
                nextMenuPage() {
                    if (this.menuPage < this.totalMenuPages) this.menuPage++;
                },
                getCategoryBadgeClass(catName) {
                    if (!catName) return 'bg-[#fdf8f3] text-[#74472d]';
                    const name = catName.toLowerCase();
                    if (name.includes('coffee') || name.includes('kopi')) {
                        return 'bg-[#fdf8f3] text-[#74472d]';
                    }
                    if (name.includes('snack') || name.includes('cemilan')) {
                        return 'bg-[#faf5ff] text-[#7e22ce]';
                    }
                    if (name.includes('drink') || name.includes('minuman') || name.includes('tea') || name.includes('latte') || name.includes('matcha')) {
                        return 'bg-[#ecfeff] text-[#0e7490]';
                    }
                    if (name.includes('main') || name.includes('makanan') || name.includes('food') || name.includes('meal') || name.includes('nasi') || name.includes('burger')) {
                        return 'bg-[#fff7ed] text-[#c2410c]';
                    }
                    return 'bg-[#fdf8f3] text-[#74472d]';
                },
                getCategoryIcon(catName) {
                    if (!catName) return 'fa-utensils';
                    const name = catName.toLowerCase();
                    if (name.includes('promo') || name.includes('diskon')) return 'fa-percent';
                    if (name.includes('kopi') || name.includes('coffee') || name.includes('espresso')) return 'fa-coffee';
                    if (name.includes('teh') || name.includes('tea') || name.includes('leaf') || name.includes('matcha')) return 'fa-leaf';
                    if (name.includes('makan') || name.includes('food') || name.includes('rice') || name.includes('nasi') || name.includes('mie') || name.includes('ayam') || name.includes('sate')) return 'fa-utensils';
                    if (name.includes('snack') || name.includes('cemilan') || name.includes('roti') || name.includes('toast') || name.includes('cookie') || name.includes('kentang')) return 'fa-cookie';
                    if (name.includes('minum') || name.includes('drink') || name.includes('beverage') || name.includes('juice') || name.includes('boba') || name.includes('soda')) return 'fa-cocktail';
                    if (name.includes('dessert') || name.includes('manis') || name.includes('ice') || name.includes('es')) return 'fa-ice-cream';
                    return 'fa-layer-group';
                },
                get previewProducts() {
                    if (this.allShopProducts && this.allShopProducts.length > 0) {
                        return this.allShopProducts.slice(0, 2);
                    }
                    return [
                        {
                            id: 'mock-1',
                            name: 'Kopi Susu Gula Aren',
                            price: 22000,
                            discount_price: 18000,
                            effective_price: 18000,
                            has_active_discount: true,
                            discount_percentage: 18,
                            description: 'Espresso dengan susu segar dan gula aren khas.',
                            image_url: null,
                            is_best_seller: true,
                        },
                        {
                            id: 'mock-2',
                            name: 'Butter Croissant',
                            price: 25000,
                            discount_price: null,
                            effective_price: 25000,
                            has_active_discount: false,
                            discount_percentage: null,
                            description: 'Pastry mentega renyah dan lembut di dalam.',
                            image_url: null,
                            is_best_seller: false,
                        }
                    ];
                },
                get previewCategories() {
                    let list = [];
                    if (this.settings?.discount_settings?.show_discount_section ?? true) {
                        list.push({ id: 'cat-promo', name: 'Promo', icon: 'fa-percent' });
                    }
                    if (this.categories && this.categories.length > 0) {
                        list = list.concat(this.categories.slice(0, list.length > 0 ? 2 : 3));
                    } else {
                        list = list.concat([
                            { id: 'mock-cat-1', name: 'Coffee' },
                            { id: 'mock-cat-2', name: 'Foods' }
                        ]);
                    }
                    return list;
                },
                isOpenBillOrder(o) {
                    if (!o) return false;
                    if (o.status === 'HOLD' || o.order_status === 'HOLD') return true;
                    if (o.is_open_bill) return true;
                    return (o.payment_status === 'UNPAID' && !['Completed', 'COMPLETED', 'Dibatalkan', 'CANCELLED'].includes(o.status || o.order_status));
                },
                isPaidOrder(o) {
                    if (!o) return false;
                    return !this.isOpenBillOrder(o);
                },
                setPrimaryOrderFilter(tab) {
                    this.activeOrderFilter = tab;
                    this.subOrderStatusFilter = 'all';
                },
                getOrderSubStatus(o) {
                    if (!o) return 'new';
                    const s = o.status || o.order_status || '';
                    if (['Completed', 'COMPLETED'].includes(s)) return 'completed';
                    if (['Ready', 'READY'].includes(s)) return 'ready';
                    if (['In Progress', 'PREPARING', 'MEMASAK'].includes(s)) return 'cooking';
                    if (['Dibatalkan', 'CANCELLED'].includes(s)) return 'cancelled';
                    return 'new';
                },
                getDineInItems(order) {
                    if (!order || !Array.isArray(order.items)) return [];
                    const defaultFulfillment = (order.fulfillment_type || (order.type === 'Takeaway' ? 'TAKEAWAY' : (order.type === 'Delivery' ? 'DELIVERY' : 'DINE_IN'))).toUpperCase();
                    return order.items.filter(i => {
                        const itemType = (i.serving_type || defaultFulfillment).toUpperCase();
                        return itemType === 'DINE_IN' || (itemType !== 'TAKEAWAY' && itemType !== 'DELIVERY');
                    });
                },
                getTakeawayItems(order) {
                    if (!order || !Array.isArray(order.items)) return [];
                    const defaultFulfillment = (order.fulfillment_type || (order.type === 'Takeaway' ? 'TAKEAWAY' : (order.type === 'Delivery' ? 'DELIVERY' : 'DINE_IN'))).toUpperCase();
                    return order.items.filter(i => {
                        const itemType = (i.serving_type || defaultFulfillment).toUpperCase();
                        return itemType === 'TAKEAWAY';
                    });
                },
                getDeliveryItems(order) {
                    if (!order || !Array.isArray(order.items)) return [];
                    const defaultFulfillment = (order.fulfillment_type || (order.type === 'Takeaway' ? 'TAKEAWAY' : (order.type === 'Delivery' ? 'DELIVERY' : 'DINE_IN'))).toUpperCase();
                    return order.items.filter(i => {
                        const itemType = (i.serving_type || defaultFulfillment).toUpperCase();
                        return itemType === 'DELIVERY';
                    });
                },
                get ordersByPrimaryTab() {
                    if (this.activeOrderFilter === 'open_bill') {
                        return this.orders.filter(o => this.isOpenBillOrder(o));
                    }
                    if (this.activeOrderFilter === 'paid' || this.activeOrderFilter === 'completed') {
                        return this.orders.filter(o => this.isPaidOrder(o));
                    }
                    return this.orders;
                },
                get filteredOrders() {
                    let filtered = this.ordersByPrimaryTab;

                    // 2. Secondary Sub-Status Filter: 'all', 'new', 'cooking', 'ready', 'completed'
                    if (this.subOrderStatusFilter && this.subOrderStatusFilter !== 'all') {
                        filtered = filtered.filter(o => this.getOrderSubStatus(o) === this.subOrderStatusFilter);
                    }

                    // 3. Fulfillment Type Dropdown Filter: 'all', 'DINE_IN', 'TAKEAWAY', 'DELIVERY'
                    if (this.orderFulfillmentFilter && this.orderFulfillmentFilter !== 'all') {
                        const targetFulfillment = this.orderFulfillmentFilter.toUpperCase();
                        filtered = filtered.filter(o => {
                            const fType = (o.fulfillment_type || (o.type === 'Dine In' ? 'DINE_IN' : (o.type === 'Takeaway' ? 'TAKEAWAY' : 'DELIVERY'))).toUpperCase();
                            return fType === targetFulfillment;
                        });
                    }
                    
                    // 4. Search Query Filter
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(o => 
                            (o.customer || '').toLowerCase().includes(q) || 
                            (o.raw_customer_name || '').toLowerCase().includes(q) ||
                            (o.id || '').toString().includes(q) ||
                            (o.table && o.table.toLowerCase().includes(q))
                        );
                    }
                    return filtered;
                },
                tabs: window.INITIAL_DATA.pages || [],
                navigateToPage(id) {
                    const destination = this.tabs.find(tab => tab.id === id);
                    if (destination) window.location.assign(destination.url);
                },
                                tables: [],
                getQRUrl(tableCode, token = '') {
                    const slug = window.INITIAL_DATA.shop?.slug || 'menu';
                    const baseUrl = window.location.origin + '/' + slug;
                    const url = `${baseUrl}?table=${tableCode}${token ? '&token='+token : ''}`;
                    return `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(url)}`;
                },
                get totalRevenue() {
                    return this.orders.filter(o => (o.status === 'Completed' || o.order_status === 'COMPLETED' || o.status === 'COMPLETED')).reduce((sum, o) => sum + (o.total || 0), 0);
                },
                get totalOrders() {
                    return this.orders.length;
                },
                get totalSuccess() {
                    return this.orders.filter(o => (o.status === 'Completed' || o.order_status === 'COMPLETED' || o.status === 'COMPLETED')).length;
                },
                get successRate() {
                    if (this.orders.length === 0) return 100;
                    return Math.round((this.totalSuccess / this.orders.length) * 100);
                },
                get topMenuItems() {
                    const counts = {};
                    this.orders.forEach(order => {
                        (order.items || []).forEach(item => {
                            counts[item.name] = (counts[item.name] || 0) + item.qty;
                        });
                    });
                    
                    let sorted = Object.keys(counts).map(name => {
                        const menuItem = this.menuItems.find(m => m.name === name);
                        const imgSrc = menuItem ? (menuItem.img || menuItem.image) : null;
                        return { 
                            name, 
                            count: counts[name], 
                            img: imgSrc || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop' // Coffee dummy
                        };
                    }).sort((a, b) => b.count - a.count);
                    
                    // Jika data kosong, gunakan permintaan user sebagai fallback
                    if (sorted.length === 0) {
                        const fallbackNames = ['Americano', 'Vanilla Latte', 'Irish Latte'];
                        return fallbackNames.map(name => {
                            const menuItem = this.menuItems.find(m => m.name === name);
                            const imgSrc = menuItem ? (menuItem.img || menuItem.image) : null;
                            return {
                                name: name,
                                count: 0,
                                img: imgSrc || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop'
                            };
                        });
                    }
                    
                    return sorted.slice(0, 3);
                },
                formatRevenue(num) {
                    if (num >= 1000000) return 'Rp ' + (num / 1000000).toFixed(2) + 'M';
                    if (num >= 1000) return 'Rp ' + (num / 1000).toFixed(1) + 'K';
                    return 'Rp ' + num;
                },

                menuItems: [],
                formatOrderTime(timeStr) {
                    if (!timeStr) return '--:--';
                    const d = new Date(String(timeStr).replace(' ', 'T'));
                    if (isNaN(d.getTime())) return String(timeStr);
                    const now = new Date();
                    const isToday = d.getDate() === now.getDate() && d.getMonth() === now.getMonth() && d.getFullYear() === now.getFullYear();
                    const timePart = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');
                    if (isToday) return timePart;
                    const datePart = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    return `${datePart} • ${timePart}`;
                },
                formatOmzetShort(val) {
                    const num = Number(val ?? 0);
                    if (isNaN(num) || num <= 0) return 'Rp0';
                    if (num >= 1000000) {
                        return 'Rp' + (num / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 }) + ' jt';
                    }
                    return 'Rp' + num.toLocaleString('id-ID');
                },
                formatPctChange(val) {
                    const num = Number(val ?? 0);
                    if (isNaN(num) || num === 0) return '0%';
                    const prefix = num > 0 ? '+' : '−';
                    return prefix + Math.abs(num).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + '%';
                },
                getLiveKitchenQueue() {
                    const live = (this.orders || []).filter(o => {
                        const s = (o.status || o.order_status || '').toLowerCase();
                        return ['masuk', 'pending', 'confirmed', 'in progress', 'preparing', 'memasak'].includes(s);
                    });

                    return live.map((o) => {
                        const items = Array.isArray(o.items) ? o.items : [];
                        const totalQty = items.reduce((acc, i) => acc + (Number(i.qty || i.quantity) || 1), 0);
                        const itemNames = items.slice(0, 2).map(i => `${i.name || 'Menu'} ×${i.qty || i.quantity || 1}`).join(', ');
                        const extraCount = items.length > 2 ? items.length - 2 : 0;
                        const rawStatus = (o.status || o.order_status || 'Masuk').toLowerCase();
                        let statusLabel = 'Memasak';
                        let progressPct = 70;
                        if (['masuk', 'pending'].includes(rawStatus)) {
                            statusLabel = 'Pesanan Baru';
                            progressPct = 30;
                        } else if (rawStatus === 'confirmed') {
                            statusLabel = 'Dikonfirmasi';
                            progressPct = 55;
                        }

                        const rawTable = String(o.table || '').trim();
                        const isTakeaway = !rawTable || rawTable.toUpperCase() === 'TA' || rawTable.toLowerCase().includes('takeaway');
                        const badgeText = isTakeaway ? 'TA' : rawTable.replace(/^meja\s*/i, '').padStart(2, '0');
                        const titleText = isTakeaway
                            ? (o.customer && !o.customer.toLowerCase().includes('meja') ? `${o.customer} (Takeaway)` : 'Takeaway')
                            : (rawTable.toLowerCase().includes('meja') ? rawTable : `Meja ${rawTable}`);

                        let displayTime = '—';
                        if (o.time) {
                            const parsed = new Date(String(o.time).replace(' ', 'T'));
                            if (!isNaN(parsed.getTime())) {
                                displayTime = parsed.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                            }
                        }

                        return {
                            id: o.id,
                            rawOrder: o,
                            badgeText: badgeText,
                            titleText: titleText,
                            status: statusLabel,
                            progressPct: progressPct,
                            total: Number(o.total || 0),
                            items: items,
                            totalQty: totalQty,
                            itemsText: itemNames ? (itemNames + (extraCount > 0 ? ', ' : '')) : 'Tidak ada rincian item',
                            extraItemText: extraCount > 0 ? `+${extraCount} menu lainnya` : '',
                            displayTime: displayTime
                        };
                    });
                },
                openOrderDetail(item) {
                    if (!item) return;
                    const target = item.rawOrder || (this.orders || []).find(o => o.id === item.id) || item;
                    this.viewOrderDetails(target);
                },
                async markOrderReady(order) {
                    if (!order || !order.id) return;
                    await this.updateStatus(order.id, 'Ready');
                    this.initAnalytics();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pesanan Siap Disajikan!',
                            text: `Pesanan ${order.titleText || ''} telah siap disajikan kepada pelanggan.`,
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                },
                getTopProductsList() {
                    const period = this.topProductsFilter || 'today';
                    const periodList = this.analytics?.topProductsByPeriod?.[period];
                    let sourceList = Array.isArray(periodList)
                        ? periodList
                        : (period === 'today' && Array.isArray(this.analytics?.topProducts) ? this.analytics.topProducts : []);

                    if ((!sourceList || sourceList.length === 0) && Array.isArray(this.orders) && this.orders.length > 0) {
                        sourceList = this.calculateTopProductsFromOrders(period);
                    }

                    if (Array.isArray(sourceList) && sourceList.length > 0) {
                        return sourceList.map((p, idx) => ({
                            rank: p.rank || (idx + 1),
                            name: p.name || 'Produk',
                            sold: Number(p.sold || 0),
                            revenue: Number(p.revenue || 0),
                            image: p.image || null
                        }));
                    }
                    return [];
                },
                calculateTopProductsFromOrders(period) {
                    if (!Array.isArray(this.orders) || this.orders.length === 0) return [];

                    const now = new Date();
                    const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

                    // Start of week (Monday)
                    const day = now.getDay();
                    const diffToMonday = (day === 0 ? -6 : 1) - day;
                    const startOfWeek = new Date(now);
                    startOfWeek.setDate(now.getDate() + diffToMonday);
                    startOfWeek.setHours(0, 0, 0, 0);

                    // Start of month
                    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1, 0, 0, 0, 0);

                    const validOrders = this.orders.filter(o => {
                        const status = String(o.status || o.order_status || '').toUpperCase();
                        if (['DIBATALKAN', 'CANCELLED'].includes(status)) return false;

                        const rawTime = o.time || o.created_at;
                        if (!rawTime) return false;
                        const orderDate = new Date(rawTime);
                        if (isNaN(orderDate.getTime())) return false;

                        if (period === 'today') {
                            const oDateStr = orderDate.getFullYear() + '-' + String(orderDate.getMonth() + 1).padStart(2, '0') + '-' + String(orderDate.getDate()).padStart(2, '0');
                            return oDateStr === todayStr;
                        } else if (period === 'week') {
                            return orderDate >= startOfWeek;
                        } else if (period === 'month') {
                            return orderDate >= startOfMonth;
                        }
                        return true;
                    });

                    const productMap = {};
                    validOrders.forEach(o => {
                        (o.items || []).forEach(item => {
                            const name = item.name || 'Produk';
                            const qty = parseInt(item.qty || 1);
                            const price = parseFloat(item.price || 0);
                            if (!productMap[name]) {
                                productMap[name] = {
                                    name: name,
                                    sold: 0,
                                    revenue: 0,
                                    image: item.image || null
                                };
                            }
                            productMap[name].sold += qty;
                            productMap[name].revenue += (price * qty);
                            if (!productMap[name].image && item.image) {
                                productMap[name].image = item.image;
                            }
                        });
                    });

                    return Object.values(productMap)
                        .sort((a, b) => b.sold - a.sold)
                        .slice(0, 5)
                        .map((p, idx) => ({ ...p, rank: idx + 1 }));
                },
                getBusyHoursBars() {
                    if (Array.isArray(this.analytics?.busyHours?.bars) && this.analytics.busyHours.bars.length > 0) {
                        return this.analytics.busyHours.bars;
                    }
                    const emptyBars = [];
                    for (let h = 9; h <= 20; h++) {
                        emptyBars.push({
                            label: String(h).padStart(2, '0') + ':00',
                            visitors: 0,
                            height: 6,
                            isPeak: false
                        });
                    }
                    return emptyBars;
                },
                getTrendPoints() {
                    if (Array.isArray(this.analytics?.monthlyTrend?.points) && this.analytics.monthlyTrend.points.length > 0) {
                        return this.analytics.monthlyTrend.points;
                    }
                    const defaultMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
                    return defaultMonths.map((label, idx) => ({
                        label,
                        revenue: 0,
                        x: idx * 100,
                        y: 170
                    }));
                },
                getTrendLinePath() {
                    const pts = this.getTrendPoints();
                    if (!pts.length) return 'M 0 170 L 500 170';
                    return pts.map((p, idx) => `${idx === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
                },
                getTrendAreaPath() {
                    const pts = this.getTrendPoints();
                    if (!pts.length) return 'M 0 170 L 500 170 L 500 180 L 0 180 Z';
                    const line = pts.map((p, idx) => `${idx === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
                    const lastX = pts[pts.length - 1].x;
                    return `${line} L ${lastX} 180 L 0 180 Z`;
                },

                init() {
                    localStorage.removeItem('activeDashboardTab');
                    const unlockAudio = () => {
                        const chime = document.getElementById('chime-sound');
                        if (chime) {
                            chime.volume = 0;
                            chime.play().then(() => {
                                chime.pause();
                                chime.currentTime = 0;
                                chime.volume = 1;
                            }).catch(() => {});
                        }
                        document.removeEventListener('click', unlockAudio);
                    };
                    document.addEventListener('click', unlockAudio);
                    if (this.currentTab === 'logs') this.fetchLogs(1);
                    if (this.currentTab === 'shifts') this.fetchShifts();
                    if (['menu', 'qr'].includes(this.currentTab)) this.initSummary();
                    if (this.currentTab === 'dashboard') this.initAnalytics();
                    if (['menu', 'inventory'].includes(this.currentTab)) this.loadMenu();
                    if (['menu', 'inventory'].includes(this.currentTab)) this.fetchInventory();
                    this.loadVouchers();
                    this.loadAddons();
                    if (window.INITIAL_DATA.shop) {
                        this.settings.name = window.INITIAL_DATA.shop.name || '';
                        this.settings.slug = window.INITIAL_DATA.shop.slug || '';
                        this.settings.primary_color = window.OQARI_THEME.primary;
                        if (window.INITIAL_DATA.shop.logo_url) {
                            this.settings.logoPreview = window.INITIAL_DATA.shop.logo_url + '?v=' + new Date(window.INITIAL_DATA.shop.updated_at).getTime();
                        }
                    }
                    
                    this.originalOperatingHours = JSON.parse(JSON.stringify(this.operatingHours));
                    this.settings.operating_hours = this.operatingHours;
                    if (['dashboard', 'orders'].includes(this.currentTab)) this.fetchLiveOrders(true);
                    window.addEventListener('keydown', (e) => {
                        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                            e.preventDefault();
                            const searchInput = document.getElementById('global-search-input');
                            if (searchInput) {
                                searchInput.focus();
                                searchInput.select();
                            }
                        }
                    });

                    
                    // The global notification engine (orders.js) handles Reverb connection 
                    // and dispatches the 'new-order-received' window event.
                    
                    // Fallback polling just in case WebSocket fails
                    setInterval(() => {
                        if (['dashboard', 'orders'].includes(this.currentTab)) this.fetchLiveOrders(false);
                    }, 5000);


                },
                async loadMenu(page = 1) {
    if (!this.can('manage-menu')) { this.isLoadingMenus = false; return; }
                    this.isLoadingMenus = true;
                    try {
                        const res = await fetch('/admin/api/dashboard/products?per_page=50&page=' + page);
                        if (res.ok) {
                            const data = await res.json();
                            this.menuItems = data.data.map(m => {
                                let catId = m.category_id || (m.category ? m.category.id : null);
                                let catName = m.category ? m.category.name : (m.category_name || '');
                                if (!catId && catName && this.categories.length > 0) {
                                    const found = this.categories.find(c => c.name.toLowerCase() === catName.toLowerCase());
                                    if (found) catId = found.id;
                                }
                                return {
                                    ...m,
                                    categoryId: catId || m.category_name || '',
                                    categoryName: catName,
                                    desc: m.description,
                                    image: m.image_url ? (m.image_url + '?v=' + new Date(m.updated_at).getTime()) : null,
                                    tags: m.tags || []
                                };
                            });
                        }
                    } catch(e) {}
                    this.isLoadingMenus = false;
                },
                editMenu(item) {
                    let matchedCatId = item.categoryId || item.category_id || '';
                    if (matchedCatId && this.categories.length > 0 && !this.categories.some(c => c.id == matchedCatId)) {
                        const found = this.categories.find(c => c.name && c.name.toLowerCase() === String(matchedCatId).toLowerCase());
                        if (found) matchedCatId = found.id;
                    }

                    let discPercent = null;
                    const basePrice = Number(item.price) || 0;
                    const discPrice = item.discount_price !== undefined && item.discount_price !== null ? item.discount_price : null;
                    if (basePrice > 0 && discPrice !== null && Number(discPrice) < basePrice) {
                        discPercent = Math.round(((basePrice - Number(discPrice)) / basePrice) * 100);
                    }

                    const attachedGroupIds = (item.modifier_groups || item.modifierGroups || []).map(g => g.id);
                    const hasAddons = attachedGroupIds.length > 0;

                    this.draftMenus = [{
                        id: item.id,
                        name: item.name,
                        price: item.price,
                        discount_price: discPrice,
                        discount_type: 'nominal',
                        discount_percent: discPercent,
                        is_discount_active: Boolean(item.is_discount_active),
                        categoryId: matchedCatId || (this.categories.length > 0 ? this.categories[0].id : ''),
                        desc: item.desc || item.description || '',
                        imagePreview: item.image || item.image_url || null,
                        imageFile: null,
                        has_addons: hasAddons,
                        modifier_group_ids: attachedGroupIds
                    }];
                    this.showBulkUpload = true;
                },
                async deleteMenu(id) {
                    const result = await Swal.fire({
                        title: 'Hapus Menu?',
                        text: "Data menu ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    });
                    
                    if (result.isConfirmed) {
                        fetch('/admin/api/menu/' + id, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).then(res => res.json()).then(data => {
                            if (data.success) {
                                this.menuItems = this.menuItems.filter(m => m.id !== id);
                                this.addToast('Menu berhasil dihapus', 'success');
                            } else {
                                this.addToast(data.message || 'Gagal menghapus menu', 'error');
                            }
                        }).catch(err => {
                            this.addToast('Terjadi kesalahan jaringan', 'error');
                        });
                    }
                },
                saveNewMenu() {
                    if(!this.newMenu.name || !this.newMenu.price || !this.newMenu.categoryId) {
                        this.addToast('Nama, Harga, dan Kategori wajib diisi!', 'error');
                        return;
                    }
                    
                    let formData = new FormData();
                    if (this.newMenu.id) formData.append('id', this.newMenu.id);
                    formData.append('name', this.newMenu.name);
                    formData.append('price', this.newMenu.price);
                    formData.append('categoryId', this.newMenu.categoryId);
                    formData.append('desc', this.newMenu.desc || '');
                    formData.append('cogs', this.calculatedMenuHpp || 0);
                    formData.append('recipes', JSON.stringify(this.newMenu.recipes || []));
                    if (this.$refs.menuImageInput && this.$refs.menuImageInput.files[0]) {
                        formData.append('image', this.$refs.menuImageInput.files[0]);
                    }
                    
                    fetch('/admin/api/menu', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            if (this.newMenu.id) {
                                const item = this.menuItems.find(m => m.id === this.newMenu.id);
                                if (item) {
                                    item.name = this.newMenu.name;
                                    item.price = parseInt(this.newMenu.price);
                                    item.desc = this.newMenu.desc;
                                    item.categoryId = this.newMenu.categoryId;
                                    if (data.menu.image_url) {
                                        item.image = data.menu.image_url + '?v=' + Date.now();
                                    }
                                }
                            } else {
                                data.menu.categoryId = data.menu.category_name;
                                data.menu.desc = data.menu.description;
                                data.menu.image = data.menu.image_url ? data.menu.image_url : null;
                                this.menuItems.unshift(data.menu);
                            }
                            
                            if (data.has_duplicate_name) {
                                this.addToast('Menu disimpan. Peringatan: Nama menu ini sudah ada sebelumnya!', 'error');
                            } else {
                                this.addToast('Menu berhasil disimpan!', 'success');
                            }
                            
                            this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '', imagePreview: null };
                            if (this.$refs.menuImageInput) this.$refs.menuImageInput.value = '';
                            this.showAddMenuModal = false;
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan menu', 'error');
                        }
                    }).catch(err => {
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });
                },
                saveCategory() {
                    if (!this.newCategoryName.trim()) {
                        this.addToast('Nama kategori tidak boleh kosong', 'error');
                        return;
                    }
                    fetch('/admin/api/category', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ 
                            name: this.newCategoryName,
                            icon: this.newCategoryIcon || 'fa-mug-hot'
                        })
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            if (!this.categories.some(c => c.id === data.category.id)) {
                                this.categories.push(data.category);
                            } else {
                                const idx = this.categories.findIndex(c => c.id === data.category.id);
                                if (idx !== -1) {
                                    this.categories[idx] = Object.assign({}, this.categories[idx], data.category);
                                }
                            }
                            this.newCategoryName = '';
                            this.newCategoryIcon = 'fa-mug-hot';
                            this.showAddCategoryModal = false;
                            this.addToast(data.already_exists ? 'Kategori sudah tersedia!' : 'Kategori berhasil ditambahkan!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal menambahkan kategori', 'error');
                        }
                    }).catch(err => {
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });
                },
                async createQuickCategory(name, draftTarget = null) {
                    const trimmed = (name || '').trim();
                    if (!trimmed) {
                        this.addToast('Nama kategori tidak boleh kosong', 'error');
                        return null;
                    }

                    // Cek apakah sudah ada di categories lokal
                    const existing = this.categories.find(c => c.name && c.name.toLowerCase() === trimmed.toLowerCase());
                    if (existing) {
                        if (draftTarget) {
                            draftTarget.categoryId = existing.id;
                        }
                        this.addToast(`Kategori "${existing.name}" sudah ada dan dipilih`, 'info');
                        return existing;
                    }

                    try {
                        const res = await fetch('/admin/api/category', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: trimmed })
                        });
                        const data = await res.json();
                        if (data.success && data.category) {
                            if (!this.categories.some(c => c.id === data.category.id)) {
                                this.categories.push(data.category);
                            }
                            if (draftTarget) {
                                draftTarget.categoryId = data.category.id;
                            }
                            this.addToast(`Kategori "${data.category.name}" ${data.already_exists ? 'sudah tersedia dan dipilih' : 'berhasil ditambahkan!'}`, 'success');
                            return data.category;
                        } else {
                            this.addToast(data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Gagal menambahkan kategori'), 'error');
                            return null;
                        }
                    } catch (err) {
                        console.error('Error creating quick category:', err);
                        this.addToast('Terjadi kesalahan jaringan saat menambahkan kategori', 'error');
                        return null;
                    }
                },
                startEditCategory(cat) {
                    this.editingCategoryId = cat.id;
                    this.editingCategoryName = cat.name;
                    this.editingCategoryIcon = cat.icon || 'fa-mug-hot';
                },
                cancelEditCategory() {
                    this.editingCategoryId = null;
                    this.editingCategoryName = '';
                    this.editingCategoryIcon = 'fa-mug-hot';
                },
                async updateCategory(catId) {
                    const trimmed = (this.editingCategoryName || '').trim();
                    if (!trimmed) {
                        this.addToast('Nama kategori tidak boleh kosong', 'error');
                        return;
                    }
                    this.isSavingCategoryAction = true;
                    try {
                        const res = await fetch(`/admin/api/category/${catId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ 
                                name: trimmed,
                                icon: this.editingCategoryIcon || 'fa-mug-hot'
                            })
                        });
                        const data = await res.json();
                        this.isSavingCategoryAction = false;
                        if (data.success && data.category) {
                            const idx = this.categories.findIndex(c => c.id === catId);
                            if (idx !== -1) {
                                this.categories[idx] = Object.assign({}, this.categories[idx], data.category);
                            }
                            // Update menuItems matching this category
                            this.menuItems.forEach(item => {
                                if (item.categoryId == catId || item.category_id == catId) {
                                    item.categoryName = data.category.name;
                                    item.category_name = data.category.name;
                                }
                            });
                            this.cancelEditCategory();
                            this.addToast(data.message || 'Kategori berhasil diperbarui!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal memperbarui kategori', 'error');
                        }
                    } catch (err) {
                        this.isSavingCategoryAction = false;
                        console.error('Update category error:', err);
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    }
                },
                async deleteCategory(catId, catName) {
                    const result = await Swal.fire({
                        title: 'Hapus Kategori?',
                        html: `Apakah Anda yakin ingin menghapus kategori <strong>"${catName}"</strong>?<br><span class="text-xs text-gray-500">Menu di dalam kategori ini akan dialihkan ke kategori lain dengan aman.</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#df5826',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    });

                    if (!result.isConfirmed) return;

                    this.isSavingCategoryAction = true;
                    try {
                        const res = await fetch(`/admin/api/category/${catId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        this.isSavingCategoryAction = false;
                        if (data.success) {
                            this.categories = this.categories.filter(c => c.id !== catId);
                            
                            // Reassign menuItems
                            const fallbackId = data.fallback_category_id;
                            const fallbackName = data.fallback_category_name || 'Uncategorized';
                            this.menuItems.forEach(item => {
                                if (item.categoryId == catId || item.category_id == catId) {
                                    item.categoryId = fallbackId;
                                    item.category_id = fallbackId;
                                    item.categoryName = fallbackName;
                                    item.category_name = fallbackName;
                                }
                            });

                            if (this.activeMenuFilter == catId) {
                                this.setMenuFilter('all');
                            }

                            this.addToast(data.message || 'Kategori berhasil dihapus!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal menghapus kategori', 'error');
                        }
                    } catch (err) {
                        this.isSavingCategoryAction = false;
                        console.error('Delete category error:', err);
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    }
                },
                viewOrderDetails(order) {
                    this.selectedOrder = order;
                    this.showOrderDetailModal = true;
                },

                openCreateOpenBillModal() {
                    this.openBillModal.recalledOrderId = null;
                    this.openBillModal.customerName = '';
                    this.openBillModal.selectedTable = this.tables.length > 0 ? (this.tables[0].name || this.tables[0].id) : '';
                    this.openBillModal.fulfillmentType = 'DINE_IN';
                    this.openBillModal.searchQuery = '';
                    this.openBillModal.selectedCategory = 'all';
                    this.openBillModal.cart = [];
                    this.openBillModal.show = true;
                    if (!this.openBillModal.products || this.openBillModal.products.length === 0) {
                        this.fetchOpenBillProducts();
                    }
                },

                openEditOpenBillModal(order) {
                    this.openBillModal.recalledOrderId = order.id;
                    this.openBillModal.customerName = order.raw_customer_name || (order.customer && !order.customer.startsWith('Meja ') && order.customer !== 'Walk-in Customer' ? order.customer : '');
                    this.openBillModal.selectedTable = order.raw_table || (order.table && order.table !== 'TA' && order.table !== '-' ? order.table : (this.tables.length > 0 ? (this.tables[0].name || this.tables[0].id) : ''));
                    this.openBillModal.fulfillmentType = order.fulfillment_type || (order.type === 'Takeaway' ? 'TAKEAWAY' : 'DINE_IN');
                    this.openBillModal.searchQuery = '';
                    this.openBillModal.selectedCategory = 'all';
                    this.openBillModal.cart = (order.items || []).map(i => ({
                        id: i.id || i.product_id || (i.product ? i.product.id : null),
                        order_item_id: i.order_item_id || i.id,
                        name: i.name || 'Produk',
                        price: parseFloat(i.price || 0),
                        qty: parseInt(i.qty || 1),
                        notes: i.notes || '',
                        showNotes: Boolean(i.notes),
                        serving_type: i.serving_type || (order.fulfillment_type === 'TAKEAWAY' ? 'TAKEAWAY' : 'DINE_IN'),
                        variant_id: i.variant_id || null,
                        variant_name: i.variant_name || null,
                        modifiers: (i.modifiers || []).map(m => typeof m === 'object' ? m.id : m),
                        image: i.image || null,
                    }));
                    this.openBillModal.show = true;
                    if (!this.openBillModal.products || this.openBillModal.products.length === 0) {
                        this.fetchOpenBillProducts();
                    }
                },

                closeOpenBillModal() {
                    this.openBillModal.show = false;
                    this.openBillModal.modifierModal.show = false;
                },

                async fetchOpenBillProducts() {
                    this.openBillModal.isLoading = true;
                    try {
                        const res = await fetch('/admin/pos/products');
                        if (res.ok) {
                            const data = await res.json();
                            this.openBillModal.products = data;
                            const catMap = new Map();
                            data.forEach(p => {
                                const cId = p.category_id;
                                const cName = p.category_name || p.category?.name || 'Lainnya';
                                if (cId && !catMap.has(cId)) {
                                    catMap.set(cId, cName);
                                }
                            });
                            this.openBillModal.categories = Array.from(catMap.entries()).map(([id, name]) => ({ id, name }));
                        }
                    } catch(err) {
                        console.error('fetchOpenBillProducts error:', err);
                    } finally {
                        this.openBillModal.isLoading = false;
                    }
                },

                filteredOpenBillProducts() {
                    const q = (this.openBillModal.searchQuery || '').toLowerCase().trim();
                    const cat = this.openBillModal.selectedCategory;
                    return (this.openBillModal.products || []).filter(p => {
                        const matchesQuery = !q || (p.name && p.name.toLowerCase().includes(q)) || (p.category_name && p.category_name.toLowerCase().includes(q));
                        if (!matchesQuery) return false;
                        if (cat === 'all') return true;
                        if (cat === 'best_seller') return Boolean(p.is_best_seller) || (p.sold_count > 0);
                        return String(p.category_id) === String(cat);
                    });
                },

                addToOpenBillCart(product, servingType = 'DINE_IN') {
                    const effectiveServingType = (this.openBillModal.fulfillmentType === 'DELIVERY') ? 'DELIVERY' : (servingType || 'DINE_IN');
                    if ((product.variants && product.variants.length > 0) || (product.modifier_groups && product.modifier_groups.length > 0)) {
                        this.openOpenBillModifierModal(product, effectiveServingType);
                        return;
                    }

                    const existing = this.openBillModal.cart.find(i => i.id === product.id && !i.variant_id && (!i.modifiers || i.modifiers.length === 0) && (i.serving_type || 'DINE_IN') === effectiveServingType);
                    if (existing) {
                        existing.qty = (parseInt(existing.qty) || 1) + 1;
                    } else {
                        this.openBillModal.cart.push({
                            id: product.id,
                            name: product.name,
                            price: Number(product.effective_price || product.price || 0),
                            qty: 1,
                            variant_id: null,
                            variant_name: null,
                            modifiers: [],
                            notes: '',
                            showNotes: false,
                            serving_type: effectiveServingType,
                            image: product.image_url || null,
                        });
                    }
                },

                switchOpenBillFulfillment(type) {
                    if (this.openBillModal.fulfillmentType === type) return;

                    if (type === 'DELIVERY') {
                        const hasNonDelivery = this.openBillModal.cart.some(item => (item.serving_type || 'DINE_IN') !== 'DELIVERY');
                        if (this.openBillModal.cart.length > 0 && hasNonDelivery) {
                            if (confirm('Ubah semua ' + this.openBillModal.cart.length + ' item di keranjang menjadi tipe Delivery?')) {
                                this.openBillModal.fulfillmentType = 'DELIVERY';
                                this.openBillModal.cart.forEach(i => i.serving_type = 'DELIVERY');
                            }
                            return;
                        }
                        this.openBillModal.fulfillmentType = 'DELIVERY';
                        return;
                    }

                    if (type === 'DINE_IN') {
                        const hasDelivery = this.openBillModal.cart.some(item => item.serving_type === 'DELIVERY');
                        if (this.openBillModal.cart.length > 0 && hasDelivery) {
                            if (confirm('Ubah semua ' + this.openBillModal.cart.length + ' item di keranjang menjadi tipe Direct Order (Dine In)?')) {
                                this.openBillModal.fulfillmentType = 'DINE_IN';
                                this.openBillModal.cart.forEach(i => i.serving_type = 'DINE_IN');
                            }
                            return;
                        }
                        this.openBillModal.fulfillmentType = 'DINE_IN';
                    }
                },

                openOpenBillModifierModal(product, servingType = 'DINE_IN') {
                    this.openBillModal.modifierModal = {
                        show: true,
                        product: product,
                        selectedVariant: (product.variants && product.variants.length > 0) ? product.variants[0] : null,
                        selectedModifiers: [],
                        notes: '',
                        qty: 1,
                        servingType: servingType,
                    };
                },

                toggleOpenBillModifier(modifier) {
                    const list = this.openBillModal.modifierModal.selectedModifiers;
                    const idx = list.findIndex(m => m.id === modifier.id);
                    if (idx > -1) {
                        list.splice(idx, 1);
                    } else {
                        list.push(modifier);
                    }
                },

                toggleOpenBillItemServingType(idx) {
                    if (!this.openBillModal.cart[idx] || this.openBillModal.fulfillmentType === 'DELIVERY') return;
                    const cur = this.openBillModal.cart[idx].serving_type || 'DINE_IN';
                    this.openBillModal.cart[idx].serving_type = (cur === 'TAKEAWAY') ? 'DINE_IN' : 'TAKEAWAY';
                },

                splitOpenBillItem(idx) {
                    const item = this.openBillModal.cart[idx];
                    if (!item || item.qty <= 1 || this.openBillModal.cart.length >= 50) return;
                    item.qty--;
                    this.openBillModal.cart.splice(idx + 1, 0, {
                        ...item,
                        modifiers: [...(item.modifiers || [])],
                        qty: 1,
                        showNotes: true
                    });
                },

                saveOpenBillModifier() {
                    const mod = this.openBillModal.modifierModal;
                    const p = mod.product;
                    if (!p) return;

                    let unitPrice = Number(p.effective_price || p.price || 0);
                    if (mod.selectedVariant) {
                        unitPrice += Number(mod.selectedVariant.price_adjustment || 0);
                    }
                    mod.selectedModifiers.forEach(m => {
                        unitPrice += Number(m.price_adjustment || 0);
                    });

                    this.openBillModal.cart.push({
                        id: p.id,
                        name: p.name,
                        price: unitPrice,
                        qty: mod.qty || 1,
                        variant_id: mod.selectedVariant ? mod.selectedVariant.id : null,
                        variant_name: mod.selectedVariant ? mod.selectedVariant.name : null,
                        modifiers: [...mod.selectedModifiers.map(m => m.id)],
                        modifier_names: mod.selectedModifiers.map(m => m.name),
                        notes: (mod.notes || '').trim(),
                        showNotes: Boolean(mod.notes),
                        serving_type: mod.servingType || 'DINE_IN',
                        image: p.image_url || null,
                    });

                    this.openBillModal.modifierModal.show = false;
                },

                openBillCartTotal() {
                    return (this.openBillModal.cart || []).reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.qty || 1)), 0);
                },

                async submitOpenBill() {
                    if (this.openBillModal.cart.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Keranjang Kosong',
                            text: 'Silakan pilih minimal 1 menu untuk pesanan open bill.',
                            confirmButtonColor: '#df5826'
                        });
                        return;
                    }

                    if (this.openBillModal.fulfillmentType === 'DINE_IN' && !this.openBillModal.selectedTable && !this.openBillModal.customerName) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tentukan Meja / Pelanggan',
                            text: 'Harap tentukan nomor meja atau nama pelanggan untuk pesanan Dine-in.',
                            confirmButtonColor: '#df5826'
                        });
                        return;
                    }

                    this.openBillModal.isSaving = true;

                    const payload = {
                        items: this.openBillModal.cart.map(i => ({
                            id: i.id,
                            qty: parseInt(i.qty || 1),
                            variant_id: i.variant_id || null,
                            modifiers: i.modifiers || [],
                            notes: (i.notes || '').trim() || null,
                            serving_type: i.serving_type || 'DINE_IN',
                        })),
                        table: this.openBillModal.fulfillmentType === 'DINE_IN' ? (this.openBillModal.selectedTable || null) : null,
                        fulfillment_type: this.openBillModal.fulfillmentType || 'DINE_IN',
                        customer_name: (this.openBillModal.customerName || '').trim() || null,
                        recalled_order_id: this.openBillModal.recalledOrderId || null,
                    };

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        const res = await fetch('/admin/pos/orders/hold', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        const data = await res.json();

                        if (res.ok && data.success) {
                            this.openBillModal.show = false;
                            await this.fetchLiveOrders(false);

                            Swal.fire({
                                icon: 'success',
                                title: data.updated ? 'Open Bill Diperbarui!' : 'Open Bill Tersimpan!',
                                html: `<div class="text-[13px] text-gray-600 mt-1">
                                        <p>Pesanan <strong>#${data.order_id}</strong> (${data.table}) berhasil disimpan.</p>
                                        <p class="mt-2 text-[18px] font-bold text-[#df5826]">Rp ${Number(data.total).toLocaleString('id-ID')}</p>
                                       </div>`,
                                showCancelButton: true,
                                confirmButtonText: '<i class="fas fa-utensils mr-1.5"></i> Cetak Tiket Dapur',
                                cancelButtonText: 'Selesai',
                                confirmButtonColor: '#059669',
                                cancelButtonColor: '#6b7280',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.open(`/admin/api/orders/${data.order_id}/print?type=kitchen`, '_blank');
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan Open Bill',
                                text: data.message || 'Terjadi kesalahan saat memproses open bill.',
                                confirmButtonColor: '#df5826'
                            });
                        }
                    } catch (e) {
                        console.error('submitOpenBill error:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal menghubungi server.',
                            confirmButtonColor: '#df5826'
                        });
                    } finally {
                        this.openBillModal.isSaving = false;
                    }
                },
                updateStatus(id, newStatus) {
                    return fetch(`/admin/api/orders/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    }).then(res => {
                        if (!res.ok) throw new Error('Status update failed');
                        return res.json();
                    }).then(() => {
                        const order = this.orders.find(o => o.id === id);
                        if (order) order.status = newStatus;
                    });
                },
                logout() {
                    // Redirect to actual laravel logout
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => window.location.href = '/');
                },
                toggleSoldOut(id) {
                    fetch(`/admin/api/menu/${id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(res => res.json()).then(data => {
                        const item = this.menuItems.find(m => m.id === id);
                        if (item) {
                            item.is_sold_out = data.is_sold_out;
                            this.addToast(data.is_sold_out ? 'Menu ditandai Sold Out' : 'Menu tersedia kembali', 'success');
                        }
                    }).catch(err => {
                        this.addToast('Gagal mengubah status menu', 'error');
                    });
                },
                async toggleMenuDiscount(item) {
                    try {
                        const res = await fetch(`/admin/api/menu/${item.id}/toggle-discount`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            item.is_discount_active = data.is_discount_active;
                            item.effective_price = data.effective_price;
                            item.discount_percentage = data.discount_percentage;
                            item.has_active_discount = data.has_active_discount;
                            this.addToast(
                                data.is_discount_active ? 'Harga diskon promo diaktifkan' : 'Harga promo dinonaktifkan',
                                'success'
                            );
                        } else {
                            this.addToast(data.message || 'Gagal mengubah status promo', 'error');
                        }
                    } catch(err) {
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    }
                },
                processIncomingQueue() {
                    if (!this.activeIncomingOrder && this.incomingOrderQueue.length > 0) {
                        this.activeIncomingOrder = this.incomingOrderQueue.shift();
                        this.incomingOrderState = 'new';
                        this.incomingTimer = 30;
                        clearInterval(this.incomingTimerInterval);
                        
                        this.incomingTimerInterval = setInterval(() => {
                            if (['new', 'reject_confirm'].includes(this.incomingOrderState)) {
                                this.incomingTimer--;
                                if (this.incomingTimer <= 0) {
                                    this.autoCloseOrder();
                                }
                            }
                        }, 1000);
                    }
                },
                playNotification() {
                    const chime = document.getElementById('chime-sound');
                    if (chime) {
                        chime.currentTime = 0;
                        chime.loopCount = 0;
                        chime.onended = () => {
                            if (chime.loopCount < 4) {
                                chime.loopCount++;
                                chime.currentTime = 0;
                                chime.play().catch(e => console.log('Audio error', e));
                            }
                        };
                        chime.play().catch(e => {
                            console.log('Audio autoplay blocked', e);
                            if (typeof this.addToast === 'function') {
                                this.addToast('?? Pesanan Baru! (Klik layar untuk izinkan suara otomatis)', 'info');
                            }
                        });
                    }
                },
                stopNotification() {
                    try {
                        const chime = document.getElementById('chime-sound');
                        if (chime) {
                            chime.loopCount = 5;
                            const pausePromise = chime.pause();
                            if (pausePromise !== undefined && typeof pausePromise.then === 'function') {
                                pausePromise.catch(() => {});
                            }
                            chime.currentTime = 0;
                        }
                    } catch (e) {
                        console.warn('stopNotification error', e);
                    }
                },
                async acceptOrder() {
                    if (!this.activeIncomingOrder || this.incomingOrderState !== 'new') return;
                    this.stopNotification();
                    clearInterval(this.incomingTimerInterval);
                    const orderId = this.activeIncomingOrder.id;

                    this.incomingOrderState = 'accepting';
                    try {
                        await this.updateStatus(orderId, 'In Progress');
                        this.incomingOrderState = 'accepted';
                        this.addToast(`Pesanan #${orderId} diterima & sedang disiapkan!`, 'success');
                        setTimeout(() => this.closeActiveOrder(), 1200);
                    } catch (e) {
                        console.error('acceptOrder error:', e);
                        this.incomingOrderState = 'new';
                        this.addToast('Gagal memproses pesanan, silakan coba lagi', 'error');
                    }
                },
                autoCloseOrder() {
                    if (!this.activeIncomingOrder) return;
                    this.stopNotification();
                    clearInterval(this.incomingTimerInterval);
                    const orderId = this.activeIncomingOrder.id;
                    this.addToast(`Pop-up ditutup. Pesanan #${orderId} tetap tersimpan di antrean pesanan.`, 'info');
                    this.closeActiveOrder();
                },
                async confirmRejectOrder(isAuto = false) {
                    if (!this.activeIncomingOrder) return;
                    this.stopNotification();
                    clearInterval(this.incomingTimerInterval);
                    const orderId = this.activeIncomingOrder.id;

                    this.incomingOrderState = 'rejected';
                    try {
                        await this.updateStatus(orderId, 'Dibatalkan');
                        if (isAuto) {
                            this.addToast(`Pesanan #${orderId} telah kedaluwarsa.`, 'error');
                        } else {
                            this.addToast(`Pesanan #${orderId} telah ditolak.`, 'error');
                        }
                    } catch (e) {
                        console.error('rejectOrder error:', e);
                    }
                    setTimeout(() => this.closeActiveOrder(), 1200);
                },
                handleNewOrderFromSocket(newOrder) {
                    const mappedOrder = {
                        id: newOrder.id,
                        customer: newOrder.customer_name || ('Meja ' + (newOrder.table ? (newOrder.table.name || newOrder.table) : '-')),
                        table: newOrder.table ? (newOrder.table.name || newOrder.table) : 'TA',
                        type: newOrder.fulfillment_type === 'DINE_IN' ? 'Dine In' : (newOrder.fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Delivery'),
                        status: newOrder.order_status || newOrder.status,
                        total: parseFloat(newOrder.total || newOrder.grand_total || newOrder.total_price || 0),
                        time: newOrder.created_at,
                        items: (newOrder.items || []).map(i => ({
                            name: i.product_name || i.product?.name || i.name || 'Produk',
                            qty: i.quantity,
                            price: parseFloat(i.unit_price ?? i.price ?? i.product?.price ?? 0), notes: i.notes || '',
                            image: i.product?.image_url ? (i.product.image_url) : null,
                            desc: i.product?.description || ''
                        }))
                    };

                    // Add to beginning of orders
                    this.orders.unshift(mappedOrder);

                    // Add to incoming queue
                    this.playNotification();
                    this.incomingOrderQueue.push(mappedOrder);
                    this.processIncomingQueue();
                },
                closeActiveOrder() {
                    this.activeIncomingOrder = null;
                    clearInterval(this.incomingTimerInterval);
                    setTimeout(() => this.processIncomingQueue(), 400);
                },
                async initSummary() {
    this.isLoadingSummary = true;
    try {
        const res = await fetch('/admin/api/dashboard/summary');
        if (res.ok) {
            const data = await res.json();
            this.categories = data.categories || [];
            this.users = data.users || [];
            this.tables = (data.tables || []).map(t => ({
                id: t.name,
                qr: t.qr_code_url || this.getQRUrl(t.name)
            }));
        }
    } catch(e) {}
    this.isLoadingSummary = false;
},
async initAnalytics() {
    if (!this.can('view-reports')) { this.isLoadingAnalytics = false; return; }
    this.isLoadingAnalytics = true;
    try {
        const res = await fetch('/admin/api/dashboard/analytics?t=' + new Date().getTime(), {
            headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
        });
        if (res.ok) {
            this.analytics = await res.json();
        }
    } catch(e) {}
    this.isLoadingAnalytics = false;
},
async fetchLiveOrders(isInit = false) {
    if (!this.can('view-orders')) { return; }
    try {
        let sourceOrders = [];
        if (true) {
                            try {
                                const res = await fetch('/admin/api/dashboard/orders?per_page=150&t=' + new Date().getTime(), {
    headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache' }
});
if (res.ok) {
    const json = await res.json();
    sourceOrders = json.data;
                                } else {
                                    return;
                                }
                            } catch (err) {
                                return;
                            }
                        }

                        if (!Array.isArray(sourceOrders)) {
                            return;
                        }

                        const dbOrders = sourceOrders.map(o => ({
                            id: o.id,
                            customer: o.customer_name || ('Meja ' + (o.table ? o.table.name : '-')),
                            raw_customer_name: o.customer_name || '',
                            table: o.table ? o.table.name : 'TA',
                            raw_table: o.table ? o.table.name : '',
                            type: o.fulfillment_type === 'DINE_IN' ? 'Dine In' : (o.fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Delivery'),
                            fulfillment_type: o.fulfillment_type || 'DINE_IN',
                            status: o.order_status || o.status,
                            order_status: o.order_status || o.status,
                            payment_status: o.payment_status || 'UNPAID',
                            is_open_bill: (o.order_status === 'HOLD' || o.status === 'HOLD' || (o.payment_status === 'UNPAID' && !['COMPLETED', 'CANCELLED'].includes(o.order_status || o.status))),
                            total: parseFloat(o.grand_total || o.total_price),
                            payment_method: o.payment_method || 'Tunai',
                            total_cogs: parseFloat(o.total_cogs || 0),
                            time: o.created_at,
                            items: (o.items || []).map(i => ({
                                id: i.product_id || (i.product ? i.product.id : null),
                                order_item_id: i.id,
                                name: i.product_name || i.product?.name || i.name || 'Produk',
                                qty: parseInt(i.quantity || 1),
                                price: parseFloat(i.unit_price ?? i.price ?? i.product?.price ?? 0),
                                notes: i.notes || '',
                                variant_id: i.variant_id || null,
                                modifiers: (i.modifiers || []).map(m => typeof m === 'object' ? m.id : m),
                                image: i.product?.image_url ? (i.product.image_url) : null,
                                desc: i.product?.description || ''
                            })),
                            rawOrder: o
                        }));

                        const newIncomingOrders = dbOrders.filter(o => (o.status === 'Masuk' || o.order_status === 'CONFIRMED' || o.status === 'CONFIRMED') && !this.orders.find(old => old.id == o.id));

                        if (!isInit && newIncomingOrders.length > 0) {
                            this.playNotification();

                            newIncomingOrders.forEach(o => this.incomingOrderQueue.push(o));
                            this.processIncomingQueue();
                        }

                        this.orders = dbOrders;

                        if (isInit) {
                            const urlOrderParam = new URLSearchParams(window.location.search).get('order');
                            if (urlOrderParam && this.currentTab === 'orders') {
                                const target = this.orders.find(o => String(o.id) === String(urlOrderParam));
                                if (target) {
                                    this.$nextTick(() => this.viewOrderDetails(target));
                                }
                            }
                        }
                    } catch (e) {
                        console.error('fetchLiveOrders error:', e);
                    }
                },
                shifts: [],
                shiftSubTab: (new URLSearchParams(window.location.search).get('sub') === 'my') ? 'my' : 'team',
                shiftViewMode: 'matrix', // 'matrix' (default), 'timeline', 'table'
                currentMatrixMonth: '{{ now(config('attendance.timezone'))->format('Y-m') }}',
                currentMatrixMonthLabel: '{{ now(config('attendance.timezone'))->locale('id')->translatedFormat('F Y') }}',
                matrixActiveTool: 'auto', // 'auto', '1', '2', '3', '4', 'delete'
                matrixSaveStatus: 'idle', // 'idle' | 'saving' | 'saved' | 'error'
                matrixPendingSaves: 0,
                shiftTemplates: [
                    { code: '1', name: 'Shift 1', start_time: '07:00', end_time: '15:00', color: 'blue' },
                    { code: '2', name: 'Shift 2', start_time: '11:00', end_time: '19:00', color: 'emerald' },
                    { code: '3', name: 'Shift 3', start_time: '15:00', end_time: '23:00', color: 'purple' },
                    { code: '4', name: 'Shift 4', start_time: '08:00', end_time: '17:00', color: 'amber' }
                ],
                draftShiftTemplates: [],
                shiftTemplateSaving: false,
                shiftTemplateError: '',
                shiftPresets: {
                    '1': { code: '1', name: 'Shift 1', start_time: '07:00', end_time: '15:00', duration_minutes: 480, label: 'Shift 1', color: 'blue', badgeClass: 'bg-blue-600 text-white' },
                    '2': { code: '2', name: 'Shift 2', start_time: '11:00', end_time: '19:00', duration_minutes: 480, label: 'Shift 2', color: 'emerald', badgeClass: 'bg-emerald-600 text-white' },
                    '3': { code: '3', name: 'Shift 3', start_time: '15:00', end_time: '23:00', duration_minutes: 480, label: 'Shift 3', color: 'purple', badgeClass: 'bg-purple-600 text-white' },
                    '4': { code: '4', name: 'Shift 4', start_time: '08:00', end_time: '17:00', duration_minutes: 540, label: 'Shift 4', color: 'amber', badgeClass: 'bg-amber-600 text-white' }
                },
                getShiftToolClasses(code, color) {
                    const isSelected = this.matrixActiveTool === code;
                    const colorMaps = {
                        blue: isSelected ? 'bg-blue-600 text-white shadow-xs ring-2 ring-blue-300' : 'bg-blue-50 text-blue-700 hover:bg-blue-100',
                        emerald: isSelected ? 'bg-emerald-600 text-white shadow-xs ring-2 ring-emerald-300' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
                        purple: isSelected ? 'bg-purple-600 text-white shadow-xs ring-2 ring-purple-300' : 'bg-purple-50 text-purple-700 hover:bg-purple-100',
                        amber: isSelected ? 'bg-amber-600 text-white shadow-xs ring-2 ring-amber-300' : 'bg-amber-50 text-amber-700 hover:bg-amber-100',
                        rose: isSelected ? 'bg-rose-600 text-white shadow-xs ring-2 ring-rose-300' : 'bg-rose-50 text-rose-700 hover:bg-rose-100',
                        cyan: isSelected ? 'bg-cyan-600 text-white shadow-xs ring-2 ring-cyan-300' : 'bg-cyan-50 text-cyan-700 hover:bg-cyan-100',
                        indigo: isSelected ? 'bg-indigo-600 text-white shadow-xs ring-2 ring-indigo-300' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100',
                    };
                    return colorMaps[color] || (isSelected ? 'bg-blue-600 text-white shadow-xs ring-2 ring-blue-300' : 'bg-blue-50 text-blue-700 hover:bg-blue-100');
                },
                getShiftBadgeColorClasses(color) {
                    const badgeMaps = {
                        blue: 'bg-blue-600 text-white',
                        emerald: 'bg-emerald-600 text-white',
                        purple: 'bg-purple-600 text-white',
                        amber: 'bg-amber-600 text-white',
                        rose: 'bg-rose-600 text-white',
                        cyan: 'bg-cyan-600 text-white',
                        indigo: 'bg-indigo-600 text-white',
                    };
                    return badgeMaps[color] || 'bg-blue-600 text-white';
                },
                getShiftTextColorClass(color) {
                    const textMaps = {
                        blue: 'text-blue-600',
                        emerald: 'text-emerald-600',
                        purple: 'text-purple-600',
                        amber: 'text-amber-600',
                        rose: 'text-rose-600',
                        cyan: 'text-cyan-600',
                        indigo: 'text-indigo-600',
                    };
                    return textMaps[color] || 'text-blue-600';
                },
                getShiftBadgeClassByCode(code) {
                    const preset = this.shiftPresets[code];
                    const color = preset ? preset.color : 'blue';
                    return this.getShiftBadgeColorClasses(color);
                },
                getMatrixCellClass(code) {
                    const preset = this.shiftPresets[code];
                    const color = preset ? preset.color : 'blue';
                    const cellMaps = {
                        blue: 'bg-blue-600 text-white hover:bg-blue-700',
                        emerald: 'bg-emerald-600 text-white hover:bg-emerald-700',
                        purple: 'bg-purple-600 text-white hover:bg-purple-700',
                        amber: 'bg-amber-600 text-white hover:bg-amber-700',
                        rose: 'bg-rose-600 text-white hover:bg-rose-700',
                        cyan: 'bg-cyan-600 text-white hover:bg-cyan-700',
                        indigo: 'bg-indigo-600 text-white hover:bg-indigo-700',
                    };
                    return cellMaps[color] || 'bg-blue-600 text-white hover:bg-blue-700';
                },
                updateShiftPresetsFromTemplates() {
                    const presets = {};
                    (this.shiftTemplates || []).forEach((tmpl, idx) => {
                        const code = String(idx + 1);
                        const [sh, sm] = (tmpl.start_time || '08:00').split(':').map(Number);
                        const [eh, em] = (tmpl.end_time || '16:00').split(':').map(Number);
                        let startMin = (isNaN(sh) ? 8 : sh) * 60 + (isNaN(sm) ? 0 : sm);
                        let endMin = (isNaN(eh) ? 16 : eh) * 60 + (isNaN(em) ? 0 : em);
                        if (endMin <= startMin) endMin += 24 * 60;
                        const duration_minutes = endMin - startMin;

                        presets[code] = {
                            id: tmpl.id,
                            code: code,
                            name: tmpl.name,
                            start_time: tmpl.start_time,
                            end_time: tmpl.end_time,
                            duration_minutes: duration_minutes,
                            label: tmpl.name,
                            color: tmpl.color || 'blue',
                            badgeClass: this.getShiftBadgeColorClasses(tmpl.color || 'blue')
                        };
                    });
                    this.shiftPresets = presets;
                },
                openShiftTemplateModal() {
                    this.shiftTemplateError = '';
                    if (this.shiftTemplates && this.shiftTemplates.length > 0) {
                        this.draftShiftTemplates = JSON.parse(JSON.stringify(this.shiftTemplates));
                    } else {
                        this.draftShiftTemplates = [
                            { name: 'Shift 1', start_time: '07:00', end_time: '15:00', color: 'blue' },
                            { name: 'Shift 2', start_time: '11:00', end_time: '19:00', color: 'emerald' },
                            { name: 'Shift 3', start_time: '15:00', end_time: '23:00', color: 'purple' },
                            { name: 'Shift 4', start_time: '08:00', end_time: '17:00', color: 'amber' }
                        ];
                    }
                    this.$refs.shiftTemplateDialog.showModal();
                },
                closeShiftTemplateModal() {
                    if (this.shiftTemplateSaving) return;
                    this.$refs.shiftTemplateDialog.close();
                },
                addShiftTemplate() {
                    const palette = ['blue', 'emerald', 'purple', 'amber', 'rose', 'cyan', 'indigo'];
                    const nextIndex = this.draftShiftTemplates.length + 1;
                    const color = palette[(nextIndex - 1) % palette.length];
                    this.draftShiftTemplates.push({
                        id: null,
                        name: 'Shift ' + nextIndex,
                        start_time: '09:00',
                        end_time: '17:00',
                        color: color
                    });
                },
                removeShiftTemplate(index) {
                    if (this.draftShiftTemplates.length <= 1) {
                        this.shiftTemplateError = 'Minimal harus memiliki 1 template shift.';
                        return;
                    }
                    this.shiftTemplateError = '';
                    this.draftShiftTemplates.splice(index, 1);
                },
                async saveShiftTemplates() {
                    if (this.shiftTemplateSaving) return;
                    this.shiftTemplateError = '';

                    if (!this.draftShiftTemplates || this.draftShiftTemplates.length === 0) {
                        this.shiftTemplateError = 'Minimal harus ada 1 template shift.';
                        return;
                    }

                    const timeRegex = /^([01]\d|2[0-3]):[0-5]\d$/;
                    for (let i = 0; i < this.draftShiftTemplates.length; i++) {
                        const item = this.draftShiftTemplates[i];
                        if (!item.name || !item.name.trim()) {
                            this.shiftTemplateError = `Nama template baris ke-${i + 1} tidak boleh kosong.`;
                            return;
                        }
                        const start = (item.start_time || '').substring(0, 5);
                        const end = (item.end_time || '').substring(0, 5);
                        if (!timeRegex.test(start)) {
                            this.shiftTemplateError = `Jam mulai baris ke-${i + 1} tidak valid (format JJ:MM).`;
                            return;
                        }
                        if (!timeRegex.test(end)) {
                            this.shiftTemplateError = `Jam selesai baris ke-${i + 1} tidak valid (format JJ:MM).`;
                            return;
                        }
                        if (start === end) {
                            this.shiftTemplateError = `Jam mulai dan selesai baris ke-${i + 1} tidak boleh sama.`;
                            return;
                        }
                    }

                    this.shiftTemplateSaving = true;
                    try {
                        const res = await fetch('/admin/api/shift-templates', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ templates: this.draftShiftTemplates })
                        });
                        const resJson = await res.json();
                        if (!res.ok) {
                            this.shiftTemplateError = resJson.message || 'Gagal menyimpan template shift.';
                            return;
                        }
                        this.shiftTemplates = resJson.data || [];
                        this.updateShiftPresetsFromTemplates();
                        if (this.matrixActiveTool !== 'auto' && this.matrixActiveTool !== 'delete') {
                            if (!this.shiftTemplates.some(t => t.code === this.matrixActiveTool)) {
                                this.matrixActiveTool = 'auto';
                            }
                        }
                        this.addToast('Template shift berhasil diperbarui', 'success');
                        this.closeShiftTemplateModal();
                        await this.fetchShifts();
                    } catch (e) {
                        this.shiftTemplateError = 'Terjadi kesalahan saat menyimpan template shift.';
                    } finally {
                        this.shiftTemplateSaving = false;
                    }
                },
                shiftSearchStaff: '',
                shiftsLoading: false,
                shiftsError: '',
                shiftSaving: false,
                shiftDeletingId: null,
                shiftFormError: '',
                shiftDate: '{{ now(config('attendance.timezone'))->format('Y-m-d') }}',
                shiftTodayDate: '{{ now(config('attendance.timezone'))->format('Y-m-d') }}',
                shiftDateRangeLabel: '',
                shiftSearch: '',
                shiftEmployees: [],
                shiftTimezone: '{{ config('attendance.timezone') }}',
                currentUserId: {{ auth()->id() ?? 0 }},
                newShift: { id: null, user_id: '', date: '', start_time: '', end_time: '', position: '', notes: '' },
                get filteredShifts() {
                    const term = this.shiftSearch.trim().toLowerCase();
                    const staffFilter = this.shiftSearchStaff ? String(this.shiftSearchStaff) : '';
                    return this.shifts.filter(shift => {
                        const matchTerm = ((shift.user_name || '') + ' ' + (shift.position || '') + ' ' + (shift.notes || '')).toLowerCase().includes(term);
                        const matchStaff = !staffFilter || String(shift.user_id) === staffFilter;
                        return matchTerm && matchStaff;
                    });
                },
                get matrixDays() {
                    const parts = (this.currentMatrixMonth || '{{ now(config('attendance.timezone'))->format('Y-m') }}').split('-');
                    const year = parseInt(parts[0], 10);
                    const month = parseInt(parts[1], 10);
                    const daysInMonth = new Date(year, month, 0).getDate();
                    const result = [];
                    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

                    for (let day = 1; day <= daysInMonth; day++) {
                        const d = new Date(year, month - 1, day);
                        const dayNum = String(day).padStart(2, '0');
                        const monthNum = String(month).padStart(2, '0');
                        const dateStr = `${year}-${monthNum}-${dayNum}`;
                        const dayOfWeek = d.getDay();
                        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                        const isToday = (dateStr === this.shiftTodayDate);

                        result.push({
                            day: day,
                            dayNum: dayNum,
                            dateStr: dateStr,
                            dayName: dayNames[dayOfWeek],
                            isWeekend: isWeekend,
                            isToday: isToday
                        });
                    }
                    return result;
                },
                get shiftMatrixMap() {
                    const map = {};
                    for (const shift of this.shifts) {
                        const key = `${shift.user_id}_${shift.date}`;
                        map[key] = shift;
                    }
                    return map;
                },
                get currentMonthShortName() {
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const parts = (this.currentMatrixMonth || '2026-11').split('-');
                    const m = parseInt(parts[1], 10) - 1;
                    return monthNames[m] || '';
                },
                countWorkersForDate(dateStr) {
                    return this.shifts.filter(s => s.date === dateStr).length;
                },
                countWorkerMonthShifts(userId) {
                    return this.shifts.filter(s => Number(s.user_id) === Number(userId)).length;
                },
                calcWorkerMonthHours(userId) {
                    const totalMin = this.shifts.filter(s => Number(s.user_id) === Number(userId)).reduce((acc, s) => acc + (s.duration_minutes || 0), 0);
                    const h = Math.floor(totalMin / 60);
                    return `${h} jam`;
                },
                prevMonth() {
                    const parts = this.currentMatrixMonth.split('-');
                    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 2, 1);
                    this.currentMatrixMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    this.fetchShifts();
                },
                nextMonth() {
                    const parts = this.currentMatrixMonth.split('-');
                    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10), 1);
                    this.currentMatrixMonth = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
                    this.fetchShifts();
                },
                thisMonth() {
                    this.currentMatrixMonth = '{{ now(config('attendance.timezone'))->format('Y-m') }}';
                    this.fetchShifts();
                },
                async onMatrixCellClick(employee, day) {
                    const key = `${employee.id}_${day.dateStr}`;
                    const existing = this.shiftMatrixMap[key];

                    const availableCodes = (this.shiftTemplates && this.shiftTemplates.length > 0)
                        ? this.shiftTemplates.map(t => t.code)
                        : Object.keys(this.shiftPresets);

                    let targetCode = availableCodes[0] || '1';
                    if (this.matrixActiveTool === 'auto') {
                        if (!existing) {
                            targetCode = availableCodes[0] || '1';
                        } else {
                            const curIdx = availableCodes.indexOf(existing.code);
                            if (curIdx !== -1 && curIdx < availableCodes.length - 1) {
                                targetCode = availableCodes[curIdx + 1];
                            } else {
                                targetCode = 'delete';
                            }
                        }
                    } else if (this.matrixActiveTool === 'delete') {
                        targetCode = 'delete';
                    } else {
                        if (existing && existing.code === this.matrixActiveTool) {
                            targetCode = 'delete';
                        } else {
                            targetCode = this.matrixActiveTool;
                        }
                    }

                    if (targetCode === 'delete') {
                        if (!existing) return;
                        const shiftId = existing.id;
                        const oldShifts = [...this.shifts];
                        this.shifts = this.shifts.filter(s => s.id !== shiftId);

                        this.matrixPendingSaves++;
                        this.matrixSaveStatus = 'saving';

                        try {
                            const res = await fetch(`/admin/api/shifts/${shiftId}`, {
                                method: 'DELETE',
                                headers: { Accept: 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                            });
                            if (!res.ok) throw new Error('delete failed');
                            this.matrixPendingSaves = Math.max(0, this.matrixPendingSaves - 1);
                            if (this.matrixPendingSaves === 0) {
                                this.matrixSaveStatus = 'saved';
                            }
                        } catch (e) {
                            this.matrixPendingSaves = Math.max(0, this.matrixPendingSaves - 1);
                            this.shifts = oldShifts;
                            this.matrixSaveStatus = 'error';
                            this.addToast('Gagal menghapus shift', 'error');
                        }
                    } else {
                        const preset = this.shiftPresets[targetCode] || (this.shiftTemplates && this.shiftTemplates[0] ? {
                            start_time: this.shiftTemplates[0].start_time,
                            end_time: this.shiftTemplates[0].end_time,
                            name: this.shiftTemplates[0].name,
                            duration_minutes: 480
                        } : {
                            start_time: '07:00',
                            end_time: '15:00',
                            name: 'Shift 1',
                            duration_minutes: 480
                        });
                        const payload = {
                            id: existing ? existing.id : null,
                            user_id: employee.id,
                            date: day.dateStr,
                            start_time: preset.start_time,
                            end_time: preset.end_time,
                            position: employee.role || preset.position || 'Barista',
                            notes: preset.name
                        };

                        const tempId = existing ? existing.id : 'temp_' + Date.now();
                        const optimisticShift = {
                            id: tempId,
                            user_id: employee.id,
                            user_name: employee.name,
                            user_role: employee.role,
                            user_initials: employee.initials,
                            date: day.dateStr,
                            date_label: `${day.day} ${this.currentMonthShortName}`,
                            day_name: day.dayName,
                            start_time: preset.start_time,
                            end_time: preset.end_time,
                            overnight: preset.start_time > preset.end_time,
                            position: payload.position,
                            notes: payload.notes,
                            code: targetCode,
                            duration_minutes: preset.duration_minutes
                        };

                        const oldShifts = [...this.shifts];
                        if (existing) {
                            this.shifts = this.shifts.map(s => s.id === existing.id ? optimisticShift : s);
                        } else {
                            this.shifts.push(optimisticShift);
                        }

                        this.matrixPendingSaves++;
                        this.matrixSaveStatus = 'saving';

                        try {
                            const res = await fetch('/admin/api/shifts', {
                                method: 'POST',
                                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify(payload)
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                this.matrixPendingSaves = Math.max(0, this.matrixPendingSaves - 1);
                                this.shifts = oldShifts;
                                this.matrixSaveStatus = 'error';
                                this.addToast(data.message || 'Gagal menyimpan shift', 'error');
                                return;
                            }
                            if (data.shift && data.shift.id) {
                                this.shifts = this.shifts.map(s => s.id === tempId ? { ...s, id: data.shift.id } : s);
                            }
                            this.matrixPendingSaves = Math.max(0, this.matrixPendingSaves - 1);
                            if (this.matrixPendingSaves === 0) {
                                this.matrixSaveStatus = 'saved';
                            }
                        } catch (e) {
                            this.matrixPendingSaves = Math.max(0, this.matrixPendingSaves - 1);
                            this.shifts = oldShifts;
                            this.matrixSaveStatus = 'error';
                            this.addToast('Gagal menyimpan shift', 'error');
                        }
                    }
                },
                get groupedShiftsByDay() {
                    if (!this.shiftDate) return [];
                    const result = [];
                    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

                    const parts = this.shiftDate.split('-');
                    if (parts.length !== 3) return [];
                    const baseDate = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));

                    for (let i = 0; i < 7; i++) {
                        const d = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate() + i);
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const dateNum = String(d.getDate()).padStart(2, '0');
                        const dateStr = `${year}-${month}-${dateNum}`;

                        const isToday = (dateStr === this.shiftTodayDate);
                        const todayParts = this.shiftTodayDate.split('-');
                        const dToday = new Date(parseInt(todayParts[0], 10), parseInt(todayParts[1], 10) - 1, parseInt(todayParts[2], 10));
                        const isPast = d < dToday;

                        const dayShifts = this.filteredShifts.filter(s => s.date === dateStr);

                        result.push({
                            date: dateStr,
                            dateNum: dateNum,
                            dayName: dayNames[d.getDay()],
                            monthName: monthNames[d.getMonth()],
                            dateLabel: `${dateNum} ${monthNames[d.getMonth()]} ${year}`,
                            isToday: isToday,
                            isPast: isPast,
                            shifts: dayShifts
                        });
                    }
                    return result;
                },
                get myShifts() {
                    return this.shifts.filter(s => Number(s.user_id) === Number(this.currentUserId));
                },
                get myTodayShift() {
                    return this.myShifts.find(s => s.date === this.shiftTodayDate);
                },
                get todayOnDutyShifts() {
                    return this.shifts.filter(s => s.date === this.shiftTodayDate);
                },
                get teamTotalDurationText() {
                    const totalMin = this.filteredShifts.reduce((acc, s) => acc + (s.duration_minutes || 0), 0);
                    const h = Math.floor(totalMin / 60);
                    const m = totalMin % 60;
                    return m > 0 ? `${h} jam ${m} mnt` : `${h} jam`;
                },
                get myTotalDurationText() {
                    const totalMin = this.myShifts.reduce((acc, s) => acc + (s.duration_minutes || 0), 0);
                    const h = Math.floor(totalMin / 60);
                    const m = totalMin % 60;
                    return m > 0 ? `${h} jam ${m} mnt` : `${h} jam`;
                },
                shiftDurationCalc(startTime, endTime) {
                    if (!startTime || !endTime) return '—';
                    const [sh, sm] = startTime.split(':').map(Number);
                    const [eh, em] = endTime.split(':').map(Number);
                    if (isNaN(sh) || isNaN(sm) || isNaN(eh) || isNaN(em)) return '—';
                    let startMin = sh * 60 + sm;
                    let endMin = eh * 60 + em;
                    if (endMin <= startMin) endMin += 24 * 60;
                    const diff = endMin - startMin;
                    const h = Math.floor(diff / 60);
                    const m = diff % 60;
                    return m > 0 ? `${h} jam ${m} menit` : `${h} jam`;
                },
                isShiftLive(shift) {
                    if (!shift || shift.date !== this.shiftTodayDate) return false;
                    const now = new Date();
                    const currentHours = now.getHours();
                    const currentMinutes = now.getMinutes();
                    const currentTotal = currentHours * 60 + currentMinutes;

                    const [sh, sm] = (shift.start_time || '00:00').split(':').map(Number);
                    const [eh, em] = (shift.end_time || '00:00').split(':').map(Number);
                    const startTotal = sh * 60 + sm;
                    let endTotal = eh * 60 + em;
                    if (endTotal <= startTotal) {
                        return currentTotal >= startTotal || currentTotal < endTotal;
                    }
                    return currentTotal >= startTotal && currentTotal <= endTotal;
                },
                prevWeek() {
                    const parts = this.shiftDate.split('-');
                    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                    d.setDate(d.getDate() - 7);
                    this.shiftDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    this.fetchShifts();
                },
                nextWeek() {
                    const parts = this.shiftDate.split('-');
                    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
                    d.setDate(d.getDate() + 7);
                    this.shiftDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    this.fetchShifts();
                },
                todayWeek() {
                    this.shiftDate = this.shiftTodayDate;
                    this.fetchShifts();
                },
                openShift(shift = null, targetDate = null) {
                    this.shiftFormError = '';
                    if (shift) {
                        this.newShift = { ...shift };
                    } else {
                        this.newShift = {
                            id: null,
                            user_id: this.shiftEmployees.length > 0 ? this.shiftEmployees[0].id : '',
                            date: targetDate || this.shiftDate || this.shiftTodayDate,
                            start_time: '08:00',
                            end_time: '16:00',
                            position: 'Barista',
                            notes: ''
                        };
                    }
                    this.$refs.shiftDialog.showModal();
                },
                applyShiftPreset(startTime, endTime, defaultPosition = '') {
                    this.newShift.start_time = startTime;
                    this.newShift.end_time = endTime;
                    if (defaultPosition && (!this.newShift.position || this.newShift.position === 'Barista' || this.newShift.position === 'Kasir')) {
                        this.newShift.position = defaultPosition;
                    }
                },
                async fetchShifts() {
                    if (!this.can('manage-crew') || this.shiftsLoading) return;
                    this.shiftsLoading = true;
                    this.shiftsError = '';
                    try {
                        const params = this.shiftViewMode === 'matrix'
                            ? { month: this.currentMatrixMonth }
                            : { month: this.currentMatrixMonth, date: this.shiftDate };
                        const res = await fetch('/admin/api/shifts?' + new URLSearchParams(params), { headers: { Accept: 'application/json' } });
                        if (!res.ok) throw new Error('shifts');
                        const data = await res.json();
                        this.shifts = data.data;
                        this.shiftEmployees = data.employees;
                        this.shiftTimezone = data.timezone;
                        this.shiftDateRangeLabel = data.date_range_label || '';
                        if (data.month_label) this.currentMatrixMonthLabel = data.month_label;
                        if (data.templates && data.templates.length) {
                            this.shiftTemplates = data.templates;
                            this.updateShiftPresetsFromTemplates();
                        }
                    } catch (e) {
                        this.shiftsError = 'Jadwal gagal dimuat. Silakan coba lagi.';
                    } finally {
                        this.shiftsLoading = false;
                    }
                },
                logs: [],
                logsLoading: false,
                logsError: '',
                logSearch: '',
                logDate: '',
                logPage: 1,
                logLastPage: 1,
                logTotal: 0,
                async fetchLogs(page = 1) {
                    if (!this.can('view-reports') || this.logsLoading) return;
                    this.logsLoading = true;
                    this.logsError = '';
                    try {
                        const params = new URLSearchParams({ page, search: this.logSearch, date: this.logDate });
                        const res = await fetch('{{ route('admin.logs.index', [], false) }}' + '?' + params, { headers: { Accept: 'application/json' } });
                        if (!res.ok) throw new Error('logs');
                        const data = await res.json();
                        this.logs = data.data;
                        this.logPage = data.current_page;
                        this.logLastPage = data.last_page;
                        this.logTotal = data.total;
                    } catch (e) {
                        this.logsError = 'Riwayat aktivitas gagal dimuat. Silakan coba lagi.';
                    } finally {
                        this.logsLoading = false;
                    }
                },
                async saveShift() {
                    if (this.shiftSaving) return;
                    this.shiftSaving = true;
                    this.shiftFormError = '';
                    try {
                        const res = await fetch('/admin/api/shifts', {
                            method: 'POST',
                            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify(this.newShift)
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            this.shiftFormError = Object.values(data.errors || {}).flat().join(' ') || data.message || 'Gagal menyimpan jadwal.';
                            return;
                        }
                        this.addToast('Jadwal shift berhasil disimpan', 'success');
                        this.$refs.shiftDialog.close();
                        this.shiftDate = this.newShift.date;
                        await this.fetchShifts();
                    } catch (e) {
                        this.shiftFormError = 'Jadwal gagal disimpan. Periksa koneksi dan coba lagi.';
                    } finally {
                        this.shiftSaving = false;
                    }
                },
                async deleteShift(id) {
                    if (this.shiftDeletingId) return;
                    const result = await Swal.fire({ title: 'Hapus jadwal shift?', text: 'Jadwal ini akan dihapus. Riwayat absensi tetap tersimpan.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ea580c', confirmButtonText: 'Hapus jadwal', cancelButtonText: 'Batal' });
                    if (!result.isConfirmed) return;
                    this.shiftDeletingId = id;
                    try {
                        const res = await fetch(`/admin/api/shifts/${id}`, { method: 'DELETE', headers: { Accept: 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                        if (!res.ok) throw new Error('delete');
                        this.addToast('Jadwal dihapus', 'success');
                        await this.fetchShifts();
                    } catch (e) {
                        this.addToast('Jadwal gagal dihapus. Silakan coba lagi.', 'error');
                    } finally {
                        this.shiftDeletingId = null;
                    }
                },
                addTableFromModal() {
                    if (!this.newTableId.trim()) {
                        this.addToast('Silakan masukkan nama/nomor meja!', 'error');
                        return;
                    }
                    
                    const tableName = this.newTableId.trim();
                    
                    fetch('/admin/api/table', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name: tableName })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.isAddingTable = false;
                        if(data.success) {
                            this.tables.push({
                                id: data.table.name,
                                qr: data.table.qr_code_url
                            });
                            this.showAddTableModal = false;
                            this.newTableId = '';
                        }
                    })
                    .catch(err => {
                        this.addToast(err.message || 'Gagal menyimpan meja', 'error');
                    });
                },
                printQR(table) {
                    const shopName = this.settings.name || 'Bitten Coffee';
                    const printWindow = window.open('', '_blank', 'width=800,height=600');
                    if (!printWindow) {
                        this.addToast('Mohon izinkan popup browser untuk mencetak QR.', 'error');
                        return;
                    }
                    
                    const html = "<!DOCTYPE html>\n<html" + ">\n<he" + "ad>\n" +
                        "    <title>Print QR - " + table.id + "</title>\n" +
                        "    <style>\n" +
                        "        @page { margin: 0; size: auto; }\n" +
                        "        body { font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background-color: #fff; text-align: center; }\n" +
                        "        .card { border: 2px dashed #ccc; padding: 40px; border-radius: 20px; max-width: 300px; margin: 20px auto; }\n" +
                        "        h1 { color: var(--oq-primary); margin-bottom: 10px; font-size: 28px; font-weight: 800; }\n" +
                        "        h2 { color: var(--oq-ink); font-size: 44px; margin: 0 0 20px 0; font-weight: 900; }\n" +
                        "        img { width: 220px; height: 220px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto; mix-blend-mode: multiply; }\n" +
                        "        p { color: var(--oq-muted); font-size: 15px; margin: 0; font-weight: 500; }\n" +
                        "        @media print {\n" +
                        "            body { display: block; height: auto; align-items: flex-start; margin-top: 2cm; }\n" +
                        "            .card { border: none; padding: 20px; max-width: 100%; margin: 0 auto; border-radius: 0; }\n" +
                        "        }\n" +
                        "    </style>\n" +
                        "</he" + "ad>\n<bo" + "dy>\n" +
                        "    <div class=\"card\">\n" +
                        "        <h1>" + shopName + "</h1>\n" +
                        "        <h2>" + table.id.toUpperCase() + "</h2>\n" +
                        "        <img src=\"" + table.qr + "\" alt=\"QR Code\" onload=\"setTimeout(() => { window.print(); }, 500)\" onafterprint=\"window.close()\">\n" +
                        "        <p>Scan untuk melihat menu<br>dan memesan langsung dari mejamu!</p>\n" +
                        "    </div>\n" +
                        "</bo" + "dy>\n</ht" + "ml>";
                    
                    printWindow.document.open();
                    printWindow.document.write(html);
                    printWindow.document.close();
                },
                resetQR(table) {
                    this.qrTableToReset = table;
                    this.showResetQRModal = true;
                },
                confirmResetQR() {
                    if (!this.qrTableToReset) return;
                    this.isResettingQR = true;
                    const table = this.qrTableToReset;
                    
                    fetch('/admin/api/table', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name: table.id })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isResettingQR = false;
                        if(data.success) {
                            table.qr = data.table.qr_code_url;
                            this.showConfirmQRReset = false;
                            this.addToast('QR Code berhasil di-reset', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal reset QR Code', 'error');
                        }
                    })
                    .catch(err => {
                        this.isResettingQR = false;
                        this.addToast('Kesalahan jaringan', 'error');
                    });
                },
                handleLogoUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    if (file.size > 2 * 1024 * 1024) {
                        this.addToast('Ukuran file maksimal 2MB', 'error');
                        return;
                    }
                    
                    this.settings.logoFile = file;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.settings.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },
                // ── Vouchers & POS Discount Presets ────────────────
                vouchers: [],
                isLoadingVouchers: false,
                isSavingVoucher: false,
                voucherModal: {
                    show: false,
                    isEdit: false,
                    id: null,
                    code: '',
                    name: '',
                    discount_type: 'percentage',
                    discount_value: '',
                    min_spend: '',
                    max_discount: '',
                    usage_limit: '',
                    expires_at: '',
                    is_active: true
                },
                newPreset: {
                    name: '',
                    type: 'percentage',
                    value: ''
                },
                addDiscountPreset() {
                    if (!this.newPreset.name || !this.newPreset.value) {
                        this.addToast('Nama dan nilai preset harus diisi!', 'error');
                        return;
                    }
                    if (!this.settings.discount_settings) {
                        this.settings.discount_settings = { is_enabled: true, allow_custom: true, presets: [] };
                    }
                    if (!Array.isArray(this.settings.discount_settings.presets)) {
                        this.settings.discount_settings.presets = [];
                    }
                    this.settings.discount_settings.presets.push({
                        id: 'preset_' + Date.now(),
                        name: this.newPreset.name.trim(),
                        type: this.newPreset.type,
                        value: Number(this.newPreset.value)
                    });
                    this.newPreset = { name: '', type: 'percentage', value: '' };
                    this.addToast('Preset shortcut ditambahkan! Klik "Simpan Perubahan" untuk menyimpan.', 'info');
                },
                removeDiscountPreset(index) {
                    if (this.settings.discount_settings?.presets) {
                        this.settings.discount_settings.presets.splice(index, 1);
                    }
                },
                async loadVouchers() {
                    this.isLoadingVouchers = true;
                    try {
                        const res = await fetch('/admin/api/vouchers', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.vouchers = data.vouchers || [];
                        }
                    } catch (e) {
                        console.error('Failed to load vouchers', e);
                    } finally {
                        this.isLoadingVouchers = false;
                    }
                },
                openCreateVoucherModal() {
                    this.voucherModal = {
                        show: true,
                        isEdit: false,
                        id: null,
                        code: '',
                        name: '',
                        discount_type: 'percentage',
                        discount_value: '',
                        min_spend: '',
                        max_discount: '',
                        usage_limit: '',
                        expires_at: '',
                        is_active: true
                    };
                },
                openEditVoucherModal(v) {
                    this.voucherModal = {
                        show: true,
                        isEdit: true,
                        id: v.id,
                        code: v.code,
                        name: v.name,
                        discount_type: v.discount_type,
                        discount_value: v.discount_value,
                        min_spend: v.min_spend || '',
                        max_discount: v.max_discount || '',
                        usage_limit: v.usage_limit || '',
                        expires_at: v.expires_at ? v.expires_at.substring(0, 10) : '',
                        is_active: Boolean(v.is_active)
                    };
                },
                async saveVoucher() {
                    if (!this.voucherModal.code || !this.voucherModal.name || !this.voucherModal.discount_value) {
                        this.addToast('Kode voucher, nama promo, dan nilai diskon wajib diisi!', 'error');
                        return;
                    }
                    this.isSavingVoucher = true;
                    const url = this.voucherModal.isEdit
                        ? `/admin/api/vouchers/${this.voucherModal.id}`
                        : '/admin/api/vouchers';
                    const method = this.voucherModal.isEdit ? 'PUT' : 'POST';
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            },
                            body: JSON.stringify({
                                code: this.voucherModal.code,
                                name: this.voucherModal.name,
                                discount_type: this.voucherModal.discount_type,
                                discount_value: Number(this.voucherModal.discount_value),
                                min_spend: this.voucherModal.min_spend ? Number(this.voucherModal.min_spend) : 0,
                                max_discount: this.voucherModal.max_discount ? Number(this.voucherModal.max_discount) : null,
                                usage_limit: this.voucherModal.usage_limit ? Number(this.voucherModal.usage_limit) : null,
                                expires_at: this.voucherModal.expires_at || null,
                                is_active: this.voucherModal.is_active
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.addToast(data.message || 'Voucher berhasil disimpan!', 'success');
                            this.voucherModal.show = false;
                            this.loadVouchers();
                    this.loadAddons();
                        } else {
                            this.addToast(data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Gagal menyimpan voucher'), 'error');
                        }
                    } catch (e) {
                        this.addToast('Terjadi kesalahan saat menyimpan voucher', 'error');
                    } finally {
                        this.isSavingVoucher = false;
                    }
                },
                async toggleVoucher(v) {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const res = await fetch(`/admin/api/vouchers/${v.id}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            v.is_active = !v.is_active;
                            this.addToast(data.message, 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal mengubah status voucher', 'error');
                    }
                },
                async deleteVoucher(v) {
                    if (!confirm(`Hapus voucher "${v.code}"?`)) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    try {
                        const res = await fetch(`/admin/api/vouchers/${v.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token || ''
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.vouchers = this.vouchers.filter(item => item.id !== v.id);
                            this.addToast('Voucher berhasil dihapus!', 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal menghapus voucher', 'error');
                    }
                },
                // ── Addons & Modifiers ──────────────────────────────
                modifierGroups: window.INITIAL_DATA.modifierGroups || [],
                isLoadingAddons: false,
                isSavingAddon: false,
                addonFilter: 'all',
                addonSearch: '',
                addonModal: {
                    show: false,
                    id: null,
                    name: '',
                    selection_type: 'SINGLE',
                    category_target: 'all',
                    is_required: false,
                    is_active: true,
                    items: [],
                    product_ids: []
                },
                get filteredModifierGroups() {
                    let list = this.modifierGroups || [];
                    if (this.addonFilter === 'SINGLE') {
                        list = list.filter(g => g.selection_type === 'SINGLE');
                    } else if (this.addonFilter === 'MULTIPLE') {
                        list = list.filter(g => g.selection_type === 'MULTIPLE');
                    }
                    if (this.addonSearch && this.addonSearch.trim()) {
                        const q = this.addonSearch.toLowerCase().trim();
                        list = list.filter(g => 
                            g.name.toLowerCase().includes(q) || 
                            (g.modifiers || []).some(m => m.name.toLowerCase().includes(q))
                        );
                    }
                    return list;
                },
                async loadAddons() {
                    this.isLoadingAddons = true;
                    try {
                        const res = await fetch('/admin/api/addons', {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.modifierGroups = data.groups || [];
                        }
                    } catch (e) {
                        console.error('Failed to load addons', e);
                    } finally {
                        this.isLoadingAddons = false;
                    }
                },
                openCreateAddonModal() {
                    this.addonModal = {
                        show: true,
                        id: null,
                        name: '',
                        selection_type: 'SINGLE',
                        category_target: 'all',
                        is_required: false,
                        is_active: true,
                        items: [
                            { name: 'Normal Ice', price_adjustment: 0, cogs_adjustment: 0, is_active: true },
                            { name: 'Less Ice', price_adjustment: 0, cogs_adjustment: 0, is_active: true },
                            { name: 'No Ice', price_adjustment: 0, cogs_adjustment: 0, is_active: true },
                        ],
                        product_ids: []
                    };
                },
                editAddonGroup(group) {
                    this.addonModal = {
                        show: true,
                        id: group.id,
                        name: group.name,
                        selection_type: group.selection_type || 'MULTIPLE',
                        category_target: group.category_target || 'all',
                        is_required: Boolean(group.is_required),
                        is_active: Boolean(group.is_active),
                        items: (group.modifiers || []).map(m => ({
                            id: m.id,
                            name: m.name,
                            price_adjustment: Number(m.price_adjustment) || 0,
                            cogs_adjustment: Number(m.cogs_adjustment) || 0,
                            is_active: Boolean(m.is_active)
                        })),
                        product_ids: (group.products || []).map(p => p.id)
                    };
                    if (this.addonModal.items.length === 0) {
                        this.addAddonItemRow();
                    }
                },
                addAddonItemRow() {
                    this.addonModal.items.push({
                        name: '',
                        price_adjustment: 0,
                        cogs_adjustment: 0,
                        is_active: true
                    });
                },
                removeAddonItemRow(index) {
                    if (this.addonModal.items.length > 1) {
                        this.addonModal.items.splice(index, 1);
                    } else {
                        this.addToast('Minimal harus ada 1 opsi addon.', 'error');
                    }
                },
                async saveAddonGroup() {
                    if (!this.addonModal.name || !this.addonModal.name.trim()) {
                        this.addToast('Nama grup addon wajib diisi!', 'error');
                        return;
                    }
                    const validItems = this.addonModal.items.filter(i => i.name && i.name.trim());
                    if (validItems.length === 0) {
                        this.addToast('Minimal harus ada 1 nama opsi addon!', 'error');
                        return;
                    }
                    this.isSavingAddon = true;
                    try {
                        const payload = {
                            name: this.addonModal.name.trim(),
                            selection_type: this.addonModal.selection_type,
                            category_target: this.addonModal.category_target,
                            is_required: this.addonModal.is_required,
                            is_active: this.addonModal.is_active,
                            items: validItems,
                            product_ids: this.addonModal.product_ids
                        };
                        const url = this.addonModal.id ? `/admin/api/addons/${this.addonModal.id}` : '/admin/api/addons';
                        const method = this.addonModal.id ? 'PUT' : 'POST';

                        const res = await fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Gagal menyimpan grup addon');
                        }
                        this.addToast(data.message || 'Grup addon berhasil disimpan!', 'success');
                        this.addonModal.show = false;
                        await this.loadAddons();
                        if (typeof this.loadMenu === 'function') {
                            this.loadMenu();
                        }
                    } catch (e) {
                        this.addToast(e.message || 'Terjadi kesalahan sistem', 'error');
                    } finally {
                        this.isSavingAddon = false;
                    }
                },
                async toggleAddonStatus(group) {
                    try {
                        const res = await fetch(`/admin/api/addons/${group.id}/toggle`, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            group.is_active = data.is_active;
                            this.addToast(data.message || 'Status addon berhasil diperbarui', 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal mengubah status addon', 'error');
                    }
                },
                async deleteAddonGroup(group) {
                    if (!confirm(`Hapus grup addon "${group.name}"? Pilihan ini akan dilepas dari seluruh menu terkait.`)) return;
                    try {
                        const res = await fetch(`/admin/api/addons/${group.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.addToast(data.message || 'Grup addon berhasil dihapus', 'success');
                            await this.loadAddons();
                        } else {
                            throw new Error(data.message || 'Gagal menghapus');
                        }
                    } catch (e) {
                        this.addToast(e.message || 'Gagal menghapus grup addon', 'error');
                    }
                },
                saveSettings() {
                    if (!this.settings.name || !this.settings.slug) {
                        this.addToast('Nama dan Slug toko wajib diisi!', 'error');
                        return;
                    }
                    
                    this.isSavingSettings = true;
                    
                    const formData = new FormData();
                    formData.append('name', this.settings.name || '');
                    formData.append('slug', this.settings.slug || '');
                    formData.append('primary_color', this.settings.primary_color || '');
                    formData.append('slogan', this.settings.slogan || '');
                    formData.append('is_open', this.settings.is_open ? 1 : 0);
                    formData.append('theme_style', this.settings.theme_style || '');
                    formData.append('font_family', this.settings.font_family || '');
                    formData.append('title_font_family', this.settings.title_font_family || '');
                    formData.append('receipt_header', this.settings.receipt_header || '');
                    formData.append('receipt_footer', this.settings.receipt_footer || '');
                    formData.append('receipt_settings', JSON.stringify(this.settings.receipt_settings || {}));
                    formData.append('printers', JSON.stringify(this.settings.printers || []));
                    formData.append('discount_settings', JSON.stringify(this.settings.discount_settings || {}));
                    formData.append('instagram_link', this.settings.instagram_link || '');
                    formData.append('tiktok_link', this.settings.tiktok_link || '');
                    formData.append('social_settings', JSON.stringify(this.settings.social_settings));
                    formData.append('whatsapp_number', this.settings.whatsapp_number || '');
                    formData.append('maps_link', this.settings.maps_link || '');
                    
                    formData.append('background_type', this.settings.background_type || 'pattern');
                    formData.append('background_pattern', this.settings.background_pattern || 'dots');
                    formData.append('background_color', this.settings.background_color || '#fdfbf7');
                    formData.append('background_overlay_opacity', this.settings.background_overlay_opacity ?? 85);

                    if (this.settings.removeBackgroundImage) {
                        formData.append('remove_background_image', 1);
                    } else if (this.settings.backgroundImageFile) {
                        formData.append('background_image', this.settings.backgroundImageFile);
                    }

                    formData.append('best_seller_ids', JSON.stringify(this.settings.best_seller_ids || []));
                    
                    if (this.settings.logoFile && this.$refs.logoInput && this.$refs.logoInput.files.length > 0) {
                        formData.append('logo', this.$refs.logoInput.files[0]);
                    }
                    formData.append('is_banner_active', this.settings.is_banner_active ? 1 : 0);
                    for (let i = 0; i < 3; i++) {
                        const bInput = document.getElementById('bannerInput' + i);
                        if (this.settings.bannerFiles[i] && bInput && bInput.files.length > 0) {
                            formData.append(`banner_${i}`, bInput.files[0]);
                        } else if (this.settings.bannerPaths[i]) {
                            formData.append(`existing_banner_${i}`, this.settings.bannerPaths[i]);
                        }
                    }
                    formData.append('operating_hours', JSON.stringify(this.settings.operating_hours));
                    
                    fetch('/admin/api/settings', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingSettings = false;
                        if (data.success) {
                            this.addToast('Pengaturan berhasil disimpan!', 'success');
                            if (data.shop && data.shop.logo_url) {
                                this.settings.logoPreview = data.shop.logo_url;
                                this.settings.logoFile = null;
                            }
                            
                            // Perbarui elemen UI lain
                            const favicon = document.getElementById('favicon');
                            const sidebarLogo = document.getElementById('sidebar-logo');
                            if (data.shop) {
                                const newLogoUrl = data.shop.logo_url || favicon?.dataset.fallbackUrl;
                                if (favicon) favicon.href = newLogoUrl;
                                if (sidebarLogo) sidebarLogo.src = newLogoUrl;
                            }
                            
                            if (data.shop && data.shop.banners) {
                                this.settings.bannerPaths = data.shop.banners.concat([null, null, null]).slice(0, 3);
                                this.settings.banners = data.shop.banners.map(b => b ? (b.startsWith('http') || b.startsWith('/') ? b : '/storage/' + b) : null).concat([null, null, null]).slice(0, 3);
                                this.settings.bannerFiles = [null, null, null];
                            }

                            if (data.shop) {
                                this.settings.backgroundImagePreview = data.shop.background_image_url || null;
                                this.settings.backgroundImageFile = null;
                                this.settings.removeBackgroundImage = false;
                                if (data.shop.background_type) {
                                    this.settings.background_type = data.shop.background_type;
                                }
                            }
                        } else {
                            this.addToast('Gagal menyimpan pengaturan: ' + (data.message || 'Error tidak diketahui'), 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingSettings = false;
                        this.addToast('Terjadi kesalahan jaringan.', 'error');
                        console.error(err);
                    });
                }
            }))
        });
    </script>

    <!-- MODAL TAMBAH / EDIT VOUCHER PROMO -->
    <div x-show="voucherModal.show" x-cloak class="fixed inset-0 z-[9990] flex items-center justify-center p-4">
        <div x-show="voucherModal.show" x-transition.opacity class="absolute inset-0 bg-ink/60 backdrop-blur-sm" @click="voucherModal.show = false"></div>

        <div x-show="voucherModal.show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white rounded-[24px] border border-line w-full max-w-lg shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[92vh]">

            <div class="p-5 border-b border-line flex items-center justify-between bg-ivory/50">
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <i class="fas fa-ticket-alt"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-base text-ink" x-text="voucherModal.isEdit ? 'Edit Voucher Promo' : 'Buat Voucher Promo Baru'"></h3>
                        <p class="text-xs text-muted">Atur kode kupon dan aturan diskon untuk pelanggan.</p>
                    </div>
                </div>
                <button type="button" @click="voucherModal.show = false" class="w-8 h-8 flex items-center justify-center text-muted hover:text-ink rounded-full transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form @submit.prevent="saveVoucher" class="p-6 overflow-y-auto space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-ink mb-1.5">Kode Voucher (Unik) *</label>
                        <input type="text" x-model="voucherModal.code" @input="voucherModal.code = voucherModal.code.toUpperCase().replace(/\s+/g, '')"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 font-mono uppercase font-black text-sm tracking-wider focus:border-primary focus:outline-none"
                            placeholder="MISAL: HEMAT20" required>
                    </div>
                    <div>
                        <label class="block font-bold text-ink mb-1.5">Nama / Keterangan Promo *</label>
                        <input type="text" x-model="voucherModal.name"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 focus:border-primary focus:outline-none"
                            placeholder="Contoh: Promo Tanggal Muda" required>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-ink mb-1.5">Tipe & Besaran Diskon *</label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button" @click="voucherModal.discount_type = 'percentage'"
                            :class="voucherModal.discount_type === 'percentage' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-line bg-ivory/40 text-muted font-medium'"
                            class="py-2 rounded-xl border transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-percent text-xs"></i>
                            <span>Persentase (%)</span>
                        </button>
                        <button type="button" @click="voucherModal.discount_type = 'fixed'"
                            :class="voucherModal.discount_type === 'fixed' ? 'border-primary bg-primary/10 text-primary font-bold' : 'border-line bg-ivory/40 text-muted font-medium'"
                            class="py-2 rounded-xl border transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-coins text-xs"></i>
                            <span>Nominal Tetap (Rp)</span>
                        </button>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted font-bold"
                              x-text="voucherModal.discount_type === 'percentage' ? '%' : 'Rp'"></span>
                        <input type="number" x-model.number="voucherModal.discount_value" :min="0.1" :max="voucherModal.discount_type === 'percentage' ? 100 : 10000000" step="any"
                            class="w-full pl-10 pr-4 py-2.5 border border-line rounded-xl font-bold focus:border-primary focus:outline-none"
                            :placeholder="voucherModal.discount_type === 'percentage' ? 'Besaran persen, misal: 20' : 'Nominal rupiah, misal: 15000'" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div x-show="voucherModal.discount_type === 'percentage'">
                        <label class="block font-bold text-ink mb-1.5">Maksimal Diskon (Rp, Opsional)</label>
                        <input type="number" x-model.number="voucherModal.max_discount" min="0" step="1000"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 focus:border-primary focus:outline-none"
                            placeholder="Tanpa batas maksimal">
                        <span class="text-[10px] text-muted">Batas maksimal potongan untuk diskon persen.</span>
                    </div>
                    <div>
                        <label class="block font-bold text-ink mb-1.5">Minimal Belanja (Rp)</label>
                        <input type="number" x-model.number="voucherModal.min_spend" min="0" step="1000"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 focus:border-primary focus:outline-none"
                            placeholder="0 (Tanpa minimum)">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-ink mb-1.5">Batas Kuota Pemakaian (Opsional)</label>
                        <input type="number" x-model.number="voucherModal.usage_limit" min="1"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 focus:border-primary focus:outline-none"
                            placeholder="Tanpa kuota (unlimited)">
                        <span class="text-[10px] text-muted">Total berapa kali voucher ini bisa dipakai.</span>
                    </div>
                    <div>
                        <label class="block font-bold text-ink mb-1.5">Masa Berlaku Hingga (Opsional)</label>
                        <input type="date" x-model="voucherModal.expires_at"
                            class="w-full border border-line rounded-xl px-3.5 py-2.5 focus:border-primary focus:outline-none">
                        <span class="text-[10px] text-muted">Kosongkan jika berlaku selamanya.</span>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="inline-flex items-center gap-2 cursor-pointer font-bold text-ink">
                        <input type="checkbox" x-model="voucherModal.is_active" class="w-4 h-4 text-primary rounded border-line focus:ring-primary">
                        <span>Aktifkan voucher ini langsung</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-line flex items-center justify-end gap-2.5">
                    <button type="button" @click="voucherModal.show = false"
                        class="px-4 py-2.5 rounded-xl border border-line font-bold text-muted hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSavingVoucher"
                        class="px-5 py-2.5 rounded-xl bg-primary hover:bg-brand-700 text-white font-bold transition flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <i class="fas fa-spinner fa-spin" x-show="isSavingVoucher" x-cloak></i>
                        <span x-text="isSavingVoucher ? 'Menyimpan...' : (voucherModal.isEdit ? 'Simpan Perubahan' : 'Buat Voucher')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[9999] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="px-5 py-3.5 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-3 pointer-events-auto border min-w-[300px] justify-between"
                 :class="toast.type === 'error' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-900 text-white border-gray-800'">
                <div class="flex items-center gap-3">
                    <i class="fas text-lg" :class="toast.type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'"></i>
                    <span x-text="toast.message"></span>
                </div>
                <button @click="removeToast(toast.id)" class="opacity-50 hover:opacity-100 transition-opacity">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Floating Action Button to Customer Menu -->
    <a :href="'/' + settings.slug" target="_blank" class="fixed bottom-10 right-10 bg-primary text-white p-4 rounded-full shadow-2xl flex items-center justify-center hover:scale-105 hover:bg-primary-dark transition-all z-[100] group border-4 border-white">
        <i class="fas fa-store text-xl"></i>
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-[200px] transition-all duration-500 ease-in-out pl-0 group-hover:pl-3 font-bold text-sm">Lihat Web Pelanggan</span>
    </a>

    <script>
        window.SHOP_ID = {{ $shop->id ?? 'null' }};
    </script>
</body>
</html>






