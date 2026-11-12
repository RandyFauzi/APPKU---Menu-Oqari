@props(['dashboard' => false])
@auth
@php
$member = auth()->user();
$cafe = $member?->shop;
$groups = [
    'Operasional' => [
        ['access-pos', 'Kasir', 'fa-cash-register', 'admin.pos.index', 'images/sparta/nav-kasir.svg'],
        ['view-orders', 'Pesanan Aktif', 'fa-receipt', 'admin.orders.index', 'images/sparta/nav-pesanan.svg'],
        ['record-attendance', 'Absensi', 'fa-user-clock', 'admin.attendance.index', 'images/sparta/nav-absensi.svg'],
        ['view-kitchen', 'Layar Dapur', 'fa-fire-burner', 'admin.kitchen.index', 'images/sparta/nav-dapur.svg'],
        ['manage-register', 'Rekonsiliasi Kas', 'fa-wallet', 'admin.register.index', 'images/sparta/nav-rekonsiliasi.svg'],
    ],
    'Menu & Stok' => [
        ['manage-menu', 'Kelola Menu', 'fa-burger', 'admin.menu.index', 'images/sparta/nav-menu.svg'],
        ['manage-inventory', 'Bahan & Stok', 'fa-boxes-stacked', 'admin.inventory.index', 'images/sparta/nav-stok.svg'],
    ],
    'Manajemen Tim' => [
        ['manage-users', 'Manajemen Staf', 'fa-user-shield', 'admin.users.index', 'images/sparta/nav-staf.svg'],
        ['manage-crew', 'Jadwal Shift', 'fa-calendar-days', 'admin.shifts.index', 'images/sparta/nav-shift.svg'],
        ['view-own-schedule', 'Jadwal Saya', 'fa-calendar', 'admin.my-schedule', 'images/sparta/nav-jadwal-saya.svg'],
    ],
    'Laporan' => [
        ['view-reports', 'Laporan Keuangan', 'fa-book', 'admin.reports.index', 'images/sparta/nav-laporan.svg'],
        ['view-reports', 'Log Aktivitas', 'fa-clock-rotate-left', 'admin.activity-logs.index', 'images/sparta/nav-log.svg'],
    ],
    'Pengaturan' => [
        ['manage-settings', 'Meja & QR', 'fa-qrcode', 'admin.tables.index', 'images/sparta/nav-meja-qr.svg'],
        ['manage-settings', 'Toko Saya', 'fa-store', 'admin.settings.index', 'images/sparta/nav-toko.svg'],
        ['manage-settings', 'Jam Operasional', 'fa-clock', 'admin.operating-hours.index', 'images/sparta/clock.svg'],
        ['manage-settings', 'Pengaturan Struk', 'fa-print', 'admin.receipt-settings.index', 'images/sparta/nav-struk.svg'],
        ['manage-hpp', 'Kalkulator HPP', 'fa-calculator', 'admin.hpp.index', 'images/sparta/nav-hpp.svg'],
        ['manage-settings', 'Voucher Promo', 'fa-ticket-alt', 'admin.vouchers.index', 'images/sparta/nav-voucher.svg'],
    ],
];
if ($member?->role === 'superadmin') {
    $groups = ['Administrasi Platform' => [
        [null, 'Dashboard', 'fa-chart-pie', 'superadmin.dashboard', null],
        [null, 'Manajemen Toko', 'fa-store', 'superadmin.shops.index', null],
        [null, 'Manajemen Pengguna', 'fa-users', 'superadmin.users.index', null],
    ]];
}
@endphp
<div class="contents" data-cafe-navigation x-data="{{ $dashboard ? '{}' : '{ isMinimized: window.innerWidth < 1024, mobileNavOpen: false }' }}" @toggle-mobile-nav.window="mobileNavOpen = !mobileNavOpen" @keydown.escape.window="mobileNavOpen = false">
    <div class="cafe-nav-backdrop md:hidden" x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" aria-hidden="true"></div>
    <aside id="app-sidebar" data-cafe-sidebar class="app-sidebar transition-[width] duration-200 bg-[#f8f9fa] border-r border-[#e9ebf0]" :class="{ 'is-collapsed': isMinimized && !mobileNavOpen, 'is-mobile-open': mobileNavOpen }" aria-label="Navigasi aplikasi">
        
        <!-- Sidebar Brand Header (Dynamic user/shop logo system maintained!) -->
        <div class="shrink-0 flex items-center px-4 border-b border-[#e9ebf0] transition-all" :class="isMinimized && !mobileNavOpen ? 'flex-col justify-center py-3 gap-2' : 'h-20 gap-3'">
            <a href="{{ route($member->homeRoute()) }}" class="shrink-0 flex items-center justify-center" aria-label="Beranda aplikasi">
                <img id="sidebar-logo" src="{{ $cafe?->logo_url ?: asset('logo-oqari.webp') }}" alt="Logo {{ $cafe?->name ?? 'OQARI' }}" class="w-10 h-10 rounded-lg object-contain">
            </a>
            <div x-show="!isMinimized || mobileNavOpen" class="min-w-0 flex-1">
                <p class="font-bold text-sm text-gray-900 truncate leading-tight">{{ $cafe?->name ?? 'OQARI' }}</p>
                <p class="text-[11px] text-gray-500 mt-0.5">{{ $member->role === 'superadmin' ? 'Administrasi Platform' : 'Cafe Management' }}</p>
            </div>
            <button type="button" @click="isMinimized = !isMinimized" class="hidden md:flex p-1.5 rounded-lg text-gray-500 hover:text-gray-800 hover:bg-gray-200/70 border border-transparent hover:border-gray-200 transition-all items-center justify-center" :title="isMinimized ? 'Lebarkan Sidebar' : 'Kecilkan Sidebar'" aria-label="Ubah ukuran sidebar" x-show="!mobileNavOpen">
                <img src="{{ asset('images/sparta/panel-collapse.svg') }}" class="w-4 h-4 transition-transform duration-200" :class="isMinimized ? 'rotate-180' : ''" alt="">
            </button>
            <button type="button" @click="mobileNavOpen = false" class="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors" title="Tutup Menu" x-show="mobileNavOpen">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="sidebar-scroll flex-1 min-h-0 overflow-y-auto px-3 py-3" aria-label="Menu utama" x-data="sidebarScroll('{{ $member->id }}')" @scroll.passive="savePosition()" @resize.window="$nextTick(() => revealActive())">
            
            @if($member?->role !== 'superadmin')
                <!-- Top Primary Dashboard Link (Sparta Style) -->
                <div class="mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link group relative transition-all {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" title="Dashboard" aria-label="Dashboard" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        @if(request()->routeIs('admin.dashboard'))<span class="absolute left-0 top-2 bottom-2 w-[3px] bg-[#ea580c] rounded-r"></span>@endif
                        <img src="{{ asset('images/sparta/nav-dashboard.svg') }}" class="nav-icon-dashboard w-[18px] h-[18px] shrink-0 transition-all" alt="">
                        <span x-show="!isMinimized || mobileNavOpen" class="truncate font-medium">Dashboard</span>
                    </a>
                </div>
            @endif

            <!-- Group Links -->
            @foreach($groups as $heading => $links)
                @php $visibleLinks = array_filter($links, fn ($link) => $link[0] === null || $member->can($link[0])); @endphp
                @if(count($visibleLinks))
                    <p class="sidebar-heading" x-show="!isMinimized || mobileNavOpen">{{ $heading }}</p>
                    <div class="my-3 border-t border-gray-200" x-show="isMinimized && !mobileNavOpen" aria-hidden="true"></div>
                    @foreach($visibleLinks as [$ability, $label, $icon, $routeName, $svgIcon])
                        @php
                            $active = request()->routeIs($routeName, str_replace('.index', '.*', $routeName));
                            $destination = route($routeName);
                            $ariaLabel = $label === 'Kasir' ? 'POS (Kasir)' : $label;
                        @endphp
                        <a href="{{ $destination }}" class="sidebar-link group relative transition-all {{ $active ? 'is-active' : '' }}" title="{{ $label }}" aria-label="{{ $ariaLabel }}" @if($active) aria-current="page" @endif>
                            @if($active)<span class="absolute left-0 top-2 bottom-2 w-[3px] bg-[#ea580c] rounded-r"></span>@endif
                            @if($svgIcon)
                                <img src="{{ asset($svgIcon) }}" class="nav-icon-mono w-[18px] h-[18px] shrink-0 opacity-80 group-hover:opacity-100 transition-all" alt="">
                            @else
                                <i class="fas {{ $icon }}" aria-hidden="true"></i>
                            @endif
                            <span x-show="!isMinimized || mobileNavOpen" class="truncate">{{ $label }}</span>
                        </a>
                    @endforeach
                @endif
            @endforeach
        </nav>

        <!-- Bottom User Profile Card (Sparta Figma Style) -->
        <div class="shrink-0 border-t border-[#e9ebf0] p-3 bg-transparent space-y-1">
            <a href="{{ route('profile.edit') }}" aria-label="Profil Saya" class="flex items-center gap-2.5 px-2 py-2 rounded-xl hover:bg-gray-100/70 transition-colors group">
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
            </a>
            
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
