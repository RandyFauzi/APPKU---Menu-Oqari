<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir — {{ $shop->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.OQARI_REALTIME = {
            shopId: @json(auth()->check() ? auth()->user()->shop_id : null)
        };
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .product-card:active { transform: scale(0.98); }
        .product-card { transition: transform 0.12s ease, box-shadow 0.12s ease, border-color 0.12s ease; }
        .sparta-pos { font-family: 'DM Sans', sans-serif; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-pos bg-white text-[#1e211f] min-h-screen w-full antialiased" x-data="posApp()" @keydown.window="handleKey($event)" @refresh-live-orders.window="fetchIncoming()">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-white">

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SHIFT GATE — Blur overlay if no active session             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if(!$activeSession)
<div class="fixed inset-0 z-50 bg-[#1e211f]/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-[#e6e5e0] rounded-[20px] shadow-2xl p-7 w-full max-w-sm text-center">
        <div class="w-14 h-14 bg-[#fff6f0] border border-[#f5d5c6] rounded-[14px] flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cash-register text-xl text-[#df5826]"></i>
        </div>
        <h2 class="text-[20px] font-bold text-[#1e211f] mb-1">Buka Shift Kasir</h2>
        <p class="text-[#8b8e93] text-[12px] mb-5">Masukkan modal kas awal sebelum mulai menerima pesanan</p>
        <form action="{{ route('shift.open') }}" method="POST">
            @csrf
            <div class="relative mb-4">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8b8e93] font-semibold text-sm">Rp</span>
                <input type="number" name="opening_cash" placeholder="0" min="0" step="1000"
                    class="w-full pl-10 pr-4 py-3 rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] focus:border-[#df5826] focus:bg-white outline-none text-[16px] font-bold text-[#111827] text-right transition" required>
            </div>
            <button type="submit" class="w-full py-3 px-4 rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-semibold text-[13px] shadow-sm hover:opacity-95 transition">
                Buka Shift &amp; Mulai Kasir
            </button>
        </form>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- TOP HEADER (Figma 45:12178)                                 --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<header class="h-[52px] bg-white border-b border-[#e6e5e0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-3">
        <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
            class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-[#1e211f] hover:bg-gray-200 transition-colors md:hidden">
            <i class="fas fa-bars text-base"></i>
        </button>
        <span class="text-[13px] font-semibold text-[#1e211f]">Kasir</span>
        @if($activeSession)
        <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#eaf7ef] text-[#289e5c] text-[10.5px] font-semibold">
            <span class="w-1.5 h-1.5 rounded-full bg-[#289e5c]"></span> Shift Aktif
        </span>
        @endif
    </div>

    <div class="flex items-center gap-2 sm:gap-[8px]">
        {{-- Top Search Box --}}
        <div class="hidden md:flex items-center justify-between bg-[#f8f9fa] border border-[#e6e5e0] h-[34px] w-[220px] px-[10px] rounded-[9px]">
            <div class="flex items-center gap-[7px] flex-1 min-w-0">
                <img src="{{ asset('images/sparta/search.svg') }}" alt="" class="w-[14px] h-[14px] shrink-0">
                <input type="text" x-model="search" placeholder="Cari menu atau pesanan…"
                    class="bg-transparent border-none outline-none text-[11.5px] text-[#1e211f] placeholder-[#8b8e93] w-full p-0 focus:ring-0">
            </div>
            <span class="bg-white border border-[#e6e5e0] rounded-[4px] px-[5px] py-[1px] text-[10px] font-medium text-[#8b8e93] shrink-0">⌘K</span>
        </div>

        {{-- Topbar Datetime (Standardized Sparta Component) --}}
        <x-topbar-datetime />

        {{-- Minimized Cart Shortcut (Desktop & Tablet) --}}
        <button type="button" x-show="isCartMinimized" x-cloak @click="isCartMinimized = false"
                class="hidden lg:inline-flex items-center gap-1.5 h-[34px] px-2.5 rounded-[9px] bg-[#fff6f0] border border-[#df5826] text-[#df5826] text-[11px] font-bold shadow-xs hover:bg-[#ffece0] transition cursor-pointer">
            <i class="fas fa-shopping-bag text-xs"></i>
            <span>Pesanan</span>
            <span class="bg-[#df5826] text-white text-[10px] px-1.5 py-0.2 rounded-full font-bold" x-text="cart.reduce((s,i)=>s+i.qty,0)"></span>
        </button>

        @if($activeSession)
        <a href="{{ route('admin.register.index') }}" class="h-[34px] px-[10px] rounded-[9px] bg-white border border-[#e6e5e0] hover:border-[#df5826] text-[11px] font-semibold text-[#1e211f] inline-flex items-center gap-1.5 transition">
            <span>Kas &amp; Tutup Shift</span>
        </a>
        @endif
    </div>
</header>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MAIN WORKSPACE (Figma 45:12192)                             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="flex-1 p-3 sm:p-4 lg:p-[20px] xl:p-[24px] flex flex-col lg:flex-row items-start gap-4 lg:gap-[18px] pb-24 lg:pb-[24px]">

    {{-- LEFT COLUMN: Antrean + Menu (Figma 45:12193) --}}
    <div class="flex-1 w-full min-w-0 flex flex-col gap-[16px]">

        {{-- 1. ANTREAN STATUS SECTION (Figma 45:12194 / Frame 1597884462) --}}
        <section class="bg-white border border-[#e6e5e0] rounded-[16px] p-3.5 sm:p-4 flex flex-col gap-3 shadow-2xs">
            {{-- Header Row: Antrean (N) + Status pesanan on left, Semua Pesanan > on right --}}
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-[#111827] flex items-center gap-1.5 leading-tight">
                        <span>Antrean</span>
                        <span class="font-normal text-[#71717a]">(<span x-text="activeOrders.length">0</span>)</span>
                    </h2>
                    <p class="text-[11.5px] text-[#71717a] mt-0.5 leading-tight">Status pesanan</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#df5826] hover:text-[#b84218] transition">
                    <span>Semua Pesanan</span>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div>
                {{-- Empty State when no active orders --}}
                <div x-show="activeOrders.length === 0" x-cloak class="bg-[#f8f9fa] border border-dashed border-[#e6e5e0] rounded-[12px] py-3 px-4 flex items-center justify-center text-center">
                    <p class="text-[12px] text-[#71717a]">Belum ada antrean pesanan aktif.</p>
                </div>

                {{-- Active Queue Cards (Horizontal Scroll, matching Figma Frame 1597884462) --}}
                <div x-show="activeOrders.length > 0" class="flex items-center gap-3 overflow-x-auto hide-scroll pb-1">
                    <template x-for="(order, oIdx) in activeOrders" :key="order.id">
                        <div @click="goToActiveOrder(order)"
                             class="min-w-[260px] sm:min-w-[280px] max-w-[320px] bg-white border border-[#e5e7eb] hover:border-[#df5826]/60 rounded-[14px] p-2.5 sm:p-3 flex items-center justify-between gap-3 shadow-[0px_2px_8px_rgba(0,0,0,0.03)] hover:shadow-md transition-all cursor-pointer group shrink-0 select-none">
                            
                            {{-- Left: Table Icon + Number Box (Figma Frame 1597884462) --}}
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-[42px] h-[42px] rounded-[10px] bg-[#f4f4f5] border border-[#e4e4e7] flex items-center justify-center gap-1 shrink-0 text-[#18181b]">
                                    <template x-if="order.table_number || (order.table && order.table !== 'Takeaway' && order.table !== 'Delivery' && order.table !== 'Tanpa Meja')">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-chair text-[11px] text-[#71717a]"></i>
                                            <span class="text-[12.5px] font-bold text-[#18181b]" x-text="formatTableBadge(order, oIdx)"></span>
                                        </div>
                                    </template>
                                    <template x-if="!order.table_number && (!order.table || order.table === 'Takeaway')">
                                        <i class="fas fa-shopping-bag text-[13px] text-[#71717a]"></i>
                                    </template>
                                    <template x-if="!order.table_number && order.table === 'Delivery'">
                                        <i class="fas fa-motorcycle text-[13px] text-[#71717a]"></i>
                                    </template>
                                    <template x-if="!order.table_number && order.table === 'Tanpa Meja'">
                                        <span class="text-[11px] font-bold text-[#71717a]" x-text="'#' + order.id"></span>
                                    </template>
                                </div>

                                {{-- Center: Customer Name + Type & ID --}}
                                <div class="min-w-0">
                                    <h4 class="text-[13px] sm:text-[13.5px] font-bold text-[#111827] truncate group-hover:text-[#df5826] transition-colors leading-tight" 
                                        x-text="order.customer_name || 'Pelanggan'"></h4>
                                    <div class="flex items-center gap-1.5 text-[11px] text-[#71717a] mt-0.5 leading-tight">
                                        <template x-if="!order.is_open_bill">
                                            <span class="inline-flex items-center gap-1 font-medium text-[#71717a]">
                                                <i class="fas fa-money-bill-wave text-[10px] text-[#10b981]"></i>
                                                <span>Paid</span>
                                            </span>
                                        </template>
                                        <template x-if="order.is_open_bill">
                                            <span class="inline-flex items-center gap-1 font-medium text-amber-700">
                                                <i class="fas fa-receipt text-[10px] text-amber-600"></i>
                                                <span>Open Bill</span>
                                            </span>
                                        </template>
                                        <span>•</span>
                                        <span x-text="'#' + order.id"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Status Badge (Consistent with Pesanan Aktif & Figma) --}}
                            <div class="shrink-0">
                                <span :class="getOrderStatusInfo(order).classes"
                                      class="px-2.5 py-1 rounded-[7px] text-[10.5px] font-bold whitespace-nowrap inline-flex items-center gap-1"
                                      x-text="getOrderStatusInfo(order).label"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- 2. MENU CATALOG SECTION (Figma 45:12312) --}}
        <section class="bg-white border border-[#e6e5e0] rounded-[16px] p-[14px] flex flex-col">
            {{-- Search & Category Filter Row (Figma 45:12313) --}}
            <div class="pb-[12px] border-b border-[#e6e5e0] flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-[12px]">
                <div class="flex-1 bg-[#f8f9fa] border border-[#d8dce2] rounded-[9px] px-[13px] py-[9px] flex items-center gap-[8px] focus-within:border-[#df5826] focus-within:bg-white transition">
                    <img src="{{ asset('images/sparta/search.svg') }}" alt="" class="w-[16px] h-[16px] shrink-0">
                    <input x-ref="searchInput" x-model="search" type="text" placeholder="Search menu"
                        class="w-full bg-transparent border-none outline-none text-[12px] font-medium text-[#1e211f] placeholder-[#8b8e93] p-0 focus:ring-0">
                </div>

                <div class="flex items-center gap-[6px] overflow-x-auto hide-scroll shrink-0">
                    <button type="button" @click="activeCategory = null"
                        :class="activeCategory === null ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                        class="border rounded-[9px] px-[12px] py-[6px] text-[11px] leading-[16.5px] transition whitespace-nowrap">
                        All
                    </button>
                    <button type="button" @click="activeCategory = 'best_seller'"
                        :class="activeCategory === 'best_seller' ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                        class="border rounded-[9px] px-[12px] py-[6px] text-[11px] leading-[16.5px] transition whitespace-nowrap">
                        Best Seller
                    </button>
                    @foreach($categories as $cat)
                    <button type="button" @click="activeCategory = {{ $cat['id'] }}"
                        :class="activeCategory === {{ $cat['id'] }} ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                        class="border rounded-[9px] px-[12px] py-[6px] text-[11px] leading-[16.5px] transition whitespace-nowrap">
                        {{ $cat['name'] }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Cards Grid (Figma 45:12328) --}}
            @if($products->isEmpty())
                <div class="py-12 px-4 text-center">
                    <div class="w-12 h-12 rounded-full bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <p class="text-[13px] font-bold text-[#1e211f]">Belum ada menu tersedia</p>
                    <p class="text-[11.5px] text-[#8b8e93] mt-1">Tambahkan produk melalui halaman Kelola Menu terlebih dahulu.</p>
                </div>
            @else
                <div class="pt-[16px] grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-[16px]">
                    @foreach($products as $product)
                    @php
                        $productPayload = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => (float) $product->price,
                            'discount_price' => $product->discount_price !== null ? (float) $product->discount_price : null,
                            'is_discount_active' => (bool) $product->is_discount_active,
                            'effective_price' => (float) $product->effective_price,
                            'discount_percentage' => $product->discount_percentage,
                            'has_active_discount' => (bool) $product->has_active_discount,
                            'image_url' => $product->image_url,
                            'category_id' => $product->category_id,
                            'category_name' => $product->category_name ?? ($product->category?->name ?? 'Menu'),
                            'sold_count' => (int) ($product->sold_count ?? 0),
                            'variants' => $product->variants,
                            'modifier_groups' => $product->modifierGroups,
                        ];
                    @endphp
                    <div
                        x-show="matchesFilter({{ $product->category_id ?? 'null' }}, '{{ addslashes($product->name) }}', {{ (int) ($product->sold_count ?? 0) }}, {{ $product->is_best_seller ? 'true' : 'false' }})"
                        class="product-card group bg-white border border-[#e6e5e0] hover:border-[#df5826]/50 rounded-[18px] overflow-hidden flex flex-col justify-between text-left shadow-[0px_4px_16px_0px_rgba(17,24,39,0.03)] hover:shadow-md transition-all duration-200">
                        {{-- Product Image (Flush to top, matching Figma) --}}
                        <div class="bg-[#f8f9fa] border-b border-[#e6e5e0] h-[135px] sm:h-[150px] xl:h-[160px] w-full overflow-hidden relative cursor-pointer"
                             @click="handleProductClick({{ Illuminate\Support\Js::from($productPayload) }}, 'DINE_IN')">
                            @if($product->has_active_discount)
                                <span class="absolute top-2.5 right-2.5 z-10 bg-red-600 text-white text-[9.5px] font-black px-2 py-0.5 rounded-full shadow-md flex items-center gap-1">
                                    <i class="fas fa-tag text-[8px]"></i> -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                            @if($product->is_best_seller)
                                <span class="absolute top-2.5 left-2.5 z-10 bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                    <i class="fas fa-crown text-[8px]"></i> Best
                                </span>
                            @endif
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-[#f8f9fa] text-[#8b8e93]">
                                    <i class="fas fa-mug-hot text-2xl text-[#df5826]/40 mb-1"></i>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-[#71717a]">{{ $product->category_name ?? 'Menu' }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Product Details & Buttons Container --}}
                        <div class="p-3 sm:p-3.5 pt-3 flex flex-col flex-1 justify-between gap-2.5">
                            <div>
                                {{-- Product Name --}}
                                <h3 class="text-[14px] sm:text-[15px] font-bold text-[#111827] leading-snug truncate" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h3>

                                {{-- Category • Sold Count & Price Row (Figma Screen 2) --}}
                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div class="text-[11.5px] text-[#71717a] font-normal truncate">
                                        <span>{{ $product->category_name ?? ($product->category?->name ?? 'Menu') }}</span>
                                        <span class="mx-0.5">•</span>
                                        <span>{{ (int) ($product->sold_count ?? 0) }} sold</span>
                                    </div>
                                    @if($product->has_active_discount)
                                        <div class="flex items-baseline gap-1.5 shrink-0">
                                            <span class="text-[10px] text-[#8b8e93] line-through">
                                                Rp{{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                            <span class="text-[14px] sm:text-[15px] font-extrabold text-[#df5826] leading-none">
                                                Rp{{ number_format($product->effective_price, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-[14px] sm:text-[15px] font-extrabold text-[#111827] leading-none shrink-0">
                                            Rp{{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Stacked Action Buttons: [ 🍴 Dine In ] & [ 🛍️ Takeaway ] (Figma 45:12312) --}}
                            <div class="flex flex-col gap-2 w-full pt-1">
                                <button type="button"
                                    @click="handleProductClick({{ Illuminate\Support\Js::from($productPayload) }}, 'DINE_IN')"
                                    class="w-full py-2.5 px-3 rounded-[11px] bg-gradient-to-r from-[#f97316] to-[#ea580c] hover:opacity-95 text-white text-[12.5px] font-bold flex items-center justify-center gap-2 shadow-xs hover:shadow transition active:scale-[0.98] cursor-pointer">
                                    <i class="fas fa-utensils text-[11px]"></i>
                                    <span>Dine In</span>
                                </button>
                                <button type="button"
                                    @click="handleProductClick({{ Illuminate\Support\Js::from($productPayload) }}, 'TAKEAWAY')"
                                    class="w-full py-2.5 px-3 rounded-[11px] bg-white hover:bg-[#fff9f6] border border-[#ea580c] text-[#ea580c] text-[12.5px] font-bold flex items-center justify-center gap-2 shadow-2xs transition active:scale-[0.98] cursor-pointer">
                                    <i class="fas fa-shopping-bag text-[11px]"></i>
                                    <span>Takeaway</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- MOBILE CART BAR --}}
    <div class="lg:hidden fixed bottom-0 left-0 right-0 p-3 pb-[max(12px,env(safe-area-inset-bottom))] bg-white/95 backdrop-blur-sm border-t border-[#e6e5e0] shadow-lg z-30 transition-transform duration-300 ease-in-out"
         :class="showMobileCart ? 'translate-y-full' : 'translate-y-0'">
        <button type="button" @click="showMobileCart = true"
            class="w-full py-3 px-4 rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] text-white font-semibold text-[13px] flex items-center justify-between shadow-sm hover:opacity-95 active:scale-[0.99] transition">
            <div class="flex items-center gap-2">
                <span class="bg-white/20 px-2 py-0.5 rounded text-[11px] font-bold"><span x-text="cart.reduce((s,i)=>s+i.qty,0)"></span> Item</span>
                <span>Lihat Pesanan Baru</span>
            </div>
            <span class="font-bold">Rp<span x-text="formatRp(subtotal)"></span></span>
        </button>
    </div>

    {{-- MOBILE OVERLAY --}}
    <div x-show="showMobileCart" x-cloak x-transition.opacity class="fixed inset-0 bg-black/50 z-30 lg:hidden" @click="showMobileCart = false"></div>

    {{-- FLOATING MINIMIZED CART DOCK (Desktop & Tablet) --}}
    <div x-show="isCartMinimized" x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="hidden lg:flex fixed bottom-6 right-6 z-40 bg-white/95 backdrop-blur-md border border-[#d8dce2] rounded-[18px] shadow-[0_16px_40px_-8px_rgba(0,0,0,0.18)] p-3.5 items-center gap-4">
        
        <div class="flex items-center gap-3 cursor-pointer group" @click="isCartMinimized = false">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#df5826] to-[#f06428] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform">
                <i class="fas fa-shopping-bag text-sm"></i>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[12px] font-bold text-[#111827]">Pesanan Kasir</span>
                    <span class="bg-[#fff6f0] text-[#df5826] text-[10px] font-bold px-1.5 py-0.5 rounded-full border border-[#f5d5c6]" x-text="cart.reduce((s,i)=>s+i.qty,0) + ' Item'"></span>
                </div>
                <div class="text-[14px] font-black text-[#111827]">Rp<span x-text="formatRp(subtotal)"></span></div>
            </div>
        </div>

        <div class="h-8 w-[1px] bg-gray-200 mx-1"></div>

        <div class="flex items-center gap-2">
            <button type="button" @click="isCartMinimized = false" 
                    class="px-3 py-2 rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] hover:bg-gray-100 text-[11.5px] font-bold text-[#111827] transition flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-expand-alt text-xs text-gray-500"></i>
                <span>Buka Panel</span>
            </button>
            <button type="button" @click="openPayment()" :disabled="cart.length === 0"
                    class="px-4 py-2 rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] text-white text-[11.5px] font-bold shadow-xs hover:opacity-95 transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer flex items-center gap-1.5">
                <span>Bayar</span>
                <span class="opacity-80 text-[10px]">(F4)</span>
            </button>
        </div>
    </div>

    {{-- RIGHT SIDEBAR: CURRENT ORDER / Pesanan Baru (Figma 45:12496) --}}
    <aside :class="showMobileCart ? 'translate-x-0' : (isCartMinimized ? 'translate-x-full lg:hidden' : 'translate-x-full lg:translate-x-0')"
        class="fixed inset-y-0 right-0 z-40 w-full sm:w-[400px] lg:sticky lg:top-[68px] lg:right-auto lg:bottom-auto lg:z-10 lg:w-[350px] xl:w-[374px] shrink-0 transition-transform duration-300 ease-in-out bg-white border border-[#e6e5e0] lg:rounded-[16px] flex flex-col overflow-hidden shadow-2xl lg:shadow-none h-full h-[100dvh] max-h-[100dvh] lg:h-[calc(100dvh-84px)] lg:max-h-[calc(100dvh-84px)]">

        {{-- Mobile Close Bar --}}
        <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-[#e6e5e0] shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-[13px] font-bold text-[#111827]">Detail Pesanan</span>
                <span class="bg-[#fff6f0] text-[#df5826] text-[11px] font-bold px-2 py-0.5 rounded-full" x-text="cart.reduce((s,i)=>s+i.qty,0) + ' Item'"></span>
            </div>
            <button type="button" @click="showMobileCart = false" class="text-[12px] font-semibold text-[#df5826] hover:underline px-2 py-1">Tutup</button>
        </div>

        {{-- Order Header + Mode Tabs + Customer/Table Inputs (Ultra-Compact for Tablet & Medium Displays) --}}
        <div class="p-2.5 sm:p-3 border-b border-[#e6e5e0] flex flex-col gap-2 shrink-0 bg-white">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <button type="button" @click="isCartMinimized = true"
                        class="hidden lg:flex w-7 h-7 rounded-[7px] border border-[#d8dce2] bg-[#f8f9fa] hover:bg-gray-100 hover:border-gray-400 items-center justify-center text-[#53565a] hover:text-[#111827] transition shrink-0 cursor-pointer"
                        title="Minimalkan panel pesanan">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                    <h2 class="text-[14px] font-bold text-[#111827] truncate">
                        <span x-text="recalledOrderId ? 'Pesanan #' + recalledOrderId : 'Current Order'"></span>
                    </h2>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" @click="clearCart()" x-show="cart.length > 0" x-cloak
                        class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-[#df5826] hover:text-[#b84218] transition cursor-pointer">
                        <span>Reset</span>
                        <i class="fas fa-rotate-right text-[11px]"></i>
                    </button>
                </div>
            </div>

            {{-- Customer Name & Table Number (Side by Side matching layout) --}}
            <div class="grid grid-cols-2 gap-2 pt-0.5">
                <div>
                    <label class="block text-[10.5px] font-semibold text-[#53565a] mb-1">Customer name</label>
                    <div class="relative">
                        <input id="pos-customer-name" 
                               type="text" 
                               x-model="customerName" 
                               placeholder="Walk-in Customer"
                               class="w-full bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[8px] px-2.5 py-1.5 text-[11.5px] font-semibold text-[#111827] placeholder-[#8b8e93] outline-none transition">
                        <button type="button" x-show="customerName" x-cloak @click="customerName = ''"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 hover:text-gray-600 p-0.5 cursor-pointer">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10.5px] font-semibold text-[#53565a]">Table number</label>
                        <button type="button" @click="tableSetupModal.show = true" class="text-[10px] font-bold text-[#df5826] hover:underline cursor-pointer" title="Tambah Meja Baru">+ Meja</button>
                    </div>
                    <div class="relative">
                        <select id="pos-table-select" 
                                x-model="selectedTable"
                                class="w-full appearance-none bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[8px] pl-2.5 pr-6 py-1.5 text-[11.5px] font-semibold text-[#111827] outline-none transition truncate cursor-pointer">
                            <option value="">Pilih Meja</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->name }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[8.5px] text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- Segmented Control: [ Direct Order ] & [ Delivery ] (Circled in Cyan) --}}
            <div class="bg-[#f3f4f6] p-[3px] rounded-[10px] grid grid-cols-2 gap-[3px]">
                <button type="button" @click="fulfillmentType = 'DINE_IN'; paymentModal.fulfillment = 'DINE_IN'"
                    :class="fulfillmentType !== 'DELIVERY' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                    class="py-1.5 px-3 rounded-[8px] text-[11.5px] text-center transition cursor-pointer">
                    Direct Order
                </button>
                <button type="button" @click="fulfillmentType = 'DELIVERY'; paymentModal.fulfillment = 'DELIVERY'"
                    :class="fulfillmentType === 'DELIVERY' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                    class="py-1.5 px-3 rounded-[8px] text-[11.5px] text-center transition cursor-pointer">
                    Delivery
                </button>
            </div>
        </div>

        {{-- Cart Items List (Single unified list matching Figma) --}}
        <div class="p-2.5 sm:p-3 flex-1 min-h-0 overflow-y-auto flex flex-col gap-2.5 overscroll-contain pb-3">
            <template x-if="cart.length === 0">
                <div class="py-10 px-4 text-center flex flex-col items-center justify-center border border-dashed border-[#d8dce2] rounded-[10px] bg-[#f8f9fa]/60">
                    <div class="w-9 h-9 rounded-full bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center mb-2">
                        <img src="{{ asset('images/sparta/pos-doc-add.svg') }}" alt="" class="w-3.5 h-3.5">
                    </div>
                    <p class="text-[12px] font-semibold text-[#111827]">Belum ada item dipilih</p>
                    <p class="text-[10.5px] text-[#8b8e93] mt-0.5">Pilih menu di samping untuk menambahkan ke pesanan.</p>
                </div>
            </template>

            <template x-for="(item, idx) in cart" :key="'cart-item-' + idx">
                <div class="bg-white border border-[#e6e5e0] rounded-[12px] p-2.5 flex flex-col gap-2 shadow-[0px_1px_3px_rgba(0,0,0,0.03)] hover:border-[#df5826]/40 transition">
                    {{-- Top Item Row: Thumbnail + Name + Neutral Serving Badge + Red Trash --}}
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-[36px] h-[36px] rounded-[8px] bg-[#f3f4f6] border border-[#e5e7eb] overflow-hidden shrink-0 flex items-center justify-center">
                            <template x-if="item.image">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.image">
                                <span class="text-[11px] font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                            </template>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[13px] font-bold text-[#111827] leading-tight truncate" x-text="item.name"></p>
                            
                            {{-- Serving Type Badge (Neutral gray background with icon per Figma) --}}
                            <div class="mt-1 flex items-center gap-1.5">
                                <button type="button" @click="toggleServingType(idx)"
                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[6px] bg-[#f3f4f6] hover:bg-[#e5e7eb] text-[#374151] border border-[#e5e7eb] text-[10.5px] font-medium transition cursor-pointer"
                                    :title="'Klik untuk ubah ke ' + ((item.serving_type || 'DINE_IN') === 'TAKEAWAY' ? 'Dine In' : 'Takeaway')">
                                    <i class="fas fa-utensils text-[9px] text-[#53565a]" x-show="(item.serving_type || 'DINE_IN') !== 'TAKEAWAY'"></i>
                                    <i class="fas fa-shopping-bag text-[9px] text-[#53565a]" x-show="(item.serving_type || 'DINE_IN') === 'TAKEAWAY'"></i>
                                    <span x-text="(item.serving_type || 'DINE_IN') === 'TAKEAWAY' ? 'Takeaway' : 'Dine In'"></span>
                                    <i class="fas fa-right-left text-[8px] text-[#8b8e93] opacity-70 ml-0.5"></i>
                                </button>
                                <span x-show="item.variant" x-cloak class="text-[10.5px] text-[#53565a] truncate" x-text="'· ' + item.variant"></span>
                            </div>

                            <p x-show="item.modifiers && item.modifiers.length > 0" x-cloak
                               class="text-[10px] text-[#53565a] truncate mt-0.5"
                               x-text="item.modifiers.map(m => m.name).join(' · ')"></p>
                        </div>

                        <button type="button" @click="removeItem(idx)" class="w-6 h-6 flex items-center justify-center shrink-0 text-[#ef4444] hover:text-[#dc2626] hover:bg-red-50 rounded-full transition cursor-pointer" title="Hapus item">
                            <i class="far fa-trash-can text-[13px]"></i>
                        </button>
                    </div>

                    {{-- Collapsed Customer Note Trigger --}}
                    <button type="button"
                        x-show="!item.showNotes && !item.notes"
                        @click="item.showNotes = true; $nextTick(() => document.getElementById('item-notes-' + idx)?.focus())"
                        class="flex items-center gap-1.5 text-left w-fit hover:opacity-80 transition cursor-pointer py-0.5">
                        <i class="fas fa-pen-to-square text-[10.5px] text-[#df5826]"></i>
                        <span class="text-[11px] font-semibold text-[#df5826]">Buat Catatan Customer</span>
                    </button>

                    {{-- Expanded Customer Note Box (Matching Figma Es Kopi Susu card) --}}
                    <div x-show="item.showNotes || item.notes" class="bg-[#f8f9fa] rounded-[8px] p-2 flex flex-col gap-1.5 border border-[#e6e5e0]">
                        <div class="relative border border-[#d8dce2] rounded-[6px] p-2 bg-white focus-within:border-[#df5826] transition">
                            <label :for="'item-notes-' + idx" class="text-[10px] font-medium text-[#7f837e] block mb-0.5">
                                Catatan customer (Opsional)
                            </label>
                            <textarea :id="'item-notes-' + idx" x-model="item.notes" maxlength="255" rows="2" :disabled="paymentPending"
                                placeholder="Contoh: tanpa gula, es sedikit"
                                class="w-full bg-transparent border-none outline-none text-[11px] text-[#111827] placeholder-[#8b8e93] resize-none p-0 focus:ring-0"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" @click="item.notes = ''; item.showNotes = false" :disabled="paymentPending"
                                class="border border-[#d8dce2] rounded-[7px] py-1.5 px-2 flex items-center justify-center gap-1.5 bg-white hover:bg-gray-50 text-[#111827] text-[11px] font-semibold transition disabled:opacity-50 cursor-pointer">
                                <i class="far fa-trash-can text-[11px] text-[#53565a]"></i>
                                <span class="truncate">Hapus Catatan</span>
                            </button>
                            <button type="button" @click="splitItem(idx)" :disabled="paymentPending || item.qty <= 1 || cart.length >= 50"
                                class="border border-[#d8dce2] rounded-[7px] py-1.5 px-2 flex items-center justify-center gap-1.5 bg-white hover:bg-gray-50 text-[#111827] text-[11px] font-semibold transition disabled:opacity-50 cursor-pointer">
                                <i class="fas fa-up-right-from-square text-[10.5px] text-[#53565a]"></i>
                                <span class="truncate">Pisahkan 1 Item</span>
                            </button>
                        </div>
                    </div>

                    {{-- Price & Stepper Row --}}
                    <div class="flex items-center justify-between pt-0.5">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[13.5px] font-bold text-[#111827] leading-tight">
                                    Rp<span x-text="formatRp(item.subtotal)"></span>
                                </span>
                                <template x-if="item.has_discount">
                                    <span class="bg-[#fff0e6] text-[#df5826] text-[9px] font-black px-1.5 py-0.2 rounded border border-[#ffd2b8]">PROMO</span>
                                </template>
                            </div>
                            <span x-show="item.qty > 1" class="block text-[10px] text-[#8b8e93]">
                                @ Rp<span x-text="formatRp(item.price)"></span>
                            </span>
                        </div>

                        <div class="flex items-center gap-1">
                            <button type="button" @click="changeQty(idx, -1)"
                                class="w-6 h-6 rounded-full border border-[#d8dce2] bg-white text-[#111827] hover:bg-gray-100 active:scale-95 flex items-center justify-center transition cursor-pointer">
                                <i class="fas fa-minus text-[9px]"></i>
                            </button>
                            <span class="w-6 text-center text-[12.5px] font-bold text-[#111827] leading-tight select-none" x-text="item.qty"></span>
                            <button type="button" @click="changeQty(idx, 1)"
                                class="w-6 h-6 rounded-full bg-[#111827] text-white hover:bg-[#df5826] active:scale-95 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                <i class="fas fa-plus text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Order Summary & CTA Footer (Figma Screen 1 & Screen 3) --}}
        <div class="border-t border-[#e6e5e0] bg-[#fcfcfd] flex flex-col shrink-0 sticky bottom-0 z-10 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] pb-[max(14px,env(safe-area-inset-bottom))]">

            {{-- Applied Discount Banner (Figma Screen 3: full width, rounded top corners only, flush with summary below) --}}
            <div x-show="discountAmount > 0" x-cloak
                 class="w-full px-4 py-2.5 bg-[#e8f8f0] border-b border-[#a7f3d0] rounded-t-[14px] rounded-b-none flex items-center justify-between transition-all">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-[7px] bg-[#d1fae5] text-[#059669] flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="fas fa-percent text-[10px]" x-show="discount.type === 'percentage'"></i>
                        <i class="fas fa-ticket-alt text-[10px]" x-show="discount.type !== 'percentage'"></i>
                    </div>
                    <div class="truncate">
                        <span class="font-bold text-[#065f46] text-[12.5px] truncate block leading-tight" x-text="discount.code ? ('Voucher ' + discount.code) : (discount.reason || '5% Discount')"></span>
                        <span class="text-[10.5px] text-[#047857] font-medium leading-tight">Potongan Harga</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="font-black text-[#047857] text-[13.5px]">-Rp<span x-text="formatRp(discountAmount)"></span></span>
                    <button type="button" @click="removeDiscount()" class="w-5 h-5 rounded-full bg-white/80 hover:bg-white text-gray-400 hover:text-red-500 flex items-center justify-center text-[10px] transition cursor-pointer shadow-2xs ml-1" title="Hapus Diskon">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="px-4 pt-3 pb-2 flex flex-col gap-1.5">
                {{-- Header row: Total Pembayaran on left, Tambah Voucher/Diskon > on right --}}
                <div class="flex items-center justify-between text-[11.5px]">
                    <button type="button" @click="showPriceBreakdown = !showPriceBreakdown" 
                            class="font-semibold text-[#53565a] hover:text-[#111827] flex items-center gap-1 select-none transition-colors cursor-pointer group">
                        <span>Total Pembayaran</span>
                        <i class="fas fa-chevron-right text-[8.5px] text-gray-400 group-hover:text-[#df5826] transition-transform duration-200" :class="showPriceBreakdown ? 'rotate-90' : ''"></i>
                    </button>
                    
                    <button type="button" @click="openDiscountModal()" 
                            class="inline-flex items-center gap-1 text-[#df5826] hover:text-[#c44719] font-bold text-[11.5px] transition cursor-pointer">
                        <i class="fas fa-ticket-alt text-[10px]"></i>
                        <span>Tambah Voucher/Diskon</span>
                        <i class="fas fa-chevron-right text-[8.5px]"></i>
                    </button>
                </div>

                {{-- Price Display: Large Bold Final Total + Strikethrough Subtotal if Discounted (Redundant Rincian Harga removed!) --}}
                <div class="flex items-baseline justify-between pt-0.5">
                    <div class="flex items-baseline gap-2">
                        <span class="text-[20px] font-black text-[#111827] tracking-tight">
                            Rp<span x-text="formatRp(grandTotal)"></span>
                        </span>
                        <template x-if="discountAmount > 0">
                            <span class="text-[13px] font-semibold text-[#8b8e93] line-through">
                                Rp<span x-text="formatRp(subtotal)"></span>
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Expandable Price Breakdown (Subtotal, Tax, Additional fee) --}}
            <div x-show="showPriceBreakdown" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 class="px-4 py-2.5 border-t border-dashed border-[#e6e5e0] bg-[#f8f9fa] flex flex-col gap-1.5 text-[11.5px]">
                <div class="flex items-center justify-between">
                    <span class="text-[#53565a]">Subtotal Menu (<span x-text="cart.reduce((s,i)=>s+i.qty,0)"></span> item)</span>
                    <span class="font-semibold text-[#111827]">Rp<span x-text="formatRp(subtotal)"></span></span>
                </div>
                <template x-if="discountAmount > 0">
                    <div class="flex items-center justify-between py-1 border-t border-dashed border-[#e6e5e0]">
                        <span class="text-emerald-700 font-semibold flex items-center gap-1">
                            <i class="fas fa-tags text-[10px]"></i>
                            <span x-text="discount.code ? ('Voucher: ' + discount.code) : (discount.reason || 'Diskon')"></span>
                        </span>
                        <span class="font-bold text-emerald-600">-Rp<span x-text="formatRp(discountAmount)"></span></span>
                    </div>
                </template>
                <div class="flex items-center justify-between">
                    <span class="text-[#53565a]">Pajak Resto (0%)</span>
                    <span class="font-medium text-[#111827]">Rp0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#53565a]">Biaya Layanan</span>
                    <span class="font-medium text-[#53565a]">—</span>
                </div>
            </div>

            {{-- CTA Button (Open Bill & Bayar matching Figma) --}}
            <div class="px-4 pt-1.5 pb-2">
                @can('manage-payments')
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" @click="confirmOpenBill()" :disabled="cart.length === 0"
                        class="py-[11px] px-3.5 rounded-[10px] bg-white hover:bg-[#fff6f0] border border-[#df5826] text-[#df5826] text-[12.5px] font-bold transition flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shadow-2xs"
                        :title="recalledOrderId ? 'Perbarui Open Bill (F2)' : 'Buka Tagihan Meja / Bayar Nanti (F2)'">
                        <i class="fas fa-receipt text-[11px]"></i>
                        <span x-text="recalledOrderId ? 'Perbarui Open Bill' : 'Open Bill'"></span>
                    </button>
                    <button type="button" @click="openPayment()" :disabled="cart.length === 0"
                        class="py-[11px] px-[16px] rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_2px_6px_0px_rgba(223,88,38,0.25)] flex items-center justify-center gap-[8px] text-white text-[12.5px] font-bold hover:opacity-95 active:scale-[0.99] transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fas fa-credit-card text-[12px]"></i>
                        <span>Bayar</span>
                    </button>
                </div>
                @else
                <p class="text-[11.5px] text-[#8b8e93] text-center py-2">Pembayaran memerlukan izin dari Owner.</p>
                @endcan
            </div>
        </div>
    </aside>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODIFIER MODAL                                             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="modifierModal.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-[#1e211f]/60 backdrop-blur-[2px] flex items-center justify-center p-4"
    @click.self="modifierModal.show = false">
    <div class="bg-white border border-[#e6e5e0] rounded-[20px] shadow-2xl w-full max-w-sm max-h-[90vh] flex flex-col overflow-hidden">
        <div class="p-5 border-b border-[#e6e5e0] flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-bold text-[16px] text-[#111827]" x-text="modifierModal.product?.name"></h3>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <template x-if="modifierModal.product?.has_active_discount">
                        <span class="text-[11px] text-[#8b8e93] line-through" x-text="'Rp' + formatRp(modifierModal.product?.price)"></span>
                    </template>
                    <p class="text-[#df5826] font-bold text-[13px]">Rp<span x-text="formatRp(modalTotal)"></span></p>
                    <template x-if="modifierModal.product?.has_active_discount && modifierModal.product?.discount_percentage">
                        <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full" x-text="'-' + modifierModal.product.discount_percentage + '%'"></span>
                    </template>
                </div>
            </div>
            <button type="button" @click="modifierModal.show = false" class="w-8 h-8 rounded-full bg-[#f8f9fa] border border-[#d8dce2] flex items-center justify-center text-[#53565a] hover:bg-gray-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-4 hide-scroll">
            {{-- Serving Type Selection in Modifier Modal --}}
            <div class="grid grid-cols-2 gap-1.5 p-1 bg-[#f3f4f6] rounded-[10px]">
                <button type="button" @click="modifierModal.servingType = 'DINE_IN'"
                    :class="(modifierModal.servingType || 'DINE_IN') === 'DINE_IN' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-gray-500 font-medium'"
                    class="py-1.5 rounded-[8px] text-[11.5px] transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-utensils text-[10px]"></i>
                    <span>Dine In</span>
                </button>
                <button type="button" @click="modifierModal.servingType = 'TAKEAWAY'"
                    :class="modifierModal.servingType === 'TAKEAWAY' ? 'bg-white text-[#df5826] font-bold shadow-xs' : 'text-gray-500 font-medium'"
                    class="py-1.5 rounded-[8px] text-[11.5px] transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-shopping-bag text-[10px]"></i>
                    <span>Takeaway</span>
                </button>
            </div>
            {{-- Variants --}}
            <template x-if="modifierModal.product?.variants?.length > 0">
            <div>
                <p class="text-[11px] font-bold text-[#8b8e93] uppercase tracking-wider mb-2">Pilih Varian</p>
                <div class="space-y-2">
                    <template x-for="v in modifierModal.product.variants" :key="v.id">
                        <label class="flex items-center gap-3 p-3 rounded-[10px] border cursor-pointer transition"
                            :class="modifierModal.selectedVariant?.id === v.id ? 'border-[#df5826] bg-[#fff6f0]' : 'border-[#d8dce2] hover:border-[#8b8e93]'">
                            <input type="radio" class="sr-only" :value="v.id" x-model.number="modifierModal.selectedVariantId" @change="selectVariant(v)">
                            <span class="text-[12.5px] font-semibold text-[#111827] flex-1" x-text="v.name"></span>
                            <span class="text-[11.5px] text-[#df5826] font-bold" x-show="v.price_adjustment > 0" x-text="'+Rp' + formatRp(v.price_adjustment)"></span>
                        </label>
                    </template>
                </div>
            </div>
            </template>

            {{-- Modifier Groups --}}
            <template x-for="group in (modifierModal.product?.modifier_groups ?? [])" :key="group.id">
            <div>
                <p class="text-[11px] font-bold text-[#8b8e93] uppercase tracking-wider mb-2" x-text="group.name"></p>
                <div class="space-y-2">
                    <template x-for="mod in group.modifiers" :key="mod.id">
                        <label class="flex items-center gap-3 p-3 rounded-[10px] border cursor-pointer transition"
                            :class="isModifierSelected(mod.id) ? 'border-[#df5826] bg-[#fff6f0]' : 'border-[#d8dce2] hover:border-[#8b8e93]'">
                            <input type="checkbox" class="rounded text-[#df5826] focus:ring-[#df5826]" :checked="isModifierSelected(mod.id)" @change="toggleModifier(mod)">
                            <span class="text-[12.5px] font-semibold text-[#111827] flex-1" x-text="mod.name"></span>
                            <span class="text-[11.5px] text-[#df5826] font-bold" x-show="mod.price_adjustment > 0" x-text="'+Rp' + formatRp(mod.price_adjustment)"></span>
                        </label>
                    </template>
                </div>
            </div>
            </template>

            {{-- Notes --}}
            <div>
                <label for="modifier-notes" class="block text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-2">Catatan Customer (Opsional)</label>
                <textarea id="modifier-notes" maxlength="255" x-model="modifierModal.notes" rows="2" placeholder="Contoh: tanpa es, extra panas..."
                    class="w-full px-3 py-2 text-[12px] rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:outline-none focus:border-[#df5826] resize-none"></textarea>
            </div>
        </div>

        <div class="p-4 border-t border-[#e6e5e0] flex items-center gap-3 shrink-0 bg-[#fcfcfd]">
            <div class="bg-[#f8f9fa] border border-[#d8dce2] rounded-full p-1 flex items-center gap-2">
                <button type="button" @click="modifierModal.qty > 1 && modifierModal.qty--"
                    class="w-7 h-7 rounded-full flex items-center justify-center hover:bg-gray-200">
                    <img src="{{ asset('images/sparta/pos-minus.svg') }}" alt="-" class="w-3.5 h-3.5">
                </button>
                <span class="text-[13px] font-bold w-6 text-center text-[#111827]" x-text="modifierModal.qty"></span>
                <button type="button" @click="modifierModal.qty++"
                    class="w-7 h-7 rounded-full bg-[#111827] flex items-center justify-center">
                    <img src="{{ asset('images/sparta/pos-plus.svg') }}" alt="+" class="w-3.5 h-3.5">
                </button>
            </div>
            <button type="button" @click="addToCart()" class="flex-1 py-2.5 px-4 bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-semibold text-[12.5px] rounded-[10px] shadow-sm hover:opacity-95 transition">
                Tambah — Rp<span x-text="formatRp(modalTotal * modifierModal.qty)"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- PAYMENT MODAL                                              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="paymentModal.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-[#1e211f]/60 backdrop-blur-[2px] flex items-center justify-center p-4"
    @click.self="paymentModal.show = false">
    <div class="bg-white border border-[#e6e5e0] rounded-[20px] shadow-2xl w-full max-w-sm max-h-[92dvh] flex flex-col overflow-hidden">
        <div class="p-5 border-b border-[#e6e5e0] flex items-center justify-between shrink-0">
            <div>
                <p class="text-[11px] font-medium text-[#f04c17] uppercase tracking-wider">KONFIRMASI PEMBAYARAN</p>
                <h3 class="font-bold text-[16px] text-[#111827]">Total Tagihan</h3>
                <template x-if="discountAmount > 0">
                    <span class="text-[10.5px] text-emerald-700 font-semibold" x-text="'Hemat Rp ' + formatRp(discountAmount) + (discount.code ? ' (' + discount.code + ')' : '')"></span>
                </template>
            </div>
            <div class="text-right">
                <template x-if="discountAmount > 0">
                    <div class="text-[11px] text-gray-400 line-through leading-tight">Rp<span x-text="formatRp(subtotal)"></span></div>
                </template>
                <p class="text-[22px] font-bold text-[#df5826]">Rp<span x-text="formatRp(grandTotal)"></span></p>
            </div>
        </div>

        <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0">
            {{-- Fulfillment Type Summary --}}
            <div class="bg-[#f8f9fa] p-1 rounded-[10px] grid grid-cols-3 gap-1">
                <template x-for="mode in ['DINE_IN', 'TAKEAWAY', 'DELIVERY']" :key="mode">
                    <button type="button"
                        @click="paymentModal.fulfillment = mode; fulfillmentType = mode"
                        :class="paymentModal.fulfillment === mode ? 'bg-white border border-[#d8dce2] text-[#111827] font-semibold shadow-sm' : 'text-[#53565a] font-medium'"
                        class="py-1.5 rounded-[8px] text-[11.5px] transition">
                        <span x-text="mode === 'DINE_IN' ? 'Dine-in' : (mode === 'TAKEAWAY' ? 'Takeaway' : 'Delivery')"></span>
                    </button>
                </template>
            </div>

            {{-- CONTEXTUAL ACTION: MISSING TABLES --}}
            <div x-show="paymentModal.fulfillment === 'DINE_IN' && tablesCount === 0" class="bg-[#fff6f0] border border-[#f5d5c6] rounded-[12px] p-3.5 flex items-center justify-between gap-3">
                <div class="text-[11.5px] text-[#1e211f]">
                    <p class="font-bold text-[#df5826]">Meja Belum Tersedia</p>
                    <p class="text-[#53565a]">Tambahkan meja untuk pesanan Dine-in.</p>
                </div>
                <button type="button" @click="tableSetupModal.show = true" class="shrink-0 bg-[#df5826] text-white px-3 py-1.5 rounded-[8px] font-semibold text-[11px]">
                    + Buat Meja
                </button>
            </div>

            {{-- Method --}}
            <div>
                <p class="text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-2">Metode Pembayaran</p>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="m in ['CASH','QRIS','CARD']" :key="m">
                    <button type="button" @click="paymentModal.method = m; paymentModal.amountPaid = grandTotal"
                        :class="paymentModal.method === m ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826]' : 'border-[#d8dce2] bg-[#f8f9fa] text-[#53565a] hover:border-[#8b8e93]'"
                        class="py-2.5 rounded-[10px] border font-semibold text-[12px] transition text-center">
                        <i :class="m === 'CASH' ? 'fas fa-money-bill-wave' : m === 'QRIS' ? 'fas fa-qrcode' : 'fas fa-credit-card'" class="block text-base mb-1"></i>
                        <span x-text="m === 'QRIS' ? 'QRIS' : m"></span>
                    </button>
                    </template>
                </div>
            </div>

            {{-- Cash input --}}
            <template x-if="paymentModal.method === 'CASH'">
            <div>
                <p class="text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-1.5">Uang Diterima (F5)</p>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-[#8b8e93] font-semibold text-sm">Rp</span>
                    <input x-ref="cashInput" x-model.number="paymentModal.amountPaid" type="number" step="1000" min="0"
                        class="w-full pl-10 pr-4 py-2.5 rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] outline-none text-right font-bold text-[16px] text-[#111827]">
                </div>
                <div class="flex gap-1.5 mt-2 flex-wrap">
                    <button type="button" @click="paymentModal.amountPaid = grandTotal"
                        class="text-[11px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] hover:bg-[#ffece0] px-2.5 py-1 rounded-[7px] font-bold transition cursor-pointer">
                        Uang Pas
                    </button>
                    <template x-for="quick in smartQuickCash" :key="quick">
                    <button type="button" @click="paymentModal.amountPaid = quick"
                        class="text-[11px] bg-[#f8f9fa] hover:bg-[#fff6f0] border border-[#d8dce2] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] px-2.5 py-1 rounded-[7px] font-semibold transition cursor-pointer"
                        x-text="'Rp' + formatRp(quick)"></button>
                    </template>
                </div>
                <div x-show="paymentModal.amountPaid >= grandTotal" x-cloak class="mt-3 bg-[#eaf7ef] border border-[#bfe5cd] rounded-[10px] p-3 flex items-center justify-between">
                    <span class="text-[12px] text-[#1e211f] font-medium">Kembalian</span>
                    <span class="text-[15px] font-bold text-[#289e5c]">Rp<span x-text="formatRp(paymentModal.amountPaid - grandTotal)"></span></span>
                </div>
            </div>
            </template>

            {{-- QRIS --}}
            <template x-if="paymentModal.method === 'QRIS'">
            <div class="text-center py-2 bg-[#f8f9fa] rounded-[12px] border border-[#d8dce2] p-3">
                <div class="w-24 h-24 bg-white rounded-[12px] border border-[#e6e5e0] mx-auto flex items-center justify-center">
                    <i class="fas fa-qrcode text-4xl text-[#111827]"></i>
                </div>
                <p class="text-[11px] text-[#53565a] mt-2">Minta pelanggan scan QRIS lalu klik Konfirmasi Pembayaran</p>
            </div>
            </template>

            <div class="flex gap-2 pt-1">
                <button type="button" @click="paymentModal.show = false"
                    class="px-4 py-3 rounded-[10px] border border-[#d8dce2] text-[12px] font-semibold text-[#53565a] hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" @click="confirmPayment()"
                    :disabled="paymentPending || !canPay()"
                    :class="canPay() ? 'bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white shadow-sm' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                    class="flex-1 py-3 rounded-[10px] font-semibold text-[13px] transition">
                    <span x-text="paymentPending ? 'Memproses...' : 'Konfirmasi & Buat Pesanan'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SUCCESS SCREEN                                             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="successScreen.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-[#1e211f]/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-[#e6e5e0] rounded-[24px] p-8 max-w-sm w-full text-center shadow-2xl">
        <div class="w-16 h-16 rounded-full bg-[#eaf7ef] text-[#289e5c] flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-3xl"></i>
        </div>
        <p class="text-[12px] font-bold text-[#289e5c] uppercase tracking-wider mb-1">Transaksi Berhasil</p>
        <p class="text-[28px] font-bold text-[#111827] mb-3">Rp<span x-text="formatRp(successScreen.total)"></span></p>
        <template x-if="successScreen.change > 0">
        <div class="bg-[#fff6f0] border border-[#f5d5c6] rounded-[12px] px-5 py-2.5 inline-block mb-5">
            <p class="text-[11px] text-[#53565a]">Kembalian Pelanggan</p>
            <p class="text-[18px] font-bold text-[#df5826]">Rp<span x-text="formatRp(successScreen.change)"></span></p>
        </div>
        </template>
        <div class="grid grid-cols-2 gap-2 mt-3">
            <button type="button" @click="printReceipt('customer')" class="py-2.5 px-3 bg-[#f8f9fa] hover:bg-gray-200 border border-[#d8dce2] rounded-[10px] text-[12px] font-semibold text-[#111827] transition flex items-center justify-center gap-1.5">
                <i class="fas fa-receipt text-[#df5826]"></i> Struk Pelanggan
            </button>
            <button type="button" @click="printReceipt('kitchen')" class="py-2.5 px-3 bg-[#f8f9fa] hover:bg-gray-200 border border-[#d8dce2] rounded-[10px] text-[12px] font-semibold text-[#111827] transition flex items-center justify-center gap-1.5">
                <i class="fas fa-utensils text-[#059669]"></i> Tiket Dapur
            </button>
        </div>
        <div class="flex gap-2 justify-center mt-2.5">
            <button type="button" @click="printAll()" class="w-2/5 py-2.5 px-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-[10px] text-[11px] font-bold transition flex items-center justify-center gap-1">
                <i class="fas fa-print"></i> Cetak Semua
            </button>
            <button type="button" @click="newOrder()" class="flex-1 py-2.5 px-3 bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] rounded-[10px] text-white text-[12px] font-bold shadow-sm transition hover:opacity-95">
                Pesanan Baru
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- OPEN BILL CONFIRMATION MODAL                                --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="openBillConfirmModal.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-[#1e211f]/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-[#e6e5e0] rounded-[24px] p-6 max-w-sm w-full text-center shadow-2xl relative"
         @click.outside="openBillConfirmModal.show = false">
        
        <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3.5 shadow-xs">
            <i class="fas fa-bookmark text-2xl"></i>
        </div>

        <h3 class="text-[18px] font-bold text-[#111827] leading-tight mb-1"
            x-text="recalledOrderId ? 'Perbarui Open Bill Ini?' : 'Jadikan Pesanan Open Bill?'"></h3>
        
        <p class="text-[12px] text-[#53565a] mb-4">
            Pesanan akan disimpan sebagai tagihan berjalan (belum lunas) dan dapat ditambah menu atau dibayar nanti.
        </p>

        <!-- Summary Info Box -->
        <div class="bg-[#fffbf5] border border-amber-200 rounded-[12px] p-3.5 text-left mb-4 space-y-2 text-[12px]">
            <div class="flex items-center justify-between">
                <span class="text-[#53565a]">Tipe / Meja:</span>
                <span class="font-bold text-[#111827] bg-white px-2 py-0.5 rounded border border-amber-200"
                      x-text="fulfillmentType === 'DINE_IN' ? (selectedTable || 'Tanpa Meja / Walk-in') : (fulfillmentType === 'TAKEAWAY' ? 'Takeaway (Bungkus)' : 'Delivery')"></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-[#53565a]">Pelanggan:</span>
                <span class="font-semibold text-[#111827]" x-text="customerName || 'Walk-in Customer'"></span>
            </div>
            <div class="flex items-center justify-between border-t border-amber-200/60 pt-2">
                <span class="font-bold text-[#111827]">Total Tagihan:</span>
                <span class="font-black text-[#df5826] text-[15px]">Rp<span x-text="formatRp(subtotal)"></span></span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <button type="button" @click="openBillConfirmModal.show = false"
                class="py-2.5 px-3 rounded-[10px] border border-[#d8dce2] bg-gray-100 hover:bg-gray-200 text-[#53565a] text-[12px] font-semibold transition">
                Batal
            </button>
            <button type="button" @click="executeHoldCart()" :disabled="paymentPending"
                class="py-2.5 px-3 rounded-[10px] bg-gradient-to-b from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white text-[12px] font-bold shadow-sm transition flex items-center justify-center gap-1.5 disabled:opacity-50">
                <i class="fas fa-check" x-show="!paymentPending"></i>
                <i class="fas fa-spinner fa-spin" x-show="paymentPending" x-cloak></i>
                <span x-text="paymentPending ? 'Menyimpan...' : (recalledOrderId ? 'Ya, Update' : 'Ya, Open Bill')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- OPEN BILL SUCCESS MODAL                                     --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="openBillSuccessModal.show" x-cloak x-transition
    class="fixed inset-0 z-[70] bg-[#1e211f]/70 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="closeOpenBillSuccessModal()">
    <div class="bg-white border border-[#e6e5e0] rounded-[24px] p-6 max-w-sm w-full text-center shadow-2xl relative"
         @click.outside="closeOpenBillSuccessModal()">
        
        <!-- Close Button (X) Top-Right -->
        <button type="button" 
                @click="closeOpenBillSuccessModal()" 
                class="absolute top-4 right-4 w-7 h-7 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition cursor-pointer"
                title="Tutup">
            <i class="fas fa-times text-xs"></i>
        </button>

        <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3.5 shadow-xs">
            <i class="fas fa-bookmark text-2xl"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200"
              x-text="openBillSuccessModal.updated ? 'Open Bill Diperbarui' : 'Open Bill Tersimpan'"></span>
        
        <h3 class="text-[20px] font-bold text-[#111827] mt-2 mb-1" x-text="openBillSuccessModal.table"></h3>
        <p class="text-[12.5px] text-[#53565a] mb-4">
            Tagihan Sementara: <strong class="text-[#df5826] text-[15px]">Rp<span x-text="formatRp(openBillSuccessModal.total)"></span></strong>
        </p>

        <div class="space-y-2 mt-2">
            <button type="button" @click="printKitchenTicket(openBillSuccessModal.orderId); closeOpenBillSuccessModal()"
                class="w-full py-2.5 px-4 bg-[#059669] hover:bg-[#047857] text-white rounded-[10px] text-[12px] font-bold transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                <i class="fas fa-utensils"></i> Cetak Tiket Dapur / Bar
            </button>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="printTempBill(openBillSuccessModal.orderId); closeOpenBillSuccessModal()"
                    class="py-2.5 px-3 bg-[#f8f9fa] hover:bg-gray-100 border border-[#d8dce2] text-[#111827] rounded-[10px] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-receipt text-[#df5826]"></i> Bill Sementara
                </button>
                <button type="button" @click="closeOpenBillSuccessModal()"
                    class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-[#53565a] rounded-[10px] text-[11.5px] font-semibold transition cursor-pointer">
                    Tutup (Selesai)
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- TABLE SETUP CONTEXTUAL MODAL                               --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="tableSetupModal.show" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div x-show="tableSetupModal.show" x-transition.opacity class="absolute inset-0 bg-[#1e211f]/50 backdrop-blur-sm" @click="tableSetupModal.show = false"></div>

    <div x-show="tableSetupModal.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-white border border-[#e6e5e0] rounded-[20px] w-full max-w-sm shadow-2xl relative z-10 overflow-hidden flex flex-col">

        <div class="p-4 border-b border-[#e6e5e0] flex items-center justify-between bg-[#fcfcfd]">
            <h3 class="font-bold text-[15px] text-[#111827]">Tambah Meja Baru</h3>
            <button type="button" @click="tableSetupModal.show = false" class="w-7 h-7 flex items-center justify-center text-[#8b8e93] hover:text-[#111827] rounded-full">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="p-5 space-y-3">
            <label class="block text-[11.5px] font-semibold text-[#53565a]">Nama / Nomor Meja</label>
            <input type="text" x-model="tableSetupModal.name"
                class="w-full px-3.5 py-2.5 border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] rounded-[10px] outline-none font-semibold text-[14px] text-[#111827]"
                placeholder="Contoh: 04 atau Meja 04" @keydown.enter="submitTableSetup()">
        </div>

        <div class="p-4 bg-[#fcfcfd] border-t border-[#e6e5e0]">
            <button type="button" @click="submitTableSetup()" :disabled="!tableSetupModal.name || tableSetupModal.loading"
                class="w-full bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-semibold py-2.5 rounded-[10px] text-[12.5px] shadow-sm transition disabled:opacity-50">
                <span x-show="!tableSetupModal.loading">Simpan Meja</span>
                <span x-show="tableSetupModal.loading">Memproses...</span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- DISCOUNT & VOUCHER MODAL (Figma Screen 2)                   --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="discountModal.show" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div x-show="discountModal.show" x-transition.opacity class="absolute inset-0 bg-[#1e211f]/60 backdrop-blur-sm" @click="discountModal.show = false"></div>

    <div x-show="discountModal.show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="bg-white border border-[#e6e5e0] rounded-[20px] w-full max-w-lg shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">

        {{-- Header --}}
        <div class="p-4 px-5 border-b border-[#e6e5e0] flex items-center justify-between bg-white">
            <div>
                <h3 class="font-bold text-[16px] text-[#111827] leading-tight">Diskon &amp; Voucher Promo</h3>
                <p class="text-[11.5px] text-[#7f837e] mt-0.5">Subtotal: <strong class="text-[#111827]">Rp<span x-text="formatRp(subtotal)"></span></strong></p>
            </div>
            <button type="button" @click="discountModal.show = false" class="w-8 h-8 flex items-center justify-center text-[#8b8e93] hover:text-[#111827] rounded-full transition cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        {{-- Segmented Tabs --}}
        <div class="p-3 px-5 bg-white border-b border-[#e6e5e0]">
            <div class="bg-[#f3f4f6] p-[3px] rounded-[11px] grid grid-cols-3 gap-[3px]">
                <button type="button" @click="discountModal.tab = 'preset'"
                    :class="discountModal.tab === 'preset' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] hover:text-[#111827] font-semibold'"
                    class="py-2 px-2 rounded-[9px] text-[11.5px] transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-percent text-[10px] text-emerald-700"></i>
                    <span>Quick Discount</span>
                </button>
                <button type="button" @click="discountModal.tab = 'manual'"
                    :class="discountModal.tab === 'manual' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] hover:text-[#111827] font-semibold'"
                    class="py-2 px-2 rounded-[9px] text-[11.5px] transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-pencil-alt text-[10px]"></i>
                    <span>Manual</span>
                </button>
                <button type="button" @click="discountModal.tab = 'voucher'"
                    :class="discountModal.tab === 'voucher' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] hover:text-[#111827] font-semibold'"
                    class="py-2 px-2 rounded-[9px] text-[11.5px] transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fas fa-ticket-alt text-[10px]"></i>
                    <span>Voucher Code</span>
                </button>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="p-5 overflow-y-auto flex-1 min-h-0 space-y-4">
            
            {{-- TAB 1: QUICK DISCOUNT (Figma 3x2 Grid) --}}
            <div x-show="discountModal.tab === 'preset'" class="space-y-3">
                <div class="grid grid-cols-3 gap-2.5">
                    {{-- 1. 5% Discount --}}
                    <button type="button" @click="applyQuickDiscount('percentage', 5, '5% Discount')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-percent text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">5% Discount</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block" x-text="'Hemat Rp' + formatRp(Math.round((subtotal * 5)/100))">Hemat Rp8.030</span>
                        </div>
                    </button>

                    {{-- 2. 10% Discount --}}
                    <button type="button" @click="applyQuickDiscount('percentage', 10, '10% Discount')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-percent text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">10% Discount</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block" x-text="'Hemat Rp' + formatRp(Math.round((subtotal * 10)/100))">Hemat Rp16.060</span>
                        </div>
                    </button>

                    {{-- 3. 15% Discount --}}
                    <button type="button" @click="applyQuickDiscount('percentage', 15, '15% Discount')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-percent text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">15% Discount</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block" x-text="'Hemat Rp' + formatRp(Math.round((subtotal * 15)/100))">Hemat Rp24.090</span>
                        </div>
                    </button>

                    {{-- 4. Rp5.000 --}}
                    <button type="button" @click="applyQuickDiscount('fixed', 5000, 'Rp5.000')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-money-bill-wave text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">Rp5.000</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block">Potongan Rp5.000</span>
                        </div>
                    </button>

                    {{-- 5. Rp25.000 --}}
                    <button type="button" @click="applyQuickDiscount('fixed', 25000, 'Rp25.000')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-money-bill-wave text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">Rp25.000</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block">Potongan Rp25.000</span>
                        </div>
                    </button>

                    {{-- 6. Rp30.000 --}}
                    <button type="button" @click="applyQuickDiscount('fixed', 30000, 'Rp30.000')"
                        class="p-3 rounded-[12px] border border-[#e6e5e0] hover:border-[#059669] bg-white hover:bg-[#e8f8f0]/40 text-left transition shadow-2xs flex flex-col justify-between gap-2.5 group cursor-pointer">
                        <div class="w-8 h-7 rounded-[7px] bg-[#e6f4ea] text-[#137333] flex items-center justify-center font-bold text-xs">
                            <i class="fas fa-money-bill-wave text-[10px]"></i>
                        </div>
                        <div>
                            <span class="font-bold text-[12.5px] text-[#111827] group-hover:text-[#065f46] block leading-tight">Rp30.000</span>
                            <span class="text-[10px] text-[#8b8e93] font-medium mt-0.5 block">Potongan Rp30.000</span>
                        </div>
                    </button>
                </div>

                {{-- Applied discount indicator inside modal --}}
                <div x-show="discountAmount > 0" class="pt-2">
                    <div class="p-2.5 px-3 bg-[#e8f8f0] border border-[#a7f3d0] rounded-[10px] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#065f46]" x-text="'Diskon Aktif: ' + (discount.code || discount.reason || 'Diskon')"></span>
                            <span class="text-xs font-black text-[#059669]" x-text="'-Rp' + formatRp(discountAmount)"></span>
                        </div>
                        <button type="button" @click="removeDiscount()" class="text-xs text-red-600 hover:text-red-700 font-bold hover:underline cursor-pointer">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            {{-- TAB 2: MANUAL INPUT --}}
            <div x-show="discountModal.tab === 'manual'" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-2">Tipe Diskon</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="discountModal.manualType = 'percentage'"
                            :class="discountModal.manualType === 'percentage' ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] font-bold' : 'border-[#d8dce2] bg-[#f8f9fa] text-[#53565a] font-medium'"
                            class="py-2.5 rounded-xl border text-[12px] transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-percent text-xs"></i>
                            <span>Persentase (%)</span>
                        </button>
                        <button type="button" @click="discountModal.manualType = 'fixed'"
                            :class="discountModal.manualType === 'fixed' ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] font-bold' : 'border-[#d8dce2] bg-[#f8f9fa] text-[#53565a] font-medium'"
                            class="py-2.5 rounded-xl border text-[12px] transition flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fas fa-coins text-xs"></i>
                            <span>Nominal Tetap (Rp)</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-1.5"
                           x-text="discountModal.manualType === 'percentage' ? 'Besaran Diskon (%)' : 'Jumlah Potongan (Rp)'"></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm"
                              x-text="discountModal.manualType === 'percentage' ? '%' : 'Rp'"></span>
                        <input type="number" x-model.number="discountModal.manualValue" :min="0" :max="discountModal.manualType === 'percentage' ? 100 : subtotal"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] outline-none font-bold text-[15px] text-[#111827]"
                            :placeholder="discountModal.manualType === 'percentage' ? 'Contoh: 10' : 'Contoh: 15000'">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-1.5">Alasan / Catatan Diskon (Opsional)</label>
                    <input type="text" x-model="discountModal.manualReason" maxlength="100"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] outline-none text-[12.5px] text-[#111827]"
                        placeholder="Misal: Member VIP, Teman Owner, Promo Khusus">
                </div>

                {{-- Live preview calculation --}}
                <div x-show="discountModal.manualValue > 0" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-[12px]">
                    <span class="text-emerald-800 font-medium">Estimasi Potongan:</span>
                    <span class="text-emerald-700 font-bold text-[13px]">
                        -Rp<span x-text="formatRp(discountModal.manualType === 'percentage' ? Math.min(subtotal, (subtotal * discountModal.manualValue)/100) : Math.min(subtotal, discountModal.manualValue))"></span>
                    </span>
                </div>

                <button type="button" @click="applyManualDiscount()" :disabled="!discountModal.manualValue || discountModal.manualValue <= 0"
                    class="w-full py-2.5 px-4 bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-bold text-[12.5px] rounded-xl shadow-sm hover:opacity-95 transition disabled:opacity-50 cursor-pointer">
                    Terapkan Diskon Manual
                </button>
            </div>

            {{-- TAB 3: VOUCHER CODE --}}
            <div x-show="discountModal.tab === 'voucher'" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold text-[#53565a] uppercase tracking-wider mb-1.5">Masukkan Kode Voucher Promo</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">
                            <i class="fas fa-ticket-alt text-[#df5826]"></i>
                        </span>
                        <input type="text" x-model="discountModal.voucherCode" @input="discountModal.voucherCode = discountModal.voucherCode.toUpperCase()"
                            @keydown.enter="applyVoucherCode()"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] outline-none font-mono uppercase font-black text-[15px] tracking-wider text-[#111827]"
                            placeholder="Contoh: HEMAT10, KOPIASIK">
                    </div>
                    <p class="text-[10.5px] text-[#8b8e93] mt-1.5">Ketik kode kupon yang dibagikan kepada pelanggan atau promosi aktif toko.</p>
                </div>

                {{-- Voucher error alert --}}
                <div x-show="discountModal.voucherError" x-cloak class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl flex items-start gap-2">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5 shrink-0"></i>
                    <span x-text="discountModal.voucherError"></span>
                </div>

                <button type="button" @click="applyVoucherCode()" :disabled="!discountModal.voucherCode || discountModal.voucherLoading"
                    class="w-full py-2.5 px-4 bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-bold text-[12.5px] rounded-xl shadow-sm hover:opacity-95 transition disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-spinner fa-spin" x-show="discountModal.voucherLoading" x-cloak></i>
                    <span x-text="discountModal.voucherLoading ? 'Memeriksa Voucher...' : 'Terapkan Voucher Promo'"></span>
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="p-3 px-5 border-t border-[#e6e5e0] bg-[#fcfcfd] flex items-center justify-between">
            <template x-if="discountAmount > 0">
                <button type="button" @click="removeDiscount(); discountModal.show = false"
                    class="text-xs text-red-600 hover:text-red-700 font-bold flex items-center gap-1.5 px-2 py-1 rounded hover:bg-red-50 transition cursor-pointer">
                    <i class="fas fa-trash-alt text-[11px]"></i>
                    <span>Hapus Diskon Aktif</span>
                </button>
            </template>
            <div x-show="discountAmount === 0"></div>
            <button type="button" @click="discountModal.show = false"
                class="px-4 py-2 rounded-xl border border-[#d8dce2] text-xs font-semibold text-[#53565a] hover:bg-gray-100 transition cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- ALPINE JS CONTROLLER                                        --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<script>
function posApp() {
    return {
        // State
        cart: [],
        showMobileCart: false,
        isCartMinimized: false,
        showPriceBreakdown: false,
        isQueueExpanded: true,
        activeCategory: null,
        search: '',
        fulfillmentType: 'DINE_IN',
        customerName: '',
        selectedTable: '',
        heldOrders: {{ Illuminate\Support\Js::from($heldOrderSummaries) }} || [],
        activeOrders: {{ Illuminate\Support\Js::from($activeQueueOrders ?? []) }} || [],
        recalledOrderId: null,
        paymentPending: false,
        checkoutRequestId: null,
        toast: { show: false, message: '', type: 'success', timeout: null },

        // Modals
        modifierModal: {
            show: false, product: null, selectedVariant: null, selectedVariantId: null,
            selectedModifiers: [], qty: 1, notes: ''
        },
        paymentModal: { show: false, method: 'CASH', amountPaid: '', fulfillment: 'DINE_IN' },
        successScreen: { show: false, total: 0, change: 0, orderId: null },
        openBillConfirmModal: { show: false },
        openBillSuccessModal: { show: false, orderId: null, table: '', total: 0, updated: false },
        tableSetupModal: { show: false, name: '', loading: false },

        quickCash: [10000, 20000, 50000, 100000],

        // Discount & Voucher state
        discountSettings: {{ Illuminate\Support\Js::from($shop->discount_settings ?? [
            'is_enabled' => true,
            'allow_custom' => true,
            'presets' => [
                ['id' => 'p1', 'name' => '5%', 'type' => 'percentage', 'value' => 5],
                ['id' => 'p2', 'name' => '10%', 'type' => 'percentage', 'value' => 10],
                ['id' => 'p3', 'name' => '15%', 'type' => 'percentage', 'value' => 15],
                ['id' => 'p4', 'name' => '20%', 'type' => 'percentage', 'value' => 20],
                ['id' => 'p5', 'name' => 'Rp 5.000', 'type' => 'fixed', 'value' => 5000],
                ['id' => 'p6', 'name' => 'Rp 10.000', 'type' => 'fixed', 'value' => 10000],
            ]
        ]) }},
        discount: {
            type: null,
            value: 0,
            amount: 0,
            reason: '',
            code: null,
            maxDiscount: null
        },
        discountModal: {
            show: false,
            tab: 'preset',
            manualType: 'percentage',
            manualValue: '',
            manualReason: '',
            voucherCode: '',
            voucherLoading: false,
            voucherError: '',
            isEditingPresets: false,
            newPresetName: '',
            newPresetType: 'percentage',
            newPresetValue: '',
            isSavingPreset: false
        },

        incomingCount: 0,
        lastKnownOrderIds: [],

        tablesCount: {{ $tables->count() }} || 0,
        salesModes: @json($shop->sales_modes ?? ['DINE_IN', 'TAKEAWAY', 'DELIVERY']),

        init() {
            this.fetchIncoming();
            setInterval(() => this.fetchIncoming(), 5000);

            // Auto-recall order from URL query (?recall=12&pay=1)
            const urlParams = new URLSearchParams(window.location.search);
            const recallId = urlParams.get('recall');
            const autoPay = urlParams.get('pay');
            if (recallId) {
                this.$nextTick(() => {
                    this.recallOrder(parseInt(recallId));
                    if (autoPay === '1') {
                        setTimeout(() => this.openPayment(), 500);
                    }
                });
            }
        },

        normalizeLiveOrder(o) {
            const tableName = o.table && typeof o.table === 'object'
                ? (o.table.name || '')
                : (typeof o.table === 'string' ? o.table : '');

            const tableDigits = tableName ? tableName.replace(/\D/g, '') : (o.table_number || '');

            let formattedTime = o.time || '--:--';
            if ((!o.time || o.time === '--:--') && o.created_at) {
                const dt = new Date(o.created_at);
                if (!isNaN(dt.getTime())) {
                    formattedTime = dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');
                }
            }

            const isOpenBill = Boolean(o.is_held || (o.payment_status && o.payment_status.toUpperCase() === 'UNPAID') || (o.order_status && o.order_status.toUpperCase() === 'HOLD') || (o.status && o.status.toUpperCase() === 'HOLD'));

            return {
                id: o.id,
                table: tableName || (o.fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : (o.fulfillment_type === 'DELIVERY' ? 'Delivery' : 'Tanpa Meja')),
                table_number: tableDigits,
                customer_name: o.customer_name || o.customer || 'Walk-in Customer',
                total: Number(o.total ?? o.grand_total ?? o.total_price ?? 0),
                status: o.status || o.order_status || 'CONFIRMED',
                order_status: o.order_status || o.status || 'CONFIRMED',
                payment_status: o.payment_status || (isOpenBill ? 'UNPAID' : 'PAID'),
                is_open_bill: isOpenBill,
                fulfillment_type: o.fulfillment_type || 'DINE_IN',
                time: formattedTime,
                items: Array.isArray(o.items) ? o.items.map(i => ({
                    name: i.name || i.product_name || i.product?.name || 'Menu',
                    qty: Number(i.qty ?? i.quantity ?? 1),
                    status: i.status || null,
                })) : [],
            };
        },

        async fetchIncoming() {
            try {
                const res = await fetch('/admin/api/orders/live?t=' + new Date().getTime(), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const dbOrders = await res.json();

                if (Array.isArray(dbOrders)) {
                    const normalized = dbOrders.map(o => this.normalizeLiveOrder(o));
                    this.activeOrders = normalized.filter(o =>
                        o.status !== 'Selesai' && o.order_status !== 'COMPLETED' && o.order_status !== 'CANCELLED' && o.order_status !== 'HOLD'
                    );
                    const newIncoming = normalized.filter(o =>
                        o.status === 'Masuk' || o.order_status === 'CONFIRMED' || o.status === 'CONFIRMED'
                    );
                    this.incomingCount = newIncoming.length;
                    this.lastKnownOrderIds = newIncoming.map(o => o.id);
                }
            } catch (e) {
                // silent
            }
        },

        // ── Queue Formatting Helpers (Figma Frame 1597884462) ──
        goToActiveOrder(order) {
            if (!order || !order.id) return;
            window.location.href = '{{ route('admin.orders.index') }}?order=' + order.id;
        },
        formatTableBadge(order, idx) {
            if (!order) return String((idx ?? 0) + 1).padStart(2, '0');
            if (order.table_number) {
                return String(order.table_number).padStart(2, '0').slice(-2);
            }
            if (order.table) {
                const raw = typeof order.table === 'object' ? (order.table.name || '') : String(order.table);
                const digits = raw.replace(/\D/g, '');
                if (digits) return digits.padStart(2, '0').slice(-2);
            }
            return String((idx ?? 0) + 1).padStart(2, '0');
        },
        getOrderStatusInfo(order) {
            if (!order) return { label: 'Pesanan Baru', classes: 'bg-[#e0f2fe] border border-[#bae6fd] text-[#0369a1]' };

            if (Array.isArray(order.items) && order.items.length > 1) {
                const itemStatuses = new Set(order.items.map(i => (i.status || '').toUpperCase()).filter(Boolean));
                if (itemStatuses.size > 1) {
                    return {
                        label: `${itemStatuses.size} Status`,
                        classes: 'bg-[#fff4ed] border border-[#fed7aa] text-[#ea580c]'
                    };
                }
            }

            const st = (order.status || order.order_status || '').toUpperCase();
            if (['READY', 'SIAP'].includes(st)) {
                return {
                    label: 'Siap Disajikan',
                    classes: 'bg-[#ecfdf5] border border-[#a7f3d0] text-[#059669]'
                };
            }
            if (['PREPARING', 'IN PROGRESS', 'DIPROSES', 'MEMASAK'].includes(st)) {
                return {
                    label: 'Sedang Dimasak',
                    classes: 'bg-[#f3e8ff] border border-[#e9d5ff] text-[#7e22ce]'
                };
            }
            if (['COMPLETED', 'SELESAI'].includes(st)) {
                return {
                    label: 'Selesai',
                    classes: 'bg-[#f3f4f6] border border-[#e5e7eb] text-[#4b5563]'
                };
            }
            if (['CANCELLED', 'BATAL', 'DIBATALKAN'].includes(st)) {
                return {
                    label: 'Dibatalkan',
                    classes: 'bg-red-50 border border-red-200 text-red-600'
                };
            }

            return {
                label: 'Pesanan Baru',
                classes: 'bg-[#e0f2fe] border border-[#bae6fd] text-[#0369a1]'
            };
        },

        // ── Computed ──────────────────────────
        get subtotal() {
            return this.cart.reduce((s, i) => s + i.subtotal, 0);
        },
        get discountAmount() {
            if (!this.discount || (!this.discount.type && !this.discount.amount)) return 0;
            const sub = this.subtotal;
            if (sub <= 0) return 0;
            if (this.discount.amount && !this.discount.type) {
                return Math.min(sub, Number(this.discount.amount));
            }
            let calculated = 0;
            if (this.discount.type === 'percentage') {
                calculated = (sub * Number(this.discount.value || 0)) / 100;
                if (this.discount.maxDiscount && Number(this.discount.maxDiscount) > 0) {
                    calculated = Math.min(calculated, Number(this.discount.maxDiscount));
                }
            } else {
                calculated = Number(this.discount.value || 0);
            }
            return Math.min(sub, Math.max(0, calculated));
        },
        get grandTotal() {
            return Math.max(0, this.subtotal - this.discountAmount);
        },
        get modalTotal() {
            if (!this.modifierModal.product) return 0;
            let base = Number(this.modifierModal.product.effective_price || this.modifierModal.product.price);
            if (this.modifierModal.selectedVariant) base += Number(this.modifierModal.selectedVariant.price_adjustment || 0);
            this.modifierModal.selectedModifiers.forEach(m => base += Number(m.price_adjustment || 0));
            return base;
        },
        get smartQuickCash() {
            const tot = Number(this.grandTotal) || 0;
            if (tot <= 0) return [10000, 20000, 50000, 100000];
            const options = new Set();
            const stepCeils = [
                Math.ceil(tot / 10000) * 10000,
                Math.ceil(tot / 20000) * 20000,
                Math.ceil(tot / 50000) * 50000,
                Math.ceil(tot / 100000) * 100000,
            ];
            stepCeils.forEach(val => {
                if (val > tot) options.add(val);
            });
            [50000, 100000, 150000, 200000, 300000, 500000].forEach(val => {
                if (val > tot && options.size < 5) options.add(val);
            });
            return Array.from(options).sort((a, b) => a - b).slice(0, 4);
        },

        // ── Filters ───────────────────────────
        matchesFilter(catId, name, soldCount = 0, isBestSeller = false) {
            let catMatch = true;
            if (this.activeCategory === 'best_seller') {
                catMatch = Boolean(isBestSeller) || Number(soldCount) > 0;
            } else if (this.activeCategory !== null) {
                catMatch = catId === this.activeCategory;
            }
            const searchMatch = this.search === '' || name.toLowerCase().includes(this.search.toLowerCase());
            return catMatch && searchMatch;
        },

        // ── Modifier Modal ───────────────────
        handleProductClick(product, servingType = 'DINE_IN') {
            const hasVariants = product.variants && product.variants.length > 0;
            const hasModifiers = product.modifier_groups && product.modifier_groups.length > 0;

            if (hasVariants || hasModifiers) {
                this.openModifier(product, servingType);
            } else {
                this.addToCartDirectly(product, servingType);
            }
        },
        addToCartDirectly(p, servingType = 'DINE_IN') {
            const unitPrice = Number(p.effective_price !== undefined ? p.effective_price : p.price);
            const existing = this.cart.find(c => c.id === p.id && !c.variant_id && (!c.modifiers || c.modifiers.length === 0) && !c.notes && (c.serving_type || 'DINE_IN') === servingType);
            if (existing) {
                existing.qty++;
                existing.subtotal = existing.price * existing.qty;
            } else {
                this.cart.push({
                    id: p.id, name: p.name,
                    image: p.image_url || null,
                    variant_id: null, variant: null,
                    modifiers: [], notes: '', showNotes: false, qty: 1, 
                    price: unitPrice,
                    regular_price: Number(p.price),
                    has_discount: Boolean(p.has_active_discount),
                    discount_percentage: p.discount_percentage || null,
                    subtotal: unitPrice,
                    serving_type: servingType,
                });
            }
        },
        openModifier(product, servingType = 'DINE_IN') {
            this.modifierModal = {
                show: true, product, selectedVariant: null, selectedVariantId: null,
                selectedModifiers: [], qty: 1, notes: '', servingType: servingType,
            };
            if (product.variants?.length === 1) this.selectVariant(product.variants[0]);
        },
        selectVariant(v) { this.modifierModal.selectedVariant = v; },
        isModifierSelected(id) { return this.modifierModal.selectedModifiers.some(m => m.id === id); },
        toggleModifier(mod) {
            const idx = this.modifierModal.selectedModifiers.findIndex(m => m.id === mod.id);
            idx === -1 ? this.modifierModal.selectedModifiers.push(mod) : this.modifierModal.selectedModifiers.splice(idx, 1);
        },
        toggleServingType(idx) {
            if (!this.cart[idx]) return;
            this.cart[idx].serving_type = (this.cart[idx].serving_type === 'TAKEAWAY') ? 'DINE_IN' : 'TAKEAWAY';
        },

        addToCart() {
            const p = this.modifierModal.product;
            const qty = this.modifierModal.qty;
            const variant = this.modifierModal.selectedVariant;
            const mods = [...this.modifierModal.selectedModifiers];
            const notes = this.modifierModal.notes.trim();
            const price = this.modalTotal;
            const servingType = this.modifierModal.servingType || 'DINE_IN';

            this.cart.push({
                id: p.id, name: p.name,
                image: p.image_url || null,
                variant_id: variant?.id ?? null,
                variant: variant?.name ?? null,
                modifiers: mods,
                notes, showNotes: Boolean(notes), qty, price,
                regular_price: Number(p.price),
                has_discount: Boolean(p.has_active_discount),
                discount_percentage: p.discount_percentage || null,
                subtotal: price * qty,
                serving_type: servingType,
            });

            this.modifierModal.show = false;
        },

        // ── Cart ──────────────────────────────
        changeQty(idx, delta) {
            this.cart[idx].qty = Math.max(1, this.cart[idx].qty + delta);
            this.cart[idx].subtotal = this.cart[idx].price * this.cart[idx].qty;
        },
        splitItem(idx) {
            const item = this.cart[idx];
            if (this.paymentPending || !item || item.qty <= 1 || this.cart.length >= 50) return;
            item.qty--;
            item.subtotal = item.price * item.qty;
            this.cart.splice(idx + 1, 0, {...item, modifiers: (item.modifiers ?? []).map(modifier => ({...modifier})), qty: 1, subtotal: item.price, showNotes: true});
            this.$nextTick(() => document.getElementById('item-notes-' + (idx + 1))?.focus());
        },
        removeItem(idx) { this.cart.splice(idx, 1); },
        clearCart() {
            this.cart = [];
            this.recalledOrderId = null;
            this.checkoutRequestId = null;
            this.customerName = '';
            this.selectedTable = '';
            this.discount = {
                type: null,
                value: 0,
                amount: 0,
                reason: '',
                code: null,
                maxDiscount: null
            };
        },

        // ── Discount & Voucher Actions ───────────
        openDiscountModal() {
            if (this.cart.length === 0) {
                this.notify('Pilih menu terlebih dahulu untuk menerapkan diskon', 'error');
                return;
            }
            this.discountModal.voucherError = '';
            this.discountModal.voucherLoading = false;
            this.discountModal.show = true;
        },
        applyPresetDiscount(preset) {
            this.discount = {
                type: preset.type,
                value: Number(preset.value),
                amount: 0,
                reason: preset.name,
                code: null,
                maxDiscount: null
            };
            this.discountModal.show = false;
            this.notify(`Diskon ${preset.name} berhasil diterapkan!`, 'success');
        },
        applyQuickDiscount(type, value, name) {
            this.discount = {
                type: type,
                value: Number(value),
                amount: 0,
                reason: name,
                code: null,
                maxDiscount: null
            };
            this.discountModal.show = false;
            this.notify(`Diskon ${name} berhasil diterapkan!`, 'success');
        },
        applyManualDiscount() {
            const val = Number(this.discountModal.manualValue);
            if (!val || val <= 0) {
                this.notify('Masukkan nilai diskon yang valid', 'error');
                return;
            }
            if (this.discountModal.manualType === 'percentage' && val > 100) {
                this.notify('Persentase diskon maksimal 100%', 'error');
                return;
            }
            const defaultReason = this.discountModal.manualType === 'percentage'
                ? `Diskon ${val}%`
                : `Potongan Rp ${this.formatRp(val)}`;
            this.discount = {
                type: this.discountModal.manualType,
                value: val,
                amount: 0,
                reason: (this.discountModal.manualReason || '').trim() || defaultReason,
                code: null,
                maxDiscount: null
            };
            this.discountModal.show = false;
            this.notify('Diskon manual berhasil diterapkan!', 'success');
        },
        async applyVoucherCode() {
            const code = (this.discountModal.voucherCode || '').trim();
            if (!code) {
                this.discountModal.voucherError = 'Silakan ketik kode voucher.';
                return;
            }
            this.discountModal.voucherLoading = true;
            this.discountModal.voucherError = '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('admin.pos.vouchers.validate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({
                        code: code,
                        subtotal: this.subtotal
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.discount = {
                        type: data.voucher.discount_type,
                        value: Number(data.voucher.discount_value),
                        amount: Number(data.discount_amount),
                        reason: data.voucher.name,
                        code: data.voucher.code,
                        maxDiscount: data.voucher.max_discount
                    };
                    this.discountModal.show = false;
                    this.discountModal.voucherCode = '';
                    this.notify(`Voucher "${data.voucher.code}" berhasil diterapkan!`, 'success');
                } else {
                    this.discountModal.voucherError = data.message || 'Kode voucher tidak valid.';
                }
            } catch (e) {
                this.discountModal.voucherError = 'Gagal memeriksa voucher. Silakan coba lagi.';
            } finally {
                this.discountModal.voucherLoading = false;
            }
        },
        async saveNewPosDiscountPreset() {
            const name = (this.discountModal.newPresetName || '').trim();
            const val = Number(this.discountModal.newPresetValue);
            const type = this.discountModal.newPresetType || 'percentage';
            if (!name) {
                this.notify('Nama shortcut wajib diisi', 'error');
                return;
            }
            if (isNaN(val) || val <= 0) {
                this.notify('Nilai diskon harus lebih dari 0', 'error');
                return;
            }
            if (type === 'percentage' && val > 100) {
                this.notify('Persentase maksimal 100%', 'error');
                return;
            }

            this.discountModal.isSavingPreset = true;
            try {
                const currentPresets = Array.isArray(this.discountSettings?.presets) ? [...this.discountSettings.presets] : [];
                currentPresets.push({
                    id: 'pos_p_' + Date.now(),
                    name: name,
                    type: type,
                    value: val
                });

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('admin.pos.discount-settings.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({
                        presets: currentPresets
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.discountSettings.presets = data.discount_settings.presets;
                    this.discountModal.newPresetName = '';
                    this.discountModal.newPresetValue = '';
                    this.notify('Shortcut diskon baru berhasil disimpan!', 'success');
                } else {
                    this.notify(data.message || 'Gagal menyimpan shortcut', 'error');
                }
            } catch (err) {
                this.notify('Koneksi gagal saat menyimpan shortcut', 'error');
            } finally {
                this.discountModal.isSavingPreset = false;
            }
        },

        async deletePosDiscountPreset(index) {
            if (!confirm('Hapus shortcut diskon ini?')) return;
            const currentPresets = Array.isArray(this.discountSettings?.presets) ? [...this.discountSettings.presets] : [];
            currentPresets.splice(index, 1);

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch('{{ route('admin.pos.discount-settings.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: JSON.stringify({
                        presets: currentPresets
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.discountSettings.presets = data.discount_settings.presets;
                    this.notify('Shortcut diskon berhasil dihapus!', 'success');
                }
            } catch (err) {
                this.notify('Gagal menghapus shortcut', 'error');
            }
        },

        removeDiscount() {
            this.discount = {
                type: null,
                value: 0,
                amount: 0,
                reason: '',
                code: null,
                maxDiscount: null
            };
            this.notify('Diskon / voucher dihapus', 'info');
        },

        // ── Open Bill (Hold / Recall) ─────────────────────
        confirmOpenBill() {
            if (this.cart.length === 0) {
                this.notify('Keranjang belanja masih kosong', 'error');
                return;
            }
            this.openBillConfirmModal.show = true;
        },

        closeOpenBillSuccessModal() {
            this.openBillSuccessModal.show = false;
            this.openBillConfirmModal.show = false;
        },

        async executeHoldCart() {
            this.openBillConfirmModal.show = false;
            await this.holdCart();
        },

        async holdCart() {
            if (this.cart.length === 0) return;
            this.paymentPending = true;
            const payload = {
                items: this.cartPayload(),
                table: this.fulfillmentType === 'DINE_IN' ? (this.selectedTable || null) : null,
                fulfillment_type: this.fulfillmentType || 'DINE_IN',
                customer_name: (this.customerName || '').trim() || null,
                recalled_order_id: this.recalledOrderId || null,
                voucher_code: this.discount.code || null,
                discount_type: this.discount.type || null,
                discount_value: this.discount.value || 0,
                discount_amount: this.discountAmount,
                discount_reason: this.discount.reason || null,
            };
            try {
                const res = await this.post('{{ route('admin.pos.orders.hold') }}', payload);
                if (res.success) {
                    if (res.updated) {
                        this.heldOrders = this.heldOrders.map(o => o.id === res.order_id ? {
                            id: res.order_id,
                            total: res.total,
                            customer_name: res.customer_name,
                            table: res.table,
                            time: res.time,
                            items_count: res.items_count,
                        } : o);
                        this.notify(`Open Bill #${res.order_id} (${res.table}) berhasil diperbarui!`, 'success');
                    } else {
                        this.heldOrders.unshift({
                            id: res.order_id,
                            total: res.total,
                            customer_name: res.customer_name,
                            table: res.table,
                            time: res.time,
                            items_count: res.items_count,
                        });
                        this.notify(`Open Bill #${res.order_id} (${res.table}) berhasil disimpan!`, 'success');
                    }

                    this.openBillSuccessModal = {
                        show: true,
                        orderId: res.order_id,
                        table: res.table,
                        total: res.total,
                        updated: Boolean(res.updated),
                    };

                    this.recalledOrderId = null;
                    this.clearCart();
                }
            } catch (e) { this.notify(e.message || 'Gagal menyimpan Open Bill', 'error'); }
            finally { this.paymentPending = false; }
        },

        printKitchenTicket(orderId) {
            if (!orderId) return;
            window.open(`/admin/api/orders/${orderId}/print?type=kitchen`, '_blank');
        },

        printTempBill(orderId) {
            if (!orderId) return;
            window.open(`/admin/api/orders/${orderId}/print?type=customer`, '_blank');
        },

        async cancelHeldBill(orderId) {
            if (!confirm(`Batalkan Open Bill #${orderId}?`)) return;
            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(`/admin/pos/orders/${orderId}/hold`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    }
                }).then(r => r.json());

                if (res.success) {
                    this.heldOrders = this.heldOrders.filter(o => o.id !== orderId);
                    if (this.recalledOrderId === orderId) {
                        this.recalledOrderId = null;
                        this.clearCart();
                    }
                    this.notify('Open Bill berhasil dibatalkan', 'success');
                } else {
                    this.notify(res.error || 'Gagal membatalkan Open Bill', 'error');
                }
            } catch (e) {
                this.notify('Gagal membatalkan Open Bill', 'error');
            }
        },

        async recallOrder(orderId) {
            try {
                const res = await this.post(`/admin/pos/orders/${orderId}/recall`, {});
                if (res.success) {
                    this.clearCart();
                    this.customerName = res.order.customer_name || '';
                    this.selectedTable = res.order.table || '';
                    this.fulfillmentType = res.order.fulfillment_type || 'DINE_IN';
                    if (res.order.discount_amount > 0 || res.order.voucher_code) {
                        this.discount = {
                            type: null,
                            value: 0,
                            amount: Number(res.order.discount_amount || 0),
                            reason: res.order.discount_reason || '',
                            code: res.order.voucher_code || null,
                            maxDiscount: null
                        };
                    }
                    res.order.items.forEach(i => {
                        this.cart.push({
                            id: i.id, name: i.name,
                            image: i.image || null,
                            variant_id: i.variant_id, variant: i.variant,
                            modifiers: i.modifiers, notes: i.notes || '',
                            showNotes: Boolean(i.notes),
                            qty: i.qty, price: i.price, subtotal: i.subtotal,
                            serving_type: i.serving_type || 'DINE_IN',
                        });
                    });
                    this.recalledOrderId = orderId;
                    this.heldOrders = this.heldOrders.filter(o => o.id !== orderId);
                    this.notify(`Open Bill #${orderId} (${res.order.table || 'Walk-in'}) dibuka`, 'success');
                }
            } catch (e) { this.notify('Gagal recall order', 'error'); }
        },

        // ── Payment ───────────────────────────
        openPayment() {
            if (this.cart.length === 0) return;
            this.paymentModal = {
                show: true,
                method: 'CASH',
                amountPaid: this.grandTotal,
                fulfillment: this.fulfillmentType || 'DINE_IN'
            };
        },
        canPay() {
            if (this.paymentModal.method === 'CASH') return Number(this.paymentModal.amountPaid) >= this.grandTotal;
            return true;
        },
        newCheckoutRequestId() {
            if (crypto.randomUUID) return crypto.randomUUID();
            const bytes = crypto.getRandomValues(new Uint8Array(16));
            bytes[6] = (bytes[6] & 15) | 64;
            bytes[8] = (bytes[8] & 63) | 128;
            const hex = Array.from(bytes, value => value.toString(16).padStart(2, '0')).join('');
            return `${hex.slice(0,8)}-${hex.slice(8,12)}-${hex.slice(12,16)}-${hex.slice(16,20)}-${hex.slice(20)}`;
        },
        async confirmPayment() {
            if (this.paymentPending || !this.canPay()) return;
            this.paymentPending = true;
            this.checkoutRequestId ??= this.newCheckoutRequestId();
            const amountPaid = this.paymentModal.method === 'CASH' ? Number(this.paymentModal.amountPaid) : this.grandTotal;
            const payload = {
                request_id: this.checkoutRequestId,
                recalled_order_id: this.recalledOrderId,
                items: this.cartPayload(),
                payment_method: this.paymentModal.method,
                amount_paid: amountPaid,
                fulfillment_type: this.paymentModal.fulfillment,
                customer_name: (this.customerName || '').trim() || null,
                table: this.paymentModal.fulfillment === 'DINE_IN' ? (this.selectedTable || null) : null,
                voucher_code: this.discount.code || null,
                discount_type: this.discount.type || null,
                discount_value: this.discount.value || 0,
                discount_amount: this.discountAmount,
                discount_reason: this.discount.reason || null,
            };
            try {
                const res = await this.post('{{ route('admin.pos.orders.submit') }}', payload);
                if (res.success) {
                    this.paymentModal.show = false;
                    this.successScreen = { show: true, total: res.total, change: res.change ?? 0, orderId: res.order_id };
                    this.fetchIncoming();
                }
            } catch (e) { this.notify(e.message || 'Gagal memproses pembayaran', 'error'); }
            finally { this.paymentPending = false; }
        },

        // ── Success / New Order ───────────────
        printReceipt(type = 'customer') {
            if (!this.successScreen.orderId) return;
            window.open(`/admin/api/orders/${this.successScreen.orderId}/print?type=${type}`, '_blank');
        },
        printAll() {
            if (!this.successScreen.orderId) return;
            window.open(`/admin/api/orders/${this.successScreen.orderId}/print?type=customer`, '_blank');
            setTimeout(() => {
                window.open(`/admin/api/orders/${this.successScreen.orderId}/print?type=kitchen`, '_blank');
            }, 500);
        },
        newOrder() {
            this.successScreen = { show: false, total: 0, change: 0, orderId: null };
            this.clearCart();
        },

        // ── Keyboard Shortcuts ────────────────
        handleKey(e) {
            if (['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) return;
            if (this.openBillConfirmModal.show) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.executeHoldCart();
                    return;
                }
                if (e.key === 'Escape') {
                    e.preventDefault();
                    this.openBillConfirmModal.show = false;
                    return;
                }
            }
            if (this.successScreen.show && (e.key === 'Enter' || e.key === 'Escape' || e.key === ' ')) {
                e.preventDefault();
                this.newOrder();
                return;
            }
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                this.$refs.searchInput?.focus();
                return;
            }
            switch (e.key) {
                case 'F1': e.preventDefault(); this.$refs.searchInput?.focus(); break;
                case 'F2': e.preventDefault(); this.confirmOpenBill(); break;
                case 'F3': e.preventDefault(); if (this.cart.length > 0 && this.discountSettings?.is_enabled) this.openDiscountModal(); break;
                case 'F4': e.preventDefault(); if (this.cart.length > 0) this.openPayment(); break;
                case 'F5': e.preventDefault(); if (this.paymentModal.show) { this.paymentModal.method='CASH'; this.$nextTick(() => this.$refs.cashInput?.focus()); } break;
                case 'F6': e.preventDefault(); if (this.paymentModal.show) this.paymentModal.method='QRIS'; break;
                case 'F7': e.preventDefault(); if (this.successScreen.show) this.printReceipt('customer'); break;
                case 'F8': e.preventDefault(); if (this.successScreen.show) this.printReceipt('kitchen'); break;
                case 'Escape': this.modifierModal.show = false; this.paymentModal.show = false; this.openBillConfirmModal.show = false; this.openBillSuccessModal.show = false; this.discountModal.show = false; break;
            }
        },

        // ── Helpers ───────────────────────────
        cartPayload() {
            return this.cart.map(i => ({
                id: i.id, qty: i.qty,
                variant_id: i.variant_id,
                modifiers: (i.modifiers ?? []).map(m => m.id),
                notes: (i.notes || '').trim() || null,
                serving_type: i.serving_type || 'DINE_IN',
            }));
        },
        async post(url, data) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(data),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.errors ? Object.values(json.errors).flat().join(' ') : json.message || 'Permintaan gagal. Silakan coba lagi.');
            return json;
        },
        async submitTableSetup() {
            if (!this.tableSetupModal.name) return;
            this.tableSetupModal.loading = true;
            try {
                const res = await fetch('/admin/api/table', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name: this.tableSetupModal.name })
                });
                const data = await res.json();
                if (data.success) {
                    this.tablesCount++;
                    const select = document.getElementById('pos-table-select');
                    if (select) {
                        const opt = document.createElement('option');
                        opt.value = this.tableSetupModal.name;
                        opt.textContent = this.tableSetupModal.name;
                        select.appendChild(opt);
                    }
                    this.selectedTable = this.tableSetupModal.name;
                    this.tableSetupModal.show = false;
                    this.tableSetupModal.name = '';
                    this.notify('Meja berhasil dibuat!', 'success');
                } else {
                    this.notify(data.message || 'Gagal membuat meja', 'error');
                }
            } catch (e) {
                this.notify('Terjadi kesalahan jaringan', 'error');
            } finally {
                this.tableSetupModal.loading = false;
            }
        },

        formatRp(n) { return Number(n ?? 0).toLocaleString('id-ID'); },
        notify(msg, type = 'success') {
            if (this.toast.timeout) clearTimeout(this.toast.timeout);
            this.toast.message = msg;
            this.toast.type = type;
            this.toast.show = true;
            this.toast.timeout = setTimeout(() => {
                this.toast.show = false;
            }, 3500);
        },
    };
}
</script>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODERN FLOATING TOAST NOTIFICATION                          --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="toast.show" x-cloak
     x-transition:enter="transition ease-out duration-250"
     x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
     x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
     class="fixed top-4 right-4 sm:top-5 sm:right-6 z-[9999] max-w-sm w-full pointer-events-auto">
    <div :class="toast.type === 'error' ? 'bg-[#fff5f5] border-red-200 text-red-900 shadow-[0_10px_30px_rgba(239,68,68,0.15)]' : 'bg-[#f0fdf4] border-emerald-200 text-emerald-900 shadow-[0_10px_30px_rgba(16,185,129,0.15)]'"
         class="border rounded-[14px] p-3 sm:p-3.5 shadow-xl flex items-start gap-3 backdrop-blur-md">
        <div :class="toast.type === 'error' ? 'bg-red-500 text-white' : 'bg-emerald-500 text-white'"
             class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 text-xs shadow-xs">
            <i :class="toast.type === 'error' ? 'fas fa-exclamation' : 'fas fa-check'"></i>
        </div>
        <div class="flex-1 min-w-0 pt-0.5">
            <p class="text-[12.5px] font-bold leading-tight" x-text="toast.type === 'error' ? 'Pemberitahuan' : 'Sukses'"></p>
            <p class="text-[11.5px] text-gray-700 mt-0.5 leading-snug break-words" x-text="toast.message"></p>
        </div>
        <button type="button" @click="toast.show = false" class="text-gray-400 hover:text-gray-600 text-xs p-1 cursor-pointer">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<x-order-notification />
<script>
    window.SHOP_ID = {{ $shop->id ?? 'null' }};
</script>
</div></div>
</body>
</html>
