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

                                    <!-- Price & Add Button Row -->
                                    <div class="flex items-center justify-between gap-1.5 mt-2.5 pt-2 border-t border-gray-100">
                                        <div>
                                            <span class="font-bold text-[13.5px] text-[#111827]" x-text="formatRp(p.effective_price || p.price)"></span>
                                            <template x-if="p.has_active_discount">
                                                <span class="block text-[10px] text-gray-400 line-through" x-text="formatRp(p.price)"></span>
                                            </template>
                                        </div>

                                        <button type="button" 
                                                @click="addToOpenBillCart(p)"
                                                class="w-7 h-7 rounded-full bg-[#111827] group-hover:bg-[#df5826] text-white flex items-center justify-center text-xs transition shadow-xs cursor-pointer active:scale-95 shrink-0"
                                                title="Tambah ke pesanan">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: CURRENT ORDER (Cart Summary & Actions) -->
                <div class="w-full md:w-[360px] lg:w-[400px] shrink-0 border-t md:border-t-0 md:border-l border-[#e6e5e0] bg-[#fafaf9] flex flex-col justify-between p-4 lg:p-5 overflow-y-auto">
                    
                    <div class="space-y-3.5">
                        <!-- Panel Title -->
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black text-[#df5826] tracking-wider uppercase">CURRENT ORDER</span>
                            <span class="text-[11.5px] font-semibold text-gray-500" x-text="openBillModal.cart.reduce((s, i) => s + parseInt(i.qty || 1), 0) + ' Item'"></span>
                        </div>

                        <!-- Customer & Table Header Card -->
                        <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-3.5 shadow-2xs space-y-2.5">
                            
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <!-- Table Badge / Icon -->
                                    <div class="w-9 h-9 rounded-[10px] bg-amber-100 text-amber-900 border border-amber-300 flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="fas fa-chair" x-show="!openBillModal.selectedTable"></i>
                                        <span x-show="openBillModal.selectedTable" x-text="String(openBillModal.selectedTable).replace(/^mejas*/i, '').padStart(2, '0')"></span>
                                    </div>

                                    <div class="min-w-0">
                                        <h4 class="font-bold text-[14px] text-[#111827] truncate leading-tight" 
                                            x-text="openBillModal.customerName ? openBillModal.customerName : (openBillModal.selectedTable ? 'Meja ' + openBillModal.selectedTable : 'Walk-in Customer')">
                                        </h4>
                                        <p class="text-[11px] text-amber-800 font-semibold flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-bookmark text-[9.5px]"></i>
                                            <span x-text="openBillModal.recalledOrderId ? 'Open Bill • ' + (openBillModal.fulfillmentType === 'DINE_IN' ? 'Dine In' : 'Takeaway') + ' • #' + openBillModal.recalledOrderId : 'Open Bill Baru'"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Fulfillment Switch (Dine-in / Takeaway) -->
                            <div class="grid grid-cols-2 gap-1.5 p-1 bg-[#f3f4f6] rounded-[10px]">
                                <button type="button" 
                                        @click="openBillModal.fulfillmentType = 'DINE_IN'"
                                        :class="openBillModal.fulfillmentType === 'DINE_IN' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-800 font-medium'"
                                        class="py-1.5 text-[11.5px] rounded-[8px] transition text-center cursor-pointer">
                                    Dine-in
                                </button>
                                <button type="button" 
                                        @click="openBillModal.fulfillmentType = 'TAKEAWAY'"
                                        :class="openBillModal.fulfillmentType === 'TAKEAWAY' ? 'bg-white text-[#111827] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-800 font-medium'"
                                        class="py-1.5 text-[11.5px] rounded-[8px] transition text-center cursor-pointer">
                                    Takeaway
                                </button>
                            </div>

                            <!-- Customer & Table Input Fields (Collapsible / Customizable) -->
                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-100">
                                <div>
                                    <label class="block text-[10.5px] font-semibold text-gray-500 mb-1">Pilih / Nomor Meja</label>
                                    <div class="relative">
                                        <input type="text" 
                                               x-model="openBillModal.selectedTable" 
                                               list="openbill-table-options"
                                               placeholder="Contoh: 02" 
                                               class="w-full bg-[#f8f9fa] border border-[#e6e5e0] focus:border-[#df5826] focus:bg-white rounded-[8px] px-2.5 py-1.5 text-[11.5px] outline-none">
                                        <datalist id="openbill-table-options">
                                            <template x-for="t in tables" :key="t.id">
                                                <option :value="t.name || t.id"></option>
                                            </template>
                                        </datalist>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10.5px] font-semibold text-gray-500 mb-1">Nama Pelanggan</label>
                                    <input type="text" 
                                           x-model="openBillModal.customerName" 
                                           placeholder="Walk-in / Nama" 
                                           class="w-full bg-[#f8f9fa] border border-[#e6e5e0] focus:border-[#df5826] focus:bg-white rounded-[8px] px-2.5 py-1.5 text-[11.5px] outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Cart Items List -->
                        <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-3 shadow-2xs">
                            <div class="text-[10.5px] font-bold uppercase tracking-wider text-gray-400 pb-2 mb-2 border-b border-gray-100 flex items-center justify-between">
                                <span>Menu Terpilih</span>
                                <button type="button" 
                                        x-show="openBillModal.cart.length > 0" 
                                        @click="openBillModal.cart = []" 
                                        class="text-red-500 hover:text-red-700 text-[10px] font-semibold">
                                    Reset
                                </button>
                            </div>

                            <!-- Empty Cart -->
                            <div x-show="openBillModal.cart.length === 0" class="py-8 text-center text-gray-400">
                                <i class="fas fa-shopping-basket text-2xl mb-1.5 opacity-30"></i>
                                <p class="text-[11.5px]">Keranjang masih kosong</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Pilih menu di sebelah kiri untuk menambahkan</p>
                            </div>

                            <!-- Items -->
                            <div x-show="openBillModal.cart.length > 0" class="max-h-[30vh] overflow-y-auto space-y-2.5 pr-1 divide-y divide-gray-100">
                                <template x-for="(item, idx) in openBillModal.cart" :key="idx">
                                    <div class="pt-2 first:pt-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <h5 class="font-bold text-[12.5px] text-[#111827] truncate leading-tight" x-text="item.name"></h5>
                                                    <span x-show="item.variant_name" class="text-[9.5px] font-bold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded" x-text="item.variant_name"></span>
                                                </div>
                                                <p class="text-[11.5px] font-semibold text-[#df5826] mt-0.5" x-text="formatRp(item.price)"></p>
                                            </div>

                                            <!-- Quantity Controls -->
                                            <div class="flex items-center gap-1.5 bg-[#f8f9fa] border border-[#e6e5e0] rounded-[8px] p-0.5 shrink-0">
                                                <button type="button" 
                                                        @click="if (item.qty > 1) { item.qty--; } else { openBillModal.cart.splice(idx, 1); }"
                                                        class="w-5 h-5 rounded-[6px] hover:bg-white text-gray-600 hover:text-red-500 flex items-center justify-center text-[10px] transition cursor-pointer">
                                                    <i :class="item.qty > 1 ? 'fas fa-minus' : 'fas fa-trash-alt'"></i>
                                                </button>
                                                <span class="w-5 text-center font-bold text-[11.5px] text-[#111827]" x-text="item.qty"></span>
                                                <button type="button" 
                                                        @click="item.qty++"
                                                        class="w-5 h-5 rounded-[6px] hover:bg-white text-gray-600 hover:text-[#df5826] flex items-center justify-center text-[10px] transition cursor-pointer">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Notes field -->
                                        <div class="mt-1.5">
                                            <input type="text" 
                                                   x-model="item.notes" 
                                                   placeholder="+ Tambah catatan..." 
                                                   class="w-full text-[10.5px] bg-[#fffaf6] border border-[#fae8dc] focus:border-[#df5826] text-[#df5826] placeholder-[#df5826]/50 rounded-[6px] px-2 py-0.5 outline-none">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown & Submit Button -->
                    <div class="mt-4 pt-3.5 border-t border-[#e6e5e0] space-y-3">
                        <div class="flex items-center justify-between text-[12px] text-gray-500">
                            <span class="font-medium">Rincian Harga</span>
                            <span class="text-xs text-gray-400">Total Sementara</span>
                        </div>

                        <div class="flex items-baseline justify-between">
                            <span class="text-[14px] font-bold text-[#111827]">Total</span>
                            <span class="text-[20px] font-black text-[#df5826] tracking-tight" x-text="formatRp(openBillCartTotal())"></span>
                        </div>

                        <button type="button" 
                                @click="submitOpenBill()" 
                                :disabled="openBillModal.isSaving || openBillModal.cart.length === 0"
                                class="w-full py-3.5 px-4 rounded-[14px] bg-gradient-to-b from-[#f06428] to-[#df5826] hover:from-[#e0571e] hover:to-[#cb4c1d] text-white text-[13.5px] font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.99]">
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
