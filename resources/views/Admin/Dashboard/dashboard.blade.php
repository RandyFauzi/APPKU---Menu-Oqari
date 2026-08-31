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
    <link rel="icon" type="image/png" id="favicon" href="{{ (isset($shop) && $shop->logo_url) ? asset('uploads/' . $shop->logo_url) : asset('Pavico.webp') }}">
    
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>`n</head>
<body class="font-sans text-textdark h-screen flex overflow-hidden" x-data="dashboardApp()">
    

    <!-- Audio untuk notifikasi pesanan masuk -->
    <audio id="chime-sound" src="{{ asset('Assest/notif_orderan_masuk.mp3') }}" preload="auto"></audio>

<!-- Sidebar -->
    <aside class="w-64 flex flex-col shrink-0 border-r border-brewlyborder p-6 gap-6 h-full bg-brewlybg">
        <div class="flex items-center gap-3 mb-2">
            @if(isset($shop) && $shop->logo_url)
                <img src="{{ asset('uploads/' . $shop->logo_url) }}" alt="Logo" id="sidebar-logo" class="w-10 h-10 rounded-md object-cover shadow-sm">
            @else
                <img src="{{ asset('Pavico.webp') }}" alt="Logo" id="sidebar-logo" class="w-10 h-10 object-contain">
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
        
        <!-- Store Status Toggle -->
        <div class="mt-auto mb-2 bg-[#F8F7F3] p-4 rounded-2xl border border-[#E3E1DC] flex flex-col gap-2">
            <div>
                <label class="block text-[13px] font-bold text-[#202522]">Toko Buka Sekarang?</label>
                <p class="text-[10px] text-[#777873] leading-tight mt-0.5">Matikan untuk menutup toko manual.</p>
            </div>
            <div @click="settings.is_open = !settings.is_open; saveSettings()" class="relative inline-flex items-center cursor-pointer mt-1">
                <div class="w-12 h-6 rounded-full transition-colors duration-300 ease-in-out relative shadow-inner" :class="settings.is_open ? 'bg-[#164A35]' : 'bg-gray-300'">
                    <div class="w-4 h-4 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out absolute top-[4px] left-[4px]" :class="settings.is_open ? 'translate-x-6' : 'translate-x-0'"></div>
                </div>
                <span class="ml-2 text-xs font-bold" :class="settings.is_open ? 'text-[#164A35]' : 'text-gray-400'" x-text="settings.is_open ? 'Buka' : 'Tutup'"></span>
            </div>
        </div>

        <!-- Promo Card -->
        <div class="bg-[#F8F7F3] rounded-2xl p-5 relative overflow-hidden flex flex-col gap-3 mb-2 border border-[#E3E1DC]">
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
        @include('Admin.Dashboard.tabs.report')
        @include('Admin.Dashboard.tabs.qr')
        @include('Admin.Dashboard.tabs.crew')
        @include('Admin.Dashboard.tabs.shifts')
        @include('Admin.Dashboard.tabs.logs')
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

        <!-- MODAL: RESET QR CONFIRM -->
        <div x-show="showResetQRModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-[#164A35]/40 backdrop-blur-sm" x-transition.opacity>
            <div class="bg-white rounded-[24px] w-full max-w-[400px] p-8 shadow-[0_20px_60px_rgba(22,74,53,0.15)] relative overflow-hidden" @click.away="showResetQRModal = false" x-transition>
                <div class="absolute -top-16 -right-16 w-32 h-32 bg-[#D97A32]/10 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-[#164A35]/5 rounded-full blur-2xl"></div>
                
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-[#D97A32]/10 text-[#D97A32] rounded-full flex items-center justify-center text-2xl mb-4 border-4 border-white shadow-sm">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-[22px] font-bold text-[#164A35] mb-2" style="font-family: 'Playfair Display', serif;">Reset QR Code?</h3>
                    <p class="text-[14px] text-[#777873] leading-relaxed mb-6">Yakin ingin mereset/mengganti URL QR Code untuk <strong class="text-[#202522]" x-text="qrTableToReset?.id"></strong>? <br> <span class="text-red-500 font-medium">URL lama tidak akan bisa diakses lagi.</span></p>
                    
                    <div class="flex gap-3 w-full">
                        <button @click="showResetQRModal = false" class="w-1/2 py-3 rounded-[14px] text-[14px] font-bold text-[#777873] bg-[#F8F7F3] hover:bg-[#E3E1DC] transition-colors">Batal</button>
                        <button @click="confirmResetQR" :disabled="isResettingQR" class="w-1/2 py-3 rounded-[14px] text-[14px] font-bold bg-[#D97A32] text-white shadow-sm hover:bg-[#b8662a] transition-all flex items-center justify-center gap-2 disabled:opacity-70">
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
            <div class="relative bg-[#F8F7F3] w-full sm:w-[520px] rounded-[24px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] flex flex-col border border-[#E3E1DC] overflow-hidden" @keydown.escape.window="showOrderDetailModal = false" x-show="selectedOrder"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95">

                <!-- Header -->
                <div class="p-6 bg-[#F8F7F3] border-b border-[#E3E1DC] flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="w-12 h-12 rounded-[14px] bg-[#164A35] text-white flex items-center justify-center shadow-sm shrink-0">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-[18px] font-bold text-[#164A35] leading-tight mb-1" x-text="'Struk Pesanan #' + (selectedOrder ? selectedOrder.id : '')"></h2>
                            <p class="text-[13px] font-mono text-[#777873]" x-text="selectedOrder ? selectedOrder.time : ''"></p>
                        </div>
                    </div>

                    <button @click="showOrderDetailModal = false" class="w-10 h-10 flex items-center justify-center rounded-[12px] bg-white border border-[#E3E1DC] text-[#777873] hover:text-[#202522] hover:bg-gray-50 transition-colors shrink-0">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 bg-white relative">
                    <!-- Order Number & Type -->
                    <div class="flex gap-4 items-center mt-2 mb-6">
                        <div class="w-12 h-12 rounded-[14px] bg-[#F8F7F3] border border-[#E3E1DC] text-[#202522] flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-[18px] font-bold text-[#202522] mb-1" x-text="selectedOrder?.customer"></h3>
                            <p class="text-[13px] text-[#777873]" x-text="(selectedOrder?.type || 'Takeaway') + ((selectedOrder?.type === 'Dine-in' || selectedOrder?.type === 'Dine In') && selectedOrder?.table ? ' (' + selectedOrder.table + ')' : '')"></p>
                        </div>
                    </div>

                    <div class="border-t border-dashed border-[#E3E1DC] my-4"></div>

                    <!-- Items -->
                    <div class="flex flex-col gap-4 max-h-[40vh] overflow-y-auto hide-scroll py-2">
                        <template x-for="(item, idx) in selectedOrder?.items" :key="idx">
                            <div class="flex items-center gap-4">
                                <img :src="item.image || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-[12px] object-cover bg-gray-100 shrink-0">
                                <div class="flex-grow min-w-0">
                                    <h4 class="text-[14px] font-bold text-[#202522] truncate" x-text="item.name"></h4>
                                    <p x-show="item.notes" class="text-[11px] text-[#D97A32] mt-0.5"><i class="fas fa-comment-dots mr-1"></i> <span x-text="item.notes"></span></p>
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
                            <p class="text-[13px] font-bold text-[#202522] mb-1">Total Harga</p>
                            <p class="text-[26px] font-bold text-[#164A35]" x-text="selectedOrder ? formatRp(selectedOrder.total) : '0'"></p>
                        </div>

                        <!-- Payment Badge -->
                        <div class="bg-[#DDEBDD] rounded-[12px] px-4 py-2.5 flex items-start gap-2.5">
                            <i class="fas fa-check-circle text-[#164A35] mt-0.5"></i>
                            <div>
                                <p class="text-[13px] font-bold text-[#164A35] leading-none mb-1.5">Pembayaran</p>
                                <p class="text-[11px] font-medium text-[#164A35]/80 leading-none">QRIS / Online</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 bg-[#F8F7F3] border-t border-[#E3E1DC] flex gap-3">
                    <button @click="showOrderDetailModal = false" class="w-1/3 py-3.5 rounded-[16px] text-[15px] font-bold text-[#777873] bg-white border border-[#E3E1DC] hover:text-[#202522] hover:bg-gray-50 transition-all flex justify-center items-center">
                        Tutup
                    </button>
                    <button @click="window.print()" class="w-2/3 py-3.5 rounded-[16px] text-[15px] font-bold bg-[#164A35] text-white shadow-sm hover:bg-[#123A2A] hover:-translate-y-0.5 transition-all flex justify-center items-center gap-2">
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
                showResetQRModal: false,
                qrTableToReset: null,
                isResettingQR: false,
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
                            this.addToast(added + ' item diimpor dari CSV. Sedang menyimpan...', 'info');
                            if (this.draftMenus.length > 0 && this.draftMenus[this.draftMenus.length - 1].name === '') {
                                this.draftMenus.pop();
                            }
                            this.saveBulkMenu();
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
                    name: window.INITIAL_DATA.shop?.name || '',
                    slug: window.INITIAL_DATA.shop?.slug || '',
                    primary_color: window.INITIAL_DATA.shop?.primary_color || '#1E5A7A',
                    logoPreview: window.INITIAL_DATA.shop?.logo_url ? '/uploads/' + window.INITIAL_DATA.shop.logo_url : '',
                    logoFile: null,
                    slogan: window.INITIAL_DATA.shop?.slogan || '',
                    is_open: window.INITIAL_DATA.shop?.is_open ?? true,
                    theme_style: ['list', 'grid'].includes(window.INITIAL_DATA.shop?.theme_style) ? window.INITIAL_DATA.shop?.theme_style : 'list',
                    font_family: window.INITIAL_DATA.shop?.font_family || 'poppins',
                    instagram_link: window.INITIAL_DATA.shop?.instagram_link || '',
                    whatsapp_number: window.INITIAL_DATA.shop?.whatsapp_number || '',
                    maps_link: window.INITIAL_DATA.shop?.maps_link || '',
                    is_banner_active: window.INITIAL_DATA.shop?.is_banner_active ?? true,
                      banners: (window.INITIAL_DATA.shop?.banners || []).map(b => b ? '/uploads/' + b : null).concat([null, null, null]).slice(0, 3),
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
                      { id: 'report', name: 'Laporan Keuangan', icon: 'fas fa-book' },
                    { id: 'orders', name: 'Live Orders', icon: 'fas fa-receipt' },
                    { id: 'menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'qr', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'crew', name: 'Crew Management', icon: 'fas fa-users' },
                    { id: 'shifts', name: 'Jadwal Shift', icon: 'fas fa-calendar-alt' },
                    { id: 'logs', name: 'Log Aktivitas', icon: 'fas fa-history' },
                    { id: 'settings', name: 'Toko & Branding', icon: 'fas fa-store' },
                ],
                                tables: [],
                reportPeriod: 'all',
                formatNum(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                },
                get reportData() {
                    const now = new Date();
                    const startOfToday = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const startOfWeek = new Date(startOfToday);
                    startOfWeek.setDate(startOfToday.getDate() - now.getDay());
                    const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    const completed = this.orders.filter(o => o.status === 'Completed');
                    let daily = { count: 0, total: 0 };
                    let weekly = { count: 0, total: 0 };
                    let monthly = { count: 0, total: 0 };
                    completed.forEach(o => {
                        const d = new Date(o.created_at);
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
                    return this.orders.filter(o => {
                        if (o.status !== 'Completed') return false;
                        const d = new Date(o.created_at);
                        if (this.reportPeriod === 'daily') return d >= startOfToday;
                        if (this.reportPeriod === 'weekly') return d >= startOfWeek;
                        if (this.reportPeriod === 'monthly') return d >= startOfMonth;
                        return true;
                    });
                },
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
                    
                    this.loadMenu();
                    this.fetchLiveOrders(true);
                    this.fetchShifts();
                    this.fetchLogs();
                    
                    document.body.addEventListener('click', unlockAudio, { once: true });
                    document.body.addEventListener('touchstart', unlockAudio, { once: true });
                    document.body.addEventListener('keydown', unlockAudio, { once: true });
                    
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
                            this.settings.logoPreview = '/uploads/' + window.INITIAL_DATA.shop.logo_url + '?v=' + new Date(window.INITIAL_DATA.shop.updated_at).getTime();
                        }
                    }
                    
                    this.fetchLiveOrders(true);
                    
                    // Listen for real-time orders via Laravel Reverb
                    if (window.Echo && window.INITIAL_DATA.shop) {
                        window.Echo.private('shop.' + window.INITIAL_DATA.shop.id + '.orders')
                            .listen('OrderCreated', (e) => {
                                console.log('New Order Received via Reverb:', e.order);
                                this.handleNewOrderFromSocket(e.order);
                            });
                    } else {
                        // Fallback polling just in case Echo fails to load
                        setInterval(() => {
                            this.fetchLiveOrders(false);
                        }, 5000);
                    }


                },
                loadMenu() {
                    this.menuItems = window.INITIAL_DATA.menu.map(m => ({
                        ...m,
                        categoryId: m.category_name,
                        desc: m.description,
                        image: m.image_url ? ('/uploads/' + m.image_url + '?v=' + new Date(m.updated_at).getTime()) : null,
                        tags: m.tags || []
                    }));
                },
                editMenu(item) {
                    this.newMenu = { id: item.id, name: item.name, price: item.price, desc: item.desc, categoryId: item.categoryId || 'beverages' };
                    this.showAddMenuModal = true;
                },
                async deleteMenu(id) {
                    const result = await Swal.fire({
                        title: 'Hapus Menu?',
                        text: "Data menu ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#777873',
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
                                        item.image = '/uploads/' + data.menu.image_url + '?v=' + Date.now();
                                    }
                                }
                            } else {
                                data.menu.categoryId = data.menu.category_name;
                                data.menu.desc = data.menu.description;
                                data.menu.image = data.menu.image_url ? '/uploads/' + data.menu.image_url : null;
                                this.menuItems.unshift(data.menu);
                            }
                            this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '' };
                            if (this.$refs.menuImageInput) this.$refs.menuImageInput.value = '';
                            this.showAddMenuModal = false;
                            this.addToast('Menu berhasil disimpan!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan menu', 'error');
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
                        customer: newOrder.customer_name || ('Meja ' + (newOrder.table ? newOrder.table.name : '-')),
                        status: newOrder.status,
                        total: parseFloat(newOrder.total_price),
                        time: newOrder.created_at,
                        items: (newOrder.items || []).map(i => ({
                            name: i.product?.name || 'Produk',
                            qty: i.quantity,
                            price: parseFloat(i.price),
                            image: i.product?.image_url ? ('/uploads/' + i.product.image_url) : null,
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
                async fetchLiveOrders(isInit = false) {
                    try {
                        let sourceOrders = window.INITIAL_DATA.orders;
                        
                        if (!isInit) {
                            try {
                                const res = await fetch('/admin/api/orders/live?t=' + new Date().getTime(), {
                                    headers: {
                                        'Cache-Control': 'no-cache',
                                        'Pragma': 'no-cache'
                                    }
                                });
                                if (res.ok) {
                                    sourceOrders = await res.json();
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
                            status: o.status,
                            total: parseFloat(o.total_price),
                            time: o.created_at,
                            items: (o.items || []).map(i => ({
                                name: i.product?.name || 'Produk',
                                qty: i.quantity,
                                price: parseFloat(i.price),
                                image: i.product?.image_url ? ('/uploads/' + i.product.image_url) : null,
                                desc: i.product?.description || ''
                            }))
                        }));

                        const newIncomingOrders = dbOrders.filter(o => o.status === 'Masuk' && !this.orders.find(old => old.id == o.id));

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
                showAddShiftModal: false,
                newShift: { id: null, user_id: '', date: '', start_time: '', end_time: '', notes: '' },
                async fetchShifts() {
                    try {
                        let res = await fetch('/admin/api/shifts');
                        if (res.ok) this.shifts = await res.json();
                    } catch (e) {}
                },
                async fetchLogs() {
                    try {
                        let res = await fetch('/admin/api/logs');
                        if (res.ok) this.logs = await res.json();
                    } catch (e) {}
                },
                async saveShift() {
                    this.isSaving = true;
                    try {
                        let formData = new FormData();
                        if (this.newShift.id) formData.append('id', this.newShift.id);
                        formData.append('user_id', this.newShift.user_id);
                        formData.append('date', this.newShift.date);
                        formData.append('start_time', this.newShift.start_time);
                        formData.append('end_time', this.newShift.end_time);
                        if (this.newShift.notes) formData.append('notes', this.newShift.notes);

                        let res = await fetch('/admin/api/shifts', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: formData
                        });
                        let data = await res.json();
                        if (data.success) {
                            this.addToast('Jadwal shift berhasil disimpan', 'success');
                            this.showAddShiftModal = false;
                            this.fetchShifts();
                        } else {
                            this.addToast('Gagal menyimpan shift', 'error');
                        }
                    } catch (e) {
                        this.addToast('Terjadi kesalahan', 'error');
                    }
                    this.isSaving = false;
                },
                async deleteShift(id) {
                    const result = await Swal.fire({
                        title: 'Hapus Jadwal Shift?',
                        text: "Data jadwal ini akan dihapus permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#777873',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    });
                    
                    if (!result.isConfirmed) return;
                    try {
                        let res = await fetch(`/admin/api/shifts/${id}`, {
                            method: 'DELETE',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        let data = await res.json();
                        if (data.success) {
                            this.addToast('Shift dihapus', 'success');
                            this.fetchShifts();
                        }
                    } catch (e) {}
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
                        "        h1 { color: #164A35; margin-bottom: 10px; font-size: 28px; font-weight: 800; }\n" +
                        "        h2 { color: #202522; font-size: 44px; margin: 0 0 20px 0; font-weight: 900; }\n" +
                        "        img { width: 220px; height: 220px; margin-bottom: 20px; display: block; margin-left: auto; margin-right: auto; mix-blend-mode: multiply; }\n" +
                        "        p { color: #777873; font-size: 15px; margin: 0; font-weight: 500; }\n" +
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
                        this.isResettingQR = false;
                        if(data.success) {
                            table.qr = newQrUrl;
                            this.showResetQRModal = false;
                            this.qrTableToReset = null;
                            this.addToast('QR Code berhasil di-reset!', 'success');
                        } else {
                            this.addToast(data.message || 'Gagal reset QR', 'error');
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
                        cancelButtonColor: '#777873',
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
                    formData.append('instagram_link', this.settings.instagram_link || '');
                    formData.append('whatsapp_number', this.settings.whatsapp_number || '');
                    formData.append('maps_link', this.settings.maps_link || '');
                    
                    if (this.settings.logoFile) {
                        formData.append('logo', this.settings.logoFile);
                    }
                    formData.append('is_banner_active', this.settings.is_banner_active ? 1 : 0);
                    for (let i = 0; i < 3; i++) {
                        if (this.settings.bannerFiles[i]) {
                            formData.append(`banner_${i}`, this.settings.bannerFiles[i]);
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
                                this.settings.logoPreview = '/uploads/' + data.shop.logo_url;
                                this.settings.logoFile = null;
                            }
                            
                            // Perbarui elemen UI lain
                            const favicon = document.getElementById('favicon');
                            const sidebarLogo = document.getElementById('sidebar-logo');
                            if (data.shop && data.shop.logo_url) {
                                const newLogoUrl = '/uploads/' + data.shop.logo_url;
                                if (favicon) favicon.href = newLogoUrl;
                                if (sidebarLogo) sidebarLogo.src = newLogoUrl;
                            }
                            
                            if (data.shop && data.shop.banners) {
                                this.settings.bannerPaths = data.shop.banners.concat([null, null, null]).slice(0, 3);
                                this.settings.banners = data.shop.banners.map(b => b ? '/uploads/' + b : null).concat([null, null, null]).slice(0, 3);
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
    <a :href="'/' + settings.slug" target="_blank" class="fixed bottom-10 right-10 bg-[#1E5A7A] text-white p-4 rounded-full shadow-2xl flex items-center justify-center hover:scale-105 hover:bg-[#154660] transition-all z-[100] group border-4 border-white">
        <i class="fas fa-store text-xl"></i>
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-[200px] transition-all duration-500 ease-in-out pl-0 group-hover:pl-3 font-bold text-sm">Lihat Web Pelanggan</span>
    </a>
</body>
</html>





