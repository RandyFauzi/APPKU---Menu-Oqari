<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $shop->name ?? config('app.name', 'Appku') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Favicon -->
    @if(isset($shop) && $shop->logo_url)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $shop->logo_url) }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('Pavico.webp') }}">
    @endif
    
    <!-- View Transitions API -->
    <meta name="view-transition" content="same-origin" />
    <style>
        body { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Auth Guard removed, using Laravel middleware instead -->
    
    <!-- Data -->
    <script src="js/data.js"></script>
    <script src="js/database.js"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        brewlybg: '#FDFBF7',
                        brewlygreen: '#34714C',
                        brewlylightgreen: '#E7F2EB',
                        brewlypeach: '#FEF0E6',
                        brewlyorange: '#D9652A',
                        brewlyborder: '#F0ECE4',
                        brewlytext: '#222222',
                        brewlymuted: '#888888',
                        primary: '#1E5A7A', bgbase: '#F4EFE6', accent: '#8CB8C9', textdark: '#2D3748' 
                    }, 
                    fontFamily: { heading: ['Inter', 'sans-serif'], mono: ['Space Mono', 'monospace'], sans: ['Inter', 'sans-serif'] } 
                } 
            } 
        }
    </script>
    <style>
        body { background-color: #FDFBF7; color: #222222; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
        .dashed-box { background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%23CFCFCF' stroke-width='2' stroke-dasharray='8%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e"); border-radius: 16px; }
    </style>
</head>
<body class="font-sans text-textdark h-screen flex overflow-hidden" x-data="dashboardApp()">

    <!-- Audio untuk notifikasi pesanan masuk -->
    <audio id="chime-sound" src="Assest/Notif%20Orderan%20Masuk.mp3" preload="auto"></audio>

    <!-- Sidebar -->
    <aside class="w-64 flex flex-col shrink-0 border-r border-brewlyborder p-6 gap-6 h-full bg-brewlybg">
        <div class="flex items-center gap-3 mb-2">
            @if(isset($shop) && $shop->logo_url)
                <img src="{{ asset('storage/' . $shop->logo_url) }}" alt="Logo" class="w-10 h-10 rounded-md object-cover shadow-sm">
            @else
                <img src="{{ asset('Pavico.webp') }}" alt="Logo" class="w-10 h-10 object-contain">
            @endif
            <div class="flex flex-col">
                <span class="font-bold text-lg leading-tight truncate w-32">{{ $shop->name ?? 'Appku' }}</span>
                <span class="text-[10px] text-brewlymuted uppercase tracking-wider">Coffee Shops. Stronger.</span>
            </div>
        </div>
        
        <nav class="flex-grow space-y-1">
            <template x-for="tab in tabs" :key="tab.id">
                <button @click="currentTab = tab.id" 
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-colors text-left"
                        :class="currentTab === tab.id ? 'bg-[#DDEBDD] text-[#164A35] font-bold' : 'text-brewlymuted hover:bg-gray-100 hover:text-[#164A35] font-medium'">
                    <i :class="tab.icon" class="w-5 text-center"></i>
                    <span x-text="tab.name"></span>
                </button>
            </template>
        </nav>
        
        <!-- Promo Card -->
        <div class="bg-[#F8F7F3] rounded-2xl p-5 relative overflow-hidden flex flex-col gap-3 mt-auto mb-2 border border-[#E3E1DC]">
            <h4 class="font-bold text-[16px] leading-tight z-10 text-[#164A35]">Brew Better Days</h4>
            <p class="text-[12px] text-[#777873] z-10 leading-snug w-4/5">Track, analyze and grow your cafe effortlessly.</p>
            <img src="https://images.unsplash.com/photo-1550133730-695473e544be?w=100&fit=crop" class="absolute -bottom-4 -right-4 w-20 h-20 object-cover rounded-full opacity-70 border-4 border-white shadow-sm">
        </div>
        
        <!-- Logout Button -->
        <button @click="logout()" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 transition-colors mt-2">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col h-full bg-white rounded-l-[40px] shadow-[-10px_0_30px_rgba(0,0,0,0.02)] border-l border-brewlyborder overflow-hidden">
        
        <!-- Top Header for Main Area -->
        <header class="h-24 flex justify-between items-center px-10 shrink-0 border-b border-brewlyborder/50 bg-white relative z-10">
            <div x-show="currentTab !== 'analytics'">
                <h2 class="font-sans font-bold text-3xl text-[#164A35]" x-text="tabs.find(t => t.id === currentTab)?.name"></h2>
            </div>
            
            <div class="flex items-center gap-6 ml-auto">
                <div class="relative cursor-pointer group" x-show="currentTab !== 'analytics'">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search..." class="bg-gray-50 border border-gray-200 rounded-full pl-11 pr-4 py-2 text-sm focus:outline-none focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen w-64 transition-all">
                </div>

                <div class="relative cursor-pointer group">
                    <i class="fas fa-bell text-[#777873] text-xl group-hover:text-[#164A35] transition-colors"></i>
                    <span class="absolute -top-1 -right-1.5 bg-red-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border-2 border-white">3</span>
                </div>
                
                <div class="relative pl-6 border-l border-gray-200" x-data="{ open: false }">
                    <div @click="open = !open" @click.away="open = false" class="flex items-center gap-3 cursor-pointer group">
                        <img src="https://ui-avatars.com/api/?name=Admin&background=164A35&color=fff" class="w-10 h-10 rounded-full object-cover">
                        <div class="flex flex-col">
                            <span class="text-[14px] font-bold text-[#202522] leading-tight w-24 truncate" x-text="settings.name || 'Admin'"></span>
                            <span class="text-[12px] font-semibold text-[#777873]">Admin</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-[#777873] ml-1 group-hover:text-[#164A35] transition-colors"></i>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak x-transition.opacity class="absolute right-0 mt-3 w-48 bg-white rounded-[16px] shadow-[0_10px_40px_rgba(0,0,0,0.08)] border border-[#E3E1DC] overflow-hidden z-50">
                        <div class="p-2">
                            <button @click="currentTab = 'profile'; open = false" class="w-full text-left px-4 py-2.5 rounded-[10px] text-sm font-semibold text-[#202522] hover:bg-[#F8F7F3] hover:text-[#164A35] transition-colors flex items-center gap-3">
                                <i class="fas fa-user w-4"></i> Profile
                            </button>
                            <div class="h-px bg-gray-100 my-1 mx-2"></div>
                            <button @click="logout()" class="w-full text-left px-4 py-2.5 rounded-[10px] text-sm font-bold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-3">
                                <i class="fas fa-sign-out-alt w-4"></i> Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- VIEW: LIVE ORDERS (KANBAN) -->
                @include('Admin.Dashboard.tabs.orders')
        @include('Admin.Dashboard.tabs.menu')
        
        <!-- VIEW: ANALYTICS (OWNER) -->
        @include('Admin.Dashboard.tabs.analytics')
        @include('Admin.Dashboard.tabs.qr')
        @include('Admin.Dashboard.tabs.crew')
        @include('Admin.Dashboard.tabs.settings')
        @include('Admin.Dashboard.tabs.profile')


        <!-- MODAL: ADD/EDIT MENU -->
        <div x-show="showAddMenuModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 font-sans">
            <!-- Backdrop -->
            <div @click="showAddMenuModal = false" class="absolute inset-0 bg-[#202522]/30 backdrop-blur-sm transition-opacity"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-[#FFFFFF] w-full max-w-2xl rounded-[28px] p-8 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col border border-[#E3E1DC]" @keydown.escape.window="showAddMenuModal = false" x-transition>
                
                <!-- Header -->
                <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-[#E3E1DC]">
                    <div class="flex gap-5">
                        <div class="w-16 h-16 rounded-[16px] bg-[#164A35] text-white flex items-center justify-center shadow-sm">
                            <i class="fas fa-pen text-2xl" x-show="newMenu.id"></i>
                            <i class="fas fa-hamburger text-2xl" x-show="!newMenu.id"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[28px] text-[#164A35] leading-tight mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;" x-text="newMenu.id ? 'Edit Menu' : 'Tambah Menu Baru'"></h2>
                            <p class="text-[#777873] text-[15px]" x-text="newMenu.id ? 'Perbarui detail, harga, dan gambar menu ini.' : 'Tambahkan menu baru ke dalam daftar toko Anda.'"></p>
                        </div>
                    </div>
                    <button @click="showAddMenuModal = false" class="text-[#777873] hover:text-[#202522] bg-[#F8F7F3] hover:bg-[#E3E1DC] transition-colors w-8 h-8 flex items-center justify-center rounded-full mt-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex flex-col gap-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Kategori</label>
                            <select x-model="newMenu.categoryId" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-bold text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none appearance-none">
                                <option value="beverages">Beverages</option>
                                <option value="foods">Foods</option>
                                <option value="snacks">Snacks</option>
                                <option value="sweets">Sweets</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Harga (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-[15px] text-[#202522] font-medium">Rp</span>
                                <input type="number" x-model="newMenu.price" placeholder="0" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] pl-11 pr-4 py-3.5 text-[15px] font-bold text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Nama Menu</label>
                        <input type="text" x-model="newMenu.name" placeholder="Misal: Iced Matcha Latte" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-bold text-[#202522] placeholder:font-medium placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Deskripsi Singkat</label>
                        <textarea x-model="newMenu.desc" placeholder="Penjelasan menarik tentang menu ini..." rows="2" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[14px] px-4 py-3.5 text-[15px] font-medium text-[#202522] placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Upload Gambar (Opsional)</label>
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-[14px] bg-[#F8F7F3] border border-dashed border-[#C5DBC5] flex items-center justify-center shrink-0">
                                <i class="fas fa-image text-[#C5DBC5] text-xl"></i>
                            </div>
                            <input type="file" x-ref="menuImageInput" class="w-full text-sm text-[#777873] file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-[13px] file:font-bold file:bg-[#DDEBDD] file:text-[#164A35] hover:file:bg-[#C5DBC5] cursor-pointer outline-none">
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-[#E3E1DC]">
                    <button @click="showAddMenuModal = false" class="px-6 py-3 rounded-[12px] font-bold text-[14px] bg-white border border-[#E3E1DC] text-[#777873] hover:bg-[#F8F7F3] transition-colors">Batal</button>
                    <button @click="saveNewMenu" class="px-6 py-3 rounded-[12px] font-bold text-[14px] bg-[#164A35] text-white hover:bg-[#0f3526] transition-colors shadow-sm flex items-center gap-2">
                        <i class="fas fa-check" x-show="!newMenu.id"></i>
                        <i class="fas fa-save" x-show="newMenu.id"></i>
                        <span x-text="newMenu.id ? 'Simpan Perubahan' : 'Simpan Menu'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD TABLE -->
        <!-- MODAL: ADD TABLE -->
        <div x-show="showAddTableModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-[#164A35]/40 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-white rounded-[24px] w-full max-w-[420px] p-8 shadow-[0_20px_60px_rgba(22,74,53,0.15)] relative overflow-hidden" @click.away="showAddTableModal = false" x-transition>
                
                <!-- Decorative Circle -->
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-[#F8F7F3] rounded-full pointer-events-none"></div>
                <div class="absolute top-4 right-4 z-10">
                    <button @click="showAddTableModal = false" class="text-[#777873] hover:text-[#202522] bg-[#F8F7F3] hover:bg-[#E3E1DC] transition-colors w-8 h-8 flex items-center justify-center rounded-full">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-full bg-[#DDEBDD] text-[#164A35] flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-[24px] font-bold text-[#164A35] leading-tight" style="font-family: 'Playfair Display', serif;">Tambah Meja Baru</h3>
                        <p class="text-[13px] text-[#777873]">Buat QR code instan untuk meja baru.</p>
                    </div>
                </div>

                <div class="mb-8 relative z-10">
                    <label class="block text-[12px] font-bold text-[#777873] mb-2 uppercase tracking-widest">Nama / Nomor Meja</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-hashtag text-[#C5DBC5]"></i>
                        </div>
                        <input x-ref="tableInput" type="text" x-model="newTableId" @keydown.enter="addTableFromModal" placeholder="Misal: Meja 07" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-[16px] pl-10 pr-4 py-3.5 text-[15px] font-bold text-[#202522] placeholder:font-medium placeholder:text-[#C5DBC5] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none transition-all shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]">
                    </div>
                </div>

                <div class="flex gap-3 relative z-10">
                    <button @click="showAddTableModal = false" class="w-1/3 py-3.5 rounded-[14px] text-[14px] font-bold text-[#777873] bg-white border border-[#E3E1DC] hover:bg-[#F8F7F3] transition-colors text-center">Batal</button>
                    <button @click="addTableFromModal" class="flex-grow py-3.5 rounded-[14px] text-[14px] font-bold bg-[#164A35] text-white shadow-[0_4px_12px_rgba(22,74,53,0.2)] hover:bg-[#0f3526] hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                        Generate QR <i class="fas fa-arrow-right text-[12px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: ORDER DETAIL -->
        <div x-show="showOrderDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div @click="showOrderDetailModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[500px] max-h-[90vh] rounded-2xl shadow-xl flex flex-col overflow-hidden" @keydown.escape.window="showOrderDetailModal = false" x-show="selectedOrder">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="font-sans font-bold text-xl text-primary flex items-center gap-2" x-text="'Struk Pesanan #' + (selectedOrder ? selectedOrder.id : '')"></h3>
                        <p class="text-xs text-gray-500 font-mono mt-1" x-text="selectedOrder ? selectedOrder.time : ''"></p>
                    </div>
                    <button @click="showOrderDetailModal = false" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-white border border-transparent hover:border-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Body (Scrollable) -->
                <div class="p-6 flex-grow overflow-y-auto font-mono">
                    <template x-if="selectedOrder">
                        <div>
                            <!-- Customer Info -->
                            <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-gray-200">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pelanggan</p>
                                    <p class="text-base font-bold text-textdark flex items-center gap-2"><i class="fas fa-user-circle text-primary"></i> <span x-text="selectedOrder.customer"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tipe Pesanan</p>
                                    <p class="text-base font-bold text-textdark" x-text="selectedOrder.type + (selectedOrder.type === 'Dine-in' || selectedOrder.type === 'Dine In' ? ' (' + selectedOrder.table + ')' : '')"></p>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="mb-4">
                                <div class="flex justify-between text-[10px] text-gray-400 font-bold uppercase mb-3 px-1">
                                    <span>Item</span>
                                    <div class="flex gap-4 w-32 justify-end">
                                        <span class="w-8 text-center">Qty</span>
                                        <span class="w-20 text-right">Subtotal</span>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <template x-for="item in selectedOrder.items">
                                        <div class="flex justify-between text-sm items-center bg-gray-50/50 p-2 rounded-lg border border-gray-100">
                                            <div class="flex flex-col pr-2 flex-grow">
                                                <span x-text="item.name" class="font-bold text-gray-700"></span>
                                                <span x-show="item.notes" class="text-[11px] text-red-500 italic mt-0.5"><i class="fas fa-comment-dots text-[10px] mr-1"></i><span x-text="item.notes"></span></span>
                                            </div>
                                            <div class="flex gap-4 w-32 justify-end flex-shrink-0 items-center">
                                                <span class="w-8 text-center font-bold text-gray-600 bg-white border border-gray-200 rounded px-1 py-0.5" x-text="item.qty + 'x'"></span>
                                                <span class="w-20 text-right font-bold text-primary" x-text="formatRp(item.price * item.qty)"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Payment Info -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center text-sm mb-2 text-gray-500">
                                    <span>Metode Pembayaran</span>
                                    <span class="font-bold text-textdark bg-[#00569c]/10 text-[#00569c] px-2 py-0.5 rounded text-xs"><i class="fas fa-qrcode mr-1"></i>QRIS</span>
                                </div>
                                <div class="flex justify-between items-center text-lg mt-4">
                                    <span class="font-bold text-gray-600">Total Harga</span>
                                    <span class="font-extrabold text-2xl text-primary" x-text="formatRp(selectedOrder.total)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Footer Actions -->
                <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex gap-3">
                    <button @click="showOrderDetailModal = false" class="flex-grow py-3 rounded-xl text-sm font-bold text-gray-500 border border-gray-200 bg-white hover:bg-gray-50 transition">
                        Tutup
                    </button>
                    <button @click="window.print()" class="flex-grow py-3 rounded-xl text-sm font-bold bg-primary text-white shadow hover:bg-[#154660] transition flex justify-center items-center gap-2">
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
            <div class="relative bg-[#F8F7F3] w-full sm:w-[520px] rounded-[24px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col border border-[#E3E1DC] overflow-hidden" 
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-6 bg-[#F8F7F3] border-b border-[#E3E1DC] flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-[14px] bg-[#D97A32] text-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fas fa-bell text-xl animate-pulse"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[18px] font-bold text-[#D97A32] leading-tight mb-1">Pesanan Baru Masuk!</h2>
                            <p class="text-[13px] text-[#777873]">Baru saja</p>
                        </div>
                    </div>
                    
                    <!-- Timer Badge -->
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-[10px] text-[15px] font-mono font-bold shrink-0 transition-colors"
                         :class="{
                            'bg-[#D97A32] text-white': incomingTimer > 15,
                            'bg-red-500 text-white animate-pulse': incomingTimer <= 15
                         }">
                        <i class="fas fa-stopwatch text-sm"></i>
                        <span x-text="formatTime(incomingTimer)"></span>
                    </div>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 bg-white relative">
                    <!-- Queue info -->
                    <div x-show="incomingOrderQueue.length > 0" class="absolute top-0 inset-x-0 bg-[#F7E5D2] text-[#D97A32] text-[12px] font-bold py-1 text-center">
                        <span x-text="incomingOrderQueue.length"></span> pesanan menunggu antrean
                    </div>

                    <!-- Order Number & Type -->
                    <div class="flex gap-4 items-center mt-2 mb-6">
                        <div class="w-12 h-12 rounded-[14px] bg-[#F8F7F3] border border-[#E3E1DC] text-[#202522] flex items-center justify-center shrink-0">
                            <i class="fas fa-shopping-bag text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[18px] font-bold text-[#202522] mb-1">Pesanan #<span x-text="activeIncomingOrder?.id"></span></h3>
                            <p class="text-[13px] text-[#777873]">Takeaway • <span x-text="activeIncomingOrder?.customer"></span></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-[#E3E1DC] my-4"></div>

                    <!-- Items -->
                    <div class="flex flex-col gap-4 max-h-[30vh] overflow-y-auto hide-scroll py-2">
                        <template x-for="(item, idx) in activeIncomingOrder?.items" :key="idx">
                            <div class="flex items-center gap-4">
                                <img :src="item.image || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-[12px] object-cover bg-gray-100 shrink-0">
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-[14px] font-bold text-[#202522] truncate" x-text="item.name"></h4>
                                </div>
                                <div class="text-[13px] font-bold text-[#777873] w-8 text-center shrink-0" x-text="item.qty + 'x'"></div>
                                <div class="text-[14px] font-bold text-[#202522] w-24 text-right shrink-0" x-text="formatRp(item.price * item.qty)"></div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t border-dashed border-[#E3E1DC] my-4"></div>

                    <!-- Total & Payment -->
                    <div class="flex justify-between items-end mb-2 mt-4">
                        <div>
                            <p class="text-[13px] font-bold text-[#202522] mb-1">Total Pesanan</p>
                            <p class="text-[26px] font-bold text-[#164A35]" x-text="activeIncomingOrder ? formatRp(activeIncomingOrder.total) : '0'"></p>
                        </div>
                        
                        <!-- Payment Badge -->
                        <div class="bg-[#DDEBDD] rounded-[12px] px-4 py-2.5 flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-[#164A35] mt-0.5"></i>
                            <div>
                                <p class="text-[13px] font-bold text-[#164A35] leading-none mb-1.5">Pembayaran Lunas</p>
                                <p class="text-[11px] font-medium text-[#164A35]/80 leading-none">Dibayar via QRIS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 bg-[#F8F7F3] border-t border-[#E3E1DC]">
                    <!-- State: Confirm Reject -->
                    <div x-show="incomingOrderState === 'reject_confirm'" class="flex flex-col gap-4">
                        <p class="text-center text-[15px] font-bold text-[#202522]">Tolak pesanan #<span x-text="activeIncomingOrder?.id"></span>?</p>
                        <div class="flex gap-3">
                            <button @click="incomingOrderState = 'new'" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-white border border-[#E3E1DC] text-[#777873] hover:bg-gray-50 transition-colors">Batal</button>
                            <button @click="confirmRejectOrder(false)" class="flex-1 py-3.5 rounded-[14px] font-bold text-[14px] bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition-colors">Ya, Tolak Pesanan</button>
                        </div>
                    </div>

                    <!-- State: New/Normal -->
                    <div x-show="incomingOrderState === 'new'" class="flex gap-4">
                        <button @click="incomingOrderState = 'reject_confirm'" class="w-[140px] shrink-0 py-4 rounded-[14px] font-bold text-[14px] bg-transparent border border-[#E3E1DC] text-[#777873] hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-colors">
                            Tolak Pesanan
                        </button>
                        <button @click="acceptOrder()" class="flex-1 py-4 rounded-[14px] font-bold text-[15px] bg-[#164A35] text-white hover:bg-[#0f3526] transition-colors shadow-[0_4px_12px_rgba(22,74,53,0.2)] flex items-center justify-center gap-2">
                            <i class="fas fa-coffee"></i> Terima & Siapkan
                        </button>
                    </div>

                    <!-- State: Accepting -->
                    <div x-show="incomingOrderState === 'accepting'" class="flex justify-center items-center py-4">
                        <i class="fas fa-spinner fa-spin text-[#164A35] text-2xl"></i>
                        <span class="ml-3 font-bold text-[#164A35]">Menerima pesanan...</span>
                    </div>

                    <!-- State: Accepted -->
                    <div x-show="incomingOrderState === 'accepted'" class="flex flex-col justify-center items-center py-2">
                        <div class="w-10 h-10 bg-[#DDEBDD] text-[#164A35] rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-check text-xl"></i>
                        </div>
                        <span class="font-bold text-[#164A35]">Pesanan Diterima!</span>
                        <span class="text-[13px] text-[#777873]">Sedang disiapkan...</span>
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
                        <p class="text-[12px] font-medium text-[#777873] flex items-center justify-center gap-1.5">
                            <i class="far fa-clock"></i> Pesanan otomatis ditolak dalam <span x-text="incomingTimer" class="font-mono font-bold"></span> detik
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        window.INITIAL_DATA = {
            menu: @json($menuItems ?? []),
            orders: @json($orders ?? []),
            tables: @json($tables ?? []),
            shop: @json($shop ?? null),
            user: @json(auth()->user()),
            users: @json($users ?? [])
        };

        const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                currentTab: localStorage.getItem('activeDashboardTab') || 'orders',
                showAddMenuModal: false,
                showAddTableModal: false,
                showAddCrewModal: false,
                users: window.INITIAL_DATA.users || [],
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
                activeOrderFilter: 'process',
                searchQuery: '',
                newMenu: { id: null, name: '', price: '', desc: '', categoryId: '' },
                showBulkUpload: false,
                draftMenus: [],
                categories: ['Coffee', 'Pastry', 'Beverages', 'Foods', 'Snacks', 'Sweets'],
                initBulkUpload() {
                    this.showBulkUpload = true;
                    if (this.draftMenus.length === 0) {
                        this.addDraftRow();
                    }
                },
                addDraftRow() {
                    this.draftMenus.push({ id: null, name: '', price: '', categoryId: this.categories[0], imagePreview: null, imageFile: null });
                },
                removeDraftRow(index) {
                    this.draftMenus.splice(index, 1);
                    if (this.draftMenus.length === 0) this.addDraftRow();
                },
                
                handleCSVUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const text = e.target.result;
                        const lines = text.split('\n');
                        let added = 0;
                        
                        for (let i = 1; i < lines.length; i++) {
                            const line = lines[i].trim();
                            if (!line) continue;
                            
                            const cols = line.split(',');
                            if (cols.length >= 3) {
                                const name = cols[0].replace(/^"|"$/g, '').trim();
                                const category = cols[1].replace(/^"|"$/g, '').trim();
                                const priceStr = cols[2].replace(/[^0-9]/g, '');
                                const price = parseInt(priceStr) || 0;
                                
                                let validCategory = this.categories[0];
                                const catLower = category.toLowerCase();
                                const matchedCat = this.categories.find(c => c.toLowerCase() === catLower);
                                if (matchedCat) validCategory = matchedCat;
                                
                                this.draftMenus.unshift({
                                    id: null,
                                    name: name,
                                    categoryId: validCategory,
                                    price: price,
                                    imagePreview: null,
                                    imageFile: null
                                });
                                added++;
                            }
                        }
                        
                        if (added > 0) {
                            this.addToast(added + ' item diimpor dari CSV', 'success');
                            if (this.draftMenus.length > 0 && this.draftMenus[this.draftMenus.length - 1].name === '') {
                                this.draftMenus.pop();
                            }
                        } else {
                            this.addToast('Format CSV tidak valid atau kosong', 'error');
                        }
                        
                        event.target.value = '';
                    };
                    reader.readAsText(file);
                },
handleDraftImageUpload(event, index) {
                    const file = event.target.files[0];
                    if (!file) return;
                    if (file.size > 2 * 1024 * 1024) {
                        this.addToast('Image max 2MB', 'error');
                        return;
                    }
                    this.draftMenus[index].imageFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.draftMenus[index].imagePreview = e.target.result; };
                    reader.readAsDataURL(file);
                },
                saveBulkMenu() {
                    const validItems = this.draftMenus.filter(m => m.name && m.price);
                    if (validItems.length === 0) {
                        this.addToast('Minimal isi Nama dan Harga', 'error');
                        return;
                    }
                    let formData = new FormData();
                    validItems.forEach((item, index) => {
                        if (item.id) formData.append(`items[${index}][id]`, item.id);
                        formData.append(`items[${index}][name]`, item.name);
                        formData.append(`items[${index}][price]`, item.price);
                        formData.append(`items[${index}][category_name]`, item.categoryId);
                        if (item.imageFile) formData.append(`images[${index}]`, item.imageFile);
                    });
                    fetch('/admin/api/menu/bulk', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.addToast('Berhasil upload menu!', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }).catch(err => this.addToast('Error saving bulk menu', 'error'));
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
                settings: {
                    name: '',
                    slug: '',
                    primary_color: '#1E5A7A',
                    logoPreview: '',
                    logoFile: null
                },
                isSavingSettings: false,
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
                    let items = this.menuItems;
                    if (this.activeMenuFilter !== 'all') {
                        items = items.filter(m => m.categoryId === this.activeMenuFilter);
                    }
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(m => m.name.toLowerCase().includes(q) || m.desc.toLowerCase().includes(q));
                    }
                    return items;
                },
                get filteredOrders() {
                    let filtered = this.orders;
                    if (this.activeOrderFilter === 'process') {
                        filtered = filtered.filter(o => o.status === 'In Progress' || o.status === 'Ready' || o.status === 'Masuk');
                    } else if (this.activeOrderFilter === 'completed') {
                        filtered = filtered.filter(o => o.status === 'Completed');
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
                    { id: 'analytics', name: 'Dashboard Analytics', icon: 'fas fa-chart-pie' },
                    { id: 'orders', name: 'Live Orders', icon: 'fas fa-receipt' },
                    { id: 'menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'qr', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'crew', name: 'Crew Management', icon: 'fas fa-users' },
                    { id: 'settings', name: 'Toko & Branding', icon: 'fas fa-store' },
                ],
                tables: [],
                getQRUrl(tableCode, token = '') {
                    const slug = window.INITIAL_DATA.shop?.slug || 'menu';
                    const baseUrl = window.location.origin + '/' + slug;
                    const url = `${baseUrl}?table=${tableCode}${token ? '&token='+token : ''}`;
                    return `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(url)}`;
                },
                chartInstance: null,
                get totalRevenue() {
                    return this.orders.filter(o => o.status === 'Completed').reduce((sum, o) => sum + (o.total || 0), 0);
                },
                get totalOrders() {
                    return this.orders.length;
                },
                get totalSuccess() {
                    return this.orders.filter(o => o.status === 'Completed').length;
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
                init() {
                    this.tables = [
                        { id: 'Meja 01', qr: this.getQRUrl('01') },
                        { id: 'Meja 02', qr: this.getQRUrl('02') },
                        { id: 'Meja 03', qr: this.getQRUrl('03') },
                        { id: 'Meja 04', qr: this.getQRUrl('04') },
                        { id: 'Meja 05', qr: this.getQRUrl('05') },
                        { id: 'Meja 06', qr: this.getQRUrl('06') },
                    ];
                    this.fetchLiveOrders(true);
                    
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'bitten_orders') {
                            this.fetchLiveOrders();
                        }
                    });

                    this.$watch('currentTab', (val) => {
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });
                },
                initChart() {
                    const ctx = document.getElementById('salesChart');
                    if (ctx && !this.chartInstance) {
                        this.chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: [{
                                    label: 'Revenue',
                                    data: [1.2, 1.9, 1.5, 2.1, 1.8, 3.2, 4.25],
                                    borderColor: '#8C5D3A',
                                    backgroundColor: 'rgba(140, 93, 58, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#8C5D3A',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { 
                                    y: { 
                                        beginAtZero: true, 
                                        grid: { borderDash: [5, 5] },
                                        ticks: { callback: function(value) { return value + 'M'; } }
                                    },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                },
                orders: [],
                menuItems: [],
                init() {
                    this.$watch('currentTab', (val) => {
                        localStorage.setItem('activeDashboardTab', val);
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });

                    this.loadMenu();
                    
                    this.tables = window.INITIAL_DATA.tables.map(t => ({
                        id: t.name,
                        qr: t.qr_code_url || this.getQRUrl(t.name)
                    }));
                    
                    if (window.INITIAL_DATA.shop) {
                        this.settings.name = window.INITIAL_DATA.shop.name || '';
                        this.settings.slug = window.INITIAL_DATA.shop.slug || '';
                        this.settings.primary_color = window.INITIAL_DATA.shop.primary_color || '#1E5A7A';
                        if (window.INITIAL_DATA.shop.logo_url) {
                            this.settings.logoPreview = '/storage/' + window.INITIAL_DATA.shop.logo_url + '?v=' + new Date(window.INITIAL_DATA.shop.updated_at).getTime();
                        }
                    }
                    
                    this.fetchLiveOrders(true);
                    
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'bitten_orders' || e.key === 'bitten_menu') {
                            // Can't really sync across tabs without websockets in Laravel, 
                            // but we can leave this or reload page.
                            window.location.reload();
                        }
                    });

                    this.$watch('currentTab', (val) => {
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });
                },
                loadMenu() {
                    this.menuItems = window.INITIAL_DATA.menu.map(m => ({
                        ...m,
                        categoryId: m.category_name,
                        desc: m.description,
                        image: m.image_url ? ('/storage/' + m.image_url + '?v=' + new Date(m.updated_at).getTime()) : null,
                        tags: m.tags || []
                    }));
                },
                editMenu(item) {
                    this.newMenu = { id: item.id, name: item.name, price: item.price, desc: item.desc, categoryId: item.categoryId || 'beverages' };
                    this.showAddMenuModal = true;
                },
                deleteMenu(id) {
                    if (confirm("Yakin ingin menghapus menu ini?")) {
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
                                        item.image = '/storage/' + data.menu.image_url + '?v=' + Date.now();
                                    }
                                }
                            } else {
                                data.menu.categoryId = data.menu.category_name;
                                data.menu.desc = data.menu.description;
                                this.menuItems.unshift(data.menu);
                            }
                            this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '' };
                            if (this.$refs.menuImageInput) this.$refs.menuImageInput.value = '';
                            this.showAddMenuModal = false;
                        }
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
                        this.incomingTimer = 45;
                        clearInterval(this.incomingTimerInterval);
                        
                        this.incomingTimerInterval = setInterval(() => {
                            if (['new', 'reject_confirm'].includes(this.incomingOrderState)) {
                                this.incomingTimer--;
                                if (this.incomingTimer <= 0) {
                                    this.confirmRejectOrder(true);
                                }
                            }
                        }, 1000);
                    }
                },
                acceptOrder() {
                    this.incomingOrderState = 'accepting';
                    setTimeout(() => {
                        this.incomingOrderState = 'accepted';
                        this.updateStatus(this.activeIncomingOrder.id, 'In Progress');
                        setTimeout(() => this.closeActiveOrder(), 1500);
                    }, 800);
                },
                confirmRejectOrder(isAuto = false) {
                    this.incomingOrderState = 'rejected';
                    this.updateStatus(this.activeIncomingOrder.id, 'Dibatalkan');
                    if (isAuto) {
                        this.addToast(`Pesanan #${this.activeIncomingOrder.id} telah kedaluwarsa.`, 'error');
                    }
                    setTimeout(() => this.closeActiveOrder(), 1500);
                },
                closeActiveOrder() {
                    this.activeIncomingOrder = null;
                    clearInterval(this.incomingTimerInterval);
                    setTimeout(() => this.processIncomingQueue(), 400);
                },
                fetchLiveOrders(isInit = false) {
                    const dbOrders = window.INITIAL_DATA.orders.map(o => ({
                        id: o.id,
                        customer: o.customer_name || ('Meja ' + (o.table ? o.table.name : '-')),
                        status: o.status,
                        total: o.total_price,
                        time: o.created_at,
                        items: o.items.map(i => ({ 
                            name: i.product.name, 
                            qty: i.quantity, 
                            price: i.price, 
                            image: i.product.image_url ? ('/storage/' + i.product.image_url) : null,
                            desc: i.product.description
                        }))
                    }));
                    
                    const newIncomingOrders = dbOrders.filter(o => o.status === 'Masuk' && !this.orders.find(old => old.id === o.id));
                    
                    if (!isInit && newIncomingOrders.length > 0) {
                        const chime = document.getElementById('chime-sound');
                        if (chime) {
                            chime.currentTime = 0;
                            chime.play().catch(e => console.log('Audio autoplay blocked', e));
                        }
                        
                        newIncomingOrders.forEach(o => this.incomingOrderQueue.push(o));
                        this.processIncomingQueue();
                    }
                    
                    this.orders = dbOrders;
                },
                addTableFromModal() {
                    if (!this.newTableId.trim()) {
                        this.addToast('Silakan masukkan nama/nomor meja!', 'error');
                        return;
                    }
                    
                    const tableNumStr = this.newTableId.replace(/[^a-zA-Z0-9]/g, ''); // bersihkan untuk URL
                    const tableName = this.newTableId.trim();
                    const qrUrl = this.getQRUrl(tableNumStr);
                    
                    fetch('/admin/api/table', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ name: tableName, qr_code_url: qrUrl })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        if(data.success) {
                            this.tables.push({
                                id: data.table.name,
                                qr: data.table.qr_code_url
                            });
                            this.newTableId = '';
                            this.showAddTableModal = false;
                            this.addToast('Meja berhasil ditambahkan', 'success');
                        }
                    })
                    .catch(err => {
                        this.addToast(err.message || 'Gagal menyimpan meja', 'error');
                    });
                },
                printQR(table) {
                    if(window.printQRWindow) window.printQRWindow(table);
                },
                resetQR(table) {
                    if(confirm(`Yakin ingin mereset/mengganti URL QR Code untuk ${table.id}? URL lama tidak akan bisa diakses lagi.`)) {
                        const randomToken = Math.random().toString(36).substring(2, 8);
                        const tableNum = table.id.replace('Meja ', '');
                        const newQrUrl = this.getQRUrl(tableNum, randomToken);
                        
                        fetch('/admin/api/table', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                            'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ name: table.id, qr_code_url: newQrUrl })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                table.qr = newQrUrl;
                                this.addToast('QR Code di-reset!', 'success');
                            }
                        });
                    }
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
                deleteCrew(id) {
                    if(!confirm('Hapus crew ini?')) return;
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
                    formData.append('name', this.settings.name);
                    formData.append('slug', this.settings.slug);
                    formData.append('primary_color', this.settings.primary_color);
                    if (this.settings.logoFile) {
                        formData.append('logo', this.settings.logoFile);
                    }
                    
                    fetch('/admin/api/settings', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingSettings = false;
                        if (data.success) {
                            this.addToast('Pengaturan berhasil disimpan!', 'success');
                            if (data.logo_url) {
                                this.settings.logoPreview = '/storage/' + data.logo_url;
                                this.settings.logoFile = null;
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
    <a :href="'/' + settings.slug" target="_blank" class="fixed bottom-10 right-10 bg-[#1E5A7A] text-white p-4 rounded-full shadow-2xl flex items-center justify-center hover:scale-105 hover:bg-[#154660] transition-all z-[100] group border-4 border-white">
        <i class="fas fa-store text-xl"></i>
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-[200px] transition-all duration-500 ease-in-out pl-0 group-hover:pl-3 font-bold text-sm">Lihat Web Pelanggan</span>
    </a>
</body>
</html>



