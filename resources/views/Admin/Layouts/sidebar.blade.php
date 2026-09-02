@php $role = Auth::user()->role; @endphp

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CASHIER: Ultra-minimal — POS first                             --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@if($role === 'cashier')
<aside class="w-56 bg-gray-900 h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-14 flex items-center px-4 border-b border-gray-700">
        <span class="text-lg font-black text-green-400">OQARI</span>
        <span class="ml-auto text-xs text-gray-500 uppercase">Kasir</span>
    </div>
    <div class="p-3 border-b border-gray-700/50 flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="overflow-hidden">
            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
            <p class="text-[10px] text-green-400 font-semibold uppercase">Cashier</p>
        </div>
    </div>
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.pos*') ? 'bg-green-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-cash-register w-5"></i> POS
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
            <i class="fas fa-list-check w-5"></i> Orders
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
            <i class="fas fa-cash-register w-5"></i> Cash Register
        </a>
        <a href="{{ route('admin.my-schedule') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.my-schedule') ? 'bg-green-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-alt w-5"></i> My Shift
        </a>
    </nav>
    <div class="p-3 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-2 px-3 py-2.5 text-red-400 hover:bg-gray-800 rounded-xl text-sm font-bold transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- BARISTA / KITCHEN: Kitchen-first                              --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@elseif(in_array($role, ['barista', 'kitchen']))
<aside class="w-56 bg-gray-900 h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-14 flex items-center px-4 border-b border-gray-700">
        <i class="fas fa-fire-burner text-orange-400 mr-2"></i>
        <span class="text-lg font-black text-orange-400">KITCHEN</span>
    </div>
    <div class="p-3 border-b border-gray-700/50 flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-sm">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="overflow-hidden">
            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
            <p class="text-[10px] text-orange-400 font-semibold uppercase">{{ $role }}</p>
        </div>
    </div>
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('admin.kitchen.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.kitchen*') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-fire-burner w-5"></i> Kitchen
            <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">LIVE</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition">
            <i class="fas fa-list-check w-5"></i> Orders
        </a>
        <a href="{{ route('admin.my-schedule') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.my-schedule') ? 'bg-orange-600 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
            <i class="fas fa-calendar-alt w-5"></i> My Shift
        </a>
    </nav>
    <div class="p-3 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-2 px-3 py-2.5 text-red-400 hover:bg-gray-800 rounded-xl text-sm font-bold transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- CREW: Schedule-first, minimal                                 --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@elseif($role === 'crew')
<aside class="w-56 bg-white border-r border-gray-100 h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-14 flex items-center px-4 border-b border-gray-100">
        <span class="text-lg font-black text-primary">OQARI</span>
    </div>
    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div>
            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-400 uppercase font-semibold">Crew</p>
        </div>
    </div>
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('admin.my-schedule') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm transition {{ request()->routeIs('admin.my-schedule') ? 'bg-primary text-white shadow' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-calendar-check w-5"></i> Jadwal Saya
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-bullhorn w-5"></i> Pengumuman
        </a>
        <a href="#" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-50 transition">
            <i class="fas fa-user-circle w-5"></i> Profil
        </a>
    </nav>
    <div class="p-3 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center gap-2 px-3 py-2.5 text-red-400 hover:bg-red-50 rounded-xl text-sm font-bold transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SUPERADMIN                                                    --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@elseif($role === 'superadmin')
<aside class="w-64 bg-gray-900 h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-16 flex items-center px-5 border-b border-gray-700">
        <span class="text-xl font-black text-white">APPKU<span class="text-accent">SaaS</span></span>
    </div>
    <nav class="flex-1 p-3 space-y-1">
        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl font-bold text-sm text-gray-400 hover:bg-gray-800 hover:text-white transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
            <i class="fas fa-satellite w-5"></i> SaaS Center
        </a>
    </nav>
    <div class="p-3 border-t border-gray-700">
        <form method="POST" action="{{ route('logout') }}"><@csrf
            <button class="w-full flex items-center gap-2 px-3 py-2.5 text-red-400 hover:bg-gray-800 rounded-xl text-sm font-bold transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- OWNER / MANAGER: Full dashboard sidebar                       --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
@else
<aside class="w-64 bg-white shadow-xl h-screen fixed top-0 left-0 flex flex-col z-50">
    <div class="h-16 bg-primary text-white flex items-center justify-center border-b border-primary/20">
        <h2 class="text-xl font-black tracking-wider">{{ Str::upper(Auth::user()->shop?->name ?? 'OQARI') }}</h2>
    </div>
    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold">
            {{ substr(Auth::user()->name, 0, 1) }}
        </div>
        <div class="flex-1 overflow-hidden">
            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-400 uppercase font-semibold">{{ $role }}</p>
        </div>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 hide-scroll">
        <ul class="space-y-0.5 px-3">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fas fa-home w-5"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.pos.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('admin.pos*') ? 'bg-primary text-white shadow' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fas fa-cash-register w-5"></i> POS
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-list-check w-5"></i> Orders
                </a>
            </li>

            <li class="pt-3 pb-1"><span class="px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Katalog</span></li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-hamburger w-5"></i> Menu
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-boxes-stacked w-5"></i> Inventory
                </a>
            </li>

            <li class="pt-3 pb-1"><span class="px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Bisnis</span></li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-wallet w-5"></i> Finance
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('admin.reports*') ? 'bg-primary text-white shadow' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fas fa-chart-pie w-5"></i> Reports
                </a>
            </li>

            <li class="pt-3 pb-1"><span class="px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Tim</span></li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-users w-5"></i> Crew
                </a>
            </li>
            <li>
                <a href="{{ route('admin.shifts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold transition {{ request()->routeIs('admin.shifts*') ? 'bg-primary text-white shadow' : 'text-gray-600 hover:bg-primary/10 hover:text-primary' }}">
                    <i class="fas fa-calendar-days w-5"></i> Shift
                </a>
            </li>

            <li class="pt-3 pb-1"><span class="px-3 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Sistem</span></li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-plug w-5"></i> Integrasi
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-bold text-gray-600 hover:bg-primary/10 hover:text-primary transition">
                    <i class="fas fa-cog w-5"></i> Pengaturan
                </a>
            </li>
        </ul>
    </nav>
    <div class="p-3 border-t border-gray-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-red-400 hover:bg-red-50 rounded-xl text-sm font-bold transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>
@endif
