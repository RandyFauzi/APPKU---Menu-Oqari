<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pilihan Paket & Harga — OQARI All-in-One Coffee SaaS</title>
    <meta name="description" content="Pilihan paket langganan dan akses permanen (lifetime) OQARI POS & QR Menu. Tanpa biaya bulanan tersembunyi, pangkas biaya software operasional cafe Anda.">

    <link rel="icon" type="image/webp" href="{{ asset('logo-oqari.webp') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: #252525;
            color: #1C1917;
            min-height: 100vh;
        }

        .pricing-canvas {
            background: 
                radial-gradient(circle at 0% 0%, rgba(165, 140, 135, 0.28) 0%, rgba(255, 255, 255, 0.98) 40%),
                radial-gradient(circle at 100% 100%, rgba(170, 145, 138, 0.28) 0%, rgba(255, 255, 255, 0.98) 40%),
                #FFFFFF;
        }
    </style>
</head>
<body class="antialiased flex flex-col justify-between p-4 sm:p-8 lg:p-12">

    <!-- Top Floating Back / Navigation Bar -->
    <div class="max-w-6xl mx-auto w-full flex items-center justify-between pb-8 text-stone-300">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-stone-300 hover:text-white transition-colors bg-stone-800/80 hover:bg-stone-800 px-4 py-2 rounded-full border border-stone-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Beranda</span>
        </a>
        <div class="flex items-center gap-3">
            @if(auth()->check())
                <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 px-4 py-2 rounded-full transition-colors">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-xs font-semibold text-stone-300 hover:text-white transition-colors">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="text-xs font-bold text-white bg-amber-600 hover:bg-amber-500 px-4 py-2 rounded-full transition-colors">
                    Daftar Akun
                </a>
            @endif
        </div>
    </div>

    <!-- MAIN EXACT PRICING CANVAS -->
    <main class="max-w-6xl mx-auto w-full relative my-auto">

        <!-- Outer Relative Wrapper for Overlapping 3D Elements -->
        <div class="relative">
            
            <!-- Top Right Overlapping Decoration: Pastry / Bakery Plate -->
            <img src="{{ asset('pricing_plate_trans.png') }}" alt="Pastry plate" class="absolute -top-10 -right-4 sm:-top-14 sm:-right-8 lg:-top-16 lg:-right-10 w-28 sm:w-36 lg:w-44 z-30 pointer-events-none drop-shadow-2xl">

            <!-- Bottom Left Overlapping Decoration: 3D Wooden QR Code Stand -->
            <img src="{{ asset('pricing_qr_stand_trans.png') }}" alt="QR Stand" class="absolute -bottom-8 -left-4 sm:-bottom-12 sm:-left-8 lg:-bottom-14 lg:-left-10 w-24 sm:w-32 lg:w-36 z-30 pointer-events-none drop-shadow-2xl">

            <!-- Central Big Rounded Canvas Card -->
            <div class="pricing-canvas rounded-[36px] sm:rounded-[48px] p-6 sm:p-12 lg:p-16 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.6)] relative z-10 border border-white/20">
                
                <!-- Center Header: Oqari Brand -->
                <div class="text-center mb-10 sm:mb-12">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2.5 group">
                        <img src="{{ asset('pricing_brand_logo_trans.png') }}" alt="Oqari" class="h-8 sm:h-9 object-contain">
                    </a>
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
                            <h3 class="text-xl sm:text-2xl font-bold text-stone-900 tracking-tight">
                                Monthly Starter
                            </h3>

                            <!-- Subtitle -->
                            <p class="text-xs text-stone-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                Pilihan low-risk untuk tes efektivitas sistem di operasional.
                            </p>

                            <!-- Price -->
                            <div class="flex items-baseline mb-6">
                                <span class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Rp80.000</span>
                                <span class="text-xs text-stone-400 font-normal ml-1.5">per month</span>
                            </div>

                            <!-- Features List -->
                            <ul class="space-y-3 text-xs text-stone-700 font-normal mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Akses Penuh Core System</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>System pay-as-you-go</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Bebas Cancel kapan saja</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Button -->
                        <a href="{{ route('register') }}" class="w-full py-2.5 rounded-xl border border-stone-300 hover:border-stone-400 text-stone-900 bg-white font-medium text-xs sm:text-sm text-center block transition-colors">
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
                            <h3 class="text-xl sm:text-2xl font-bold text-stone-900 tracking-tight">
                                Lifetime Basic
                            </h3>

                            <!-- Subtitle -->
                            <p class="text-xs text-stone-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                Potong overhead cost. Bayar sekali untuk akses selamanya.
                            </p>

                            <!-- Price -->
                            <div class="flex items-baseline mb-6">
                                <span class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Rp1.500.000</span>
                                <span class="text-xs text-stone-400 font-normal ml-1.5">lifetime</span>
                            </div>

                            <!-- Features List -->
                            <ul class="space-y-3 text-xs text-stone-700 font-normal mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Mencakup semua benefit Monthly.</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Prioritas support & pendampingan setup</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>100% pangkas biaya software (Rp0/bulan)</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Button -->
                        <a href="{{ route('register') }}" class="w-full py-2.5 rounded-xl border border-stone-300 hover:border-stone-400 text-stone-900 bg-white font-medium text-xs sm:text-sm text-center block transition-colors">
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
                            <h3 class="text-xl sm:text-2xl font-bold text-stone-900 tracking-tight">
                                Lifetime Custom
                            </h3>

                            <!-- Subtitle -->
                            <p class="text-xs text-stone-500 font-normal leading-relaxed mt-1.5 mb-5 min-h-[36px]">
                                Sistem yang adaptasi dengan flow bisnis anda, bukan sebaliknya.
                            </p>

                            <!-- Price -->
                            <div class="flex items-baseline mb-6">
                                <span class="text-2xl sm:text-3xl font-extrabold text-stone-900 tracking-tight">Rp2.500.000</span>
                                <span class="text-xs text-stone-400 font-normal ml-1.5">lifetime</span>
                            </div>

                            <!-- Features List -->
                            <ul class="space-y-3 text-xs text-stone-700 font-normal mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Mencakup semua benefit Lifetime Basic.</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Custom Feature; bebas request fitur khusus</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Prioritas support & pendampingan setup</span>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-stone-700 font-bold text-xs">✓</span>
                                    <span>Lifetime update</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Button -->
                        <a href="https://wa.me/6281234567890?text=Halo%20OQARI,%20saya%20ingin%20konsultasi%20fitur%20custom%20Paket%20Lifetime%20Custom" target="_blank" class="w-full py-2.5 rounded-xl bg-stone-950 hover:bg-stone-800 text-white font-medium text-xs sm:text-sm text-center block transition-colors shadow relative z-10">
                            Konsultasi & Custom Fitur
                        </a>
                    </div>

                </div>

                <!-- Footer Text in Canvas -->
                <div class="mt-12 text-center text-xs text-stone-600 font-normal max-w-2xl mx-auto space-y-1">
                    <p class="font-semibold text-stone-800">Semua paket sudah termasuk All Core System:</p>
                    <p class="text-[11px] text-stone-500">POS Kasir, Table & QR Order, Live Order Dashboard, Laporan Keuangan, Crew Management, dan Menu CMS.</p>
                </div>

            </div>

        </div>

    </main>

    <!-- Simple Bottom Footer -->
    <footer class="max-w-6xl mx-auto w-full text-center pt-8 text-[11px] text-stone-500">
        © {{ date('Y') }} OQARI Indonesia. All rights reserved.
    </footer>

</body>
</html>
