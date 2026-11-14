<?php

return [
    'dashboard' => ['route' => 'admin.dashboard', 'view' => 'Admin.Dashboard.tabs.overview', 'label' => 'Dashboard', 'ability' => 'view-dashboard', 'schema' => 'summary'],
    'orders' => ['route' => 'admin.orders.index', 'uri' => 'orders', 'view' => 'Admin.Dashboard.tabs.orders', 'label' => 'Pesanan Aktif', 'ability' => 'view-orders', 'schema' => 'orders'],
    'menu' => ['route' => 'admin.menu.index', 'uri' => 'menu', 'view' => 'Admin.Dashboard.tabs.menu', 'label' => 'Kelola Menu', 'ability' => 'manage-menu', 'schema' => 'catalog,inventory'],
    'addons' => ['route' => 'admin.addons.index', 'uri' => 'addons', 'view' => 'Admin.Dashboard.tabs.addons', 'label' => 'Kelola Add-ons', 'ability' => 'manage-menu', 'schema' => 'catalog'],
    'inventory' => ['route' => 'admin.inventory.index', 'uri' => 'inventory', 'view' => 'Admin.Dashboard.tabs.inventory', 'label' => 'Bahan & Stok', 'ability' => 'manage-inventory', 'schema' => 'inventory'],
    'qr' => ['route' => 'admin.tables.index', 'uri' => 'tables', 'view' => 'Admin.Dashboard.tabs.qr', 'label' => 'Meja & QR', 'ability' => 'manage-settings', 'schema' => 'settings'],
    'shifts' => ['route' => 'admin.shifts.index', 'uri' => 'shifts', 'view' => 'Admin.Dashboard.tabs.shifts', 'label' => 'Jadwal Shift', 'ability' => 'manage-crew', 'schema' => 'shifts'],
    'report' => ['route' => 'admin.reports.index', 'uri' => 'reports', 'view' => 'Admin.reports.index', 'label' => 'Laporan Keuangan', 'ability' => 'view-reports', 'schema' => 'reports'],
    'logs' => ['route' => 'admin.activity-logs.index', 'uri' => 'activity-logs', 'view' => 'Admin.Dashboard.tabs.logs', 'label' => 'Log Aktivitas', 'ability' => 'view-reports', 'schema' => 'audit'],
    'settings' => ['route' => 'admin.settings.index', 'uri' => 'settings', 'view' => 'Admin.Dashboard.tabs.settings', 'label' => 'Toko Saya', 'ability' => 'manage-settings', 'schema' => 'settings'],
    'hours' => ['route' => 'admin.operating-hours.index', 'uri' => 'operating-hours', 'view' => 'Admin.Dashboard.tabs.hours', 'label' => 'Jam Operasional', 'ability' => 'manage-settings', 'schema' => 'settings'],
    'receipt' => ['route' => 'admin.receipt-settings.index', 'uri' => 'receipt-settings', 'view' => 'Admin.Dashboard.tabs.receipt', 'label' => 'Pengaturan Struk', 'ability' => 'manage-settings', 'schema' => 'settings'],
    'vouchers' => ['route' => 'admin.vouchers.index', 'uri' => 'vouchers', 'view' => 'Admin.Dashboard.tabs.vouchers', 'label' => 'Voucher Promo', 'ability' => 'manage-settings', 'schema' => 'settings'],
];
