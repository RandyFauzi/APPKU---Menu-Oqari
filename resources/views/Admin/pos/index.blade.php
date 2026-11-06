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
<body class="sparta-pos bg-[#f6f5f2] text-[#1e211f] min-h-screen w-full antialiased" x-data="posApp()" @keydown.window="handleKey($event)" @refresh-live-orders.window="fetchIncoming()">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-[#f6f5f2]">

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
<header class="h-[52px] bg-[#f6f5f2] border-b border-[#e6e5e0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
    <div class="flex items-center gap-3">
        <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
            class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#1e211f] hover:bg-[#dfddd7] transition-colors md:hidden">
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
        <div class="hidden md:flex items-center justify-between bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] w-[220px] px-[10px] rounded-[9px]">
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

        {{-- 1. ANTREAN STATUS SECTION (Figma 45:12194 - Collapsible for Compact Tablet Displays) --}}
        <section class="bg-white border border-[#e6e5e0] rounded-[14px] p-3 sm:p-3.5 flex flex-col gap-2.5 shadow-2xs">
            <div class="flex items-center justify-between">
                <button type="button" @click="isQueueExpanded = !isQueueExpanded" 
                        class="flex items-center gap-2 group cursor-pointer text-left focus:outline-none select-none">
                    <div class="text-[13px] font-bold text-[#1e211f] flex items-center gap-1.5">
                        <span>Antrean Dapur</span>
                        <span class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full"
                              :class="activeOrders.length > 0 ? 'bg-[#fff6f0] text-[#df5826] border border-[#f5d5c6]' : 'bg-gray-100 text-gray-500'"
                              x-text="activeOrders.length">0</span>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-[#df5826] transition-transform duration-200"
                       :class="isQueueExpanded ? 'rotate-180' : ''"></i>
                </button>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#df5826] hover:opacity-80 transition">
                    <span>Semua Pesanan</span>
                    <img src="{{ asset('images/sparta/arrow-right.svg') }}" alt="" class="w-[12px] h-[12px]">
                </a>
            </div>

            <div x-show="isQueueExpanded" x-cloak class="space-y-2">
                {{-- Empty State when no active orders (Compact single-line) --}}
                <div x-show="activeOrders.length === 0" x-cloak class="bg-[#f8f9fa] border border-dashed border-[#e6e5e0] rounded-[9px] py-2 px-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-[#fff6f0] text-[#df5826] flex items-center justify-center text-[9px] font-bold shrink-0">
                            <i class="fas fa-check"></i>
                        </div>
                        <p class="text-[11.5px] text-[#53565a]">Belum ada antrean pesanan aktif di dapur.</p>
                    </div>
                </div>

                {{-- Active Queue Cards (Up to 3 cards horizontally, matching Figma 45:12206) --}}
                <div x-show="activeOrders.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
                    <template x-for="(order, oIdx) in activeOrders.slice(0, 3)" :key="order.id">
                        <div class="bg-[#f6f5f2] border border-[#e6e5e0] rounded-[10px] p-2.5 pl-3.5 relative overflow-hidden flex flex-col gap-2">
                            <div class="w-[3px] bg-[#df5826] rounded-l-[10px] absolute left-0 top-0 bottom-0"></div>

                            {{-- Top row: badge + table/title + status pill --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-[26px] h-[26px] rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center shrink-0">
                                        <span class="text-[10.5px] font-bold text-[#df5826]" x-text="getOrderBadge(order, oIdx)"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[12px] font-bold text-[#1e211f] leading-tight truncate" x-text="formatOrderTitle(order)"></p>
                                        <div class="flex items-center gap-1 text-[10.5px] text-[#8b8e93] leading-tight mt-0.5">
                                            <span class="truncate" x-text="getOrderItemCount(order) + ' item'"></span>
                                            <span>•</span>
                                            <span class="truncate" x-text="'Rp' + formatRp(order.total)"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="bg-[#fff6f0] px-1.5 py-0.5 rounded-[4px] text-[9.5px] font-semibold text-[#df5826] shrink-0" x-text="getOrderStatusLabel(order)"></span>
                            </div>

                            {{-- Items summary row --}}
                            <div class="text-[10.5px] font-medium text-[#1e211f] leading-snug truncate">
                                <span x-text="formatOrderItemsPreview(order)"></span>
                                <template x-if="(order.items || []).length > 2">
                                    <span class="text-[#df5826] font-medium" x-text="' +' + (order.items.length - 2) + ' item'"></span>
                                </template>
                            </div>

                            {{-- Progress bar & timestamp --}}
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-[4px] bg-[#e6e5e0] rounded-[2px] overflow-hidden">
                                    <div class="h-full bg-[#df5826] rounded-[2px] transition-all duration-300" :style="'width: ' + getOrderProgress(order) + '%'"></div>
                                </div>
                                <div class="flex items-center gap-1 text-[10px] font-medium text-[#53565a] shrink-0">
                                    <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-[10px] h-[10px]">
                                    <span x-text="order.time || '--:--'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- 2. OPEN BILL / HELD ORDERS RECALL BAR --}}
        <template x-if="heldOrders.length > 0">
            <section class="bg-[#fffbf5] border border-amber-200/90 rounded-[14px] px-[14px] py-[10px] flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 shadow-2xs">
                <div class="flex items-center gap-2 shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-[11.5px] font-black text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-bookmark text-amber-600 text-xs"></i>
                        <span>Tagihan Aktif / Open Bill</span>
                    </span>
                    <span class="bg-amber-100 text-amber-800 border border-amber-300 font-bold px-2 py-0.2 rounded-full text-[10px]" x-text="heldOrders.length + ' Meja'"></span>
                </div>
                <div class="flex items-center gap-2 overflow-x-auto hide-scroll py-0.5">
                    <template x-for="held in heldOrders" :key="held.id">
                        <div class="shrink-0 bg-white border border-amber-200 hover:border-amber-400 rounded-[10px] pl-2.5 pr-1.5 py-1 text-[11.5px] flex items-center gap-2 transition shadow-xs hover:shadow-sm group">
                            <div class="flex items-center gap-1.5 cursor-pointer" @click="recallOrder(held.id)" title="Buka tagihan untuk bayar / tambah menu">
                                <span class="font-black text-[#df5826] bg-[#fff6f0] px-1.5 py-0.5 rounded text-[10.5px] border border-[#f5d5c6]" x-text="held.table || ('#' + held.id)"></span>
                                <span class="font-bold text-[#1e211f] max-w-[90px] truncate" x-text="held.customer_name"></span>
                                <span class="font-bold text-amber-900">Rp<span x-text="formatRp(held.total)"></span></span>
                            </div>
                            <div class="flex items-center gap-0.5 border-l border-gray-200 pl-1.5">
                                <button type="button" @click.stop="printKitchenTicket(held.id)" class="w-6 h-6 rounded-md hover:bg-emerald-50 text-emerald-600 flex items-center justify-center transition text-[10.5px]" title="Cetak Ulang Tiket Dapur">
                                    <i class="fas fa-utensils"></i>
                                </button>
                                <button type="button" @click.stop="printTempBill(held.id)" class="w-6 h-6 rounded-md hover:bg-blue-50 text-blue-600 flex items-center justify-center transition text-[10.5px]" title="Cetak Bill Sementara (Proforma)">
                                    <i class="fas fa-receipt"></i>
                                </button>
                                <button type="button" @click.stop="cancelHeldBill(held.id)" class="w-6 h-6 rounded-md hover:bg-red-50 text-red-500 flex items-center justify-center transition text-[10.5px]" title="Batalkan Tagihan Meja">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </template>

        {{-- 3. MENU CATALOG SECTION (Figma 45:12312) --}}
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
                            'image_url' => $product->image_url,
                            'category_id' => $product->category_id,
                            'category_name' => $product->category_name ?? ($product->category?->name ?? 'Menu'),
                            'sold_count' => (int) ($product->sold_count ?? 0),
                            'variants' => $product->variants,
                            'modifier_groups' => $product->modifierGroups,
                        ];
                    @endphp
                    <button type="button"
                        x-show="matchesFilter({{ $product->category_id ?? 'null' }}, '{{ addslashes($product->name) }}', {{ (int) ($product->sold_count ?? 0) }}, {{ $product->is_best_seller ? 'true' : 'false' }})"
                        @click="handleProductClick({{ Illuminate\Support\Js::from($productPayload) }})"
                        class="product-card group bg-white border border-[#d8dce2] hover:border-[#df5826] rounded-[12px] pb-[8px] flex flex-col justify-between text-left shadow-[0px_4px_12px_0px_rgba(17,24,39,0.03)] focus:outline-none cursor-pointer">
                        {{-- Product Image --}}
                        <div class="bg-[#ede7dc] h-[120px] sm:h-[128px] xl:h-[135px] w-full rounded-[11px] overflow-hidden relative">
                            @if($product->is_best_seller)
                                <span class="absolute top-2 left-2 z-10 bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                    <i class="fas fa-crown text-[8px]"></i> Best
                                </span>
                            @endif
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-[#f3efe6] to-[#e6dfd1] text-[#8b8e93]">
                                    <i class="fas fa-mug-hot text-2xl text-[#df5826]/60 mb-1"></i>
                                    <span class="text-[10px] font-semibold uppercase tracking-wider text-[#8b8e93]">{{ $product->category_name ?? 'Menu' }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Product Name + Dark Plus Button --}}
                        <div class="px-[10px] pt-[8px] pb-[4px] flex items-center justify-between gap-2 w-full">
                            <span class="text-[13px] font-semibold text-[#1e211f] leading-[15.6px] truncate">{{ $product->name }}</span>
                            <span class="w-[22px] h-[22px] rounded-full bg-[#111827] flex items-center justify-center shrink-0">
                                <img src="{{ asset('images/sparta/pos-plus.svg') }}" alt="+" class="w-[14px] h-[14px]">
                            </span>
                        </div>

                        {{-- Category • Sold Count & Price --}}
                        <div class="px-[10px] flex items-center justify-between gap-2 w-full">
                            <div class="flex items-center gap-[5px] text-[11px] font-normal text-[#53565a] leading-[16.5px] truncate">
                                <span class="truncate">{{ $product->category_name ?? ($product->category?->name ?? 'Menu') }}</span>
                                <span>•</span>
                                <span>{{ (int) ($product->sold_count ?? 0) }} sold</span>
                            </div>
                            <span class="text-[13px] font-semibold text-[#111827] leading-[19.5px] shrink-0">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </button>
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
        <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-[#f6f5f2] border-b border-[#e6e5e0] shrink-0">
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
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="text-[9.5px] font-bold text-[#f04c17] uppercase tracking-wider bg-[#fff6f0] px-1.5 py-0.5 rounded border border-[#f5d5c6]">ORDER</span>
                        <h2 class="text-[13px] font-bold text-[#111827] truncate">
                            <span>Pesanan Baru</span>
                            <span x-show="recalledOrderId" x-cloak class="text-[#df5826]">#<span x-text="recalledOrderId"></span></span>
                        </h2>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0" x-show="cart.length > 0" x-cloak>
                    <button type="button" @click="holdCart()" class="px-2 py-0.5 rounded-[6px] border border-amber-300 bg-amber-50 hover:bg-amber-100 text-[10.5px] font-bold text-amber-900 transition cursor-pointer flex items-center gap-1 shadow-2xs" :title="recalledOrderId ? 'Perbarui Open Bill (F2)' : 'Simpan ke Open Bill / Bayar Nanti (F2)'">
                        <i class="fas fa-bookmark text-[9.5px] text-amber-600"></i>
                        <span x-text="recalledOrderId ? 'Update Bill' : 'Open Bill'"></span>
                    </button>
                    <button type="button" @click="clearCart()" class="px-2 py-0.5 rounded-[6px] border border-[#f5d5c6] bg-[#fff6f0] text-[10.5px] font-semibold text-[#df5826] hover:opacity-80 transition cursor-pointer">
                        Reset
                    </button>
                </div>
            </div>

            {{-- Compact Fulfillment Segmented Control --}}
            <div class="bg-[#f8f9fa] p-[2px] rounded-[9px] border border-[#e6e5e0] grid grid-cols-3 gap-[2px]">
                <button type="button" @click="fulfillmentType = 'DINE_IN'; paymentModal.fulfillment = 'DINE_IN'"
                    :class="fulfillmentType === 'DINE_IN' ? 'bg-white text-[#df5826] font-bold shadow-xs border border-[#e6e5e0]' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                    class="py-1 px-1.5 rounded-[7px] text-[11px] leading-tight text-center transition flex items-center justify-center gap-1 cursor-pointer">
                    <i class="fas fa-utensils text-[9px]"></i>
                    <span>Dine-in</span>
                </button>
                <button type="button" @click="fulfillmentType = 'TAKEAWAY'; paymentModal.fulfillment = 'TAKEAWAY'"
                    :class="fulfillmentType === 'TAKEAWAY' ? 'bg-white text-[#df5826] font-bold shadow-xs border border-[#e6e5e0]' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                    class="py-1 px-1.5 rounded-[7px] text-[11px] leading-tight text-center transition flex items-center justify-center gap-1 cursor-pointer">
                    <i class="fas fa-shopping-bag text-[9px]"></i>
                    <span>Takeaway</span>
                </button>
                <button type="button" @click="fulfillmentType = 'DELIVERY'; paymentModal.fulfillment = 'DELIVERY'"
                    :class="fulfillmentType === 'DELIVERY' ? 'bg-white text-[#df5826] font-bold shadow-xs border border-[#e6e5e0]' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                    class="py-1 px-1.5 rounded-[7px] text-[11px] leading-tight text-center transition flex items-center justify-center gap-1 cursor-pointer">
                    <i class="fas fa-motorcycle text-[9px]"></i>
                    <span>Delivery</span>
                </button>
            </div>

            {{-- Compact Single-Row Customer Name & Table Number --}}
            <div class="flex items-center gap-1.5">
                <div class="relative flex-1 min-w-0">
                    <i class="fas fa-user absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                    <input id="pos-customer-name" 
                           type="text" 
                           x-model="customerName" 
                           placeholder="Pelanggan (Walk-in)"
                           class="w-full bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white focus:ring-2 focus:ring-[#df5826]/15 rounded-[8px] pl-7 pr-6 py-1.5 text-[11.5px] font-semibold text-[#111827] placeholder-[#8b8e93] outline-none transition">
                    <button type="button" x-show="customerName" x-cloak @click="customerName = ''"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 hover:text-gray-600 p-0.5 cursor-pointer"
                            title="Reset nama pelanggan">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>

                <div class="relative w-[125px] shrink-0" x-show="fulfillmentType === 'DINE_IN'">
                    <i class="fas fa-chair absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] text-gray-400 pointer-events-none"></i>
                    <select id="pos-table-select" 
                            x-model="selectedTable"
                            class="w-full appearance-none bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white focus:ring-2 focus:ring-[#df5826]/15 rounded-[8px] pl-7 pr-6 py-1.5 text-[11.5px] font-semibold text-[#111827] outline-none transition truncate cursor-pointer">
                        <option value="">Pilih Meja</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->name }}">{{ $table->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-gray-400 pointer-events-none"></i>
                </div>

                <button type="button" 
                        x-show="fulfillmentType === 'DINE_IN'"
                        @click="tableSetupModal.show = true" 
                        class="text-[10px] font-bold text-[#df5826] hover:bg-[#fff6f0] px-1.5 py-1.5 rounded-[6px] border border-transparent hover:border-[#f5d5c6] transition shrink-0 cursor-pointer" 
                        title="Tambah Meja Baru">
                    +Meja
                </button>
            </div>
        </div>

        {{-- Cart Items List (Streamlined for Tablet & Medium Displays) --}}
        <div class="p-2.5 sm:p-3 flex-1 min-h-0 overflow-y-auto flex flex-col gap-2 overscroll-contain pb-3">
            <template x-if="cart.length === 0">
                <div class="py-10 px-4 text-center flex flex-col items-center justify-center border border-dashed border-[#d8dce2] rounded-[10px] bg-[#f8f9fa]/60">
                    <div class="w-9 h-9 rounded-full bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center mb-2">
                        <img src="{{ asset('images/sparta/pos-doc-add.svg') }}" alt="" class="w-3.5 h-3.5">
                    </div>
                    <p class="text-[12px] font-semibold text-[#111827]">Belum ada item dipilih</p>
                    <p class="text-[10.5px] text-[#8b8e93] mt-0.5">Pilih menu di samping untuk menambahkan ke pesanan.</p>
                </div>
            </template>

            <template x-for="(item, idx) in cart" :key="idx">
                <div class="bg-white border border-[#d8dce2] rounded-[10px] p-2.5 flex flex-col gap-1.5 shadow-2xs">
                    {{-- Top Item Row: Thumbnail + Name + Red Trash --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="w-[28px] h-[28px] rounded-[6px] bg-[#ede7dc] overflow-hidden shrink-0 flex items-center justify-center">
                            <template x-if="item.image">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!item.image">
                                <span class="text-[10px] font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                            </template>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[12.5px] font-semibold text-[#111827] leading-tight truncate" x-text="item.name"></p>
                            <p x-show="item.variant" x-cloak class="text-[10.5px] text-[#53565a] truncate" x-text="item.variant"></p>
                            <p x-show="item.modifiers && item.modifiers.length > 0" x-cloak
                               class="text-[10.5px] text-[#53565a] truncate"
                               x-text="item.modifiers.map(m => m.name).join(' · ')"></p>
                        </div>

                        <button type="button" @click="removeItem(idx)" class="w-[20px] h-[20px] flex items-center justify-center shrink-0 hover:opacity-75 transition cursor-pointer" title="Hapus item">
                            <img src="{{ asset('images/sparta/pos-trash-red.svg') }}" alt="Hapus" class="w-[14px] h-[14px]">
                        </button>
                    </div>

                    {{-- Collapsed Customer Note Trigger (Figma 45:12598) --}}
                    <button type="button"
                        x-show="!item.showNotes && !item.notes"
                        @click="item.showNotes = true; $nextTick(() => document.getElementById('item-notes-' + idx)?.focus())"
                        class="flex items-center gap-1 text-left w-fit hover:opacity-80 transition cursor-pointer py-0.5">
                        <img src="{{ asset('images/sparta/pos-doc-add.svg') }}" alt="" class="w-[12px] h-[12px]">
                        <span class="text-[10.5px] font-semibold text-[#df5826]">Buat Catatan Customer</span>
                    </button>

                    {{-- Expanded Customer Note Box (Figma 45:12546) --}}
                    <div x-show="item.showNotes || item.notes" class="bg-[#f8f9fa] rounded-[8px] pt-2 pb-1.5 px-2 flex flex-col gap-1.5">
                        <div class="relative border border-[#d8dce2] rounded-[6px] p-1.5 bg-[#f8f9fa]">
                            <label :for="'item-notes-' + idx" class="text-[10px] font-medium text-[#111827] block mb-0.5">
                                Catatan customer (Opsional)
                            </label>
                            <textarea :id="'item-notes-' + idx" x-model="item.notes" maxlength="255" rows="2" :disabled="paymentPending"
                                :aria-describedby="'item-notes-help-' + idx" placeholder="Contoh: tanpa gula, es sedikit"
                                class="w-full bg-transparent border-none outline-none text-[10.5px] text-[#111827] placeholder-[#8b8e93] resize-none p-0 focus:ring-0"></textarea>
                            <span :id="'item-notes-help-' + idx" class="sr-only">Berlaku untuk semua jumlah pada baris ini.</span>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" @click="item.notes = ''; item.showNotes = false" :disabled="paymentPending"
                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition disabled:opacity-50 cursor-pointer">
                                <img src="{{ asset('images/sparta/pos-trash-dark.svg') }}" alt="" class="w-[12px] h-[12px] shrink-0">
                                <span class="text-[10.5px] font-semibold text-[#111827] truncate">Hapus</span>
                            </button>
                            <button type="button" @click="splitItem(idx)" :disabled="paymentPending || item.qty <= 1 || cart.length >= 50"
                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition disabled:opacity-50 cursor-pointer">
                                <img src="{{ asset('images/sparta/pos-split.svg') }}" alt="" class="w-[12px] h-[12px] shrink-0">
                                <span class="text-[10.5px] font-semibold text-[#111827] truncate">Pisahkan 1</span>
                            </button>
                        </div>
                    </div>

                    {{-- Price & Stepper Row --}}
                    <div class="flex items-center justify-between pt-0.5">
                        <div>
                            <span class="text-[12.5px] font-bold text-[#111827] leading-tight">
                                Rp<span x-text="formatRp(item.subtotal)"></span>
                            </span>
                            <span x-show="item.qty > 1" class="block text-[10px] text-[#8b8e93]">
                                @ Rp<span x-text="formatRp(item.price)"></span>
                            </span>
                        </div>

                        <div class="bg-[#f8f9fa] border border-[#d8dce2] rounded-full p-[2px] px-1 flex items-center gap-1 shadow-2xs">
                            <button type="button" @click="changeQty(idx, -1)"
                                class="w-[26px] h-[26px] rounded-full flex items-center justify-center hover:bg-gray-200 active:scale-90 transition cursor-pointer"
                                aria-label="Kurangi jumlah">
                                <img src="{{ asset('images/sparta/pos-minus.svg') }}" alt="-" class="w-[12px] h-[12px]">
                            </button>
                            <span class="w-[18px] text-center text-[12px] font-bold text-[#111827] leading-tight select-none" x-text="item.qty"></span>
                            <button type="button" @click="changeQty(idx, 1)"
                                class="w-[26px] h-[26px] rounded-full bg-[#111827] hover:bg-[#df5826] active:scale-90 flex items-center justify-center transition shadow-2xs cursor-pointer"
                                aria-label="Tambah jumlah">
                                <img src="{{ asset('images/sparta/pos-plus.svg') }}" alt="+" class="w-[12px] h-[12px]">
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- Order Summary & CTA Footer with Pull-Tab Accordion --}}
        <div class="border-t border-[#e6e5e0] bg-[#fcfcfd] flex flex-col shrink-0 sticky bottom-0 z-10 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] pb-[max(14px,env(safe-area-inset-bottom))]">
            
            {{-- Pull Tab Handle & Total Summary Header --}}
            <div @click="showPriceBreakdown = !showPriceBreakdown" 
                 class="px-4 pt-2.5 pb-2 cursor-pointer hover:bg-gray-100/70 select-none transition-colors group">
                
                {{-- Centered Drag / Pull Pill Handle --}}
                <div class="w-10 h-1 bg-[#d8dce2] group-hover:bg-[#df5826]/70 rounded-full mx-auto mb-2 transition-colors"></div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[12.5px] font-bold text-[#111827]">Total</span>
                        <button type="button" 
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#df5826] bg-[#fff6f0] hover:bg-[#ffede3] px-2 py-0.5 rounded-full border border-[#f5d5c6] transition">
                            <span x-text="showPriceBreakdown ? 'Tutup Rincian' : 'Rincian Harga'"></span>
                            <i class="fas fa-chevron-up text-[9px] transition-transform duration-200" :class="showPriceBreakdown ? 'rotate-180' : ''"></i>
                        </button>
                    </div>
                    <span class="text-[16px] font-black text-[#111827] tracking-tight">
                        Rp<span x-text="formatRp(subtotal)"></span>
                    </span>
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
                <div class="flex items-center justify-between">
                    <span class="text-[#53565a]">Pajak Resto (0%)</span>
                    <span class="font-medium text-[#111827]">Rp0</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[#53565a]">Biaya Layanan</span>
                    <span class="font-medium text-[#53565a]">—</span>
                </div>
            </div>

            {{-- CTA Button (Open Bill F2 & Bayar F4) --}}
            <div class="px-4 pt-1.5 pb-2">
                @can('manage-payments')
                <div class="flex gap-2">
                    <button type="button" @click="holdCart()" :disabled="cart.length === 0"
                        class="py-[11px] px-3.5 rounded-[10px] bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 text-[12px] font-bold transition flex items-center justify-center gap-1.5 shrink-0 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer shadow-xs"
                        :title="recalledOrderId ? 'Perbarui Open Bill (F2)' : 'Buka Tagihan Meja / Bayar Nanti (F2)'">
                        <i class="fas fa-bookmark text-amber-700"></i>
                        <span x-text="recalledOrderId ? 'Update Bill' : 'Open Bill'"></span>
                        <span class="text-[10px] font-normal opacity-75">(F2)</span>
                    </button>
                    <button type="button" @click="openPayment()" :disabled="cart.length === 0"
                        class="flex-1 py-[11px] px-[16px] rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_2px_6px_0px_rgba(223,88,38,0.25)] flex items-center justify-center gap-[8px] text-white text-[13px] font-bold hover:opacity-95 active:scale-[0.99] transition disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                        <img src="{{ asset('images/sparta/cart-btn-icon.svg') }}" alt="" class="w-[15px] h-[15px]">
                        <span class="flex items-center gap-1.5">
                            <span>Bayar</span>
                            <span class="hidden sm:inline-block text-[10.5px] font-normal opacity-85">(F4)</span>
                        </span>
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
                <p class="text-[#df5826] font-bold text-[13px]">Rp<span x-text="formatRp(modalTotal)"></span></p>
            </div>
            <button type="button" @click="modifierModal.show = false" class="w-8 h-8 rounded-full bg-[#f8f9fa] border border-[#d8dce2] flex items-center justify-center text-[#53565a] hover:bg-gray-200">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-5 hide-scroll">
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
            </div>
            <p class="text-[22px] font-bold text-[#df5826]">Rp<span x-text="formatRp(subtotal)"></span></p>
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
                    <button type="button" @click="paymentModal.method = m; paymentModal.amountPaid = m === 'CASH' ? subtotal : subtotal"
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
                    <button type="button" @click="paymentModal.amountPaid = subtotal"
                        class="text-[11px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] hover:bg-[#ffece0] px-2.5 py-1 rounded-[7px] font-bold transition cursor-pointer">
                        Uang Pas
                    </button>
                    <template x-for="quick in smartQuickCash" :key="quick">
                    <button type="button" @click="paymentModal.amountPaid = quick"
                        class="text-[11px] bg-[#f8f9fa] hover:bg-[#fff6f0] border border-[#d8dce2] hover:border-[#df5826] text-[#53565a] hover:text-[#df5826] px-2.5 py-1 rounded-[7px] font-semibold transition cursor-pointer"
                        x-text="'Rp' + formatRp(quick)"></button>
                    </template>
                </div>
                <div x-show="paymentModal.amountPaid >= subtotal" x-cloak class="mt-3 bg-[#eaf7ef] border border-[#bfe5cd] rounded-[10px] p-3 flex items-center justify-between">
                    <span class="text-[12px] text-[#1e211f] font-medium">Kembalian</span>
                    <span class="text-[15px] font-bold text-[#289e5c]">Rp<span x-text="formatRp(paymentModal.amountPaid - subtotal)"></span></span>
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
{{-- OPEN BILL SUCCESS MODAL                                     --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="openBillSuccessModal.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-[#1e211f]/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-[#e6e5e0] rounded-[24px] p-6 max-w-sm w-full text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3.5">
            <i class="fas fa-bookmark text-2xl"></i>
        </div>
        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200"
              x-text="openBillSuccessModal.updated ? 'Open Bill Diperbarui' : 'Open Bill Tersimpan'"></span>
        
        <h3 class="text-[20px] font-bold text-[#111827] mt-2 mb-1" x-text="openBillSuccessModal.table"></h3>
        <p class="text-[12.5px] text-[#53565a] mb-4">
            Tagihan Sementara: <strong class="text-[#df5826] text-[15px]">Rp<span x-text="formatRp(openBillSuccessModal.total)"></span></strong>
        </p>

        <div class="space-y-2 mt-2">
            <button type="button" @click="printKitchenTicket(openBillSuccessModal.orderId); openBillSuccessModal.show = false"
                class="w-full py-2.5 px-4 bg-[#059669] hover:bg-[#047857] text-white rounded-[10px] text-[12px] font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fas fa-utensils"></i> Cetak Tiket Dapur / Bar
            </button>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="printTempBill(openBillSuccessModal.orderId); openBillSuccessModal.show = false"
                    class="py-2.5 px-3 bg-[#f8f9fa] hover:bg-gray-100 border border-[#d8dce2] text-[#111827] rounded-[10px] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-receipt text-[#df5826]"></i> Bill Sementara
                </button>
                <button type="button" @click="openBillSuccessModal.show = false"
                    class="py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-[#53565a] rounded-[10px] text-[11.5px] font-semibold transition">
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
        openBillSuccessModal: { show: false, orderId: null, table: '', total: 0, updated: false },
        tableSetupModal: { show: false, name: '', loading: false },

        quickCash: [10000, 20000, 50000, 100000],

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

            let formattedTime = o.time || '--:--';
            if ((!o.time || o.time === '--:--') && o.created_at) {
                const dt = new Date(o.created_at);
                if (!isNaN(dt.getTime())) {
                    formattedTime = dt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');
                }
            }

            return {
                id: o.id,
                table: tableName || (o.fulfillment_type === 'TAKEAWAY' ? 'Takeaway' : (o.fulfillment_type === 'DELIVERY' ? 'Delivery' : 'Tanpa Meja')),
                customer_name: o.customer_name || o.customer || 'Walk-in Customer',
                total: Number(o.total ?? o.grand_total ?? o.total_price ?? 0),
                status: o.status || o.order_status || 'CONFIRMED',
                order_status: o.order_status || o.status || 'CONFIRMED',
                fulfillment_type: o.fulfillment_type || 'DINE_IN',
                time: formattedTime,
                items: Array.isArray(o.items) ? o.items.map(i => ({
                    name: i.name || i.product_name || i.product?.name || 'Menu',
                    qty: Number(i.qty ?? i.quantity ?? 1),
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

        // ── Queue Formatting Helpers ──────────
        getOrderBadge(order, idx) {
            if (order && order.table) {
                const rawTable = typeof order.table === 'object' ? (order.table.name || '') : String(order.table);
                const digits = rawTable.replace(/\D/g, '');
                if (digits) return digits.padStart(2, '0').slice(-2);
            }
            return String((idx ?? 0) + 1).padStart(2, '0');
        },
        formatOrderTitle(order) {
            if (!order) return 'Pesanan';
            const rawTable = order.table && typeof order.table === 'object' ? (order.table.name || '') : String(order.table || '');
            const t = rawTable.trim();
            if (!t || t === 'Tanpa Meja') {
                return order.customer_name && order.customer_name !== 'Walk-in Customer'
                    ? order.customer_name
                    : 'Pesanan #' + order.id;
            }
            if (/^meja/i.test(t) || /^takeaway/i.test(t) || /^delivery/i.test(t)) {
                return t;
            }
            return 'Meja ' + t.padStart(2, '0');
        },
        getOrderItemCount(order) {
            if (!order || !Array.isArray(order.items)) return 0;
            return order.items.reduce((sum, item) => sum + Number(item.qty ?? item.quantity ?? 1), 0);
        },
        getOrderStatusLabel(order) {
            const st = order?.status || order?.order_status || '';
            if (st === 'Diproses' || st === 'PREPARING') return 'Memasak';
            if (st === 'Siap' || st === 'READY') return 'Siap';
            return 'Memasak';
        },
        getOrderProgress(order) {
            const st = order?.status || order?.order_status || '';
            if (st === 'Siap' || st === 'READY') return 90;
            if (st === 'Diproses' || st === 'PREPARING') return 68;
            return 45;
        },
        formatOrderItemsPreview(order) {
            if (!order || !Array.isArray(order.items) || order.items.length === 0) {
                return 'Tidak ada detail item';
            }
            return order.items
                .slice(0, 2)
                .map(i => `${i.name || i.product_name || i.product?.name || 'Menu'} ×${i.qty ?? i.quantity ?? 1}`)
                .join(', ');
        },

        // ── Computed ──────────────────────────
        get subtotal() {
            return this.cart.reduce((s, i) => s + i.subtotal, 0);
        },
        get modalTotal() {
            if (!this.modifierModal.product) return 0;
            let base = Number(this.modifierModal.product.price);
            if (this.modifierModal.selectedVariant) base += Number(this.modifierModal.selectedVariant.price_adjustment || 0);
            this.modifierModal.selectedModifiers.forEach(m => base += Number(m.price_adjustment || 0));
            return base;
        },
        get smartQuickCash() {
            const tot = Number(this.subtotal) || 0;
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
        handleProductClick(product) {
            const hasVariants = product.variants && product.variants.length > 0;
            const hasModifiers = product.modifier_groups && product.modifier_groups.length > 0;

            if (hasVariants || hasModifiers) {
                this.openModifier(product);
            } else {
                this.addToCartDirectly(product);
            }
        },
        addToCartDirectly(p) {
            const existing = this.cart.find(c => c.id === p.id && !c.variant_id && (!c.modifiers || c.modifiers.length === 0) && !c.notes);
            if (existing) {
                existing.qty++;
                existing.subtotal = existing.price * existing.qty;
            } else {
                this.cart.push({
                    id: p.id, name: p.name,
                    image: p.image_url || null,
                    variant_id: null, variant: null,
                    modifiers: [], notes: '', showNotes: false, qty: 1, price: Number(p.price),
                    subtotal: Number(p.price),
                });
            }
        },
        openModifier(product) {
            this.modifierModal = {
                show: true, product, selectedVariant: null, selectedVariantId: null,
                selectedModifiers: [], qty: 1, notes: ''
            };
            if (product.variants?.length === 1) this.selectVariant(product.variants[0]);
        },
        selectVariant(v) { this.modifierModal.selectedVariant = v; },
        isModifierSelected(id) { return this.modifierModal.selectedModifiers.some(m => m.id === id); },
        toggleModifier(mod) {
            const idx = this.modifierModal.selectedModifiers.findIndex(m => m.id === mod.id);
            idx === -1 ? this.modifierModal.selectedModifiers.push(mod) : this.modifierModal.selectedModifiers.splice(idx, 1);
        },

        addToCart() {
            const p = this.modifierModal.product;
            const qty = this.modifierModal.qty;
            const variant = this.modifierModal.selectedVariant;
            const mods = [...this.modifierModal.selectedModifiers];
            const notes = this.modifierModal.notes.trim();
            const price = this.modalTotal;

            this.cart.push({
                id: p.id, name: p.name,
                image: p.image_url || null,
                variant_id: variant?.id ?? null,
                variant: variant?.name ?? null,
                modifiers: mods,
                notes, showNotes: Boolean(notes), qty, price,
                subtotal: price * qty,
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
        },

        // ── Open Bill (Hold / Recall) ─────────────────────
        async holdCart() {
            if (this.cart.length === 0) return;
            const payload = {
                items: this.cartPayload(),
                table: this.fulfillmentType === 'DINE_IN' ? (this.selectedTable || null) : null,
                fulfillment_type: this.fulfillmentType || 'DINE_IN',
                customer_name: (this.customerName || '').trim() || null,
                recalled_order_id: this.recalledOrderId || null,
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
                    res.order.items.forEach(i => {
                        this.cart.push({
                            id: i.id, name: i.name,
                            image: i.image || null,
                            variant_id: i.variant_id, variant: i.variant,
                            modifiers: i.modifiers, notes: i.notes || '',
                            showNotes: Boolean(i.notes),
                            qty: i.qty, price: i.price, subtotal: i.subtotal,
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
                amountPaid: this.subtotal,
                fulfillment: this.fulfillmentType || 'DINE_IN'
            };
        },
        canPay() {
            if (this.paymentModal.method === 'CASH') return Number(this.paymentModal.amountPaid) >= this.subtotal;
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
            const amountPaid = this.paymentModal.method === 'CASH' ? Number(this.paymentModal.amountPaid) : this.subtotal;
            const payload = {
                request_id: this.checkoutRequestId,
                recalled_order_id: this.recalledOrderId,
                items: this.cartPayload(),
                payment_method: this.paymentModal.method,
                amount_paid: amountPaid,
                fulfillment_type: this.paymentModal.fulfillment,
                customer_name: (this.customerName || '').trim() || null,
                table: this.paymentModal.fulfillment === 'DINE_IN' ? (this.selectedTable || null) : null,
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
                case 'F2': e.preventDefault(); this.holdCart(); break;
                case 'F3': e.preventDefault(); break;
                case 'F4': e.preventDefault(); if (this.cart.length > 0) this.openPayment(); break;
                case 'F5': e.preventDefault(); if (this.paymentModal.show) { this.paymentModal.method='CASH'; this.$nextTick(() => this.$refs.cashInput?.focus()); } break;
                case 'F6': e.preventDefault(); if (this.paymentModal.show) this.paymentModal.method='QRIS'; break;
                case 'F7': e.preventDefault(); if (this.successScreen.show) this.printReceipt('customer'); break;
                case 'F8': e.preventDefault(); if (this.successScreen.show) this.printReceipt('kitchen'); break;
                case 'Escape': this.modifierModal.show = false; this.paymentModal.show = false; break;
            }
        },

        // ── Helpers ───────────────────────────
        cartPayload() {
            return this.cart.map(i => ({
                id: i.id, qty: i.qty,
                variant_id: i.variant_id,
                modifiers: (i.modifiers ?? []).map(m => m.id),
                notes: (i.notes || '').trim() || null,
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
