<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi — {{ $shop->name ?? 'OQARI' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sparta-attendance { font-family: 'DM Sans', sans-serif; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-attendance min-h-screen bg-[#f6f5f2] text-[#1e211f] antialiased">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-[#f6f5f2]">
    <a href="#attendance-content" class="sr-only focus:not-sr-only">Lewati navigasi</a>

    {{-- TOP HEADER BAR (Sparta Style) --}}
    <header class="h-[52px] bg-[#f6f5f2] border-b border-[#e6e5e0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button type="button" x-data @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#1e211f] hover:bg-[#dfddd7] transition-colors md:hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
            <span class="text-[13px] font-semibold text-[#1e211f]">Absensi</span>
        </div>

        <div class="flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-1.5 bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] text-[11px] font-medium text-[#53565a]">
                <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3.5 h-3.5 opacity-75">
                <span>{{ $today->format('H:i') }} ({{ $timezone }})</span>
            </div>
            <div class="bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] flex items-center text-[11px] font-medium text-[#53565a]">
                {{ $today->translatedFormat('d M Y') }}
            </div>
        </div>
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

        {{-- CATAT KEHADIRAN CARD (Personal Check-In / Check-Out) --}}
        @unless($isReport)
            <section aria-labelledby="record-title" class="bg-white border border-[#e6e5e0] rounded-[16px] p-5 md:p-6 pl-6 relative overflow-hidden shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
                {{-- Left Accent Bar --}}
                <div class="w-[3.5px] absolute left-0 top-0 bottom-0 {{ $active ? 'bg-[#df5826]' : ($todayRecord ? 'bg-[#289e5c]' : 'bg-[#df5826]') }}"></div>

                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 pb-4 border-b border-[#e6e5e0]">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[10px] {{ $active ? 'bg-[#fff6f0] border-[#f5d5c6] text-[#df5826]' : ($todayRecord ? 'bg-[#eaf7ef] border-[#bfe5cd] text-[#289e5c]' : 'bg-[#fff6f0] border-[#f5d5c6] text-[#df5826]') }} border flex items-center justify-center shrink-0">
                            <i class="fas {{ $active ? 'fa-user-clock' : ($todayRecord ? 'fa-user-check' : 'fa-fingerprint') }} text-base"></i>
                        </div>
                        <div>
                            <h2 id="record-title" class="text-[15px] font-bold text-[#1e211f] leading-tight">Catat Kehadiran</h2>
                            @if($active)
                                <p class="mt-1 text-[12.5px] text-[#53565a]">
                                    Anda masuk pada <strong class="font-semibold text-[#111827]">{{ $active->checked_in_at->setTimezone($active->timezone)->format('d/m/Y H:i') }}</strong> ({{ $active->timezone }}).
                                </p>
                            @elseif($todayRecord)
                                <p class="mt-1 text-[12.5px] text-[#289e5c] font-medium">
                                    Absensi hari ini sudah selesai. Kehadiran berikutnya dapat dicatat pada hari kerja berikutnya.
                                </p>
                            @else
                                <p class="mt-1 text-[12.5px] text-[#53565a]">
                                    Waktu dicatat oleh server. Jadwal yang tersedia digunakan untuk menghitung keterlambatan.
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="shrink-0">
                        @if($active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#df5826] animate-pulse"></span>
                                <span>Sesi Aktif</span>
                            </span>
                        @elseif($todayRecord)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] text-[11px] font-semibold">
                                <i class="fas fa-check text-[10px]"></i>
                                <span>Selesai Hari Ini</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] bg-[#f8f9fa] border border-[#d8dce2] text-[#53565a] text-[11px] font-semibold">
                                <span>Belum Absen Masuk</span>
                            </span>
                        @endif
                    </div>
                </div>

                @if($active && $active->work_date->format('Y-m-d') !== $today->toDateString())
                    <div class="mt-4 bg-[#fff6f0] border border-[#f5d5c6] rounded-[10px] px-3.5 py-2.5 flex items-center gap-2.5 text-[#df5826] text-[12px] font-medium">
                        <i class="fas fa-exclamation-circle shrink-0"></i>
                        <span>Sesi dari hari sebelumnya masih terbuka. Absen pulang akan menutup sesi tersebut.</span>
                    </div>
                @endif

                @if($active || !$todayRecord)
                    <form method="POST" action="{{ route('admin.attendance.store') }}" class="mt-4 space-y-3" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="type" value="{{ $active ? 'check_out' : 'check_in' }}">

                        <div>
                            <label for="attendance-note" class="block text-[12px] font-semibold text-[#111827] mb-1.5">
                                Catatan {{ $active ? 'pulang' : 'masuk' }} <span class="font-normal text-[#8b8e93]">(opsional)</span>
                            </label>
                            <textarea id="attendance-note" name="note" maxlength="500" rows="2"
                                placeholder="Contoh: {{ $active ? 'Menyelesaikan penutupan shift sore' : 'Hadir tepat waktu' }}..."
                                class="w-full rounded-[10px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] focus:ring-0 px-3.5 py-2.5 text-[12.5px] text-[#111827] placeholder-[#8b8e93] outline-none transition"
                                aria-describedby="note-help">{{ old('note') }}</textarea>
                            <p id="note-help" class="text-[11px] text-[#8b8e93] mt-1">
                                Maksimal 500 karakter. Satu sesi kehadiran per hari kerja. Absensi tidak menutup shift kasir.
                            </p>
                        </div>

                        <div class="pt-1">
                            <button type="submit" :disabled="submitting"
                                class="inline-flex items-center justify-center gap-2 py-2.5 px-5 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-[12.5px] font-semibold hover:opacity-95 transition disabled:opacity-60 disabled:cursor-wait">
                                <i class="fas {{ $active ? 'fa-sign-out-alt' : 'fa-sign-in-alt' }} text-xs"></i>
                                <span x-text="submitting ? 'Mencatat…' : '{{ $active ? 'Absen Pulang' : 'Absen Masuk' }}'">{{ $active ? 'Absen Pulang' : 'Absen Masuk' }}</span>
                            </button>
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
                    <p class="text-[12px] font-medium text-[#53565a]">Tercatat</p>
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

            {{-- Card 3: Belum Pulang --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Belum Pulang</p>
                    <div class="w-7 h-7 rounded-[8px] bg-[#fff6f0] border border-[#f5d5c6] flex items-center justify-center text-[#df5826]">
                        <i class="fas fa-user-clock text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ $summary['active'] }}</p>
            </div>

            {{-- Card 4: Durasi Selesai --}}
            <div class="bg-white border border-[#e6e5e0] rounded-[14px] p-4 shadow-[0px_2px_8px_rgba(0,0,0,0.02)] flex flex-col justify-between min-h-[96px]">
                <div class="flex items-center justify-between">
                    <p class="text-[12px] font-medium text-[#53565a]">Durasi Selesai</p>
                    <div class="w-7 h-7 rounded-[8px] bg-[#eaf7ef] border border-[#bfe5cd] flex items-center justify-center text-[#289e5c]">
                        <i class="fas fa-hourglass-half text-xs"></i>
                    </div>
                </div>
                <p class="text-[24px] font-bold text-[#111827] leading-tight mt-2">{{ intdiv($summary['minutes'], 60).'j '.($summary['minutes'] % 60).'m' }}</p>
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
                        Daftar catatan kehadiran periode {{ $filters['month'] }}
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
                                @foreach(['late' => 'Terlambat', 'on_time' => 'Tepat waktu', 'unscheduled' => 'Tanpa jadwal', 'active' => 'Belum pulang', 'completed' => 'Selesai'] as $key => $label)
                                    <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="submit"
                        class="py-2 px-4 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-[12px] font-semibold hover:opacity-95 transition min-h-[36px]">
                        Tampilkan
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[12.5px] text-left border-collapse">
                    <caption class="sr-only">Daftar kehadiran periode {{ $filters['month'] }}</caption>
                    <thead class="bg-[#f8f9fa] border-b border-[#e6e5e0] text-[11px] font-semibold text-[#8b8e93] uppercase tracking-wider">
                        <tr>
                            @foreach(['Hari Kerja', 'Karyawan', 'Masuk / Pulang', 'Kehadiran', 'Durasi', 'Catatan'] as $heading)
                                <th scope="col" class="px-4 py-3 whitespace-nowrap">{{ $heading }}</th>
                            @endforeach
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
                                    <div class="space-y-0.5">
                                        <p class="text-[#111827] font-medium">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#289e5c] mr-1.5"></span>{{ $attendance->checked_in_at->setTimezone($attendance->timezone)->format('d/m H:i') }}
                                        </p>
                                        <p class="{{ $attendance->checked_out_at ? 'text-[#53565a]' : 'text-[#df5826] font-semibold' }}">
                                            <span class="inline-block w-1.5 h-1.5 rounded-full {{ $attendance->checked_out_at ? 'bg-[#8b8e93]' : 'bg-[#df5826]' }} mr-1.5"></span>{{ $attendance->checked_out_at?->setTimezone($attendance->timezone)->format('d/m H:i') ?? 'Belum pulang' }}
                                        </p>
                                        <span class="text-[10.5px] text-[#8b8e93] block pl-3">{{ $attendance->timezone }}</span>
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

                                        @if($attendance->early_leave_minutes > 0)
                                            <span class="px-2 py-0.5 rounded-[5px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] text-[11px] font-medium">Pulang awal {{ $attendance->early_leave_minutes }} menit</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($attendance->worked_minutes !== null)
                                        <span class="font-semibold text-[#111827]">{{ intdiv($attendance->worked_minutes, 60).'j '.($attendance->worked_minutes % 60).'m' }}</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-[5px] bg-[#fff6f0] text-[#df5826] text-[11px] font-semibold">Berlangsung</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 min-w-[180px] max-w-sm break-words text-[#53565a]">
                                    <p>{{ $attendance->check_in_note ?? '—' }}</p>
                                    @if($attendance->check_out_note)
                                        <p class="mt-1 text-[11.5px] text-[#8b8e93]">Pulang: {{ $attendance->check_out_note }}</p>
                                    @endif
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
            Durasi adalah selisih waktu masuk dan pulang, belum dikurangi istirahat. Catatan tanpa jadwal tidak dihitung sebagai keterlambatan. Rekap ini bukan perhitungan gaji atau lembur.
        </p>
    </main>
</div></div>
</body>
</html>
