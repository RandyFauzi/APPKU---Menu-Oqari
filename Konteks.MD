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

## 🎯 Apa Saja Yang Sudah Selesai Dikerjakan?
1. Inisialisasi Project Laravel 11 (coffee-saas).
2. Setup Database, Migration, dan Seeder untuk 3 toko.
3. Pembuatan rute dasar Web (web.php).
4. Halaman Login SaaS: Membuat UI Login yang sangat profesional, split-screen dengan gambar kopi premium.
5. Dashboard Admin: Berhasil memigrasi layout statis dashboard.html sebelumnya ke dalam file Blade.
6. Customer Menu Dinamis: Berhasil membuat Blade template yang warnanya berubah dinamis menyesuaikan primary_color.
7. NPM Script Khusus: Menambahkan perintah npm run dev-all.
8. Live Orders Real-time (Laravel Reverb): Integrasi WebSockets secara penuh.
9. Cetak Struk (Thermal Printer): Fitur cetak struk khusus ukuran 58mm/80mm.
10. Bulk Upload Menu (CSV): Fitur impor menu massal yang langsung masuk ke database.
11. Xendit Payment Gateway (Scaffolding): Kode integrasi Xendit telah disiapkan di ShopController.
12. Branding & Halaman Toko (5 Pilar):
    - Database: Penambahan field is_open, slogan, font_family, instagram_link, whatsapp_number, maps_link.
    - Dashboard CMS: Integrasi penuh di Alpine.js pada tab Pengaturan.
    - UI Customer: Mendukung kustomisasi tipografi, Layout Dinamis (List & Grid), Toggle Buka/Tutup Toko (Kill Switch Overlay), Slogan, dan Tautan Sosial Media.

## 🚀 Langkah Selanjutnya (Next Steps untuk AI berikutnya)
1. Integrasi Payment Gateway: Mengaktifkan dan menyempurnakan XENDIT_ACTIVE=true.
2. Upload Banner Carousel: Menyempurnakan form upload gambar banner di tab Pengaturan.
3. Penyempurnaan Reporting: Membangun backend logika untuk Analytics agar datanya asli.
4. Deploy ke Cloud: Menyesuaikan Reverb dan Vite build untuk siap naik produksi ke server.
