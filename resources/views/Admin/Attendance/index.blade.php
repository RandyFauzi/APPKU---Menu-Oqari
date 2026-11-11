<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi — {{ $shop->name ?? 'OQARI' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sparta-attendance { font-family: 'DM Sans', sans-serif; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-attendance min-h-screen bg-white text-[#1e211f] antialiased" x-data="attendanceRoot()">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-white">
    <a href="#attendance-content" class="sr-only focus:not-sr-only">Lewati navigasi</a>

    {{-- TOP HEADER BAR (Sparta Style) --}}
    <header class="h-[52px] bg-white border-b border-[#e9ebf0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#1e211f] hover:bg-[#dfddd7] transition-colors md:hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
            <span class="text-[13px] font-semibold text-[#1e211f]">Absensi</span>
        </div>

        {{-- Topbar Datetime (Standardized Sparta Component) --}}
        <x-topbar-datetime />
    </header>

    <main id="attendance-content" class="flex-1 w-full p-4 md:p-[24px] space-y-4">

        {{-- PAGE TITLE & MODE SWITCHER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold text-[#df5826] uppercase tracking-[0.22px]">Tim &amp; Kehadiran</p>
                <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight mt-0.5">
                    Absensi
                </h1>
                <p class="text-[12px] text-[#7f837e] mt-0.5">
                    {{ auth()->user()->name }} · {{ $today->format('d/m/Y H:i') }} · {{ $timezone }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <nav aria-label="Tampilan absensi" class="bg-white border border-[#e6e5e0] p-1 rounded-[11px] inline-flex items-center gap-1 self-start sm:self-auto shadow-[0px_1px_2px_rgba(0,0,0,0.02)]">
                    <a href="{{ route('admin.attendance.index', ['month' => $filters['month']]) }}" @if(!$isReport) aria-current="page" @endif
                       class="px-3.5 py-1.5 rounded-[8px] text-[12px] transition {{ !$isReport ? 'bg-[#fff6f0] border border-[#df5826] text-[#df5826] font-semibold' : 'text-[#53565a] font-medium hover:text-[#111827]' }}">
                        Absensi Saya
                    </a>
                    @if(auth()->user()->role === 'owner' && auth()->user()->can('view-attendance'))
                    <a href="{{ route('admin.attendance.index', ['tab' => 'team', 'month' => $filters['month']]) }}" @if($isReport) aria-current="page" @endif
                       class="px-3.5 py-1.5 rounded-[8px] text-[12px] transition {{ $isReport ? 'bg-[#fff6f0] border border-[#df5826] text-[#df5826] font-semibold' : 'text-[#53565a] font-medium hover:text-[#111827]' }}">
                        Rekap Tim
                    </a>
                    @endif
                </nav>

                @if(auth()->user()->role === 'owner')
                <button type="button" @click="showLocationModal = true" 
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-[10px] bg-white border border-[#e6e5e0] hover:bg-gray-50 text-[12px] font-semibold text-[#111827] shadow-[0px_1px_2px_rgba(0,0,0,0.02)] transition">
                    <i class="fas fa-location-dot text-[#df5826]"></i>
                    <span>Atur Lokasi Kafe</span>
                    @if($shop && $shop->hasLocation())
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500" title="Titik lokasi GPS kafe aktif"></span>
                    @else
                        <span class="inline-block w-2 h-2 rounded-full bg-amber-400" title="Titik lokasi belum disetel"></span>
                    @endif
                </button>
                @endif
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('status'))
            <div role="status" class="bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] px-4 py-3 rounded-[12px] text-[12.5px] font-semibold flex items-center gap-2.5">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] text-[12.5px] font-medium">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- CATAT KEHADIRAN CARD (Personal Check-In Only with Live Selfie & GPS) --}}
        @unless($isReport)
            <section aria-labelledby="record-title" class="bg-white border border-[#e6e5e0] rounded-[16px] p-5 md:p-6 pl-6 relative overflow-hidden shadow-[0px_3px_12px_rgba(38,39,35,0.04)]"
                     x-data="attendanceCapture({
                         shopLat: {{ $shop && $shop->latitude !== null ? $shop->latitude : 'null' }},
                         shopLng: {{ $shop && $shop->longitude !== null ? $shop->longitude : 'null' }},
                         shopRadius: {{ $shop->attendance_radius_meters ?? 100 }},
                         requirePhoto: {{ ($shop && $shop->attendance_require_photo) ? 'true' : 'false' }},
                         requireGps: {{ ($shop && $shop->attendance_require_gps) ? 'true' : 'false' }},
                         strictLocation: {{ ($shop && $shop->attendance_strict_location) ? 'true' : 'false' }}
                     })"
                     x-init="init()">
                {{-- Left Accent Bar --}}
                <div class="w-[3.5px] absolute left-0 top-0 bottom-0 {{ $todayRecord ? 'bg-[#289e5c]' : 'bg-[#df5826]' }}"></div>

                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 pb-4 border-b border-[#e6e5e0]">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[10px] {{ $todayRecord ? 'bg-[#eaf7ef] border-[#bfe5cd] text-[#289e5c]' : 'bg-[#fff6f0] border-[#f5d5c6] text-[#df5826]' }} border flex items-center justify-center shrink-0">
                            <i class="fas {{ $todayRecord ? 'fa-user-check' : 'fa-camera' }} text-base"></i>
                        </div>
                        <div>
                            <h2 id="record-title" class="text-[15px] font-bold text-[#1e211f] leading-tight">
                                {{ $todayRecord ? 'Kehadiran Hari Ini Sudah Tercatat' : 'Catat Kehadiran (Absen Masuk)' }}
                            </h2>
                            @if($todayRecord)
                                <p class="mt-1 text-[12.5px] text-[#289e5c] font-medium">
                                    Anda telah absen masuk pada hari ini pukul <strong class="font-semibold text-[#111827]">{{ $todayRecord->checked_in_at->setTimezone($todayRecord->timezone)->format('H:i') }} {{ $todayRecord->timezone }}</strong>. Absensi cukup 1x masuk, tidak perlu absen pulang.
                                </p>
                            @else
                                <p class="mt-1 text-[12.5px] text-[#53565a]">
                                    Ambil foto selfie langsung via kamera web/aplikasi dan pastikan GPS aktif untuk memverifikasi kehadiran.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="shrink-0">
                        @if($todayRecord)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] text-[11px] font-semibold">
                                <i class="fas fa-check text-[10px]"></i>
                                <span>Hadir Hari Ini</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] animate-pulse"></span>
                                <span>Belum Absen Masuk</span>
                            </span>
                        @endif
                    </div>
                </div>

                @if($todayRecord)
                    {{-- Detail Kehadiran Hari Ini (Sudah Absen Masuk) --}}
                    <div class="mt-5 p-4 sm:p-5 bg-[#f8f9fa] border border-[#e6e5e0] rounded-[14px]">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                            {{-- Foto Selfie yang Sudah Diambil --}}
                            <div class="md:col-span-4 flex flex-col items-center sm:items-start">
                                <span class="text-[11px] font-semibold text-[#7f837e] uppercase tracking-wider mb-2">Foto Bukti Kehadiran</span>
                                @if($todayRecord->check_in_photo_path)
                                    <div class="relative w-40 h-40 rounded-xl overflow-hidden border border-[#d8dce2] shadow-xs group bg-gray-100">
                                        <img src="{{ Storage::url($todayRecord->check_in_photo_path) }}" alt="Foto Selfie Masuk" class="w-full h-full object-cover">
                                        <button type="button" @click="openPhotoPreview('{{ Storage::url($todayRecord->check_in_photo_path) }}', '{{ auth()->user()->name }} (Masuk)', '{{ $todayRecord->checked_in_at->setTimezone($todayRecord->timezone)->format('d/m/Y H:i') }}', '{{ $todayRecord->check_in_latitude }}', '{{ $todayRecord->check_in_longitude }}', {{ $todayRecord->check_in_distance_meters ?? 'null' }}, {{ $todayRecord->check_in_in_radius !== null ? ($todayRecord->check_in_in_radius ? 'true' : 'false') : 'null' }})" 
                                                class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-semibold gap-1.5 cursor-pointer">
                                            <i class="fas fa-search-plus"></i> Lihat Foto
                                        </button>
                                    </div>
                                @else
                                    <div class="w-40 h-40 rounded-xl border border-dashed border-[#d8dce2] bg-white flex flex-col items-center justify-center text-gray-400 gap-1.5">
                                        <i class="fas fa-camera text-2xl"></i>
                                        <span class="text-[11px]">Tanpa Foto</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Informasi Detail Absensi --}}
                            <div class="md:col-span-8 space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="bg-white p-3 rounded-xl border border-[#e6e5e0]">
                                        <span class="text-[11px] text-[#7f837e] block">Waktu Masuk</span>
                                        <p class="text-[15px] font-bold text-[#111827] mt-0.5">
                                            {{ $todayRecord->checked_in_at->setTimezone($todayRecord->timezone)->format('H:i') }} <span class="text-xs font-semibold text-[#7f837e]">{{ $todayRecord->timezone }}</span>
                                        </p>
                                    </div>

                                    <div class="bg-white p-3 rounded-xl border border-[#e6e5e0]">
                                        <span class="text-[11px] text-[#7f837e] block">Status Kehadiran</span>
                                        <p class="text-[14px] font-bold mt-0.5 {{ $todayRecord->late_minutes > 0 ? 'text-red-600' : 'text-[#289e5c]' }}">
                                            @if($todayRecord->late_minutes > 0)
                                                <i class="fas fa-clock text-xs mr-1"></i> Terlambat {{ $todayRecord->late_minutes }} menit
                                            @elseif($todayRecord->scheduled_start_at)
                                                <i class="fas fa-check-circle text-xs mr-1"></i> Tepat Waktu
                                            @else
                                                <i class="fas fa-check-circle text-xs mr-1"></i> Hadir (Tanpa Jadwal)
                                            @endif
                                        </p>
                                    </div>

                                    <div class="bg-white p-3 rounded-xl border border-[#e6e5e0]">
                                        <span class="text-[11px] text-[#7f837e] block">Verifikasi Lokasi GPS</span>
                                        <p class="text-[13px] font-semibold text-[#111827] mt-0.5 flex items-center gap-1.5">
                                            @if($todayRecord->check_in_in_radius)
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                <span class="text-emerald-700 font-bold">Valid di Area Kafe</span>
                                            @elseif($todayRecord->check_in_latitude)
                                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                                <span class="text-amber-700">Di Luar Radius Kafe</span>
                                            @else
                                                <span class="text-[#7f837e]">Tanpa GPS</span>
                                            @endif
                                        </p>
                                        @if($todayRecord->check_in_latitude)
                                            <a href="https://www.google.com/maps?q={{ $todayRecord->check_in_latitude }},{{ $todayRecord->check_in_longitude }}" target="_blank" class="text-[11px] text-[#df5826] hover:underline font-semibold mt-1 inline-flex items-center gap-1">
                                                <i class="fas fa-map-location-dot"></i> Buka Titik Koordinat
                                            </a>
                                        @endif
                                    </div>

                                    <div class="bg-white p-3 rounded-xl border border-[#e6e5e0]">
                                        <span class="text-[11px] text-[#7f837e] block">Catatan Masuk</span>
                                        <p class="text-[12px] font-medium text-[#111827] mt-0.5 italic">
                                            {{ $todayRecord->check_in_note ? '"' . $todayRecord->check_in_note . '"' : '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="p-3 bg-[#eaf7ef] border border-[#bfe5cd] rounded-xl flex items-center gap-2 text-[#289e5c] text-[12px] font-semibold">
                                    <i class="fas fa-circle-check text-base"></i>
                                    <span>Absensi hari ini telah terekam dan selesai. Sistem menggunakan absensi masuk 1x, tidak perlu absen pulang.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Form Absen Masuk (Hanya Muncul Jika Belum Absen Masuk Hari Ini) --}}
                    <form method="POST" action="{{ route('admin.attendance.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4" @submit="handleSubmit($event)">
                        @csrf
                        <input type="hidden" name="type" value="check_in">
                        <input type="hidden" name="latitude" :value="latitude || ''">
                        <input type="hidden" name="longitude" :value="longitude || ''">
                        <input type="hidden" name="accuracy" :value="accuracy || ''">
                        <input type="hidden" name="photo_base64" :value="photoBase64 || ''">

                        <!-- Two Column Verification Layout: Left = Selfie Camera, Right = GPS & Notes -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                            
                            <!-- COL 1: Live Selfie Camera (5 cols on lg) -->
                            <div class="lg:col-span-5 flex flex-col">
                                <label class="block text-[12px] font-bold text-[#111827] mb-1.5">
                                    <i class="fas fa-camera text-[#df5826] mr-1"></i>
                                    Foto Selfie Kehadiran (Wajib Foto Langsung)
                                    <template x-if="requirePhoto"><span class="text-red-500 font-bold">*</span></template>
                                    <template x-if="!requirePhoto"><span class="text-[11px] text-[#7f837e] font-normal">(wajib via kamera)</span></template>
                                </label>

                                <div class="relative w-full rounded-2xl overflow-hidden bg-gray-900 border border-[#e6e5e0] min-h-[240px] flex items-center justify-center flex-1">
                                    
                                    <!-- STATE 1: Live Video Stream -->
                                    <div x-show="cameraStatus === 'streaming'" class="w-full h-full relative flex items-center justify-center">
                                        <video x-ref="video" autoplay playsinline muted class="w-full h-[240px] sm:h-[260px] object-cover scale-x-[-1]"></video>
                                        
                                        <!-- Oval Face Framing Guide -->
                                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                                            <div class="w-36 h-48 border-2 border-dashed border-white/60 rounded-[50%] shadow-[0_0_20px_rgba(0,0,0,0.4)]"></div>
                                        </div>

                                        <!-- Top Info Bar -->
                                        <div class="absolute top-2 left-2 right-2 flex items-center justify-between pointer-events-none">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-black/50 backdrop-blur-xs px-2 py-0.5 text-[10px] font-semibold text-white">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Kamera Aktif
                                            </span>
                                        </div>

                                        <!-- Shutter Button Overlay -->
                                        <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-3">
                                            <button type="button" @click="takeSnapshot()" 
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white text-[#111827] text-xs font-bold shadow-lg hover:bg-gray-100 active:scale-95 transition cursor-pointer">
                                                <i class="fas fa-circle-dot text-[#df5826] text-sm"></i>
                                                <span>Ambil Foto Selfie</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- STATE 2: Photo Captured -->
                                    <div x-show="cameraStatus === 'captured'" class="w-full h-full relative flex flex-col items-center justify-center">
                                        <img :src="photoBase64" alt="Hasil Foto Selfie" class="w-full h-[240px] sm:h-[260px] object-cover">
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-600/90 backdrop-blur-xs px-2 py-0.5 text-[11px] font-bold text-white shadow">
                                                <i class="fas fa-check"></i>Foto Siap (Terkonfirmasi)
                                            </span>
                                        </div>
                                        <div class="absolute bottom-3 inset-x-0 flex items-center justify-center">
                                            <button type="button" @click="retake()" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-black/70 backdrop-blur-xs text-white text-xs font-semibold hover:bg-black/90 transition shadow cursor-pointer">
                                                <i class="fas fa-rotate-right text-xs"></i>
                                                <span>Foto Ulang</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- STATE 3: Loading Camera -->
                                    <div x-show="cameraStatus === 'loading'" class="p-6 text-center text-white/80">
                                        <i class="fas fa-circle-notch fa-spin text-2xl text-[#df5826] mb-2"></i>
                                        <p class="text-xs">Menyiapkan kamera perangkat...</p>
                                    </div>

                                    <!-- STATE 4: Camera Inactive / Permission Denied -->
                                    <div x-show="cameraStatus === 'error'" class="p-5 text-center text-white space-y-3">
                                        <div class="w-10 h-10 rounded-full bg-amber-500/20 text-amber-400 mx-auto flex items-center justify-center">
                                            <i class="fas fa-camera text-base"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-white leading-snug" x-text="cameraErrorMsg || 'Kamera belum aktif'"></p>
                                            <p class="text-[11px] text-amber-200/90 mt-1">Wajib berfoto selfie langsung melalui kamera web/aplikasi (tidak dapat memilih foto dari galeri/file).</p>
                                            <p class="text-[10.5px] text-gray-300 mt-1">Pastikan browser diberi izin mengakses kamera, lalu klik tombol di bawah.</p>
                                        </div>
                                        <div class="flex items-center justify-center gap-2 pt-1">
                                            <button type="button" @click="startCamera()" 
                                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-gradient-to-b from-[#f06428] to-[#df5826] text-white text-xs font-bold hover:opacity-95 shadow transition cursor-pointer">
                                                <i class="fas fa-video"></i>
                                                <span>Aktifkan Kamera Sekarang</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-[11px] text-[#7f837e] mt-1.5">
                                    💡 Pengambilan foto selfie harus dilakukan langsung via kamera web/aplikasi secara real-time.
                                </p>
                            </div>

                            <!-- COL 2: GPS Verification & Notes (7 cols on lg) -->
                            <div class="lg:col-span-7 flex flex-col justify-between space-y-4">
                                
                                <!-- GPS Geolocation Status Box -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-[12px] font-bold text-[#111827]">
                                            <i class="fas fa-location-dot text-[#df5826] mr-1"></i>
                                            Lokasi GPS Kehadiran
                                            <template x-if="requireGps"><span class="text-red-500 font-bold">*</span></template>
                                            <template x-if="!requireGps"><span class="text-[11px] text-[#7f837e] font-normal">(opsional)</span></template>
                                        </label>
                                        <button type="button" @click="detectLocation()" :disabled="gpsStatus === 'loading'" 
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#df5826] hover:underline disabled:opacity-50 cursor-pointer">
                                            <i class="fas fa-rotate-right" :class="gpsStatus === 'loading' ? 'fa-spin' : ''"></i>
                                            <span>Deteksi Ulang</span>
                                        </button>
                                    </div>

                                    <!-- GPS Card Info -->
                                    <div class="rounded-xl border p-3.5 transition-all"
                                         :class="gpsStatus === 'success' ? (inRadius ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50') : (gpsStatus === 'error' ? 'border-red-200 bg-red-50/60' : 'border-[#e6e5e0] bg-[#f8f9fa]')">
                                        
                                        <!-- Loading GPS -->
                                        <div x-show="gpsStatus === 'loading'" class="flex items-center gap-3 text-xs text-[#53565a]">
                                            <i class="fas fa-circle-notch fa-spin text-base text-[#df5826]"></i>
                                            <div>
                                                <p class="font-semibold text-[#111827]">Mendeteksi posisi koordinat GPS...</p>
                                                <p class="text-[11px] text-[#7f837e]">Pastikan browser diizinkan mengakses lokasi perangkat.</p>
                                            </div>
                                        </div>

                                        <!-- Success GPS -->
                                        <div x-show="gpsStatus === 'success'" class="space-y-2">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                                         :class="inRadius ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                                        <i class="fas" :class="inRadius ? 'fa-check text-xs' : 'fa-exclamation-triangle text-xs'"></i>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-bold" :class="inRadius ? 'text-emerald-900' : 'text-amber-900'"
                                                           x-text="inRadius ? 'Lokasi Valid — Berada di Area Kafe' : 'Peringatan: Berada di Luar Radius Kafe'">
                                                        </p>
                                                        <p class="text-[11px] text-[#53565a]">
                                                            Koordinat: <span class="font-mono" x-text="latitude + ', ' + longitude"></span>
                                                            <span class="text-[#7f837e]" x-text="'(Akurasi ±' + accuracy + 'm)'"></span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Google Maps Link -->
                                                <a :href="'https://www.google.com/maps?q=' + latitude + ',' + longitude" target="_blank"
                                                   class="inline-flex items-center gap-1 rounded-md bg-white border border-gray-200 px-2 py-1 text-[11px] font-semibold text-[#53565a] hover:text-[#111827] shadow-2xs shrink-0"
                                                   title="Buka titik koordinat di Google Maps">
                                                    <i class="fas fa-map-location-dot text-[#df5826]"></i>
                                                    <span>Buka Peta</span>
                                                </a>
                                            </div>

                                            <!-- Distance Comparison Badge (if shop has coordinates) -->
                                            <template x-if="distanceMeters !== null">
                                                <div class="rounded-lg p-2 text-xs flex items-center justify-between"
                                                     :class="inRadius ? 'bg-emerald-100/70 text-emerald-800' : 'bg-amber-100/70 text-amber-800'">
                                                    <span>Jarak ke Kafe: <strong class="font-bold" x-text="distanceMeters + ' meter'"></strong> (Maks radius: <span x-text="shopRadius + 'm'"></span>)</span>
                                                    <span class="font-bold" x-text="inRadius ? '✓ Dalam Radius' : '⚠️ Luar Radius'"></span>
                                                </div>
                                            </template>

                                            <template x-if="strictLocation && !inRadius">
                                                <p class="text-[11px] text-red-600 font-bold">
                                                    ⛔ Validasi ketat aktif: Absensi diblokir karena Anda berada di luar radius kafe.
                                                </p>
                                            </template>
                                        </div>

                                        <!-- Error GPS / Permission Denied -->
                                        <div x-show="gpsStatus === 'error'" class="space-y-2">
                                            <div class="flex items-start gap-2.5 text-red-700">
                                                <i class="fas fa-triangle-exclamation text-base shrink-0 mt-0.5"></i>
                                                <div>
                                                    <p class="text-xs font-bold leading-tight">Gagal Mengakses Lokasi GPS</p>
                                                    <p class="text-[11px] mt-0.5" x-text="gpsErrorMsg"></p>
                                                </div>
                                            </div>
                                            <div class="text-[11px] text-[#53565a] bg-white/80 p-2 rounded-lg border border-red-100">
                                                💡 <strong>Cara Mengizinkan:</strong> Klik ikon gembok/setelan di kiri address bar browser Anda, pilih <em>Izinkan Lokasi (Location)</em>, lalu klik tombol "Deteksi Ulang".
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan Kehadiran (Opsional) -->
                                <div>
                                    <label for="attendance-note" class="block text-[12px] font-semibold text-[#111827] mb-1.5">
                                        Catatan masuk <span class="font-normal text-[#8b8e93]">(opsional)</span>
                                    </label>
                                    <textarea id="attendance-note" name="note" maxlength="500" rows="2"
                                        placeholder="Contoh: Hadir tepat waktu untuk shift pagi..."
                                        class="w-full rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] focus:ring-0 px-3.5 py-2.5 text-[12.5px] text-[#111827] placeholder-[#8b8e93] outline-none transition"
                                        aria-describedby="note-help">{{ old('note') }}</textarea>
                                </div>

                                <!-- Validation Summary Hint & Submit Action Button -->
                                <div class="pt-2 border-t border-[#e6e5e0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="text-[11.5px] text-[#7f837e]">
                                        <template x-if="!canSubmit">
                                            <span class="text-amber-600 font-semibold flex items-center gap-1.5">
                                                <i class="fas fa-circle-info"></i>
                                                <span x-text="submitBlockReason"></span>
                                            </span>
                                        </template>
                                        <template x-if="canSubmit">
                                            <span class="text-emerald-600 font-semibold flex items-center gap-1">
                                                <i class="fas fa-check-circle"></i> Data verifikasi foto &amp; lokasi lengkap.
                                            </span>
                                        </template>
                                    </div>

                                    <button type="submit" :disabled="!canSubmit"
                                        class="inline-flex items-center justify-center gap-2 py-2.5 px-6 rounded-[10px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-[13px] font-bold hover:opacity-95 transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                        <i class="fas fa-sign-in-alt text-xs"></i>
                                        <span x-text="submitting ? 'Mencatat…' : 'Absen Masuk Sekarang'">Absen Masuk</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif
            </section>
        @endunless

        {{-- 4 KPI SUMMARY CARDS (Sparta Dashboard Style) --}}
        <section aria-label="Ringkasan periode terpilih" class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Card 1: Tercatat --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Tercatat Bulan Ini</p>
                    <div class="w-7 h-7 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center text-[#df5826]">
                        <i class="fas fa-calendar-check text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ $summary['count'] }}</p>
            </div>

            {{-- Card 2: Terlambat --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Terlambat</p>
                    <div class="w-7 h-7 rounded-[8px] bg-red-50 border border-red-200 flex items-center justify-center text-red-600">
                        <i class="fas fa-clock text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ $summary['late'] }}</p>
            </div>

            {{-- Card 3: Tepat Waktu --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Tepat Waktu</p>
                    <div class="w-7 h-7 rounded-[8px] bg-[#eaf7ef] border border-[#bfe5cd] flex items-center justify-center text-[#289e5c]">
                        <i class="fas fa-check-circle text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ $summary['on_time'] }}</p>
            </div>

            {{-- Card 4: Hadir Hari Ini --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Hadir Hari Ini</p>
                    <div class="w-7 h-7 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center text-[#df5826]">
                        <i class="fas fa-users text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ $summary['today'] }}</p>
            </div>
        </section>

        {{-- RIWAYAT KEHADIRAN / KEHADIRAN TIM TABLE CARD --}}
        <section aria-labelledby="history-title" class="bg-white border border-[#e6e5e0] rounded-[16px] overflow-hidden shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
            <div class="p-4 md:p-5 border-b border-[#e6e5e0] flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <h2 id="history-title" class="text-[15px] font-bold text-[#1e211f] leading-tight">
                        {{ $isReport ? 'Kehadiran Tim' : 'Riwayat Kehadiran' }}
                    </h2>
                    <p class="text-[11.5px] text-[#8b8e93] mt-0.5">
                        Daftar catatan kehadiran periode {{ $filters['month'] }} beserta bukti verifikasi foto selfie &amp; lokasi GPS
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex flex-wrap items-end gap-2.5">
                    @if($isReport)<input type="hidden" name="tab" value="team">@endif
                    <div>
                        <label for="month" class="block text-[11px] font-medium text-[#53565a] mb-1">Bulan</label>
                        <input id="month" type="month" name="month" value="{{ $filters['month'] }}"
                            class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition" required>
                    </div>

                    @if($isReport)
                        <div>
                            <label for="employee" class="block text-[11px] font-medium text-[#53565a] mb-1">Karyawan</label>
                            <select id="employee" name="user_id"
                                class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition">
                                <option value="">Semua karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(($filters['user_id'] ?? '') == $employee->id)>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status" class="block text-[11px] font-medium text-[#53565a] mb-1">Status</label>
                            <select id="status" name="status"
                                class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition">
                                <option value="">Semua status</option>
                                @foreach(['on_time' => 'Tepat waktu', 'late' => 'Terlambat', 'unscheduled' => 'Tanpa jadwal'] as $key => $label)
                                    <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="submit"
                        class="py-2 px-4 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-[12px] font-semibold hover:opacity-95 transition min-h-[36px] cursor-pointer">
                        Tampilkan
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[12.5px] text-left border-collapse">
                    <caption class="sr-only">Daftar kehadiran periode {{ $filters['month'] }}</caption>
                    <thead class="bg-[#f8f9fa] border-b border-[#e6e5e0] text-[11px] font-semibold text-[#8b8e93] uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Hari Kerja</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Karyawan</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Waktu Masuk</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Verifikasi (Foto &amp; GPS)</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Status Kehadiran</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e6e5e0]">
                        @forelse($attendances as $attendance)
                            @php
                                $empName = $attendance->employee_name ?? $attendance->user?->name ?? 'Karyawan';
                            @endphp
                            <tr class="hover:bg-[#fcfcfd] transition-colors align-top">
                                <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-[#111827]">
                                    {{ $attendance->work_date->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center font-bold text-[10.5px] shrink-0">
                                            {{ strtoupper(substr($empName, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-[#111827] leading-tight">{{ $empName }}</p>
                                            @if(!$attendance->user)
                                                <p class="text-[11px] font-normal text-[#8b8e93] mt-0.5">Akun telah dihapus</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 font-semibold text-[#111827]">
                                        <span class="inline-block w-2 h-2 rounded-full bg-[#289e5c] shrink-0"></span>
                                        <span>{{ $attendance->checked_in_at->setTimezone($attendance->timezone)->format('H:i') }}</span>
                                        <span class="text-[11px] font-normal text-[#8b8e93]">{{ $attendance->timezone }}</span>
                                    </div>
                                </td>

                                {{-- Verification Column: Photos & GPS --}}
                                <td class="px-4 py-3.5">
                                    <div class="space-y-1.5 min-w-[170px]">
                                        <!-- Masuk Verification -->
                                        <div class="flex items-center gap-2">
                                            @if($attendance->check_in_photo_path)
                                                <button type="button" @click="openPhotoPreview('{{ $attendance->check_in_photo_url }}', '{{ $empName }} (Masuk)', '{{ $attendance->checked_in_at->setTimezone($attendance->timezone)->format('d/m/Y H:i') }}', '{{ $attendance->check_in_latitude }}', '{{ $attendance->check_in_longitude }}', {{ $attendance->check_in_distance_meters ?? 'null' }}, {{ $attendance->check_in_in_radius !== null ? ($attendance->check_in_in_radius ? 'true' : 'false') : 'null' }})"
                                                    class="relative w-8 h-8 rounded-lg overflow-hidden border border-emerald-300 shadow-2xs hover:scale-105 transition shrink-0 group cursor-pointer"
                                                    title="Lihat foto selfie masuk">
                                                    <img src="{{ $attendance->check_in_photo_url }}" alt="Foto Masuk" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent flex items-center justify-center text-white text-[9px]">
                                                        <i class="fas fa-search"></i>
                                                    </div>
                                                </button>
                                            @else
                                                <span class="w-8 h-8 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 text-xs shrink-0" title="Tanpa foto masuk">
                                                    <i class="fas fa-camera-slash text-[10px]"></i>
                                                </span>
                                            @endif

                                            <div class="text-[11px] leading-tight">
                                                <div class="flex items-center gap-1 font-semibold text-[#111827]">
                                                    <span class="text-[10px] text-emerald-600 font-bold">GPS:</span>
                                                    @if($attendance->check_in_latitude && $attendance->check_in_longitude)
                                                        <a href="https://www.google.com/maps?q={{ $attendance->check_in_latitude }},{{ $attendance->check_in_longitude }}" target="_blank"
                                                           class="inline-flex items-center gap-0.5 hover:underline"
                                                           :class="{{ $attendance->check_in_in_radius !== false ? "'text-emerald-700'" : "'text-amber-700'" }}"
                                                           title="Buka titik GPS di Google Maps">
                                                            <i class="fas fa-location-dot text-[9px]"></i>
                                                            @if($attendance->check_in_distance_meters !== null)
                                                                <span>{{ $attendance->check_in_distance_meters }}m</span>
                                                            @else
                                                                <span>Peta</span>
                                                            @endif
                                                        </a>
                                                        @if($attendance->check_in_in_radius === false)
                                                            <span class="rounded bg-amber-100 px-1 text-[9px] font-bold text-amber-700" title="Di luar radius kafe">Luar</span>
                                                        @else
                                                            <span class="rounded bg-emerald-100 px-1 text-[9px] font-bold text-emerald-700" title="Di dalam radius kafe">Valid</span>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-400 font-normal">Tanpa GPS</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex flex-col items-start gap-1">
                                        @if(!$attendance->scheduled_start_at)
                                            <span class="px-2 py-0.5 rounded-[5px] bg-[#f1f0ec] text-[#53565a] text-[11px] font-medium">Tanpa jadwal</span>
                                        @elseif($attendance->late_minutes > 0)
                                            <span class="px-2 py-0.5 rounded-[5px] bg-red-50 border border-red-200 text-red-600 text-[11px] font-semibold">Terlambat {{ $attendance->late_minutes }} menit</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-[5px] bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] text-[11px] font-semibold">Tepat waktu</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 min-w-[180px] max-w-sm break-words text-[#53565a]">
                                    <p>{{ $attendance->check_in_note ?? '—' }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-4 text-center">
                                    <div class="w-11 h-11 rounded-[12px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto mb-2.5">
                                        <i class="fas fa-calendar-times text-sm"></i>
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#1e211f]">Belum ada catatan kehadiran pada periode ini.</p>
                                    <p class="text-[11.5px] text-[#8b8e93] mt-0.5">Data absensi yang tercatat pada bulan terpilih akan tampil di tabel ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($attendances->hasPages())
                <div class="p-4 border-t border-[#e6e5e0] bg-[#fcfcfd]">
                    {{ $attendances->links() }}
                </div>
            @endif
        </section>

        <p class="text-[11px] text-[#8b8e93] pb-4">
            Catatan tanpa jadwal tidak dihitung sebagai keterlambatan. Rekap kehadiran ini mencatat kehadiran harian staf dan bukan perhitungan langsung gaji atau lembur.
        </p>
    </main>

    {{-- MODAL 1: FULL RESOLUTION PHOTO & GPS PREVIEW --}}
    <div x-show="previewPhotoModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="previewPhotoModal = false">
        <div class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl space-y-4" @click.away="previewPhotoModal = false">
            <div class="flex items-center justify-between border-b pb-3 border-[#e6e5e0]">
                <div>
                    <h3 class="text-sm font-bold text-[#111827]" x-text="activePhoto.title"></h3>
                    <p class="text-[11px] text-[#7f837e]" x-text="activePhoto.timestamp"></p>
                </div>
                <button type="button" @click="previewPhotoModal = false" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-[#53565a] flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="overflow-hidden rounded-xl bg-gray-900 border border-gray-200 max-h-[380px] flex items-center justify-center">
                <img :src="activePhoto.url" alt="Foto Bukti Kehadiran" class="w-full h-auto max-h-[380px] object-contain">
            </div>

            <!-- GPS Detail in Modal -->
            <div class="rounded-xl bg-gray-50 p-3 border border-[#e6e5e0] text-xs space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[#7f837e]">Status Lokasi:</span>
                    <span class="font-bold" :class="activePhoto.inRadius ? 'text-emerald-700' : 'text-amber-700'" 
                          x-text="activePhoto.inRadius ? '✓ Dalam Radius Kafe' : (activePhoto.inRadius === false ? '⚠️ Di Luar Radius Kafe' : 'Tanpa Lokasi')"></span>
                </div>
                <template x-if="activePhoto.distance !== null">
                    <div class="flex items-center justify-between">
                        <span class="text-[#7f837e]">Jarak ke Kafe:</span>
                        <span class="font-bold text-[#111827]" x-text="activePhoto.distance + ' meter'"></span>
                    </div>
                </template>
                <template x-if="activePhoto.lat && activePhoto.lng">
                    <div class="flex items-center justify-between pt-1 border-t border-gray-200">
                        <span class="text-[11px] font-mono text-[#53565a]" x-text="activePhoto.lat + ', ' + activePhoto.lng"></span>
                        <a :href="'https://www.google.com/maps?q=' + activePhoto.lat + ',' + activePhoto.lng" target="_blank"
                           class="inline-flex items-center gap-1 text-[11px] font-bold text-[#df5826] hover:underline">
                            <i class="fas fa-location-arrow"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- MODAL 2: OWNER CAFE LOCATION SETTINGS --}}
    @if(auth()->user()->role === 'owner')
    <div x-show="showLocationModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         @keydown.escape.window="showLocationModal = false">
        <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl space-y-5" @click.away="showLocationModal = false">
            <div class="flex items-center justify-between border-b pb-3 border-[#e6e5e0]">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center">
                        <i class="fas fa-map-location-dot text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#111827]">Atur Titik Lokasi &amp; Aturan Absensi Kafe</h3>
                        <p class="text-[11px] text-[#7f837e]">Tentukan titik koordinat GPS kafe dan radius validasi absensi kru.</p>
                    </div>
                </div>
                <button type="button" @click="showLocationModal = false" class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-[#53565a] flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.attendance.location') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- 1-Click Get Current Location Button -->
                <div class="rounded-xl bg-[#fff6f0] border border-[#f5d5c6] p-3.5 flex items-center justify-between gap-3">
                    <div class="text-xs">
                        <p class="font-bold text-[#111827]">Sedang berada di lokasi kafe?</p>
                        <p class="text-[11px] text-[#7f837e]">Klik tombol untuk mengisi koordinat secara otomatis.</p>
                    </div>
                    <button type="button" @click="getOwnerCurrentLocation()" :disabled="locatingOwner"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#df5826] hover:bg-[#c94d00] text-white text-xs font-bold shadow-xs transition disabled:opacity-50">
                        <i class="fas" :class="locatingOwner ? 'fa-spinner fa-spin' : 'fa-crosshairs'"></i>
                        <span x-text="locatingOwner ? 'Mendeteksi…' : 'Gunakan Posisi Saya'">Gunakan Posisi Saya</span>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="shop-lat" class="block text-xs font-semibold text-[#111827] mb-1">Latitude</label>
                        <input id="shop-lat" type="number" step="any" name="latitude" x-model="modalLat" placeholder="-6.2088..."
                            class="w-full rounded-[9px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] px-3 py-2 text-xs font-mono">
                    </div>
                    <div>
                        <label for="shop-lng" class="block text-xs font-semibold text-[#111827] mb-1">Longitude</label>
                        <input id="shop-lng" type="number" step="any" name="longitude" x-model="modalLng" placeholder="106.8455..."
                            class="w-full rounded-[9px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] px-3 py-2 text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label for="shop-radius" class="block text-xs font-semibold text-[#111827] mb-1">
                        Radius Maksimal Absensi (Meter)
                    </label>
                    <input id="shop-radius" type="number" name="attendance_radius_meters" x-model="modalRadius" min="10" max="5000"
                        class="w-full rounded-[9px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] px-3 py-2 text-xs font-semibold">
                    <p class="text-[11px] text-[#7f837e] mt-1">Jarak toleransi maksimal staf dari titik kafe saat melakukan absensi (rekomendasi: 50 – 150 meter).</p>
                </div>

                <!-- Toggles -->
                <div class="space-y-2.5 pt-2 border-t border-[#e6e5e0]">
                    <!-- Require Photo -->
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-[#111827] block">Wajibkan Foto Selfie</span>
                            <span class="text-[11px] text-[#7f837e]">Kru wajib mengambil foto selfie langsung via kamera saat absen masuk.</span>
                        </div>
                        <input type="checkbox" name="attendance_require_photo" value="1" x-model="modalReqPhoto"
                            class="w-4 h-4 text-[#df5826] rounded border-gray-300 focus:ring-[#df5826]">
                    </label>

                    <!-- Require GPS -->
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-[#111827] block">Wajibkan Lokasi GPS</span>
                            <span class="text-[11px] text-[#7f837e]">Kru wajib mengaktifkan GPS lokasi saat melakukan absensi.</span>
                        </div>
                        <input type="checkbox" name="attendance_require_gps" value="1" x-model="modalReqGps"
                            class="w-4 h-4 text-[#df5826] rounded border-gray-300 focus:ring-[#df5826]">
                    </label>

                    <!-- Strict Location Enforcement -->
                    <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer">
                        <div>
                            <span class="text-xs font-bold text-[#111827] block">Validasi Ketat Radius Kafe</span>
                            <span class="text-[11px] text-[#7f837e]">Tolak absensi jika kru berada di luar radius. Jika dinonaktifkan, absensi tetap diterima namun ditandai peringatan "Luar Radius".</span>
                        </div>
                        <input type="checkbox" name="attendance_strict_location" value="1" x-model="modalStrictLoc"
                            class="w-4 h-4 text-[#df5826] rounded border-gray-300 focus:ring-[#df5826]">
                    </label>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#e6e5e0]">
                    <button type="button" @click="showLocationModal = false" class="px-4 py-2 rounded-[9px] border border-gray-200 text-xs font-semibold text-[#53565a] hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] text-white text-xs font-bold hover:opacity-95 shadow-sm">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div></div>

<script>
function attendanceRoot() {
    return {
        showLocationModal: false,
        locatingOwner: false,
        modalLat: '{{ $shop->latitude ?? '' }}',
        modalLng: '{{ $shop->longitude ?? '' }}',
        modalRadius: {{ $shop->attendance_radius_meters ?? 100 }},
        modalReqPhoto: {{ ($shop && $shop->attendance_require_photo) ? 'true' : 'false' }},
        modalReqGps: {{ ($shop && $shop->attendance_require_gps) ? 'true' : 'false' }},
        modalStrictLoc: {{ ($shop && $shop->attendance_strict_location) ? 'true' : 'false' }},

        previewPhotoModal: false,
        activePhoto: {
            url: '',
            title: '',
            timestamp: '',
            lat: null,
            lng: null,
            distance: null,
            inRadius: null,
        },

        openPhotoPreview(url, title, timestamp, lat, lng, distance, inRadius) {
            this.activePhoto = { url, title, timestamp, lat, lng, distance, inRadius };
            this.previewPhotoModal = true;
        },

        getOwnerCurrentLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung oleh browser ini.');
                return;
            }
            this.locatingOwner = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.locatingOwner = false;
                    this.modalLat = pos.coords.latitude.toFixed(7);
                    this.modalLng = pos.coords.longitude.toFixed(7);
                },
                (err) => {
                    this.locatingOwner = false;
                    alert('Gagal mendeteksi lokasi: ' + (err.message || 'Izin lokasi tidak diberikan.'));
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    };
}

function attendanceCapture(config) {
    return {
        shopLat: config.shopLat,
        shopLng: config.shopLng,
        shopRadius: config.shopRadius || 100,
        requirePhoto: config.requirePhoto,
        requireGps: config.requireGps,
        strictLocation: config.strictLocation,

        // GPS State
        gpsStatus: 'idle', // 'idle' | 'loading' | 'success' | 'error'
        gpsErrorMsg: '',
        latitude: null,
        longitude: null,
        accuracy: null,
        distanceMeters: null,
        inRadius: true,

        // Camera State
        cameraStatus: 'idle', // 'idle' | 'loading' | 'streaming' | 'captured' | 'error'
        cameraErrorMsg: '',
        photoBase64: '',
        videoStream: null,
        facingMode: 'user',

        submitting: false,

        init() {
            this.detectLocation();
            this.startCamera();
        },

        calcDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c);
        },

        detectLocation() {
            if (!navigator.geolocation) {
                this.gpsStatus = 'error';
                this.gpsErrorMsg = 'Browser Anda tidak mendukung deteksi lokasi (Geolocation).';
                return;
            }
            this.gpsStatus = 'loading';
            this.gpsErrorMsg = '';

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = Number(pos.coords.latitude.toFixed(7));
                    this.longitude = Number(pos.coords.longitude.toFixed(7));
                    this.accuracy = Math.round(pos.coords.accuracy);
                    this.gpsStatus = 'success';

                    if (this.shopLat !== null && this.shopLng !== null) {
                        this.distanceMeters = this.calcDistance(this.latitude, this.longitude, this.shopLat, this.shopLng);
                        this.inRadius = (this.distanceMeters <= this.shopRadius);
                    } else {
                        this.distanceMeters = null;
                        this.inRadius = true;
                    }
                },
                (err) => {
                    this.gpsStatus = 'error';
                    if (err.code === 1) {
                        this.gpsErrorMsg = 'Izin lokasi (GPS) ditolak oleh browser. Harap izinkan akses lokasi di setelan browser untuk mencatat absensi.';
                    } else if (err.code === 2) {
                        this.gpsErrorMsg = 'Posisi GPS tidak dapat diperoleh. Pastikan GPS perangkat aktif dan sinyal memadai.';
                    } else if (err.code === 3) {
                        this.gpsErrorMsg = 'Waktu permintaan lokasi habis (timeout). Silakan klik Deteksi Ulang.';
                    } else {
                        this.gpsErrorMsg = 'Gagal mendeteksi lokasi: ' + (err.message || 'Error tidak diketahui');
                    }
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        },

        async startCamera() {
            this.cameraStatus = 'loading';
            this.cameraErrorMsg = '';
            try {
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(track => track.stop());
                }
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode, width: { ideal: 640 }, height: { ideal: 480 } },
                    audio: false
                });
                this.videoStream = stream;
                this.cameraStatus = 'streaming';
                this.$nextTick(() => {
                    const videoEl = this.$refs.video;
                    if (videoEl) {
                        videoEl.srcObject = stream;
                        videoEl.play();
                    }
                });
            } catch (err) {
                this.cameraStatus = 'error';
                if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                    this.cameraErrorMsg = 'Izin kamera tidak diizinkan di browser.';
                } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                    this.cameraErrorMsg = 'Kamera tidak ditemukan pada perangkat Anda.';
                } else {
                    this.cameraErrorMsg = 'Kamera tidak dapat diakses (' + (err.message || 'Error') + ').';
                }
            }
        },

        takeSnapshot() {
            const video = this.$refs.video;
            if (!video) return;
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            const ctx = canvas.getContext('2d');
            
            // Mirror selfie horizontally
            if (this.facingMode === 'user') {
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
            }
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.photoBase64 = canvas.toDataURL('image/jpeg', 0.85);
            this.cameraStatus = 'captured';
            this.stopStream();
        },

        retake() {
            this.photoBase64 = '';
            this.startCamera();
        },

        stopStream() {
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(track => track.stop());
                this.videoStream = null;
            }
        },

        get canSubmit() {
            if (this.submitting) return false;
            if (this.requirePhoto && !this.photoBase64) return false;
            if (this.requireGps && (this.latitude === null || this.longitude === null)) return false;
            if (this.strictLocation && !this.inRadius) return false;
            return true;
        },

        get submitBlockReason() {
            if (this.requirePhoto && !this.photoBase64) {
                return 'Ambil foto selfie langsung via kamera web/aplikasi.';
            }
            if (this.requireGps && (this.latitude === null || this.longitude === null)) {
                return 'Aktifkan GPS & izinkan akses lokasi browser.';
            }
            if (this.strictLocation && !this.inRadius) {
                return 'Anda berada di luar radius kafe.';
            }
            return '';
        },

        handleSubmit(e) {
            if (!this.canSubmit) {
                e.preventDefault();
                return;
            }
            this.submitting = true;
        }
    };
}
</script>
</body>
</html>
