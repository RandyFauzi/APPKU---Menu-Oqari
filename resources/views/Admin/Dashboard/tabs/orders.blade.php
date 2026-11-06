<div x-show="currentTab === 'orders'" x-cloak class="flex-grow bg-[#f6f5f2] p-4 md:p-[24px] overflow-y-auto hide-scroll font-['DM_Sans',sans-serif] text-[#1e211f]">

    <!-- Page Title & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight">Pesanan Aktif</h1>
            <p class="text-[12px] text-[#7f837e] mt-0.5">Pantau dan kelola status antrean pesanan pelanggan secara real-time.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.pos.index') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-semibold shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition">
                <img src="{{ asset('images/sparta/cart-btn-icon.svg') }}" alt="" class="w-[15px] h-[15px]">
                <span>Buat Pesanan</span>
            </a>
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
                                    <a :href="'/admin/pos?recall=' + order.id"
                                       class="py-[9px] px-3 rounded-[9px] bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 text-[12px] font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-plus text-[10px] text-amber-700"></i>
                                        <span>+ Tambah Menu</span>
                                    </a>
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
                                    <a :href="'/admin/pos?recall=' + order.id"
                                       class="w-full py-[6px] px-2.5 rounded-[8px] bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-[11px] font-semibold transition flex items-center justify-center gap-1">
                                        <i class="fas fa-edit text-[9.5px] text-amber-700"></i>
                                        <span>Buka di Kasir (+ Tambah Menu / Open Bill)</span>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                                Dibatalkan
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
