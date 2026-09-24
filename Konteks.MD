# Konteks Project: OQARI SaaS (Multi-Tenant Coffee Shop POS & Smart QR Menu)

## 📌 Ringkasan Project
Aplikasi ini adalah **SaaS (Software as a Service)** untuk manajemen kedai kopi dan bisnis F&B modern dengan merek **OQARI**. Terdiri dari beberapa pilar utama:
1. **Landing Page SaaS (Tampilan Utama Sebelum Login)**: Halaman konversi utama bagi calon pengguna, mitra, dan publik untuk mempelajari ekosistem OQARI, fitur-fitur, demo live, komparasi harga, dan konsultasi demo.
2. **Admin Dashboard (SaaS Portal & Kasir POS)**: Tempat pemilik/kasir mengelola pesanan (Live Orders, Kanban, Shift Cashier), Menu CMS, KDS (Kitchen Display System), dan melihat Analytics.
3. **Customer Menu (Katalog Menu Digital & QR Table Ordering)**: Halaman tempat pelanggan melihat menu visual dan memesan via HP (Scan QR di meja) tanpa perlu install aplikasi apapun.

## 🛠️ Tech Stack
* **Framework Backend**: Laravel 11 (PHP 8.3)
* **Frontend/UI**: Tailwind CSS, Alpine.js, & Vite.
* **Database**: MySQL (menggunakan Eloquent ORM).
* **Package Manager**: npm (dilengkapi script `npm run dev-all` untuk run PHP & Vite bersamaan).
* **Realtime**: Laravel Echo & WebSockets / Reverb.
* **Environment**: Laragon (Windows).

## 🏢 Arsitektur Multi-Tenant
Sistem menggunakan pendekatan **Single Database, Row-Level Isolation**. 
Setiap kedai kopi memiliki "akun" (Tabel `shops`). Data seperti menu, pesanan, dan akun kasir dipisahkan menggunakan `shop_id`.
* URL Landing Page: `http://127.0.0.1:8000/` (Tampilan utama OQARI sebelum login)
* URL Login Admin: `http://127.0.0.1:8000/admin/login` -> `/admin/dashboard`
* URL Register Admin: `http://127.0.0.1:8000/admin/register`
* URL Customer: `http://127.0.0.1:8000/{shop_slug}` (Contoh: `/bitten`, `/senja`)

---

## 🎯 Analisis Komparatif Layout Landing Page (Benchmark: Majoo.id)

Berdasarkan analisis arsitektur landing page **Majoo.id** (market leader POS UMKM Indonesia), berikut adalah pemetaan adaptasi prinsip konversi dan psikologi antarmuka yang diterapkan ke dalam Landing Page OQARI:

