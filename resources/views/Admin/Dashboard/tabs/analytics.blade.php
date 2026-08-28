<div x-show="currentTab === 'analytics'"" x-cloak class="flex-grow p-10 pt-4 overflow-auto hide-scroll bg-[#FAFAFA]">
            
            <div class="flex justify-end mb-6">
                <button class="bg-white border border-gray-200 text-[#4A4A4A] px-4 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-sm hover:bg-gray-50 transition-colors">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Export CSV
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-6 mb-6">
                <!-- Revenue (Hero Card) -->
                <div class="bg-gradient-to-br from-[#2D1A10] to-[#1A0E08] rounded-[24px] p-6 relative overflow-hidden text-white flex flex-col justify-between h-[180px] shadow-sm">
                    <div class="flex justify-between items-start z-10 relative">
                        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/20">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                        </div>
                        <button class="bg-white/10 border border-white/20 text-white/90 text-xs px-3 py-1.5 rounded-lg flex items-center gap-2 hover:bg-white/20 transition-colors">
                            Today <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                    </div>
                    <div class="z-10 relative">
                        <p class="text-[11px] font-bold text-white/60 uppercase tracking-widest mb-1">Total Revenue</p>
                        <p class="font-bold text-[32px] mb-2" x-text="formatRevenue(totalRevenue)"></p>
                        <p class="text-[11px] font-semibold text-[#54C14B] flex items-center gap-1"><i class="fas fa-arrow-up text-[10px]"></i> 18.6% <span class="text-white/50 font-medium">from yesterday</span></p>
                    </div>
                    <!-- Coffee Background Image -->
                    <img src="https://images.unsplash.com/photo-1579992357154-faf4bde95b3d?w=400&fit=crop" class="absolute -right-10 -bottom-10 w-48 h-48 object-cover rounded-full opacity-50 mix-blend-luminosity rotate-12 pointer-events-none">
                </div>

                <!-- Orders -->
                <div class="bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-[180px]">
                    <div class="w-10 h-10 rounded-xl bg-[#FFF5EB] text-[#D9652A] flex items-center justify-center mb-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Orders</p>
                        <p class="font-bold text-[32px] text-[#1A1A1A] mb-4" x-text="totalOrders"></p>
                        <!-- Progress Bar -->
                        <div class="w-full bg-[#FCF5EB] h-2 rounded-full overflow-hidden mb-2">
                            <div class="bg-[#D9652A] h-full rounded-full transition-all duration-500" :style="'width: ' + ((totalSuccess/totalOrders)*100 || 0) + '%'"></div>
                        </div>
                        <div class="flex justify-between text-[11px] text-gray-500 font-semibold">
                            <span x-text="(totalOrders - totalSuccess) + ' Active'"></span>
                            <span x-text="totalSuccess + ' Completed'"></span>
                        </div>
                    </div>
                </div>

                <!-- Success vs Cancelled -->
                <div class="bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 flex flex-col justify-between h-[180px]">
                    <div class="w-10 h-10 rounded-xl bg-[#E8F5E9] text-[#4CAF50] flex items-center justify-center mb-4">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Success Rate</p>
                        <p class="font-bold text-[32px] text-[#1A1A1A] mb-4">100%</p>
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-2">
                            <div class="bg-[#4CAF50] h-full rounded-full"></div>
                        </div>
                        <div class="flex justify-between text-[11px] font-semibold">
                            <span class="text-[#4CAF50]" x-text="totalSuccess + ' Success'"></span>
                            <span class="text-red-500">0 Unfinished</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="flex gap-6 pb-10">
                <!-- Left: Sales Trend -->
                <div class="w-2/3 bg-white rounded-[24px] p-6 shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-[#1A1A1A]">Sales Trend (This Week)</h3>
                        <button class="bg-white border border-gray-200 text-gray-600 text-xs px-3 py-1.5 rounded-lg flex items-center gap-2 shadow-sm font-semibold hover:bg-gray-50">
                            This Week <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                    </div>
                    
                    <!-- Chart -->
                    <div class="h-56 mb-8 relative w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                    
                    <!-- Bottom 4 mini stats -->
                    <div class="grid grid-cols-4 gap-4 mt-auto">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#F6F0E6] text-[#A67C52] flex items-center justify-center shrink-0">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Avg. Daily Revenue</p>
                                <p class="font-bold text-sm text-[#1A1A1A]">Rp 1.62M</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#E8F5E9] text-[#4CAF50] flex items-center justify-center shrink-0">
                                <i class="fas fa-arrow-trend-up"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Best Day</p>
                                <p class="font-bold text-sm text-[#1A1A1A]">Sunday</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#F0F2FF] text-[#6B7AFF] flex items-center justify-center shrink-0">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Growth (vs last week)</p>
                                <p class="font-bold text-sm text-[#4CAF50]"><i class="fas fa-arrow-up text-[10px]"></i> 24.8%</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#E8F4F8] text-[#03A9F4] flex items-center justify-center shrink-0">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Avg. Order Value</p>
                                <p class="font-bold text-sm text-[#1A1A1A]">Rp 79.9K</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Top Menu -->
                <div class="w-1/3 bg-[#FDFDFD] rounded-[24px] p-6 shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-lg text-[#1A1A1A]">Top Menu</h3>
                        <button class="bg-white border border-gray-200 text-gray-600 text-xs px-3 py-1 rounded-lg shadow-sm font-semibold hover:bg-gray-50 transition-colors">View all</button>
                    </div>
                    
                    <div class="flex-grow flex flex-col gap-5">
                        <!-- Item 1 -->
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-[#FFE58F] text-[#D48806] font-bold text-[10px] flex items-center justify-center shrink-0">1</div>
                            <img src="Assest/Menu/Chicken Katsu.png" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1569058242253-92a9c755a0ec?w=100&fit=crop'" class="w-14 h-14 rounded-xl object-cover shrink-0">
                            <div class="flex-grow">
                                <div class="flex justify-between mb-0.5">
                                    <p class="font-bold text-sm text-[#1A1A1A]">Chicken Katsu</p>
                                    <p class="font-bold text-sm text-[#1A1A1A]">Rp 8.1M</p>
                                </div>
                                <p class="text-[10px] text-gray-500 font-semibold mb-2 tracking-wider">80 ORDERS</p>
                                <div class="w-full bg-[#FCF5EB] h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#8C5D3A] h-full rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-[#E8E8E8] text-[#8C8C8C] font-bold text-[10px] flex items-center justify-center shrink-0">2</div>
                            <img src="Assest/Menu/Vanilla Latte.png" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1572442388796-11668a67efeb?w=100&fit=crop'" class="w-14 h-14 rounded-xl object-cover shrink-0">
                            <div class="flex-grow">
                                <div class="flex justify-between mb-0.5">
                                    <p class="font-bold text-sm text-[#1A1A1A]">Vanilla Latte</p>
                                    <p class="font-bold text-sm text-[#1A1A1A]">Rp 6.2M</p>
                                </div>
                                <p class="text-[10px] text-gray-500 font-semibold mb-2 tracking-wider">73 ORDERS</p>
                                <div class="w-full bg-[#FCF5EB] h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#8C5D3A] h-full rounded-full" style="width: 65%"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Item 3 -->
                        <div class="flex items-center gap-4">
                            <div class="w-6 h-6 rounded-full bg-[#F4D3C5] text-[#A65E44] font-bold text-[10px] flex items-center justify-center shrink-0">3</div>
                            <img src="Assest/Menu/Caramel Latte.png" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1585409677983-0f6c41ca9c3b?w=100&fit=crop'" class="w-14 h-14 rounded-xl object-cover shrink-0">
                            <div class="flex-grow">
                                <div class="flex justify-between mb-0.5">
                                    <p class="font-bold text-sm text-[#1A1A1A]">Caramel Latte</p>
                                    <p class="font-bold text-sm text-[#1A1A1A]">Rp 5.7M</p>
                                </div>
                                <p class="text-[10px] text-gray-500 font-semibold mb-2 tracking-wider">73 ORDERS</p>
                                <div class="w-full bg-[#FCF5EB] h-1.5 rounded-full overflow-hidden">
                                    <div class="bg-[#8C5D3A] h-full rounded-full" style="width: 55%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between text-[11px] font-semibold text-[#8C5D3A] bg-[#FCF7F1] -mx-6 -mb-6 px-6 py-4 rounded-b-[24px]">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-trophy"></i> Keep it up! Your best seller is doing great.
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>