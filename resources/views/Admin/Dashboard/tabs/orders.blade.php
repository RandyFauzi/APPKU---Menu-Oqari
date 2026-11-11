<div x-show="currentTab === 'orders'" x-cloak class="flex-grow bg-white p-4 md:p-[24px] overflow-y-auto hide-scroll font-['DM_Sans',sans-serif] text-[#1e211f]">

    <!-- Page Title & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div>
            <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight">Pesanan Aktif</h1>
            <p class="text-[12px] text-[#7f837e] mt-0.5">Pantau dan kelola status antrean pesanan pelanggan secara real-time.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <button type="button"
               @click="openCreateOpenBillModal()"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] text-white text-[12px] font-bold shadow-[0px_1px_2px_rgba(223,88,38,0.18)] hover:opacity-95 transition cursor-pointer">
                <i class="fas fa-shopping-cart text-[12px]"></i>
                <span>Buat Pesanan</span>
            </button>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- LEVEL 1 FILTER TABS (Figma: Semua, Open Bill, Paid)              -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="flex items-center gap-2 mb-4 overflow-x-auto hide-scroll border-b border-[#e6e5e0] pb-0">
        <!-- Tab: Semua -->
        <button type="button" @click="setPrimaryOrderFilter('all')"
            :class="activeOrderFilter === 'all' 
                ? 'bg-[#fff4ed] text-[#df5826] font-bold border-b-2 border-[#df5826]' 
                : 'text-[#71717a] hover:text-[#18181b] font-medium border-b-2 border-transparent'"
            class="px-4 py-2.5 text-[13px] inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer rounded-t-[10px]">
            <span>Semua</span>
            <span class="px-2 py-0.5 rounded-[6px] text-[11px] font-bold"
                  :class="activeOrderFilter === 'all' ? 'bg-[#df5826] text-white' : 'bg-[#e4e4e7] text-[#71717a]'"
                  x-text="orders.length">0</span>
        </button>

        <!-- Tab: Open Bill -->
        <button type="button" @click="setPrimaryOrderFilter('open_bill')"
            :class="activeOrderFilter === 'open_bill' 
                ? 'bg-[#fff4ed] text-[#df5826] font-bold border-b-2 border-[#df5826]' 
                : 'text-[#71717a] hover:text-[#18181b] font-medium border-b-2 border-transparent'"
            class="px-4 py-2.5 text-[13px] inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer rounded-t-[10px]">
            <span>Open Bill</span>
            <span class="px-2 py-0.5 rounded-[6px] text-[11px] font-bold"
                  :class="activeOrderFilter === 'open_bill' ? 'bg-[#df5826] text-white' : 'bg-[#e4e4e7] text-[#71717a]'"
                  x-text="orders.filter(o => isOpenBillOrder(o)).length">0</span>
        </button>

        <!-- Tab: Paid -->
        <button type="button" @click="setPrimaryOrderFilter('paid')"
            :class="activeOrderFilter === 'paid' 
                ? 'bg-[#fff4ed] text-[#df5826] font-bold border-b-2 border-[#df5826]' 
                : 'text-[#71717a] hover:text-[#18181b] font-medium border-b-2 border-transparent'"
            class="px-4 py-2.5 text-[13px] inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer rounded-t-[10px]">
            <span>Paid</span>
            <span class="px-2 py-0.5 rounded-[6px] text-[11px] font-bold"
                  :class="activeOrderFilter === 'paid' ? 'bg-[#df5826] text-white' : 'bg-[#e4e4e7] text-[#71717a]'"
                  x-text="orders.filter(o => isPaidOrder(o)).length">0</span>
        </button>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- LEVEL 2 FILTER BAR (Sub Status Pills + Search + Dropdown on Right) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-3 sm:p-3.5 mb-5 shadow-[0px_2px_8px_rgba(0,0,0,0.02)]">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <!-- Sub Status Filter Pills (Clean neutral styling matching Figma) -->
            <div class="flex items-center gap-2 overflow-x-auto hide-scroll pb-1 lg:pb-0">
                <button type="button" @click="subOrderStatusFilter = 'all'"
                    :class="subOrderStatusFilter === 'all' 
                        ? 'border-[#18181b] bg-white text-[#18181b] font-bold shadow-xs' 
                        : 'border-[#e4e4e7] bg-white text-[#52525b] hover:bg-gray-50 font-medium'"
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer">
                    <span>Semua</span>
                    <span class="rounded-[5px] px-1.5 py-0.5 text-[10.5px]" 
                          :class="subOrderStatusFilter === 'all' ? 'bg-[#18181b] text-white font-bold' : 'bg-[#f4f4f5] text-[#71717a]'"
                          x-text="ordersByPrimaryTab.length">0</span>
                </button>

                <button type="button" @click="subOrderStatusFilter = 'new'"
                    :class="subOrderStatusFilter === 'new' 
                        ? 'border-[#18181b] bg-white text-[#18181b] font-bold shadow-xs' 
                        : 'border-[#e4e4e7] bg-white text-[#52525b] hover:bg-gray-50 font-medium'"
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer">
                    <span>Pesanan Baru</span>
                    <span class="rounded-[5px] px-1.5 py-0.5 text-[10.5px]"
                          :class="subOrderStatusFilter === 'new' ? 'bg-[#18181b] text-white font-bold' : 'bg-[#f4f4f5] text-[#71717a]'"
                          x-text="ordersByPrimaryTab.filter(o => getOrderSubStatus(o) === 'new').length">0</span>
                </button>

                <button type="button" @click="subOrderStatusFilter = 'cooking'"
                    :class="subOrderStatusFilter === 'cooking' 
                        ? 'border-[#18181b] bg-white text-[#18181b] font-bold shadow-xs' 
                        : 'border-[#e4e4e7] bg-white text-[#52525b] hover:bg-gray-50 font-medium'"
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer">
                    <span>Sedang Diproses</span>
                    <span class="rounded-[5px] px-1.5 py-0.5 text-[10.5px]"
                          :class="subOrderStatusFilter === 'cooking' ? 'bg-[#18181b] text-white font-bold' : 'bg-[#f4f4f5] text-[#71717a]'"
                          x-text="ordersByPrimaryTab.filter(o => getOrderSubStatus(o) === 'cooking').length">0</span>
                </button>

                <button type="button" @click="subOrderStatusFilter = 'ready'"
                    :class="subOrderStatusFilter === 'ready' 
                        ? 'border-[#18181b] bg-white text-[#18181b] font-bold shadow-xs' 
                        : 'border-[#e4e4e7] bg-white text-[#52525b] hover:bg-gray-50 font-medium'"
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer">
                    <span>Siap Disajikan</span>
                    <span class="rounded-[5px] px-1.5 py-0.5 text-[10.5px]"
                          :class="subOrderStatusFilter === 'ready' ? 'bg-[#18181b] text-white font-bold' : 'bg-[#f4f4f5] text-[#71717a]'"
                          x-text="ordersByPrimaryTab.filter(o => getOrderSubStatus(o) === 'ready').length">0</span>
                </button>

                <button type="button" @click="subOrderStatusFilter = 'completed'"
                    :class="subOrderStatusFilter === 'completed' 
                        ? 'border-[#18181b] bg-white text-[#18181b] font-bold shadow-xs' 
                        : 'border-[#e4e4e7] bg-white text-[#52525b] hover:bg-gray-50 font-medium'"
                    class="border rounded-[8px] px-3 py-1.5 text-[12px] leading-none inline-flex items-center gap-2 transition whitespace-nowrap cursor-pointer">
                    <span>Selesai</span>
                    <span class="rounded-[5px] px-1.5 py-0.5 text-[10.5px]"
                          :class="subOrderStatusFilter === 'completed' ? 'bg-[#18181b] text-white font-bold' : 'bg-[#f4f4f5] text-[#71717a]'"
                          x-text="ordersByPrimaryTab.filter(o => getOrderSubStatus(o) === 'completed').length">0</span>
                </button>
            </div>

            <!-- Search & Tipe Pesanan Filter on Far Right (Figma Frame 1597884473) -->
            <div class="flex items-center gap-2.5 shrink-0">
                <!-- Search Input -->
                <div class="w-full sm:w-64 bg-white border border-[#e4e4e7] rounded-[9px] px-3 py-1.5 flex items-center gap-2 focus-within:border-[#df5826] transition">
                    <i class="fas fa-search text-xs text-[#71717a]"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama pelanggan, meja, atau #ID..."
                        class="w-full bg-transparent border-none outline-none text-[12px] text-[#1e211f] placeholder-[#8b8e93] p-0 focus:ring-0">
                </div>

                <!-- Tipe Pesanan Custom Dropdown (Matching Figma & Sparta Design System) -->
                <div class="relative shrink-0"
                     x-data="{
                         openDropdown: false,
                         options: [
                             { value: 'all', label: 'Tipe Pesanan', icon: 'fa-sliders-h' },
                             { value: 'DINE_IN', label: 'Dine In', icon: 'fa-utensils' },
                             { value: 'TAKEAWAY', label: 'Takeaway', icon: 'fa-shopping-bag' },
                             { value: 'DELIVERY', label: 'Delivery', icon: 'fa-motorcycle' }
                         ],
                         get currentLabel() {
                             const found = this.options.find(o => o.value === orderFulfillmentFilter);
                             return found ? found.label : 'Tipe Pesanan';
                         },
                         get currentIcon() {
                             const found = this.options.find(o => o.value === orderFulfillmentFilter);
                             return found ? found.icon : 'fa-sliders-h';
                         },
                         selectOption(val) {
                             orderFulfillmentFilter = val;
                             this.openDropdown = false;
                         }
                     }"
                     @click.away="openDropdown = false"
                     @keydown.escape.window="openDropdown = false">
                    
                    <!-- Trigger Button -->
                    <button type="button" 
                            @click="openDropdown = !openDropdown"
                            class="h-[34px] pl-3 pr-2.5 bg-white border rounded-[9px] text-[12px] font-medium flex items-center gap-2 cursor-pointer transition-all outline-none"
                            :class="openDropdown 
                                ? 'border-[#df5826] ring-2 ring-[#df5826]/15 shadow-xs text-[#18181b]' 
                                : (orderFulfillmentFilter !== 'all' 
                                    ? 'border-[#df5826]/50 bg-[#fffbf9] text-[#df5826] font-semibold hover:border-[#df5826]' 
                                    : 'border-[#e4e4e7] hover:border-[#a1a1aa] hover:bg-[#fafbfc] text-[#18181b]')">
                        <i :class="'fas ' + currentIcon + ' text-xs ' + (orderFulfillmentFilter !== 'all' || openDropdown ? 'text-[#df5826]' : 'text-[#71717a]')"></i>
                        <span class="truncate" x-text="currentLabel"></span>
                        <i class="fas fa-chevron-down text-[9px] transition-transform duration-200 ml-0.5"
                           :class="openDropdown ? 'rotate-180 text-[#df5826]' : 'text-[#71717a]'"></i>
                    </button>

                    <!-- Dropdown Menu Popover -->
                    <div x-show="openDropdown"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         x-cloak
                         class="absolute right-0 top-full mt-1.5 z-50 bg-white border border-[#e5e7eb] rounded-[14px] shadow-[0_16px_36px_rgba(15,23,42,0.14)] p-1.5 min-w-[170px]">
                        <template x-for="opt in options" :key="opt.value">
                            <button type="button" 
                                    @click="selectOption(opt.value)"
                                    class="w-full text-left px-2.5 py-2 rounded-[8px] text-[12px] font-medium flex items-center justify-between transition cursor-pointer"
                                    :class="orderFulfillmentFilter === opt.value 
                                        ? 'bg-[#fff6f0] text-[#df5826] font-semibold' 
                                        : 'text-[#374151] hover:bg-gray-50 hover:text-[#111827]'">
                                <span class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center shrink-0"
                                          :class="orderFulfillmentFilter === opt.value ? 'bg-[#df5826]/10 text-[#df5826]' : 'text-[#71717a]'">
                                        <i :class="'fas ' + opt.icon + ' text-[11px]'"></i>
                                    </span>
                                    <span x-text="opt.label"></span>
                                </span>
                                <i x-show="orderFulfillmentFilter === opt.value" class="fas fa-check text-[11px] text-[#df5826] ml-2"></i>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- ORDERS CARDS GRID (Sparta Caffe Cards Style)                    -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 pb-16 items-stretch">

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
            <div class="bg-white border border-[#e6e5e0] rounded-[16px] p-4 flex flex-col justify-between h-full shadow-[0px_3px_12px_rgba(38,39,35,0.04)] hover:border-[#df5826]/60 transition-all gap-3.5">
                
                <!-- TOP HEADER -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Table Badge with Neutral Background -->
                        <div class="w-[38px] h-[38px] rounded-[10px] border border-[#e4e4e7] bg-[#f4f4f5] flex items-center justify-center shrink-0 font-bold text-[12px] text-[#18181b] shadow-2xs gap-1">
                            <template x-if="order.fulfillment_type === 'DELIVERY' || order.type === 'Delivery'">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-motorcycle text-[10px] text-[#71717a]"></i>
                                    <span class="text-[10px] font-bold">DLV</span>
                                </span>
                            </template>
                            <template x-if="order.fulfillment_type !== 'DELIVERY' && order.type !== 'Delivery' && (!order.table || order.table === 'TA' || order.table === '-')">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-shopping-bag text-[10px] text-[#71717a]"></i>
                                    <span class="text-[10px] font-bold">TA</span>
                                </span>
                            </template>
                            <template x-if="order.fulfillment_type !== 'DELIVERY' && order.type !== 'Delivery' && order.table && order.table !== 'TA' && order.table !== '-'">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-chair text-[10px] text-[#71717a]"></i>
                                    <span x-text="String(order.table).replace(/^meja\s*/i, '').padStart(2, '0')"></span>
                                </span>
                            </template>
                        </div>

                        <div class="min-w-0">
                            <!-- Customer Name -->
                            <h3 class="text-[14.5px] font-bold text-[#18181b] leading-tight truncate"
                                x-text="order.raw_customer_name || (order.customer && !order.customer.startsWith('Meja ') ? order.customer : (order.table && order.table !== 'TA' ? 'Meja ' + String(order.table).replace(/^meja\s*/i, '') : 'Pelanggan Walk-in'))"></h3>
                            
                            <!-- Subtitle: Neutral text Open Bill · #42 or Paid · #41 -->
                            <div class="flex items-center gap-1.5 text-[11px] text-[#71717a] mt-0.5 truncate font-normal">
                                <template x-if="isOpenBillOrder(order)">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-receipt text-[10px] text-[#71717a]"></i>
                                        <span>Open Bill</span>
                                    </span>
                                </template>
                                <template x-if="!isOpenBillOrder(order)">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-money-bill-wave text-[10px] text-[#71717a]"></i>
                                        <span>Paid</span>
                                    </span>
                                </template>
                                
                                <span class="text-[#d4d4d8]">•</span>
                                <span x-text="'#' + order.id"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Link: "Lihat Detail ->" -->
                    <button type="button" @click="viewOrderDetails(order)"
                            class="text-[#df5826] hover:text-[#b84218] text-[12px] font-semibold flex items-center gap-1 shrink-0 transition cursor-pointer pt-0.5">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </button>
                </div>

                <!-- SUB-ORDER BODY (Figma Sub-Order Group Layout) -->
                <div class="bg-[#fafafa] border border-[#f4f4f5] rounded-[12px] p-3 flex flex-col gap-2.5">
                    
                    <!-- Sub-order Header: #1 Order (N) + Status Badge -->
                    <div class="flex items-center justify-between gap-2 pb-2 border-b border-[#f0eee9]">
                        <span class="text-[12px] font-bold text-[#18181b]">
                            #1 Order (<span x-text="(order.items || []).reduce((s, i) => s + Number(i.qty || 1), 0)"></span>)
                        </span>

                        <!-- Status Badge Pill (Only progress badge retains color) -->
                        <span x-show="getOrderSubStatus(order) === 'new'"
                              class="px-2 py-0.5 rounded-[6px] bg-[#e0f2fe] border border-[#bae6fd] text-[#0284c7] text-[10.5px] font-bold inline-flex items-center gap-1">
                            <span>Pesanan Baru</span>
                        </span>

                        <span x-show="getOrderSubStatus(order) === 'cooking'"
                              class="px-2 py-0.5 rounded-[6px] bg-[#f3e8ff] border border-[#e9d5ff] text-[#7e22ce] text-[10.5px] font-bold">
                            Sedang Diproses
                        </span>

                        <span x-show="getOrderSubStatus(order) === 'ready'"
                              class="px-2 py-0.5 rounded-[6px] bg-[#dcfce7] border border-[#bbf7d0] text-[#15803d] text-[10.5px] font-bold">
                            Siap Disajikan
                        </span>

                        <span x-show="getOrderSubStatus(order) === 'completed'"
                              class="px-2 py-0.5 rounded-[6px] bg-[#f4f4f5] border border-[#e4e4e7] text-[#71717a] text-[10.5px] font-bold">
                            Selesai
                        </span>

                        <span x-show="getOrderSubStatus(order) === 'cancelled'"
                              class="px-2 py-0.5 rounded-[6px] bg-red-50 border border-red-200 text-red-600 text-[10.5px] font-bold">
                            Dibatalkan
                        </span>
                    </div>

                    <!-- Items List Grouped by Dine In, Takeaway, and Delivery -->
                    <div class="space-y-2.5 max-h-48 overflow-y-auto hide-scroll">
                        <!-- Dine In Section -->
                        <div x-show="getDineInItems(order).length > 0" class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[11px] font-medium text-[#71717a]">
                                <i class="fas fa-utensils text-[9px]"></i>
                                <span>Dine In</span>
                            </div>
                            <div class="space-y-1 pl-1">
                                <template x-for="(item, idx) in getDineInItems(order)" :key="'di-' + idx">
                                    <div class="text-[12px] text-[#18181b] flex items-baseline justify-between gap-2">
                                        <div class="truncate">
                                            <span class="font-bold text-[#18181b]" x-text="item.qty + '×'"></span>
                                            <span class="text-[#27272a] font-normal" x-text="' ' + item.name"></span>
                                            <span x-show="item.notes" class="text-[10px] text-[#df5826] ml-1" x-text="'(' + item.notes + ')'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Takeaway Section -->
                        <div x-show="getTakeawayItems(order).length > 0" class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[11px] font-medium text-[#71717a]">
                                <i class="fas fa-shopping-bag text-[9px]"></i>
                                <span>Takeaway</span>
                            </div>
                            <div class="space-y-1 pl-1">
                                <template x-for="(item, idx) in getTakeawayItems(order)" :key="'ta-' + idx">
                                    <div class="text-[12px] text-[#18181b] flex items-baseline justify-between gap-2">
                                        <div class="truncate">
                                            <span class="font-bold text-[#18181b]" x-text="item.qty + '×'"></span>
                                            <span class="text-[#27272a] font-normal" x-text="' ' + item.name"></span>
                                            <span x-show="item.notes" class="text-[10px] text-[#df5826] ml-1" x-text="'(' + item.notes + ')'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Delivery Section -->
                        <div x-show="getDeliveryItems(order).length > 0" class="space-y-1">
                            <div class="flex items-center gap-1.5 text-[11px] font-medium text-[#71717a]">
                                <i class="fas fa-motorcycle text-[9px]"></i>
                                <span>Delivery</span>
                            </div>
                            <div class="space-y-1 pl-1">
                                <template x-for="(item, idx) in getDeliveryItems(order)" :key="'dlv-' + idx">
                                    <div class="text-[12px] text-[#18181b] flex items-baseline justify-between gap-2">
                                        <div class="truncate">
                                            <span class="font-bold text-[#18181b]" x-text="item.qty + '×'"></span>
                                            <span class="text-[#27272a] font-normal" x-text="' ' + item.name"></span>
                                            <span x-show="item.notes" class="text-[10px] text-[#df5826] ml-1" x-text="'(' + item.notes + ')'"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-order Status Action Button (Neutral button with arrow matching Figma) -->
                    <template x-if="getOrderSubStatus(order) === 'new'">
                        <button type="button" @click="updateStatus(order.id, 'In Progress')"
                                class="w-full py-2 px-3 rounded-[8px] bg-white hover:bg-gray-50 border border-[#e4e4e7] text-[#18181b] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer mt-1">
                            <span>Konfirmasi Pesanan</span>
                            <i class="fas fa-arrow-right text-[10px] text-[#71717a]"></i>
                        </button>
                    </template>

                    <template x-if="getOrderSubStatus(order) === 'cooking'">
                        <button type="button" @click="updateStatus(order.id, 'Ready')"
                                class="w-full py-2 px-3 rounded-[8px] bg-white hover:bg-gray-50 border border-[#e4e4e7] text-[#18181b] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer mt-1">
                            <span>Siap Disajikan</span>
                            <i class="fas fa-arrow-right text-[10px] text-[#71717a]"></i>
                        </button>
                    </template>

                    <template x-if="getOrderSubStatus(order) === 'ready'">
                        <button type="button" @click="updateStatus(order.id, 'Completed')"
                                class="w-full py-2 px-3 rounded-[8px] bg-white hover:bg-gray-50 border border-[#e4e4e7] text-[#18181b] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer mt-1">
                            <span>Selesaikan</span>
                            <i class="fas fa-arrow-right text-[10px] text-[#71717a]"></i>
                        </button>
                    </template>
                </div>

                <!-- CARD FOOTER: Total Pembayaran & Action Buttons (Pinned to bottom with mt-auto) -->
                <div class="mt-auto pt-3 border-t border-[#f0eee9] space-y-2.5">
                    <!-- Total Row -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-[11.5px] text-[#71717a]">
                            <img src="{{ asset('images/sparta/bill-check.svg') }}" alt="" class="w-4 h-4 opacity-70">
                            <span>Total Pembayaran</span>
                        </div>
                        <span class="text-[15px] font-bold text-[#18181b] tracking-tight" x-text="formatRp(order.total)"></span>
                    </div>

                    <!-- Action Buttons -->
                    <div>
                        <!-- OPEN BILL CARDS (HOLD / UNPAID): Two buttons matching Figma! -->
                        <template x-if="isOpenBillOrder(order)">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button"
                                   @click="openEditOpenBillModal(order)"
                                   class="py-2 px-2.5 rounded-[9px] bg-white hover:bg-[#f8f9fa] border border-[#d8dce2] hover:border-[#8b8e93] text-[#1e211f] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                    <i class="fas fa-plus text-[10px] text-[#df5826]"></i>
                                    <span>Tambah Order</span>
                                </button>
                                <a :href="'/admin/pos?recall=' + order.id + '&pay=1'"
                                   class="py-2 px-2.5 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] hover:opacity-95 text-white text-[11.5px] font-bold shadow-sm transition flex items-center justify-center gap-1.5 text-center">
                                    <i class="fas fa-cash-register text-[10px]"></i>
                                    <span>Bayar di Kasir</span>
                                </a>
                            </div>
                        </template>

                        <!-- REGULAR ACTIVE CARDS (Non-Open Bill) -->
                        <template x-if="!['completed', 'cancelled'].includes(getOrderSubStatus(order)) && !isOpenBillOrder(order)">
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button"
                                   @click="openEditOpenBillModal(order)"
                                   class="py-2 px-2.5 rounded-[9px] bg-white hover:bg-[#f8f9fa] border border-[#d8dce2] hover:border-[#8b8e93] text-[#1e211f] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 shadow-2xs cursor-pointer">
                                    <i class="fas fa-plus text-[10px] text-[#df5826]"></i>
                                    <span>Tambah Menu</span>
                                </button>
                                <button type="button" @click="viewOrderDetails(order)"
                                        class="py-2 px-2.5 rounded-[9px] bg-[#f8f9fa] hover:bg-gray-100 border border-[#d8dce2] text-[#1e211f] text-[11.5px] font-semibold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                    <span>Lihat Detail</span>
                                </button>
                            </div>
                        </template>

                        <!-- REGULAR COMPLETED OR CANCELLED CARDS -->
                        <template x-if="['completed', 'cancelled'].includes(getOrderSubStatus(order))">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[11px] text-[#8b8e93]">
                                    <span x-text="formatOrderTime(order.time)"></span>
                                </span>
                                <button type="button" @click="viewOrderDetails(order)"
                                        class="py-1.5 px-3 rounded-[8px] border border-[#d8dce2] bg-white hover:bg-gray-50 text-[#53565a] text-[11px] font-semibold transition">
                                    Lihat Struk
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- MODAL: TAMBAH PESANAN OPEN BILL (DIRECT FIGMA FRAME 1597884472) -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
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

                                    <!-- Price & TWO Serving Buttons (Direct Dine In & Takeaway) -->
                                    <div class="mt-2 pt-2 border-t border-[#f0eee9] flex flex-col gap-1.5">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-[13px] text-[#111827]" x-text="'Rp ' + formatRp(p.effective_price || p.price)"></span>
                                            <template x-if="p.has_active_discount">
                                                <span class="text-[10px] text-gray-400 line-through" x-text="'Rp ' + formatRp(p.price)"></span>
                                            </template>
                                        </div>

                                        <div class="flex flex-col gap-1.5 w-full">
                                            <button type="button" 
                                                    @click="addToOpenBillCart(p, 'DINE_IN')"
                                                    class="w-full py-2 px-2.5 rounded-[9px] bg-gradient-to-r from-[#f97316] to-[#ea580c] hover:opacity-95 text-white text-[11.5px] font-bold flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-utensils text-[10px]"></i>
                                                <span>Dine In</span>
                                            </button>
                                            <button type="button" 
                                                    @click="addToOpenBillCart(p, 'TAKEAWAY')"
                                                    class="w-full py-2 px-2.5 rounded-[9px] bg-white hover:bg-[#fff9f6] border border-[#ea580c] text-[#ea580c] text-[11.5px] font-bold flex items-center justify-center gap-1.5 shadow-2xs transition active:scale-[0.98] cursor-pointer">
                                                <i class="fas fa-shopping-bag text-[10px]"></i>
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
                                <h3 class="text-[10px] font-bold tracking-wider text-[#df5826] uppercase">CURRENT ORDER</h3>
                                <p class="text-[14px] font-bold text-[#111827] leading-tight" x-text="openBillModal.recalledOrderId ? 'Pesanan #' + openBillModal.recalledOrderId : 'Pesanan Baru'"></p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="openBillModal.cart = []" x-show="openBillModal.cart.length > 0"
                                    class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-[#df5826] hover:text-[#b84218] transition cursor-pointer">
                                    <span>Reset</span>
                                    <i class="fas fa-rotate-right text-[11px]"></i>
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

                        <!-- Cart Items List (Single unified list matching Figma) -->
                        <div class="flex flex-col gap-2.5 max-h-[36vh] overflow-y-auto pr-1">
                            <template x-if="openBillModal.cart.length === 0">
                                <div class="py-8 text-center text-gray-400 bg-white border border-dashed border-[#d8dce2] rounded-[12px] p-4">
                                    <i class="fas fa-shopping-basket text-2xl mb-1.5 opacity-30"></i>
                                    <p class="text-[11.5px] font-semibold text-gray-600">Keranjang masih kosong</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Pilih menu di samping untuk menambahkan ke pesanan.</p>
                                </div>
                            </template>

                            <template x-for="(item, idx) in openBillModal.cart" :key="'ob-item-' + idx">
                                <div class="bg-white border border-[#e6e5e0] rounded-[12px] p-2.5 flex flex-col gap-2 shadow-[0px_1px_3px_rgba(0,0,0,0.03)] hover:border-[#df5826]/40 transition">
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
                                                <button type="button" @click="toggleOpenBillItemServingType(idx)"
                                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-[6px] bg-[#f3f4f6] hover:bg-[#e5e7eb] text-[#374151] border border-[#e5e7eb] text-[10.5px] font-medium transition cursor-pointer"
                                                    :title="'Klik untuk ubah ke ' + ((item.serving_type || 'DINE_IN') === 'TAKEAWAY' ? 'Dine In' : 'Takeaway')">
                                                    <i class="fas fa-utensils text-[9px] text-[#53565a]" x-show="(item.serving_type || 'DINE_IN') !== 'TAKEAWAY'"></i>
                                                    <i class="fas fa-shopping-bag text-[9px] text-[#53565a]" x-show="(item.serving_type || 'DINE_IN') === 'TAKEAWAY'"></i>
                                                    <span x-text="(item.serving_type || 'DINE_IN') === 'TAKEAWAY' ? 'Takeaway' : 'Dine In'"></span>
                                                    <i class="fas fa-right-left text-[8px] text-[#8b8e93] opacity-70 ml-0.5"></i>
                                                </button>
                                                <span x-show="item.variant_name" class="text-[10.5px] text-[#53565a] truncate" x-text="'· ' + item.variant_name"></span>
                                            </div>
                                        </div>

                                        <button type="button" @click="openBillModal.cart.splice(idx, 1)" class="w-6 h-6 flex items-center justify-center shrink-0 text-[#ef4444] hover:text-[#dc2626] hover:bg-red-50 rounded-full transition cursor-pointer" title="Hapus item">
                                            <i class="far fa-trash-can text-[13px]"></i>
                                        </button>
                                    </div>

                                    {{-- Collapsed Note Trigger --}}
                                    <button type="button"
                                        x-show="!item.showNotes && !item.notes"
                                        @click="item.showNotes = true"
                                        class="flex items-center gap-1.5 text-left w-fit hover:opacity-80 transition cursor-pointer py-0.5">
                                        <i class="fas fa-pen-to-square text-[10.5px] text-[#df5826]"></i>
                                        <span class="text-[11px] font-semibold text-[#df5826]">Buat Catatan Customer</span>
                                    </button>

                                    {{-- Expanded Note Box --}}
                                    <div x-show="item.showNotes || item.notes" class="bg-[#f8f9fa] rounded-[8px] p-2 flex flex-col gap-1.5 border border-[#e6e5e0]">
                                        <div class="relative border border-[#d8dce2] rounded-[6px] p-2 bg-white focus-within:border-[#df5826] transition">
                                            <label class="text-[10px] font-medium text-[#7f837e] block mb-0.5">Catatan customer (Opsional)</label>
                                            <textarea x-model="item.notes" rows="2" placeholder="Contoh: tanpa gula, es sedikit"
                                                class="w-full bg-transparent border-none outline-none text-[11px] text-[#111827] placeholder-[#8b8e93] resize-none p-0 focus:ring-0"></textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1.5">
                                            <button type="button" @click="item.notes = ''; item.showNotes = false"
                                                class="border border-[#d8dce2] rounded-[7px] py-1.5 px-2 flex items-center justify-center gap-1.5 bg-white hover:bg-gray-50 text-[#111827] text-[11px] font-semibold transition cursor-pointer">
                                                <i class="far fa-trash-can text-[11px] text-[#53565a]"></i>
                                                <span class="truncate">Hapus Catatan</span>
                                            </button>
                                            <button type="button" @click="splitOpenBillItem(idx)" :disabled="item.qty <= 1"
                                                class="border border-[#d8dce2] rounded-[7px] py-1.5 px-2 flex items-center justify-center gap-1.5 bg-white hover:bg-gray-50 text-[#111827] text-[11px] font-semibold transition disabled:opacity-40 cursor-pointer">
                                                <i class="fas fa-up-right-from-square text-[10.5px] text-[#53565a]"></i>
                                                <span class="truncate">Pisahkan 1 Item</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-0.5">
                                        <span class="text-[13.5px] font-bold text-[#111827]" x-text="'Rp ' + formatRp(item.price * item.qty)"></span>
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="if (item.qty > 1) { item.qty--; } else { openBillModal.cart.splice(idx, 1); }"
                                                class="w-6 h-6 rounded-full border border-[#d8dce2] bg-white text-[#111827] hover:bg-gray-100 active:scale-95 flex items-center justify-center transition cursor-pointer">
                                                <i class="fas fa-minus text-[9px]"></i>
                                            </button>
                                            <span class="w-6 text-center text-[12.5px] font-bold text-[#111827]" x-text="item.qty"></span>
                                            <button type="button" @click="item.qty++"
                                                class="w-6 h-6 rounded-full bg-[#111827] text-white hover:bg-[#df5826] active:scale-95 flex items-center justify-center transition shadow-2xs cursor-pointer">
                                                <i class="fas fa-plus text-[9px]"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Price Breakdown & Submit Button -->
                    <div class="mt-3 pt-3 border-t border-[#e6e5e0] space-y-2">
                        <div class="flex items-center justify-between text-[11.5px]">
                            <button type="button" @click="openBillModal.showPriceBreakdown = !openBillModal.showPriceBreakdown" 
                                    class="font-semibold text-[#53565a] hover:text-[#111827] flex items-center gap-1 select-none transition-colors cursor-pointer group">
                                <span>Total Pembayaran</span>
                                <i class="fas fa-chevron-right text-[8.5px] text-gray-400 group-hover:text-[#df5826] transition-transform duration-200" :class="openBillModal.showPriceBreakdown ? 'rotate-90' : ''"></i>
                            </button>
                        </div>

                        <div class="flex items-baseline justify-between pt-0.5">
                            <span class="text-[18px] font-black text-[#df5826] tracking-tight" x-text="'Rp ' + formatRp(openBillCartTotal() + Math.round(openBillCartTotal() * 0.1))"></span>
                        </div>

                        <div x-show="openBillModal.showPriceBreakdown" class="space-y-1.5 pt-1 border-t border-dashed border-[#e6e5e0]">
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
