@props(['dashboard' => false])
@auth
@php
$member = auth()->user();
$cafe = $member?->shop;
$groups = [
    'Operasional' => [
        ['access-pos', 'POS (Kasir)', 'fa-cash-register', 'admin.pos.index', null],
        ['view-orders', 'Live Orders', 'fa-receipt', 'admin.dashboard', 'orders'],
        ['view-kitchen', 'Layar Dapur', 'fa-fire-burner', 'admin.kitchen.index', null],
        ['manage-register', 'Rekonsiliasi Kas', 'fa-wallet', 'admin.register.index', null],
    ],
    'Produk & Stok' => [
        ['manage-menu', 'Menu CMS', 'fa-burger', 'admin.dashboard', 'menu'],
        ['manage-inventory', 'Bahan & Stok', 'fa-boxes-stacked', 'admin.dashboard', 'inventory'],
        ['manage-hpp', 'Kalkulator HPP', 'fa-calculator', 'admin.hpp.index', null],
    ],
    'Tim Cafe' => [
        ['record-attendance', 'Absensi Saya', 'fa-user-clock', 'admin.attendance.index', null],
        ['view-attendance', 'Rekap Absensi', 'fa-calendar-check', 'admin.attendance.report', null],
        ['manage-crew', 'Jadwal Shift', 'fa-calendar-days', 'admin.dashboard', 'shifts'],
        ['view-own-schedule', 'Jadwal Saya', 'fa-calendar', 'admin.my-schedule', null],
        ['manage-users', 'User & Hak Akses', 'fa-user-shield', 'admin.users.index', null],
    ],
    'Laporan' => [
        ['view-reports', 'Dashboard Analytics', 'fa-chart-pie', 'admin.dashboard', 'analytics'],
        ['view-reports', 'Laporan Keuangan', 'fa-book', 'admin.dashboard', 'report'],
        ['view-reports', 'Log Aktivitas', 'fa-clock-rotate-left', 'admin.dashboard', 'logs'],
    ],
    'Pengaturan' => [
        ['manage-settings', 'Meja & QR', 'fa-qrcode', 'admin.dashboard', 'qr'],
        ['manage-settings', 'Toko Saya', 'fa-store', 'admin.dashboard', 'settings'],
        ['manage-settings', 'Pengaturan Struk', 'fa-print', 'admin.dashboard', 'receipt'],
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
<div class="contents" data-cafe-navigation x-data="{{ $dashboard ? '{}' : '{ isMinimized: window.innerWidth < 1024, mobileNavOpen: false }' }}" @keydown.escape.window="mobileNavOpen = false">
    <button type="button" class="cafe-nav-mobile" @click="mobileNavOpen = !mobileNavOpen" aria-label="Buka menu aplikasi" :aria-expanded="mobileNavOpen" aria-controls="app-sidebar"><i class="fas fa-bars" aria-hidden="true"></i><span>Menu</span></button>
    <div class="cafe-nav-backdrop md:hidden" x-show="mobileNavOpen" x-cloak @click="mobileNavOpen = false" aria-hidden="true"></div>
    <aside id="app-sidebar" data-cafe-sidebar class="app-sidebar transition-[width] duration-200" :class="{ 'is-collapsed': isMinimized && !mobileNavOpen, 'is-mobile-open': mobileNavOpen }" aria-label="Navigasi aplikasi">
        <div class="h-24 shrink-0 flex items-center px-4 gap-3 border-b border-gray-200/70" :class="isMinimized && !mobileNavOpen ? 'justify-center' : ''">
            <a href="{{ route($member->homeRoute()) }}" class="shrink-0" aria-label="Beranda aplikasi"><img src="{{ $cafe?->logo_url ?: asset('logo-oqari.webp') }}" alt="Logo {{ $cafe?->name ?? 'OQARI' }}" class="w-10 h-10 rounded-lg object-contain"></a>
            <div x-show="!isMinimized || mobileNavOpen" class="min-w-0"><p class="font-bold text-sm text-gray-900 truncate">{{ $cafe?->name ?? 'OQARI' }}</p><p class="text-[11px] text-gray-500 mt-1">{{ $member->role === 'superadmin' ? 'Administrasi Platform' : 'Cafe Management' }}</p></div>
        </div>
        <nav class="sidebar-scroll flex-1 min-h-0 overflow-y-auto px-3 pb-4" aria-label="Menu utama">
            @foreach($groups as $heading => $links)
                @php $visibleLinks = array_filter($links, fn ($link) => $link[0] === null || $member->can($link[0])); @endphp
                @if(count($visibleLinks))
                    <p class="sidebar-heading" x-show="!isMinimized || mobileNavOpen">{{ $heading }}</p>
                    <div class="my-3 border-t border-gray-200" x-show="isMinimized && !mobileNavOpen" aria-hidden="true"></div>
                    @foreach($visibleLinks as [$ability, $label, $icon, $routeName, $tab])
                        @php
                            $active = $tab ? request()->routeIs('admin.dashboard') && request('tab', 'orders') === $tab : request()->routeIs(str_replace('.index', '.*', $routeName));
                            $destination = route($routeName, $tab ? ['tab' => $tab] : []);
                        @endphp
                        @if($dashboard && $tab)
                            <button type="button" class="sidebar-link" :class="{ 'is-active': currentTab === '{{ $tab }}' }" @click="currentTab = '{{ $tab }}'; mobileNavOpen = false" title="{{ $label }}" aria-label="{{ $label }}" :aria-current="currentTab === '{{ $tab }}' ? 'page' : null"><i class="fas {{ $icon }}" aria-hidden="true"></i><span x-show="!isMinimized || mobileNavOpen" class="truncate">{{ $label }}</span></button>
                        @else
                            <a href="{{ $destination }}" class="sidebar-link {{ $active ? 'is-active' : '' }}" title="{{ $label }}" aria-label="{{ $label }}" @if($active) aria-current="page" @endif><i class="fas {{ $icon }}" aria-hidden="true"></i><span x-show="!isMinimized || mobileNavOpen" class="truncate">{{ $label }}</span></a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </nav>
        <div class="shrink-0 border-t border-gray-200 p-3 space-y-1">
            <button type="button" class="sidebar-link hidden md:flex" @click="isMinimized = !isMinimized" :title="isMinimized ? 'Perluas sidebar' : 'Perkecil sidebar'" :aria-label="isMinimized ? 'Perluas sidebar' : 'Perkecil sidebar'" :aria-expanded="!isMinimized" aria-controls="app-sidebar"><i class="fas fa-angles-left" :class="{ 'rotate-180': isMinimized }" aria-hidden="true"></i><span x-show="!isMinimized">Perkecil Menu</span></button>
            <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" title="Profil Saya" aria-label="Profil Saya"><i class="fas fa-user" aria-hidden="true"></i><span x-show="!isMinimized || mobileNavOpen">Profil Saya</span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="sidebar-link text-red-600 hover:text-red-700 hover:bg-red-50" title="Keluar" aria-label="Keluar"><i class="fas fa-right-from-bracket" aria-hidden="true"></i><span x-show="!isMinimized || mobileNavOpen">Keluar</span></button></form>
        </div>
    </aside>
</div>

@endauth
