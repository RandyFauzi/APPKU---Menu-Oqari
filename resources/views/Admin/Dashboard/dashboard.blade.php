<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Appku') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/webp" href="Pavico.webp">
    
    <!-- View Transitions API -->
    <meta name="view-transition" content="same-origin" />
    <style>
        body { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
    
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Auth Guard removed, using Laravel middleware instead -->
    
    <!-- Data -->
    <script src="js/data.js"></script>
    <script src="js/database.js"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    colors: { 
                        brewlybg: '#FDFBF7',
                        brewlygreen: '#34714C',
                        brewlylightgreen: '#E7F2EB',
                        brewlypeach: '#FEF0E6',
                        brewlyorange: '#D9652A',
                        brewlyborder: '#F0ECE4',
                        brewlytext: '#222222',
                        brewlymuted: '#888888',
                        primary: '#1E5A7A', bgbase: '#F4EFE6', accent: '#8CB8C9', textdark: '#2D3748' 
                    }, 
                    fontFamily: { heading: ['Inter', 'sans-serif'], mono: ['Space Mono', 'monospace'], sans: ['Inter', 'sans-serif'] } 
                } 
            } 
        }
    </script>
    <style>
        body { background-color: #FDFBF7; color: #222222; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        [x-cloak] { display: none !important; }
        .dashed-box { background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%23CFCFCF' stroke-width='2' stroke-dasharray='8%2c 8' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e"); border-radius: 16px; }
    </style>
</head>
<body class="font-sans text-textdark h-screen flex overflow-hidden" x-data="dashboardApp()">

    <!-- Audio untuk notifikasi pesanan masuk -->
    <audio id="chime-sound" src="Assest/Notif%20Orderan%20Masuk.mp3" preload="auto"></audio>

    <!-- Sidebar -->
    <aside class="w-64 flex flex-col shrink-0 border-r border-brewlyborder p-6 gap-6 h-full bg-brewlybg">
        <div class="flex items-center gap-2 mb-2">
            <img src="Pavico.webp" alt="Logo" class="w-8 h-8 object-contain">
            <div class="flex flex-col">
                <span class="font-bold text-lg leading-tight">Bitten</span>
                <span class="text-[10px] text-brewlymuted uppercase tracking-wider">Coffee Shops. Stronger.</span>
            </div>
        </div>
        
        <nav class="flex-grow space-y-1">
            <template x-for="tab in tabs" :key="tab.id">
                <button @click="currentTab = tab.id" 
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition-colors text-left"
                        :class="currentTab === tab.id ? 'bg-brewlypeach text-brewlyorange font-bold' : 'text-brewlymuted hover:bg-gray-100 hover:text-brewlytext font-medium'">
                    <i :class="tab.icon" class="w-5 text-center"></i>
                    <span x-text="tab.name"></span>
                </button>
            </template>
        </nav>
        
        <!-- Promo Card -->
        <div class="bg-[#F3EBE1] rounded-2xl p-5 relative overflow-hidden flex flex-col gap-4 mt-auto mb-2">
            <h4 class="font-heading font-extrabold text-xl leading-tight z-10 w-3/4 text-brewlytext">Great coffee builds stronger communities.</h4>
            <div class="z-10 bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-sm cursor-pointer hover:scale-105 transition-transform"><i class="fas fa-arrow-right text-xs"></i></div>
            <img src="https://images.unsplash.com/photo-1551030173-122aabc4489c?w=200&fit=crop" class="absolute -bottom-6 -right-6 w-28 h-28 object-cover rounded-full opacity-80 border-4 border-[#F3EBE1]">
        </div>
        
        <!-- Logout Button -->
        <button @click="logout()" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-red-500 bg-red-50 hover:bg-red-100 transition-colors mt-2">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </aside>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col h-full bg-white rounded-l-[40px] shadow-[-10px_0_30px_rgba(0,0,0,0.02)] border-l border-brewlyborder overflow-hidden">
        
        <!-- Top Header for Main Area -->
        <header class="h-24 flex justify-between items-center px-10 shrink-0 border-b border-brewlyborder/50">
            <div>
                <h2 class="font-sans font-bold text-3xl text-brewlytext" x-text="tabs.find(t => t.id === currentTab)?.name"></h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="searchQuery" placeholder="Search orders or menu..." class="bg-gray-50 border border-gray-200 rounded-full pl-11 pr-4 py-2 text-sm focus:outline-none focus:border-brewlygreen focus:ring-1 focus:ring-brewlygreen w-64 transition-all">
                </div>
            </div>
        </header>

        <!-- VIEW: LIVE ORDERS (KANBAN) -->
        <div x-show="currentTab === 'orders'" x-cloak class="flex-grow p-8 pt-2 overflow-hidden flex flex-col">
            <!-- Order Type Filters -->
            <div class="flex gap-2 mb-6 shrink-0">
                <button @click="activeOrderFilter = 'all'" :class="activeOrderFilter === 'all' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">All</button>
                <button @click="activeOrderFilter = 'process'" :class="activeOrderFilter === 'process' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">On Process</button>
                <button @click="activeOrderFilter = 'completed'" :class="activeOrderFilter === 'completed' ? 'bg-[#1E5A7A] text-white shadow-sm border-transparent' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors">Completed</button>
            </div>

            <!-- Kanban Grid -->
            <div class="grid grid-cols-3 gap-6 overflow-y-auto hide-scroll pb-20 items-start">
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
                                    <div class="flex justify-between text-sm" :class="order.status === 'Completed' ? 'text-gray-400 line-through' : 'text-textdark'">
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
                                
                                <template x-if="order.status === 'Masuk'">
                                    <button @click="updateStatus(order.id, 'In Progress')" class="flex-grow py-2.5 rounded text-sm font-bold bg-yellow-500 text-yellow-900 shadow hover:bg-yellow-400 transition">
                                        Terima & Proses
                                    </button>
                                </template>
                                
                                <template x-if="order.status === 'In Progress'">
                                    <button @click="updateStatus(order.id, 'Ready')" class="flex-grow py-2.5 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow hover:bg-[#154660] transition">
                                        Mark Ready
                                    </button>
                                </template>
                                
                                <template x-if="order.status === 'Ready'">
                                    <button @click="updateStatus(order.id, 'Completed')" class="flex-grow py-2.5 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow hover:bg-[#154660] transition">
                                        Selesaikan
                                    </button>
                                </template>

                                <template x-if="order.status === 'Completed'">
                                    <button disabled class="flex-grow py-2.5 rounded text-sm font-bold bg-gray-100 text-gray-400 cursor-not-allowed">
                                        Done
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- VIEW: MENU CMS (OWNER) -->
        <div x-show="currentTab === 'menu'" x-cloak class="flex-grow p-10 pt-6 overflow-y-auto hide-scroll flex flex-col gap-8 bg-brewlybg">
            <p class="text-brewlymuted text-sm">Add your coffee, food, and drinks to create a beautiful menu for your shop.</p>
            
            <!-- Cards Section -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Upload Card -->
                <div class="dashed-box flex flex-col items-center justify-center p-8 text-center bg-gray-50/30">
                    <div class="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center text-brewlymuted mb-3 bg-white shadow-sm">
                        <i class="fas fa-cloud-upload-alt text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-1 text-brewlytext">Upload Menu Items</h3>
                    <p class="text-sm text-brewlymuted mb-6">Drag and drop images, or click to upload<br>JPG, PNG up to 10MB</p>
                    <button @click="newMenu = { id: null, name: '', price: '', desc: '', categoryId: 'beverages' }; showAddMenuModal = true" class="bg-brewlygreen text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-sm hover:bg-[#2A5E3E] transition">
                        Upload Files
                    </button>
                </div>
                <!-- Tips Card -->
                <div class="bg-[#FCF7F1] rounded-2xl p-6 relative flex items-stretch overflow-hidden h-[230px]">
                    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&display=swap" rel="stylesheet">
                    <!-- Left Content -->
                    <div class="z-20 w-1/2 flex flex-col justify-center pl-2">
                        <div class="text-[#D9652A] mb-3 flex items-center gap-1">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 1L14.8 9.2L23 12L14.8 14.8L12 23L9.2 14.8L1 12L9.2 9.2L12 1Z"/>
                            </svg>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="mt-4 -ml-1">
                                <path d="M12 1L14.8 9.2L23 12L14.8 14.8L12 23L9.2 14.8L1 12L9.2 9.2L12 1Z"/>
                            </svg>
                        </div>
                        <h3 class="font-bold text-[22px] leading-[1.2] text-[#1A1A1A] mb-2 tracking-tight">A great menu<br>brings people in.</h3>
                        <p class="text-[13px] text-[#666666] mb-5 leading-relaxed pr-2 font-medium">Add clear photos and organized<br>categories to make your menu<br>shine online.</p>
                        <a href="#" class="text-[#D9652A] font-bold text-[13px] hover:underline flex items-center gap-1.5 transition-colors">
                            Tips for a great menu 
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                        </a>
                    </div>
                    
                    <!-- Right Content (Image & Decorations) -->
                    <div class="absolute right-0 top-0 bottom-0 w-[55%] pointer-events-none">
                        <!-- Background offset shape -->
                        <div class="absolute left-4 top-8 w-[210px] h-[155px] bg-[#F2EAE0] rounded-[32px]"></div>
                        
                        <!-- Main Image -->
                        <img src="https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=400&fit=crop" class="absolute left-10 top-12 z-10 w-[190px] h-[130px] object-cover rounded-[20px] shadow-sm">
                        
                        <!-- Handwritten Text (Good Food Better Days) -->
                        <div class="absolute right-6 top-10 text-[#4A443E] leading-[1] text-3xl transform -rotate-6" style="font-family: 'Alex Brush', cursive;">
                            Good<br>Food<br>Better<br>Days
                        </div>
                        
                        <!-- Sunburst bottom right -->
                        <div class="absolute right-6 bottom-8 text-[#D9652A]">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                <path d="M21 9 L17 12" />
                                <path d="M22 15 L16 15" />
                                <path d="M21 21 L17 18" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Filter -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span class="font-bold text-lg text-brewlytext">Categories</span>
                    <div class="flex gap-2">
                        <button @click="activeMenuFilter = 'all'" :class="activeMenuFilter === 'all' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            All Items <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'beverages'" :class="activeMenuFilter === 'beverages' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-coffee opacity-50"></i> Beverages <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='beverages').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'foods'" :class="activeMenuFilter === 'foods' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-utensils opacity-50"></i> Foods <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='foods').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'snacks'" :class="activeMenuFilter === 'snacks' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-cookie opacity-50"></i> Snacks <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='snacks').length"></span>
                        </button>
                        <button @click="activeMenuFilter = 'sweets'" :class="activeMenuFilter === 'sweets' ? 'bg-brewlygreen text-white' : 'bg-white border border-gray-200 text-brewlytext hover:bg-gray-50'" class="px-4 py-1.5 rounded-full text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-ice-cream opacity-50"></i> Sweets <span class="bg-black/10 px-1.5 py-0.5 rounded text-[10px]" x-text="menuItems.filter(i=>i.categoryId==='sweets').length"></span>
                        </button>
                    </div>
                </div>
                <button class="bg-white border border-gray-200 text-brewlytext px-4 py-2 rounded-full font-bold text-sm shadow-sm hover:bg-gray-50 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>

            <!-- Table Section -->
            <div class="bg-white border border-gray-200 rounded-2xl flex flex-col shadow-sm flex-grow">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-brewlytext">Menu Items <span class="text-brewlymuted font-normal text-sm" x-text="'(' + filteredMenuItems.length + ')'"></span></h3>
                    <div class="flex gap-3">
                        <select class="border border-gray-200 rounded-lg px-4 py-2 text-sm font-medium focus:outline-none focus:border-brewlygreen bg-white text-brewlytext">
                            <option>Sort by: Newest</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-brewlytext">
                        <thead>
                            <tr class="border-b border-gray-100 text-sm text-brewlymuted bg-gray-50/50">
                                <th class="p-4 w-12 text-center"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brewlygreen focus:ring-brewlygreen"></th>
                                <th class="p-4 font-semibold">Item</th>
                                <th class="p-4 font-semibold">Category</th>
                                <th class="p-4 font-semibold">Price</th>
                                <th class="p-4 font-semibold">Status</th>
                                <th class="p-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in filteredMenuItems" :key="item.id">
                                <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition group" :class="item.soldOut ? 'opacity-70 grayscale-[30%]' : ''">
                                    <td class="p-4 text-center align-middle"><input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-brewlygreen focus:ring-brewlygreen"></td>
                                    <td class="p-4 flex gap-4 items-center">
                                        <img :src="item.image || item.img" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1541167760496-1628856ab772?w=100&h=100&fit=crop'" class="w-12 h-12 rounded-lg object-cover border border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-sm" x-text="item.name"></span>
                                            <span class="text-xs text-brewlymuted line-clamp-1" x-text="item.desc || 'No description'"></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-3 py-1 bg-brewlylightgreen text-brewlygreen text-xs font-bold rounded-full capitalize" x-text="item.categoryId"></span>
                                    </td>
                                    <td class="p-4 font-semibold text-sm font-mono" x-text="formatRp(item.price)"></td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full" :class="item.soldOut ? 'bg-red-500' : 'bg-brewlygreen'"></div>
                                            <span class="text-sm font-semibold" :class="item.soldOut ? 'text-red-600' : 'text-brewlygreen'" x-text="item.soldOut ? 'Sold Out' : 'Published'"></span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="editMenu(item)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-brewlygreen hover:border-brewlygreen transition flex items-center justify-center" title="Edit"><i class="fas fa-edit text-xs"></i></button>
                                            <button @click="toggleSoldOut(item.id)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-brewlyorange hover:border-brewlyorange transition flex items-center justify-center" title="Toggle Status"><i class="fas fa-power-off text-xs"></i></button>
                                            <button @click="deleteMenu(item.id)" class="w-8 h-8 rounded border border-gray-200 bg-white text-gray-500 hover:text-red-500 hover:border-red-500 transition flex items-center justify-center" title="Delete"><i class="fas fa-trash text-xs"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- VIEW: ANALYTICS (OWNER) -->
        <div x-show="currentTab === 'analytics'" x-cloak class="flex-grow p-10 pt-4 overflow-auto hide-scroll bg-[#FAFAFA]">
            
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

        <!-- VIEW: TABLE & QR (OWNER) -->
        <div x-show="currentTab === 'qr'" x-cloak class="flex-grow p-8 overflow-auto hide-scroll">
            <div class="flex justify-between items-center mb-8">
                <!-- Removed redundant title since it's already in the top header -->
                <p class="text-gray-500 font-medium">Kelola QR Code untuk setiap meja secara dinamis.</p>
                <button @click="showAddTableModal = true; $nextTick(() => $refs.tableInput.focus())" class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2 shadow-sm hover:bg-[#154660] transition-colors">
                    <i class="fas fa-plus"></i> Tambah Meja Baru
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20">
                <template x-for="table in tables" :key="table.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-5 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-sans font-bold text-2xl text-textdark mb-1" x-text="table.id"></h4>
                                <div class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md inline-block uppercase tracking-wider">Aktif</div>
                            </div>
                            <div class="p-1.5 border border-gray-100 rounded-xl bg-gray-50 shrink-0">
                                <img :src="table.qr" alt="QR Code" class="w-20 h-20 mix-blend-multiply">
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="printQR(table)" class="flex-grow py-2.5 rounded-xl text-sm font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i> Print QR
                            </button>
                            <button @click="resetQR(table)" class="w-11 h-11 shrink-0 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center shadow-sm" title="Reset URL">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- VIEW: SETTINGS & BRANDING -->
        <div x-show="currentTab === 'settings'" x-cloak class="flex-grow p-8 overflow-auto hide-scroll">
            <div class="mb-8">
                <p class="text-gray-500 font-medium">Kelola informasi toko dan branding Anda.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-3xl">
                <form @submit.prevent="saveSettings" class="flex flex-col gap-6">
                    <!-- Shop Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Toko</label>
                        <input type="text" x-model="settings.name" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="Contoh: Bitten Coffee" required>
                    </div>

                    <!-- URL Slug -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">URL Slug Toko</label>
                        <div class="flex items-center">
                            <span class="bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl px-4 py-3 text-sm text-gray-500 font-mono flex-shrink-0" x-text="window.location.host + '/'"></span>
                            <input type="text" x-model="settings.slug" class="w-full border border-gray-200 rounded-r-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all" placeholder="bitten-coffee" required>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Ini akan menjadi alamat web unik untuk menu pelanggan Anda.</p>
                    </div>

                    <!-- Logo Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Logo Toko (Opsional)</label>
                        <div class="flex items-center gap-6">
                            <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden relative group">
                                <img x-show="settings.logoPreview" :src="settings.logoPreview" class="w-full h-full object-cover">
                                <i x-show="!settings.logoPreview" class="fas fa-image text-3xl text-gray-300"></i>
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" @click="$refs.logoInput.click()">
                                    <i class="fas fa-camera text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <input type="file" x-ref="logoInput" @change="handleLogoUpload" class="hidden" accept="image/*">
                                <button type="button" @click="$refs.logoInput.click()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-200 transition-colors">Pilih Gambar</button>
                                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG. Maksimal 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Primary Color -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Warna Utama (Opsional)</label>
                        <div class="flex items-center gap-3">
                            <input type="color" x-model="settings.primary_color" class="w-12 h-12 rounded cursor-pointer border-0 p-0 bg-transparent">
                            <input type="text" x-model="settings.primary_color" class="w-32 border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none uppercase" placeholder="#1E5A7A">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Warna ini akan digunakan sebagai warna tombol dan aksen di halaman menu pelanggan.</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-primary text-white px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:bg-[#154660] transition-colors flex items-center gap-2" :disabled="isSavingSettings">
                            <i class="fas fa-spinner fa-spin" x-show="isSavingSettings"></i>
                            <span x-text="isSavingSettings ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: ADD MENU -->
        <div x-show="showAddMenuModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div @click="showAddMenuModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[500px] rounded-lg shadow-xl flex flex-col" @keydown.escape.window="showAddMenuModal = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-heading font-extrabold text-lg text-[#2D3748]"><i class="fas fa-hamburger mr-2 text-[#1E5A7A]"></i> Tambah Menu Baru</h3>
                    <button @click="showAddMenuModal = false" class="text-gray-400 hover:text-red-500 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 flex flex-col gap-4 font-mono">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Kategori Menu</label>
                        <select x-model="newMenu.categoryId" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-[#1E5A7A] focus:outline-none bg-white">
                            <option value="beverages">Beverages</option>
                            <option value="foods">Foods</option>
                            <option value="snacks">Snacks</option>
                            <option value="sweets">Sweets</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Nama Menu</label>
                        <input type="text" x-model="newMenu.name" placeholder="Misal: Matcha Latte" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-[#1E5A7A] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Harga (Rp)</label>
                        <input type="number" x-model="newMenu.price" placeholder="Misal: 25000" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-[#1E5A7A] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Deskripsi Singkat</label>
                        <textarea x-model="newMenu.desc" placeholder="Penjelasan singkat tentang menu ini..." rows="2" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-[#1E5A7A] focus:outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Upload Gambar (Opsional)</label>
                        <input type="file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#1E5A7A]/10 file:text-[#1E5A7A] hover:file:bg-[#1E5A7A]/20">
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 flex justify-end gap-2">
                    <button @click="showAddMenuModal = false" class="px-4 py-2 rounded text-sm font-bold text-gray-500 hover:bg-gray-50 transition">Batal</button>
                    <button @click="saveNewMenu" class="px-4 py-2 rounded text-sm font-bold bg-[#1E5A7A] text-white shadow-sm hover:bg-[#154660] transition">Simpan Menu</button>
                </div>
            </div>
        </div>

        <!-- MODAL: ADD TABLE -->
        <div x-show="showAddTableModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div @click="showAddTableModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[400px] rounded-2xl shadow-xl flex flex-col overflow-hidden" @keydown.escape.window="showAddTableModal = false">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-sans font-bold text-lg text-primary flex items-center gap-2"><i class="fas fa-qrcode text-accent"></i> Tambah Meja Baru</h3>
                    <button @click="showAddTableModal = false" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-5 flex flex-col gap-4 font-mono">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase tracking-wider">Nama / Nomor Meja</label>
                        <input x-ref="tableInput" type="text" x-model="newTableId" @keydown.enter="addTableFromModal" placeholder="Misal: Meja 07" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:border-primary focus:outline-none">
                    </div>
                </div>
                <div class="p-5 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50">
                    <button @click="showAddTableModal = false" class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 hover:bg-gray-100 transition">Batal</button>
                    <button @click="addTableFromModal" class="px-4 py-2 rounded-lg text-sm font-bold bg-primary text-white shadow-sm hover:bg-[#154660] transition flex items-center gap-2">Generate QR <i class="fas fa-arrow-right text-xs"></i></button>
                </div>
            </div>
        </div>

        <!-- MODAL: ORDER DETAIL -->
        <div x-show="showOrderDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
            <!-- Backdrop -->
            <div @click="showOrderDetailModal = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[500px] max-h-[90vh] rounded-2xl shadow-xl flex flex-col overflow-hidden" @keydown.escape.window="showOrderDetailModal = false" x-show="selectedOrder">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h3 class="font-sans font-bold text-xl text-primary flex items-center gap-2" x-text="'Struk Pesanan #' + (selectedOrder ? selectedOrder.id : '')"></h3>
                        <p class="text-xs text-gray-500 font-mono mt-1" x-text="selectedOrder ? selectedOrder.time : ''"></p>
                    </div>
                    <button @click="showOrderDetailModal = false" class="text-gray-400 hover:text-red-500 transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-white border border-transparent hover:border-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Body (Scrollable) -->
                <div class="p-6 flex-grow overflow-y-auto font-mono">
                    <template x-if="selectedOrder">
                        <div>
                            <!-- Customer Info -->
                            <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-gray-200">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pelanggan</p>
                                    <p class="text-base font-bold text-textdark flex items-center gap-2"><i class="fas fa-user-circle text-primary"></i> <span x-text="selectedOrder.customer"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tipe Pesanan</p>
                                    <p class="text-base font-bold text-textdark" x-text="selectedOrder.type + (selectedOrder.type === 'Dine-in' || selectedOrder.type === 'Dine In' ? ' (' + selectedOrder.table + ')' : '')"></p>
                                </div>
                            </div>
                            
                            <!-- Order Items -->
                            <div class="mb-4">
                                <div class="flex justify-between text-[10px] text-gray-400 font-bold uppercase mb-3 px-1">
                                    <span>Item</span>
                                    <div class="flex gap-4 w-32 justify-end">
                                        <span class="w-8 text-center">Qty</span>
                                        <span class="w-20 text-right">Subtotal</span>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <template x-for="item in selectedOrder.items">
                                        <div class="flex justify-between text-sm items-center bg-gray-50/50 p-2 rounded-lg border border-gray-100">
                                            <div class="flex flex-col pr-2 flex-grow">
                                                <span x-text="item.name" class="font-bold text-gray-700"></span>
                                                <span x-show="item.notes" class="text-[11px] text-red-500 italic mt-0.5"><i class="fas fa-comment-dots text-[10px] mr-1"></i><span x-text="item.notes"></span></span>
                                            </div>
                                            <div class="flex gap-4 w-32 justify-end flex-shrink-0 items-center">
                                                <span class="w-8 text-center font-bold text-gray-600 bg-white border border-gray-200 rounded px-1 py-0.5" x-text="item.qty + 'x'"></span>
                                                <span class="w-20 text-right font-bold text-primary" x-text="formatRp(item.price * item.qty)"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Payment Info -->
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center text-sm mb-2 text-gray-500">
                                    <span>Metode Pembayaran</span>
                                    <span class="font-bold text-textdark bg-[#00569c]/10 text-[#00569c] px-2 py-0.5 rounded text-xs"><i class="fas fa-qrcode mr-1"></i>QRIS</span>
                                </div>
                                <div class="flex justify-between items-center text-lg mt-4">
                                    <span class="font-bold text-gray-600">Total Harga</span>
                                    <span class="font-extrabold text-2xl text-primary" x-text="formatRp(selectedOrder.total)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Footer Actions -->
                <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex gap-3">
                    <button @click="showOrderDetailModal = false" class="flex-grow py-3 rounded-xl text-sm font-bold text-gray-500 border border-gray-200 bg-white hover:bg-gray-50 transition">
                        Tutup
                    </button>
                    <button @click="window.print()" class="flex-grow py-3 rounded-xl text-sm font-bold bg-primary text-white shadow hover:bg-[#154660] transition flex justify-center items-center gap-2">
                        <i class="fas fa-print"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL: INCOMING ORDER ALERT -->
        <div x-show="incomingOrder" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/70 backdrop-blur-md"></div>
            <!-- Modal Content -->
            <div class="relative bg-white w-[500px] rounded-2xl shadow-2xl flex flex-col overflow-hidden animate-bounce" style="animation-iteration-count: 3; animation-duration: 0.5s;">
                <!-- Header -->
                <div class="bg-yellow-400 p-6 flex flex-col items-center justify-center border-b-4 border-yellow-500">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg mb-3">
                        <i class="fas fa-bell text-3xl text-yellow-500 animate-pulse"></i>
                    </div>
                    <h2 class="font-heading font-extrabold text-2xl text-yellow-900 tracking-wide uppercase">Pesanan Baru Masuk!</h2>
                </div>
                
                <!-- Body -->
                <div class="p-8 text-center font-mono">
                    <template x-if="incomingOrder">
                        <div>
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1">Meja</p>
                            <p class="text-5xl font-sans font-extrabold text-primary mb-6" x-text="incomingOrder.table"></p>
                            
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6">
                                <p class="text-base font-bold text-textdark mb-1"><i class="fas fa-user-circle text-gray-400 mr-2"></i> <span x-text="incomingOrder.customer"></span></p>
                                <p class="text-xs text-gray-500" x-text="incomingOrder.items.length + ' Item â€¢ ' + formatRp(incomingOrder.total)"></p>
                            </div>
                        </div>
                    </template>
                    <p class="text-sm text-gray-500 italic">Pesanan ini sudah dibayar (QRIS) dan menunggu untuk diproses.</p>
                </div>
                
                <!-- Footer Actions -->
                <div class="p-6 border-t border-gray-100 bg-gray-50 flex gap-3">
                    <button @click="incomingOrder = null" class="w-1/3 py-4 rounded-xl text-sm font-bold text-gray-500 bg-white border border-gray-200 hover:bg-gray-100 transition">
                        Nanti Saja
                    </button>
                    <button @click="acceptIncomingOrder()" class="w-2/3 py-4 rounded-xl text-base font-bold bg-[#1E5A7A] text-white shadow-lg hover:bg-[#154660] hover:scale-[1.02] transition transform active:scale-95 flex justify-center items-center gap-2">
                        <i class="fas fa-fire"></i> Terima & Proses
                    </button>
                </div>
            </div>
        </div>

    </main>

    <script>
        window.INITIAL_DATA = {
            menu: @json($menuItems ?? []),
            orders: @json($orders ?? []),
            tables: @json($tables ?? []),
            shop: @json($shop ?? null)
        };

        const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardApp', () => ({
                currentTab: 'orders',
                showAddMenuModal: false,
                showAddTableModal: false,
                showOrderDetailModal: false,
                selectedOrder: null,
                incomingOrder: null,
                newTableId: '',
                activeMenuFilter: 'all',
                activeOrderFilter: 'process',
                searchQuery: '',
                newMenu: { id: null, name: '', price: '', desc: '', categoryId: '' },
                toasts: [],
                addToast(message, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 3000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },
                settings: {
                    name: '',
                    slug: '',
                    primary_color: '#1E5A7A',
                    logoPreview: '',
                    logoFile: null
                },
                isSavingSettings: false,
                get filteredMenuItems() {
                    let items = this.menuItems;
                    if (this.activeMenuFilter !== 'all') {
                        items = items.filter(m => m.categoryId === this.activeMenuFilter);
                    }
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(m => m.name.toLowerCase().includes(q) || m.desc.toLowerCase().includes(q));
                    }
                    return items;
                },
                get filteredOrders() {
                    let filtered = this.orders;
                    if (this.activeOrderFilter === 'process') {
                        filtered = filtered.filter(o => o.status === 'In Progress' || o.status === 'Ready' || o.status === 'Masuk');
                    } else if (this.activeOrderFilter === 'completed') {
                        filtered = filtered.filter(o => o.status === 'Completed');
                    }
                    
                    if (this.searchQuery) {
                        const q = this.searchQuery.toLowerCase();
                        filtered = filtered.filter(o => 
                            o.customer.toLowerCase().includes(q) || 
                            o.id.toString().includes(q) ||
                            (o.table && o.table.toLowerCase().includes(q))
                        );
                    }
                    return filtered;
                },
                tabs: [
                    { id: 'analytics', name: 'Dashboard Analytics', icon: 'fas fa-chart-pie' },
                    { id: 'orders', name: 'Live Orders', icon: 'fas fa-receipt' },
                    { id: 'menu', name: 'Menu CMS', icon: 'fas fa-hamburger' },
                    { id: 'qr', name: 'Table & QR', icon: 'fas fa-qrcode' },
                    { id: 'settings', name: 'Profile & Branding', icon: 'fas fa-store' },
                ],
                tables: [],
                baseUrl: window.location.origin + window.location.pathname.replace('dashboard.html', 'index.html'),
                getQRUrl(tableCode, token = '') {
                    const url = `${this.baseUrl}?table=${tableCode}${token ? '&token='+token : ''}`;
                    return `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(url)}`;
                },
                chartInstance: null,
                get totalRevenue() {
                    return this.orders.filter(o => o.status === 'Completed').reduce((sum, o) => sum + (o.total || 0), 0);
                },
                get totalOrders() {
                    return this.orders.length;
                },
                get totalSuccess() {
                    return this.orders.filter(o => o.status === 'Completed').length;
                },
                get successRate() {
                    if (this.orders.length === 0) return 100;
                    return Math.round((this.totalSuccess / this.orders.length) * 100);
                },
                get topMenuItems() {
                    const counts = {};
                    this.orders.forEach(order => {
                        (order.items || []).forEach(item => {
                            counts[item.name] = (counts[item.name] || 0) + item.qty;
                        });
                    });
                    
                    let sorted = Object.keys(counts).map(name => {
                        const menuItem = this.menuItems.find(m => m.name === name);
                        const imgSrc = menuItem ? (menuItem.img || menuItem.image) : null;
                        return { 
                            name, 
                            count: counts[name], 
                            img: imgSrc || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop' // Coffee dummy
                        };
                    }).sort((a, b) => b.count - a.count);
                    
                    // Jika data kosong, gunakan permintaan user sebagai fallback
                    if (sorted.length === 0) {
                        const fallbackNames = ['Americano', 'Vanilla Latte', 'Irish Latte'];
                        return fallbackNames.map(name => {
                            const menuItem = this.menuItems.find(m => m.name === name);
                            const imgSrc = menuItem ? (menuItem.img || menuItem.image) : null;
                            return {
                                name: name,
                                count: 0,
                                img: imgSrc || 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=100&h=100&fit=crop'
                            };
                        });
                    }
                    
                    return sorted.slice(0, 3);
                },
                formatRevenue(num) {
                    if (num >= 1000000) return 'Rp ' + (num / 1000000).toFixed(2) + 'M';
                    if (num >= 1000) return 'Rp ' + (num / 1000).toFixed(1) + 'K';
                    return 'Rp ' + num;
                },
                init() {
                    this.tables = [
                        { id: 'Meja 01', qr: this.getQRUrl('01') },
                        { id: 'Meja 02', qr: this.getQRUrl('02') },
                        { id: 'Meja 03', qr: this.getQRUrl('03') },
                        { id: 'Meja 04', qr: this.getQRUrl('04') },
                        { id: 'Meja 05', qr: this.getQRUrl('05') },
                        { id: 'Meja 06', qr: this.getQRUrl('06') },
                    ];
                    this.fetchLiveOrders(true);
                    
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'bitten_orders') {
                            this.fetchLiveOrders();
                        }
                    });

                    this.$watch('currentTab', (val) => {
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });
                },
                initChart() {
                    const ctx = document.getElementById('salesChart');
                    if (ctx && !this.chartInstance) {
                        this.chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: [{
                                    label: 'Revenue',
                                    data: [1.2, 1.9, 1.5, 2.1, 1.8, 3.2, 4.25],
                                    borderColor: '#8C5D3A',
                                    backgroundColor: 'rgba(140, 93, 58, 0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#8C5D3A',
                                    pointBorderWidth: 2,
                                    pointRadius: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: { 
                                    y: { 
                                        beginAtZero: true, 
                                        grid: { borderDash: [5, 5] },
                                        ticks: { callback: function(value) { return value + 'M'; } }
                                    },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                },
                orders: [],
                menuItems: [],
                init() {
                    this.loadMenu();
                    
                    this.tables = window.INITIAL_DATA.tables.map(t => ({
                        id: t.name,
                        qr: t.qr_code_url || this.getQRUrl(t.name)
                    }));
                    
                    if (window.INITIAL_DATA.shop) {
                        this.settings.name = window.INITIAL_DATA.shop.name || '';
                        this.settings.slug = window.INITIAL_DATA.shop.slug || '';
                        this.settings.primary_color = window.INITIAL_DATA.shop.primary_color || '#1E5A7A';
                        if (window.INITIAL_DATA.shop.logo_url) {
                            this.settings.logoPreview = '/storage/' + window.INITIAL_DATA.shop.logo_url;
                        }
                    }
                    
                    this.fetchLiveOrders(true);
                    
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'bitten_orders' || e.key === 'bitten_menu') {
                            // Can't really sync across tabs without websockets in Laravel, 
                            // but we can leave this or reload page.
                            window.location.reload();
                        }
                    });

                    this.$watch('currentTab', (val) => {
                        if (val === 'analytics') {
                            setTimeout(() => this.initChart(), 50);
                        }
                    });
                },
                loadMenu() {
                    this.menuItems = window.INITIAL_DATA.menu.map(m => ({
                        ...m,
                        categoryId: m.category_name,
                        desc: m.description,
                        tags: m.tags || []
                    }));
                },
                editMenu(item) {
                    this.newMenu = { id: item.id, name: item.name, price: item.price, desc: item.desc, categoryId: item.categoryId || 'beverages' };
                    this.showAddMenuModal = true;
                },
                deleteMenu(id) {
                    if (confirm("Yakin ingin menghapus menu ini? (Belum diimplementasikan API-nya)")) {
                        this.menuItems = this.menuItems.filter(m => m.id !== id);
                    }
                },
                saveNewMenu() {
                    if(!this.newMenu.name || !this.newMenu.price || !this.newMenu.categoryId) {
                        this.addToast('Nama, Harga, dan Kategori wajib diisi!', 'error');
                        return;
                    }
                    
                    fetch('/admin/api/menu', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newMenu)
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            if (this.newMenu.id) {
                                const item = this.menuItems.find(m => m.id === this.newMenu.id);
                                if (item) {
                                    item.name = this.newMenu.name;
                                    item.price = parseInt(this.newMenu.price);
                                    item.desc = this.newMenu.desc;
                                    item.categoryId = this.newMenu.categoryId;
                                }
                            } else {
                                data.menu.categoryId = data.menu.category_name;
                                data.menu.desc = data.menu.description;
                                this.menuItems.unshift(data.menu);
                            }
                            this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '' };
                            this.showAddMenuModal = false;
                        }
                    });
                },
                viewOrderDetails(order) {
                    this.selectedOrder = order;
                    this.showOrderDetailModal = true;
                },
                updateStatus(id, newStatus) {
                    fetch(`/admin/api/orders/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: newStatus })
                    }).then(() => {
                        const order = this.orders.find(o => o.id === id);
                        if (order) order.status = newStatus;
                    });
                },
                logout() {
                    // Redirect to actual laravel logout
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(() => window.location.href = '/');
                },
                toggleSoldOut(id) {
                    fetch(`/admin/api/menu/${id}/toggle`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(res => res.json()).then(data => {
                        const item = this.menuItems.find(m => m.id === id);
                        if (item) item.is_sold_out = data.is_sold_out;
                    });
                },
                acceptIncomingOrder() {
                    if (this.incomingOrder) {
                        this.updateStatus(this.incomingOrder.id, 'In Progress');
                        this.incomingOrder = null;
                    }
                },
                fetchLiveOrders(isInit = false) {
                    const dbOrders = window.INITIAL_DATA.orders.map(o => ({
                        id: o.id,
                        customer: o.customer_name || ('Meja ' + (o.table ? o.table.name : '-')),
                        status: o.status,
                        total: o.total_price,
                        time: o.created_at,
                        items: o.items.map(i => ({ name: i.product.name, qty: i.quantity, price: i.price }))
                    }));
                    
                    if (!isInit && dbOrders.length > 0 && dbOrders[0].status === 'Masuk' && (!this.orders.length || dbOrders[0].id !== this.orders[0].id)) {
                        const chime = document.getElementById('chime-sound');
                        if (chime) {
                            chime.currentTime = 0;
                            chime.play().catch(e => console.log('Audio autoplay blocked', e));
                        }
                        this.incomingOrder = dbOrders[0];
                    }
                    this.orders = dbOrders;
                },
                addTableFromModal() {
                    if (!this.newTableId.trim()) {
                        this.addToast('Silakan masukkan nama/nomor meja!', 'error');
                        return;
                    }
                    
                    const tableNumStr = this.newTableId.replace(/[^a-zA-Z0-9]/g, ''); // bersihkan untuk URL
                    this.tables.push({
                        id: this.newTableId.trim(),
                        qr: this.getQRUrl(tableNumStr)
                    });
                    
                    this.newTableId = '';
                    this.showAddTableModal = false;
                },
                printQR(table) {
                    if(window.printQRWindow) window.printQRWindow(table);
                },
                resetQR(table) {
                    if(confirm(`Yakin ingin mereset/mengganti URL QR Code untuk ${table.id}? URL lama tidak akan bisa diakses lagi.`)) {
                        const randomToken = Math.random().toString(36).substring(2, 8);
                        const tableNum = table.id.replace('Meja ', '');
                        table.qr = this.getQRUrl(tableNum, randomToken);
                    }
                },
                handleLogoUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    if (file.size > 2 * 1024 * 1024) {
                        this.addToast('Ukuran file maksimal 2MB', 'error');
                        return;
                    }
                    
                    this.settings.logoFile = file;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.settings.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },
                saveSettings() {
                    if (!this.settings.name || !this.settings.slug) {
                        this.addToast('Nama dan Slug toko wajib diisi!', 'error');
                        return;
                    }
                    
                    this.isSavingSettings = true;
                    
                    const formData = new FormData();
                    formData.append('name', this.settings.name);
                    formData.append('slug', this.settings.slug);
                    formData.append('primary_color', this.settings.primary_color);
                    if (this.settings.logoFile) {
                        formData.append('logo', this.settings.logoFile);
                    }
                    
                    fetch('/admin/api/settings', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingSettings = false;
                        if (data.success) {
                            this.addToast('Pengaturan berhasil disimpan!', 'success');
                            if (data.logo_url) {
                                this.settings.logoPreview = '/storage/' + data.logo_url;
                                this.settings.logoFile = null;
                            }
                        } else {
                            this.addToast('Gagal menyimpan pengaturan: ' + (data.message || 'Error tidak diketahui'), 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingSettings = false;
                        this.addToast('Terjadi kesalahan jaringan.', 'error');
                        console.error(err);
                    });
                }
            }))
        });
    </script>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[9999] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 scale-95"
                 class="px-5 py-3.5 rounded-2xl shadow-xl font-bold text-sm flex items-center gap-3 pointer-events-auto border min-w-[300px] justify-between"
                 :class="toast.type === 'error' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-900 text-white border-gray-800'">
                <div class="flex items-center gap-3">
                    <i class="fas text-lg" :class="toast.type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'"></i>
                    <span x-text="toast.message"></span>
                </div>
                <button @click="removeToast(toast.id)" class="opacity-50 hover:opacity-100 transition-opacity">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Floating Action Button to Customer Menu -->
    <a href="index.html" target="_blank" class="fixed bottom-10 right-10 bg-[#1E5A7A] text-white p-4 rounded-full shadow-2xl flex items-center justify-center hover:scale-105 hover:bg-[#154660] transition-all z-[100] group border-4 border-white">
        <i class="fas fa-store text-xl"></i>
        <span class="max-w-0 overflow-hidden whitespace-nowrap group-hover:max-w-[200px] transition-all duration-500 ease-in-out pl-0 group-hover:pl-3 font-bold text-sm">Lihat Web Pelanggan</span>
    </a>
</body>
</html>
