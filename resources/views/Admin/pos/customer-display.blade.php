<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Layar Pelanggan — {{ $shop->name }}</title>

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
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="bg-[#f8f9fa] text-[#1e211f] h-[100dvh] w-full overflow-hidden antialiased select-none"
      x-data="customerDisplayApp({{ (int) $shop->id }})">

    {{-- Top Status Bar --}}
    <header class="h-[56px] bg-white border-b border-[#e6e5e0] px-6 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3 min-w-0">
            @if(!empty($shop->logo_url))
                <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-9 h-9 rounded-[10px] object-cover border border-[#e6e5e0] shrink-0">
            @else
                <div class="w-9 h-9 rounded-[10px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center shrink-0">
                    <i class="fas fa-mug-hot text-[#df5826] text-base"></i>
                </div>
            @endif
            <div class="min-w-0">
                <h1 class="text-[15px] font-bold text-[#111827] truncate leading-tight">{{ $shop->name }}</h1>
                <p class="text-[11px] text-[#71717a] leading-tight">Layar Informasi Pelanggan</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold"
                  :class="isConnected ? 'bg-[#eaf7ef] text-[#289e5c]' : 'bg-amber-50 text-amber-700'">
                <span class="w-2 h-2 rounded-full" :class="isConnected ? 'bg-[#289e5c] animate-pulse' : 'bg-amber-500'"></span>
                <span x-text="syncLabel"></span>
            </span>

            <div class="text-right hidden sm:block">
                <div class="text-[13px] font-bold text-[#111827] leading-tight" x-text="clockTime"></div>
                <div class="text-[10.5px] text-[#71717a] leading-tight" x-text="clockDate"></div>
            </div>

            <a href="{{ route('admin.pos.customer-kiosk') }}"
               class="h-9 px-3 rounded-[10px] bg-[#f8f9fa] hover:bg-gray-200 border border-[#e6e5e0] text-[11.5px] font-semibold text-[#53565a] inline-flex items-center gap-1.5 transition"
               title="Beralih ke Mode Touchscreen (Pelanggan Pilih Menu)">
                <i class="fas fa-hand-pointer text-[#df5826]"></i>
                <span class="hidden sm:inline">Mode Touchscreen</span>
            </a>

            <button type="button" @click="toggleFullscreen()"
                    class="w-9 h-9 rounded-[10px] bg-[#f8f9fa] hover:bg-gray-200 border border-[#e6e5e0] text-[#53565a] flex items-center justify-center transition cursor-pointer"
                    title="Layar Penuh">
                <i class="fas" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
            </button>
        </div>
    </header>

    {{-- Main Display Area --}}
    <main class="h-[calc(100dvh-56px)] w-full p-5 lg:p-6">

        {{-- 1. IDLE STATE (Keranjang Kosong) --}}
        <section x-show="state.status === 'idle'" x-transition.opacity
                 class="h-full w-full bg-white border border-[#e6e5e0] rounded-[20px] shadow-xs flex flex-col lg:flex-row items-center justify-center gap-8 p-8 text-center lg:text-left">
            <div class="max-w-md flex flex-col items-center lg:items-start">
                @if(!empty($shop->logo_url))
                    <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-20 h-20 rounded-[18px] object-cover border border-[#e6e5e0] shadow-sm mb-5">
                @else
                    <div class="w-20 h-20 rounded-[18px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center mb-5">
                        <i class="fas fa-mug-hot text-3xl text-[#df5826]"></i>
                    </div>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#fff6f0] text-[#df5826] text-[12px] font-bold mb-3">
                    <i class="fas fa-sparkles"></i> Selamat Datang
                </span>
                <h2 class="text-[28px] lg:text-[34px] font-bold text-[#111827] leading-tight mb-2">
                    Silakan Pesan di Kasir
                </h2>
                <p class="text-[14px] text-[#71717a] leading-relaxed">
                    Daftar pesanan dan rincian pembayaran Anda akan tampil secara otomatis di layar ini.
                </p>
            </div>

            @if(!empty($shop->banner_url))
                <div class="w-full max-w-lg h-[260px] lg:h-[340px] rounded-[16px] overflow-hidden border border-[#e6e5e0] shadow-sm shrink-0">
                    <img src="{{ $shop->banner_url }}" alt="Promo {{ $shop->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-full max-w-sm bg-[#f8f9fa] border border-dashed border-[#d8dce2] rounded-[18px] p-6 text-center shrink-0">
                    <div class="text-[40px] font-bold text-[#111827] tracking-tight" x-text="clockTime"></div>
                    <div class="text-[13px] font-medium text-[#71717a] mt-1" x-text="clockDate"></div>
                    <div class="mt-4 pt-4 border-t border-[#e6e5e0] text-[12px] text-[#53565a]">
                        Terima kasih telah berkunjung ke <strong>{{ $shop->name }}</strong>
                    </div>
                </div>
            @endif
        </section>

        {{-- 2. ORDERING & PAYMENT STATE --}}
        <section x-show="state.status === 'ordering' || state.status === 'payment'" x-cloak x-transition.opacity
                 class="h-full w-full grid grid-cols-1 md:grid-cols-12 gap-4 lg:gap-5 overflow-y-auto md:overflow-hidden">

            {{-- Left: Itemized Order List --}}
            <div class="md:col-span-7 xl:col-span-8 bg-white border border-[#e6e5e0] rounded-[20px] flex flex-col overflow-hidden shadow-xs min-h-[260px]">
                <div class="px-6 py-4 border-b border-[#e6e5e0] flex items-center justify-between shrink-0">
                    <div>
                        <h2 class="text-[18px] font-bold text-[#111827]">Rincian Pesanan</h2>
                        <p class="text-[12px] text-[#71717a]" x-text="totalItemsCount + ' item dalam keranjang'"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-if="state.fulfillment_type">
                            <span class="px-3 py-1 rounded-full bg-[#f3f4f6] text-[#111827] text-[12px] font-bold"
                                  x-text="formatFulfillment(state.fulfillment_type)"></span>
                        </template>
                        <template x-if="state.table_name">
                            <span class="px-3 py-1 rounded-full bg-[#fff6f0] text-[#df5826] border border-[#f5d5c6] text-[12px] font-bold"
                                  x-text="state.table_name"></span>
                        </template>
                    </div>
                </div>

                <div id="cfd-items-scroll" class="flex-1 overflow-y-auto p-5 space-y-3 hide-scroll">
                    <template x-for="(item, idx) in state.items" :key="idx">
                        <div class="bg-[#f8f9fa] border border-[#e6e5e0] rounded-[14px] p-4 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5 min-w-0">
                                <div class="w-9 h-9 rounded-[10px] bg-white border border-[#d8dce2] flex items-center justify-center font-bold text-[14px] text-[#df5826] shrink-0"
                                     x-text="item.qty + '×'"></div>
                                <div class="min-w-0">
                                    <h3 class="text-[15px] font-bold text-[#111827] leading-snug" x-text="item.name"></h3>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <template x-if="item.variant">
                                            <span class="text-[11.5px] font-medium px-2 py-0.5 rounded bg-white border border-[#e6e5e0] text-[#53565a]"
                                                  x-text="item.variant"></span>
                                        </template>
                                        <template x-for="(mod, mIdx) in (item.modifiers || [])" :key="mIdx">
                                            <span class="text-[11.5px] font-medium px-2 py-0.5 rounded bg-white border border-[#e6e5e0] text-[#53565a]"
                                                  x-text="'+ ' + mod"></span>
                                        </template>
                                    </div>
                                    <template x-if="item.notes">
                                        <p class="text-[11.5px] text-[#71717a] italic mt-1" x-text="'Catatan: ' + item.notes"></p>
                                    </template>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-[15px] font-bold text-[#111827]" x-text="'Rp ' + formatRp(item.subtotal)"></p>
                                <p class="text-[11px] text-[#71717a]" x-text="'@ Rp ' + formatRp(item.price)"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Right: Summary & Payment Prompt --}}
            <div class="md:col-span-5 xl:col-span-4 bg-white border border-[#e6e5e0] rounded-[20px] p-5 lg:p-6 flex flex-col justify-between shadow-xs">
                <div class="space-y-4">
                    <template x-if="state.customer_name">
                        <div class="bg-[#f8f9fa] border border-[#e6e5e0] rounded-[12px] px-4 py-3 flex items-center justify-between">
                            <span class="text-[12px] text-[#71717a] font-medium">Nama Pelanggan</span>
                            <span class="text-[14px] font-bold text-[#111827]" x-text="state.customer_name"></span>
                        </div>
                    </template>

                    {{-- Payment Active Box --}}
                    <template x-if="state.status === 'payment'">
                        <div class="bg-[#fff6f0] border border-[#f5d5c6] rounded-[16px] p-5 text-center">
                            <template x-if="state.payment_method === 'QRIS'">
                                <div>
                                    <div class="w-24 h-24 bg-white rounded-[14px] border border-[#f5d5c6] mx-auto flex items-center justify-center mb-3 shadow-2xs">
                                        <i class="fas fa-qrcode text-5xl text-[#111827]"></i>
                                    </div>
                                    <h3 class="text-[16px] font-bold text-[#111827]">Pembayaran QRIS</h3>
                                    <p class="text-[12px] text-[#53565a] mt-1">Silakan scan kode QRIS pada perangkat pembayaran kasir</p>
                                </div>
                            </template>
                            <template x-if="state.payment_method !== 'QRIS'">
                                <div>
                                    <div class="w-12 h-12 bg-white rounded-full border border-[#f5d5c6] mx-auto flex items-center justify-center mb-2">
                                        <i class="fas" :class="state.payment_method === 'CARD' ? 'fa-credit-card text-[#df5826]' : 'fa-money-bill-wave text-[#df5826]'"></i>
                                    </div>
                                    <h3 class="text-[15px] font-bold text-[#111827]">
                                        Pembayaran <span x-text="state.payment_method || 'TUNAI'"></span>
                                    </h3>
                                    <p class="text-[12px] text-[#53565a] mt-0.5">Menunggu konfirmasi kasir...</p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Totals --}}
                <div class="pt-4 border-t border-[#e6e5e0] space-y-2.5">
                    <div class="flex items-center justify-between text-[14px] text-[#53565a]">
                        <span>Subtotal</span>
                        <span class="font-semibold text-[#111827]" x-text="'Rp ' + formatRp(state.subtotal)"></span>
                    </div>

                    <template x-if="Number(state.discount_amount) > 0">
                        <div class="flex items-center justify-between text-[14px] text-[#289e5c]">
                            <span x-text="state.discount_label || 'Diskon'"></span>
                            <span class="font-bold" x-text="'- Rp ' + formatRp(state.discount_amount)"></span>
                        </div>
                    </template>

                    <div class="pt-3 border-t border-dashed border-[#d8dce2] flex items-baseline justify-between">
                        <span class="text-[16px] font-bold text-[#111827]">Total Bayar</span>
                        <span class="text-[32px] lg:text-[36px] font-bold text-[#df5826] tracking-tight"
                              x-text="'Rp ' + formatRp(state.grand_total)"></span>
                    </div>
                </div>
            </div>
        </section>

        {{-- 3. PAID / SUCCESS STATE --}}
        <section x-show="state.status === 'paid'" x-cloak x-transition.opacity
                 class="h-full w-full bg-white border border-[#bfe5cd] rounded-[20px] shadow-xs flex flex-col items-center justify-center p-8 text-center">
            <div class="w-20 h-20 rounded-full bg-[#eaf7ef] border border-[#bfe5cd] flex items-center justify-center mb-5">
                <i class="fas fa-check text-3xl text-[#289e5c]"></i>
            </div>
            <h2 class="text-[28px] lg:text-[32px] font-bold text-[#111827]">Pembayaran Berhasil!</h2>
            <p class="text-[14px] text-[#71717a] mt-1 mb-6">
                Terima kasih telah memesan di <strong>{{ $shop->name }}</strong>
                <template x-if="state.order_id">
                    <span x-text="' • Order #' + state.order_id"></span>
                </template>
            </p>

            <div class="w-full max-w-md bg-[#f8f9fa] border border-[#e6e5e0] rounded-[16px] p-5 space-y-3">
                <div class="flex items-center justify-between text-[15px]">
                    <span class="text-[#53565a]">Total Transaksi</span>
                    <span class="font-bold text-[#111827] text-[18px]" x-text="'Rp ' + formatRp(state.grand_total)"></span>
                </div>
                <template x-if="Number(state.change) > 0">
                    <div class="pt-3 border-t border-dashed border-[#d8dce2] flex items-center justify-between text-[16px]">
                        <span class="font-semibold text-[#53565a]">Kembalian</span>
                        <span class="font-bold text-[#df5826] text-[22px]" x-text="'Rp ' + formatRp(state.change)"></span>
                    </div>
                </template>
            </div>
        </section>

    </main>

    <script>
        function customerDisplayApp(shopId) {
            return {
                shopId,
                isConnected: true,
                syncMode: 'BroadcastChannel',
                isFullscreen: false,
                clockTime: '',
                clockDate: '',
                paidResetTimer: null,
                channel: null,
                state: {
                    status: 'idle',
                    items: [],
                    subtotal: 0,
                    discount_amount: 0,
                    discount_label: '',
                    grand_total: 0,
                    customer_name: '',
                    table_name: '',
                    fulfillment_type: 'DINE_IN',
                    payment_method: 'CASH',
                    amount_paid: 0,
                    change: 0,
                    order_id: null,
                },

                get totalItemsCount() {
                    return (this.state.items || []).reduce((sum, item) => sum + Number(item.qty || 1), 0);
                },

                get syncLabel() {
                    return this.isConnected ? `Terhubung (${this.syncMode})` : 'Menghubungkan...';
                },

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = Boolean(document.fullscreenElement);
                    });

                    // Restore last local state if any
                    try {
                        const saved = localStorage.getItem(`oqari_pos_cfd_state_${this.shopId}`);
                        if (saved) {
                            const parsed = JSON.parse(saved);
                            if (parsed && parsed.status) {
                                this.applyState(parsed);
                            }
                        }
                    } catch (e) {}

                    // 1. Primary: BroadcastChannel (0ms same-browser / dual-screen window)
                    if ('BroadcastChannel' in window) {
                        this.channel = new BroadcastChannel(`oqari_pos_cfd_${this.shopId}`);
                        this.channel.onmessage = (event) => {
                            if (event.data && event.data.type === 'CFD_SYNC') {
                                this.syncMode = 'Dual Screen';
                                this.isConnected = true;
                                this.applyState(event.data.payload);
                            }
                        };
                    }

                    // 2. Fallback: Laravel Echo / Pusher (cross-browser / cross-device)
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
                        this.paidResetTimer = setTimeout(() => {
                            this.state.status = 'idle';
                            this.state.items = [];
                        }, 10000);
                    }

                    this.$nextTick(() => {
                        const scroller = document.getElementById('cfd-items-scroll');
                        if (scroller) {
                            scroller.scrollTop = scroller.scrollHeight;
                        }
                    });
                },

                updateClock() {
                    const now = new Date();
                    this.clockTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    this.clockDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                },

                formatRp(val) {
                    return Number(val || 0).toLocaleString('id-ID');
                },

                formatFulfillment(type) {
                    if (type === 'TAKEAWAY') return 'Takeaway';
                    if (type === 'DELIVERY') return 'Delivery';
                    return 'Dine In';
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
