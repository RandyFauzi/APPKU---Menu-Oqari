<div x-show="currentTab === 'orders'" x-cloak class="flex-grow bg-[#f6f5f2] p-4 md:p-[24px] overflow-y-auto hide-scroll font-['DM_Sans',sans-serif] text-[#1e211f]">

    <!-- Page Title & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight">Pesanan Aktif</h1>
            <p class="text-[12px] text-[#7f837e] mt-0.5">Pantau dan kelola status antrean pesanan pelanggan secara real-time.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button"
               @click="openCreateOpenBillModal()"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-semibold shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition cursor-pointer">
                <img src="{{ asset('images/sparta/cart-btn-icon.svg') }}" alt="" class="w-[15px] h-[15px]">
                <span>Buat Pesanan</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar Card (Sparta Style) -->
    <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-[14px] mb-5 shadow-[0px_2px_8px_rgba(0,0,0,0.02)]">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
            <!-- Status Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto hide-scroll">
                <button type="button" @click="activeOrderFilter = 'all'"
                    :class="activeOrderFilter === 'all' ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                    class="border rounded-[9px] px-3.5 py-2 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap">
                    <span>Semua</span>
                    <span class="px-1.5 py-0.5 rounded-[5px] text-[10.5px]"
                          :class="activeOrderFilter === 'all' ? 'bg-[#df5826] text-white' : 'bg-[#e6e5e0] text-[#53565a]'"
                          x-text="orders.length">0</span>
                </button>

                <button type="button" @click="activeOrderFilter = 'open_bill'"
                    :class="activeOrderFilter === 'open_bill' ? 'bg-[#fffbf5] border-amber-400 text-amber-900 font-bold shadow-xs' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                    class="border rounded-[9px] px-3.5 py-2 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fas fa-bookmark text-[11px] text-amber-600"></i>
                    <span>Open Bill</span>
                    <span class="px-1.5 py-0.5 rounded-[5px] text-[10.5px]"
                          :class="activeOrderFilter === 'open_bill' ? 'bg-amber-500 text-white font-bold' : 'bg-[#e6e5e0] text-[#53565a]'"
                          x-text="orders.filter(o => o.status === 'HOLD' || o.order_status === 'HOLD' || o.is_open_bill).length">0</span>
                </button>

                <button type="button" @click="activeOrderFilter = 'process'"
                    :class="activeOrderFilter === 'process' ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                    class="border rounded-[9px] px-3.5 py-2 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap">
                    <span>Sedang Diproses</span>
                    <span class="px-1.5 py-0.5 rounded-[5px] text-[10.5px]"
                          :class="activeOrderFilter === 'process' ? 'bg-[#df5826] text-white' : 'bg-[#e6e5e0] text-[#53565a]'"
                          x-text="orders.filter(o => ['Masuk','CONFIRMED','In Progress','PREPARING','Ready','READY'].includes(o.status || o.order_status) && o.status !== 'HOLD' && o.order_status !== 'HOLD' && !o.is_open_bill).length">0</span>
                </button>

                <button type="button" @click="activeOrderFilter = 'completed'"
                    :class="activeOrderFilter === 'completed' ? 'bg-[#fff6f0] border-[#df5826] text-[#df5826] font-semibold' : 'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a] font-medium hover:border-[#8b8e93]'"
                    class="border rounded-[9px] px-3.5 py-2 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap">
                    <span>Selesai</span>
                    <span class="px-1.5 py-0.5 rounded-[5px] text-[10.5px]"
                          :class="activeOrderFilter === 'completed' ? 'bg-[#df5826] text-white' : 'bg-[#e6e5e0] text-[#53565a]'"
                          x-text="orders.filter(o => ['Completed','COMPLETED'].includes(o.status || o.order_status)).length">0</span>
                </button>
            </div>

            <!-- Inline Search Input -->
            <div class="w-full md:w-72 bg-[#f8f9fa] border border-[#d8dce2] rounded-[9px] px-3 py-2 flex items-center gap-2 focus-within:border-[#df5826] focus-within:bg-white transition">
                <img src="{{ asset('images/sparta/search.svg') }}" alt="" class="w-4 h-4 shrink-0">
                <input type="text" x-model="searchQuery" placeholder="Cari nama pelanggan, meja, atau #ID..."
                    class="w-full bg-transparent border-none outline-none text-[12px] font-medium text-[#1e211f] placeholder-[#8b8e93] p-0 focus:ring-0">
            </div>
        </div>
    </div>

    <!-- Orders Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 pb-16 items-start">

        <!-- Empty State -->
        <div x-show="filteredOrders.length === 0" class="col-span-1 md:col-span-2 xl:col-span-3 bg-white border border-[#e6e5e0] rounded-[16px] py-16 px-6 text-center">
            <div class="w-14 h-14 bg-[#fff6f0] border border-[#f5d5c6] rounded-[14px] flex items-center justify-center mx-auto mb-3.5 text-[#df5826]">
                <img src="{{ asset('images/sparta/bill-check.svg') }}" alt="" class="w-6 h-6">
            </div>
            <h3 class="text-[15px] font-bold text-[#1e211f] mb-1">Belum ada pesanan pada kategori ini</h3>
            <p class="text-[12px] text-[#8b8e93] max-w-sm mx-auto">Pesanan baru dari Kasir maupun QR Meja akan otomatis muncul di halaman ini secara real-time.</p>
        </div>

        <!-- Order Card -->
        <template x-for="order in filteredOrders" :key="order.id">
            <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-4 pl-[19px] relative overflow-hidden flex flex-col justify-between shadow-[0px_3px_12px_rgba(38,39,35,0.04)] hover:border-[#df5826]/60 transition-all">

                <!-- Left Accent Bar -->
                <div class="w-[3.5px] absolute left-0 top-0 bottom-0 rounded-l-[16px]"
                     :class="{
                        'bg-amber-500': order.status === 'HOLD' || order.order_status === 'HOLD' || order.is_open_bill,
                        'bg-[#df5826]': ['Masuk', 'CONFIRMED', 'In Progress', 'PREPARING'].includes(order.status) && order.status !== 'HOLD' && !order.is_open_bill,
                        'bg-[#289e5c]': ['Ready', 'READY'].includes(order.status),
                        'bg-[#d8dce2]': ['Completed', 'COMPLETED'].includes(order.status),
                        'bg-red-500': order.status === 'Dibatalkan' || order.status === 'CANCELLED'
                     }"></div>

                <!-- Top Header: Table Badge + Customer/Order Info + Status Badge -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3 min-w-0">
                        <!-- Table / Type Badge -->
                        <div class="w-[38px] h-[38px] rounded-[10px] border flex items-center justify-center shrink-0 font-bold text-[13px]"
                             :class="{
                                'bg-amber-100 border-amber-300 text-amber-900': order.status === 'HOLD' || order.is_open_bill,
                                'bg-[#f8f9fa] border-[#d8dce2] text-[#53565a]': ['Completed', 'COMPLETED'].includes(order.status),
                                'bg-[#fff6f0] border-[#f5d5c6] text-[#df5826]': !['Completed', 'COMPLETED'].includes(order.status) && order.status !== 'HOLD' && !order.is_open_bill
                             }"
                             x-text="order.table && order.table !== '-' ? String(order.table).replace(/^meja\s*/i, '').padStart(2, '0') : 'TA'">
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[14px] font-bold text-[#1e211f] leading-tight"
                                      x-text="order.table && order.table !== 'TA' && order.table !== '-' ? (/^meja/i.test(order.table) ? order.table : 'Meja ' + String(order.table).padStart(2, '0')) : (order.type || 'Takeaway')"></span>
                                <span class="text-[11.5px] font-semibold text-[#8b8e93]" x-text="'#' + order.id"></span>
                            </div>
                            <p class="text-[12px] font-medium text-[#53565a] truncate mt-0.5">
                                <span x-text="order.customer || 'Walk-in Customer'"></span>
                                <span class="text-[#d8dce2] mx-1">•</span>
                                <span class="text-[#8b8e93]" x-text="order.type || 'Dine In'"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Status Pill & Formatted Time -->
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        <span x-show="order.status === 'HOLD' || order.order_status === 'HOLD' || order.is_open_bill"
                              class="px-2.5 py-1 rounded-[6px] bg-[#fffbf5] border border-amber-300 text-amber-900 text-[10.5px] font-bold inline-flex items-center gap-1 shadow-2xs">
                            <i class="fas fa-bookmark text-amber-600 text-[10px]"></i>
                            <span>Open Bill</span>
                        </span>

                        <span x-show="(order.status === 'Masuk' || order.status === 'CONFIRMED') && order.status !== 'HOLD' && !order.is_open_bill"
                              class="px-2.5 py-1 rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[10.5px] font-semibold inline-flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] animate-ping"></span>
                            <span>Pesanan Baru</span>
                        </span>

                        <span x-show="order.status === 'In Progress' || order.status === 'PREPARING'"
                              class="px-2.5 py-1 rounded-[6px] bg-[#fff6f0] text-[#df5826] text-[10.5px] font-semibold">
                            Memasak
                        </span>

                        <span x-show="order.status === 'Ready' || order.status === 'READY'"
                              class="px-2.5 py-1 rounded-[6px] bg-[#eaf7ef] text-[#289e5c] text-[10.5px] font-semibold">
                            Siap Disajikan
                        </span>

                        <span x-show="order.status === 'Completed' || order.status === 'COMPLETED'"
                              class="px-2.5 py-1 rounded-[6px] bg-[#f1f0ec] text-[#53565a] text-[10.5px] font-semibold">
                            Selesai
                        </span>

                        <span x-show="order.status === 'Dibatalkan' || order.status === 'CANCELLED'"
                              class="px-2.5 py-1 rounded-[6px] bg-red-50 text-red-600 text-[10.5px] font-semibold">
                            Dibatalkan
                        </span>

                        <div class="flex items-center gap-1 text-[10.5px] font-medium text-[#8b8e93]">
                            <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3 h-3 opacity-80">
                            <span x-text="formatOrderTime(order.time)"></span>
                        </div>
                    </div>
                </div>

                <!-- Middle: Items Box -->
                <div class="bg-[#f6f5f2] border border-[#e6e5e0] rounded-[11px] p-3 my-3.5">
                    <div class="flex items-center justify-between text-[10px] font-semibold text-[#8b8e93] uppercase tracking-wider pb-2 mb-2 border-b border-[#e6e5e0]">
                        <span>Daftar Menu (<span x-text="(order.items || []).reduce((s, i) => s + Number(i.qty || 1), 0)"></span> item)</span>
                        <span>Subtotal</span>
                    </div>

                    <div class="space-y-2 max-h-40 overflow-y-auto hide-scroll">
                        <template x-for="(item, iIdx) in (order.items || [])" :key="iIdx">
                            <div class="flex items-start justify-between gap-2 text-[12px]"
                                 :class="['Completed', 'COMPLETED', 'Dibatalkan', 'CANCELLED'].includes(order.status) ? 'text-[#8b8e93] line-through' : 'text-[#1e211f]'">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-[#df5826] shrink-0" x-text="item.qty + '×'"></span>
                                        <span class="font-medium truncate" x-text="item.name"></span>
                                    </div>
                                    <p x-show="item.notes" class="text-[10.5px] text-[#df5826] bg-[#fff6f0] border border-[#f5d5c6] rounded px-1.5 py-0.5 mt-1 inline-block not-italic" x-text="'Catatan: ' + item.notes"></p>
                                </div>
                                <span class="font-semibold text-[#111827] shrink-0 whitespace-nowrap" x-text="formatRp((item.price || 0) * (item.qty || 1))"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Progress & Total Summary -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-[11.5px] text-[#53565a]">
                            <img src="{{ asset('images/sparta/bill-check.svg') }}" alt="" class="w-4 h-4">
                            <span>Total Pembayaran</span>
                        </div>
                        <span class="text-[16px] font-bold text-[#111827] tracking-[-0.16px]" x-text="formatRp(order.total)"></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full h-[5px] bg-[#e6e5e0] rounded-[3px] overflow-hidden">
                        <div class="h-full rounded-[3px] transition-all duration-300"
                             :class="{
                                'bg-amber-500': order.status === 'HOLD' || order.order_status === 'HOLD' || order.is_open_bill,
                                'bg-[#289e5c]': ['Ready', 'READY', 'Completed', 'COMPLETED'].includes(order.status),
                                'bg-[#df5826]': !['Ready', 'READY', 'Completed', 'COMPLETED'].includes(order.status) && order.status !== 'HOLD' && !order.is_open_bill
                             }"
                             :style="'width: ' + ((order.status === 'HOLD' || order.is_open_bill) ? '40%' : (['Completed', 'COMPLETED'].includes(order.status) ? '100%' : (['Ready', 'READY'].includes(order.status) ? '90%' : (['In Progress', 'PREPARING'].includes(order.status) ? '65%' : '35%'))))"></div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-1">
                        <!-- 1. OPEN BILL ORDERS (HOLD / UNPAID) -->
                        <template x-if="order.status === 'HOLD' || order.order_status === 'HOLD' || order.is_open_bill">
                            <div class="space-y-2 w-full">
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button"
                                       @click="openEditOpenBillModal(order)"
                                       class="py-[9px] px-3 rounded-[9px] bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 text-[12px] font-bold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                        <i class="fas fa-plus text-[10px] text-amber-700"></i>
                                        <span>+ Tambah Menu</span>
                                    </button>
                                    <a :href="'/admin/pos?recall=' + order.id + '&pay=1'"
                                       class="py-[9px] px-3 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-bold shadow-sm hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                        <i class="fas fa-cash-register text-[11px]"></i>
                                        <span>Bayar di Kasir</span>
                                    </a>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="viewOrderDetails(order)"
                                            class="flex-1 py-[7px] px-2 rounded-[8px] border border-[#d8dce2] bg-white hover:bg-[#f8f9fa] text-[#53565a] text-[11px] font-semibold transition text-center">
                                        Detail
                                    </button>
                                    <button type="button" @click="window.open('/admin/api/orders/' + order.id + '/print?type=kitchen', '_blank')"
                                            class="py-[7px] px-2.5 rounded-[8px] border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-semibold transition flex items-center gap-1"
                                            title="Cetak Tiket Dapur / Bar">
                                        <i class="fas fa-utensils text-[10px]"></i>
                                        <span>Dapur</span>
                                    </button>
                                    <button type="button" @click="window.open('/admin/api/orders/' + order.id + '/print?type=customer', '_blank')"
                                            class="py-[7px] px-2.5 rounded-[8px] border border-blue-200 bg-blue-50 hover:bg-blue-100 text-blue-800 text-[11px] font-semibold transition flex items-center gap-1"
                                            title="Cetak Bill Sementara">
                                        <i class="fas fa-receipt text-[10px]"></i>
                                        <span>Bill</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- 2. STANDARD ORDERS (MASUK / DIPROSES / SELESAI / BATAL) -->
                        <template x-if="order.status !== 'HOLD' && order.order_status !== 'HOLD' && !order.is_open_bill">
                            <div class="space-y-1.5 w-full">
                                <div class="grid grid-cols-2 gap-2.5">
                                    <button type="button" @click="viewOrderDetails(order)"
                                            class="py-[9px] px-3 rounded-[9px] border border-[#d8dce2] bg-white hover:bg-[#f8f9fa] text-[#1e211f] text-[12px] font-semibold transition flex items-center justify-center">
                                        Lihat Detail
                                    </button>

                                    <template x-if="order.status === 'Masuk' || order.status === 'CONFIRMED'">
                                        <button type="button" @click="updateStatus(order.id, 'In Progress')"
                                                class="py-[9px] px-3 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-semibold shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                            <span>Terima &amp; Proses</span>
                                        </button>
                                    </template>

                                    <template x-if="order.status === 'In Progress' || order.status === 'PREPARING'">
                                        <button type="button" @click="updateStatus(order.id, 'Ready')"
                                                class="py-[9px] px-3 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-semibold shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition flex items-center justify-center gap-1.5">
                                            <img src="{{ asset('images/sparta/check-white.svg') }}" alt="" class="w-3.5 h-3.5">
                                            <span>Siap Disajikan</span>
                                        </button>
                                    </template>

                                    <template x-if="order.status === 'Ready' || order.status === 'READY'">
                                        <button type="button" @click="updateStatus(order.id, 'Completed')"
                                                class="py-[9px] px-3 rounded-[9px] bg-[#289e5c] hover:bg-[#21854c] border border-[#289e5c] text-white text-[12px] font-semibold shadow-sm transition flex items-center justify-center gap-1.5">
                                            <img src="{{ asset('images/sparta/check-white.svg') }}" alt="" class="w-3.5 h-3.5">
                                            <span>Selesaikan</span>
                                        </button>
                                    </template>

                                    <template x-if="order.status === 'Completed' || order.status === 'COMPLETED'">
                                        <button type="button" disabled
                                                class="py-[9px] px-3 rounded-[9px] bg-[#f1f0ec] border border-[#e6e5e0] text-[#8b8e93] text-[12px] font-semibold cursor-not-allowed flex items-center justify-center">
                                            Selesai
                                        </button>
                                    </template>

                                    <template x-if="order.status === 'Dibatalkan' || order.status === 'CANCELLED'">
                                        <button type="button" disabled
                                                class="py-[9px] px-3 rounded-[9px] bg-red-50 border border-red-200 text-red-400 text-[12px] font-semibold cursor-not-allowed flex items-center justify-center">
                                            Dibatalkan
                                        </button>
                                    </template>
                                </div>

                                <!-- Shortcut to open in POS and add menu items if still active -->
                                <template x-if="['Masuk', 'CONFIRMED', 'In Progress', 'PREPARING'].includes(order.status || order.order_status)">
                                    <button type="button"
                                       @click="openEditOpenBillModal(order)"
                                       class="w-full py-[6px] px-2.5 rounded-[8px] bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-[11px] font-semibold transition flex items-center justify-center gap-1 cursor-pointer">
                                        <i class="fas fa-edit text-[9.5px] text-amber-700"></i>
                                        <span>+ Tambah Menu ke Pesanan Ini</span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- MODAL: TAMBAH PESANAN OPEN BILL (DIRECT POPUP)              -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div x-show="openBillModal.show" 
         x-cloak 
         @keydown.escape.window="closeOpenBillModal()"
         class="fixed inset-0 z-[100] bg-black/60 backdrop-blur-xs flex items-center justify-center p-2 sm:p-4 md:p-6 overflow-y-auto font-sans">
        
        <div class="bg-white rounded-[24px] shadow-2xl border border-[#e6e5e0] w-full max-w-6xl max-h-[94vh] flex flex-col overflow-hidden relative"
             @click.outside="closeOpenBillModal()">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 sm:px-6 py-4 border-b border-[#e6e5e0] shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <i class="fas fa-bookmark text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-[18px] sm:text-[20px] font-bold text-[#111827] leading-tight">
                            Tambah Pesanan Open Bill
                        </h2>
                        <p class="text-[11.5px] text-[#7f837e] mt-0.5">
                            <span x-show="openBillModal.recalledOrderId">
                                Menambahkan menu ke Open Bill <strong class="text-[#df5826]" x-text="'#' + openBillModal.recalledOrderId"></strong>
                            </span>
                            <span x-show="!openBillModal.recalledOrderId">
                                Buat pesanan berjalan baru tanpa perlu berpindah ke halaman kasir
                            </span>
                        </p>
                    </div>
                </div>

                <button type="button" 
                        @click="closeOpenBillModal()" 
                        class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-800 flex items-center justify-center transition cursor-pointer"
                        title="Tutup">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Modal Content (2 Columns Layout) -->
            <div class="flex flex-col md:flex-row flex-1 overflow-hidden min-h-0">
                
                <!-- LEFT COLUMN: Catalog / Product Picker -->
                <div class="flex-1 flex flex-col p-4 sm:p-5 overflow-hidden min-w-0 bg-white">
                    
                    <!-- Search & Categories Row -->
                    <div class="space-y-3 mb-4 shrink-0">
                        <!-- Search Box -->
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-[#f8f9fa] border border-[#e6e5e0] focus-within:border-[#df5826] focus-within:bg-white rounded-[12px] h-[40px] px-3.5 flex items-center gap-2.5 transition shadow-2xs">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                                <input type="text" 
                                       x-model="openBillModal.searchQuery" 
                                       placeholder="Cari nama menu..." 
                                       class="bg-transparent border-none outline-none text-[12.5px] text-[#111827] placeholder-[#7f837e] w-full p-0">
                                <button type="button" 
                                        x-show="openBillModal.searchQuery" 
                                        @click="openBillModal.searchQuery = ''" 
                                        class="text-gray-400 hover:text-gray-600 text-xs">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto hide-scroll pb-1">
                            <button type="button" 
                                    @click="openBillModal.selectedCategory = 'all'"
                                    :class="openBillModal.selectedCategory === 'all' ? 'bg-[#111827] text-white font-bold shadow-xs' : 'bg-[#f8f9fa] border border-[#e6e5e0] text-[#53565a] hover:bg-gray-100 font-medium'"
                                    class="rounded-[10px] px-3.5 py-1.5 text-[12px] whitespace-nowrap transition cursor-pointer">
                                All
                            </button>

                            <button type="button" 
                                    @click="openBillModal.selectedCategory = 'best_seller'"
                                    :class="openBillModal.selectedCategory === 'best_seller' ? 'bg-[#df5826] text-white font-bold shadow-xs' : 'bg-[#f8f9fa] border border-[#e6e5e0] text-[#53565a] hover:bg-gray-100 font-medium'"
                                    class="rounded-[10px] px-3.5 py-1.5 text-[12px] whitespace-nowrap transition cursor-pointer flex items-center gap-1.5">
                                <i class="fas fa-fire text-xs" :class="openBillModal.selectedCategory === 'best_seller' ? 'text-amber-200' : 'text-[#df5826]'"></i>
                                <span>Best Seller</span>
                            </button>

                            <template x-for="cat in openBillModal.categories" :key="cat.id">
                                <button type="button" 
                                        @click="openBillModal.selectedCategory = cat.id"
                                        :class="String(openBillModal.selectedCategory) === String(cat.id) ? 'bg-[#111827] text-white font-bold shadow-xs' : 'bg-[#f8f9fa] border border-[#e6e5e0] text-[#53565a] hover:bg-gray-100 font-medium'"
                                        class="rounded-[10px] px-3.5 py-1.5 text-[12px] whitespace-nowrap transition cursor-pointer"
                                        x-text="cat.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="flex-1 overflow-y-auto pr-1 min-h-[280px]">
                        
                        <!-- Loading State -->
                        <div x-show="openBillModal.isLoading" class="py-16 text-center text-gray-400">
                            <i class="fas fa-spinner fa-spin text-2xl text-[#df5826] mb-2"></i>
                            <p class="text-xs">Memuat daftar menu...</p>
                        </div>

                        <!-- Empty Products -->
                        <div x-show="!openBillModal.isLoading && filteredOpenBillProducts().length === 0" class="py-16 text-center text-gray-400">
                            <i class="fas fa-utensils text-2xl mb-2 opacity-40"></i>
                            <p class="text-xs">Tidak ada menu yang sesuai dengan filter.</p>
                        </div>

                        <!-- Grid -->
                        <div x-show="!openBillModal.isLoading && filteredOpenBillProducts().length > 0" 
                             class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pb-4">
                            <template x-for="p in filteredOpenBillProducts()" :key="p.id">
                                <div class="bg-white border border-[#e6e5e0] hover:border-[#df5826] rounded-[16px] p-2.5 flex flex-col justify-between transition-all duration-200 hover:shadow-md group">
                                    <div>
                                        <!-- Product Image -->
                                        <div class="w-full aspect-[4/3] rounded-[12px] overflow-hidden bg-gray-100 mb-2.5 relative">
                                            <template x-if="p.image_url">
                                                <img :src="p.image_url" :alt="p.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            </template>
                                            <template x-if="!p.image_url">
                                                <div class="w-full h-full flex items-center justify-center bg-[#f8f9fa] text-gray-300">
                                                    <i class="fas fa-utensils text-xl"></i>
                                                </div>
                                            </template>

                                            <!-- Badges -->
                                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                                <span x-show="p.has_active_discount" class="bg-red-500 text-white text-[9.5px] font-black px-1.5 py-0.5 rounded-full shadow-xs">
                                                    PROMO
                                                </span>
                                                <span x-show="p.is_best_seller" class="bg-amber-500 text-white text-[9.5px] font-bold px-1.5 py-0.5 rounded-full shadow-xs flex items-center gap-0.5">
                                                    <i class="fas fa-star text-[8px]"></i> Top
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <h4 class="font-bold text-[13px] text-[#111827] leading-tight line-clamp-1" x-text="p.name"></h4>
                                        
                                        <!-- Category & Sold Info -->
                                        <p class="text-[11px] text-[#7f837e] mt-0.5">
                                            <span x-text="p.category_name || 'Menu'"></span>
                                            <span class="mx-0.5">•</span>
                                            <span x-text="(p.sold_count || 0) + ' sold'"></span>
                                        </p>
                                    </div>

                                    <!-- Price & Stacked Serving Buttons -->
                                    <div class="mt-2 pt-2 border-t border-[#f0eee9] flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[13px] text-[#111827]" x-text="'Rp ' + formatRp(p.effective_price || p.price)"></span>
                                            <template x-if="p.has_active_discount">
                                                <span class="text-[10px] text-gray-400 line-through" x-text="'Rp ' + formatRp(p.price)"></span>
                                            </template>
                                        </div>

                                        <div class="flex flex-col gap-1 w-full">
                                            <button type="button" 
                                                    @click="addToOpenBillCart(p, 'DINE_IN')"
                                                    class="w-full py-1.5 px-2.5 rounded-[8px] bg-gradient-to-b from-[#f06428] to-[#df5826] hover:opacity-95 text-white text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-utensils text-[9px]"></i>
                                                <span>Dine In</span>
                                            </button>
                                            <button type="button" 
                                                    @click="addToOpenBillCart(p, 'TAKEAWAY')"
                                                    class="w-full py-1.5 px-2.5 rounded-[8px] bg-white hover:bg-[#fff6f0] border border-[#df5826] text-[#df5826] text-[11px] font-bold flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-shopping-bag text-[9px]"></i>
                                                <span>Takeaway</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: CURRENT ORDER (Matching Layout & Segments) -->
                <div class="w-full md:w-[360px] lg:w-[400px] shrink-0 border-t md:border-t-0 md:border-l border-[#e6e5e0] bg-[#fafaf9] flex flex-col justify-between p-4 lg:p-5 overflow-y-auto">
                    
                    <div class="space-y-3">
                        <!-- Panel Title -->
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-[10px] font-bold tracking-wider text-[#8b8e93] uppercase">CURRENT ORDER</h3>
                                <p class="text-[14px] font-bold text-[#111827] leading-tight" x-text="openBillModal.recalledOrderId ? 'Pesanan #' + openBillModal.recalledOrderId : 'Pesanan Baru'"></p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="openBillModal.cart = []" class="px-2 py-1 rounded-[7px] border border-[#f5d5c6] bg-[#fff6f0] text-[11px] font-semibold text-[#df5826] hover:opacity-80 transition cursor-pointer">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <!-- Customer Name & Table Number (Side by Side matching layout) -->
                        <div class="grid grid-cols-2 gap-2 pt-0.5">
                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#53565a] mb-1">Customer name</label>
                                <input type="text" 
                                       x-model="openBillModal.customerName" 
                                       placeholder="Walk-in Customer"
                                       class="w-full bg-white border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[8px] px-2.5 py-1.5 text-[11.5px] font-semibold text-[#111827] placeholder-[#8b8e93] outline-none transition">
                            </div>

                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#53565a] mb-1">Table number</label>
                                <div class="relative">
                                    <input type="text" 
                                           x-model="openBillModal.selectedTable" 
                                           list="openbill-table-options"
                                           placeholder="Pilih Meja" 
                                           class="w-full bg-white border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[8px] px-2.5 py-1.5 text-[11.5px] font-semibold text-[#111827] outline-none transition">
                                    <datalist id="openbill-table-options">
                                        <template x-for="t in tables" :key="t.id">
                                            <option :value="t.name || t.id"></option>
                                        </template>
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        <!-- Segmented Control: [ Direct Order ] & [ Delivery ] (Circled in Cyan) -->
                        <div class="bg-[#f3f4f6] p-[3px] rounded-[10px] grid grid-cols-2 gap-[3px]">
                            <button type="button" @click="openBillModal.fulfillmentType = 'DINE_IN'"
                                :class="openBillModal.fulfillmentType !== 'DELIVERY' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                                class="py-1.5 px-3 rounded-[8px] text-[11.5px] text-center transition cursor-pointer">
                                Direct Order
                            </button>
                            <button type="button" @click="openBillModal.fulfillmentType = 'DELIVERY'"
                                :class="openBillModal.fulfillmentType === 'DELIVERY' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-[#53565a] font-medium hover:text-[#111827]'"
                                class="py-1.5 px-3 rounded-[8px] text-[11.5px] text-center transition cursor-pointer">
                                Delivery
                            </button>
                        </div>

                        <!-- Cart Items List (Separated with clean dividers & labels) -->
                        <div class="flex flex-col gap-2.5 max-h-[36vh] overflow-y-auto pr-1">
                            <template x-if="openBillModal.cart.length === 0">
                                <div class="py-8 text-center text-gray-400 bg-white border border-dashed border-[#d8dce2] rounded-[12px] p-4">
                                    <i class="fas fa-shopping-basket text-2xl mb-1.5 opacity-30"></i>
                                    <p class="text-[11.5px] font-semibold text-gray-600">Keranjang masih kosong</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Pilih menu di samping untuk menambahkan ke pesanan.</p>
                                </div>
                            </template>

                            {{-- 1. DINE IN SECTION --}}
                            <template x-if="openBillModal.cart.some(i => (i.serving_type || 'DINE_IN') !== 'TAKEAWAY')">
                                <div class="flex items-center justify-between px-1 pt-1 pb-0.5 border-b border-[#e6e5e0]">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-[#1e211f]">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <i class="fas fa-utensils text-[9.5px] text-emerald-600"></i>
                                        <span>Makan di Tempat (Dine In)</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-[#8b8e93]" x-text="openBillModal.cart.filter(i => (i.serving_type || 'DINE_IN') !== 'TAKEAWAY').reduce((s,i)=>s+parseInt(i.qty||1),0) + ' item'"></span>
                                </div>
                            </template>

                            <template x-for="(item, idx) in openBillModal.cart" :key="'ob-dinein-' + idx">
                                <div x-show="(item.serving_type || 'DINE_IN') !== 'TAKEAWAY'" class="bg-white border border-[#d8dce2] rounded-[10px] p-2.5 flex flex-col gap-1.5 shadow-2xs transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="w-[30px] h-[30px] rounded-[6px] bg-[#ede7dc] overflow-hidden shrink-0 flex items-center justify-center">
                                            <template x-if="item.image">
                                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image">
                                                <span class="text-[10px] font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-[12.5px] font-semibold text-[#111827] leading-tight truncate" x-text="item.name"></p>
                                            
                                            <div class="mt-0.5 flex items-center gap-1">
                                                <button type="button" @click="toggleOpenBillItemServingType(idx)"
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-[4px] bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-[9.5px] font-bold transition cursor-pointer"
                                                    title="Klik untuk pindah ke Takeaway">
                                                    <i class="fas fa-utensils text-[8px]"></i>
                                                    <span>Dine In</span>
                                                    <i class="fas fa-sync-alt text-[7px] opacity-60 ml-0.5"></i>
                                                </button>
                                                <span x-show="item.variant_name" class="text-[10px] text-[#53565a] truncate" x-text="'· ' + item.variant_name"></span>
                                            </div>
                                        </div>

                                        <button type="button" @click="openBillModal.cart.splice(idx, 1)" class="w-[20px] h-[20px] flex items-center justify-center shrink-0 hover:opacity-75 transition cursor-pointer" title="Hapus item">
                                            <img src="{{ asset('images/sparta/pos-trash-red.svg') }}" alt="Hapus" class="w-[14px] h-[14px]">
                                        </button>
                                    </div>

                                    {{-- Collapsed Note Trigger --}}
                                    <button type="button"
                                        x-show="!item.showNotes && !item.notes"
                                        @click="item.showNotes = true"
                                        class="flex items-center gap-1 text-left w-fit hover:opacity-80 transition cursor-pointer py-0.5">
                                        <img src="{{ asset('images/sparta/pos-doc-add.svg') }}" alt="" class="w-[12px] h-[12px]">
                                        <span class="text-[10.5px] font-semibold text-[#df5826]">Buat Catatan Customer</span>
                                    </button>

                                    {{-- Expanded Note Box --}}
                                    <div x-show="item.showNotes || item.notes" class="bg-[#f8f9fa] rounded-[8px] pt-2 pb-1.5 px-2 flex flex-col gap-1.5">
                                        <div class="relative border border-[#d8dce2] rounded-[6px] p-1.5 bg-[#f8f9fa]">
                                            <label class="text-[10px] font-medium text-[#111827] block mb-0.5">Catatan customer (Opsional)</label>
                                            <textarea x-model="item.notes" rows="2" placeholder="Contoh: tanpa gula, es sedikit"
                                                class="w-full bg-transparent border-none outline-none text-[10.5px] text-[#111827] placeholder-[#8b8e93] resize-none p-0 focus:ring-0"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <button type="button" @click="item.notes = ''; item.showNotes = false"
                                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-trash-dark.svg') }}" alt="" class="w-[12px] h-[12px]">
                                                <span class="text-[10.5px] font-semibold text-[#111827]">Hapus Catatan</span>
                                            </button>
                                            <button type="button" @click="splitOpenBillItem(idx)" :disabled="item.qty <= 1"
                                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition disabled:opacity-40 cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-split.svg') }}" alt="" class="w-[12px] h-[12px]">
                                                <span class="text-[10.5px] font-semibold text-[#111827]">Pisahkan 1 Item</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-0.5">
                                        <span class="text-[12.5px] font-bold text-[#111827]" x-text="'Rp ' + formatRp(item.price * item.qty)"></span>
                                        <div class="bg-[#f8f9fa] border border-[#d8dce2] rounded-full p-[2px] px-1 flex items-center gap-1 shadow-2xs">
                                            <button type="button" @click="if (item.qty > 1) { item.qty--; } else { openBillModal.cart.splice(idx, 1); }"
                                                class="w-[24px] h-[24px] rounded-full flex items-center justify-center hover:bg-gray-200 active:scale-90 transition cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-minus.svg') }}" alt="-" class="w-[11px] h-[11px]">
                                            </button>
                                            <span class="w-[18px] text-center text-[12px] font-bold text-[#111827]" x-text="item.qty"></span>
                                            <button type="button" @click="item.qty++"
                                                class="w-[24px] h-[24px] rounded-full bg-[#111827] hover:bg-[#df5826] active:scale-90 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-plus.svg') }}" alt="+" class="w-[11px] h-[11px]">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- 2. TAKEAWAY SECTION --}}
                            <template x-if="openBillModal.cart.some(i => (i.serving_type || 'DINE_IN') === 'TAKEAWAY')">
                                <div class="flex items-center justify-between px-1 pt-2 pb-0.5 border-b border-[#e6e5e0] mt-1">
                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-[#df5826]">
                                        <span class="w-2 h-2 rounded-full bg-[#df5826]"></span>
                                        <i class="fas fa-shopping-bag text-[9.5px]"></i>
                                        <span>Bungkus (Takeaway)</span>
                                    </div>
                                    <span class="text-[10px] font-semibold text-[#8b8e93]" x-text="openBillModal.cart.filter(i => (i.serving_type || 'DINE_IN') === 'TAKEAWAY').reduce((s,i)=>s+parseInt(i.qty||1),0) + ' item'"></span>
                                </div>
                            </template>

                            <template x-for="(item, idx) in openBillModal.cart" :key="'ob-takeaway-' + idx">
                                <div x-show="(item.serving_type || 'DINE_IN') === 'TAKEAWAY'" class="bg-[#fffdfb] border border-[#f5d5c6] rounded-[10px] p-2.5 flex flex-col gap-1.5 shadow-2xs transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="w-[30px] h-[30px] rounded-[6px] bg-[#fff0e6] overflow-hidden shrink-0 flex items-center justify-center">
                                            <template x-if="item.image">
                                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!item.image">
                                                <span class="text-[10px] font-bold text-[#df5826]" x-text="(item.name || 'M').substring(0, 2).toUpperCase()"></span>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-[12.5px] font-semibold text-[#111827] leading-tight truncate" x-text="item.name"></p>
                                            
                                            <div class="mt-0.5 flex items-center gap-1">
                                                <button type="button" @click="toggleOpenBillItemServingType(idx)"
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-[4px] bg-[#fff0e6] hover:bg-[#ffe2d1] text-[#df5826] border border-[#ffd2b8] text-[9.5px] font-bold transition cursor-pointer"
                                                    title="Klik untuk pindah ke Dine In">
                                                    <i class="fas fa-shopping-bag text-[8px]"></i>
                                                    <span>Takeaway</span>
                                                    <i class="fas fa-sync-alt text-[7px] opacity-60 ml-0.5"></i>
                                                </button>
                                                <span x-show="item.variant_name" class="text-[10px] text-[#53565a] truncate" x-text="'· ' + item.variant_name"></span>
                                            </div>
                                        </div>

                                        <button type="button" @click="openBillModal.cart.splice(idx, 1)" class="w-[20px] h-[20px] flex items-center justify-center shrink-0 hover:opacity-75 transition cursor-pointer" title="Hapus item">
                                            <img src="{{ asset('images/sparta/pos-trash-red.svg') }}" alt="Hapus" class="w-[14px] h-[14px]">
                                        </button>
                                    </div>

                                    {{-- Collapsed Note Trigger --}}
                                    <button type="button"
                                        x-show="!item.showNotes && !item.notes"
                                        @click="item.showNotes = true"
                                        class="flex items-center gap-1 text-left w-fit hover:opacity-80 transition cursor-pointer py-0.5">
                                        <img src="{{ asset('images/sparta/pos-doc-add.svg') }}" alt="" class="w-[12px] h-[12px]">
                                        <span class="text-[10.5px] font-semibold text-[#df5826]">Buat Catatan Customer</span>
                                    </button>

                                    {{-- Expanded Note Box --}}
                                    <div x-show="item.showNotes || item.notes" class="bg-[#f8f9fa] rounded-[8px] pt-2 pb-1.5 px-2 flex flex-col gap-1.5">
                                        <div class="relative border border-[#d8dce2] rounded-[6px] p-1.5 bg-[#f8f9fa]">
                                            <label class="text-[10px] font-medium text-[#111827] block mb-0.5">Catatan customer (Opsional)</label>
                                            <textarea x-model="item.notes" rows="2" placeholder="Contoh: tanpa gula, es sedikit"
                                                class="w-full bg-transparent border-none outline-none text-[10.5px] text-[#111827] placeholder-[#8b8e93] resize-none p-0 focus:ring-0"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <button type="button" @click="item.notes = ''; item.showNotes = false"
                                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-trash-dark.svg') }}" alt="" class="w-[12px] h-[12px]">
                                                <span class="text-[10.5px] font-semibold text-[#111827]">Hapus Catatan</span>
                                            </button>
                                            <button type="button" @click="splitOpenBillItem(idx)" :disabled="item.qty <= 1"
                                                class="border border-[#d8dce2] rounded-[7px] py-1 px-1.5 flex items-center justify-center gap-1 bg-white hover:bg-gray-50 transition disabled:opacity-40 cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-split.svg') }}" alt="" class="w-[12px] h-[12px]">
                                                <span class="text-[10.5px] font-semibold text-[#111827]">Pisahkan 1 Item</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-0.5">
                                        <span class="text-[12.5px] font-bold text-[#df5826]" x-text="'Rp ' + formatRp(item.price * item.qty)"></span>
                                        <div class="bg-[#f8f9fa] border border-[#d8dce2] rounded-full p-[2px] px-1 flex items-center gap-1 shadow-2xs">
                                            <button type="button" @click="if (item.qty > 1) { item.qty--; } else { openBillModal.cart.splice(idx, 1); }"
                                                class="w-[24px] h-[24px] rounded-full flex items-center justify-center hover:bg-gray-200 active:scale-90 transition cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-minus.svg') }}" alt="-" class="w-[11px] h-[11px]">
                                            </button>
                                            <span class="w-[18px] text-center text-[12px] font-bold text-[#111827]" x-text="item.qty"></span>
                                            <button type="button" @click="item.qty++"
                                                class="w-[24px] h-[24px] rounded-full bg-[#df5826] hover:bg-[#cb4c1d] active:scale-90 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                                <img src="{{ asset('images/sparta/pos-plus.svg') }}" alt="+" class="w-[11px] h-[11px]">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Price Breakdown & Submit Button (Matching Layout) -->
                    <div class="mt-3 pt-3 border-t border-[#e6e5e0] space-y-2">
                        <div class="flex items-center justify-between text-[11.5px] text-[#53565a]">
                            <span>Subtotal</span>
                            <span class="font-semibold text-[#111827]" x-text="'Rp ' + formatRp(openBillCartTotal())"></span>
                        </div>
                        <div class="flex items-center justify-between text-[11.5px] text-[#53565a]">
                            <span>Tax (10%)</span>
                            <span class="font-semibold text-[#111827]" x-text="'Rp ' + formatRp(Math.round(openBillCartTotal() * 0.1))"></span>
                        </div>
                        <div class="flex items-center justify-between text-[11.5px] text-[#53565a]">
                            <span>Additional fee</span>
                            <span class="font-medium text-[#8b8e93]">-</span>
                        </div>

                        <div class="border-t border-[#e6e5e0] pt-2 flex items-baseline justify-between">
                            <span class="text-[13px] font-bold text-[#111827]">Total</span>
                            <span class="text-[18px] font-black text-[#df5826] tracking-tight" x-text="'Rp ' + formatRp(openBillCartTotal() + Math.round(openBillCartTotal() * 0.1))"></span>
                        </div>

                        <button type="button" 
                                @click="submitOpenBill()" 
                                :disabled="openBillModal.isSaving || openBillModal.cart.length === 0"
                                class="w-full py-3 px-4 rounded-[12px] bg-gradient-to-b from-[#f06428] to-[#df5826] hover:from-[#e0571e] hover:to-[#cb4c1d] text-white text-[13px] font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.99] mt-2">
                            <i class="fas fa-shopping-cart text-xs" x-show="!openBillModal.isSaving"></i>
                            <i class="fas fa-spinner fa-spin text-xs" x-show="openBillModal.isSaving" x-cloak></i>
                            <span x-text="openBillModal.isSaving ? 'Menyimpan...' : (openBillModal.recalledOrderId ? 'Perbarui Open Bill' : 'Buat Pesanan')"></span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- MODIFIER & VARIANT MODAL OVERLAY (Sub-modal) -->
            <div x-show="openBillModal.modifierModal.show" 
                 x-cloak 
                 class="absolute inset-0 z-20 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 max-w-sm w-full shadow-2xl space-y-4"
                     @click.outside="openBillModal.modifierModal.show = false">
                    
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-[15px] text-[#111827]" x-text="openBillModal.modifierModal.product?.name"></h4>
                            <p class="text-[12px] text-[#df5826] font-semibold mt-0.5" x-text="formatRp(openBillModal.modifierModal.product?.effective_price || openBillModal.modifierModal.product?.price)"></p>
                        </div>
                        <button type="button" @click="openBillModal.modifierModal.show = false" class="text-gray-400 hover:text-gray-600 text-sm">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Variants -->
                    <template x-if="openBillModal.modifierModal.product?.variants?.length > 0">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Pilih Varian</label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="v in openBillModal.modifierModal.product.variants" :key="v.id">
                                    <button type="button" 
                                            @click="openBillModal.modifierModal.selectedVariant = v"
                                            :class="openBillModal.modifierModal.selectedVariant?.id === v.id ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] font-bold' : 'border-[#e6e5e0] text-gray-700 hover:bg-gray-50'"
                                            class="border rounded-[10px] p-2 text-left text-[11.5px] transition cursor-pointer">
                                        <div x-text="v.name"></div>
                                        <div class="text-[10px] text-gray-500" x-show="v.price_adjustment > 0" x-text="'+' + formatRp(v.price_adjustment)"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Modifier Groups -->
                    <template x-for="group in (openBillModal.modifierModal.product?.modifier_groups ?? [])" :key="group.id">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5" x-text="group.name"></label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="mod in (group.modifiers ?? [])" :key="mod.id">
                                    <button type="button" 
                                            @click="toggleOpenBillModifier(mod)"
                                            :class="openBillModal.modifierModal.selectedModifiers.some(m => m.id === mod.id) ? 'border-[#df5826] bg-[#fff6f0] text-[#df5826] font-bold' : 'border-[#e6e5e0] text-gray-700 hover:bg-gray-50'"
                                            class="border rounded-[10px] p-2 text-left text-[11.5px] transition cursor-pointer">
                                        <div x-text="mod.name"></div>
                                        <div class="text-[10px] text-gray-500" x-show="mod.price_adjustment > 0" x-text="'+' + formatRp(mod.price_adjustment)"></div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Notes -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                        <input type="text" 
                               x-model="openBillModal.modifierModal.notes" 
                               placeholder="Contoh: Less ice, gula terpisah..." 
                               class="w-full bg-[#f8f9fa] border border-[#e6e5e0] focus:border-[#df5826] focus:bg-white rounded-[8px] px-3 py-1.5 text-[12px] outline-none">
                    </div>

                    <!-- Quantity & Confirm -->
                    <div class="flex items-center gap-3 pt-2">
                        <div class="flex items-center border border-[#e6e5e0] rounded-[8px] bg-[#f8f9fa] p-1">
                            <button type="button" @click="if (openBillModal.modifierModal.qty > 1) openBillModal.modifierModal.qty--" class="w-6 h-6 flex items-center justify-center text-xs text-gray-600">
                                <i class="fas fa-minus"></i>
                            </button>
                            <span class="w-6 text-center font-bold text-xs" x-text="openBillModal.modifierModal.qty"></span>
                            <button type="button" @click="openBillModal.modifierModal.qty++" class="w-6 h-6 flex items-center justify-center text-xs text-gray-600">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <button type="button" 
                                @click="saveOpenBillModifier()"
                                class="flex-1 py-2.5 px-4 rounded-[10px] bg-[#df5826] hover:bg-[#cb4c1d] text-white font-bold text-[12px] shadow-sm transition text-center cursor-pointer">
                            Tambahkan
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
