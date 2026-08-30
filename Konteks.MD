# Konteks Project: Brewly SaaS (Multi-Tenant Coffee Shop POS & Menu)

## ?? Ringkasan Project
Aplikasi ini adalah **SaaS (Software as a Service)** untuk manajemen kedai kopi. Terdiri dari dua bagian utama:
1. **Admin Dashboard (SaaS Portal & Kasir)**: Tempat pemilik/kasir mengelola pesanan (Live Orders, Kanban), Menu CMS, dan melihat Analytics. Desain dashboard seragam untuk semua kedai.
2. **Customer Menu (Katalog Menu Digital)**: Halaman tempat pelanggan melihat menu dan memesan via HP (Scan QR). **UI/UX halaman ini akan dikustomisasi secara manual di kode (Blade)** untuk masing-masing *coffee shop* klien agar memiliki *branding* yang unik.

## ?? Tech Stack
* **Framework Backend**: Laravel 11
* **Frontend/UI**: Tailwind CSS & Alpine.js (via CDN untuk *rapid prototyping/blade view*) & Vite.
* **Database**: MySQL/SQLite (menggunakan Eloquent ORM).
* **Package Manager**: npm (dilengkapi script `npm run dev-all` untuk run PHP & Vite bersamaan).
* **Environment**: Laragon (Windows).

## ?? Arsitektur Multi-Tenant
Sistem menggunakan pendekatan **Single Database, Row-Level Isolation**. 
Setiap kedai kopi memiliki "akun" (Tabel `shops`). Data seperti menu, pesanan, dan akun kasir dipisahkan menggunakan `shop_id`.
* URL Admin: `http://127.0.0.1:8000/` (SaaS Login) -> `/admin/dashboard`
* URL Customer: `http://127.0.0.1:8000/{shop_slug}/menu` (Contoh: `/bitten/menu`)

## ??? Struktur Database (Telah di-Migrasi & Seeding)
Telah ada 3 data toko *dummy* (Bitten, Goodwill, Mada) di database:
1. `shops`: `id`, `name`, `slug`, `logo_url`, `primary_color`, `theme_style`.
2. `users` (Kasir/Admin): `id`, `shop_id`, `name`, `email`, `password`, `role`.
3. `products` (Menu): `id`, `shop_id`, `category_name`, `name`, `price`, `image_url`, `is_sold_out`.
4. `orders`: `id`, `shop_id`, `table_id`, `customer_name`, `status`, `total_amount`.
5. `order_items`: `id`, `order_id`, `product_id`, `qty`, `subtotal`.
6. `tables`: `id`, `shop_id`, `table_number`, `qr_code_string`.

## ?? Struktur Direktori (Custom Clean Architecture)
```text
app/Http/Controllers/
+-- Admin/DashboardController.php, LiveOrderController.php
+-- Customer/CustomerMenuController.php

resources/views/
+-- Admin/
¦   +-- Layouts/admin_master.blade.php
¦   +-- Dashboard/dashboard.blade.php
+-- Customer/
¦   +-- Layouts/customer_master.blade.php
¦   +-- Menu/catalog_menu.blade.php
+-- welcome_saas.blade.php (Halaman Login SaaS)
```

## ? Apa Saja Yang Sudah Selesai Dikerjakan?
1. Inisialisasi Project Laravel 11 (`coffee-saas`).
2. Setup *Database*, *Migration*, dan *Seeder* untuk 3 toko.
3. Pembuatan rute dasar Web (`web.php`).
4. **Halaman Login SaaS**: Membuat UI Login yang sangat profesional, split-screen dengan gambar kopi premium.
5. **Dashboard Admin**: Berhasil memigrasi layout statis `dashboard.html` sebelumnya (lengkap dengan Alpine.js tabs, Kanban Live Orders, & Analytics) ke dalam file Blade.
6. **Customer Menu Dinamis**: Berhasil membuat Blade template yang warnanya berubah dinamis (via inline style CSS variable) menyesuaikan `primary_color` milik kedai kopi di database.
7. **NPM Script Khusus**: Menambahkan perintah `npm run dev-all` (menggunakan paket `concurrently`) untuk menjalankan `php artisan serve` dan `npm run dev` dalam 1 terminal.

## ?? Langkah Selanjutnya (Next Steps untuk AI berikutnya)
AI selanjutnya dapat melanjutkan dari titik ini:
1. **Fungsionalitas Database Admin**: Menyambungkan form "Tambah Menu" di `dashboard.blade.php` agar menyimpan data nyata ke tabel `products`.
2. **Checkout Pelanggan**: Membuat fitur keranjang pesanan (Cart) di menu pelanggan berfungsi dan menyimpan transaksi ke tabel `orders`.
3. **Live Orders Real-time**: Mengimplementasikan *Laravel Reverb / Pusher* (WebSockets) agar saat pesanan masuk, layar Kanban Live Orders di kasir ter-*update* otomatis secara real-time tanpa refresh.
4. **Desain Tema Kustom**: Membuat *routing* yang membaca `theme_style` toko untuk merender file Blade yang berbeda (misal `bitten_menu.blade.php` vs `mada_menu.blade.php`) karena user ingin merancang UI/UX menu secara manual untuk setiap klien.
