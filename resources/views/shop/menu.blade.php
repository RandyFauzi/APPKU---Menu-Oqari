<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $shop->name }}</title>
    
    <!-- Favicon -->
    @if(isset($shop) && $shop->logo_url)
        <link rel="icon" type="image/png" href="{{ $shop->logo_url }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">
    @endif
    
    <!-- Styles & Scripts (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @php
        $font = $shop->font_family ?? 'poppins';
        if ($font == 'playfair') {
            echo '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">';
            $fontFamily = "'Playfair Display', serif";
        } elseif ($font == 'nunito') {
            echo '<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">';
            $fontFamily = "'Nunito', sans-serif";
        } else {
            echo '<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">';
            $fontFamily = "'Poppins', sans-serif";
        }
    @endphp

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <style>
        :root {
            --color-primary: {{ $shop->primary_color ?? '#1c4532' }};
            --color-secondary: {{ $shop->primary_color ?? '#2d6a4f' }};
        }
        body { font-family: {!! $fontFamily !!}; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .page-transition { transition: all 0.3s ease; }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-page="home" class="antialiased max-w-md mx-auto bg-gray-50 min-h-screen relative shadow-xl overflow-x-hidden pb-24 page-transition">

    @if(!($shop->is_open ?? true))
    <!-- TOKO TUTUP OVERLAY -->
    <div class="fixed inset-0 bg-white/80 backdrop-blur-sm z-[200] flex flex-col items-center justify-center p-6 text-center">
        <i class="fas fa-store-slash text-6xl text-gray-400 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Toko Sedang Tutup</h2>
        <p class="text-gray-500 font-medium">Mohon maaf, toko kami saat ini sedang tidak menerima pesanan.</p>
        <button onclick="window.location.reload()" class="mt-6 bg-primary text-white px-6 py-3 rounded-full font-bold shadow-md hover:bg-opacity-90">Cek Lagi Nanti</button>
    </div>
    @endif

    <!-- SPLASH SCREEN -->
    <div id="splash-screen" class="fixed inset-0 bg-white z-[100] flex flex-col items-center justify-center transition-opacity duration-700">
        <div class="w-28 h-28 flex items-center justify-center p-2 mb-4 animate-bounce">
            <img src="{{ $shop->logo_url ? $shop->logo_url : asset('logo-oqari.webp') }}" alt="Logo" class="w-full h-full object-contain">
        </div>
        <h1 class="text-primary text-3xl font-extrabold tracking-widest uppercase">{{ $shop->name }}</h1>
        <p class="text-gray-500 text-sm mt-1 font-medium tracking-wider uppercase">{{ $shop->slogan ?? 'COFFEE & EATERY' }}</p>
    </div>

    <!-- Header APP (Logo + Search Bar) - Sticky -->
    <div id="sticky-header" class="sticky top-0 z-40 bg-white transition-shadow duration-300">
        <div class="pt-4 pb-2 px-4">
            <div class="flex items-center justify-between px-1 pb-2">
                <div class="flex items-center gap-2">
                    <img src="{{ $shop->logo_url ? $shop->logo_url : asset('logo-oqari.webp') }}" alt="{{ $shop->name }} Logo" class="h-10 w-10 object-contain drop-shadow-sm rounded-lg">
                    <div class="flex flex-col">
                        <span class="font-extrabold text-[15px] leading-tight tracking-tight text-primary uppercase">{{ $shop->name }}</span>
                        <span class="text-[8px] font-bold text-gray-500 tracking-[0.2em] mt-0.5 uppercase">{{ $shop->slogan ?? 'COFFEE & EATERY' }}</span>
                    </div>
                </div>
            </div>

            <!-- Location & Search Bar -->
            <div class="pb-3">
                <div class="bg-white rounded-full p-1.5 flex items-center shadow-[0_2px_8px_-3px_rgba(0,0,0,0.1)] border border-gray-200 focus-within:ring-2 focus-within:ring-primary/20 focus-within:border-primary/30 transition-all duration-300">
                    <div class="flex items-center gap-2 pl-2 pr-3 border-r border-gray-200 cursor-pointer hover:bg-gray-50 transition rounded-l-full py-1.5 shrink-0 max-w-[55%]" onclick="document.getElementById('modal-table-selector').classList.remove('hidden')">
                        <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                            <i class="fas fa-map-marker-alt text-primary text-[11px]"></i>
                        </div>
                        <div class="flex flex-col leading-tight min-w-0">
                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider truncate">Deliver To / Meja</span>
                            <span class="text-xs font-bold text-gray-800 flex items-center gap-1 truncate" id="header-table-number">
                                {{ $table ?? '(...)' }}
                                <i class="fas fa-chevron-down text-[8px] text-gray-400 ml-0.5"></i>
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 flex items-center px-3 relative">
                        <i class="fas fa-search text-gray-400 text-sm absolute left-3"></i>
                        <input type="text" id="search-menu" placeholder="Cari menu favoritmu..." class="w-full bg-transparent text-sm font-medium focus:outline-none focus:ring-0 border-0 border-transparent p-0 pl-6 text-gray-800 placeholder-gray-400 shadow-none h-auto">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('scroll', function () {
            const header = document.getElementById('sticky-header');
            if (!header) return;
            if (window.scrollY > 10) {
                header.classList.add('shadow-md');
            } else {
                header.classList.remove('shadow-md');
            }
        });
    </script>


    @if($shop->is_banner_active ?? true)
    <!-- Promo Carousel Section -->
    <div class="px-4 mb-8">
        <div class="relative bg-primary rounded-[20px] overflow-hidden shadow-lg h-56 group">
            
            <!-- Carousel Images Container -->
            <!-- We will let main.js inject into this container -->
            <div id="carousel-container" class="w-full h-full flex overflow-x-auto snap-x snap-mandatory hide-scroll relative z-0">
                <!-- JS Inject Here -->
            </div>
            
            <!-- Carousel Dots -->
            <div class="absolute bottom-4 w-full flex justify-center gap-1.5 z-10" id="carousel-dots">
                <!-- Injected by JS -->
            </div>
        </div>
    </div>
    @endif

    <!-- Categories Square Grid -->
    <div class="sticky top-0 bg-white z-20 pt-3 pb-3 border-b border-gray-100 shadow-sm">
        <div id="category-container" class="flex overflow-x-auto px-4 gap-3 hide-scroll">
             <!-- Injected by JS -->
        </div>
    </div>

    <!-- Menu List -->
    <div class="px-4 mt-5 flex justify-between items-center mb-1">
        <h2 class="font-extrabold text-xl text-gray-800">Popular Picks</h2>
        <span class="text-xs font-bold text-primary cursor-pointer hover:underline">View All &rarr;</span>
    </div>
    <main class="p-4 grid grid-cols-2 gap-4" id="menu-container">
        <!-- Injected by JS -->
    </main>

    <!-- Floating Cart Bar -->
    <div id="cart-bar" class="fixed bottom-0 w-full max-w-md bg-primary text-white p-4 flex justify-between items-center rounded-t-3xl shadow-[0_-10px_20px_rgba(0,0,0,0.1)] transform translate-y-full opacity-0 transition-all duration-300 z-40 mx-auto left-0 right-0">
        <div class="flex flex-col">
            <span class="text-xs text-white/70">Total Pesanan</span>
            <span class="font-bold text-lg" id="cart-total">Rp 0</span>
        </div>
        <button onclick="window.location.href='{{ route('shop.cart', $shop->slug) }}'" class="bg-white text-primary px-6 py-2 rounded-full font-bold shadow-md hover:bg-gray-100 flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i>
            <span id="cart-count" class="bg-accent text-white text-xs px-2 py-0.5 rounded-full">0</span>
        </button>
    </div>

    <!-- ITEM DETAIL MODAL (BOTTOM SHEET) -->
    <div id="modal-item-detail" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-md mx-auto">
        <div class="absolute inset-0 modal-overlay bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeItemDetail()"></div>
        
        <div class="relative bg-white w-full rounded-t-3xl bottom-sheet flex flex-col max-h-[85vh]">
            <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto my-3 flex-shrink-0"></div>
            
            <div class="overflow-y-auto px-5 pb-24 hide-scroll relative">
                <!-- Close Button Inside -->
                <button onclick="closeItemDetail()" class="absolute top-0 right-5 w-8 h-8 bg-black/5 rounded-full flex items-center justify-center text-gray-700 hover:bg-black/10 transition z-10">
                    <i class="fas fa-times"></i>
                </button>

                <img id="detail-img" src="" alt="Product" class="w-full h-48 object-cover rounded-2xl mb-4 shadow-sm border border-gray-100">
                
                <h3 id="detail-name" class="font-extrabold text-2xl text-gray-800 leading-tight mb-1">Nama Produk</h3>
                <p id="detail-desc" class="text-sm text-gray-500 mb-4 line-clamp-3">Deskripsi</p>
                <p id="detail-price" class="text-xl font-bold text-primary mb-6">Rp 0</p>

                <!-- Variasi Opsi -->
                <div id="dynamic-addons" class="mb-4"></div>

                <!-- Catatan Tambahan -->
                <div class="border-t border-gray-100 pt-4">
                    <h4 class="font-bold text-sm text-gray-800 mb-2">Catatan Tambahan</h4>
                    <textarea id="detail-notes" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition" placeholder="Contoh: Tanpa bawang, pisah es..."></textarea>
                </div>
            </div>

            <div class="absolute bottom-0 w-full bg-white p-4 border-t border-gray-100 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
                <button id="add-cart-btn" onclick="confirmAddToCart()" class="w-full bg-primary text-white py-3.5 rounded-xl font-bold text-base shadow-lg hover:shadow-xl active:scale-95 transition-all">
                    Tambah ke Keranjang
                </button>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-sm px-4 pointer-events-none"></div>

    <!-- Blocking Table Selection Modal -->
    <div id="modal-table-selector" class="fixed inset-0 bg-black/75 z-[200] flex-col items-center justify-center p-4 backdrop-blur-md hidden max-w-md mx-auto">
        <div class="bg-white rounded-[24px] w-full max-w-[320px] text-center shadow-2xl overflow-hidden relative mx-auto mt-[30vh]">
            <div class="h-32 bg-primary relative flex items-center justify-center">
                <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?q=80&w=600&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay">
                <div class="absolute inset-0 bg-gradient-to-t from-white via-white/10 to-transparent"></div>
                <div class="absolute -bottom-8 w-20 h-20 bg-white rounded-full p-2.5 shadow-[0_4px_10px_rgba(0,0,0,0.1)] border-[3px] border-white z-10 flex items-center justify-center left-1/2 -translate-x-1/2">
                    <img src="{{ $shop->logo_url ? $shop->logo_url : asset('logo-oqari.webp') }}" alt="Logo" class="w-full h-full object-contain rounded-full">
                </div>
            </div>
            
            <div class="pt-12 pb-6 px-6 relative z-20">
                <button type="button" onclick="document.getElementById('modal-table-selector').classList.add('hidden')" class="absolute top-2 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-lg"></i>
                </button>
                <h2 class="text-xl font-black text-primary mb-1 tracking-tight">Hai, Coffee Lovers!</h2>
                <p class="text-[11px] text-gray-500 mb-6 font-medium leading-relaxed px-2">Silakan masukkan nomor meja Anda untuk mulai memesan menu favoritmu.</p>
                
                <div class="text-left mb-6 relative">
                    <label class="block text-[10px] font-extrabold text-primary mb-2 uppercase tracking-widest ml-1">Nomor Meja <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-chair absolute left-4 top-1/2 -translate-y-1/2 text-primary opacity-60 text-sm"></i>
                        <input type="text" id="manual-table-input" value="{{ $table ?? '' }}" placeholder="Cth: Meja 05" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl py-3.5 pl-11 pr-4 text-sm font-extrabold text-gray-800 focus:outline-none focus:border-primary focus:bg-white transition-all shadow-inner placeholder-gray-400">
                    </div>
                </div>
                
                <button onclick="saveManualTable()" class="w-full bg-primary hover:bg-primary/90 text-white font-bold py-3.5 rounded-2xl transition-all shadow-lg active:scale-95 flex justify-center items-center gap-2">
                    Simpan Meja <i class="fas fa-check text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Tombol Akses Dashboard (Sementara) -->
    <a href="{{ route('admin.dashboard') }}" class="fixed bottom-24 right-4 z-50 bg-primary text-white w-12 h-12 rounded-full flex justify-center items-center shadow-lg hover:scale-110 active:scale-95 transition-transform border-2 border-white">
        <i class="fas fa-home text-lg"></i>
    </a>

    <!-- Footer / Social Media -->
    <div class="px-4 py-8 mb-6 mt-8 flex flex-col items-center justify-center text-center opacity-80">
        <div class="flex gap-4 mb-4">
            @if($shop->instagram_link)
                <a href="{{ $shop->instagram_link }}" target="_blank" class="w-10 h-10 rounded-full bg-pink-100 text-pink-500 flex items-center justify-center text-lg shadow-sm hover:scale-110 transition-transform">
                    <i class="fab fa-instagram"></i>
                </a>
            @endif
            @if($shop->whatsapp_number)
                <a href="https://wa.me/{{ $shop->whatsapp_number }}" target="_blank" class="w-10 h-10 rounded-full bg-green-100 text-green-500 flex items-center justify-center text-lg shadow-sm hover:scale-110 transition-transform">
                    <i class="fab fa-whatsapp"></i>
                </a>
            @endif
            @if($shop->maps_link)
                <a href="{{ $shop->maps_link }}" target="_blank" class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center text-lg shadow-sm hover:scale-110 transition-transform">
                    <i class="fas fa-map-marker-alt"></i>
                </a>
            @endif
        </div>
        <p class="text-xs text-gray-400 font-medium">Powered by Menu Oqari &copy; {{ date('Y') }}</p>
    </div>

    <!-- Include Scripts -->
    <script>
        const SHOP_THEME = "{{ $shop->theme_style ?? 'list' }}";
    </script>
    @include('shop.scripts')
    <script>
        function saveManualTable() {
            const table = document.getElementById('manual-table-input').value;
            if (table) {
                const shopName = "{{ $shop->name }}";
                document.getElementById('header-table-number').innerHTML = 'Meja ' + table + ', ' + shopName + ' <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-1"></i>';
                localStorage.setItem('bitten_table_qr', table);
                document.getElementById('modal-table-selector').classList.add('hidden');
            }
        }
    </script>
</body>
</html>
