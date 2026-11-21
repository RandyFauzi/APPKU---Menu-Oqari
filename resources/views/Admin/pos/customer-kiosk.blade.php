<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu Pelanggan (Touchscreen) — {{ $shop->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'DM Sans', sans-serif; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .kiosk-card:active { transform: scale(0.97); }
        button { touch-action: manipulation; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="bg-[#f8f9fa] text-[#1e211f] h-[100dvh] w-full overflow-hidden antialiased select-none flex flex-col"
      x-data="customerKioskApp({{ (int) $shop->id }})">

    {{-- Top Header --}}
    <header class="h-[56px] bg-white border-b border-[#e6e5e0] px-3 sm:px-5 flex items-center justify-between gap-2 shrink-0">
        <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
            @if(!empty($shop->logo_url))
                <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-8 h-8 sm:w-9 sm:h-9 rounded-[10px] object-cover border border-[#e6e5e0] shrink-0">
            @else
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-[10px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center shrink-0">
                    <i class="fas fa-mug-hot text-[#df5826] text-sm sm:text-base"></i>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-[14px] sm:text-[15px] font-bold text-[#111827] truncate leading-tight">{{ $shop->name }}</h1>
                <p class="text-[10.5px] sm:text-[11px] text-[#71717a] truncate leading-tight">Pilih Menu Mandiri (Touchscreen)</p>
            </div>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-2 sm:px-2.5 py-1 rounded-full text-[10.5px] sm:text-[11px] font-semibold"
                  :class="isConnected ? 'bg-[#eaf7ef] text-[#289e5c]' : 'bg-amber-50 text-amber-700'">
                <span class="w-2 h-2 rounded-full shrink-0" :class="isConnected ? 'bg-[#289e5c] animate-pulse' : 'bg-amber-500'"></span>
                <span class="hidden sm:inline" x-text="syncLabel"></span>
                <span class="sm:hidden" x-text="isConnected ? 'Aktif' : '...'"></span>
            </span>

            <a href="{{ route('admin.pos.customer-display') }}"
               class="h-8 sm:h-9 px-2.5 sm:px-3 rounded-[10px] bg-[#f8f9fa] hover:bg-gray-200 border border-[#e6e5e0] text-[11px] sm:text-[11.5px] font-semibold text-[#53565a] inline-flex items-center gap-1.5 transition"
               title="Beralih ke Mode Display (Non-Touch)">
                <i class="fas fa-tv text-[#df5826]"></i>
                <span class="hidden sm:inline">Mode Display</span>
            </a>

            <button type="button" @click="toggleFullscreen()"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-[10px] bg-[#f8f9fa] hover:bg-gray-200 border border-[#e6e5e0] text-[#53565a] flex items-center justify-center transition cursor-pointer"
                    title="Layar Penuh">
                <i class="fas text-xs sm:text-sm" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
            </button>
        </div>
    </header>

    {{-- Main Workspace: Side-by-side from md (768px+), Full-height Catalog + Bottom Bar on < md --}}
    <main class="flex-1 min-h-0 p-2.5 sm:p-4 flex flex-col md:flex-row gap-3 sm:gap-4 relative">

        {{-- Left: Search, Categories & Product Catalog --}}
        <section class="flex-1 min-w-0 min-h-0 bg-white border border-[#e6e5e0] rounded-[16px] sm:rounded-[18px] p-3 sm:p-4 flex flex-col overflow-hidden shadow-2xs">
            {{-- Search & Category Filter --}}
            <div class="pb-2.5 sm:pb-3 border-b border-[#e6e5e0] flex flex-col lg:flex-row items-stretch lg:items-center gap-2.5 shrink-0">
                <div class="w-full lg:w-[220px] xl:w-[250px] bg-[#f8f9fa] border border-[#d8dce2] focus-within:border-[#df5826] rounded-[10px] px-3 py-2 flex items-center gap-2 shrink-0">
                    <i class="fas fa-search text-[#8b8e93] text-xs"></i>
                    <input type="text" x-model="search" placeholder="Cari menu favorit..."
                           class="w-full bg-transparent border-none outline-none text-[12.5px] sm:text-[13px] font-medium text-[#1e211f] placeholder-[#8b8e93] p-0 focus:ring-0">
                    <button type="button" x-show="search" x-cloak @click="search = ''"
                            class="w-5 h-5 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-[10px] cursor-pointer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="flex-1 min-w-0 flex items-center gap-1.5 sm:gap-2 overflow-x-auto hide-scroll py-0.5">
                    <button type="button" @click="activeCategory = null"
                            :class="activeCategory === null ? 'bg-[#df5826] text-white border-[#df5826]' : 'bg-[#f8f9fa] text-[#53565a] border-[#e6e5e0] hover:border-[#df5826]/50'"
                            class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-[10px] border text-[11.5px] sm:text-[12px] font-semibold whitespace-nowrap transition cursor-pointer shrink-0">
                        Semua Menu
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" @click="activeCategory = {{ (int) $cat['id'] }}"
                                :class="activeCategory === {{ (int) $cat['id'] }} ? 'bg-[#df5826] text-white border-[#df5826]' : 'bg-[#f8f9fa] text-[#53565a] border-[#e6e5e0] hover:border-[#df5826]/50'"
                                class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-[10px] border text-[11.5px] sm:text-[12px] font-semibold whitespace-nowrap transition cursor-pointer shrink-0">
                            {{ $cat['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="flex-1 min-h-0 overflow-y-auto pt-3 sm:pt-4 hide-scroll pb-16 md:pb-2">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5 sm:gap-3.5">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <button type="button"
                                @click="handleProductTap(product)"
                                :disabled="product.is_sold_out"
                                :class="product.is_sold_out ? 'opacity-60 cursor-not-allowed bg-gray-50' : 'bg-white hover:border-[#df5826]/60 hover:shadow-sm cursor-pointer kiosk-card'"
                                class="border border-[#e6e5e0] rounded-[14px] sm:rounded-[16px] overflow-hidden flex flex-col text-left transition-all">
                            <div class="relative h-[95px] sm:h-[115px] w-full bg-[#f3f4f6] overflow-hidden shrink-0">
                                <template x-if="product.image_url">
                                    <img :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!product.image_url">
                                    <div class="w-full h-full flex items-center justify-center text-[#c4c6ca]">
                                        <i class="fas fa-mug-hot text-2xl"></i>
                                    </div>
                                </template>
                                <template x-if="product.is_sold_out">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded bg-red-600 text-white text-[10px] font-bold">Habis</span>
                                </template>
                                <template x-if="!product.is_sold_out && product.is_best_seller">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded bg-[#df5826] text-white text-[10px] font-bold">Favorit</span>
                                </template>
                            </div>
                            <div class="p-2.5 sm:p-3 flex-1 flex flex-col justify-between gap-1.5 w-full">
                                <h3 class="text-[12.5px] sm:text-[13.5px] font-bold text-[#111827] leading-snug line-clamp-2" x-text="product.name"></h3>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[12.5px] sm:text-[13.5px] font-bold text-[#df5826]" x-text="'Rp ' + formatRp(product.effective_price)"></span>
                                    <span class="w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-[#fff6f0] text-[#df5826] flex items-center justify-center text-[10px] sm:text-xs shrink-0">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="filteredProducts.length === 0" x-cloak class="py-12 text-center text-[#8b8e93]">
                    <i class="fas fa-search text-2xl mb-2 opacity-50"></i>
                    <p class="text-[13px] font-medium">Menu tidak ditemukan</p>
                </div>
            </div>
        </section>

        {{-- Mobile Floating Cart Bar (< md) --}}
        <div class="md:hidden fixed bottom-3 inset-x-3 z-30">
            <button type="button" @click="showMobileCart = true"
                    class="w-full bg-[#111827] text-white rounded-[14px] px-4 py-3 shadow-xl flex items-center justify-between gap-3 cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-[9px] bg-[#df5826] flex items-center justify-center text-xs font-bold"
                          x-text="totalItemsCount"></span>
                    <div class="text-left">
                        <div class="text-[12.5px] font-bold leading-tight">Lihat Pesanan</div>
                        <div class="text-[10.5px] text-gray-300 leading-tight" x-text="totalItemsCount > 0 ? 'Otomatis masuk ke kasir' : 'Keranjang masih kosong'"></div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[14px] font-bold text-[#fb923c]" x-text="'Rp ' + formatRp(state.grand_total)"></span>
                    <i class="fas fa-chevron-up text-xs text-gray-400"></i>
                </div>
            </button>
        </div>

        {{-- Mobile Backdrop (< md) --}}
        <div x-show="showMobileCart" x-cloak x-transition.opacity @click="showMobileCart = false"
             class="md:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-xs"></div>

        {{-- Right: Live Synced Cart (Sidebar on md+, Slide-up Drawer on < md) --}}
        <aside :class="showMobileCart ? 'translate-y-0' : 'translate-y-full md:translate-y-0'"
               class="fixed inset-x-0 bottom-0 z-50 h-[82dvh] md:static md:h-auto md:z-auto w-full md:w-[295px] lg:w-[345px] xl:w-[390px] bg-white border border-[#e6e5e0] rounded-t-[20px] md:rounded-[18px] flex flex-col overflow-hidden shrink-0 shadow-2xl md:shadow-2xs transition-transform duration-250 ease-out">
            <div class="px-4 sm:px-5 py-3.5 sm:py-4 border-b border-[#e6e5e0] flex items-center justify-between shrink-0">
                <div>
                    <h2 class="text-[15px] sm:text-[16px] font-bold text-[#111827]">Pesanan Anda</h2>
                    <p class="text-[11px] sm:text-[11.5px] text-[#71717a]" x-text="totalItemsCount + ' item • Otomatis terkirim ke kasir'"></p>
                </div>
                <div class="flex items-center gap-1.5">
                    <button type="button" x-show="state.items.length > 0 && state.status === 'ordering'" x-cloak
                            @click="clearCart()"
                            class="text-[11px] font-semibold text-red-600 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition cursor-pointer">
                        Kosongkan
                    </button>
                    <button type="button" @click="showMobileCart = false"
                            class="md:hidden w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center cursor-pointer">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 min-h-0 overflow-y-auto p-3.5 sm:p-4 space-y-2.5 hide-scroll">
                <template x-if="state.items.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-center px-4 py-8 text-[#8b8e93]">
                        <div class="w-12 h-12 rounded-full bg-[#f8f9fa] border border-[#e6e5e0] flex items-center justify-center mb-3">
                            <i class="fas fa-hand-pointer text-lg text-[#df5826]"></i>
                        </div>
                        <p class="text-[13.5px] font-bold text-[#111827]">Sentuh Menu untuk Memesan</p>
                        <p class="text-[11.5px] mt-1">Menu yang Anda pilih akan langsung muncul di sini dan di layar kasir.</p>
                    </div>
                </template>

                <template x-for="(item, idx) in state.items" :key="idx">
                    <div class="bg-[#f8f9fa] border border-[#e6e5e0] rounded-[12px] sm:rounded-[14px] p-2.5 sm:p-3 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h4 class="text-[13px] sm:text-[13.5px] font-bold text-[#111827] leading-snug" x-text="item.name"></h4>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <template x-if="item.variant">
                                        <span class="text-[10.5px] px-1.5 py-0.5 rounded bg-white border border-[#e6e5e0] text-[#53565a]" x-text="item.variant"></span>
                                    </template>
                                    <template x-for="(mod, mIdx) in (item.modifiers || [])" :key="mIdx">
                                        <span class="text-[10.5px] px-1.5 py-0.5 rounded bg-white border border-[#e6e5e0] text-[#53565a]" x-text="'+ ' + mod"></span>
                                    </template>
                                </div>
                            </div>
                            <span class="text-[13px] sm:text-[13.5px] font-bold text-[#111827] shrink-0" x-text="'Rp ' + formatRp(item.subtotal)"></span>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-[11px] text-[#71717a]" x-text="'@ Rp ' + formatRp(item.price)"></span>
                            <div class="flex items-center gap-2">
                                <button type="button" @click="updateQty(idx, -1)"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-[8px] sm:rounded-[9px] bg-white border border-[#d8dce2] hover:border-[#df5826] text-[#111827] font-bold flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-minus text-[10px] sm:text-xs"></i>
                                </button>
                                <span class="w-5 text-center text-[13px] font-bold text-[#111827]" x-text="item.qty"></span>
                                <button type="button" @click="updateQty(idx, 1)"
                                        class="w-7 h-7 sm:w-8 sm:h-8 rounded-[8px] sm:rounded-[9px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] font-bold flex items-center justify-center cursor-pointer">
                                    <i class="fas fa-plus text-[10px] sm:text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Summary Footer --}}
            <div class="p-3.5 sm:p-4 border-t border-[#e6e5e0] bg-[#fcfcfd] space-y-2 shrink-0">
                <div class="flex items-center justify-between text-[12.5px] sm:text-[13px] text-[#53565a]">
                    <span>Subtotal</span>
                    <span class="font-semibold text-[#111827]" x-text="'Rp ' + formatRp(state.subtotal)"></span>
                </div>

                <template x-if="Number(state.discount_amount) > 0">
                    <div class="flex items-center justify-between text-[12.5px] sm:text-[13px] text-[#289e5c]">
                        <span x-text="state.discount_label || 'Diskon'"></span>
                        <span class="font-bold" x-text="'- Rp ' + formatRp(state.discount_amount)"></span>
                    </div>
                </template>

                <div class="pt-2 border-t border-dashed border-[#d8dce2] flex items-baseline justify-between">
                    <span class="text-[13.5px] sm:text-[14px] font-bold text-[#111827]">Total Bayar</span>
                    <span class="text-[22px] sm:text-[24px] font-bold text-[#df5826]" x-text="'Rp ' + formatRp(state.grand_total)"></span>
                </div>

                <div x-show="state.items.length > 0" x-cloak
                     class="bg-[#fff6f0] border border-[#f5d5c6] rounded-[10px] px-3 py-2 text-center text-[11px] sm:text-[11.5px] font-semibold text-[#b84218]">
                    <i class="fas fa-cash-register mr-1"></i>
                    Pesanan sudah masuk ke Kasir — silakan bayar di kasir
                </div>
            </div>
        </aside>
    </main>

    {{-- Variant / Modifier Modal --}}
    <div x-show="modifierModal.show" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
         @click.self="modifierModal.show = false">
        <div class="bg-white border border-[#e6e5e0] rounded-[20px] shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[88dvh]">
            <div class="px-5 py-4 border-b border-[#e6e5e0] flex items-center justify-between">
                <h3 class="text-[16px] font-bold text-[#111827]" x-text="modifierModal.product?.name"></h3>
                <button type="button" @click="modifierModal.show = false" class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center cursor-pointer">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-5 overflow-y-auto space-y-4">
                {{-- Variants --}}
                <template x-if="modifierModal.product?.variants?.length > 0">
                    <div>
                        <p class="text-[12px] font-bold text-[#53565a] uppercase tracking-wider mb-2">Pilih Varian</p>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="v in modifierModal.product.variants" :key="v.id">
                                <button type="button" @click="modifierModal.selectedVariant = v"
                                        :class="modifierModal.selectedVariant?.id === v.id ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826]' : 'border-[#d8dce2] bg-white text-[#111827]'"
                                        class="p-3 rounded-[12px] border text-left transition cursor-pointer">
                                    <div class="text-[13px] font-bold" x-text="v.name"></div>
                                    <div class="text-[11.5px] opacity-80" x-text="'Rp ' + formatRp(v.price)"></div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Modifier Groups --}}
                <template x-for="group in (modifierModal.product?.modifier_groups || [])" :key="group.id">
                    <div>
                        <p class="text-[12px] font-bold text-[#53565a] uppercase tracking-wider mb-2" x-text="group.name"></p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="mod in (group.modifiers || [])" :key="mod.id">
                                <button type="button" @click="toggleModifier(group, mod)"
                                        :class="isModifierSelected(mod.id) ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826]' : 'border-[#d8dce2] bg-white text-[#53565a]'"
                                        class="px-3 py-2 rounded-[10px] border text-[12.5px] font-semibold transition cursor-pointer">
                                    <span x-text="mod.name"></span>
                                    <template x-if="Number(mod.price) > 0">
                                        <span class="ml-1 text-[11px]" x-text="'+Rp ' + formatRp(mod.price)"></span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-4 border-t border-[#e6e5e0] bg-[#fcfcfd] flex items-center gap-3">
                <button type="button" @click="confirmModifierAdd()"
                        class="w-full py-3.5 rounded-[12px] bg-[#df5826] hover:bg-[#c84d20] text-white font-bold text-[14px] transition cursor-pointer">
                    Tambahkan ke Pesanan
                </button>
            </div>
        </div>
    </div>

    {{-- Payment / Paid Overlay when Cashier triggers Checkout --}}
    <div x-show="state.status === 'payment' || state.status === 'paid'" x-cloak x-transition.opacity
         class="fixed inset-0 z-50 bg-black/55 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white border border-[#e6e5e0] rounded-[24px] shadow-2xl w-full max-w-md p-6 sm:p-7 text-center">
            <template x-if="state.status === 'payment'">
                <div>
                    <div class="w-16 h-16 rounded-2xl bg-[#fff6f0] border border-[#f5d5c6] mx-auto flex items-center justify-center mb-4">
                        <i class="fas text-3xl text-[#df5826]"
                           :class="state.payment_method === 'QRIS' ? 'fa-qrcode' : (state.payment_method === 'CARD' ? 'fa-credit-card' : 'fa-money-bill-wave')"></i>
                    </div>
                    <h2 class="text-[20px] font-bold text-[#111827]">
                        Pembayaran <span x-text="state.payment_method || 'TUNAI'"></span>
                    </h2>
                    <p class="text-[13px] text-[#71717a] mt-1">Total yang harus dibayar:</p>
                    <p class="text-[32px] sm:text-[34px] font-bold text-[#df5826] my-3" x-text="'Rp ' + formatRp(state.grand_total)"></p>
                    <p class="text-[12px] text-[#53565a]">
                        <span x-show="state.payment_method === 'QRIS'">Silakan scan QRIS di meja kasir</span>
                        <span x-show="state.payment_method !== 'QRIS'">Menunggu konfirmasi kasir...</span>
                    </p>
                </div>
            </template>

            <template x-if="state.status === 'paid'">
                <div>
                    <div class="w-16 h-16 rounded-full bg-[#eaf7ef] border border-[#bfe5cd] mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-check text-3xl text-[#289e5c]"></i>
                    </div>
                    <h2 class="text-[22px] font-bold text-[#111827]">Pembayaran Berhasil!</h2>
                    <p class="text-[13px] text-[#71717a] mt-1">Terima kasih telah memesan di {{ $shop->name }}</p>
                    <div class="mt-5 bg-[#f8f9fa] border border-[#e6e5e0] rounded-[14px] p-4 space-y-2">
                        <div class="flex justify-between text-[14px]">
                            <span class="text-[#53565a]">Total</span>
                            <span class="font-bold text-[#111827]" x-text="'Rp ' + formatRp(state.grand_total)"></span>
                        </div>
                        <template x-if="Number(state.change) > 0">
                            <div class="flex justify-between text-[15px] pt-2 border-t border-dashed border-[#d8dce2]">
                                <span class="text-[#53565a] font-semibold">Kembalian</span>
                                <span class="font-bold text-[#df5826]" x-text="'Rp ' + formatRp(state.change)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <script>
        function customerKioskApp(shopId) {
            return {
                shopId,
                isConnected: true,
                syncMode: 'BroadcastChannel',
                isFullscreen: false,
                showMobileCart: false,
                search: '',
                activeCategory: null,
                channel: null,
                hasLocalCashier: false,
                lastTapAt: 0,
                lastTapProductId: null,
                paidResetTimer: null,
                products: {{ Illuminate\Support\Js::from($products->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category_id' => $p->category_id,
                    'image_url' => $p->image_url,
                    'effective_price' => (float) ($p->effective_price ?? $p->price),
                    'price' => (float) $p->price,
                    'has_active_discount' => (bool) ($p->has_active_discount ?? false),
                    'discount_percentage' => $p->discount_percentage,
                    'is_best_seller' => (bool) $p->is_best_seller,
                    'is_sold_out' => (bool) $p->is_sold_out,
                    'variants' => $p->variants->map(fn($v) => ['id' => $v->id, 'name' => $v->name, 'price' => (float) $v->price])->values(),
                    'modifier_groups' => $p->modifierGroups->map(fn($g) => [
                        'id' => $g->id,
                        'name' => $g->name,
                        'selection_type' => $g->selection_type,
                        'max_selections' => $g->max_selections,
                        'modifiers' => $g->modifiers->map(fn($m) => ['id' => $m->id, 'name' => $m->name, 'price' => (float) $m->price])->values(),
                    ])->values(),
                ])) }},
                modifierModal: { show: false, product: null, selectedVariant: null, selectedModifiers: [] },
                state: {
                    status: 'idle',
                    items: [],
                    subtotal: 0,
                    discount_amount: 0,
                    discount_label: '',
                    grand_total: 0,
                    payment_method: 'CASH',
                    change: 0,
                },

                get filteredProducts() {
                    const q = this.search.trim().toLowerCase();
                    return this.products.filter(p => {
                        const matchCat = this.activeCategory === null || Number(p.category_id) === Number(this.activeCategory);
                        const matchSearch = !q || p.name.toLowerCase().includes(q);
                        return matchCat && matchSearch;
                    });
                },

                get totalItemsCount() {
                    return (this.state.items || []).reduce((s, i) => s + Number(i.qty || 1), 0);
                },

                get syncLabel() {
                    return this.isConnected ? `Terhubung (${this.syncMode})` : 'Menghubungkan...';
                },

                init() {
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = Boolean(document.fullscreenElement);
                    });

                    try {
                        const saved = localStorage.getItem(`oqari_pos_cfd_state_${this.shopId}`);
                        if (saved) {
                            this.applyState(JSON.parse(saved));
                        }
                    } catch (e) {}

                    if ('BroadcastChannel' in window) {
                        this.channel = new BroadcastChannel(`oqari_pos_cfd_${this.shopId}`);
                        this.channel.onmessage = (event) => {
                            if (!event.data) return;
                            if (event.data.type === 'CFD_SYNC' || event.data.type === 'CFD_ACK') {
                                this.hasLocalCashier = true;
                                this.syncMode = 'Dual Screen';
                                this.isConnected = true;
                                if (event.data.type === 'CFD_SYNC') {
                                    this.applyState(event.data.payload);
                                }
                            }
                        };
                        this.channel.postMessage({ type: 'CFD_HELLO' });
                    }

                    this.$nextTick(() => {
                        if (window.Echo) {
                            window.Echo.private(`shop.${this.shopId}.pos-display`)
                                .listen('.pos.display.updated', (payload) => {
                                    this.syncMode = 'Realtime Cloud';
                                    this.isConnected = true;
                                    this.applyState(payload);
                                });
                        }
                    });
                },

                applyState(payload) {
                    if (!payload || typeof payload !== 'object') return;
                    clearTimeout(this.paidResetTimer);
                    this.state = {
                        ...this.state,
                        ...payload,
                        items: Array.isArray(payload.items) ? payload.items : [],
                    };
                    if (this.state.status === 'paid') {
                        this.showMobileCart = false;
                        this.paidResetTimer = setTimeout(() => {
                            this.state.status = 'idle';
                            this.state.items = [];
                        }, 10000);
                    }
                },

                handleProductTap(product) {
                    if (product.is_sold_out) return;

                    const now = Date.now();
                    if (this.lastTapProductId === product.id && (now - this.lastTapAt) < 250) {
                        return;
                    }
                    this.lastTapProductId = product.id;
                    this.lastTapAt = now;

                    const hasVariants = product.variants && product.variants.length > 0;
                    const hasModifiers = product.modifier_groups && product.modifier_groups.length > 0;

                    if (hasVariants || hasModifiers) {
                        const initialMods = [];
                        (product.modifier_groups || []).forEach(g => {
                            if ((g.selection_type === 'SINGLE' || g.max_selections === 1) && (g.modifiers || []).length > 0) {
                                initialMods.push(g.modifiers[0]);
                            }
                        });
                        this.modifierModal = {
                            show: true,
                            product,
                            selectedVariant: hasVariants ? product.variants[0] : null,
                            selectedModifiers: initialMods,
                        };
                        return;
                    }

                    this.sendCustomerAction('add_item', {
                        item: {
                            id: product.id,
                            name: product.name,
                            image: product.image_url || null,
                            variant_id: null,
                            variant: null,
                            modifiers: [],
                            notes: '',
                            qty: 1,
                            price: Number(product.effective_price),
                            regular_price: Number(product.price),
                            has_discount: Boolean(product.has_active_discount),
                            discount_percentage: product.discount_percentage || null,
                            subtotal: Number(product.effective_price),
                            serving_type: 'DINE_IN',
                        }
                    });
                },

                isModifierSelected(id) {
                    return this.modifierModal.selectedModifiers.some(m => m.id === id);
                },

                toggleModifier(group, mod) {
                    if (group.selection_type === 'SINGLE' || group.max_selections === 1) {
                        const ids = (group.modifiers || []).map(m => m.id);
                        this.modifierModal.selectedModifiers = this.modifierModal.selectedModifiers.filter(m => !ids.includes(m.id));
                        this.modifierModal.selectedModifiers.push(mod);
                    } else {
                        const idx = this.modifierModal.selectedModifiers.findIndex(m => m.id === mod.id);
                        idx === -1 ? this.modifierModal.selectedModifiers.push(mod) : this.modifierModal.selectedModifiers.splice(idx, 1);
                    }
                },

                confirmModifierAdd() {
                    const p = this.modifierModal.product;
                    if (!p) return;
                    const v = this.modifierModal.selectedVariant;
                    const mods = this.modifierModal.selectedModifiers || [];
                    const basePrice = v ? Number(v.price) : Number(p.effective_price);
                    const modTotal = mods.reduce((s, m) => s + Number(m.price || 0), 0);
                    const unitPrice = basePrice + modTotal;

                    this.sendCustomerAction('add_item', {
                        item: {
                            id: p.id,
                            name: p.name,
                            image: p.image_url || null,
                            variant_id: v ? v.id : null,
                            variant: v ? v.name : null,
                            modifiers: mods.map(m => ({ id: m.id, name: m.name, price: Number(m.price || 0) })),
                            notes: '',
                            qty: 1,
                            price: unitPrice,
                            regular_price: unitPrice,
                            has_discount: false,
                            discount_percentage: null,
                            subtotal: unitPrice,
                            serving_type: 'DINE_IN',
                        }
                    });
                    this.modifierModal.show = false;
                },

                updateQty(index, delta) {
                    this.sendCustomerAction('update_qty', { index, delta });
                },

                clearCart() {
                    this.sendCustomerAction('clear_cart', {});
                },

                async sendCustomerAction(action, data = {}) {
                    const actionId = (window.crypto && typeof window.crypto.randomUUID === 'function')
                        ? window.crypto.randomUUID()
                        : `${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
                    const payload = { action_id: actionId, action, ...data };

                    if (this.channel && this.hasLocalCashier) {
                        this.channel.postMessage({ type: 'CFD_CUSTOMER_ACTION', payload });
                        return;
                    }

                    if (this.channel) {
                        this.channel.postMessage({ type: 'CFD_CUSTOMER_ACTION', payload });
                    }

                    if (window.Echo) {
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            await fetch('{{ route('admin.pos.customer-kiosk.action') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token || '',
                                },
                                body: JSON.stringify(payload),
                            });
                        } catch (e) {}
                    }
                },

                formatRp(val) {
                    return Number(val || 0).toLocaleString('id-ID');
                },

                async toggleFullscreen() {
                    try {
                        if (!document.fullscreenElement) {
                            await document.documentElement.requestFullscreen();
                        } else {
                            await document.exitFullscreen();
                        }
                    } catch (e) {}
                },
            };
        }
    </script>
</body>
</html>
