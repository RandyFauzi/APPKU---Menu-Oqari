<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $shop->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @if(isset($shop) && $shop->logo_url)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $shop->logo_url) }}">
    @else
        <link rel="icon" type="image/webp" href="{{ asset('Pavico.webp') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '{{ $shop->primary_color ?? "#1E5A7A" }}', bgbase: '#F4EFE6', accent: '#8CB8C9', textdark: '#2D3748' }, fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Inter', 'sans-serif'] } } } }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body data-page="home" class="antialiased font-sans">

    <!-- Splash Screen -->
    <div id="splash-screen" class="fixed inset-0 z-[100] bg-bgbase flex flex-col items-center justify-center transition-opacity duration-500 overflow-hidden mx-auto max-w-[414px] shadow-[0_0_20px_rgba(0,0,0,0.15)]">
        <!-- Background Image with Transparency -->
        <div class="absolute inset-0 bg-cover bg-center opacity-15 mix-blend-multiply" style="background-image: url('{{ asset("Assest/Loading Screen.webp") }}');"></div>
        
        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center justify-center">
            <img src="{{ $shop->logo_url ? asset('storage/' . $shop->logo_url) : asset('Pavico.webp') }}" alt="{{ $shop->name }} Logo" class="w-32 h-32 object-contain animate-pulse drop-shadow-xl rounded-2xl">
            <h1 class="text-3xl font-sans font-black mt-4 text-primary tracking-tight drop-shadow-md uppercase">{{ $shop->name }}</h1>
        </div>
    </div>

    <div class="app-container pb-24 bg-bgbase">
        <!-- Header (Arby's Style) -->
        <header class="bg-bgbase px-4 pt-4 pb-2 sticky top-0 z-30 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="{{ $shop->logo_url ? asset('storage/' . $shop->logo_url) : asset('Pavico.webp') }}" alt="Logo" class="w-12 h-12 object-contain rounded-lg">
                <div class="flex flex-col">
                    <h1 class="text-xl font-heading font-extrabold text-primary leading-tight tracking-tight uppercase">{{ $shop->name }}</h1>
                </div>
            </div>
            <!-- Tombol profil dihapus atas permintaan -->
        </header>

        <!-- Carousel Highlight -->
        <div class="px-4 py-3 bg-bgbase">
            <div class="relative w-full rounded-2xl overflow-hidden shadow-lg border border-primary/10" style="aspect-ratio: 16/11;">
                <div id="carousel-container" class="w-full h-full flex overflow-x-auto snap-x snap-mandatory hide-scroll">
                    <!-- Di-render oleh JS -->
                </div>
                <!-- Pagination Dots -->
                <div id="carousel-dots" class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-10">
                </div>
            </div>
        </div>

        <!-- Location & Search -->
        <div class="px-4 mb-5">
            <div class="bg-white rounded-full px-5 py-3 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-primary text-lg"></i>
                    <div>
                        <p class="text-[10px] text-textdark/50 font-bold uppercase">Deliver to / Table</p>
                        <p class="text-xs font-bold text-textdark">Meja {{ $table ?? '(...)' }}, {{ $shop->name }}</p>
                    </div>
                </div>
                <button onclick="document.getElementById('search-menu').focus()" class="text-textdark/50 hover:text-primary transition-colors">
                    <i class="fas fa-search text-lg"></i>
                </button>
            </div>
            <!-- Hidden search input for actual searching -->
            <input type="text" id="search-menu" placeholder="Search..." class="w-full mt-2 bg-transparent border-b-2 border-primary/20 px-2 py-1 text-sm focus:outline-none focus:border-primary text-textdark font-bold opacity-0 h-0 transition-all focus:h-auto focus:opacity-100">
        </div>



        <!-- Categories -->
        <div class="px-4 mb-6 overflow-x-auto hide-scroll">
            <div id="category-container" class="flex gap-4 w-max pb-2">
                <!-- Categories injected by JS -->
            </div>
        </div>

        <!-- Popular Picks (Menu) -->
        <div class="px-4 mb-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-heading font-bold text-xl text-textdark">Popular Picks</h2>
                <span class="text-xs font-bold text-primary cursor-pointer hover:underline">View All &rarr;</span>
            </div>
            <div id="menu-container" class="grid grid-cols-2 gap-4">
                <!-- Menu injected by JS -->
            </div>
        </div>

        <!-- Floating Cart -->
        <div id="cart-bar" class="fixed bottom-0 max-w-[414px] w-full bg-white border-t-2 border-primary/10 p-4 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.05)] z-30 translate-y-full opacity-0 transition-all duration-300 mx-auto left-0 right-0">
            <div class="flex justify-between items-center">
                <div class="flex flex-col">
                    <p class="text-xs text-textdark font-bold"><span id="cart-count">0</span> Item Pesanan</p>
                    <p class="text-lg font-bold text-primary" id="cart-total">Rp 0</p>
                </div>
                <a href="{{ route('shop.cart', $shop->slug) }}" class="bg-primary text-white px-6 py-2.5 rounded-full font-bold shadow-[0_4px_14px_rgba(30,90,122,0.4)] hover:shadow-none hover:translate-y-0.5 transition-all flex items-center gap-2">
                    Checkout <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </div>
        </div>

        <!-- ITEM DETAIL MODAL (BOTTOM SHEET) -->
        <div id="modal-item-detail" class="fixed inset-0 z-50 hidden flex flex-col justify-end max-w-[414px] mx-auto">
            <div class="absolute inset-0 modal-overlay bg-textdark/80" onclick="closeItemDetail()"></div>
            
            <div class="relative bg-bgbase w-full rounded-t-3xl bottom-sheet flex flex-col max-h-[85vh] border-t-4 border-primary">
                <div class="w-12 h-1.5 bg-primary/20 rounded-full mx-auto my-3 flex-shrink-0"></div>
                
                <div class="overflow-y-auto px-5 pb-24 hide-scroll relative">
                    <button onclick="closeItemDetail()" class="absolute top-0 right-5 w-8 h-8 bg-white border-2 border-primary rounded-full flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-colors">
                        <i class="fas fa-times"></i>
                    </button>

                    <img id="detail-img" src="" alt="Product" class="w-full h-56 object-cover rounded-2xl mb-4 shadow-sm border-2 border-primary/20">
                    
                    <h3 id="detail-name" class="font-heading font-extrabold text-2xl text-textdark leading-tight mb-1">Nama Produk</h3>
                    <p id="detail-desc" class="text-sm text-textdark/70 mb-4 line-clamp-3">Deskripsi</p>
                    <p id="detail-price" class="text-xl font-bold text-primary mb-6">Rp 0</p>

                    <div id="dynamic-addons" class="mb-4"></div>

                    <div class="border-t-2 border-primary/10 pt-4">
                        <h4 class="font-bold text-sm text-textdark mb-2 font-heading">Catatan Tambahan</h4>
                        <textarea id="detail-notes" rows="2" class="w-full bg-white border-2 border-primary/20 rounded-xl p-3 text-sm focus:outline-none focus:border-primary transition" placeholder="Contoh: Tanpa bawang, pisah es..."></textarea>
                    </div>
                </div>

                <div class="absolute bottom-0 w-full bg-white p-4 border-t-2 border-primary/10 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
                    <button id="add-cart-btn" onclick="confirmAddToCart()" class="w-full bg-primary text-white py-3.5 rounded-full font-bold text-base shadow-[0_4px_14px_rgba(30,90,122,0.4)] active:translate-y-1 active:shadow-none transition-all">
                        Tambah ke Keranjang
                    </button>
                </div>
            </div>
        </div>

        <!-- Tombol Akses Dashboard (Sementara) -->
        <a href="{{ route('admin.dashboard') }}" class="fixed bottom-24 right-4 z-50 bg-[#1E5A7A] text-white w-12 h-12 rounded-full flex justify-center items-center shadow-lg hover:scale-110 active:scale-95 transition-transform border-2 border-white">
            <i class="fas fa-home text-lg"></i>
        </a>

        <div id="toast-container" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-[414px] px-4 pointer-events-none"></div>

    @include('shop.scripts')
</body>
</html>
