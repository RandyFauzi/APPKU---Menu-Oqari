@extends('Admin.shift.layout')
@section('content')
<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-[25px] font-semibold tracking-tight text-[#111827]">Jadwal Saya</h1>
            <p class="mt-1 text-[13px] text-[#7f837e]">{{ $user->name }} · {{ $today->translatedFormat('d M') }} – {{ $today->addDays(6)->translatedFormat('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            @can('manage-crew')
            <a href="{{ route('admin.shifts.index') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-[#e9ebf0] bg-white px-4 text-xs font-semibold text-[#53565a] shadow-xs hover:bg-gray-50 hover:text-[#111827] transition-all">
                <i class="fas fa-users text-[#ea580c]" aria-hidden="true"></i>Kelola Jadwal Tim
            </a>
            @endcan
            @can('record-attendance')
            <a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] transition-all">
                <i class="fas fa-user-clock" aria-hidden="true"></i>Buka Absensi
            </a>
            @endcan
        </div>
    </div>

    @php
        $todayShifts = $schedule[0]['shifts'] ?? collect();
    @endphp

    <!-- Hero Card: Shift Hari Ini -->
    <div class="rounded-2xl border border-orange-200 bg-gradient-to-br from-[#fffaf6] to-[#fff3ea] p-5 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-[#ea580c] to-[#f9731f] text-white shadow-xs">
                    <i class="fas fa-calendar-day text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#ea580c]">Status Shift Hari Ini</span>
                        @if($todayShifts->count() > 0)
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">ADA JADWAL SHIFT</span>
                        @else
                            <span class="rounded-md bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-[#53565a]">LIBUR / TIDAK ADA SHIFT</span>
                        @endif
                    </div>
                    @if($todayShifts->count() > 0)
                        @foreach($todayShifts as $tShift)
                            <h3 class="mt-1 text-lg font-bold text-[#111827]">
                                {{ substr($tShift->start_time, 0, 5) }} – {{ substr($tShift->end_time, 0, 5) }}
                                <span class="text-xs font-normal text-[#53565a]">({{ intdiv($tShift->durationMinutes(), 60) }} jam {{ $tShift->durationMinutes() % 60 ? ($tShift->durationMinutes() % 60).' mnt' : '' }})</span>
                            </h3>
                            <p class="mt-0.5 text-xs text-[#53565a]">
                                Posisi: <strong class="text-[#111827]">{{ $tShift->position ?: 'Staf Operasional' }}</strong>
                                @if($tShift->notes) · Catatan: {{ $tShift->notes }} @endif
                            </p>
                        @endforeach
                    @else
                        <p class="mt-1 text-xs text-[#53565a]">Hari ini Anda tidak memiliki jadwal shift kerja. Selamat beristirahat atau nikmati hari libur Anda.</p>
                    @endif
                </div>
            </div>

            @can('record-attendance')
            <div class="shrink-0">
                <a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#ea580c] to-[#f9731f] px-5 text-xs font-bold text-white shadow-sm hover:from-[#c2410c] hover:to-[#ea580c] transition-all">
                    <i class="fas fa-fingerprint text-sm" aria-hidden="true"></i>Buka Absensi Sekarang
                </a>
            </div>
            @endcan
        </div>
    </div>

    <!-- 3 KPI Summary Cards -->
    <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs text-[#7f837e]">Shift terjadwal</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]">
                    <i class="fas fa-calendar-check text-xs"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-semibold">{{ $shiftCount }} <span class="text-xs font-normal text-[#7f837e]">shift</span></p>
        </div>
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs text-[#7f837e]">Hari terjadwal</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fas fa-business-time text-xs"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-semibold">{{ $workDays }} <span class="text-xs font-normal text-[#7f837e]">dari 7 hari</span></p>
        </div>
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs text-[#7f837e]">Total durasi rencana</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-clock text-xs"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-semibold">{{ intdiv($totalMinutes, 60) }} <span class="text-xs font-normal text-[#7f837e]">jam {{ $totalMinutes % 60 ? ($totalMinutes % 60).' menit' : '' }}</span></p>
        </div>
    </div>

    <!-- 7-Day Timeline -->
    <section class="overflow-hidden rounded-2xl border border-[#e9ebf0] bg-white shadow-sm" aria-labelledby="personal-schedule-title">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e9ebf0] px-5 py-4">
            <div>
                <h2 id="personal-schedule-title" class="text-sm font-bold text-[#111827]">Jadwal 7 hari ke depan</h2>
                <p class="text-xs text-[#7f837e]">Rencana kerja personal Anda yang tercatat di sistem.</p>
            </div>
            <span class="rounded-md bg-[#fff6f0] px-2.5 py-1 text-[11px] font-semibold text-[#c94d00]">{{ $timezone }}</span>
        </div>
        <div class="divide-y divide-[#e9ebf0]">
            @foreach($schedule as $day)
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start transition-colors {{ $loop->first ? 'bg-[#fffaf6]' : 'hover:bg-[#fafbfc]' }}">
                    <div class="flex shrink-0 items-center gap-3 sm:w-36">
                        <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl {{ $loop->first ? 'bg-[#fff0e4] text-[#c94d00] font-bold border border-orange-200' : 'bg-[#f8f9fa] text-[#53565a] border border-[#e9ebf0]' }}">
                            <span class="text-base font-bold leading-none">{{ $day['date']->format('d') }}</span>
                            <span class="mt-1 text-[10px] uppercase font-semibold">{{ $day['date']->translatedFormat('M') }}</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold {{ $loop->first ? 'text-[#c94d00]' : 'text-[#111827]' }}">{{ $day['label'] }}</p>
                                @if($loop->first)
                                    <span class="rounded bg-orange-100 px-1.5 py-0.2 text-[9px] font-bold text-[#c94d00]">HARI INI</span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[11px] text-[#7f837e]">{{ $day['date']->translatedFormat('l') }}</p>
                        </div>
                    </div>
                    <div class="min-w-0 flex-1 space-y-2.5">
                        @forelse($day['shifts'] as $shift)
                            <div class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-[#e9ebf0] bg-white px-4 py-3 shadow-2xs hover:border-orange-200 transition-colors">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-bold text-[#111827]">
                                            {{ substr($shift->start_time, 0, 5) }}
                                            <span class="mx-1 font-normal text-[#9ca3af]">–</span>
                                            {{ substr($shift->end_time, 0, 5) }}
                                        </p>
                                        @if($shift->endsAt()->toDateString() !== $shift->startsAt()->toDateString())
                                            <span class="rounded bg-orange-100 px-1.5 py-0.5 text-[10px] font-bold text-[#c94d00]">+1 hari</span>
                                        @endif
                                    </div>
                                    @if($shift->position)
                                        <p class="mt-1 text-xs font-medium text-[#53565a]">
                                            <span class="inline-block rounded-md bg-[#f8f9fa] border border-[#e9ebf0] px-2 py-0.5 text-[11px]">{{ $shift->position }}</span>
                                        </p>
                                    @endif
                                    @if($shift->notes)
                                        <p class="mt-2 break-words text-xs leading-relaxed text-[#7f837e]">{{ $shift->notes }}</p>
                                    @endif
                                </div>
                                <span class="rounded-lg bg-[#f8f9fa] border border-[#e9ebf0] px-2.5 py-1 text-[11px] font-medium text-[#53565a]">
                                    {{ intdiv($shift->durationMinutes(), 60) }} jam @if($shift->durationMinutes() % 60){{ $shift->durationMinutes() % 60 }} menit @endif
                                </span>
                            </div>
                        @empty
                            <p class="flex min-h-[48px] items-center gap-2 text-xs text-[#9ca3af] italic">
                                <i class="far fa-calendar text-[11px]" aria-hidden="true"></i>Belum ada jadwal
                            </p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
        <p class="border-t border-[#e9ebf0] px-5 py-3.5 text-[11px] leading-relaxed text-[#7f837e]">
            Jadwal mengikuti zona waktu absensi kafe. Hubungi pengelola jika perlu perubahan; durasi di atas adalah rencana kerja, bukan rekap kehadiran.
        </p>
    </section>
</div>
@endsection
