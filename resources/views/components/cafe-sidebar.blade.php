@props(['dashboard' => false])
@auth
@php
$member = auth()->user();
$cafe = $member?->shop;
$groups = [
    'Operasional' => [
        ['access-pos', 'Kasir', 'fa-cash-register', 'admin.pos.index', null, 'images/sparta/nav-kasir.svg'],
        ['view-orders', 'Pesanan Aktif', 'fa-receipt', 'admin.dashboard', 'orders', 'images/sparta/nav-pesanan.svg'],
        ['record-attendance', 'Absensi', 'fa-user-clock', 'admin.attendance.index', null, 'images/sparta/nav-absensi.svg'],
        ['view-kitchen', 'Layar Dapur', 'fa-fire-burner', 'admin.kitchen.index', null, null],
        ['manage-register', 'Rekonsiliasi Kas', 'fa-wallet', 'admin.register.index', null, null],
    ],
    'Menu & Stok' => [
        ['manage-menu', 'Kelola Menu', 'fa-burger', 'admin.dashboard', 'menu', 'images/sparta/nav-menu.svg'],
        ['manage-inventory', 'Bahan & Stok', 'fa-boxes-stacked', 'admin.dashboard', 'inventory', 'images/sparta/nav-stok.svg'],
    ],
    'Manajemen Tim' => [
        ['manage-users', 'Manajemen Staf', 'fa-user-shield', 'admin.users.index', null, 'images/sparta/nav-staf.svg'],
        ['manage-crew', 'Jadwal Shift', 'fa-calendar-days', 'admin.dashboard', 'shifts', 'images/sparta/nav-shift.svg'],
        ['view-attendance', 'Rekap Absensi', 'fa-calendar-check', 'admin.attendance.report', null, null],
        ['view-own-schedule', 'Jadwal Saya', 'fa-calendar', 'admin.my-schedule', null, null],
    ],
    'Laporan' => [
        ['view-reports', 'Laporan Keuangan', 'fa-book', 'admin.dashboard', 'report', 'images/sparta/nav-laporan.svg'],
        ['view-reports', 'Log Aktivitas', 'fa-clock-rotate-left', 'admin.dashboard', 'logs', 'images/sparta/nav-log.svg'],
        ['view-reports', 'Analytics Detail', 'fa-chart-pie', 'admin.dashboard', 'analytics', null],
    ],
    'Pengaturan' => [
        ['manage-settings', 'Meja & QR', 'fa-qrcode', 'admin.dashboard', 'qr', 'images/sparta/nav-meja-qr.svg'],
        ['manage-settings', 'Toko Saya', 'fa-store', 'admin.dashboard', 'settings', 'images/sparta/nav-toko.svg'],
        ['manage-settings', 'Pengaturan Struk', 'fa-print', 'admin.dashboard', 'receipt', 'images/sparta/nav-struk.svg'],
        ['manage-hpp', 'Kalkulator HPP', 'fa-calculator', 'admin.hpp.index', null, 'images/sparta/nav-hpp.svg'],
        ['manage-users', 'Pengguna & Akses', 'fa-shield-halved', 'admin.users.index', null, 'images/sparta/nav-pengguna.svg'],
    ],
];
if ($member?->role === 'superadmin') {
    $groups = ['Administrasi Platform' => [
        [null, 'Dashboard', 'fa-chart-pie', 'superadmin.dashboard', null, null],
        [null, 'Manajemen Toko', 'fa-store', 'superadmin.shops.index', null, null],
        [null, 'Manajemen Pengguna', 'fa-users', 'superadmin.users.index', null, null],
    ]];
}
@endphp
<div class="contents" data-cafe-navigation x-data="{{ $dashboard ? '{}' : '{ isMinimized: window.innerWidth < 1024, mobileNavOpen: false }' }}" @toggle-mobile-nav.window="mobileNavOpen = !mobileNavOpen" @keydown.escape.window="mobileNavOpen = false">
    <div class="cafe-nav-backdrop md:hidden" x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" aria-hidden="true"></div>
    <aside id="app-sidebar" data-cafe-sidebar class="app-sidebar transition-[width] duration-200 bg-[#f8f9fa] border-r border-[#e9ebf0]" :class="{ 'is-collapsed': isMinimized && !mobileNavOpen, 'is-mobile-open': mobileNavOpen }" aria-label="Navigasi aplikasi">
        
        <!-- Sidebar Brand Header (Dynamic user/shop logo system maintained!) -->
        <div class="h-20 shrink-0 flex items-center px-4 gap-3 border-b border-gray-200/70" :class="isMinimized && !mobileNavOpen ? 'justify-center' : ''">
            <a href="{{ route($member->homeRoute()) }}" class="shrink-0 flex items-center justify-center" aria-label="Beranda aplikasi">
                <img src="{{ $cafe?->logo_url ?: asset('logo-oqari.webp') }}" alt="Logo {{ $cafe?->name ?? 'OQARI' }}" class="w-10 h-10 rounded-lg object-contain">
            </a>
            <div x-show="!isMinimized || mobileNavOpen" class="min-w-0 flex-1">
                <p class="font-bold text-sm text-gray-900 truncate leading-tight">{{ $cafe?->name ?? 'OQARI' }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">{{ $member->role === 'superadmin' ? 'Administrasi Platform' : 'Cafe Management' }}</p>
            </div>
            <button type="button" @click="isMinimized = !isMinimized" class="hidden md:flex p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Toggle Sidebar" x-show="!isMinimized && !mobileNavOpen">
                <img src="{{ asset('images/sparta/panel-collapse.svg') }}" class="w-4 h-4" alt="">
            </button>
            <button type="button" @click="mobileNavOpen = false" class="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Tutup Menu" x-show="mobileNavOpen">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="sidebar-scroll flex-1 min-h-0 overflow-y-auto px-3 py-3" aria-label="Menu utama">
            
            @if($member?->role !== 'superadmin')
                <!-- Top Primary Dashboard Link (Sparta Style) -->
                <div class="mb-2">
                    @if($dashboard)
                        <button type="button" 
                            class="sidebar-link group relative transition-all" 
                            :class="{ 'is-active': currentTab === 'dashboard' }" 
                            @click="currentTab = 'dashboard'; mobileNavOpen = false" 
                            title="Dashboard" 
                            aria-label="Dashboard" 
                            :aria-current="currentTab === 'dashboard' ? 'page' : null">
                            <span x-show="currentTab === 'dashboard'" class="absolute left-0 top-2 bottom-2 w-[3px] bg-[#f9731f] rounded-r"></span>
                            <img src="{{ asset('images/sparta/nav-dashboard.svg') }}" class="w-[18px] h-[18px] shrink-0" alt="">
                            <span x-show="!isMinimized || mobileNavOpen" class="truncate font-medium">Dashboard</span>
                        </button>
                    @else
                        <a href="{{ route('admin.dashboard', ['tab' => 'dashboard']) }}" 
                            class="sidebar-link group relative transition-all {{ request()->routeIs('admin.dashboard') && (request('tab') === 'dashboard' || !request('tab')) ? 'is-active' : '' }}" 
                            title="Dashboard">
                            @if(request()->routeIs('admin.dashboard') && (request('tab') === 'dashboard' || !request('tab')))
                                <span class="absolute left-0 top-2 bottom-2 w-[3px] bg-[#f9731f] rounded-r"></span>
                            @endif
                            <img src="{{ asset('images/sparta/nav-dashboard.svg') }}" class="w-[18px] h-[18px] shrink-0" alt="">
                            <span x-show="!isMinimized || mobileNavOpen" class="truncate font-medium">Dashboard</span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- Group Links -->
            @foreach($groups as $heading => $links)
                @php $visibleLinks = array_filter($links, fn ($link) => $link[0] === null || $member->can($link[0])); @endphp
                @if(count($visibleLinks))
                    <p class="sidebar-heading" x-show="!isMinimized || mobileNavOpen">{{ $heading }}</p>
                    <div class="my-3 border-t border-gray-200" x-show="isMinimized && !mobileNavOpen" aria-hidden="true"></div>
                    @foreach($visibleLinks as [$ability, $label, $icon, $routeName, $tab, $svgIcon])
                        @php
                            $active = $tab ? request()->routeIs('admin.dashboard') && request('tab') === $tab : request()->routeIs(str_replace('.index', '.*', $routeName));
                            $destination = route($routeName, $tab ? ['tab' => $tab] : []);
                            $ariaLabel = $label === 'Kasir' ? 'POS (Kasir)' : $label;
                        @endphp
                        @if($dashboard && $tab)
                            <button type="button" class="sidebar-link" :class="{ 'is-active': currentTab === '{{ $tab }}' }" @click="currentTab = '{{ $tab }}'; mobileNavOpen = false" title="{{ $label }}" aria-label="{{ $ariaLabel }}" :aria-current="currentTab === '{{ $tab }}' ? 'page' : null">
                                @if($svgIcon)
                                    <img src="{{ asset($svgIcon) }}" class="w-[18px] h-[18px] shrink-0 opacity-80 group-hover:opacity-100" alt="">
                                @else
                                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                                @endif
                                <span x-show="!isMinimized || mobileNavOpen" class="truncate">{{ $label }}</span>
                            </button>
                        @else
                            <a href="{{ $destination }}" class="sidebar-link {{ $active ? 'is-active' : '' }}" title="{{ $label }}" aria-label="{{ $ariaLabel }}" @if($active) aria-current="page" @endif>
                                @if($svgIcon)
                                    <img src="{{ asset($svgIcon) }}" class="w-[18px] h-[18px] shrink-0 opacity-80 group-hover:opacity-100" alt="">
                                @else
                                    <i class="fas {{ $icon }}" aria-hidden="true"></i>
                                @endif
                                <span x-show="!isMinimized || mobileNavOpen" class="truncate">{{ $label }}</span>
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </nav>

        <!-- Bottom User Profile Card (Sparta Figma Style) -->
        <div class="shrink-0 border-t border-gray-200/80 p-3 bg-white/40 space-y-1">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl hover:bg-gray-100/70 transition-colors cursor-pointer group" @click="{{ $dashboard ? "currentTab = 'profile'" : "window.location.href = '" . route('profile.edit') . "'" }}">
                <div class="w-8 h-8 rounded-full bg-[#dce8ec] text-[#111827] flex items-center justify-center font-bold text-xs shrink-0">
                    {{ strtoupper(substr($member->name ?? 'A', 0, 2)) }}
                </div>
                <div x-show="!isMinimized || mobileNavOpen" class="min-w-0 flex-1">
                    <p class="text-[12px] font-semibold text-[#111827] truncate leading-tight">{{ $member->name }}</p>
                    <p class="text-[10px] text-[#7f837e] truncate mt-0.5">{{ $member->role === 'owner' ? 'Owner Kafe' : ($member->role === 'manager' ? 'Manajer Kafe' : ucfirst($member->role)) }}</p>
                </div>
                <div x-show="!isMinimized || mobileNavOpen" class="shrink-0 text-gray-400 group-hover:text-gray-600">
                    <img src="{{ asset('images/sparta/chevron-right.svg') }}" class="w-3 h-3" alt="">
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-0.5">
                @csrf
                <button type="submit" class="sidebar-link text-red-600 hover:text-red-700 hover:bg-red-50 text-xs py-1.5" title="Keluar" aria-label="Keluar">
                    <i class="fas fa-right-from-bracket text-sm" aria-hidden="true"></i>
                    <span x-show="!isMinimized || mobileNavOpen">Keluar</span>
                </button>
            </form>
        </div>
    </aside>
</div>

@endauth
