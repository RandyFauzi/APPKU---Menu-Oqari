@extends('Admin.shift.layout')
@section('content')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div><h1 class="text-[25px] font-semibold tracking-tight">Jadwal Saya</h1><p class="mt-1.5 text-[13px] text-[#7f837e]">{{ $user->name }} · {{ $today->translatedFormat('d M') }} – {{ $today->addDays(6)->translatedFormat('d M Y') }}</p></div>
    @can('record-attendance')<a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c]"><i class="fas fa-user-clock" aria-hidden="true"></i>Buka Absensi</a>@endcan
</div>
<div class="mb-5 grid gap-3 sm:grid-cols-3">
    <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-sm"><p class="text-xs text-[#7f837e]">Shift terjadwal</p><p class="mt-2 text-2xl font-semibold">{{ $shiftCount }} <span class="text-xs font-normal text-[#7f837e]">shift</span></p></div>
    <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-sm"><p class="text-xs text-[#7f837e]">Hari terjadwal</p><p class="mt-2 text-2xl font-semibold">{{ $workDays }} <span class="text-xs font-normal text-[#7f837e]">dari 7 hari</span></p></div>
    <div class="rounded-xl border border-[#e9ebf0] bg-white p-5 shadow-sm"><p class="text-xs text-[#7f837e]">Total durasi rencana</p><p class="mt-2 text-2xl font-semibold">{{ intdiv($totalMinutes, 60) }} <span class="text-xs font-normal text-[#7f837e]">jam {{ $totalMinutes % 60 ? ($totalMinutes % 60).' menit' : '' }}</span></p></div>
</div>
<section class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm" aria-labelledby="personal-schedule-title">
    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#e9ebf0] px-5 py-4"><h2 id="personal-schedule-title" class="text-sm font-semibold">Jadwal 7 hari ke depan</h2><span class="rounded-md bg-[#fff6f0] px-2.5 py-1 text-[11px] font-medium text-[#c94d00]">{{ $timezone }}</span></div>
    <div class="divide-y divide-[#e9ebf0]">
        @foreach($schedule as $day)
            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start {{ $loop->first ? 'bg-[#fffaf6]' : '' }}">
                <div class="flex shrink-0 items-center gap-3 sm:w-36">
                    <div class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-lg {{ $loop->first ? 'bg-[#fff0e4] text-[#c94d00]' : 'bg-[#f8f9fa] text-[#53565a]' }}"><span class="text-lg font-semibold leading-none">{{ $day['date']->format('d') }}</span><span class="mt-1 text-[10px]">{{ $day['date']->translatedFormat('M') }}</span></div>
                    <div><p class="text-xs font-semibold {{ $loop->first ? 'text-[#c94d00]' : 'text-[#53565a]' }}">{{ $day['label'] }}</p><p class="mt-1 text-[11px] text-[#7f837e]">{{ $day['date']->translatedFormat('l') }}</p></div>
                </div>
                <div class="min-w-0 flex-1 space-y-3">
                    @forelse($day['shifts'] as $shift)
                        <div class="flex flex-wrap items-start justify-between gap-3 rounded-lg border border-[#e9ebf0] bg-white px-4 py-3">
                            <div><p class="text-sm font-semibold">{{ substr($shift->start_time, 0, 5) }} <span class="mx-1 font-normal text-[#9ca3af]">–</span> {{ substr($shift->end_time, 0, 5) }} @if($shift->endsAt()->toDateString() !== $shift->startsAt()->toDateString())<span class="ml-2 text-[10px] font-medium text-[#c94d00]">+1 hari</span>@endif</p>@if($shift->position)<p class="mt-1 text-xs text-[#53565a]">{{ $shift->position }}</p>@endif @if($shift->notes)<p class="mt-2 break-words text-xs leading-relaxed text-[#7f837e]">{{ $shift->notes }}</p>@endif</div>
                            <span class="rounded-md bg-[#f8f9fa] px-2.5 py-1 text-[11px] text-[#53565a]">{{ intdiv($shift->durationMinutes(), 60) }} jam @if($shift->durationMinutes() % 60){{ $shift->durationMinutes() % 60 }} menit @endif</span>
                        </div>
                    @empty
                        <p class="flex min-h-[48px] items-center gap-2 text-xs text-[#9ca3af]"><i class="far fa-calendar" aria-hidden="true"></i>Belum ada jadwal</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
    <p class="border-t border-[#e9ebf0] px-5 py-4 text-[11px] leading-relaxed text-[#7f837e]">Jadwal mengikuti zona waktu absensi kafe. Hubungi pengelola jika perlu perubahan; durasi di atas adalah rencana kerja, bukan rekap kehadiran.</p>
</section>
@endsection
