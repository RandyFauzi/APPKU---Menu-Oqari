@props([
    'showClock' => true,
    'showDate' => true,
    'timezone' => null,
])

@php
    $member = auth()->user();
    $shop = $member?->shop;
    $resolvedTimezone = $timezone ?? $shop?->normalized_operating_hours['timezone'] ?? config('app.timezone', 'Asia/Jakarta');
    $currentDate = now()->setTimezone($resolvedTimezone);
    $initialTime = $currentDate->format('H:i');
    $initialDate = $currentDate->translatedFormat('d M Y');
@endphp

<div class="flex items-center gap-2 select-none" x-data="{
    timezone: '{{ $resolvedTimezone }}',
    time: '{{ $initialTime }}',
    date: '{{ $initialDate }}',
    updateTime() {
        try {
            const now = new Date();
            const timeFormatter = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false,
                timeZone: this.timezone
            });
            this.time = timeFormatter.format(now).replace('.', ':');

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                timeZone: this.timezone
            });
            this.date = dateFormatter.format(now).replace('.', '');
        } catch (e) {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            this.time = h + ':' + m;
        }
    }
}" x-init="updateTime(); setInterval(() => updateTime(), 10000)">

    @if($showClock)
        <!-- Unified Clock Pill -->
        <div class="hidden sm:flex items-center gap-1.5 bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] text-[11px] font-medium text-[#53565a] transition-colors"
             title="Waktu Operasional ({{ $resolvedTimezone }})">
            <img src="{{ asset('images/sparta/clock.svg') }}" alt="" class="w-3.5 h-3.5 opacity-75">
            <span x-text="time">{{ $initialTime }}</span>
        </div>
    @endif

    @if($showDate)
        <!-- Unified Date Pill -->
        <div class="bg-[#f1f0ec] border border-[#e6e5e0] h-[34px] px-[10px] rounded-[9px] flex items-center text-[11px] font-medium text-[#53565a] tracking-tight transition-colors">
            <span x-text="date">{{ $initialDate }}</span>
        </div>
    @endif
</div>
