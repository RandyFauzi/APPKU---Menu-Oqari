<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - {{ $shop->name ?? config('app.name', 'Oqari') }}</title>
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
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Playfair+Display:wght@600;700;800&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Auth Guard removed, using Laravel middleware instead -->
    
    <!-- Data Scripts removed -->

    <style>
        body { background-color: var(--oq-ivory); color: var(--oq-ink); }
        .hide-scroll::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
        .dashed-box { background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%23CFCFCF' stroke-width='2' stroke-dasharray='8%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e"); border-radius: 16px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="font-sans text-textdark h-screen flex overflow-hidden bg-brewlybg" x-data="dashboardApp()" @new-order-received.window="handleNewOrderFromSocket($event.detail)" @refresh-live-orders.window="fetchLiveOrders(false)">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>
    
    <!-- Audio untuk notifikasi pesanan masuk -->
    <audio id="chime-sound" src="{{ asset('Assest/notif_orderan_masuk.mp3') }}" preload="auto"></audio>

    <x-cafe-sidebar :dashboard="true" />

    <!-- Main Content -->
    <main class="flex-grow min-w-0 flex flex-col h-full bg-white rounded-l-[24px] shadow-[-10px_0_30px_rgba(0,0,0,0.02)] border-l border-brewlyborder overflow-hidden">
        
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

                <!-- Date Pill (Sparta Figma Style) -->
                <div class="bg-[#f8f9fa] border border-[#e9ebf0] rounded-[8px] px-3 py-1.5 hidden md:flex items-center text-[10px] font-medium text-[#7f837e] tracking-tight">
                    {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                </div>

                <!-- Bell Notifications -->
                <div class="relative cursor-pointer p-1.5 text-gray-500 hover:text-primary transition-colors" @click="currentTab = 'orders'" title="Pesanan Masuk">
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
                            <button @click="currentTab = 'profile'; open = false" class="w-full text-left px-3.5 py-2 rounded-[8px] text-xs font-semibold text-ink hover:bg-gray-50 hover:text-primary transition-colors flex items-center gap-2.5">
                                <i class="fas fa-user w-4 text-gray-400"></i> Profil Saya
                            </button>
                            <div class="h-px bg-gray-100 my-1 mx-1.5"></div>
                            <button @click="logout()" class="w-full text-left px-3.5 py-2 rounded-[8px] text-xs font-bold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2.5">
                                <i class="fas fa-sign-out-alt w-4"></i> Keluar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        @include('Admin.Dashboard.tabs.overview')
        @include('Admin.Dashboard.tabs.orders')
        @include('Admin.Dashboard.tabs.menu')
        
        <!-- VIEW: ANALYTICS (OWNER) -->
        @include('Admin.Dashboard.tabs.report')
        @include('Admin.Dashboard.tabs.inventory')
        @include('Admin.Dashboard.tabs.qr')
        @include('Admin.Dashboard.tabs.crew')
        @include('Admin.Dashboard.tabs.shifts')
        @include('Admin.Dashboard.tabs.logs')
        @include('Admin.Dashboard.tabs.settings')
        @include('Admin.Dashboard.tabs.hours')
        @include('Admin.Dashboard.tabs.receipt')
        @include('Admin.Dashboard.tabs.profile')


        <!-- MODAL: ADD CATEGORY -->
        <div x-show="showAddCategoryModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-sans bg-black/40 backdrop-blur-xs" x-transition.opacity>
            <div class="bg-white rounded-[20px] w-full max-w-[380px] p-6 shadow-[0_20px_50px_rgba(0,0,0,0.12)] relative overflow-hidden border border-[#e9ebf0]" @click.away="showAddCategoryModal = false" x-transition>
                
                <div class="absolute top-4 right-4 z-10">
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

        <!-- MODAL: ORDER DETAIL -->
        <div x-show="showOrderDetailModal" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4 font-sans sm:items-center sm:p-0">
            <!-- Backdrop -->
            <div @click="showOrderDetailModal = false" class="absolute inset-0 bg-black/25 backdrop-blur-[2px] transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-ivory w-full sm:w-[520px] rounded-[24px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col border border-line overflow-hidden" @keydown.escape.window="showOrderDetailModal = false" x-show="selectedOrder"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-6 bg-ivory border-b border-line flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-[14px] bg-primary text-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[18px] font-bold text-primary leading-tight mb-1" x-text="'Struk Pesanan #' + (selectedOrder ? selectedOrder.id : '')"></h2>
                            <p class="text-[13px] font-mono text-muted" x-text="selectedOrder ? formatOrderTime(selectedOrder.time) : ''"></p>
                        </div>
                    </div>

                    <button @click="showOrderDetailModal = false" class="w-10 h-10 flex items-center justify-center rounded-[12px] bg-white border border-line text-muted hover:text-ink hover:bg-gray-50 transition-colors shrink-0">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 bg-white relative">
                    <!-- Order Number & Type -->
                    <div class="flex gap-4 items-center mt-2 mb-6">
                        <div class="w-12 h-12 rounded-[14px] bg-ivory border border-line text-ink flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[18px] font-bold text-ink mb-1" x-text="selectedOrder?.customer"></h3>
                            <p class="text-[13px] text-muted" x-text="(selectedOrder?.type || 'Takeaway') + ((selectedOrder?.type === 'Dine-in' || selectedOrder?.type === 'Dine In') && selectedOrder?.table ? ' (' + selectedOrder.table + ')' : '')"></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-line my-4"></div>

                    <!-- Items -->
                    <div class="flex flex-col gap-4 max-h-[40vh] overflow-y-auto hide-scroll py-2">
                        <template x-for="(item, idx) in selectedOrder?.items" :key="idx">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[12px] bg-[#ede7dc] overflow-hidden shrink-0 flex items-center justify-center"><img x-show="item.image" :src="item.image" class="w-full h-full object-cover"><span x-show="!item.image" class="text-xs font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span></div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-[14px] font-bold text-ink truncate" x-text="item.name"></h4>
                                    <p x-show="item.notes" class="text-[11px] text-gold-600 mt-0.5"><i class="fas fa-comment-dots mr-1"></i> <span x-text="item.notes"></span></p>
                                </div>
                                <div class="text-[13px] font-bold text-muted w-8 text-center shrink-0" x-text="item.qty + 'x'"></div>
                                <div class="text-[14px] font-bold text-ink w-24 text-right shrink-0" x-text="formatRp(item.price * item.qty)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-dashed border-line my-4"></div>

                    <!-- Total & Payment -->
                    <div class="flex justify-between items-end mb-2 mt-4">
                        <div>
                            <p class="text-[13px] font-bold text-ink mb-1">Total Harga</p>
                            <p class="text-[26px] font-bold text-primary" x-text="selectedOrder ? formatRp(selectedOrder.total) : '0'"></p>
                        </div>

                        <!-- Payment Badge -->
                        <div class="bg-brand-100 rounded-[12px] px-4 py-2.5 flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-primary mt-0.5"></i>
                            <div>
                                <p class="text-[13px] font-bold text-primary leading-none mb-1.5">Pembayaran</p>
                                <p class="text-[11px] font-medium text-primary/80 leading-none">QRIS / Online</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 bg-ivory border-t border-line flex gap-3">
                    <button @click="showOrderDetailModal = false" class="w-1/3 py-3.5 rounded-[16px] text-[15px] font-bold text-muted bg-white border border-line hover:text-ink hover:bg-gray-50 transition-all flex justify-center items-center">
                        Tutup
                    </button>
                    <button @click="window.open('/admin/api/orders/' + selectedOrder.id + '/print', '_blank')" class="w-2/3 py-3.5 rounded-[16px] text-[15px] font-bold bg-primary text-white shadow-sm hover:bg-[#123A2A] hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2">
                        <i class="fas fa-print"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: INCOMING ORDER ALERT -->
        <div x-show="activeIncomingOrder" x-cloak class="fixed inset-0 z-[200] flex items-center justify-center p-4 font-sans sm:items-center sm:p-0">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/25 backdrop-blur-[2px] transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-ivory w-full sm:w-[520px] rounded-[24px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col border border-line overflow-hidden"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-6 bg-ivory border-b border-line flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-[14px] bg-gold-600 text-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fas fa-bell text-xl animate-pulse"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[18px] font-bold text-gold-600 leading-tight mb-1">Pesanan Baru Masuk!</h2>
                            <p class="text-[13px] text-muted">Baru saja</p>
                        </div>
                    </div>
                    
                    <!-- Timer Badge -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[15px] font-mono font-bold shrink-0 transition-colors"
                         :class="{
                            'bg-gold-600 text-white': incomingTimer > 15,
                            'bg-red-500 text-white animate-pulse': incomingTimer <= 15
                         }">
                        <i class="fas fa-stopwatch text-sm"></i>
                        <span x-text="formatTime(incomingTimer)"></span>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 bg-white relative">
                    <!-- Queue info -->
                    <div x-show="incomingOrderQueue.length > 0" class="absolute top-0 inset-x-0 bg-gold-100 text-gold-600 text-[12px] font-bold py-1 text-center">
                        <span x-text="incomingOrderQueue.length"></span> pesanan menunggu antrean
                    </div>

                    <!-- Order Number & Type -->
                    <div class="flex gap-4 items-center mt-2 mb-6">
                        <div class="w-12 h-12 rounded-[14px] bg-ivory border border-line text-ink flex items-center justify-center shrink-0">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[18px] font-bold text-ink mb-1">Pesanan #<span x-text="activeIncomingOrder?.id"></span></h3>
                            <p class="text-[13px] text-muted"><span x-text="activeIncomingOrder?.type || 'Takeaway'"></span> • <span x-text="activeIncomingOrder?.customer"></span></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-line my-4"></div>

                    <!-- Items -->
                    <div class="flex flex-col gap-4 max-h-[30vh] overflow-y-auto hide-scroll py-2">
                        <template x-for="(item, idx) in activeIncomingOrder?.items" :key="idx">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-[12px] bg-[#ede7dc] overflow-hidden shrink-0 flex items-center justify-center"><img x-show="item.image" :src="item.image" class="w-full h-full object-cover"><span x-show="!item.image" class="text-xs font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span></div>
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-[14px] font-bold text-ink truncate" x-text="item.name"></h4>
                                </div>
                                <div class="text-[13px] font-bold text-muted w-8 text-center shrink-0" x-text="item.qty + 'x'"></div>
                                <div class="text-[14px] font-bold text-ink w-24 text-right shrink-0" x-text="formatRp(item.price * item.qty)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-dashed border-line my-4"></div>

                    <!-- Total & Payment -->
                    <div class="flex justify-between items-end mb-2 mt-4">
                        <div>
                            <p class="text-[13px] font-bold text-ink mb-1">Total Pesanan</p>
                            <p class="text-[26px] font-bold text-primary" x-text="activeIncomingOrder ? formatRp(activeIncomingOrder.total) : '0'"></p>
                        </div>
                        
                        <!-- Payment Badge -->
                        <div class="bg-brand-100 rounded-[12px] px-4 py-2.5 flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-primary mt-0.5"></i>
                            <div>
                                <p class="text-[13px] font-bold text-primary leading-none mb-1.5">Pembayaran Lunas</p>
                                <p class="text-[11px] font-medium text-primary/80 leading-none">Dibayar via QRIS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 bg-ivory border-t border-line">
                    <!-- State: Confirm Reject -->
                    <div x-show="incomingOrderState === 'reject_confirm'" class="flex flex-col gap-4">
                        <p class="text-center text-[15px] font-bold text-ink">Tolak pesanan #<span x-text="activeIncomingOrder?.id"></span>?</p>
                        <div class="flex gap-3">
                            <button @click="incomingOrderState = 'new'" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-white border border-line text-muted hover:bg-gray-50 transition-colors">Batal</button>
                            <button @click="confirmRejectOrder(false)" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors">Ya, Tolak Pesanan</button>
                        </div>
                    </div>

                    <!-- State: New/Normal -->
                    <div x-show="incomingOrderState === 'new'" class="flex gap-4">
                        <button @click="incomingOrderState = 'reject_confirm'" class="w-[140px] shrink-0 py-4 rounded-[14px] font-bold text-[14px] bg-transparent border border-line text-muted hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors">
                            Tolak Pesanan
                        </button>
                        <button @click="acceptOrder()" class="flex-1 py-4 rounded-[14px] font-bold text-[15px] bg-primary text-white hover:bg-primary-dark transition-colors shadow-[0_4px_12px_rgba(30,90,122,0.2)] flex items-center justify-center gap-2">
                            <i class="fas fa-coffee"></i> Terima & Siapkan
                        </button>
                    </div>

                    <!-- State: Accepting -->
                    <div x-show="incomingOrderState === 'accepting'" class="flex justify-center items-center py-4">
                        <i class="fas fa-spinner fa-spin text-primary text-2xl"></i>
                        <span class="ml-3 font-bold text-primary">Menerima pesanan...</span>
                    </div>

                    <!-- State: Accepted -->
                    <div x-show="incomingOrderState === 'accepted'" class="flex flex-col justify-center items-center py-2">
                        <div class="w-10 h-10 bg-brand-100 text-primary rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <span class="font-bold text-primary">Pesanan Diterima!</span>
                        <span class="text-[13px] text-muted">Sedang disiapkan...</span>
                    </div>
                    
                    <!-- State: Rejected -->
                    <div x-show="incomingOrderState === 'rejected'" class="flex flex-col justify-center items-center py-2">
                        <div class="w-10 h-10 bg-red-100 text-red-600 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-times text-xl"></i>
                        </div>
                        <span class="font-bold text-red-600">Pesanan Ditolak</span>
                    </div>

                    <!-- Timeout Notice -->
                    <div class="text-center mt-5" x-show="['new', 'reject_confirm'].includes(incomingOrderState)">
                        <p class="text-[12px] font-medium text-muted flex items-center justify-center gap-1.5">
                            <i class="far fa-clock"></i> Pesanan otomatis ditolak dalam <span x-text="incomingTimer" class="font-mono font-bold"></span> detik
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
            categories: @json($categories ?? [])
        };

        const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                user: window.INITIAL_DATA.user || {},
                can(ability) { return window.INITIAL_DATA.permissions.includes(ability); },
                isMinimized: window.innerWidth < 1024,
                mobileNavOpen: false,
                isLoadingSummary: true,
                isLoadingMenus: true,
                isLoadingAnalytics: true,
                currentTab: new URLSearchParams(window.location.search).get('tab') || localStorage.getItem('activeDashboardTab') || 'dashboard',
                showAddCategoryModal: false,
                newCategoryName: '',
                showAddMenuModal: false,
                showAddTableModal: false,
                showResetQRModal: false,
                qrTableToReset: null,
                isResettingQR: false,
                showAddCrewModal: false,
                users: [],
                categories: [],
                tables: [],
                menuItems: [],
                orders: [],
                analytics: null,
                newCrew: { name: '', email: '', password: '', role: 'barista' },
                showEditCrewModal: false,
                editCrewData: { id: null, name: '', email: '', password: '', role: 'barista' },
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
                activeOrderFilter: 'process',
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
                materialForm: { id: null, name: '', sku: '', unit: 'ml', current_stock: 0, minimum_stock: 0, cost_per_unit: 0 },
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
                    })
                    .catch(err => {
                        this.isLoadingInventory = false;
                    });
                },

                openAddMaterialModal() {
                    this.editingUnitLocked = false;
                    this.materialForm = { id: null, name: '', sku: '', unit: 'ml', current_stock: 0, minimum_stock: 10, cost_per_unit: 0 };
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
                    this.showBulkUpload = true;
                    if (this.draftMenus.length === 0) {
                        this.addDraftRow();
                    }
                },
                addDraftRow() {
                    this.draftMenus.push({ 
                        id: null, 
                        name: '', 
                        price: '', 
                        categoryId: this.categories.length > 0 ? this.categories[0].id : '', 
                        desc: '',
                        imagePreview: null, 
                        imageFile: null 
                    });
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
                        if (item.desc) formData.append(`items[${index}][description]`, item.desc);
                        if (item.imageFile) formData.append(`images[${index}]`, item.imageFile);
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
                settings: {
                    name: window.INITIAL_DATA.shop?.name || '',
                    slug: window.INITIAL_DATA.shop?.slug || '',
                    primary_color: window.OQARI_THEME.primary,
                    logoPreview: window.INITIAL_DATA.shop?.logo_url ? window.INITIAL_DATA.shop.logo_url : '',
                    logoFile: null,
                    slogan: window.INITIAL_DATA.shop?.slogan || '',
                    is_open: window.INITIAL_DATA.shop?.is_open ?? true,
                    theme_style: ['list', 'grid'].includes(window.INITIAL_DATA.shop?.theme_style) ? window.INITIAL_DATA.shop?.theme_style : 'list',
                    font_family: window.INITIAL_DATA.shop?.font_family || 'poppins',
                    title_font_family: window.INITIAL_DATA.shop?.title_font_family || 'poppins',
                    receipt_header: window.INITIAL_DATA.shop?.receipt_header || '',
                    receipt_footer: window.INITIAL_DATA.shop?.receipt_footer || 'Terima kasih atas kunjungan Anda!\nSilakan datang kembali.',
                    instagram_link: window.INITIAL_DATA.shop?.instagram_link || '',
                    whatsapp_number: window.INITIAL_DATA.shop?.whatsapp_number || '',
                    maps_link: window.INITIAL_DATA.shop?.maps_link || '',
                    is_banner_active: window.INITIAL_DATA.shop?.is_banner_active ?? true,
                      banners: (window.INITIAL_DATA.shop?.banners || []).map(b => b ? (b.startsWith('http') || b.startsWith('/') ? b : '/storage/' + b) : null).concat([null, null, null]).slice(0, 3),
                      bannerFiles: [null, null, null],
                      bannerPaths: (window.INITIAL_DATA.shop?.banners || []).concat([null, null, null]).slice(0, 3),
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
                profile: {
                    name: window.INITIAL_DATA.user?.name || '',
                    email: window.INITIAL_DATA.user?.email || '',
                    password: '',
                    password_confirmation: ''
                },
                isSavingProfile: false,
                saveProfile() {
                    this.isSavingProfile = true;
                    fetch('/admin/api/profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.profile)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingProfile = false;
                        if(data.success) {
                            this.addToast('Profil berhasil diperbarui!', 'success');
                            this.profile.password = '';
                            this.profile.password_confirmation = '';
                            if(data.user) {
                                this.profile.name = data.user.name;
                                this.profile.email = data.user.email;
                                window.INITIAL_DATA.user = data.user;
                            }
                        } else if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            this.addToast(firstError, 'error');
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan profil', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingProfile = false;
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
                get filteredOrders() {
                    let filtered = this.orders;
                    if (this.activeOrderFilter === 'process') {
                        filtered = filtered.filter(o => (o.status === 'In Progress' || o.order_status === 'PREPARING' || o.status === 'PREPARING') || (o.status === 'Ready' || o.order_status === 'READY' || o.status === 'READY') || (o.status === 'Masuk' || o.order_status === 'CONFIRMED' || o.status === 'CONFIRMED'));
                    } else if (this.activeOrderFilter === 'completed') {
                        filtered = filtered.filter(o => (o.status === 'Completed' || o.order_status === 'COMPLETED' || o.status === 'COMPLETED'));
                    }
                    
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(o => 
                            o.customer.toLowerCase().includes(q) || 
                            o.id.toString().includes(q) ||
                            (o.table && o.table.toLowerCase().includes(q))
                        );
                    }
                    return filtered;
                },
                tabs: [
                    { id: 'dashboard', ability: null, name: 'Dashboard', icon: 'fas fa-th-large' },
                    { id: 'orders', ability: 'view-orders', name: 'Pesanan Aktif', icon: 'fas fa-receipt' },
                    { id: 'menu', ability: 'manage-menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'inventory', ability: 'manage-inventory', name: 'Bahan & Stok', icon: 'fas fa-boxes-stacked' },
                    { id: 'qr', ability: 'manage-settings', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'crew', ability: 'manage-crew', name: 'Crew Management', icon: 'fas fa-users' },
                    { id: 'shifts', ability: 'manage-crew', name: 'Jadwal Shift', icon: 'fas fa-calendar-alt' },
                    { id: 'report', ability: 'view-reports', name: 'Laporan Keuangan', icon: 'fas fa-book' },
                    { id: 'logs', ability: 'view-reports', name: 'Log Aktivitas', icon: 'fas fa-history' },
                    { id: 'settings', ability: 'manage-settings', name: 'Toko Saya', icon: 'fas fa-store' },
                    { id: 'hours', ability: 'manage-settings', name: 'Jam Operasional', icon: 'fas fa-clock' },
                    { id: 'receipt', ability: 'manage-settings', name: 'Pengaturan Struk', icon: 'fas fa-print' },
                ].filter(tab => !tab.ability || window.INITIAL_DATA.permissions.includes(tab.ability)),
                                tables: [],
                reportPeriod: 'all',
                reportSearch: '',
                reportTypeFilter: 'all',
                reportPaymentFilter: 'all',
                reportActiveBarIndex: null,
                formatNum(num) {
                    return new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
                },
                reportPeriodLabel() {
                    const now = new Date();
                    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const fullMonthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    if (this.reportPeriod === 'daily') {
                        return `${now.getDate()} ${fullMonthNames[now.getMonth()]} ${now.getFullYear()}`;
                    }
                    if (this.reportPeriod === 'weekly') {
                        const start = new Date(now);
                        start.setDate(now.getDate() - now.getDay());
                        return `${String(start.getDate()).padStart(2, '0')} ${monthNames[start.getMonth()]} - ${String(now.getDate()).padStart(2, '0')} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
                    }
                    if (this.reportPeriod === 'monthly') {
                        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
                        return `01 ${monthNames[now.getMonth()]} - ${lastDay} ${monthNames[now.getMonth()]} ${now.getFullYear()}`;
                    }
                    return 'Semua Periode (Keseluruhan)';
                },
                get reportData() {
                    const now = new Date();
                    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const startOfWeek = new Date(startOfToday);
                    startOfWeek.setDate(startOfToday.getDate() - now.getDay());
                    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    
                    const validOrders = this.orders.filter(o => {
                        const s = (o.status || o.order_status || '').toUpperCase();
                        return s !== 'CANCELLED' && s !== 'BATAL' && s !== 'REJECTED';
                    });
                    
                    let daily = { count: 0, total: 0 };
                    let weekly = { count: 0, total: 0 };
                    let monthly = { count: 0, total: 0 };
                    validOrders.forEach(o => {
                        if (!o.time) return;
                        const d = new Date(String(o.time).replace(' ', 'T'));
                        if (isNaN(d.getTime())) return;
                        if (d >= startOfToday) { daily.count++; daily.total += Number(o.total || 0); }
                        if (d >= startOfWeek) { weekly.count++; weekly.total += Number(o.total || 0); }
                        if (d >= startOfMonth) { monthly.count++; monthly.total += Number(o.total || 0); }
                    });
                    return { daily, weekly, monthly };
                },
                get filteredReportOrders() {
                    const now = new Date();
                    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const startOfWeek = new Date(startOfToday);
                    startOfWeek.setDate(startOfToday.getDate() - now.getDay());
                    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    
                    const query = (this.reportSearch || '').toLowerCase().trim();
                    
                    return this.orders.filter(o => {
                        const s = (o.status || o.order_status || '').toUpperCase();
                        const ps = (o.payment_status || '').toUpperCase();
                        
                        // Exclude cancelled / rejected orders
                        if (s === 'CANCELLED' || s === 'BATAL' || s === 'REJECTED') return false;
                        
                        // All non-cancelled orders count in financial ledger
                        const isValid = (
                            s === 'COMPLETED' || s === 'SELESAI' || 
                            ps === 'PAID' || 
                            s === 'CONFIRMED' || s === 'READY' || s === 'PREPARING' || s === 'MASUK' || 
                            Number(o.total || 0) > 0
                        );
                        if (!isValid) return false;
                        
                        // Date filter
                        if (o.time && this.reportPeriod !== 'all') {
                            const d = new Date(String(o.time).replace(' ', 'T'));
                            if (!isNaN(d.getTime())) {
                                if (this.reportPeriod === 'daily' && d < startOfToday) return false;
                                if (this.reportPeriod === 'weekly' && d < startOfWeek) return false;
                                if (this.reportPeriod === 'monthly' && d < startOfMonth) return false;
                            }
                        }
                        
                        // Type filter
                        if (this.reportTypeFilter !== 'all') {
                            if (this.reportTypeFilter === 'dine_in' && o.type !== 'Dine In') return false;
                            if (this.reportTypeFilter === 'takeaway' && o.type !== 'Takeaway') return false;
                        }
                        
                        // Payment filter
                        if (this.reportPaymentFilter !== 'all') {
                            const pm = (o.payment_method || 'Tunai').toLowerCase();
                            if (this.reportPaymentFilter === 'cash' && !pm.includes('tunai') && !pm.includes('cash')) return false;
                            if (this.reportPaymentFilter === 'qris' && !pm.includes('qris')) return false;
                            if (this.reportPaymentFilter === 'transfer' && !pm.includes('transfer') && !pm.includes('bank')) return false;
                        }
                        
                        // Search query
                        if (query) {
                            const matchId = String(o.id).toLowerCase().includes(query);
                            const matchCustomer = (o.customer || '').toLowerCase().includes(query);
                            const matchItems = (o.items || []).some(i => (i.name || '').toLowerCase().includes(query));
                            const matchPayment = (o.payment_method || '').toLowerCase().includes(query);
                            if (!matchId && !matchCustomer && !matchItems && !matchPayment) return false;
                        }
                        
                        return true;
                    });
                },
                get reportMetrics() {
                    const orders = this.filteredReportOrders;
                    let revenue = 0;
                    let cogs = 0;
                    orders.forEach(o => {
                        const rev = Number(o.total || 0);
                        revenue += rev;
                        const itemCogs = Number(o.total_cogs || 0);
                        cogs += itemCogs > 0 ? itemCogs : (rev * 0.38);
                    });
                    const profit = Math.max(0, revenue - cogs);
                    const cogsPercent = revenue > 0 ? Math.round((cogs / revenue) * 100) : 38;
                    const marginPercent = revenue > 0 ? Math.round((profit / revenue) * 100) : 62;
                    const count = orders.length;
                    const aov = count > 0 ? Math.round(revenue / count) : 0;
                    return { revenue, cogs, profit, cogsPercent, marginPercent, count, aov };
                },
                get financialOverviewBars() {
                    const monthNames = ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov'];
                    const now = new Date();
                    const bars = [];
                    for (let i = 8; i >= 0; i--) {
                        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        const mIndex = d.getMonth();
                        const y = d.getFullYear();
                        const label = monthNames[mIndex % 12];
                        const fullLabel = label + ' ' + y;
                        
                        const monthOrders = this.orders.filter(o => {
                            const s = (o.status || o.order_status || '').toUpperCase();
                            if (s === 'CANCELLED' || s === 'BATAL' || s === 'REJECTED') return false;
                            if (!o.time) return false;
                            const od = new Date(String(o.time).replace(' ', 'T'));
                            return !isNaN(od.getTime()) && od.getMonth() === mIndex && od.getFullYear() === y;
                        });
                        
                        let rev = monthOrders.reduce((sum, o) => sum + Number(o.total || 0), 0);
                        let cogs = monthOrders.reduce((sum, o) => sum + (Number(o.total_cogs || 0) || (Number(o.total || 0) * 0.38)), 0);
                        
                        const isCurrent = i === 0;
                        bars.push({
                            month: label,
                            fullLabel: fullLabel,
                            revenue: rev,
                            cogs: cogs,
                            profit: Math.max(0, rev - cogs),
                            marginPercent: rev > 0 ? Math.round(((rev - cogs) / rev) * 100) : 68,
                            isCurrent: isCurrent
                        });
                    }
                    
                    // Dynamic scaling
                    const maxRev = Math.max(...bars.map(b => b.revenue), 100000);
                    bars.forEach(b => {
                        b.heightPercent = maxRev > 0 && b.revenue > 0 ? Math.max(14, Math.round((b.revenue / maxRev) * 100)) : 10;
                        b.cogsHeightPercent = maxRev > 0 && b.cogs > 0 ? Math.max(10, Math.round((b.cogs / maxRev) * 100)) : 8;
                    });
                    
                    return bars;
                },
                exportCSV() {
                    const orders = this.filteredReportOrders;
                    if (!orders || orders.length === 0) {
                        alert('Tidak ada data transaksi pada periode yang dipilih untuk di-export.');
                        return;
                    }
                    const headers = ['ID Transaksi', 'Waktu Transaksi', 'Pelanggan', 'Tipe Pesanan', 'Metode Pembayaran', 'Item Pesanan', 'Total Pendapatan (IDR)', 'Estimasi HPP (IDR)', 'Estimasi Laba (IDR)', 'Status'];
                    const rows = orders.map(o => {
                        const rev = Number(o.total || 0);
                        const cogs = Number(o.total_cogs || 0) || Math.round(rev * 0.38);
                        const profit = Math.max(0, rev - cogs);
                        const itemsStr = (o.items || []).map(i => `${i.qty}x ${i.name}`).join(' | ');
                        return [
                            `"#${o.id}"`,
                            `"${new Date((o.time || '').replace(' ', 'T')).toLocaleString('id-ID')}"`,
                            `"${(o.customer || '').replace(/"/g, '""')}"`,
                            `"${o.type || 'Dine In'}"`,
                            `"${o.payment_method || 'Tunai'}"`,
                            `"${itemsStr.replace(/"/g, '""')}"`,
                            rev,
                            cogs,
                            profit,
                            `"${o.status || 'Completed'}"`
                        ].join(',');
                    });
                    const csvContent = '\uFEFF' + [headers.join(','), ...rows].join('\r\n');
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
                    link.setAttribute('href', url);
                    link.setAttribute('download', `Laporan_Keuangan_Sparta_${this.reportPeriod}_${dateStr}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                },
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

                orders: [],
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
                    if (Array.isArray(this.analytics?.topProducts) && this.analytics.topProducts.length > 0) {
                        return this.analytics.topProducts.map((p, idx) => ({
                            rank: p.rank || (idx + 1),
                            name: p.name || 'Produk',
                            sold: Number(p.sold || 0),
                            revenue: Number(p.revenue || 0),
                            image: p.image || null
                        }));
                    }
                    return [];
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
                    this.$watch('currentTab', (val) => {
                        if (val === 'logs') this.fetchLogs(1);
                        if (val === 'shifts') this.fetchShifts();
                        localStorage.setItem('activeDashboardTab', val);
                        const url = new URL(window.location);
                        url.searchParams.set('tab', val);
                        window.history.replaceState({}, '', url);
                    });
                    
                    if (!this.tabs.some(tab => tab.id === this.currentTab)) this.currentTab = this.tabs[0]?.id || 'profile';
                    if (this.currentTab === 'logs') this.fetchLogs(1);
                    if (this.currentTab === 'shifts') this.fetchShifts();
                    this.initSummary();
                    this.initAnalytics();
                    this.loadMenu();
                    this.fetchInventory();
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
                    this.fetchLiveOrders(true);
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
                        this.fetchLiveOrders(false);
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

                    this.draftMenus = [{
                        id: item.id,
                        name: item.name,
                        price: item.price,
                        categoryId: matchedCatId || (this.categories.length > 0 ? this.categories[0].id : ''),
                        desc: item.desc || item.description || '',
                        imagePreview: item.image || item.image_url || null,
                        imageFile: null
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
                        body: JSON.stringify({ name: this.newCategoryName })
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            this.categories.push(data.category);
                            this.newCategoryName = '';
                            this.showAddCategoryModal = false;
                            this.addToast('Kategori berhasil ditambahkan!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal menambahkan kategori', 'error');
                        }
                    }).catch(err => {
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });
                },
                viewOrderDetails(order) {
                    this.selectedOrder = order;
                    this.showOrderDetailModal = true;
                },
                updateStatus(id, newStatus) {
                    fetch(`/admin/api/orders/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
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
                    const chime = document.getElementById('chime-sound');
                    if (chime) {
                        chime.pause();
                        chime.currentTime = 0;
                        chime.loopCount = 5;
                    }
                },
                acceptOrder() {
                    this.stopNotification();
                      this.incomingOrderState = 'accepting';
                    setTimeout(() => {
                        this.incomingOrderState = 'accepted';
                        this.updateStatus(this.activeIncomingOrder.id, 'In Progress');
                        setTimeout(() => this.closeActiveOrder(), 1500);
                    }, 800);
                },
                autoCloseOrder() {
                    this.addToast(`Popup ditutup. Pesanan #${this.activeIncomingOrder.id} masuk ke daftar tunggu.`, 'info');
                    this.closeActiveOrder();
                },
                confirmRejectOrder(isAuto = false) {
                    this.stopNotification();
                      this.incomingOrderState = 'rejected';
                    this.updateStatus(this.activeIncomingOrder.id, 'Dibatalkan');
                    if (isAuto) {
                        this.addToast(`Pesanan #${this.activeIncomingOrder.id} telah kedaluwarsa.`, 'error');
                    }
                    setTimeout(() => this.closeActiveOrder(), 1500);
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
        const res = await fetch('/admin/api/dashboard/analytics');
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
                            table: o.table ? o.table.name : 'TA',
                            type: o.fulfillment_type === 'DINE_IN' ? 'Dine In' : (o.fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : 'Delivery'),
                            status: o.order_status || o.status,
                            total: parseFloat(o.grand_total || o.total_price),
                            payment_method: o.payment_method || 'Tunai',
                            total_cogs: parseFloat(o.total_cogs || 0),
                            time: o.created_at,
                            items: (o.items || []).map(i => ({
                                name: i.product_name || i.product?.name || i.name || 'Produk',
                                qty: i.quantity,
                                price: parseFloat(i.unit_price ?? i.price ?? i.product?.price ?? 0), notes: i.notes || '',
                                image: i.product?.image_url ? (i.product.image_url) : null,
                                desc: i.product?.description || ''
                            }))
                        }));

                        const newIncomingOrders = dbOrders.filter(o => (o.status === 'Masuk' || o.order_status === 'CONFIRMED' || o.status === 'CONFIRMED') && !this.orders.find(old => old.id == o.id));

                        if (!isInit && newIncomingOrders.length > 0) {
                            this.playNotification();

                            newIncomingOrders.forEach(o => this.incomingOrderQueue.push(o));
                            this.processIncomingQueue();
                        }

                        this.orders = dbOrders;
                    } catch (e) {
                        console.error('fetchLiveOrders error:', e);
                    }
                },
                shifts: [],
                logs: [],
                logsLoading: false,
                logsError: '',
                logSearch: '',
                logDate: '',
                logPage: 1,
                logLastPage: 1,
                logTotal: 0,
                shiftsLoading: false,
                shiftsError: '',
                shiftSaving: false,
                shiftDeletingId: null,
                shiftFormError: '',
                shiftDate: '{{ now(config('attendance.timezone'))->format('Y-m-d') }}',
                shiftSearch: '',
                shiftEmployees: [],
                shiftTimezone: '',
                newShift: { id: null, user_id: '', date: '', start_time: '', end_time: '', position: '', notes: '' },
                get filteredShifts() {
                    const term = this.shiftSearch.trim().toLowerCase();
                    return this.shifts.filter(shift => (shift.user_name + ' ' + (shift.position || '')).toLowerCase().includes(term));
                },
                openShift(shift = null) {
                    this.shiftFormError = '';
                    this.newShift = shift ? { ...shift } : { id: null, user_id: '', date: this.shiftDate, start_time: '', end_time: '', position: '', notes: '' };
                    this.$refs.shiftDialog.showModal();
                },
                async fetchShifts() {
                    if (!this.can('manage-crew') || this.shiftsLoading) return;
                    this.shiftsLoading = true;
                    this.shiftsError = '';
                    try {
                        const res = await fetch('/admin/api/shifts?' + new URLSearchParams({ date: this.shiftDate }), { headers: { Accept: 'application/json' } });
                        if (!res.ok) throw new Error('shifts');
                        const data = await res.json();
                        this.shifts = data.data;
                        this.shiftEmployees = data.employees;
                        this.shiftTimezone = data.timezone;
                    } catch (e) {
                        this.shiftsError = 'Jadwal gagal dimuat. Silakan coba lagi.';
                    } finally {
                        this.shiftsLoading = false;
                    }
                },
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
                        "        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background-color: #fff; text-align: center; }\n" +
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
                saveCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newCrew)
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) {
                            throw data;
                        }
                        return data;
                    })
                    .then(data => {
                        this.isSaving = false;
                        if(data.success) {
                            this.users.push(data.user);
                            this.showAddCrewModal = false;
                            this.newCrew = { name: '', email: '', password: '', role: 'barista' };
                            this.addToast('Crew berhasil ditambahkan', 'success');
                        } else {
                            this.addToast(data.message || 'Error menambahkan crew', 'error');
                        }
                    })
                    .catch((err) => {
                        this.isSaving = false;
                        if (err.errors) {
                            const firstError = Object.values(err.errors)[0][0];
                            this.addToast(firstError, 'error');
                        } else {
                            this.addToast(err.message || 'Network error', 'error');
                        }
                    });
                },
                openEditCrew(user) {
                    this.editCrewData = { 
                        id: user.id, 
                        name: user.name, 
                        email: user.email, 
                        password: '', 
                        role: user.role 
                    };
                    this.showEditCrewModal = true;
                },
                updateCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew/' + this.editCrewData.id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ...this.editCrewData, _method: 'PUT' })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.isSaving = false;
                        if(data.success) {
                            const idx = this.users.findIndex(u => u.id === data.user.id);
                            if(idx !== -1) this.users[idx] = data.user;
                            this.showEditCrewModal = false;
                            this.addToast('Crew berhasil diupdate', 'success');
                        } else {
                            this.addToast(data.message || 'Error update crew', 'error');
                        }
                    })
                    .catch((err) => {
                        this.isSaving = false;
                        if (err.errors) {
                            const firstError = Object.values(err.errors)[0][0];
                            this.addToast(firstError, 'error');
                        } else {
                            this.addToast(err.message || 'Network error', 'error');
                        }
                    });
                },
                async deleteCrew(id) {
                    const result = await Swal.fire({
                        title: 'Hapus Crew?',
                        text: "Data anggota tim ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    });
                    
                    if(!result.isConfirmed) return;
                    fetch('/admin/api/crew/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.users = this.users.filter(u => u.id !== id);
                            this.addToast('Crew dihapus', 'success');
                        }
                    });
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
                    formData.append('instagram_link', this.settings.instagram_link || '');
                    formData.append('whatsapp_number', this.settings.whatsapp_number || '');
                    formData.append('maps_link', this.settings.maps_link || '');
                    
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






