<!-- Sidebar -->
<aside class="w-64 bg-white shadow-xl h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-20 bg-primary text-white flex items-center justify-center border-b border-primary/20">
        <h2 class="text-2xl font-black tracking-wider">
            @if(Auth::user()->role === 'superadmin')
                APPKU<span class="text-accent">SaaS</span>
            @else
                {{ Auth::user()->shop ? Str::upper(Auth::user()->shop->name) : 'APPKU' }}
            @endif
        </h2>
    </div>

    <!-- User Profile Snippet -->
    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="flex-1 overflow-hidden">
            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500 uppercase font-semibold">{{ Auth::user()->role }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 hide-scroll">
        <ul class="space-y-1 px-3">
            
            @php $role = Auth::user()->role; @endphp

            @if($role === 'superadmin')
                <!-- SUPERADMIN MENU -->
                <li>
                    <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-satellite w-6"></i>
                        <span>SaaS Center</span>
                    </a>
                </li>
            @endif

            @if(in_array($role, ['owner', 'manager']))
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-home w-6"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            @endif

            @if(in_array($role, ['owner', 'manager', 'cashier']))
                <li>
                    <a href="{{ route('admin.pos') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.pos*') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-cash-register w-6"></i>
                        <span>POS</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.orders') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.orders*') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-box w-6"></i>
                        <span>Orders</span>
                    </a>
                </li>
            @endif

            @if(in_array($role, ['owner', 'manager', 'cashier', 'kitchen']))
                <li>
                    <a href="{{ route('admin.kitchen') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.kitchen*') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-fire-burner w-6"></i>
                        <span>Kitchen (KDS)</span>
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
                    </a>
                </li>
            @endif

            @if(in_array($role, ['owner', 'manager']))
                <li class="pt-4 pb-2">
                    <span class="px-4 text-xs font-extrabold text-gray-400 uppercase tracking-wider">Catalog & Stock</span>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-hamburger w-6"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-boxes-stacked w-6"></i>
                        <span>Inventory</span>
                    </a>
                </li>

                <li class="pt-4 pb-2">
                    <span class="px-4 text-xs font-extrabold text-gray-400 uppercase tracking-wider">Business</span>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-users w-6"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-wallet w-6"></i>
                        <span>Finance</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports') ?? '#' }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reports*') ? 'bg-primary text-white font-bold shadow-md' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                        <i class="fas fa-chart-pie w-6"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-id-badge w-6"></i>
                        <span>Staff & Shift</span>
                    </a>
                </li>
                
                <li class="pt-4 pb-2">
                    <span class="px-4 text-xs font-extrabold text-gray-400 uppercase tracking-wider">System</span>
                </li>
                <li>
                    <a href="#" class="flex items-center px-4 py-3 rounded-xl transition-all text-gray-600 hover:bg-primary/10 hover:text-primary">
                        <i class="fas fa-cog w-6"></i>
                        <span>Settings</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Logout Snippet -->
    <div class="p-4 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-bold transition-all">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>