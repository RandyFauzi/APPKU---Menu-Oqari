<div x-show="currentTab === 'orders'" x-cloak class="flex-grow p-4 md:p-8 pt-2 overflow-hidden flex flex-col">
            <!-- Order Type Filters -->
            <div class="flex flex-wrap gap-2 mb-6 shrink-0">
                <button @click="activeOrderFilter = 'all'" :class="activeOrderFilter === 'all' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">All</button>
                <button @click="activeOrderFilter = 'process'" :class="activeOrderFilter === 'process' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">On Process</button>
                <button @click="activeOrderFilter = 'completed'" :class="activeOrderFilter === 'completed' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">Completed</button>
            </div>

            <!-- Kanban Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 overflow-y-auto hide-scroll pb-20 items-start">
                <!-- Empty State -->
                <div x-show="filteredOrders.length === 0" class="col-span-1 md:col-span-2 lg:col-span-3 flex flex-col items-center justify-center py-24 text-center">
                    <div class="w-24 h-24 bg-white border border-[#E3E1DC] rounded-full flex items-center justify-center mb-5 text-[#C5DBC5] shadow-sm">
                        <i class="fas fa-receipt text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-[#164A35] mb-2" style="font-family: 'Playfair Display', serif;">Belum ada pesanan masuk</h3>
                    <p class="text-[14px] text-[#777873]">Tidak ada orderan pada tab status ini.</p>
                </div>

                <template x-for="order in filteredOrders" :key="order.id">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col transition-all hover:shadow-md">
                        <!-- Card Header (Table Number Dominant) -->
                        <div class="flex flex-col mb-4 border-b border-gray-100 pb-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-widest font-mono">Meja</span>
                                    <span class="font-heading font-black text-4xl text-primary leading-none" x-text="order.table || 'TA'"></span>
                                </div>
                                <!-- Status Badge & Time -->
                                <div class="flex flex-col items-end gap-1">
                                    <!-- MASUK -->
                                    <span x-show="order.status === 'Masuk'" class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-[10px] font-bold border border-yellow-300 flex items-center gap-1 animate-pulse">
                                        <i class="fas fa-bell"></i> New
                                    </span>
                                    <!-- IN PROGRESS -->
                                    <span x-show="order.status === 'In Progress'" class="px-2 py-1 rounded bg-[#FDE68A] text-[#92400E] text-[10px] font-bold border border-[#FCD34D] flex items-center gap-1">
                                        <i class="fas fa-clock"></i> In Progress
                                    </span>
                                    <!-- READY -->
                                    <span x-show="order.status === 'Ready'" class="px-2 py-1 rounded bg-[#1E5A7A] text-white text-[10px] font-bold shadow-sm flex items-center gap-1">
                                        <i class="fas fa-check"></i> Ready
                                    </span>
                                    <!-- COMPLETED -->
                                    <span x-show="order.status === 'Completed'" class="px-2 py-1 rounded bg-gray-100 text-gray-500 text-[10px] font-bold border border-gray-200 flex items-center gap-1">
                                        <i class="fas fa-check-double"></i> Completed
                                    </span>
                                    <!-- DIBATALKAN -->
                                    <span x-show="order.status === 'Dibatalkan'" class="px-2 py-1 rounded bg-red-100 text-red-700 text-[10px] font-bold border border-red-200 flex items-center gap-1">
                                        <i class="fas fa-times-circle"></i> Dibatalkan
                                    </span>
                                    
                                    <p class="text-[10px] font-bold text-gray-400 mt-1" x-text="order.time"></p>
                                </div>
                            </div>
                            
                            <!-- Customer Name -->
                            <div class="flex flex-col">
                                <h3 class="font-heading font-bold text-base text-gray-500 leading-tight"><i class="fas fa-user text-xs mr-1 opacity-50"></i> <span x-text="order.customer"></span></h3>
                                <p class="text-[10px] text-gray-400 font-mono mt-0.5">Order <span x-text="'#'+order.id"></span> / <span x-text="order.type"></span></p>
                            </div>
                        </div>
                            


                        <!-- Card Body (Items) -->
                        <div class="flex-grow font-mono">
                            <div class="flex justify-between text-[10px] text-gray-400 font-bold uppercase mb-2">
                                <span>Items</span>
                                <div class="flex gap-4 w-24 justify-end">
                                    <span class="w-6 text-center">Qty</span>
                                    <span class="w-14 text-right">Price</span>
                                </div>
                            </div>
                            
                            <div class="space-y-2 mb-4 overflow-y-auto hide-scroll">
                                <template x-for="item in order.items">
                                    <div class="flex justify-between text-sm" :class="['Completed', 'Dibatalkan'].includes(order.status) ? 'text-gray-400 line-through' : 'text-textdark'">
                                        <div class="flex flex-col truncate pr-2 flex-grow">
                                            <span x-text="item.name" class="truncate font-medium"></span>
                                            <span x-show="item.notes" class="text-[10px] text-red-500 italic mt-0.5" x-text="item.notes"></span>
                                        </div>
                                        <div class="flex gap-4 w-24 justify-end flex-shrink-0">
                                            <span class="w-6 text-center" x-text="item.qty"></span>
                                            <span class="w-14 text-right font-bold" x-text="formatRp(item.price * item.qty)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="border-t border-gray-100 pt-4 flex flex-col gap-3">
                            <div class="flex justify-between items-center font-mono">
                                <span class="font-bold text-gray-500 uppercase text-xs">Total</span>
                                <span class="font-extrabold text-xl text-primary" x-text="formatRp(order.total)"></span>
                            </div>
                            
                            <!-- One Tap Action Button -->
                            <div class="flex gap-2 w-full mt-2">
                                <button @click="viewOrderDetails(order)" class="flex-grow py-2.5 rounded text-sm font-bold border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                                    See Details
                                </button>
                                
                                <template x-if="order.status === 'Masuk' || order.status === 'CONFIRMED'">
                                    <button @click="updateStatus(order.id, 'In Progress')" class="flex-grow py-2.5 rounded text-sm font-bold bg-yellow-500 text-yellow-900 shadow hover:bg-yellow-400 transition">
                                        Terima & Proses
                                    </button>
                                </template>
                                
                                <template x-if="order.status === 'In Progress' || order.status === 'PREPARING'">
                                    <button @click="updateStatus(order.id, 'Ready')" class="flex-grow py-2.5 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow hover:bg-[#154660] transition">
                                        Mark Ready
                                    </button>
                                </template>
                                
                                <template x-if="order.status === 'Ready' || order.status === 'READY'">
                                    <button @click="updateStatus(order.id, 'Completed')" class="flex-grow py-2.5 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow hover:bg-[#154660] transition">
                                        Selesaikan
                                    </button>
                                </template>

                                <template x-if="order.status === 'Completed' || order.status === 'COMPLETED'">
                                    <button disabled class="flex-grow py-2.5 rounded text-sm font-bold bg-gray-100 text-gray-400 cursor-not-allowed">
                                        Done
                                    </button>
                                </template>
                                
                                <template x-if="order.status === 'Dibatalkan'">
                                    <button disabled class="flex-grow py-2.5 rounded text-sm font-bold bg-red-50 text-red-400 cursor-not-allowed border border-red-100">
                                        Dibatalkan
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
