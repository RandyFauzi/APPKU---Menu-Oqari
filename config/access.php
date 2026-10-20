<?php

return [
    'permissions' => [
        'access-pos' => ['group' => 'Kasir', 'label' => 'Buka kasir POS'],
        'manage-payments' => ['group' => 'Kasir', 'label' => 'Terima pembayaran'],
        'manage-register' => ['group' => 'Kasir', 'label' => 'Buka/tutup dan rekonsiliasi kas sendiri'],
        'view-orders' => ['group' => 'Pesanan', 'label' => 'Lihat pesanan'],
        'manage-orders' => ['group' => 'Pesanan', 'label' => 'Kelola status pesanan'],
        'view-kitchen' => ['group' => 'Dapur', 'label' => 'Lihat layar dapur'],
        'update-kitchen-status' => ['group' => 'Dapur', 'label' => 'Ubah status dapur'],
        'manage-menu' => ['group' => 'Produk', 'label' => 'Kelola menu dan kategori'],
        'manage-hpp' => ['group' => 'Produk', 'label' => 'Kalkulator HPP dan resep'],
        'manage-inventory' => ['group' => 'Produk', 'label' => 'Kelola stok bahan baku'],
        'manage-crew' => ['group' => 'Karyawan', 'label' => 'Kelola jadwal kru'],
        'record-attendance' => ['group' => 'Karyawan', 'label' => 'Absensi sendiri'],
        'view-attendance' => ['group' => 'Karyawan', 'label' => 'Lihat rekap absensi cafe'],
        'view-own-schedule' => ['group' => 'Karyawan', 'label' => 'Lihat jadwal sendiri'],
        'manage-users' => ['group' => 'Administrasi', 'label' => 'Kelola akun dan hak akses user'],
        'manage-settings' => ['group' => 'Administrasi', 'label' => 'Kelola pengaturan cafe dan meja'],
        'view-reports' => ['group' => 'Laporan', 'label' => 'Lihat laporan, audit, dan kas seluruh cafe'],
    ],
    'roles' => ['admin' => 'Admin Cafe', 'manager' => 'Manager', 'cashier' => 'Kasir', 'barista' => 'Barista', 'kitchen' => 'Dapur', 'crew' => 'Kru'],
    'defaults' => [
        'owner' => ['*'], 'admin' => ['*'],
        'manager' => ['access-pos', 'view-orders', 'manage-orders', 'manage-menu', 'manage-hpp', 'manage-crew', 'manage-settings', 'view-reports', 'manage-register', 'manage-payments', 'manage-inventory', 'record-attendance', 'view-attendance'],
        'cashier' => ['record-attendance', 'access-pos', 'view-orders', 'manage-orders', 'manage-register', 'manage-payments'],
        'barista' => ['record-attendance', 'view-kitchen', 'update-kitchen-status', 'view-orders'],
        'kitchen' => ['record-attendance', 'view-kitchen', 'update-kitchen-status', 'view-orders'],
        'crew' => ['record-attendance', 'view-own-schedule'],
    ],
    'dependencies' => [
        'access-pos' => ['view-orders'], 'manage-payments' => ['access-pos', 'manage-register'],
        'manage-orders' => ['view-orders'], 'update-kitchen-status' => ['view-kitchen'],
    ],
    'dashboard' => ['manage-menu', 'manage-inventory', 'manage-crew', 'manage-settings', 'view-reports'],
];
