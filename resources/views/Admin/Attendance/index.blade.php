<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isReport ? 'Rekap Absensi' : 'Absensi Saya' }} — OQARI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: var(--oq-ivory); color: var(--oq-ink); }
        .attendance-primary { background: var(--oq-primary); color: var(--oq-ivory); }
        .attendance-accent { color: var(--oq-gold-ink); }
        .attendance-button { padding: .85rem 1.3rem; border-radius: .75rem; font-weight: 700; min-height: 44px; }
        .attendance-button:focus-visible, a:focus-visible { outline: 3px solid var(--oq-gold); outline-offset: 3px; }
        .attendance-button:disabled { opacity: .6; cursor: wait; }
        [x-cloak] { display: none !important; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="min-h-screen font-sans">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>
    <a href="#attendance-content" class="sr-only focus:not-sr-only">Lewati navigasi</a>
    <header class="attendance-primary border-b border-gray-200">
        <div class="max-w-7xl mx-auto p-4 flex flex-wrap items-center justify-between gap-4">
            <div><span class="font-bold tracking-widest text-white"><x-hellenic-emblem class="inline-block h-5 w-5 mr-1 align-middle" />OQARI</span><p class="text-sm">{{ auth()->user()->shop->name }}</p></div>
            <nav aria-label="Navigasi absensi" class="flex flex-wrap items-center gap-4 text-sm font-semibold">
                <a href="{{ route('admin.attendance.index') }}" @if(!$isReport) aria-current="page" @endif>Absensi Saya</a>
                @can('manage-users')<a href="{{ route('admin.users.index') }}" class="block px-3 py-2 font-semibold">User &amp; Hak Akses</a>@endcan
@can('view-attendance')
                    <a href="{{ route('admin.attendance.report') }}" @if($isReport) aria-current="page" @endif>Rekap Karyawan</a>
                @endcan
                <a href="{{ route('admin.dashboard') }}">Kembali ke Aplikasi</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Keluar</button></form>
            </nav>
        </div>
    </header>
    <main id="attendance-content" class="max-w-7xl mx-auto p-4 md:p-8">
        <div class="mb-6">
            <p class="attendance-accent text-xs uppercase tracking-widest font-bold">Tim &amp; Kehadiran</p>
            <h1 class="text-2xl md:text-3xl font-bold mt-2">{{ $isReport ? 'Rekap Absensi Karyawan' : 'Absensi Saya' }}</h1>
            <p class="text-sm text-gray-600 mt-2">{{ auth()->user()->name }} · {{ $today->format('d/m/Y H:i') }} · {{ $timezone }}</p>
        </div>
        @if(session('status'))
            <p role="status" class="bg-green-50 text-green-800 border border-green-200 p-4 rounded-xl mb-4">{{ session('status') }}</p>
        @endif
        @if($errors->any())
            <div role="alert" class="bg-red-50 text-red-800 border border-red-200 p-4 rounded-xl mb-4">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @unless($isReport)
            <section aria-labelledby="record-title" class="bg-white border border-line rounded-2xl p-5 md:p-7 mb-6">
                <h2 id="record-title" class="font-bold text-lg">Catat Kehadiran</h2>
                @if($active)
                    <p class="mt-2 text-sm">Anda masuk pada {{ $active->checked_in_at->setTimezone($active->timezone)->format('d/m/Y H:i') }} ({{ $active->timezone }}).</p>
                    @if($active->work_date->format('Y-m-d') !== $today->toDateString())
                        <p class="text-amber-800 text-sm mt-2">Sesi dari hari sebelumnya masih terbuka. Absen pulang akan menutup sesi tersebut.</p>
                    @endif
                @elseif($todayRecord)
                    <p class="mt-2 text-sm">Absensi hari ini sudah selesai. Kehadiran berikutnya dapat dicatat pada hari kerja berikutnya.</p>
                @else
                    <p class="mt-2 text-sm text-gray-600">Waktu dicatat oleh server. Jadwal yang tersedia digunakan untuk menghitung keterlambatan.</p>
                @endif
                @if($active || !$todayRecord)
                    <form method="POST" action="{{ route('admin.attendance.store') }}" class="mt-4" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="type" value="{{ $active ? 'check_out' : 'check_in' }}">
                        <label for="attendance-note" class="block text-sm font-semibold mb-2">Catatan {{ $active ? 'pulang' : 'masuk' }} <span class="font-normal">(opsional)</span></label>
                        <textarea id="attendance-note" name="note" maxlength="500" rows="2" class="w-full rounded-xl border-gray-300" aria-describedby="note-help">{{ old('note') }}</textarea>
                        <p id="note-help" class="text-xs text-gray-500 mt-1">Maksimal 500 karakter. Satu sesi kehadiran per hari kerja. Absensi tidak menutup shift kasir.</p>
                        <button type="submit" :disabled="submitting" class="attendance-button attendance-primary mt-4" x-text="submitting ? 'Mencatat…' : '{{ $active ? 'Absen Pulang' : 'Absen Masuk' }}'">{{ $active ? 'Absen Pulang' : 'Absen Masuk' }}</button>
                    </form>
                @endif
            </section>
        @endunless
        <section aria-label="Ringkasan periode terpilih" class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            @foreach(['Tercatat' => $summary['count'], 'Terlambat' => $summary['late'], 'Belum Pulang' => $summary['active'], 'Durasi Selesai' => intdiv($summary['minutes'], 60).'j '.($summary['minutes'] % 60).'m'] as $label => $value)
                <div class="bg-white border border-line rounded-xl p-4"><p class="text-xs text-gray-600">{{ $label }}</p><p class="text-xl font-bold mt-2">{{ $value }}</p></div>
            @endforeach
        </section>
        <section aria-labelledby="history-title" class="bg-white border border-line rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-line">
                <h2 id="history-title" class="font-bold text-lg">{{ $isReport ? 'Kehadiran Tim' : 'Riwayat Kehadiran' }}</h2>
                <form method="GET" class="flex flex-wrap items-end gap-3 mt-4">
                    <div><label for="month" class="block text-sm mb-1">Bulan</label><input id="month" type="month" name="month" value="{{ $filters['month'] }}" class="rounded-lg border-gray-300" required></div>
                    @if($isReport)
                        <div><label for="employee" class="block text-sm mb-1">Karyawan</label><select id="employee" name="user_id" class="rounded-lg border-gray-300"><option value="">Semua karyawan</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" @selected(($filters['user_id'] ?? '') == $employee->id)>{{ $employee->name }}</option>@endforeach</select></div>
                        <div><label for="status" class="block text-sm mb-1">Status</label><select id="status" name="status" class="rounded-lg border-gray-300"><option value="">Semua status</option>@foreach(['late' => 'Terlambat', 'on_time' => 'Tepat waktu', 'unscheduled' => 'Tanpa jadwal', 'active' => 'Belum pulang', 'completed' => 'Selesai'] as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></div>
                    @endif
                    <button type="submit" class="attendance-button attendance-primary">Tampilkan</button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <caption class="sr-only">Daftar kehadiran periode {{ $filters['month'] }}</caption>
                    <thead class="bg-[#f8f1e2] text-primary"><tr>@foreach(['Hari Kerja', 'Karyawan', 'Masuk / Pulang', 'Kehadiran', 'Durasi', 'Catatan'] as $heading)<th scope="col" class="p-4 whitespace-nowrap">{{ $heading }}</th>@endforeach</tr></thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr class="border-t border-[#efe6d4] align-top">
                                <td class="p-4 whitespace-nowrap">{{ $attendance->work_date->format('d/m/Y') }}</td>
                                <td class="p-4 font-semibold">{{ $attendance->employee_name ?? $attendance->user?->name }}@if(!$attendance->user)<p class="text-xs font-normal text-gray-500">Akun telah dihapus</p>@endif</td>
                                <td class="p-4 whitespace-nowrap"><p>{{ $attendance->checked_in_at->setTimezone($attendance->timezone)->format('d/m H:i') }}</p><p>{{ $attendance->checked_out_at?->setTimezone($attendance->timezone)->format('d/m H:i') ?? 'Belum pulang' }}</p><span class="text-xs text-gray-500">{{ $attendance->timezone }}</span></td>
                                <td class="p-4">
                                    @if(!$attendance->scheduled_start_at)<span class="text-gray-600">Tanpa jadwal</span>@elseif($attendance->late_minutes > 0)<span class="text-red-700 font-semibold">Terlambat {{ $attendance->late_minutes }} menit</span>@else<span class="text-green-800 font-semibold">Tepat waktu</span>@endif
                                    @if($attendance->early_leave_minutes > 0)<p class="text-amber-800 mt-1">Pulang awal {{ $attendance->early_leave_minutes }} menit</p>@endif
                                </td>
                                <td class="p-4 whitespace-nowrap">{{ $attendance->worked_minutes !== null ? intdiv($attendance->worked_minutes, 60).'j '.($attendance->worked_minutes % 60).'m' : 'Berlangsung' }}</td>
                                <td class="p-4 min-w-[180px] max-w-sm break-words"><p>{{ $attendance->check_in_note ?? '—' }}</p>@if($attendance->check_out_note)<p class="mt-1">Pulang: {{ $attendance->check_out_note }}</p>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-gray-500">Belum ada catatan kehadiran pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $attendances->links() }}</div>
        </section>
        <p class="text-xs text-gray-600 mt-4">Durasi adalah selisih waktu masuk dan pulang, belum dikurangi istirahat. Catatan tanpa jadwal tidak dihitung sebagai keterlambatan. Rekap ini bukan perhitungan gaji atau lembur.</p>
    </main>
</div></div>
</body>
</html>
