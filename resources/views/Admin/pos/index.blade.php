<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS — {{ $shop->name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .hide-scroll { -ms-overflow-style: none; scrollbar-width: none; }
        .product-card:active { transform: scale(0.96); }
        .product-card { transition: transform 0.1s, box-shadow 0.1s; }
    </style>
</head>
<body class="bg-gray-100 h-screen overflow-hidden font-sans" x-data="posApp()" x-init="init()" @keydown.window="handleKey($event)">

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SHIFT GATE — Blur overlay if no active session             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@if(!$activeSession)
<div class="fixed inset-0 z-50 bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-sm text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-cash-register text-2xl text-green-600"></i>
        </div>
        <h2 class="text-2xl font-black text-gray-800 mb-1">Buka Shift</h2>
        <p class="text-gray-500 text-sm mb-6">Masukkan modal awal sebelum mulai kasir</p>
        <form action="{{ route('shift.open') }}" method="POST">
            @csrf
            <div class="relative mb-4">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                <input type="number" name="opening_cash" placeholder="0" min="0" step="1000"
                    class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none text-lg font-bold text-right" required>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-4 rounded-xl text-lg transition">
                Buka Shift & Mulai Kasir
            </button>
        </form>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- TOPBAR                                                      --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="flex items-center justify-between px-4 py-2 bg-white border-b border-gray-200 h-14 shrink-0">
    <div class="flex items-center gap-3">
        <span class="text-xl font-black text-green-700">OQARI</span>
        <span class="text-gray-300">|</span>
        <span class="text-sm font-semibold text-gray-500">{{ $shop->name }}</span>
    </div>
    <div class="flex items-center gap-3">
        @if($activeSession)
        <span class="text-xs bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full">
            <i class="fas fa-circle text-green-500 text-[8px] mr-1"></i> SHIFT AKTIF
        </span>
        <form action="{{ route('shift.close') }}" method="POST" onsubmit="return confirm('Tutup shift sekarang?')">
            @csrf
            <input type="hidden" name="actual_cash" id="closeActualCash" value="0">
            <button type="button" onclick="promptCloseShift()" class="text-xs text-red-500 font-bold hover:underline">Tutup Shift</button>
        </form>
        @endif
        <div class="flex items-center gap-2 text-sm text-gray-700">
            <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-xs">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <span class="font-semibold hidden sm:block">{{ Auth::user()->name }}</span>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="text-xs text-gray-400 hover:text-red-500"><i class="fas fa-sign-out-alt"></i></button>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MAIN LAYOUT                                                 --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div class="flex h-[calc(100vh-3.5rem)] overflow-hidden">

    {{-- LEFT PANEL — Products --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Category Tabs + Search --}}
        <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3 shrink-0">
            {{-- Search --}}
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari produk... (F1)"
                    class="pl-9 pr-4 py-2 text-sm rounded-xl border border-gray-200 bg-gray-50 w-48 focus:outline-none focus:border-green-500 focus:bg-white transition">
            </div>
            {{-- Category Pills --}}
            <div class="flex gap-2 overflow-x-auto hide-scroll">
                <button @click="activeCategory = null"
                    :class="activeCategory === null ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="shrink-0 text-xs font-bold px-4 py-2 rounded-full transition whitespace-nowrap">
                    Semua
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = {{ $cat['id'] }}"
                    :class="activeCategory === {{ $cat['id'] }} ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="shrink-0 text-xs font-bold px-4 py-2 rounded-full transition whitespace-nowrap">
                    {{ $cat['name'] }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($products as $product)
                <button
                    x-show="matchesFilter({{ $product->category_id ?? 'null' }}, '{{ addslashes($product->name) }}')"
                    @click="openModifier({{ $product->toJson() }})"
                    class="product-card bg-white rounded-2xl p-3 text-left shadow-sm border border-gray-100 hover:border-green-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-green-400"
                >
                    @if($product->image_url)
                    <img src="{{ $product->image_url }}" class="w-full h-24 object-cover rounded-xl mb-2">
                    @else
                    <div class="w-full h-24 bg-gradient-to-br from-green-50 to-green-100 rounded-xl mb-2 flex items-center justify-center">
                        <i class="fas fa-coffee text-3xl text-green-300"></i>
                    </div>
                    @endif
                    <p class="text-sm font-bold text-gray-800 leading-tight truncate">{{ $product->name }}</p>
                    <p class="text-xs text-green-700 font-black mt-1">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    @if($product->is_sold_out)
                    <span class="text-[10px] bg-red-100 text-red-600 font-bold px-2 py-0.5 rounded-full">HABIS</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

        {{-- HOLD Recall Bar --}}
        <template x-if="heldOrders.length > 0">
        <div class="bg-amber-50 border-t border-amber-200 px-4 py-2 shrink-0">
            <p class="text-xs font-extrabold text-amber-600 mb-1.5 uppercase tracking-wider">
                <i class="fas fa-pause-circle mr-1"></i> Order Ditahan (F3)
            </p>
            <div class="flex gap-2 overflow-x-auto hide-scroll">
                <template x-for="held in heldOrders" :key="held.id">
                    <button @click="recallOrder(held.id)"
                        class="shrink-0 bg-white border border-amber-300 rounded-xl px-3 py-1.5 text-xs hover:bg-amber-100 transition">
                        <span class="font-black text-gray-700">#<span x-text="held.id"></span></span>
                        <span class="text-amber-700 ml-1 font-semibold">Rp <span x-text="formatRp(held.total)"></span></span>
                    </button>
                </template>
            </div>
        </div>
        </template>
    </div>

    {{-- RIGHT PANEL — Cart --}}
    <div class="w-80 bg-white border-l border-gray-200 flex flex-col shrink-0">

        {{-- Cart Header --}}
        <div class="p-4 border-b border-gray-100 flex items-center justify-between shrink-0">
            <h3 class="font-black text-gray-800">
                <i class="fas fa-receipt text-green-600 mr-2"></i>
                PESANAN <span x-show="recalledOrderId" x-cloak class="text-green-600">#<span x-text="recalledOrderId"></span></span>
            </h3>
            <button @click="clearCart()" x-show="cart.length > 0" x-cloak class="text-red-400 hover:text-red-600 text-xs font-bold">HAPUS</button>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto hide-scroll p-3">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-gray-300 py-12">
                    <i class="fas fa-shopping-basket text-5xl mb-3"></i>
                    <p class="font-bold text-sm">Keranjang kosong</p>
                    <p class="text-xs">Pilih produk untuk mulai</p>
                </div>
            </template>

            <div class="space-y-2">
                <template x-for="(item, idx) in cart" :key="idx">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-bold text-gray-800" x-text="item.name"></p>
                                <p x-show="item.variant" x-cloak class="text-xs text-gray-500" x-text="item.variant"></p>
                                <p x-show="item.modifiers && item.modifiers.length > 0" x-cloak
                                   class="text-xs text-gray-500"
                                   x-text="item.modifiers.map(m => m.name).join(' · ')"></p>
                                <p x-show="item.notes" x-cloak class="text-xs text-amber-600 italic" x-text="'📝 ' + item.notes"></p>
                            </div>
                            <button @click="removeItem(idx)" class="text-red-400 hover:text-red-600 ml-2 shrink-0">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-2">
                                <button @click="changeQty(idx, -1)" class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 text-xs font-bold flex items-center justify-center">−</button>
                                <span class="text-sm font-bold w-5 text-center" x-text="item.qty"></span>
                                <button @click="changeQty(idx, 1)" class="w-6 h-6 rounded-full bg-green-100 hover:bg-green-200 text-green-700 text-xs font-bold flex items-center justify-center">+</button>
                            </div>
                            <span class="text-sm font-black text-green-700">Rp <span x-text="formatRp(item.subtotal)"></span></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Cart Summary --}}
        <div class="p-4 border-t border-gray-100 shrink-0">
            <div class="space-y-1 text-sm mb-3">
                <div class="flex justify-between text-gray-500">
                    <span>Subtotal</span>
                    <span>Rp <span x-text="formatRp(subtotal)"></span></span>
                </div>
                <div class="flex justify-between text-gray-400 text-xs">
                    <span>Diskon</span>
                    <span>Rp 0</span>
                </div>
                <div class="flex justify-between text-gray-400 text-xs">
                    <span>Pajak</span>
                    <span>Rp 0</span>
                </div>
                <div class="flex justify-between font-black text-gray-800 text-base pt-2 border-t border-gray-200">
                    <span>TOTAL</span>
                    <span>Rp <span x-text="formatRp(subtotal)"></span></span>
                </div>
            </div>

            <div class="flex gap-2">
                <button @click="holdCart()" x-show="cart.length > 0" x-cloak
                    class="flex-1 py-3 rounded-xl border-2 border-gray-300 text-gray-600 font-bold text-sm hover:bg-gray-50 transition">
                    <i class="fas fa-pause mr-1"></i> HOLD (F2)
                </button>
                <button @click="openPayment()" x-show="cart.length > 0" x-cloak
                    class="flex-1 py-3 rounded-xl bg-green-600 hover:bg-green-700 text-white font-black text-sm shadow-lg shadow-green-200 transition">
                    <i class="fas fa-credit-card mr-1"></i> BAYAR (F4)
                </button>
                <div x-show="cart.length === 0" class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-400 font-bold text-sm text-center">
                    Pilih produk
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- MODIFIER MODAL                                             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="modifierModal.show" x-cloak x-transition
    class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center p-4"
    @click.self="modifierModal.show = false">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm max-h-[90vh] flex flex-col overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-black text-lg text-gray-800" x-text="modifierModal.product?.name"></h3>
                <p class="text-green-600 font-black text-sm">Rp <span x-text="formatRp(modalTotal)"></span></p>
            </div>
            <button @click="modifierModal.show = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-5 hide-scroll">
            {{-- Variants --}}
            <template x-if="modifierModal.product?.variants?.length > 0">
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2">Varian</p>
                <div class="space-y-2">
                    <template x-for="v in modifierModal.product.variants" :key="v.id">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition"
                            :class="modifierModal.selectedVariant?.id === v.id ? 'border-green-500 bg-green-50' : 'border-gray-100 hover:border-gray-200'">
                            <input type="radio" class="sr-only" :value="v.id" x-model.number="modifierModal.selectedVariantId" @change="selectVariant(v)">
                            <span class="text-sm font-semibold text-gray-700 flex-1" x-text="v.name"></span>
                            <span class="text-xs text-green-600 font-bold" x-show="v.price_adjustment > 0" x-text="'+Rp ' + formatRp(v.price_adjustment)"></span>
                        </label>
                    </template>
                </div>
            </div>
            </template>

            {{-- Modifier Groups --}}
            <template x-for="group in (modifierModal.product?.modifier_groups ?? [])" :key="group.id">
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2" x-text="group.name"></p>
                <div class="space-y-2">
                    <template x-for="mod in group.modifiers" :key="mod.id">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition"
                            :class="isModifierSelected(mod.id) ? 'border-green-500 bg-green-50' : 'border-gray-100 hover:border-gray-200'">
                            <input type="checkbox" class="rounded text-green-600" :checked="isModifierSelected(mod.id)" @change="toggleModifier(mod)">
                            <span class="text-sm font-semibold text-gray-700 flex-1" x-text="mod.name"></span>
                            <span class="text-xs text-green-600 font-bold" x-show="mod.price_adjustment > 0" x-text="'+Rp ' + formatRp(mod.price_adjustment)"></span>
                        </label>
                    </template>
                </div>
            </div>
            </template>

            {{-- Notes --}}
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2">Catatan Khusus</p>
                <textarea x-model="modifierModal.notes" rows="2" placeholder="Contoh: tanpa es, extra panas..."
                    class="w-full px-3 py-2 text-sm rounded-xl border border-gray-200 focus:outline-none focus:border-green-500 resize-none"></textarea>
            </div>
        </div>

        <div class="p-5 border-t border-gray-100 flex items-center gap-3 shrink-0">
            <div class="flex items-center gap-3">
                <button @click="modifierModal.qty > 1 && modifierModal.qty--"
                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 font-bold text-lg flex items-center justify-center">−</button>
                <span class="text-lg font-black w-6 text-center" x-text="modifierModal.qty"></span>
                <button @click="modifierModal.qty++"
                    class="w-10 h-10 rounded-full bg-green-100 hover:bg-green-200 text-green-700 font-bold text-lg flex items-center justify-center">+</button>
            </div>
            <button @click="addToCart()" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white font-black rounded-xl shadow-lg shadow-green-200 transition">
                TAMBAH — Rp <span x-text="formatRp(modalTotal * modifierModal.qty)"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- PAYMENT MODAL                                              --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="paymentModal.show" x-cloak x-transition
    class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center p-4"
    @click.self="paymentModal.show = false">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h3 class="font-black text-xl text-gray-800 text-center">Pembayaran</h3>
            <div class="text-center mt-3">
                <p class="text-3xl font-black text-green-600">Rp <span x-text="formatRp(subtotal)"></span></p>
            </div>
        </div>

        <div class="p-5 space-y-5">
            {{-- Method --}}
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-3">Metode Pembayaran</p>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="m in ['CASH','QRIS','CARD']" :key="m">
                    <button @click="paymentModal.method = m; paymentModal.amountPaid = m === 'CASH' ? '' : subtotal"
                        :class="paymentModal.method === m ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'"
                        class="py-3 rounded-xl border-2 font-bold text-sm transition text-center">
                        <i :class="m === 'CASH' ? 'fas fa-money-bill' : m === 'QRIS' ? 'fas fa-qrcode' : 'fas fa-credit-card'" class="block text-xl mb-1"></i>
                        <span x-text="m"></span>
                    </button>
                    </template>
                </div>
            </div>

            {{-- Cash input --}}
            <template x-if="paymentModal.method === 'CASH'">
            <div>
                <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2">Uang Diterima (F5)</p>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                    <input x-ref="cashInput" x-model.number="paymentModal.amountPaid" type="number" step="1000" min="0"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 outline-none text-right font-bold text-lg">
                </div>
                <div class="flex gap-2 mt-2 flex-wrap">
                    <template x-for="quick in quickCash" :key="quick">
                    <button @click="paymentModal.amountPaid = quick"
                        class="text-xs bg-gray-100 hover:bg-green-50 border border-gray-200 hover:border-green-400 text-gray-600 px-3 py-1.5 rounded-lg font-bold transition"
                        x-text="'Rp' + formatRp(quick)"></button>
                    </template>
                </div>
                <div x-show="paymentModal.amountPaid >= subtotal" x-cloak class="mt-3 bg-green-50 rounded-xl p-3 flex justify-between">
                    <span class="text-sm text-gray-600 font-semibold">Kembalian</span>
                    <span class="text-lg font-black text-green-600">Rp <span x-text="formatRp(paymentModal.amountPaid - subtotal)"></span></span>
                </div>
            </div>
            </template>

            {{-- QRIS --}}
            <template x-if="paymentModal.method === 'QRIS'">
            <div class="text-center py-3">
                <div class="w-32 h-32 bg-gray-100 rounded-2xl mx-auto flex items-center justify-center">
                    <i class="fas fa-qrcode text-5xl text-gray-400"></i>
                </div>
                <p class="text-xs text-gray-500 mt-2">Tunjukkan QR ke pelanggan, lalu konfirmasi setelah pembayaran diterima</p>
            </div>
            </template>

            <button @click="confirmPayment()"
                :disabled="!canPay()"
                :class="canPay() ? 'bg-green-600 hover:bg-green-700 shadow-lg shadow-green-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                class="w-full py-4 rounded-xl text-white font-black text-lg transition">
                KONFIRMASI BAYAR
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SUCCESS SCREEN                                             --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div x-show="successScreen.show" x-cloak x-transition
    class="fixed inset-0 z-50 bg-green-600 flex items-center justify-center">
    <div class="text-center text-white p-8">
        <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-5xl"></i>
        </div>
        <p class="text-2xl font-black mb-1">PEMBAYARAN BERHASIL</p>
        <p class="text-4xl font-black mb-3">Rp <span x-text="formatRp(successScreen.total)"></span></p>
        <template x-if="successScreen.change > 0">
        <div class="bg-white/20 rounded-2xl px-6 py-3 inline-block mb-6">
            <p class="text-sm opacity-80">Kembalian</p>
            <p class="text-2xl font-black">Rp <span x-text="formatRp(successScreen.change)"></span></p>
        </div>
        </template>
        <div class="flex gap-3 justify-center mt-6">
            <button @click="printReceipt()" class="px-6 py-3 bg-white/20 hover:bg-white/30 rounded-xl font-bold transition">
                <i class="fas fa-print mr-2"></i> Cetak Struk
            </button>
            <button @click="newOrder()" class="px-8 py-3 bg-white rounded-xl text-green-700 font-black shadow-lg transition hover:scale-105">
                PESANAN BARU
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
        activeCategory: null,
        search: '',
        heldOrders: @json($heldOrders->map(fn($o) => ['id' => $o->id, 'total' => $o->grand_total])),
        recalledOrderId: null,

        // Modals
        modifierModal: {
            show: false, product: null, selectedVariant: null, selectedVariantId: null,
            selectedModifiers: [], qty: 1, notes: ''
        },
        paymentModal: { show: false, method: 'CASH', amountPaid: '' },
        successScreen: { show: false, total: 0, change: 0, orderId: null },

        quickCash: [5000, 10000, 20000, 50000, 100000],

        init() {
            // Keyboard shortcut listener is on body via @keydown.window
        },

        // ── Computed ──────────────────────────
        get subtotal() {
            return this.cart.reduce((s, i) => s + i.subtotal, 0);
        },
        get modalTotal() {
            if (!this.modifierModal.product) return 0;
            let base = this.modifierModal.product.price;
            if (this.modifierModal.selectedVariant) base += this.modifierModal.selectedVariant.price_adjustment;
            this.modifierModal.selectedModifiers.forEach(m => base += m.price_adjustment);
            return base;
        },

        // ── Filters ───────────────────────────
        matchesFilter(catId, name) {
            const catMatch = this.activeCategory === null || catId === this.activeCategory;
            const searchMatch = this.search === '' || name.toLowerCase().includes(this.search.toLowerCase());
            return catMatch && searchMatch;
        },

        // ── Modifier Modal ────────────────────
        openModifier(product) {
            this.modifierModal = {
                show: true, product, selectedVariant: null, selectedVariantId: null,
                selectedModifiers: [], qty: 1, notes: ''
            };
            // Auto-select first variant if only one
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
            const notes = this.modifierModal.notes;
            const price = this.modalTotal;

            this.cart.push({
                id: p.id, name: p.name,
                variant_id: variant?.id ?? null,
                variant: variant?.name ?? null,
                modifiers: mods,
                notes, qty, price,
                subtotal: price * qty,
            });

            this.modifierModal.show = false;
        },

        // ── Cart ──────────────────────────────
        changeQty(idx, delta) {
            this.cart[idx].qty = Math.max(1, this.cart[idx].qty + delta);
            this.cart[idx].subtotal = this.cart[idx].price * this.cart[idx].qty;
        },
        removeItem(idx) { this.cart.splice(idx, 1); },
        clearCart() { this.cart = []; this.recalledOrderId = null; },

        // ── Hold / Recall ─────────────────────
        async holdCart() {
            if (this.cart.length === 0) return;
            const payload = { items: this.cartPayload(), table: null, customer_name: null };
            try {
                const res = await this.post('{{ route('admin.pos.orders.hold') }}', payload);
                if (res.success) {
                    this.heldOrders.push({ id: res.order_id, total: res.total });
                    this.clearCart();
                    this.notify('Order ditahan', 'success');
                }
            } catch(e) { this.notify('Gagal menahan order', 'error'); }
        },

        async recallOrder(orderId) {
            try {
                const res = await this.post(`/admin/pos/orders/${orderId}/recall`, {});
                if (res.success) {
                    this.clearCart();
                    res.order.items.forEach(i => {
                        this.cart.push({
                            id: i.id, name: i.name,
                            variant_id: i.variant_id, variant: i.variant,
                            modifiers: i.modifiers, notes: i.notes,
                            qty: i.qty, price: i.price, subtotal: i.subtotal,
                        });
                    });
                    this.recalledOrderId = orderId;
                    this.heldOrders = this.heldOrders.filter(o => o.id !== orderId);
                    this.notify('Order diambil kembali', 'success');
                }
            } catch(e) { this.notify('Gagal recall order', 'error'); }
        },

        // ── Payment ───────────────────────────
        openPayment() {
            this.paymentModal = { show: true, method: 'CASH', amountPaid: '' };
        },
        canPay() {
            if (this.paymentModal.method === 'CASH') return Number(this.paymentModal.amountPaid) >= this.subtotal;
            return true; // QRIS/CARD always payable
        },
        async confirmPayment() {
            if (!this.canPay()) return;
            const amountPaid = this.paymentModal.method === 'CASH' ? Number(this.paymentModal.amountPaid) : this.subtotal;
            const payload = {
                items: this.cartPayload(),
                payment_method: this.paymentModal.method,
                amount_paid: amountPaid,
                fulfillment_type: 'DINE_IN',
            };
            try {
                const res = await this.post('{{ route('admin.pos.orders.submit') }}', payload);
                if (res.success) {
                    this.paymentModal.show = false;
                    this.successScreen = { show: true, total: res.total, change: res.change ?? 0, orderId: res.order_id };
                }
            } catch(e) { this.notify(e.message || 'Gagal memproses pembayaran', 'error'); }
        },

        // ── Success / New Order ───────────────
        printReceipt() {
            if (!this.successScreen.orderId) return;
            window.open(`/admin/api/orders/${this.successScreen.orderId}/print`, '_blank');
        },
        newOrder() {
            this.successScreen = { show: false, total: 0, change: 0, orderId: null };
            this.clearCart();
        },

        // ── Keyboard Shortcuts ────────────────
        handleKey(e) {
            if (['INPUT','TEXTAREA'].includes(e.target.tagName)) return;
            switch(e.key) {
                case 'F1': e.preventDefault(); this.$refs.searchInput?.focus(); break;
                case 'F2': e.preventDefault(); this.holdCart(); break;
                case 'F3': e.preventDefault(); break; // Focus recall bar
                case 'F4': e.preventDefault(); if (this.cart.length > 0) this.openPayment(); break;
                case 'F5': e.preventDefault(); if (this.paymentModal.show) { this.paymentModal.method='CASH'; this.$nextTick(() => this.$refs.cashInput?.focus()); } break;
                case 'F6': e.preventDefault(); if (this.paymentModal.show) this.paymentModal.method='QRIS'; break;
                case 'Escape': this.modifierModal.show = false; this.paymentModal.show = false; break;
            }
        },

        // ── Helpers ───────────────────────────
        cartPayload() {
            return this.cart.map(i => ({
                id: i.id, qty: i.qty,
                variant_id: i.variant_id,
                modifiers: (i.modifiers ?? []).map(m => m.id),
                notes: i.notes,
            }));
        },
        async post(url, data) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(data),
            });
            const json = await res.json();
            if (!res.ok) throw json;
            return json;
        },
        formatRp(n) { return Number(n ?? 0).toLocaleString('id-ID'); },
        notify(msg, type) {
            // Simple toast — could be wired to a more robust toast system
            alert((type === 'error' ? '❌ ' : '✅ ') + msg);
        },
    };
}

function promptCloseShift() {
    const actual = prompt('Masukkan jumlah uang tunai di laci kasir sekarang (Rp):');
    if (actual !== null) {
        document.getElementById('closeActualCash').value = actual;
        document.querySelector('form[action*="shift.close"]').submit();
    }
}
</script>
</body>
</html>
