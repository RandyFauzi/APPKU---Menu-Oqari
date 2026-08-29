<div x-show="currentTab === 'analytics'" x-cloak class="flex-grow p-8 lg:p-10 bg-[#F8F7F3] overflow-y-auto hide-scroll font-sans" x-data="analyticsApp()">
    
    <!-- Greeting & Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-[32px] text-[#164A35] leading-tight mb-2" style="font-family: 'Playfair Display', serif; font-weight: 700;">Good morning, Bean &amp; Bloom! 👋</h1>
            <p class="text-[#777873] text-[16px]">Here’s what’s happening with your café today.</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Date Range -->
            <div class="relative">
                <select class="bg-white border border-[#E3E1DC] rounded-[12px] pl-10 pr-8 py-2.5 text-[14px] text-[#202522] font-semibold focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none appearance-none cursor-pointer shadow-sm">
                    <option>Today</option>
                    <option>Yesterday</option>
                    <option>This Week</option>
                    <option>This Month</option>
                    <option>Custom Range</option>
                </select>
                <i class="fas fa-calendar-alt absolute left-4 top-3.5 text-[#164A35]"></i>
                <i class="fas fa-chevron-down absolute right-4 top-4 text-[10px] text-[#777873] pointer-events-none"></i>
            </div>
            
            <!-- Export -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="bg-white border border-[#E3E1DC] text-[#202522] px-5 py-2.5 rounded-[12px] text-[14px] font-semibold shadow-sm hover:bg-gray-50 flex items-center gap-2 transition-colors">
                    <i class="fas fa-arrow-up text-[#164A35]"></i> Export Report
                </button>
                <div x-show="open" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-[#E3E1DC] overflow-hidden z-50">
                    <a href="#" class="block px-4 py-2.5 text-sm text-[#202522] hover:bg-[#F8F7F3] font-medium"><i class="fas fa-file-pdf w-5 text-red-500"></i> Export PDF</a>
                    <a href="#" class="block px-4 py-2.5 text-sm text-[#202522] hover:bg-[#F8F7F3] font-medium"><i class="fas fa-file-csv w-5 text-green-600"></i> Export CSV</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <!-- Card 1 -->
        <div class="bg-white rounded-[24px] p-6 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative overflow-hidden group hover:border-[#DDEBDD] transition-colors cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <p class="text-[#777873] font-semibold text-[14px]">Order Hari Ini</p>
                <div class="w-10 h-10 rounded-[12px] bg-[#F8F7F3] text-[#164A35] flex items-center justify-center group-hover:bg-[#DDEBDD] transition-colors">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <h3 class="text-[32px] font-bold text-[#202522] mb-2 leading-none" x-text="data.orders">142</h3>
            <p class="text-[13px] font-medium text-[#777873] flex items-center gap-1.5">
                <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded-md flex items-center gap-1 font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <span x-text="data.ordersChange + '%'">12%</span></span> vs. kemarin
            </p>
        </div>
        
        <!-- Card 2 -->
        <div class="bg-white rounded-[24px] p-6 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative overflow-hidden group hover:border-[#F7E5D2] transition-colors cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <p class="text-[#777873] font-semibold text-[14px]">Omzet</p>
                <div class="w-10 h-10 rounded-[12px] bg-[#F8F7F3] text-[#D97A32] flex items-center justify-center group-hover:bg-[#F7E5D2] transition-colors">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <h3 class="text-[32px] font-bold text-[#202522] mb-2 leading-none" x-text="'Rp' + formatNum(data.revenue)">Rp2.850.000</h3>
            <p class="text-[13px] font-medium text-[#777873] flex items-center gap-1.5">
                <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded-md flex items-center gap-1 font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <span x-text="data.revenueChange + '%'">18%</span></span> vs. kemarin
            </p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-[24px] p-6 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative overflow-hidden group hover:border-[#DDEBDD] transition-colors cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <p class="text-[#777873] font-semibold text-[14px]">Menu Terlaris</p>
                <div class="w-10 h-10 rounded-[12px] bg-[#F8F7F3] text-[#164A35] flex items-center justify-center group-hover:bg-[#DDEBDD] transition-colors">
                    <i class="fas fa-mug-hot"></i>
                </div>
            </div>
            <h3 class="text-[20px] font-bold text-[#202522] mb-1 truncate" x-text="data.topProduct.name">Es Kopi Aren</h3>
            <div class="flex items-end justify-between mt-3">
                <p class="text-[13px] font-bold text-[#202522]" x-text="data.topProduct.sold + ' porsi'">132 porsi</p>
                <p class="text-[13px] font-medium text-[#777873] flex items-center gap-1.5">
                    <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded-md flex items-center gap-1 font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <span x-text="data.topProduct.change + '%'">22%</span></span>
                </p>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-[24px] p-6 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative overflow-hidden group hover:border-[#DDEBDD] transition-colors cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <p class="text-[#777873] font-semibold text-[14px]">Customer Kembali</p>
                <div class="w-10 h-10 rounded-[12px] bg-[#F8F7F3] text-[#164A35] flex items-center justify-center group-hover:bg-[#DDEBDD] transition-colors">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <h3 class="text-[32px] font-bold text-[#202522] mb-2 leading-none" x-text="data.returningCustomers + '%'">38%</h3>
            <p class="text-[13px] font-medium text-[#777873] flex items-center gap-1.5">
                <span class="text-green-600 bg-green-50 px-1.5 py-0.5 rounded-md flex items-center gap-1 font-bold"><i class="fas fa-arrow-up text-[10px]"></i> <span x-text="data.returningChange + '%'">6%</span></span> vs. bulan lalu
            </p>
        </div>
    </div>

    <!-- 2 Column Layout -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8">
        <!-- Left 62% -->
        <div class="lg:w-[62%] flex flex-col gap-6">
            <!-- Chart Card -->
            <div class="bg-white rounded-[28px] p-8 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex-grow flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-[20px] font-bold text-[#202522] mb-1">Tren Pesanan</h3>
                        <p class="text-[14px] text-[#777873] font-medium">Jumlah pesanan per jam hari ini</p>
                    </div>
                    <div class="relative">
                        <select class="bg-[#F8F7F3] border-none rounded-[10px] pl-3 pr-8 py-2 text-[13px] text-[#202522] font-semibold focus:ring-0 outline-none appearance-none cursor-pointer">
                            <option>Hari ini</option>
                            <option>Kemarin</option>
                            <option>7 Hari</option>
                            <option>30 Hari</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-2.5 text-[10px] text-[#777873] pointer-events-none"></i>
                    </div>
                </div>
                <!-- Chart Container -->
                <div class="w-full h-[300px] mt-2 relative">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right 38% -->
        <div class="lg:w-[38%] flex flex-col gap-6">
            <!-- Best Selling Menu -->
            <div class="bg-white rounded-[28px] p-8 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)] relative">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-[18px] font-bold text-[#202522]">Menu Terlaris Hari Ini</h3>
                </div>
                
                <div class="flex gap-5 items-center">
                    <div class="flex-grow">
                        <div class="inline-flex px-2 py-1 rounded-md bg-[#F7E5D2] text-[#D97A32] text-[11px] font-bold uppercase tracking-wider mb-3">
                            <i class="fas fa-star mr-1"></i> #1 Terlaris
                        </div>
                        <h4 class="text-[22px] font-bold text-[#164A35] mb-2 leading-tight">Es Kopi Aren</h4>
                        <p class="text-[13px] text-[#777873] font-medium leading-relaxed mb-4">Perpaduan espresso, gula aren, dan susu segar.</p>
                        
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-[#202522] text-[15px]">132 porsi</span>
                            <span class="text-green-600 text-[13px] font-bold flex items-center gap-1"><i class="fas fa-arrow-up text-[10px]"></i> <span>22%</span></span>
                        </div>
                    </div>
                    <div class="w-[110px] h-[130px] rounded-[16px] overflow-hidden shrink-0 shadow-sm border border-[#E3E1DC]">
                        <img src="https://images.unsplash.com/photo-1572442388796-11668a67efeb?w=200&fit=crop" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <!-- Returning Customer -->
            <div class="bg-white rounded-[28px] p-8 border border-[#E3E1DC] shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <h3 class="text-[18px] font-bold text-[#202522] mb-6">Pelanggan Kembali</h3>
                
                <div class="flex items-center gap-6">
                    <!-- Donut Chart -->
                    <div class="relative w-[110px] h-[110px] shrink-0">
                        <canvas id="customerChart"></canvas>
                        <!-- Center text -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-[20px] font-bold text-[#164A35]" x-text="data.returningCustomers + '%'">38%</span>
                        </div>
                    </div>
                    
                    <div class="flex-grow">
                        <p class="text-[13px] text-[#777873] font-medium mb-1">Total pelanggan hari ini</p>
                        <div class="flex items-end gap-3 mb-5">
                            <span class="text-[28px] font-bold text-[#202522] leading-none" x-text="data.totalCustomers">372</span>
                            <span class="text-green-600 text-[12px] font-bold flex items-center gap-1 mb-1"><i class="fas fa-arrow-up text-[10px]"></i> 6%</span>
                        </div>
                        
                        <div class="flex flex-col gap-2.5">
                            <div class="flex items-center justify-between text-[13px]">
                                <div class="flex items-center gap-2 font-medium text-[#202522]">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#164A35]"></div> Pelanggan baru
                                </div>
                                <div>
                                    <span class="font-bold text-[#202522] mr-2" x-text="data.newCustomers">231</span> <span class="font-semibold text-[#777873]" x-text="data.newCustomersPct + '%'">62%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[13px]">
                                <div class="flex items-center gap-2 font-medium text-[#202522]">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#D97A32]"></div> Pelanggan kembali
                                </div>
                                <div>
                                    <span class="font-bold text-[#202522] mr-2" x-text="data.returningCount">141</span> <span class="font-semibold text-[#777873]" x-text="data.returningCustomers + '%'">38%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Bar -->
    

<!-- Load Chart.js globally if not already loaded -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('analyticsApp', () => ({
            data: {
                orders: 142,
                ordersChange: 12,
                revenue: 2850000,
                revenueChange: 18,
                topProduct: {
                    name: "Es Kopi Aren",
                    desc: "Perpaduan espresso, gula aren, dan susu segar.",
                    sold: 132,
                    change: 22
                },
                returningCustomers: 38,
                returningChange: 6,
                totalCustomers: 372,
                newCustomers: 231,
                returningCount: 141,
                newCustomersPct: 62
            },
            trendChart: null,
            customerChart: null,
            formatNum(num) {
                return Number(num).toLocaleString('id-ID').replace(/,/g, '.');
            },
            init() {
                // Initialize Charts after a small delay to ensure DOM is ready
                setTimeout(() => {
                    this.initTrendChart();
                    this.initCustomerChart();
                }, 200);

                // Watch for tab changes to re-render if needed
                this.$watch('currentTab', (val) => {
                    if (val === 'analytics') {
                        setTimeout(() => {
                            this.initTrendChart();
                            this.initCustomerChart();
                        }, 200);
                    }
                });
            },
            initTrendChart() {
                const ctx = document.getElementById('trendChart');
                if(!ctx) return;
                
                if(typeof Chart === 'undefined') return;
                
                if(this.trendChart) this.trendChart.destroy();

                this.trendChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'],
                        datasets: [
                            {
                                type: 'line',
                                label: 'Pesanan (Line)',
                                data: [15, 25, 45, 35, 30, 55, 40, 20],
                                borderColor: '#164A35',
                                backgroundColor: '#164A35',
                                borderWidth: 3,
                                tension: 0.4,
                                pointBackgroundColor: '#FFFFFF',
                                pointBorderColor: '#164A35',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                fill: false,
                                order: 1
                            },
                            {
                                type: 'bar',
                                label: 'Pesanan (Bar)',
                                data: [15, 25, 45, 35, 30, 55, 40, 20],
                                backgroundColor: '#DDEBDD',
                                borderRadius: 6,
                                barThickness: 24,
                                order: 2
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#202522',
                                titleFont: { size: 13, family: 'Inter' },
                                bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    title: (ctx) => ctx[0].label + ' WIB',
                                    label: (ctx) => ctx.raw + ' pesanan'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 60,
                                ticks: { stepSize: 15, color: '#777873', font: { family: 'Inter', size: 11 } },
                                grid: { color: '#F0F0F0', drawBorder: false }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#777873', font: { family: 'Inter', size: 11 } }
                            }
                        }
                    }
                });
            },
            initCustomerChart() {
                const ctx = document.getElementById('customerChart');
                if(!ctx) return;
                
                if(typeof Chart === 'undefined') return;

                if(this.customerChart) this.customerChart.destroy();

                this.customerChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pelanggan Baru', 'Pelanggan Kembali'],
                        datasets: [{
                            data: [this.data.newCustomersPct, this.data.returningCustomers],
                            backgroundColor: ['#164A35', '#D97A32'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#202522',
                                titleFont: { size: 13, family: 'Inter' },
                                bodyFont: { size: 14, weight: 'bold', family: 'Inter' },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: (ctx) => ' ' + ctx.raw + '%'
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
