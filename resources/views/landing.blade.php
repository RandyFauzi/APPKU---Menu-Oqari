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

        .amber-gradient-badge {
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
<body class="antialiased selection:bg-amber-100 selection:text-amber-900" x-data="{ mobileMenuOpen: false }">

    <!-- 1. TOP ANNOUNCEMENT BAR (Inspired by Majoo Promo Bar) -->
    <div class="bg-stone-900 text-stone-200 text-xs py-2 px-4 border-b border-stone-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                    PROMO KHUSUS COFFEE SHOP
                </span>
                <span class="font-medium text-stone-300 hidden md:inline">Diskon 30% Paket Tahunan + Free Setup Onboarding Menu & Meja</span>
            </div>
            <div class="flex items-center gap-4 text-stone-400">
                <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20ingin%20konsultasi%20POS%20Coffee%20Shop" target="_blank" class="hover:text-amber-400 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.221.771 1.394.857.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.12.553 4.11 1.521 5.836l-1.616 5.908 6.069-1.591c1.666.911 3.578 1.428 5.626 1.428 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span>OQARI Care: <strong>0812-8888-OQARI</strong></span>
                </a>
                <span class="hidden sm:inline text-stone-600">|</span>
                <span class="hidden sm:flex items-center gap-1.5 text-[11px]">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Sistem Cloud Aktif 99.98%
                </span>
            </div>
        </div>
    </div>

    <!-- 2. STICKY NAVBAR -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-stone-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-700 via-amber-800 to-stone-900 flex items-center justify-center shadow-md shadow-amber-900/10 group-hover:scale-105 transition-transform p-1.5">
                    <img src="{{ asset('logo-oqari.webp') }}" alt="OQARI Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="text-2xl font-black tracking-tight text-stone-900 flex items-center gap-1">
                        OQARI
                        <span class="text-[10px] font-bold px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded uppercase tracking-wider">POS</span>
                    </span>
                    <span class="text-[11px] block font-medium text-stone-500 -mt-1 tracking-wide">Coffee & F&B Ecosystem</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-stone-600">
                <a href="#fitur" class="hover:text-amber-800 transition-colors">Fitur Unggulan</a>
                <a href="#solusi" class="hover:text-amber-800 transition-colors">Solusi Bisnis</a>
                <a href="#demo-qr" class="hover:text-amber-800 transition-colors flex items-center gap-1.5">
                    <span>Demo QR Meja</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 bg-emerald-100 text-emerald-700 rounded-full">Live</span>
                </a>
                <a href="#hardware" class="hover:text-amber-800 transition-colors">Perangkat</a>
                <a href="#harga" class="hover:text-amber-800 transition-colors">Paket & Harga</a>
                <a href="#faq" class="hover:text-amber-800 transition-colors">FAQ</a>
            </nav>

            <!-- CTA Buttons -->
            <div class="hidden sm:flex items-center gap-3">
                @if(auth()->check() && isset($dashboardUrl))
                    <a href="{{ $dashboardUrl }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white primary-btn-gradient">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl font-semibold text-sm text-stone-700 hover:text-stone-900 hover:bg-stone-100 transition-colors border border-stone-200">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white primary-btn-gradient">
                        <span>Coba Gratis 14 Hari</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @endif
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden p-2 rounded-xl text-stone-700 hover:bg-stone-100 focus:outline-none" aria-label="Toggle Menu">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden bg-white border-b border-stone-200 px-4 pt-3 pb-6 space-y-3">
            <a href="#fitur" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">Fitur Unggulan</a>
            <a href="#solusi" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">Solusi Bisnis</a>
            <a href="#demo-qr" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">Demo QR Meja</a>
            <a href="#hardware" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">Perangkat Kompatibel</a>
            <a href="#harga" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">Paket & Harga</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="block py-2 text-stone-700 font-semibold">FAQ</a>
            <div class="pt-4 border-t border-stone-200 flex flex-col gap-2.5">
                @if(auth()->check() && isset($dashboardUrl))
                    <a href="{{ $dashboardUrl }}" class="w-full text-center py-3 rounded-xl font-bold text-white primary-btn-gradient">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2.5 rounded-xl font-semibold text-stone-800 border border-stone-300">
                        Masuk Akun
                    </a>
                    <a href="{{ route('register') }}" class="w-full text-center py-3 rounded-xl font-bold text-white primary-btn-gradient">
                        Coba Gratis 14 Hari
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- 3. HERO SECTION (High-Impact Value Proposition) -->
    <section class="hero-mesh-gradient pt-12 pb-20 lg:pt-16 lg:pb-28 border-b border-stone-200/80 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column (Copy & CTA) -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    <!-- Pill Tag -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold text-amber-900 amber-gradient-badge shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-amber-600 animate-ping"></span>
                        <span>#1 Ekosistem Kasir POS & Smart QR Menu untuk Coffee Shop</span>
                    </div>

                    <!-- Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-stone-950 tracking-tight leading-[1.12]">
                        Kelola Coffee Shop <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-700 via-amber-800 to-amber-950">Lebih Cepat, Rapi,</span> <br>
                        dan Serba Otomatis.
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-lg sm:text-xl text-stone-600 font-normal leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Hilangkan antrean panjang di kasir dengan <strong>QR E-Menu Meja</strong> tanpa install aplikasi. Pesanan otomatis terkirim ke <strong>Kitchen Display Barista</strong>, lengkap dengan kalkulasi HPP resep kopi per gramasi secara akurat.
                    </p>

                    <!-- Feature Capsules (Inspired by Majoo Feature Badges) -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 pt-1">
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-lg text-xs font-semibold text-stone-700 shadow-sm flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> QR Order Meja
                        </span>
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-lg text-xs font-semibold text-stone-700 shadow-sm flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Kasir POS Tablet
                        </span>
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-lg text-xs font-semibold text-stone-700 shadow-sm flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> KDS Barista Realtime
                        </span>
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-lg text-xs font-semibold text-stone-700 shadow-sm flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> HPP & Gramasi Kopi
                        </span>
                        <span class="px-3 py-1 bg-white border border-stone-200 rounded-lg text-xs font-semibold text-stone-700 shadow-sm flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Shift & Cash Drawer
                        </span>
                    </div>

                    <!-- Dual CTAs -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-base text-white primary-btn-gradient flex items-center justify-center gap-3">
                            <span>Mulai Coba Gratis 14 Hari</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                        <a href="#demo-qr" class="w-full sm:w-auto px-7 py-4 rounded-xl font-bold text-base text-stone-800 bg-white hover:bg-stone-50 border border-stone-300 shadow-sm transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-5 h-5 text-amber-600 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                            <span>Lihat Demo Interaktif</span>
                        </a>
                    </div>

                    <!-- Micro-trust signals -->
                    <div class="pt-2 text-xs text-stone-500 flex flex-wrap items-center justify-center lg:justify-start gap-y-2 gap-x-5">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Tanpa Kartu Kredit
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Setup Instan 5 Menit
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Bantuan Pendampingan Tim Ahli
                        </span>
                    </div>

                </div>

                <!-- Right Column (High-End Live Mockup / Visual Showcase) -->
                <div class="lg:col-span-5 relative">
                    
                    <!-- Decorative Backdrop Glow -->
                    <div class="absolute -top-10 -right-10 w-72 h-72 bg-amber-400/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-emerald-400/15 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Main POS Tablet Shell Container -->
                    <div class="relative bg-stone-900 p-3 sm:p-4 rounded-3xl shadow-2xl border-4 border-stone-800">
                        
                        <!-- Top Tablet Bezel Bar -->
                        <div class="flex items-center justify-between pb-3 px-2 text-stone-400 text-xs border-b border-stone-800">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span class="font-bold text-stone-200 ml-2">OQARI POS Terminal</span>
                            </div>
                            <span class="text-[11px] font-mono text-emerald-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                Live Sync (Barista & Kasir)
                            </span>
                        </div>

                        <!-- Mockup Body Content -->
                        <div class="bg-stone-950 rounded-2xl p-4 mt-3 space-y-4 text-white">
                            
                            <!-- Header Info -->
                            <div class="flex items-center justify-between bg-stone-900/80 p-3 rounded-xl border border-stone-800">
                                <div>
                                    <div class="text-xs text-stone-400">Outlet Aktif</div>
                                    <div class="font-bold text-sm text-stone-100">Kopi Senja Utama (Meja 01 - 24)</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-stone-400">Total Transaksi Hari Ini</div>
                                    <div class="font-bold text-sm text-amber-400">Rp 4.850.000 <span class="text-[10px] text-emerald-400">(↑ 24%)</span></div>
                                </div>
                            </div>

                            <!-- Live Ticket 1 (Preparing in Barista) -->
                            <div class="bg-stone-900/90 p-3.5 rounded-xl border border-amber-500/30 relative overflow-hidden">
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/40">
                                        Meja #04 • QR Self-Order
                                    </span>
                                    <span class="text-stone-400 font-mono text-[11px]">10:32 WIB</span>
                                </div>
                                <div class="space-y-1.5 text-xs text-stone-200">
                                    <div class="flex justify-between font-medium">
                                        <span>2x Spanish Aren Latte (Oatmilk, Less Sugar)</span>
                                        <span class="text-stone-400">Rp 64.000</span>
                                    </div>
                                    <div class="flex justify-between font-medium">
                                        <span>1x Almond Croissant (Warm)</span>
                                        <span class="text-stone-400">Rp 28.000</span>
                                    </div>
                                </div>
                                <div class="mt-3 pt-2.5 border-t border-stone-800 flex items-center justify-between text-xs">
                                    <span class="text-emerald-400 font-semibold flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Lunas via QRIS Meja
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 text-[10px] font-bold">
                                        KDS: Sedang Diracik (Barista)
                                    </span>
                                </div>
                            </div>

                            <!-- Live Ticket 2 (Ready to Serve) -->
                            <div class="bg-stone-900/50 p-3 rounded-xl border border-stone-800 text-xs">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="font-semibold text-stone-300">Meja #09 • Kasir POS</span>
                                    <span class="text-[10px] px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold rounded">
                                        Siap Disajikan
                                    </span>
                                </div>
                                <div class="text-stone-400 text-[11px]">
                                    1x V60 Gayo Anaerob (Hot) • 1x Cinnamon Roll
                                </div>
                            </div>

                        </div>

                        <!-- Floating Customer Smartphone Mockup (Overlapping right bottom) -->
                        <div class="hidden sm:block absolute -bottom-6 -right-6 w-52 bg-white rounded-2xl shadow-2xl border-4 border-stone-900 p-2.5 text-stone-900 animate-bounce" style="animation-duration: 4s;">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <span class="text-[10px] font-bold text-stone-700">Scan QR Menu Meja</span>
                            </div>
                            <div class="pt-2 text-center">
                                <div class="text-[11px] font-bold text-stone-900">Spanish Latte</div>
                                <div class="text-[10px] text-amber-700 font-bold">Rp 32.000</div>
                                <div class="mt-2 py-1 px-2 rounded-lg bg-amber-700 text-white text-[10px] font-bold">
                                    + Tambah ke Keranjang
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- 4. SOCIAL PROOF & STATS BAR (Majoo-style trust numbers) -->
    <section class="py-12 bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Headline -->
            <p class="text-center text-xs font-bold text-stone-500 uppercase tracking-widest mb-8">
                Telah Dipercaya oleh 500+ Pemilik Coffee Shop, Roastery, dan Artisan Cafe di Seluruh Indonesia
            </p>

            <!-- 4 Metrics Counters -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center divide-y md:divide-y-0 md:divide-x divide-stone-200">
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">500+</div>
                    <div class="text-xs sm:text-sm font-medium text-stone-500 mt-1">Outlet Cafe Aktif</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-amber-800 tracking-tight">3.2 Juta+</div>
                    <div class="text-xs sm:text-sm font-medium text-stone-500 mt-1">Pesanan Sukses Diproses</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">3x Cepat</div>
                    <div class="text-xs sm:text-sm font-medium text-stone-500 mt-1">Putaran Meja Saat Peak Hour</div>
                </div>
                <div class="pt-4 md:pt-0">
                    <div class="text-3xl sm:text-4xl font-extrabold text-emerald-600 tracking-tight">99.98%</div>
                    <div class="text-xs sm:text-sm font-medium text-stone-500 mt-1">Uptime Server Cloud</div>
                </div>
            </div>

            <!-- Client Logos Mockup -->
            <div class="mt-10 pt-8 border-t border-stone-100 flex flex-wrap items-center justify-center gap-8 sm:gap-14 opacity-75 grayscale hover:grayscale-0 transition-all">
                <span class="font-black text-lg tracking-tighter text-stone-800">SENJA ROASTERY</span>
                <span class="font-extrabold text-lg tracking-wide text-stone-800">KOPI TEMU RASA</span>
                <span class="font-bold text-lg tracking-widest text-stone-800">ARTISAN BREW LAB</span>
                <span class="font-semibold text-lg italic text-stone-800">Daily Dose Cafe</span>
                <span class="font-black text-lg tracking-tight text-stone-800">SUDUT KOTA COFFEE</span>
            </div>

        </div>
    </section>

    <!-- 5. CORE VALUE TRIO (Majoo-style 3 Pillars Grid) -->
    <section id="fitur" class="py-20 bg-stone-50 border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                    Fitur Canggih & Komprehensif
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Dirancang Khusus untuk Alur Kerja <br class="hidden sm:inline">
                    Bisnis Kopi & Cafe yang Cepat
                </h2>
                <p class="text-base sm:text-lg text-stone-600">
                    Tinggalkan sistem kasir lama yang kaku. OQARI menyinkronkan meja, kasir, barista, dan pemilik dalam satu alur kerja mulus.
                </p>
            </div>

            <!-- 3 Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Card 1: QR Self Ordering -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-stone-700 rounded-md">Tanpa Install App</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-amber-50 text-amber-800 rounded-md">QRIS Dinamis</span>
                        </div>

                        <h3 class="text-xl font-bold text-stone-900 mb-3">
                            Smart QR E-Menu & Table Self-Order
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-6">
                            Pelanggan cukup scan QR di atas meja untuk melihat menu visual estetik, kustomisasi pilihan kopi (es, gula, jenis susu), dan bayar instan. Mengurai 60% antrean kasir.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-amber-800">
                        <span>Pangkas waktu tunggu tamu</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 2: POS & Kitchen Display (KDS) -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-stone-700 rounded-md">KDS Barista</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-amber-50 text-amber-800 rounded-md">Cetak Struk Kilat</span>
                        </div>

                        <h3 class="text-xl font-bold text-stone-900 mb-3">
                            POS Kasir Cepat & Kitchen Display (KDS)
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-6">
                            Pesanan kasir atau QR langsung muncul di layar tablet barista dengan bunyi notifikasi otomatis. Tidak ada lagi kertas bon hilang, uap kopi merusak struk, atau salah resep.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-amber-800">
                        <span>Sinkronisasi audio & visual 0 detik</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

                <!-- Card 3: Recipe Costing & HPP Gramasi -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200 card-soft-shadow flex flex-col justify-between">
                    <div>
                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-800 mb-6">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>

                        <!-- Capsules -->
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-stone-100 text-stone-700 rounded-md">Gramasi Kopi & Susu</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 bg-amber-50 text-amber-800 rounded-md">Cegah Kebocoran</span>
                        </div>

                        <h3 class="text-xl font-bold text-stone-900 mb-3">
                            Manajemen Resep & HPP Otomatis
                        </h3>
                        <p class="text-sm text-stone-600 leading-relaxed mb-6">
                            Setiap cangkir Latte otomatis memotong 18 gram biji espresso dan 150ml susu. Lacak margin kotor tiap menu secara real-time dan dapatkan notifikasi saat bahan menipis.
                        </p>
                    </div>

                    <div class="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-amber-800">
                        <span>Kontrol margin profit akurat</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 6. INTERACTIVE SOLUTION TABS (Segmentasi Industri ala Majoo Solusi Bisnis) -->
    <section id="solusi" class="py-20 bg-white border-b border-stone-200" x-data="{ activeTab: 'specialty' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-500">
                    Solusi Terarah
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Cocok untuk Segala Format Bisnis Kopi & FnB
                </h2>
                <p class="text-base text-stone-600">
                    Pilih format operasional Anda untuk melihat bagaimana OQARI meningkatkan efisiensi harian.
                </p>
            </div>

            <!-- Tab Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
                <button @click="activeTab = 'specialty'" :class="activeTab === 'specialty' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    ☕ Specialty Coffee & Roastery
                </button>
                <button @click="activeTab = 'cafe'" :class="activeTab === 'cafe' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🍽️ Cafe & Casual Dining
                </button>
                <button @click="activeTab = 'booth'" :class="activeTab === 'booth' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🚀 Coffee Booth & Grab-and-Go
                </button>
                <button @click="activeTab = 'chain'" :class="activeTab === 'chain' ? 'bg-stone-900 text-white shadow-md' : 'bg-stone-100 text-stone-700 hover:bg-stone-200'" class="px-5 py-2.5 rounded-full text-sm font-bold transition-all">
                    🏢 Multi-Outlet & Franchise
                </button>
            </div>

            <!-- Tab Content Panels -->
            <div class="bg-stone-50 rounded-3xl p-8 sm:p-12 border border-stone-200 card-soft-shadow">
                
                <!-- Panel 1: Specialty Coffee -->
                <div x-show="activeTab === 'specialty'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-800 rounded-md">Untuk Artisan Roaster & Specialty Cafe</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-stone-900">
                            Kustomisasi Single Origin, Manual Brew, & Modifikasi Tanpa Batas
                        </h3>
                        <p class="text-stone-600 leading-relaxed text-sm sm:text-base">
                            Tampilkan profil rasa (*tasting notes*), proses pascapanen (*Natural, Washed, Anaerobic*), dan metode seduh (*V60, Kalita, Aeropress*) langsung di QR menu pelanggan. Barista menerima pesanan dengan rincian rasio gramasi dan suhu air yang presisi.
                        </p>
                        <ul class="space-y-2.5 text-sm text-stone-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tracking batch roasting biji kopi & tanggal resting beans
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Pilihan susu oatmilk, almond, soy milk dengan penyesuaian harga instan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Penjualan beans ritel 200g/1kg dengan barcode scan terintegrasi
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">Live Simulation: Barista Brew Bar</div>
                        <div class="space-y-3">
                            <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-stone-800 text-sm">V60 Ethiopia Guji (Natural)</div>
                                    <div class="text-xs text-stone-500">Grind: Medium-Fine • Ratio: 1:15 • Temp: 92°C</div>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded">Barista #1</span>
                            </div>
                            <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-stone-800 text-sm">Magic (Double Ristretto + Oatmilk)</div>
                                    <div class="text-xs text-stone-500">Beans: House Blend (Brazil x Flores) • 160ml cup</div>
                                </div>
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded">Espresso Bar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Cafe & Casual Dining -->
                <div x-show="activeTab === 'cafe'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-800 rounded-md">Untuk Cafe Resto & Tempat Nongkrong</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-stone-900">
                            Manajemen Denah Meja, Split Bill, & Multi-Kitchen Printer
                        </h3>
                        <p class="text-stone-600 leading-relaxed text-sm sm:text-base">
                            Atur nomor meja hingga 100+ titik dengan layout visual. Pesanan minuman otomatis terkirim ke printer Barista, sementara makanan berat (*hot kitchen*) terkirim ke printer Chef dapur.
                        </p>
                        <ul class="space-y-2.5 text-sm text-stone-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Pisah tagihan (Split Bill) per orang atau per item dengan mudah
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Open Bill (Pesan dulu, bayar nanti saat selesai nongkrong)
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tambah pesanan baru (*round 2*) langsung dari QR meja tanpa antre ulang
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">Live Table Grid Management</div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                                <div class="font-bold text-emerald-800 text-sm">Meja 01</div>
                                <div class="text-[10px] text-emerald-600">Kosong (Tersedia)</div>
                            </div>
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-center">
                                <div class="font-bold text-amber-800 text-sm">Meja 02</div>
                                <div class="text-[10px] text-amber-600">Terisi (Pesan QR)</div>
                            </div>
                            <div class="p-3 bg-amber-50 border border-amber-300 rounded-xl text-center">
                                <div class="font-bold text-amber-800 text-sm">Meja 03</div>
                                <div class="text-[10px] text-amber-600">Terisi (Open Bill)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Coffee Booth -->
                <div x-show="activeTab === 'booth'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-800 rounded-md">Untuk Gerai Kiosk & Kopi Susu Kekinian</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-stone-900">
                            Cepat, Ringkas, Cukup 1 Smartphone / Tablet Android
                        </h3>
                        <p class="text-stone-600 leading-relaxed text-sm sm:text-base">
                            Cocok untuk ruang sempit di stasiun, ruko mini, atau foodcourt. 1 kasir barista bisa memproses 100+ cup per jam dengan mode input cepat 2 ketukan.
                        </p>
                        <ul class="space-y-2.5 text-sm text-stone-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Sambungkan ke printer thermal bluetooth 58mm portable
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Tampilkan QRIS statis atau dinamis di layar pelanggan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Rekap omzet harian dikirim otomatis ke WhatsApp Owner
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">Quick Tap Fast-Checkout</div>
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl">
                                <div class="font-bold text-sm text-stone-800">Kopi Susu Aren</div>
                                <div class="text-xs text-amber-700 font-semibold">Rp 18.000</div>
                            </div>
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl">
                                <div class="font-bold text-sm text-stone-800">Americano Ice</div>
                                <div class="text-xs text-amber-700 font-semibold">Rp 15.000</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Multi Outlet Chain -->
                <div x-show="activeTab === 'chain'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-6 space-y-5">
                        <span class="text-xs font-bold px-3 py-1 bg-amber-100 text-amber-800 rounded-md">Untuk Franchise & Multi-Cabang</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-stone-900">
                            Kontrol Puluhan Cabang Terpusat dari Satu Layar SuperAdmin
                        </h3>
                        <p class="text-stone-600 leading-relaxed text-sm sm:text-base">
                            Bandingkan performa penjualan antar cabang, kendalikan harga menu regional, kelola hak akses staf (Kasir, Barista, Manager, Owner), dan distribusikan stok bahan baku dari gudang pusat.
                        </p>
                        <ul class="space-y-2.5 text-sm text-stone-700 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Dashboard konsolidasian omzet realtime seluruh kota
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                Audit log kasir & rekonsiliasi kas shift bebas kecurangan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">✓</span>
                                API webhook untuk integrasi ERP & akuntansi perusahaan
                            </li>
                        </ul>
                    </div>
                    <div class="lg:col-span-6 bg-white p-6 rounded-2xl border border-stone-200">
                        <div class="text-xs font-bold text-stone-400 uppercase tracking-wider mb-3">Multi-Branch Analytics</div>
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-stone-800">Cabang Senopati, Jaksel</span>
                                <span class="font-mono font-bold text-emerald-600">Rp 14.250.000 (100%)</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-stone-800">Cabang Riau, Bandung</span>
                                <span class="font-mono font-bold text-emerald-600">Rp 11.890.000 (83%)</span>
                            </div>
                            <div class="flex justify-between items-center p-2.5 bg-stone-50 rounded-xl text-xs">
                                <span class="font-bold text-stone-800">Cabang Gubeng, Surabaya</span>
                                <span class="font-mono font-bold text-emerald-600">Rp 9.420.000 (66%)</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 7. INTERACTIVE LIVE QR MENU DEMO (Experience OQARI Live in Browser) -->
    <section id="demo-qr" class="py-20 bg-stone-100 border-b border-stone-200" x-data="{ 
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
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                        Simulator Interaktif
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                        Coba Sensasi Memesan <br>
                        Langsung dari Layar Ini
                    </h2>
                    <p class="text-base text-stone-600 leading-relaxed">
                        Inilah tampilan yang akan dilihat oleh pelanggan Anda saat mereka memindai kode QR di meja cafe. Desain modern, cepat, dan terhubung langsung ke kasir serta dapur tanpa hambatan.
                    </p>
                    
                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm flex-shrink-0">1</div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-sm">Pelanggan Duduk & Scan QR di Meja</h4>
                                <p class="text-xs text-stone-500">Kamera smartphone langsung membuka daftar menu tanpa unduh aplikasi apapun.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm flex-shrink-0">2</div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-sm">Pilih Kopi, Modifier Topping, & Catatan</h4>
                                <p class="text-xs text-stone-500">Pilihan gula, es, dan susu terstruktur rapi tanpa resiko salah dengar.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm flex-shrink-0">3</div>
                            <div>
                                <h4 class="font-bold text-stone-900 text-sm">Pesanan Otomatis Masuk ke Barista</h4>
                                <p class="text-xs text-stone-500">Layar dapur barista langsung berbunyi 'ting!' dan menampilkan resep secara real-time.</p>
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
                    
                    <div class="w-full max-w-sm bg-stone-950 p-4 rounded-[40px] shadow-2xl border-4 border-stone-800 relative">
                        
                        <!-- Top Phone Notch -->
                        <div class="w-36 h-4 bg-stone-800 rounded-b-xl mx-auto mb-3"></div>

                        <!-- Screen Area -->
                        <div class="bg-white rounded-[28px] overflow-hidden text-stone-900 flex flex-col h-[520px]">
                            
                            <!-- App Header -->
                            <div class="bg-amber-950 text-white p-4">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        OQARI Coffee Lab
                                    </span>
                                    <span class="bg-amber-900/80 px-2 py-0.5 rounded text-[10px] font-mono">Meja #04</span>
                                </div>
                                <h3 class="text-base font-extrabold">E-Menu Digital</h3>
                            </div>

                            <!-- Menu Items List -->
                            <div class="p-3.5 overflow-y-auto flex-1 space-y-2.5">
                                
                                <div class="text-[11px] font-bold text-stone-400 uppercase tracking-wider">Pilih Menu untuk Menambah:</div>

                                <!-- Item 1 -->
                                <div class="p-2.5 rounded-xl border border-stone-200 hover:border-amber-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-stone-900">Spanish Aren Latte</div>
                                        <div class="text-[10px] text-stone-500">Espresso + Susu Aren Creamy</div>
                                        <div class="text-xs font-bold text-amber-800 mt-1">Rp 32.000</div>
                                    </div>
                                    <button @click="addItem('Spanish Aren Latte', 32000, 'Less Sugar, Ice')" type="button" class="px-2.5 py-1.5 rounded-lg bg-amber-800 text-white text-[11px] font-bold hover:bg-amber-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Item 2 -->
                                <div class="p-2.5 rounded-xl border border-stone-200 hover:border-amber-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-stone-900">Americano On The Rocks</div>
                                        <div class="text-[10px] text-stone-500">Double Shot Blend Specialty</div>
                                        <div class="text-xs font-bold text-amber-800 mt-1">Rp 24.000</div>
                                    </div>
                                    <button @click="addItem('Americano On The Rocks', 24000, 'Normal Ice')" type="button" class="px-2.5 py-1.5 rounded-lg bg-amber-800 text-white text-[11px] font-bold hover:bg-amber-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Item 3 -->
                                <div class="p-2.5 rounded-xl border border-stone-200 hover:border-amber-500 transition-all flex items-center justify-between bg-stone-50">
                                    <div>
                                        <div class="font-bold text-xs text-stone-900">Butter Croissant</div>
                                        <div class="text-[10px] text-stone-500">Flaky French Pastry (Warm)</div>
                                        <div class="text-xs font-bold text-amber-800 mt-1">Rp 26.000</div>
                                    </div>
                                    <button @click="addItem('Butter Croissant', 26000, 'Dipanaskan')" type="button" class="px-2.5 py-1.5 rounded-lg bg-amber-800 text-white text-[11px] font-bold hover:bg-amber-900">
                                        + Tambah
                                    </button>
                                </div>

                                <!-- Order Cart Preview -->
                                <div class="pt-2 border-t border-stone-200">
                                    <div class="text-[11px] font-bold text-stone-600 mb-1 flex justify-between">
                                        <span>Keranjang Pesanan:</span>
                                        <span x-text="selectedItems.length + ' Item'"></span>
                                    </div>
                                    <template x-for="(item, idx) in selectedItems" :key="idx">
                                        <div class="flex items-center justify-between py-1 text-xs border-b border-stone-100">
                                            <div>
                                                <div class="font-semibold text-stone-800" x-text="item.name"></div>
                                                <div class="text-[10px] text-stone-400" x-text="item.notes"></div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-stone-700" x-text="'Rp ' + item.price.toLocaleString('id-ID')"></span>
                                                <button @click="removeItem(idx)" class="text-red-500 hover:text-red-700 text-xs font-bold" title="Hapus">×</button>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                            </div>

                            <!-- Bottom Floating Action -->
                            <div class="p-3 bg-stone-50 border-t border-stone-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-stone-500">Estimasi Total:</span>
                                    <span class="text-base font-extrabold text-stone-900" x-text="'Rp ' + cartTotal().toLocaleString('id-ID')"></span>
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
    <section id="hardware" class="py-20 bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-500">
                    Bebas Tanpa Lock-in
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Gunakan Perangkat yang Sudah Anda Miliki
                </h2>
                <p class="text-base text-stone-600">
                    Tidak perlu merogoh kocek puluhan juta untuk mesin POS khusus. OQARI berjalan lancar di browser iPad, tablet Android, laptop, smartphone, dan printer kasir yang beredar di pasaran.
                </p>
            </div>

            <!-- Hardware Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                
                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 hover:border-amber-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold mb-4">
                        📱
                    </div>
                    <h4 class="font-bold text-stone-900 mb-1">Tablet & iPad</h4>
                    <p class="text-xs text-stone-500">Apple iPad, Samsung Galaxy Tab, Xiaomi Pad, tablet Android apapun.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 hover:border-amber-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold mb-4">
                        🖨️
                    </div>
                    <h4 class="font-bold text-stone-900 mb-1">Thermal Printer</h4>
                    <p class="text-xs text-stone-500">Printer struk 58mm & 80mm via Bluetooth, USB, maupun Ethernet LAN.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 hover:border-amber-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold mb-4">
                        📟
                    </div>
                    <h4 class="font-bold text-stone-900 mb-1">Mini POS Terminal</h4>
                    <p class="text-xs text-stone-500">Kompatibel dengan perangkat all-in-one Sunmi, iMin, Advan POS.</p>
                </div>

                <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200 hover:border-amber-400 transition-colors">
                    <div class="w-12 h-12 mx-auto rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold mb-4">
                        💻
                    </div>
                    <h4 class="font-bold text-stone-900 mb-1">PC / Laptop / Mac</h4>
                    <p class="text-xs text-stone-500">Akses modul POS dan analitik lengkap melalui browser Google Chrome / Safari.</p>
                </div>

            </div>

        </div>
    </section>

    <!-- 9. PRICING PLANS (Majoo-style transparent comparison) -->
    <section id="harga" class="py-20 bg-stone-50 border-b border-stone-200" x-data="{ isAnnual: true }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                    Investasi Transparan
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Harga Bersahabat untuk Mendukung <br class="hidden sm:inline">
                    Pertumbuhan Coffee Shop Anda
                </h2>
                <p class="text-base text-stone-600">
                    Semua paket sudah termasuk update fitur berkala, penyimpanan cloud aman, dan dukungan teknis.
                </p>

                <!-- Billing Switcher Toggle -->
                <div class="pt-4 flex items-center justify-center gap-3">
                    <span :class="!isAnnual ? 'font-bold text-stone-900' : 'text-stone-500'" class="text-sm">Tagihan Bulanan</span>
                    <button @click="isAnnual = !isAnnual" type="button" class="w-14 h-8 rounded-full bg-stone-900 p-1 flex items-center transition-colors relative" aria-label="Toggle Billing">
                        <span :class="isAnnual ? 'translate-x-6 bg-amber-400' : 'translate-x-0 bg-white'" class="w-6 h-6 rounded-full transition-transform shadow-md"></span>
                    </button>
                    <span :class="isAnnual ? 'font-bold text-stone-900' : 'text-stone-500'" class="text-sm flex items-center gap-1.5">
                        <span>Tagihan Tahunan</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Hemat 20%
                        </span>
                    </span>
                </div>
            </div>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">
                
                <!-- Plan 1: Starter Barista -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200 flex flex-col justify-between card-soft-shadow">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-2">Starter Barista</div>
                        <h3 class="text-2xl font-bold text-stone-900 mb-2">Coffee Kiosk / Booth</h3>
                        <p class="text-xs text-stone-500 mb-6">Cocok untuk gerai kecil atau kedai kopi take-away 1 outlet.</p>
                        
                        <div class="mb-6 pb-6 border-b border-stone-100">
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-stone-950" x-text="isAnnual ? 'Rp 79.000' : 'Rp 99.000'"></span>
                                <span class="text-xs text-stone-500">/ bulan</span>
                            </div>
                            <div class="text-[11px] text-stone-400 mt-1" x-text="isAnnual ? 'Ditagih tahunan (Rp 948.000 / thn)' : 'Ditagih per bulan'"></div>
                        </div>

                        <ul class="space-y-3 text-xs text-stone-700 font-medium mb-8">
                            <li class="flex items-center gap-2">✓ 1 Outlet & 2 Akun Kasir</li>
                            <li class="flex items-center gap-2">✓ POS Kasir Tablet & Smartphone</li>
                            <li class="flex items-center gap-2">✓ Cetak Struk Bluetooth 58mm/80mm</li>
                            <li class="flex items-center gap-2">✓ QR Menu Katalog Online</li>
                            <li class="flex items-center gap-2">✓ Laporan Penjualan Harian & Kas Shift</li>
                            <li class="flex items-center gap-2 text-stone-400">✗ KDS Barista Realtime</li>
                            <li class="flex items-center gap-2 text-stone-400">✗ Resep Gramasi & HPP Otomatis</li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="w-full py-3 rounded-xl font-bold text-sm text-center text-stone-800 bg-stone-100 hover:bg-stone-200 transition-colors">
                        Pilih Starter
                    </a>
                </div>

                <!-- Plan 2: Pro Coffee House (Popular Badge) -->
                <div class="bg-stone-900 text-white rounded-3xl p-8 border-2 border-amber-500 flex flex-col justify-between shadow-2xl relative">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-black text-[11px] uppercase tracking-wider shadow">
                        Paling Banyak Dipilih
                    </div>

                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-amber-400 mb-2">Pro Coffee House</div>
                        <h3 class="text-2xl font-bold text-white mb-2">Cafe & Dine-In Resto</h3>
                        <p class="text-xs text-stone-400 mb-6">Solusi lengkap untuk kedai kopi dengan meja dine-in & pesanan dinamis.</p>
                        
                        <div class="mb-6 pb-6 border-b border-stone-800">
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold text-amber-400" x-text="isAnnual ? 'Rp 159.000' : 'Rp 199.000'"></span>
                                <span class="text-xs text-stone-400">/ bulan</span>
                            </div>
                            <div class="text-[11px] text-stone-500 mt-1" x-text="isAnnual ? 'Ditagih tahunan (Rp 1.908.000 / thn)' : 'Ditagih per bulan'"></div>
                        </div>

                        <ul class="space-y-3 text-xs text-stone-200 font-medium mb-8">
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Semua Fitur Paket Starter</li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> <strong>QR Table Self-Ordering Tanpa Batas</strong></li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> <strong>Kitchen Display System (KDS Barista)</strong></li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> <strong>Kalkulasi HPP & Resep Gramasi Kopi</strong></li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Manajemen Denah Meja & Split Bill</li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Notifikasi Audio Pesanan Otomatis</li>
                            <li class="flex items-center gap-2"><span class="text-amber-400 font-bold">✓</span> Support Prioritas via WhatsApp</li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="w-full py-3.5 rounded-xl font-bold text-sm text-center text-stone-950 bg-amber-400 hover:bg-amber-300 transition-colors shadow-lg shadow-amber-500/20">
                        Coba Gratis 14 Hari
                    </a>
                </div>

                <!-- Plan 3: Enterprise Multi-Outlet -->
                <div class="bg-white rounded-3xl p-8 border border-stone-200 flex flex-col justify-between card-soft-shadow">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-stone-500 mb-2">Enterprise</div>
                        <h3 class="text-2xl font-bold text-stone-900 mb-2">Franchise & Chain</h3>
                        <p class="text-xs text-stone-500 mb-6">Untuk brand dengan banyak cabang, central kitchen, atau franchise.</p>
                        
                        <div class="mb-6 pb-6 border-b border-stone-100">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold text-stone-950">Custom Plan</span>
                            </div>
                            <div class="text-[11px] text-stone-400 mt-1">Konsultasikan kebutuhan spesifik cabang Anda</div>
                        </div>

                        <ul class="space-y-3 text-xs text-stone-700 font-medium mb-8">
                            <li class="flex items-center gap-2">✓ Multi-Outlet Tak Terbatas</li>
                            <li class="flex items-center gap-2">✓ Dashboard Konsolidasi SuperAdmin</li>
                            <li class="flex items-center gap-2">✓ Central Kitchen & Warehouse Transfer</li>
                            <li class="flex items-center gap-2">✓ Hak Akses Multi-Level (Owner, Spv, Barista)</li>
                            <li class="flex items-center gap-2">✓ Integrasi API & Custom Domain URL</li>
                            <li class="flex items-center gap-2">✓ Dedicated Account Manager & Onsite Training</li>
                        </ul>
                    </div>

                    <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20tertarik%20dengan%20Paket%20Enterprise%20Multi-Outlet" target="_blank" class="w-full py-3 rounded-xl font-bold text-sm text-center text-stone-800 bg-stone-100 hover:bg-stone-200 transition-colors">
                        Hubungi Tim Sales
                    </a>
                </div>

            </div>

        </div>
    </section>

    <!-- 10. REAL CUSTOMER TESTIMONIALS (Quotes & Social Proof) -->
    <section class="py-20 bg-white border-b border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                    Kisah Nyata
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Dipercaya Barista & Pemilik Coffee Shop
                </h2>
                <p class="text-base text-stone-600">
                    Dengarkan bagaimana OQARI mengubah ritme kerja mereka sehari-hari.
                </p>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Testimonial 1 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-500 text-sm">★★★★★</div>
                        <p class="text-sm text-stone-700 leading-relaxed italic">
                            "Sebelum pakai OQARI, setiap sore kasir kami kewalahan menghadapi antrean orderan manual. Sejak pakai QR Order meja OQARI, tamu langsung duduk dan pesan sendiri. Barista kami lebih fokus meracik kopi dengan konsisten."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-stone-200 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-stone-800 text-white flex items-center justify-center font-bold text-xs">
                            RA
                        </div>
                        <div>
                            <div class="font-bold text-stone-900 text-xs">Rian Ardiansyah</div>
                            <div class="text-[11px] text-stone-500">Founder, Senja Artisan Roastery</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-500 text-sm">★★★★★</div>
                        <p class="text-sm text-stone-700 leading-relaxed italic">
                            "Fitur HPP resep gramasi OQARI luar biasa akurat. Kami langsung tahu kalau pemakaian susu atau sirup melebihi standar resep. Kebocoran stok berkurang hingga 85% di bulan pertama."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-stone-200 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-stone-800 text-white flex items-center justify-center font-bold text-xs">
                            DR
                        </div>
                        <div>
                            <div class="font-bold text-stone-900 text-xs">Dian Rahmawati</div>
                            <div class="text-[11px] text-stone-500">Operational Manager, Temu Rasa Cafe</div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="p-8 rounded-3xl bg-stone-50 border border-stone-200 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex text-amber-500 text-sm">★★★★★</div>
                        <p class="text-sm text-stone-700 leading-relaxed italic">
                            "Kitchen Display System-nya sangat membantu barista baru. Bunyi bel pesanan masuk keras dan jelas, keterangan oatside/almond milk tercetak mencolok sehingga tidak ada pesanan salah saji lagi."
                        </p>
                    </div>
                    <div class="pt-6 border-t border-stone-200 mt-6 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-stone-800 text-white flex items-center justify-center font-bold text-xs">
                            BS
                        </div>
                        <div>
                            <div class="font-bold text-stone-900 text-xs">Bagas Santoso</div>
                            <div class="text-[11px] text-stone-500">Head Barista, Brew Lab Jakarta</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 11. FAQ ACCORDION -->
    <section id="faq" class="py-20 bg-stone-50 border-b border-stone-200" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-16 space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-500">
                    Pusat Informasi
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-base text-stone-600">
                    Masih memiliki pertanyaan seputar implementasi OQARI di kedai kopi Anda?
                </p>
            </div>

            <!-- Accordion Items -->
            <div class="space-y-4">
                
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-stone-200 p-5 cursor-pointer" @click="openFaq = (openFaq === 1 ? null : 1)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Apakah pelanggan wajib download aplikasi untuk scan QR meja?</h4>
                        <span class="text-xl font-bold text-stone-400" x-text="openFaq === 1 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 1" x-cloak class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3">
                        Sama sekali tidak! Pelanggan cukup membuka kamera smartphone bawaan atau aplikasi scanner apapun. Menu web interaktif OQARI akan terbuka seketika tanpa perlu registrasi rumit atau download aplikasi di PlayStore/AppStore.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-stone-200 p-5 cursor-pointer" @click="openFaq = (openFaq === 2 ? null : 2)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Bagaimana jika koneksi internet di cafe saya sedang lambat atau putus?</h4>
                        <span class="text-xl font-bold text-stone-400" x-text="openFaq === 2 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 2" x-cloak class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3">
                        OQARI POS dilengkapi dengan fitur toleransi jaringan lokal (*offline resilience*). Kasir tetap dapat menerima pesanan tunai dan mencetak struk secara lokal. Ketika koneksi internet pulih, seluruh data transaksi akan tersinkronisasi otomatis ke cloud.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-stone-200 p-5 cursor-pointer" @click="openFaq = (openFaq === 3 ? null : 3)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Apakah saya wajib membeli printer atau mesin POS dari OQARI?</h4>
                        <span class="text-xl font-bold text-stone-400" x-text="openFaq === 3 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 3" x-cloak class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3">
                        Tidak. OQARI adalah platform berbasis cloud yang agnostik terhadap perangkat keras. Anda bebas menggunakan tablet Android, iPad, smartphone, atau printer bluetooth standar yang sudah Anda miliki saat ini.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-stone-200 p-5 cursor-pointer" @click="openFaq = (openFaq === 4 ? null : 4)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Bagaimana cara memasukkan daftar menu dan resep gramasi pertama kali?</h4>
                        <span class="text-xl font-bold text-stone-400" x-text="openFaq === 4 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 4" x-cloak class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3">
                        Kami menyediakan fitur Onboarding Wizard yang sangat mudah, serta fitur impor Excel/CSV. Selain itu, tim onboarding OQARI siap membantu memasukkan seluruh menu dan formula resep Anda secara cuma-cuma (*free onboarding setup*).
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-stone-200 p-5 cursor-pointer" @click="openFaq = (openFaq === 5 ? null : 5)">
                    <div class="flex justify-between items-center">
                        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Apakah ada biaya tambahan atau komisi per transaksi?</h4>
                        <span class="text-xl font-bold text-stone-400" x-text="openFaq === 5 ? '−' : '+'"></span>
                    </div>
                    <div x-show="openFaq === 5" x-cloak class="mt-3 text-xs sm:text-sm text-stone-600 leading-relaxed border-t border-stone-100 pt-3">
                        Tidak ada biaya tersembunyi (*zero hidden fee*). Anda hanya membayar biaya langganan software sesuai paket yang dipilih. Biaya MDR QRIS mengikuti regulasi Bank Indonesia (0.3% untuk UMKM).
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- 12. BOTTOM MEGA CTA BANNER (Conversion Drive) -->
    <section class="cta-gradient-bg py-20 text-white relative overflow-hidden">
        
        <!-- Decorative Ambient Light -->
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold text-amber-300 bg-amber-400/10 border border-amber-400/20">
                <span>⚡ Coba Gratis Sekarang Selama 14 Hari Penuh</span>
            </div>

            <h2 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                Tingkatkan Efisiensi & Omzet <br class="hidden sm:inline">
                Coffee Shop Anda Mulai Hari Ini.
            </h2>

            <p class="text-base sm:text-lg text-stone-300 max-w-2xl mx-auto font-normal">
                Bergabunglah dengan ratusan barista dan pemilik cafe yang telah meninggalkan bon kertas dan antrean panjang. Setup selesai dalam 5 menit.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-9 py-4 rounded-xl font-bold text-base text-white primary-btn-gradient flex items-center justify-center gap-2">
                    <span>Daftar Akun OQARI Gratis</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20ingin%20jadwalkan%20sesi%20demo%20online" target="_blank" class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-base text-stone-200 bg-stone-800 hover:bg-stone-700 border border-stone-700 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.221.771 1.394.857.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.12.553 4.11 1.521 5.836l-1.616 5.908 6.069-1.591c1.666.911 3.578 1.428 5.626 1.428 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
                    <span>Jadwalkan Demo Langsung</span>
                </a>
            </div>

        </div>
    </section>

    <!-- 13. RICH FOOTER (Majoo-style corporate + support info) -->
    <footer class="bg-stone-950 text-stone-400 text-xs pt-16 pb-12 border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
                
                <!-- Col 1: Brand & Contact -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-700 p-1 flex items-center justify-center">
                            <img src="{{ asset('logo-oqari.webp') }}" alt="OQARI Logo" class="w-full h-full object-contain">
                        </div>
                        <span class="text-xl font-black text-white tracking-tight">OQARI</span>
                    </div>
                    <p class="text-stone-400 text-xs leading-relaxed max-w-sm">
                        Platform manajemen operasional, sistem kasir pintar, dan pesanan QR terpadu untuk industri Food & Beverage dan Coffee Shop di Indonesia.
                    </p>
                    <div class="space-y-2 pt-2 text-stone-300">
                        <div class="flex items-center gap-2">
                            <span class="text-amber-400">📍</span>
                            <span>Jakarta Tech Center & Kreatif Hub</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-amber-400">📞</span>
                            <span>WhatsApp: 0812-8888-OQARI</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-amber-400">✉️</span>
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
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors font-semibold text-amber-400">Login Kasir / Owner →</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition-colors font-semibold text-emerald-400">Daftar Akun Baru →</a></li>
                        <li><a href="#harga" class="hover:text-white transition-colors">Pilihan Paket Harga</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">Pusat Bantuan & FAQ</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-stone-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-stone-500">
                <p>© {{ date('Y') }} OQARI Indonesia. Hak Cipta Dilindungi Undang-Undang.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-stone-300 transition-colors">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-stone-300 transition-colors">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-stone-300 transition-colors">Keamanan Cloud</a>
                </div>
            </div>

        </div>
    </footer>

    <!-- 14. FLOATING WHATSAPP BUTTON (Direct Sales / Live Help) -->
    <div class="fixed bottom-6 right-6 z-50">
        <a href="https://wa.me/6281234567890?text=Halo%20Tim%20OQARI,%20saya%20ingin%20tanya%20fitur%20dan%20coba%20demo%20POS%20Coffee%20Shop" target="_blank" class="flex items-center gap-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs py-3 px-4 rounded-full shadow-2xl transition-all hover:scale-105 group border-2 border-white/20">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.2.662.589 1.221.771 1.394.857.173.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.12.553 4.11 1.521 5.836l-1.616 5.908 6.069-1.591c1.666.911 3.578 1.428 5.626 1.428 6.627 0 12-5.373 12-12 0-6.627-5.373-12-12-12z"/></svg>
            <span class="hidden sm:inline">Tanya Sales OQARI</span>
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
        </a>
    </div>

</body>
</html>
