<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Layar Dapur — {{ $shop->name ?? 'OQARI' }}</title>

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
        .sparta-kitchen { font-family: 'DM Sans', sans-serif; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-kitchen min-h-screen bg-[#f6f5f2] text-[#1e211f] antialiased" x-data="kitchenApp()" @refresh-live-orders.window="refresh()">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-[#f6f5f2]">

    {{-- TOP HEADER BAR (Sparta Style) --}}
    <header class="h-[52px] bg-[#f6f5f2] border-b border-[#e6e5e0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#1e211f] hover:bg-[#dfddd7] transition-colors md:hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-[13px] font-semibold text-[#1e211f]">Layar Dapur</span>
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[10.5px] font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] animate-pulse"></span> Auto-refresh 5s
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Topbar Datetime (Standardized Sparta Component) --}}
            <x-topbar-datetime />

            @can('record-attendance')
            <a href="{{ route('admin.attendance.index') }}" class="hidden md:inline-flex items-center h-[34px] px-[10px] rounded-[9px] bg-white border border-[#e6e5e0] hover:border-[#df5826] text-[11px] font-semibold text-[#1e211f] transition">
                Absensi
            </a>
            @endcan
        </div>
    </header>

    {{-- MAIN KITCHEN WORKSPACE --}}
    <main class="flex-1 w-full p-4 md:p-[24px] flex flex-col space-y-4">

        {{-- PAGE TITLE & SUMMARY PILLS --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold text-[#df5826] uppercase tracking-[0.22px]">Kitchen Display System</p>
                <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight mt-0.5">
                    Antrean Dapur
                </h1>
                <p class="text-[12px] text-[#7f837e] mt-0.5">
                    Pantau pesanan yang masuk dan perbarui proses masak secara langsung.
                </p>
            </div>

            {{-- Counter Pills --}}
            <div class="bg-white border border-[#e6e5e0] p-1.5 rounded-[12px] flex items-center gap-2 shadow-[0px_1px_2px_rgba(0,0,0,0.02)] self-start sm:self-auto overflow-x-auto hide-scroll">
                <div class="px-3 py-1 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] text-[11.5px] font-semibold text-[#df5826] flex items-center gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-[#df5826]"></span>
                    <span>Baru: </span>
                    <strong x-text="orders.filter(o => o.status === 'CONFIRMED').length">0</strong>
                </div>
                <div class="px-3 py-1 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] text-[11.5px] font-semibold text-[#df5826] flex items-center gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-[#df5826] animate-pulse"></span>
                    <span>Dimasak: </span>
                    <strong x-text="orders.filter(o => o.status === 'PREPARING').length">0</strong>
                </div>
                <div class="px-3 py-1 rounded-[8px] bg-[#eaf7ef] border border-[#bfe5cd] text-[11.5px] font-semibold text-[#289e5c] flex items-center gap-1.5 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-[#289e5c]"></span>
                    <span>Siap: </span>
                    <strong x-text="orders.filter(o => o.status === 'READY').length">0</strong>
                </div>
            </div>
        </div>

        {{-- KANBAN BOARD (3 COLUMNS) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5 flex-1 items-start">

            {{-- COLUMN 1: NEW / PESANAN BARU --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-4 flex flex-col shadow-[0px_2px_8px_rgba(0,0,0,0.02)] min-h-[480px]">
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#e6e5e0]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#df5826] animate-ping"></span>
                        <h2 class="text-[13.5px] font-bold text-[#1e211f] tracking-tight">Pesanan Baru</h2>
                    </div>
                    <span class="px-2 py-0.5 rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-bold"
                          x-text="orders.filter(o => o.status === 'CONFIRMED').length">0</span>
                </div>

                {{-- Order Cards --}}
                <div class="space-y-3 overflow-y-auto hide-scroll flex-1 pr-0.5">
                    <template x-for="order in orders.filter(o => o.status === 'CONFIRMED')" :key="order.id">
                        <div class="bg-white border border-[#d8dce2] hover:border-[#df5826] rounded-[14px] p-3.5 pl-[17px] relative overflow-hidden transition-all shadow-sm flex flex-col justify-between gap-3">
                            {{-- Left Accent Bar --}}
                            <div class="w-[3.5px] bg-[#df5826] rounded-l-[14px] absolute left-0 top-0 bottom-0"></div>

                            {{-- Card Header --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] font-bold text-[12px] flex items-center justify-center shrink-0"
                                         x-text="formatTableBadge(order.table)"></div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[13px] font-bold text-[#1e211f] truncate" x-text="formatTableTitle(order.table)"></span>
                                            <span class="text-[11px] font-semibold text-[#8b8e93]" x-text="'#' + order.id"></span>
                                        </div>
                                        <p class="text-[11px] text-[#53565a] truncate mt-0.5" x-text="order.customer || 'Walk-in Customer'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-[10.5px] font-medium text-[#8b8e93] shrink-0">
                                    <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3 h-3 opacity-75">
                                    <span x-text="order.time"></span>
                                </div>
                            </div>

                            {{-- Items Box --}}
                            {{-- Fulfillment Badge (Dine In vs Take Away) --}}
                            <div class="flex items-center gap-2">
                                <template x-if="order.type === 'Dine In' || order.fulfillment_type === 'DINE_IN'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eafbf2] border border-[#a6f0c6] text-[#00643b] text-[11px] font-bold">
                                        <i class="fas fa-mug-hot text-[11px]"></i>
                                        <span>Dine In · Gelas Saji / Keramik</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Takeaway' || order.fulfillment_type === 'TAKEAWAY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-bold">
                                        <i class="fas fa-bag-shopping text-[11px]"></i>
                                        <span>Take Away · Paper Cup &amp; Kantong</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Delivery' || order.fulfillment_type === 'DELIVERY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eff6ff] border border-[#bfdbfe] text-[#1d4ed8] text-[11px] font-bold">
                                        <i class="fas fa-truck text-[11px]"></i>
                                        <span>Delivery · Pengiriman</span>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-[#f6f5f2] border border-[#e6e5e0] rounded-[10px] p-2.5 space-y-2">
                                <template x-for="item in order.items" :key="item.id">
                                    <div class="text-[12px] text-[#1e211f]">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="font-bold text-[#df5826] shrink-0" x-text="item.qty + '×'"></span>
                                            <span class="font-semibold text-[#111827]" x-text="item.name"></span>
                                        </div>
                                        <p x-show="item.variant" class="text-[11px] text-[#53565a] pl-4 mt-0.5" x-text="'Varian: ' + item.variant"></p>
                                        <template x-for="mod in (item.modifiers ?? [])" :key="mod.id ?? mod.name">
                                            <p class="text-[11px] text-[#53565a] pl-4 mt-0.5" x-text="'• ' + mod.name"></p>
                                        </template>
                                        <div x-show="item.notes" class="mt-1 ml-4 bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] rounded px-2 py-0.5 text-[11px] font-medium inline-block whitespace-pre-wrap break-words"
                                             x-text="'Catatan: ' + item.notes"></div>
                                    </div>
                                </template>
                            </div>

                            {{-- Action CTA --}}
                            <button type="button" @click="advance(order.id, 'PREPARING')"
                                class="w-full py-2.5 px-4 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-semibold text-[12px] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-play text-[10px]"></i>
                                <span>Mulai Masak</span>
                            </button>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="orders.filter(o => o.status === 'CONFIRMED').length === 0"
                         class="border border-dashed border-[#d8dce2] rounded-[12px] p-6 text-center text-[#8b8e93]">
                        <div class="w-9 h-9 rounded-full bg-[#f8f9fa] border border-[#e6e5e0] flex items-center justify-center mx-auto mb-2 text-[#8b8e93]">
                            <i class="fas fa-inbox text-xs"></i>
                        </div>
                        <p class="text-[12px] font-semibold text-[#1e211f]">Tidak ada pesanan baru</p>
                        <p class="text-[11px] text-[#8b8e93] mt-0.5">Pesanan dari kasir atau QR akan otomatis masuk ke sini.</p>
                    </div>
                </div>
            </div>

            {{-- COLUMN 2: PREPARING / SEDANG DIMASAK --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-4 flex flex-col shadow-[0px_2px_8px_rgba(0,0,0,0.02)] min-h-[480px]">
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#e6e5e0]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#df5826] animate-pulse"></span>
                        <h2 class="text-[13.5px] font-bold text-[#1e211f] tracking-tight">Sedang Dimasak</h2>
                    </div>
                    <span class="px-2 py-0.5 rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-bold"
                          x-text="orders.filter(o => o.status === 'PREPARING').length">0</span>
                </div>

                {{-- Order Cards --}}
                <div class="space-y-3 overflow-y-auto hide-scroll flex-1 pr-0.5">
                    <template x-for="order in orders.filter(o => o.status === 'PREPARING')" :key="order.id">
                        <div class="bg-white border border-[#d8dce2] hover:border-[#df5826] rounded-[14px] p-3.5 pl-[17px] relative overflow-hidden transition-all shadow-sm flex flex-col justify-between gap-3">
                            {{-- Left Accent Bar --}}
                            <div class="w-[3.5px] bg-[#df5826] rounded-l-[14px] absolute left-0 top-0 bottom-0"></div>

                            {{-- Card Header --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] font-bold text-[12px] flex items-center justify-center shrink-0"
                                         x-text="formatTableBadge(order.table)"></div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[13px] font-bold text-[#1e211f] truncate" x-text="formatTableTitle(order.table)"></span>
                                            <span class="text-[11px] font-semibold text-[#8b8e93]" x-text="'#' + order.id"></span>
                                        </div>
                                        <p class="text-[11px] text-[#53565a] truncate mt-0.5" x-text="order.customer || 'Walk-in Customer'"></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-[10.5px] font-medium text-[#8b8e93] shrink-0">
                                    <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3 h-3 opacity-75">
                                    <span x-text="order.time"></span>
                                </div>
                            </div>

                            {{-- Items Box --}}
                            {{-- Fulfillment Badge (Dine In vs Take Away) --}}
                            <div class="flex items-center gap-2">
                                <template x-if="order.type === 'Dine In' || order.fulfillment_type === 'DINE_IN'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eafbf2] border border-[#a6f0c6] text-[#00643b] text-[11px] font-bold">
                                        <i class="fas fa-mug-hot text-[11px]"></i>
                                        <span>Dine In · Gelas Saji / Keramik</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Takeaway' || order.fulfillment_type === 'TAKEAWAY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-bold">
                                        <i class="fas fa-bag-shopping text-[11px]"></i>
                                        <span>Take Away · Paper Cup &amp; Kantong</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Delivery' || order.fulfillment_type === 'DELIVERY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eff6ff] border border-[#bfdbfe] text-[#1d4ed8] text-[11px] font-bold">
                                        <i class="fas fa-truck text-[11px]"></i>
                                        <span>Delivery · Pengiriman</span>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-[#f6f5f2] border border-[#e6e5e0] rounded-[10px] p-2.5 space-y-2">
                                <template x-for="item in order.items" :key="item.id">
                                    <div class="text-[12px] text-[#1e211f]">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="font-bold text-[#df5826] shrink-0" x-text="item.qty + '×'"></span>
                                            <span class="font-semibold text-[#111827]" x-text="item.name"></span>
                                        </div>
                                        <p x-show="item.variant" class="text-[11px] text-[#53565a] pl-4 mt-0.5" x-text="'Varian: ' + item.variant"></p>
                                        <template x-for="mod in (item.modifiers ?? [])" :key="mod.id ?? mod.name">
                                            <p class="text-[11px] text-[#53565a] pl-4 mt-0.5" x-text="'• ' + mod.name"></p>
                                        </template>
                                        <div x-show="item.notes" class="mt-1 ml-4 bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] rounded px-2 py-0.5 text-[11px] font-medium inline-block whitespace-pre-wrap break-words"
                                             x-text="'Catatan: ' + item.notes"></div>
                                    </div>
                                </template>
                            </div>

                            {{-- Action CTA --}}
                            <button type="button" @click="advance(order.id, 'READY')"
                                class="w-full py-2.5 px-4 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white font-semibold text-[12px] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                <img src="{{ asset('images/sparta/check-white.svg') }}" alt="" class="w-3.5 h-3.5">
                                <span>Tandai Siap</span>
                            </button>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="orders.filter(o => o.status === 'PREPARING').length === 0"
                         class="border border-dashed border-[#d8dce2] rounded-[12px] p-6 text-center text-[#8b8e93]">
                        <div class="w-9 h-9 rounded-full bg-[#f8f9fa] border border-[#e6e5e0] flex items-center justify-center mx-auto mb-2 text-[#8b8e93]">
                            <i class="fas fa-fire-burner text-xs"></i>
                        </div>
                        <p class="text-[12px] font-semibold text-[#1e211f]">Tidak ada menu sedang dimasak</p>
                        <p class="text-[11px] text-[#8b8e93] mt-0.5">Klik "Mulai Masak" pada kolom Pesanan Baru.</p>
                    </div>
                </div>
            </div>

            {{-- COLUMN 3: READY / SIAP DISAJIKAN --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-4 flex flex-col shadow-[0px_2px_8px_rgba(0,0,0,0.02)] min-h-[480px]">
                {{-- Column Header --}}
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#e6e5e0]">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#289e5c]"></span>
                        <h2 class="text-[13.5px] font-bold text-[#1e211f] tracking-tight">Siap Disajikan</h2>
                    </div>
                    <span class="px-2 py-0.5 rounded-[6px] bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] text-[11px] font-bold"
                          x-text="orders.filter(o => o.status === 'READY').length">0</span>
                </div>

                {{-- Order Cards --}}
                <div class="space-y-3 overflow-y-auto hide-scroll flex-1 pr-0.5">
                    <template x-for="order in orders.filter(o => o.status === 'READY')" :key="order.id">
                        <div class="bg-white border border-[#bfe5cd] rounded-[14px] p-3.5 pl-[17px] relative overflow-hidden transition-all shadow-sm flex flex-col justify-between gap-3">
                            {{-- Left Accent Bar --}}
                            <div class="w-[3.5px] bg-[#289e5c] rounded-l-[14px] absolute left-0 top-0 bottom-0"></div>

                            {{-- Card Header --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-[8px] bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] font-bold text-[12px] flex items-center justify-center shrink-0"
                                         x-text="formatTableBadge(order.table)"></div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[13px] font-bold text-[#1e211f] truncate" x-text="formatTableTitle(order.table)"></span>
                                            <span class="text-[11px] font-semibold text-[#8b8e93]" x-text="'#' + order.id"></span>
                                        </div>
                                        <p class="text-[11px] text-[#53565a] truncate mt-0.5" x-text="order.customer || 'Walk-in Customer'"></p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-[5px] bg-[#eaf7ef] text-[#289e5c] text-[10.5px] font-semibold">Siap</span>
                            </div>

                            {{-- Items Box --}}
                            {{-- Fulfillment Badge (Dine In vs Take Away) --}}
                            <div class="flex items-center gap-2">
                                <template x-if="order.type === 'Dine In' || order.fulfillment_type === 'DINE_IN'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eafbf2] border border-[#a6f0c6] text-[#00643b] text-[11px] font-bold">
                                        <i class="fas fa-mug-hot text-[11px]"></i>
                                        <span>Dine In · Gelas Saji / Keramik</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Takeaway' || order.fulfillment_type === 'TAKEAWAY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-bold">
                                        <i class="fas fa-bag-shopping text-[11px]"></i>
                                        <span>Take Away · Paper Cup &amp; Kantong</span>
                                    </div>
                                </template>
                                <template x-if="order.type === 'Delivery' || order.fulfillment_type === 'DELIVERY'">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[7px] bg-[#eff6ff] border border-[#bfdbfe] text-[#1d4ed8] text-[11px] font-bold">
                                        <i class="fas fa-truck text-[11px]"></i>
                                        <span>Delivery · Pengiriman</span>
                                    </div>
                                </template>
                            </div>

                            <div class="bg-[#f6f5f2] border border-[#e6e5e0] rounded-[10px] p-2.5 space-y-2">
                                <template x-for="item in order.items" :key="item.id">
                                    <div class="text-[12px] text-[#53565a]">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="font-semibold text-[#289e5c] shrink-0" x-text="item.qty + '×'"></span>
                                            <span class="font-medium text-[#111827]" x-text="item.name"></span>
                                        </div>
                                        <p x-show="item.variant" class="text-[11px] text-[#8b8e93] pl-4 mt-0.5" x-text="'Varian: ' + item.variant"></p>
                                        <div x-show="item.notes" class="mt-1 ml-4 bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] rounded px-2 py-0.5 text-[11px] font-medium inline-block whitespace-pre-wrap break-words"
                                             x-text="'Catatan: ' + item.notes"></div>
                                    </div>
                                </template>
                            </div>

                            {{-- Action CTA --}}
                            <button type="button" @click="advance(order.id, 'COMPLETED')"
                                class="w-full py-2.5 px-4 rounded-[9px] bg-[#289e5c] hover:bg-[#21854c] border border-[#289e5c] text-white font-semibold text-[12px] shadow-sm transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-check text-xs"></i>
                                <span>Selesai &amp; Diambil</span>
                            </button>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="orders.filter(o => o.status === 'READY').length === 0"
                         class="border border-dashed border-[#d8dce2] rounded-[12px] p-6 text-center text-[#8b8e93]">
                        <div class="w-9 h-9 rounded-full bg-[#f8f9fa] border border-[#e6e5e0] flex items-center justify-center mx-auto mb-2 text-[#8b8e93]">
                            <i class="fas fa-check-circle text-xs"></i>
                        </div>
                        <p class="text-[12px] font-semibold text-[#1e211f]">Tidak ada pesanan siap saji</p>
                        <p class="text-[11px] text-[#8b8e93] mt-0.5">Pesanan yang sudah matang akan tampil di sini untuk diambil.</p>
                    </div>
                </div>
            </div>

        </div>
    </main>

</div></div>

<script>
function kitchenApp() {
    return {
        orders: {{ Illuminate\Support\Js::from($kitchenOrders) }},
        currentTime: '',

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
            setInterval(() => this.refresh(), 5000);
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');
        },

        formatTableBadge(t) {
            const str = String(t || '').trim();
            if (!str || str === '-' || str.toUpperCase() === 'TA' || str.toLowerCase().includes('takeaway')) return 'TA';
            const digits = str.replace(/\D/g, '');
            return digits ? digits.padStart(2, '0').slice(-2) : 'TA';
        },

        formatTableTitle(t) {
            const str = String(t || '').trim();
            if (!str || str === '-' || str.toUpperCase() === 'TA' || str.toLowerCase().includes('takeaway')) return 'Take Away';
            if (str.toLowerCase().includes('delivery')) return 'Delivery';
            if (/^meja/i.test(str)) return str;
            return 'Meja ' + str.padStart(2, '0');
        },

        async refresh() {
            try {
                const res = await fetch('/admin/kitchen/orders', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.ok) {
                    this.orders = await res.json();
                }
            } catch(e) {
                // silent
            }
        },

        async advance(orderId, newStatus) {
            try {
                const res = await fetch(`/admin/kitchen/orders/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus }),
                });
                const data = await res.json();
                if (data.success) {
                    const order = this.orders.find(o => o.id === orderId);
                    if (order) {
                        if (newStatus === 'COMPLETED') {
                            this.orders = this.orders.filter(o => o.id !== orderId);
                        } else {
                            order.status = newStatus;
                        }
                    }
                }
            } catch(e) {
                alert('Gagal mengupdate status pesanan.');
            }
        },
    };
}
</script>

<x-order-notification />
<script>
    window.SHOP_ID = {{ Auth::user()->shop_id ?? 'null' }};
</script>
</body>
</html>
