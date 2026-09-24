<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>OQARI — Platform POS Kasir & Smart QR Menu untuk Coffee Shop & F&B</title>
    <meta name="description" content="Aplikasi kasir POS dan sistem QR ordering pintar untuk coffee shop, cafe, dan resto. Kelola pesanan meja, kitchen display barista, stok resep gramasi, hingga laporan omzet dalam satu platform.">
    <meta name="keywords" content="aplikasi kasir coffee shop, pos cafe, qr menu meja, kitchen display system barista, hpp kopi otomatis, oqari pos">

    <!-- OpenGraph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="OQARI — Platform POS Kasir & Smart QR Menu untuk Coffee Shop & F&B">
    <meta property="og:description" content="Revolusi operasional coffee shop & cafe Anda. Pesan langsung dari meja via QR, kirim ke barista seketika, dan pantau omzet secara real-time.">
    <meta property="og:image" content="{{ asset('logo-oqari.webp') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Alpine.js, Tailwind & AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Alpine.js & Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #FAFAF9;
            color: #1C1917;
            overflow-x: hidden;
        }

        /* Ambient Glow & Gradients */
        .hero-mesh-gradient {
            background: 
                radial-gradient(ellipse 60% 50% at 85% 15%, rgba(217, 119, 6, 0.12) 0%, transparent 70%),
                radial-gradient(ellipse 50% 40% at 15% 85%, rgba(120, 53, 15, 0.08) 0%, transparent 60%),
                #FAFAF9;
        }

        .cta-gradient-bg {
            background: linear-gradient(135deg, #1C1917 0%, #292524 50%, #44403C 100%);
        }

        .orange-gradient-badge {
            background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
            border: 1px solid #FDE68A;
        }

        .primary-btn-gradient {
            background: linear-gradient(135deg, #78350F 0%, #92400E 50%, #B45309 100%);
            box-shadow: 0 4px 14px -2px rgba(180, 83, 9, 0.4);
            transition: all 0.25s ease;
        }
        .primary-btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -2px rgba(180, 83, 9, 0.5);
            background: linear-gradient(135deg, #92400E 0%, #B45309 50%, #D97706 100%);
        }

        .card-soft-shadow {
            box-shadow: 0 10px 30px -5px rgba(28, 25, 23, 0.05), 0 4px 10px -2px rgba(28, 25, 23, 0.02);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-soft-shadow:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(28, 25, 23, 0.1), 0 8px 16px -4px rgba(28, 25, 23, 0.04);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F5F5F4;
        }
        ::-webkit-scrollbar-thumb {
            background: #D6D3D1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #A8A29E;
        }
    </style>
</head>
<body class="antialiased selection:bg-orange-100 selection:text-orange-900" x-data="{ mobileMenuOpen: false }">

    <!-- 2. STICKY NAVBAR -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-orange-100/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-700 via-orange-800 to-stone-900 flex items-center justify-center shadow-md shadow-orange-900/10 group-hover:scale-105 transition-transform p-1.5">
                    <img src="{{ asset('logo-oqari.webp') }}" alt="OQARI Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight text-orange-950 flex items-center gap-1">
                        OQARI
                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-orange-100 text-orange-800 rounded uppercase tracking-wider">POS</span>
                    </span>
                    <span class="text-[11px] block font-medium text-gray-500 -mt-1 tracking-wide">Coffee & F&B Ecosystem</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-gray-600">
                <a href="#fitur" class="hover:text-orange-800 transition-colors">Fitur Unggulan</a>
                <a href="#solusi" class="hover:text-orange-800 transition-colors">Solusi Bisnis</a>
                <a href="#demo-qr" class="hover:text-orange-800 transition-colors flex items-center gap-1.5">
                    <span>Demo QR Meja</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 bg-green-100 text-green-700 rounded-full">Live</span>
                </a>
                <a href="#hardware" class="hover:text-orange-800 transition-colors">Perangkat</a>
                <a href="#harga" class="hover:text-orange-800 transition-colors">Paket & Harga</a>
                <a href="#faq" class="hover:text-orange-800 transition-colors">FAQ</a>
            </nav>

            <!-- CTA Buttons -->
            <div class="hidden sm:flex items-center gap-3">
                @if(auth()->check() && isset($dashboardUrl))
                    <a href="{{ $dashboardUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white primary-btn-gradient">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl font-semibold text-sm text-gray-700 hover:text-orange-950 hover:bg-stone-100 transition-colors border border-orange-100">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white primary-btn-gradient">
                        <span>Coba Gratis 14 Hari</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 rounded-xl text-gray-700 hover:bg-stone-100 focus:outline-none" aria-label="Toggle Menu">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden bg-white border-b border-orange-100 px-4 pt-3 pb-6 space-y-3">
            <a href="#fitur" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">Fitur Unggulan</a>
            <a href="#solusi" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">Solusi Bisnis</a>
            <a href="#demo-qr" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">Demo QR Meja</a>
            <a href="#hardware" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">Perangkat Kompatibel</a>
            <a href="#harga" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">Paket & Harga</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="block py-2 text-gray-700 font-semibold">FAQ</a>
            <div class="pt-4 border-t border-orange-100 flex flex-col gap-2.5">
                @if(auth()->check() && isset($dashboardUrl))
                    <a href="{{ $dashboardUrl }}" class="w-full text-center py-3 rounded-xl font-bold text-white primary-btn-gradient">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl font-semibold text-gray-800 border border-stone-300">
                        Masuk Akun
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-3 rounded-xl font-bold text-white primary-btn-gradient">
                        Coba Gratis 14 Hari
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- 3. HERO SECTION (Redesigned with Split Background) -->
    <section data-aos="fade-in" class="relative w-full min-h-[600px] flex items-center bg-[#e4e3df] overflow-hidden border-b border-gray-300">
        
        <!-- Background Image with Gradient Fade -->
        <div class="absolute inset-y-0 left-0 w-full lg:w-[55%] h-full">
            <img src="{{ asset('hero.webp') }}" alt="Barista and Customer" class="w-full h-full object-cover object-[center_left]" />
            <!-- Gradient Overlay to blend the right edge of the image into the background color -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#e4e3df]/60 to-[#e4e3df] lg:bg-gradient-to-r lg:from-transparent lg:via-transparent lg:to-[#e4e3df]"></div>
        </div>

        <!-- Content Container -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center h-full">
                <!-- Spacer to push text to the right -->
                <div class="hidden lg:block lg:col-span-6"></div>

                <!-- Text Content on the Right -->
                <div class="lg:col-span-6 py-16 lg:py-28 space-y-6 text-center lg:text-left lg:pl-4">
                    
                    <!-- Pill Tag -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] font-bold text-orange-900 border border-orange-300 shadow-sm" style="background-color: #f6ebd8;">
                        <span>#1 Ekosistem Kasir POS & Smart QR Menu untuk Coffee Shop</span>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-gray-900 tracking-tight leading-[1.15]">
                        POS & Smart Menu
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-base sm:text-lg text-gray-700 font-medium leading-relaxed max-w-xl mx-auto lg:mx-0">
                        QR Menu, POS, & Kitchen Display dalam satu platform. Lebih cepat, hemat, dan efisien.
                    </p>

                    <!-- Feature Capsules -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 pt-2">
                        <span class="px-3 py-1.5 bg-white/50 backdrop-blur-sm border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 flex items-center gap-2 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> QR Order Meja
                        </span>
                        <span class="px-3 py-1.5 bg-white/50 backdrop-blur-sm border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 flex items-center gap-2 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> Kasir POS Tablet
                        </span>
                        <span class="px-3 py-1.5 bg-white/50 backdrop-blur-sm border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 flex items-center gap-2 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> KDS Barista Realtime
                        </span>
                        <span class="px-3 py-1.5 bg-white/50 backdrop-blur-sm border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 flex items-center gap-2 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> HPP & Gramasi Kopi
                        </span>
                        <span class="px-3 py-1.5 bg-white/50 backdrop-blur-sm border border-gray-400 rounded-lg text-xs font-semibold text-gray-800 flex items-center gap-2 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-600"></span> Shift & Cash Drawer
                        </span>
                    </div>

                    <!-- Dual CTAs -->
                    <div class="pt-6 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-sm text-white flex items-center justify-center gap-3 transition-transform hover:-translate-y-0.5" style="background-color: #894b15; box-shadow: 0 8px 20px -4px rgba(137, 75, 21, 0.4);">
                            <span>Mulai Coba Gratis 14 Hari</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#demo-qr" class="w-full sm:w-auto px-7 py-3.5 rounded-xl font-bold text-sm text-gray-900 bg-white/40 border border-gray-500 hover:bg-white/70 transition-all flex items-center justify-center gap-2 shadow-sm group backdrop-blur-sm">
                            <svg class="w-5 h-5 text-gray-800" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                            <span>Lihat Demo Interaktif</span>
                        </a>
                    </div>

                    <!-- Micro-trust signals -->
                    <div class="pt-4 text-[11px] font-bold text-green-700 flex flex-wrap items-center justify-center lg:justify-start gap-y-2 gap-x-5">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Tanpa Kartu Kredit
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Setup Instan 5 Menit
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Bantuan Pendampingan Tim Ahli
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- 4. SOCIAL PROOF & STATS BAR (Majoo-style trust numbers) -->
    <section data-aos="fade-up" class="py-12 bg-white border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Headline -->
            <p class="text-center text-xs font-bold text-gray-500 uppercase tracking-widest mb-8">
                Telah Dipercaya oleh 500+ Pemilik Coffee Shop, Roastery, dan Artisan Cafe di Seluruh Indonesia
            </p>

            <!-- 4 Metrics Counters -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-stone-200">
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">500+</div>
                    <div class="text-xs sm:text-sm font-medium text-gray-500 mt-1">Outlet Cafe Aktif</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-orange-800 tracking-tight">3.2 Juta+</div>
                    <div class="text-xs sm:text-sm font-medium text-gray-500 mt-1">Pesanan Sukses Diproses</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">3x Cepat</div>
                    <div class="text-xs sm:text-sm font-medium text-gray-500 mt-1">Putaran Meja Saat Peak Hour</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-green-600 tracking-tight">99.98%</div>
                    <div class="text-xs sm:text-sm font-medium text-gray-500 mt-1">Uptime Server Cloud</div>
                </div>
            </div>

            <!-- Client Logos Mockup -->
            <div class="mt-10 pt-8 border-t border-stone-100 flex flex-wrap items-center justify-center gap-8 sm:gap-14 opacity-75 grayscale hover:grayscale-0 transition-all">
                <span class="font-black text-lg tracking-tighter text-gray-800">SENJA ROASTERY</span>
                <span class="font-extrabold text-lg tracking-wide text-gray-800">KOPI TEMU RASA</span>
                <span class="font-bold text-lg tracking-widest text-gray-800">ARTISAN BREW LAB</span>
                <span class="font-semibold text-lg italic text-gray-800">Daily Dose Cafe</span>
                <span class="font-black text-lg tracking-tight text-gray-800">SUDUT KOTA COFFEE</span>
            </div>

        </div>
    </section>

    <!-- 5. CORE VALUE TRIO (Majoo-style 3 Pillars Grid) -->
    <section data-aos="fade-up" id="fitur" class="py-20 bg-stone-50 border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-800 bg-orange-100 px-3 py-1 rounded-full">
                    Fitur Canggih & Komprehensif
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                    Dirancang Khusus untuk Alur Kerja <br class="hidden sm:inline">
                    Bisnis Kopi & Cafe yang Cepat
                </h2>
                <p class="text-base sm:text-lg text-gray-600">
                    Tinggalkan sistem kasir lama yang kaku. OQARI menyinkronkan meja, kasir, barista, dan pemilik dalam satu alur kerja mulus.
                </p>
            </div>

            <!-- 3 Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Card 1: QR Self Ordering -->
                <div class="bg-white rounded-3xl p-8 border border-orange-100 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-gray-700 rounded-md">Tanpa Install App</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-orange-50 text-orange-800 rounded-md">QRIS Dinamis</span>
                        </div>

                        <h3 class="text-xl font-bold text-orange-950 mb-3">
                            Smart QR E-Menu & Table Self-Order
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-6">
                            Pelanggan cukup scan QR di atas meja untuk melihat menu visual estetik, kustomisasi pilihan kopi (es, gula, jenis susu), dan bayar instan. Mengurai 60% antrean kasir.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-orange-800">
                        <span>Pangkas waktu tunggu tamu</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 2: POS & Kitchen Display (KDS) -->
                <div class="bg-white rounded-3xl p-8 border border-orange-100 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-gray-700 rounded-md">KDS Barista</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-orange-50 text-orange-800 rounded-md">Cetak Struk Kilat</span>
                        </div>

                        <h3 class="text-xl font-bold text-orange-950 mb-3">
                            POS Kasir Cepat & Kitchen Display (KDS)
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-6">
                            Pesanan kasir atau QR langsung muncul di layar tablet barista dengan bunyi notifikasi otomatis. Tidak ada lagi kertas bon hilang, uap kopi merusak struk, atau salah resep.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-orange-800">
                        <span>Sinkronisasi audio & visual 0 detik</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 3: Recipe Costing & HPP Gramasi -->
                <div class="bg-white rounded-3xl p-8 border border-orange-100 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-gray-700 rounded-md">Gramasi Kopi & Susu</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-orange-50 text-orange-800 rounded-md">Cegah Kebocoran</span>
                        </div>

                        <h3 class="text-xl font-bold text-orange-950 mb-3">
                            Manajemen Resep & HPP Otomatis
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed mb-6">
                            Setiap cangkir Latte otomatis memotong 18 gram biji espresso dan 150ml susu. Lacak margin kotor tiap menu secara real-time dan dapatkan notifikasi saat bahan menipis.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-orange-800">
                        <span>Kontrol margin profit akurat</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 6. INTERACTIVE SOLUTION TABS (Segmentasi Industri ala Majoo Solusi Bisnis) -->
    <section data-aos="fade-up" id="solusi" class="py-24 bg-gray-50 border-b border-gray-200" x-data="{ activeTab: 'specialty' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    Solusi Terarah
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                    Cocok untuk Segala Format Bisnis Kopi & FnB
                </h2>
                <p class="text-base text-gray-600">
                    Pilih format operasional Anda untuk melihat bagaimana OQARI meningkatkan efisiensi harian.
                </p>
            </div>

            <!-- Tab Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button @click="activeTab = 'specialty'" :class="activeTab === 'specialty' ? 'bg-gradient-to-r from-orange-600 to-amber-700 text-white shadow-lg shadow-orange-600/30 border border-transparent' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 shadow-sm'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    ☕ Specialty Coffee & Roastery
                </button>
                <button @click="activeTab = 'cafe'" :class="activeTab === 'cafe' ? 'bg-gradient-to-r from-orange-600 to-amber-700 text-white shadow-lg shadow-orange-600/30 border border-transparent' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 shadow-sm'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🍽️ Cafe & Casual Dining
                </button>
                <button @click="activeTab = 'booth'" :class="activeTab === 'booth' ? 'bg-gradient-to-r from-orange-600 to-amber-700 text-white shadow-lg shadow-orange-600/30 border border-transparent' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 shadow-sm'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🚀 Coffee Booth & Grab-and-Go
                </button>
                <button @click="activeTab = 'chain'" :class="activeTab === 'chain' ? 'bg-gradient-to-r from-orange-600 to-amber-700 text-white shadow-lg shadow-orange-600/30 border border-transparent' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-700 border border-gray-200 shadow-sm'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🏢 Multi-Outlet & Franchise
                </button>
            </div>

            <!-- Tab Content Panels -->
            <div class="bg-white rounded-[2rem] p-8 sm:p-12 border border-gray-100 card-soft-shadow shadow-xl shadow-gray-200/50">
                
                <!-- Panel 1: Specialty Coffee -->
                <div x-show="activeTab === 'specialty'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-800 rounded-md">Untuk Artisan Roaster & Specialty Cafe</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-orange-950">
                            Kustomisasi Single Origin, Manual Brew, & Modifikasi Tanpa Batas
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                            Tampilkan profil rasa (*tasting notes*), proses pascapanen (*Natural, Washed, Anaerobic*), dan metode seduh (*V60, Kalita, Aeropress*) langsung di QR menu pelanggan. Barista menerima pesanan dengan rincian rasio gramasi dan suhu air yang presisi.
                        </p>
                        <ul class="space-y-2.5 text-sm text-gray-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tracking batch roasting biji kopi & tanggal resting beans
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Pilihan susu oatmilk, almond, soy milk dengan penyesuaian harga instan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Penjualan beans ritel 200g/1kg dengan barcode scan terintegrasi
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-orange-100">
                        <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-3">Live Simulation: Barista Brew Bar</div>
                        <div class="space-y-3">
                            <div class="p-3 bg-stone-50 rounded-xl border border-orange-100 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">V60 Ethiopia Guji (Natural)</div>
                                    <div class="text-xs text-gray-500">Grind: Medium-Fine • Ratio: 1:15 • Temp: 92°C</div>
                                </div>
                                <span class="px-2.5 py-1 bg-orange-100 text-orange-800 text-xs font-bold rounded">Barista #1</span>
                            </div>
                            <div class="p-3 bg-stone-50 rounded-xl border border-orange-100 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">Magic (Double Ristretto + Oatmilk)</div>
                                    <div class="text-xs text-gray-500">Beans: House Blend (Brazil x Flores) • 160ml cup</div>
                                </div>
                                <span class="px-2.5 py-1 bg-orange-100 text-orange-800 text-xs font-bold rounded">Espresso Bar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Cafe & Casual Dining -->
                <div x-show="activeTab === 'cafe'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-800 rounded-md">Untuk Cafe Resto & Tempat Nongkrong</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-orange-950">
                            Manajemen Denah Meja, Split Bill, & Multi-Kitchen Printer
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                            Atur nomor meja hingga 100+ titik dengan layout visual. Pesanan minuman otomatis terkirim ke printer Barista, sementara makanan berat (*hot kitchen*) terkirim ke printer Chef dapur.
                        </p>
                        <ul class="space-y-2.5 text-sm text-gray-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Pisah tagihan (Split Bill) per orang atau per item dengan mudah
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Open Bill (Pesan dulu, bayar nanti saat selesai nongkrong)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tambah pesanan baru (*round 2*) langsung dari QR meja tanpa antre ulang
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-orange-100">
                        <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-3">Live Table Grid Management</div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-center">
                                <div class="font-bold text-green-800 text-sm">Meja 01</div>
                                <div class="text-[10px] text-green-600">Kosong (Tersedia)</div>
                            </div>
                            <div class="p-3 bg-orange-50 border border-orange-300 rounded-xl text-center">
                                <div class="font-bold text-orange-800 text-sm">Meja 02</div>
                                <div class="text-[10px] text-orange-600">Terisi (Pesan QR)</div>
                            </div>
                            <div class="p-3 bg-orange-50 border border-orange-300 rounded-xl text-center">
                                <div class="font-bold text-orange-800 text-sm">Meja 03</div>
                                <div class="text-[10px] text-orange-600">Terisi (Open Bill)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Coffee Booth -->
                <div x-show="activeTab === 'booth'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-800 rounded-md">Untuk Gerai Kiosk & Kopi Susu Kekinian</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-orange-950">
                            Cepat, Ringkas, Cukup 1 Smartphone / Tablet Android
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                            Cocok untuk ruang sempit di stasiun, ruko mini, atau foodcourt. 1 kasir barista bisa memproses 100+ cup per jam dengan mode input cepat 2 ketukan.
                        </p>
                        <ul class="space-y-2.5 text-sm text-gray-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Sambungkan ke printer thermal bluetooth 58mm portable
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tampilkan QRIS statis atau dinamis di layar pelanggan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Rekap omzet harian dikirim otomatis ke WhatsApp Owner
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-orange-100">
                        <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-3">Quick Tap Fast-Checkout</div>
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="p-3 bg-stone-50 border border-orange-100 rounded-xl">
                                <div class="font-bold text-sm text-gray-800">Kopi Susu Aren</div>
                                <div class="text-xs text-orange-700 font-semibold">Rp 18.000</div>
                            </div>
                            <div class="p-3 bg-stone-50 border border-orange-100 rounded-xl">
                                <div class="font-bold text-sm text-gray-800">Americano Ice</div>
                                <div class="text-xs text-orange-700 font-semibold">Rp 15.000</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Multi Outlet Chain -->
                <div x-show="activeTab === 'chain'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-orange-100 text-orange-800 rounded-md">Untuk Franchise & Multi-Cabang</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-orange-950">
                            Kontrol Puluhan Cabang Terpusat dari Satu Layar SuperAdmin
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm sm:text-base">
                            Bandingkan performa penjualan antar cabang, kendalikan harga menu regional, kelola hak akses staf (Kasir, Barista, Manager, Owner), dan distribusikan stok bahan baku dari gudang pusat.
                        </p>
                        <ul class="space-y-2.5 text-sm text-gray-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Dashboard konsolidasian omzet realtime seluruh kota
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Audit log kasir & rekonsiliasi kas shift bebas kecurangan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-bold">✓</span>
                                API webhook untuk integrasi ERP & akuntansi perusahaan
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-orange-100">
                        <div class="text-xs font-bold text-orange-600 uppercase tracking-wider mb-3">Multi-Branch Analytics</div>
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-gray-800">Cabang Senopati, Jaksel</span>
                                <span class="font-mono font-bold text-green-600">Rp 14.250.000 (100%)</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-gray-800">Cabang Riau, Bandung</span>
                                <span class="font-mono font-bold text-green-600">Rp 11.890.000 (83%)</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-gray-800">Cabang Gubeng, Surabaya</span>
                                <span class="font-mono font-bold text-green-600">Rp 9.420.000 (66%)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 7. INTERACTIVE LIVE QR MENU DEMO (Experience OQARI Live in Browser) -->
    <section data-aos="fade-up" id="demo-qr" class="py-20 bg-stone-100 border-b border-orange-100" x-data="{ 
        selectedItems: [
            { name: 'Spanish Aren Latte', price: 32000, qty: 1, notes: 'Oatmilk, Less Sugar' }
        ],
        cartTotal() {
            return this.selectedItems.reduce((acc, item) => acc + (item.price * item.qty), 0);
        },
        addItem(name, price, notes) {
            this.selectedItems.push({ name, price, qty: 1, notes });
        },
        removeItem(index) {
            this.selectedItems.splice(index, 1);
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Info -->
                <div class="lg:col-span-6 space-y-6">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-800 bg-orange-100 px-3 py-1 rounded-full">
                        Simulator Interaktif
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                        Coba Sensasi Memesan <br>
                        Langsung dari Layar Ini
                    </h2>
                    <p class="text-base text-gray-600 leading-relaxed">
                        Inilah tampilan yang akan dilihat oleh pelanggan Anda saat mereka memindai kode QR di meja cafe. Desain modern, cepat, dan terhubung langsung ke kasir serta dapur tanpa hambatan.
                    </p>
                    
                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-800 flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                            <div>
                                <h4 class="font-bold text-orange-950 text-sm">Pelanggan Duduk & Scan QR di Meja</h4>
                                <p class="text-xs text-gray-500">Kamera smartphone langsung membuka daftar menu tanpa unduh aplikasi apapun.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-800 flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                            <div>
                                <h4 class="font-bold text-orange-950 text-sm">Pilih Kopi, Modifier Topping, & Catatan</h4>
                                <p class="text-xs text-gray-500">Pilihan gula, es, dan susu terstruktur rapi tanpa resiko salah dengar.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-800 flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                            <div>
                                <h4 class="font-bold text-orange-950 text-sm">Pesanan Otomatis Masuk ke Barista</h4>
                                <p class="text-xs text-gray-500">Layar dapur barista langsung berbunyi 'ting!' dan menampilkan resep secara real-time.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl font-bold text-sm text-white primary-btn-gradient">
                            <span>Pasang QR Menu di Cafe Anda Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Right Simulator Phone Shell -->
                <div class="lg:col-span-6 flex justify-center">
                    
                    <div class="w-full max-w-sm bg-orange-50 p-4 rounded-[40px] shadow-2xl border-4 border-orange-200 relative">
                        
                        <!-- Top Phone Notch -->
                        <div class="w-36 h-4 bg-orange-100 rounded-b-xl mx-auto mb-3"></div>

                        <!-- Screen Area -->
                        <div class="bg-white rounded-[28px] overflow-hidden text-orange-950 flex flex-col h-[520px]">
                            
                            <!-- App Header -->
                            <div class="bg-orange-950 text-white p-4">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-green-400"></span>
                                        OQARI Coffee Lab
                                    </span>
                                    <span class="bg-orange-900/80 px-2 py-0.5 rounded text-[10px] font-mono">Meja #04</span>
                                </div>
                                <h3 class="text-base font-extrabold">E-Menu Digital</h3>
                            </div>

                            <!-- Menu Items List -->
                            <div class="p-3.5 overflow-y-auto flex-1 space-y-2.5">
                                
                                <div class="text-[11px] font-bold text-orange-600 uppercase tracking-wider">Pilih Menu untuk Menambah:</div>

                                <!-- Item 1 -->
                                <div class="p-2.5 rounded-xl border border-orange-100 hover:border-orange-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-orange-950">Spanish Aren Latte</div>
                                        <div class="text-[10px] text-gray-500">Espresso + Susu Aren Creamy</div>
                                        <div class="text-xs font-bold text-orange-800 mt-1">Rp 32.000</div>
                                    </div>
                                    <button @click="addItem('Spanish Aren Latte', 32000, 'Less Sugar, Ice')" type="button" class="px-2.5 py-1.5 rounded-lg bg-orange-800 text-white text-[11px] font-bold hover:bg-orange-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Item 2 -->
                                <div class="p-2.5 rounded-xl border border-orange-100 hover:border-orange-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-orange-950">Americano On The Rocks</div>
                                        <div class="text-[10px] text-gray-500">Double Shot Blend Specialty</div>
                                        <div class="text-xs font-bold text-orange-800 mt-1">Rp 24.000</div>
                                    </div>
                                    <button @click="addItem('Americano On The Rocks', 24000, 'Normal Ice')" type="button" class="px-2.5 py-1.5 rounded-lg bg-orange-800 text-white text-[11px] font-bold hover:bg-orange-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Item 3 -->
                                <div class="p-2.5 rounded-xl border border-orange-100 hover:border-orange-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-orange-950">Butter Croissant</div>
                                        <div class="text-[10px] text-gray-500">Flaky French Pastry (Warm)</div>
                                        <div class="text-xs font-bold text-orange-800 mt-1">Rp 26.000</div>
                                    </div>
                                    <button @click="addItem('Butter Croissant', 26000, 'Dipanaskan')" type="button" class="px-2.5 py-1.5 rounded-lg bg-orange-800 text-white text-[11px] font-bold hover:bg-orange-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Order Cart Preview -->
                                <div class="pt-2 border-t border-orange-100">
                                    <div class="text-[11px] font-bold text-gray-600 mb-1 flex justify-between">
                                        <span>Keranjang Pesanan:</span>
                                        <span x-text="selectedItems.length + ' Item'"></span>
                                    </div>
                                    <template x-for="(item, idx) in selectedItems" :key="idx">
                                        <div class="flex items-center justify-between py-1 text-xs border-b border-stone-100">
                                            <div>
                                                <div class="font-semibold text-gray-800" x-text="item.name"></div>
                                                <div class="text-[10px] text-orange-600" x-text="item.notes"></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-700" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></span>
                                                <button @click="removeItem(idx)" class="text-red-500 hover:text-red-700 text-xs font-bold" title="Hapus">×</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                            </div>

                            <!-- Bottom Floating Action -->
                            <div class="p-3 bg-stone-50 border-t border-orange-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-500">Estimasi Total:</span>
                                    <span class="text-base font-extrabold text-orange-950" x-text="'Rp ' + cartTotal().toLocaleString('id-ID')"></span>
                                </div>
                                <button type="button" class="w-full py-2.5 rounded-xl font-bold text-xs text-white primary-btn-gradient flex items-center justify-center gap-1.5">
                                    <span>Pesan & Bayar QRIS Sekarang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 8. HARDWARE COMPATIBILITY & ECOSYSTEM -->
    <section data-aos="fade-up" id="hardware" class="py-20 bg-white border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    Bebas Tanpa Lock-in
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                    Gunakan Perangkat yang Sudah Anda Miliki
                </h2>
                <p class="text-base text-gray-600">
                    Tidak perlu merogoh kocek puluhan juta untuk mesin POS khusus. OQARI berjalan lancar di browser iPad, tablet Android, laptop, smartphone, dan printer kasir yang beredar di pasaran.
                </p>
            </div>

            <!-- Hardware Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                
                <div class="p-6 rounded-2xl bg-stone-50 border border-orange-100 hover:border-orange-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center font-bold mb-4">
                        📱
                    </div>
                    <h4 class="font-bold text-orange-950 mb-1">Tablet & iPad</h4>
                    <p class="text-xs text-gray-500">Apple iPad, Samsung Galaxy Tab, Xiaomi Pad, tablet Android apapun.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-orange-100 hover:border-orange-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center font-bold mb-4">
                        🖨️
                    </div>
                    <h4 class="font-bold text-orange-950 mb-1">Thermal Printer</h4>
                    <p class="text-xs text-gray-500">Printer struk 58mm & 80mm via Bluetooth, USB, maupun Ethernet LAN.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-orange-100 hover:border-orange-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center font-bold mb-4">
                        📟
                    </div>
                    <h4 class="font-bold text-orange-950 mb-1">Mini POS Terminal</h4>
                    <p class="text-xs text-gray-500">Kompatibel dengan perangkat all-in-one Sunmi, iMin, Advan POS.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-orange-100 hover:border-orange-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-orange-100 text-orange-800 flex items-center justify-center font-bold mb-4">
                        💻
                    </div>
                    <h4 class="font-bold text-orange-950 mb-1">PC / Laptop / Mac</h4>
                    <p class="text-xs text-gray-500">Akses modul POS dan analitik lengkap melalui browser Google Chrome / Safari.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- 9. PRICING PLANS (Desain Sama Persis sesuai Master Mockup OQARI) -->
    <section data-aos="fade-up" id="harga" class="py-20 sm:py-28 bg-[#252525] border-b border-orange-200 relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Outer Relative Wrapper for Overlapping 3D Elements -->
            <div class="relative">
                
                <!-- Top Right Overlapping Decoration: Pastry / Bakery Plate -->
                <img src="{{ asset('pricing_plate_trans.png') }}" alt="Pastry plate" class="absolute -top-10 -right-4 sm:-top-14 sm:-right-8 lg:-top-16 lg:-right-10 w-28 sm:w-36 lg:w-44 z-30 pointer-events-none drop-shadow-2xl">

                <!-- Bottom Left Overlapping Decoration: 3D Wooden QR Code Stand -->
                <img src="{{ asset('pricing_qr_stand_trans.png') }}" alt="QR Stand" class="absolute -bottom-8 -left-4 sm:-bottom-12 sm:-left-8 lg:-bottom-14 lg:-left-10 w-24 sm:w-32 lg:w-36 z-30 pointer-events-none drop-shadow-2xl">

                <!-- Central Big Rounded Canvas Card -->
                <div class="rounded-[36px] sm:rounded-[48px] p-6 sm:p-12 lg:p-16 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.6)] relative z-10 border border-white/20"
                     style="background: radial-gradient(circle at 0% 0%, rgba(165, 140, 135, 0.28) 0%, rgba(255, 255, 255, 0.98) 40%), radial-gradient(circle at 100% 100%, rgba(170, 145, 138, 0.28) 0%, rgba(255, 255, 255, 0.98) 40%), #FFFFFF;">
                    
                    <!-- Center Header: Oqari Brand -->
                    <div class="text-center mb-10 sm:mb-12">
                        <div class="inline-flex items-center justify-center gap-2.5">
                            <img src="{{ asset('pricing_brand_logo_trans.png') }}" alt="Oqari" class="h-8 sm:h-9 object-contain">
                        </div>
                    </div>

                    <!-- 3 Pricing Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-7 items-stretch max-w-5xl mx-auto">
                        
                        <!-- Card 1: Monthly Starter -->
                        <div class="bg-white rounded-2xl sm:rounded-3xl p-7 sm:p-8 border border-stone-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-between transition-transform hover:-translate-y-1">
                            <div>
                                <!-- Icon -->
                                <div class="mb-4">
                                    <img src="{{ asset('pricing_icon1_trans.png') }}" alt="Monthly Starter Icon" class="w-6 h-6 object-contain">
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl sm:text-2xl font-bold text-orange-950 tracking-tight">
                                    Monthly Starter
                                </h3>

                                <!-- Subtitle -->
                                <p class="text-xs text-gray-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                    Pilihan low-risk untuk tes efektivitas sistem di operasional.
                                </p>

                                <!-- Price -->
                                <div class="flex items-baseline mb-6">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-orange-950 tracking-tight">Rp80.000</span>
                                    <span class="text-xs text-orange-600 font-normal ml-1.5">per month</span>
                                </div>

                                <!-- Features List -->
                                <ul class="space-y-3 text-xs text-gray-700 font-normal mb-8">
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Akses Penuh Core System</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>System pay-as-you-go</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Bebas Cancel kapan saja</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Button -->
                            <a href="{{ route('register') }}" class="w-full py-2.5 rounded-xl border border-stone-300 hover:border-stone-400 text-orange-950 bg-white font-medium text-xs sm:text-sm text-center block transition-colors">
                                Coba Langganan Bulanan
                            </a>
                        </div>

                        <!-- Card 2: Lifetime Basic -->
                        <div class="bg-white rounded-2xl sm:rounded-3xl p-7 sm:p-8 border border-stone-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-between transition-transform hover:-translate-y-1">
                            <div>
                                <!-- Icon -->
                                <div class="mb-4">
                                    <img src="{{ asset('pricing_icon2_trans.png') }}" alt="Lifetime Basic Icon" class="w-6 h-6 object-contain">
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl sm:text-2xl font-bold text-orange-950 tracking-tight">
                                    Lifetime Basic
                                </h3>

                                <!-- Subtitle -->
                                <p class="text-xs text-gray-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                    Potong overhead cost. Bayar sekali untuk akses selamanya.
                                </p>

                                <!-- Price -->
                                <div class="flex items-baseline mb-6">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-orange-950 tracking-tight">Rp1.500.000</span>
                                    <span class="text-xs text-orange-600 font-normal ml-1.5">lifetime</span>
                                </div>

                                <!-- Features List -->
                                <ul class="space-y-3 text-xs text-gray-700 font-normal mb-8">
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Mencakup semua benefit Monthly.</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Prioritas support & pendampingan setup</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>100% pangkas biaya software (Rp0/bulan)</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Button -->
                            <a href="{{ route('register') }}" class="w-full py-2.5 rounded-xl border border-stone-300 hover:border-stone-400 text-orange-950 bg-white font-medium text-xs sm:text-sm text-center block transition-colors">
                                Beli Akses Permanen
                            </a>
                        </div>

                        <!-- Card 3: Lifetime Custom (Peach/Coral Aura Glow on Top Right) -->
                        <div class="relative bg-white rounded-2xl sm:rounded-3xl p-7 sm:p-8 border border-stone-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-between transition-transform hover:-translate-y-1 overflow-hidden">
                            
                            <!-- Top-Right Aesthetic Peach Glow Curve -->
                            <div class="absolute top-0 right-0 w-44 h-44 bg-gradient-to-bl from-rose-200/50 via-orange-100/30 to-transparent rounded-bl-[120px] pointer-events-none"></div>

                            <div class="relative z-10">
                                <!-- Icon -->
                                <div class="mb-4">
                                    <img src="{{ asset('pricing_icon3_trans.png') }}" alt="Lifetime Custom Icon" class="w-6 h-6 object-contain">
                                </div>

                                <!-- Title -->
                                <h3 class="text-xl sm:text-2xl font-bold text-orange-950 tracking-tight">
                                    Lifetime Custom
                                </h3>

                                <!-- Subtitle -->
                                <p class="text-xs text-gray-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                    Sistem yang adaptasi dengan flow bisnis anda, bukan sebaliknya.
                                </p>

                                <!-- Price -->
                                <div class="flex items-baseline mb-6">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-orange-950 tracking-tight">Rp2.500.000</span>
                                    <span class="text-xs text-orange-600 font-normal ml-1.5">lifetime</span>
                                </div>

                                <!-- Features List -->
                                <ul class="space-y-3 text-xs text-gray-700 font-normal mb-8">
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Mencakup semua benefit Lifetime Basic.</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Custom Feature; bebas request fitur khusus</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Prioritas support & pendampingan setup</span>
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-gray-700 font-bold text-xs">✓</span>
                                        <span>Lifetime update</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Button -->
                            <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20ingin%20konsultasi%20fitur%20custom%20Paket%20Lifetime%20Custom" target="_blank" class="w-full py-2.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-white font-medium text-xs sm:text-sm text-center block transition-colors shadow relative z-10">
                                Konsultasi & Custom Fitur
                            </a>
                        </div>

                    </div>

                    <!-- Footer Text in Canvas -->
                    <div class="mt-12 text-center text-xs text-gray-600 font-normal max-w-2xl mx-auto space-y-1">
                        <p class="font-semibold text-gray-800">Semua paket sudah termasuk All Core System:</p>
                        <p class="text-[11px] text-gray-500">POS Kasir, Table & QR Order, Live Order Dashboard, Laporan Keuangan, Crew Management, dan Menu CMS.</p>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 10. REAL CUSTOMER TESTIMONIALS (Quotes & Social Proof) -->
    <section data-aos="fade-up" class="py-20 bg-white border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-orange-800 bg-orange-100 px-3 py-1 rounded-full">
                    Kisah Nyata
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                    Dipercaya Barista & Pemilik Coffee Shop
                </h2>
                <p class="text-base text-gray-600">
                    Dengarkan bagaimana OQARI mengubah ritme kerja mereka sehari-hari.
                </p>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Testimonial 1 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-orange-100 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-orange-500 text-sm">★★★★★</div>
                        <p class="text-sm text-gray-700 leading-relaxed italic">
                            "Sebelum pakai OQARI, setiap sore kasir kami kewalahan menghadapi antrean orderan manual. Sejak pakai QR Order meja OQARI, tamu langsung duduk dan pesan sendiri. Barista kami lebih fokus meracik kopi dengan konsisten."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-orange-100 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-white flex items-center justify-center font-bold text-xs">
                            RA
                        </div>
                        <div>
                            <div class="font-bold text-orange-950 text-xs">Rian Ardiansyah</div>
                            <div class="text-[11px] text-gray-500">Founder, Senja Artisan Roastery</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-orange-100 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-orange-500 text-sm">★★★★★</div>
                        <p class="text-sm text-gray-700 leading-relaxed italic">
                            "Fitur HPP resep gramasi OQARI luar biasa akurat. Kami langsung tahu kalau pemakaian susu atau sirup melebihi standar resep. Kebocoran stok berkurang hingga 85% di bulan pertama."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-orange-100 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-white flex items-center justify-center font-bold text-xs">
                            DR
                        </div>
                        <div>
                            <div class="font-bold text-orange-950 text-xs">Dian Rahmawati</div>
                            <div class="text-[11px] text-gray-500">Operational Manager, Temu Rasa Cafe</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-orange-100 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-orange-500 text-sm">★★★★★</div>
                        <p class="text-sm text-gray-700 leading-relaxed italic">
                            "Kitchen Display System-nya sangat membantu barista baru. Bunyi bel pesanan masuk keras dan jelas, keterangan oatside/almond milk tercetak mencolok sehingga tidak ada pesanan salah saji lagi."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-orange-100 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-white flex items-center justify-center font-bold text-xs">
                            BS
                        </div>
                        <div>
                            <div class="font-bold text-orange-950 text-xs">Bagas Santoso</div>
                            <div class="text-[11px] text-gray-500">Head Barista, Brew Lab Jakarta</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 11. FAQ ACCORDION -->
    <section data-aos="fade-up" id="faq" class="py-20 bg-stone-50 border-b border-orange-100" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    Pusat Informasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-orange-950 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-base text-gray-600">
                    Masih memiliki pertanyaan seputar implementasi OQARI di kedai kopi Anda?
                </p>
            </div>

            <!-- Accordion Items -->
            <div class="space-y-4">
                
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-orange-100 p-5 cursor-pointer" @click="openFaq = (openFaq === 1 ? null : 1)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-orange-950 text-sm sm:text-base">Apakah pelanggan wajib download aplikasi untuk scan QR meja?</h4>
                        <span class="text-xl font-bold text-orange-600" x-text="openFaq === 1 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 1" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-stone-100 pt-3">
                        Sama sekali tidak! Pelanggan cukup membuka kamera smartphone bawaan atau aplikasi scanner apapun. Menu web interaktif OQARI akan terbuka seketika tanpa perlu registrasi rumit atau download aplikasi di PlayStore/AppStore.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-orange-100 p-5 cursor-pointer" @click="openFaq = (openFaq === 2 ? null : 2)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-orange-950 text-sm sm:text-base">Bagaimana jika koneksi internet di cafe saya sedang lambat atau putus?</h4>
                        <span class="text-xl font-bold text-orange-600" x-text="openFaq === 2 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 2" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-stone-100 pt-3">
                        OQARI POS dilengkapi dengan fitur toleransi jaringan lokal (*offline resilience*). Kasir tetap dapat menerima pesanan tunai dan mencetak struk secara lokal. Ketika koneksi internet pulih, seluruh data transaksi akan tersinkronisasi otomatis ke cloud.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-orange-100 p-5 cursor-pointer" @click="openFaq = (openFaq === 3 ? null : 3)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-orange-950 text-sm sm:text-base">Apakah saya wajib membeli printer atau mesin POS dari OQARI?</h4>
                        <span class="text-xl font-bold text-orange-600" x-text="openFaq === 3 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 3" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-stone-100 pt-3">
                        Tidak. OQARI adalah platform berbasis cloud yang agnostik terhadap perangkat keras. Anda bebas menggunakan tablet Android, iPad, smartphone, atau printer bluetooth standar yang sudah Anda miliki saat ini.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-orange-100 p-5 cursor-pointer" @click="openFaq = (openFaq === 4 ? null : 4)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-orange-950 text-sm sm:text-base">Bagaimana cara memasukkan daftar menu dan resep gramasi pertama kali?</h4>
                        <span class="text-xl font-bold text-orange-600" x-text="openFaq === 4 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 4" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-stone-100 pt-3">
                        Kami menyediakan fitur Onboarding Wizard yang sangat mudah, serta fitur impor Excel/CSV. Selain itu, tim onboarding OQARI siap membantu memasukkan seluruh menu dan formula resep Anda secara cuma-cuma (*free onboarding setup*).
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-orange-100 p-5 cursor-pointer" @click="openFaq = (openFaq === 5 ? null : 5)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-orange-950 text-sm sm:text-base">Apakah ada biaya tambahan atau komisi per transaksi?</h4>
                        <span class="text-xl font-bold text-orange-600" x-text="openFaq === 5 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 5" x-cloak class="mt-3 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-stone-100 pt-3">
                        Tidak ada biaya tersembunyi (*zero hidden fee*). Anda hanya membayar biaya langganan software sesuai paket yang dipilih. Biaya MDR QRIS mengikuti regulasi Bank Indonesia (0.3% untuk UMKM).
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 12. BOTTOM MEGA CTA BANNER (Conversion Drive) -->
    <section data-aos="fade-up" class="cta-gradient-bg py-20 text-white relative overflow-hidden">
        
        <!-- Decorative Ambient Light -->
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-green-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold text-orange-300 bg-orange-400/10 border border-orange-400/20">
                <span>⚡ Coba Gratis Sekarang Selama 14 Hari Penuh</span>
            </div>

            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                Tingkatkan Efisiensi & Omzet <br class="hidden sm:inline">
                Coffee Shop Anda Mulai Hari Ini.
            </h2>

            <p class="text-base sm:text-lg text-orange-700 max-w-2xl mx-auto font-normal">
                Bergabunglah dengan ratusan barista dan pemilik cafe yang telah meninggalkan bon kertas dan antrean panjang. Setup selesai dalam 5 menit.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-9 py-4 rounded-xl font-bold text-base text-white primary-btn-gradient flex items-center justify-center gap-2">
                    <span>Daftar Akun OQARI Gratis</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20ingin%20jadwalkan%20sesi%20demo%20online" target="_blank" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-base text-orange-800 bg-orange-100 hover:bg-stone-700 border border-stone-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.221.771 1.394.857.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.12.553 4.11 1.521 5.836l-1.616 5.908 6.069-1.591c1.666.911 3.578 1.428 5.626 1.428 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span>Jadwalkan Demo Langsung</span>
                </a>
            </div>

        </div>
    </section>

    <!-- 13. RICH FOOTER (Majoo-style corporate + support info) -->
    <footer class="bg-orange-50 text-orange-600 text-xs pt-16 pb-12 border-t border-orange-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
                
                <!-- Col 1: Brand & Contact -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-orange-700 p-1 flex items-center justify-center">
                            <img src="{{ asset('logo-oqari.webp') }}" alt="OQARI Logo" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xl font-black text-white tracking-tight">OQARI</span>
                    </div>
                    <p class="text-orange-600 text-xs leading-relaxed max-w-sm">
                        Platform manajemen operasional, sistem kasir pintar, dan pesanan QR terpadu untuk industri Food & Beverage dan Coffee Shop di Indonesia.
                    </p>
                    <div class="space-y-2 pt-2 text-orange-700">
                        <div class="flex items-center gap-2">
                            <span class="text-orange-400">📍</span>
                            <span>Jakarta Tech Center & Kreatif Hub</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-orange-400">📞</span>
                            <span>WhatsApp: 0812-8888-OQARI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-orange-400">✉️</span>
                            <span>Email: halo@oqari.com</span>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Fitur & Modul -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-white font-bold text-sm tracking-wide">Fitur & Modul</h4>
                    <ul class="space-y-2">
                        <li><a href="#fitur" class="hover:text-white transition-colors">QR E-Menu Meja</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">POS Kasir Layar Sentuh</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">Kitchen Display System (KDS)</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">HPP & Resep Kopi Gramasi</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">Rekonsiliasi Kas Shift Kasir</a></li>
                        <li><a href="#fitur" class="hover:text-white transition-colors">Analitik Bisnis & Laporan Omzet</a></li>
                    </ul>
                </div>

                <!-- Col 3: Solusi Bisnis -->
                <div class="lg:col-span-3 space-y-3">
                    <h4 class="text-white font-bold text-sm tracking-wide">Solusi Bisnis</h4>
                    <ul class="space-y-2">
                        <li><a href="#solusi" class="hover:text-white transition-colors">Specialty Coffee & Roastery</a></li>
                        <li><a href="#solusi" class="hover:text-white transition-colors">Cafe & Dine-In Restaurant</a></li>
                        <li><a href="#solusi" class="hover:text-white transition-colors">Coffee Booth & Kiosk</a></li>
                        <li><a href="#solusi" class="hover:text-white transition-colors">Franchise & Multi-Cabang</a></li>
                        <li><a href="#hardware" class="hover:text-white transition-colors">Dukungan Hardware Printer</a></li>
                    </ul>
                </div>

                <!-- Col 4: Akses Cepat -->
                <div class="lg:col-span-2 space-y-3">
                    <h4 class="text-white font-bold text-sm tracking-wide">Akses Pengguna</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors font-semibold text-orange-400">Login Kasir / Owner →</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors font-semibold text-green-400">Daftar Akun Baru →</a></li>
                        <li><a href="#harga" class="hover:text-white transition-colors">Pilihan Paket Harga</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">Pusat Bantuan & FAQ</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-orange-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-gray-500">
                <p>© {{ date('Y') }} OQARI Indonesia. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-orange-700 transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-orange-700 transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-orange-700 transition-colors">Keamanan Cloud</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- 14. FLOATING WHATSAPP BUTTON (Direct Sales / Live Help) -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="https://wa.me/6281234567890?text=Halo%20Tim%20OQARI,%20saya%20ingin%20tanya%20fitur%20dan%20coba%20demo%20POS%20Coffee%20Shop" target="_blank" class="flex items-center gap-2.5 bg-green-600 hover:bg-green-500 text-white font-bold text-xs py-3 px-4 rounded-full shadow-2xl transition-all hover:scale-105 group border-2 border-white/20">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.221.771 1.394.857.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.12.553 4.11 1.521 5.836l-1.616 5.908 6.069-1.591c1.666.911 3.578 1.428 5.626 1.428 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
            <span class="hidden sm:inline">Tanya Sales OQARI</span>
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        </a>
    </div>


    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>

</html>
