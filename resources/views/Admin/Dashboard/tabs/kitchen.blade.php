<div x-show="currentTab === 'kitchen'" x-cloak class="animation-fade space-y-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-xl font-bold text-[#164A35]">Kitchen Display System</h3>
            <p class="text-sm text-gray-500">Live order queue for the kitchen crew.</p>
        </div>
        <div class="flex gap-2">
            <span class="bg-[#F7E5D2] text-[#D97A32] px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                <i class="fas fa-satellite-dish mr-1"></i> LIVE
            </span>
        </div>
    </div>

    <!-- Ticket Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-start">
        <template x-for="order in liveOrders.filter(o => ['CONFIRMED', 'PREPARING'].includes(o.order_status))" :key="order.id">
            <div class="bg-white rounded-2xl border-2 shadow-sm overflow-hidden flex flex-col transition-all duration-300"
                 :class="order.order_status === 'PREPARING' ? 'border-[#D97A32] ring-2 ring-[#D97A32]/20' : 'border-[#E3E1DC]'">
                
                <!-- Ticket Header -->
                <div class="p-4 border-b flex justify-between items-start"
                     :class="order.order_status === 'PREPARING' ? 'bg-[#F7E5D2]/30 border-[#D97A32]/30' : 'bg-gray-50 border-gray-100'">
                    <div>
                        <h4 class="font-black text-lg text-gray-800">#<span x-text="order.id"></span></h4>
                        <p class="text-xs font-bold text-gray-500 mt-0.5" x-text="order.table ? order.table.name : 'Takeaway'"></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-mono font-bold text-gray-500" x-text="new Date(order.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></span>
                        <div class="mt-1">
                            <span x-show="order.order_status === 'CONFIRMED'" class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">NEW</span>
                            <span x-show="order.order_status === 'PREPARING'" class="bg-[#D97A32] text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">COOKING</span>
                        </div>
                    </div>
                </div>

                <!-- Ticket Items -->
                <div class="p-4 flex-1 flex flex-col gap-3">
                    <template x-for="item in order.items" :key="item.id">
                        <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                            <div class="flex items-start gap-2">
                                <div class="w-6 h-6 rounded bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-sm shrink-0" x-text="item.quantity + 'x'"></div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm leading-tight" x-text="item.product_name"></p>
                                    
                                    <!-- Variant -->
                                    <template x-if="item.variant_name">
                                        <p class="text-xs text-gray-500 mt-1 font-medium"><i class="fas fa-tag mr-1 opacity-50"></i> <span x-text="item.variant_name"></span></p>
                                    </template>
                                    
                                    <!-- Modifiers -->
                                    <template x-if="item.modifiers">
                                        <ul class="mt-1 space-y-0.5">
                                            <template x-for="mod in JSON.parse(item.modifiers || '[]')">
                                                <li class="text-[11px] text-gray-500 font-medium ml-1">
                                                    <span class="text-gray-300 mr-1">└</span> <span x-text="mod.name"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>

                                    <!-- Notes -->
                                    <template x-if="item.notes">
                                        <p class="text-xs text-red-500 bg-red-50 p-1.5 rounded-md mt-1.5 font-medium border border-red-100">
                                            <i class="fas fa-comment-dots mr-1"></i> <span x-text="item.notes"></span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Ticket Actions -->
                <div class="p-3 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-2">
                    <template x-if="order.order_status === 'CONFIRMED'">
                        <button @click="updateOrderStatus(order.id, 'PREPARING')" class="col-span-2 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors active:scale-95 shadow-sm flex justify-center items-center gap-2">
                            <i class="fas fa-fire"></i> TERIMA / MASAK
                        </button>
                    </template>
                    
                    <template x-if="order.order_status === 'PREPARING'">
                        <button @click="updateOrderStatus(order.id, 'READY')" class="col-span-2 py-2.5 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl text-sm transition-colors active:scale-95 shadow-sm flex justify-center items-center gap-2">
                            <i class="fas fa-check-circle"></i> PESANAN SIAP
                        </button>
                    </template>
                </div>
            </div>
        </template>
        
        <!-- Empty State -->
        <div x-show="liveOrders.filter(o => ['CONFIRMED', 'PREPARING'].includes(o.order_status)).length === 0" class="col-span-full py-16 text-center bg-white rounded-3xl border-2 border-dashed border-gray-200">
            <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-mug-hot text-3xl text-gray-300"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1">Dapur Kosong!</h3>
            <p class="text-sm text-gray-500">Belum ada pesanan yang perlu dimasak saat ini.</p>
        </div>
    </div>
</div>