| Elemen / Sesi | Anatomi di Majoo.id | Fungsi Psikologis & Konversi | Implementasi OQARI |
|---|---|---|---|
| **Top Announcement Bar** | Layanan Hotline WA + Badge Promo Diskon 50% | *Urgency* & *Instant Accessibility* bagi pengunjung baru. | Top bar "Diskon Komitmen 30% Paket Tahunan + Free Setup Onboarding Menu & Meja". |
| **Sticky Navigation Bar** | Mega Menu (Produk, Layanan, Solusi, Custom, Harga, Support) + Dual Button ("Masuk" & "Coba Gratis") | Navigasi menyeluruh tanpa kehilangan fokus ke CTA utama (Free Trial). | Header sticky dengan navigasi Fitur, Solusi, Demo QR, Perangkat, Harga, FAQ + Tombol "Masuk" dan "Coba Gratis 14 Hari". Jika user login, muncul "Buka Dashboard". |
| **Hero Section** | Headline tegas benefit operasional, kapsul fitur (tags), CTA ganda, dan Interactive Image Showcase dengan navigasi kategori bisnis (FnB, Retail, Jasa, Beauty). | Menyampaikan *core message* dalam <3 detik dan memperlihatkan preview antarmuka aplikasi. | Hero dengan headline fokus F&B Coffee: *"Kelola Coffee Shop & Cafe Lebih Cepat, Rapi, dan Serba Otomatis"*, badge kapsul produk, dan Interactive Preview Mobile & Tablet POS. |
| **Social Proof & Metrik** | 45.000+ wirausaha di 600 kota + Logo brand besar (Burger Bangor, Hotdog Booth, 5asec, dll). | Membangun kredibilitas instan (*authority & safety*). | Counter metrik: 500+ Cafe & Coffee Shop, 3.2 Juta+ Pesanan, 3x Kecepatan Putaran Meja, 99.98% Uptime Cloud + Logo partner cafe ternama. |
| **Feature Trio Grid** | 3 Kartu Layanan (Kelola Transaksi, Kelola Karyawan/Owner, Toko Online & CRM) dengan capsule tags. | Mengelompokkan puluhan fitur ke dalam 3 manfaat utama yang mudah dicerna otak manusia. | 3 Pilar Utama OQARI: (1) Smart QR E-Menu & Table Self-Order, (2) POS Kasir Kilat & KDS Barista Realtime, (3) Manajemen Resep Gramasi & HPP Otomatis. |
| **Solusi Per Kategori Bisnis** | Tab switcher vertikal (FnB, Retail, Hospitality, Enterprise). | Personalisasi kebutuhan: pengunjung langsung klik jenis bisnis mereka sendiri. | Interactive Tab: *Specialty Coffee & Roastery*, *Cafe & Casual Dining*, *Coffee Booth & Grab-and-Go*, *Multi-Outlet & Franchise*. |
| **Interactive Live QR Demo** | Showcase fitur terintegrasi. | Memberikan *aha moment* langsung pada pengunjung tanpa komitmen. | Phone Simulator Interaktif di browser yang memungkinkan pengunjung mencoba menambah Spanish Latte, Americano, Croissant, dan melihat total order realtime. |
| **Hardware Compatibility** | Showcase perangkat Android, iPad, Thermal Printer, EDC. | Menghilangkan keraguan teknis pebisnis ("Apakah bisa pakai tablet saya?"). | Visual kompatibilitas: Tablet Android/iOS, Mini POS Sunmi/iMin, Printer Bluetooth/LAN 58mm & 80mm, Cash Drawer. |
| **Pricing Table & ROI** | Paket bertingkat (Starter, Advance, Prime) dengan komparasi transparan. | Mengarahkan ke paket terlaris (*decoy effect*) dan transparan biaya tanpa hidden fees. | Paket transparan dengan toggle Bulanan / Tahunan: Starter Barista (Rp 79k/bln), Pro Coffee House (Rp 159k/bln) [POPULAR], Enterprise Multi-Outlet (Custom). |
| **FAQ & Testimoni** | Accordion FAQ interaktif + Video Testimoni pengguna nyata. | Menghapus keberatan (*handling objections*) teknis dan operasional di menit terakhir. | Accordion FAQ komprehensif seputar QRIS, cetak struk, offline mode, multi-cabang, dan impor menu. |
| **Bottom Mega CTA & Floating WA** | Banner penutup + Floating WhatsApp button di kanan bawah. | Jalur cepat *direct sales* untuk pebisnis yang ingin tanya langsung atau nego custom. | Banner CTA kontras gelap dengan aksen amber dan tombol WhatsApp floating dengan pulsing badge live. |

---

## 🚀 Fitur yang Telah Selesai Dikerjakan
1. **Landing Page SaaS OQARI (`/`)**: Tampilan utama sebelum masuk ke login dengan standar industri Majoo.id dan estetika kopi modern.
2. **Konteks & Desain System**: `Konteks.MD` dan `DESAIN.md` merinci value proposition, persona, arsitektur antarmuka, dan tokens.
3. **Inisialisasi Project Laravel 11**: Multi-tenant coffee-saas.
4. **Setup Database, Migration, dan Seeder**: Multi-shop isolation.
5. **Halaman Login SaaS (`/admin/login`)**: Split-screen auth UI dengan ilustrasi kopi dinamis.
6. **Dashboard Admin**: Modul Live Orders, Kanban, Menu CMS, QR Meja, Kitchen Display (KDS), Laporan, dan Pengaturan.
7. **Customer Menu Dinamis (`/{slug}`)**: Mobile-optimized ordering catalog dengan primary_color dinamis.
8. **Real-time Orders**: Integrasi WebSockets / Reverb untuk audio and visual alerts.
9. **Cetak Struk Thermal**: Dukungan ukuran 58mm/80mm via Web / Bluetooth / Signed URL.
10. **Bulk Upload Menu**: Fitur import menu dari CSV/Excel.
11. **Branding & Kustomisasi Toko**: Pengaturan tipografi, layout, kill-switch toko buka/tutup, dan link sosial media.
