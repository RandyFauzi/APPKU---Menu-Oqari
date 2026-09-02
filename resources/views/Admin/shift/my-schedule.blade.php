@extends('Admin.Layouts.app')

@section('title', 'My Schedule')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-800">
            <i class="fas fa-calendar-check text-primary mr-2"></i> My Schedule
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $user->name }} &middot; <span class="uppercase font-semibold text-primary">{{ $user->role }}</span>
        </p>
    </div>

    {{-- 7-Day Schedule Grid --}}
    <div class="space-y-3">
        @foreach($schedule as $day)
            <div class="bg-white rounded-2xl shadow-sm border {{ $day['is_off'] ? 'border-gray-100' : 'border-primary/20' }} overflow-hidden">

                @if($day['is_off'])
                    {{-- OFF Day --}}
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="w-16 text-center shrink-0">
                            <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">
                                {{ $day['label'] }}
                            </p>
                            <p class="text-2xl font-black text-gray-200 mt-0.5">
                                {{ $day['date']->format('d') }}
                            </p>
                        </div>
                        <div class="h-10 w-px bg-gray-100"></div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-400 font-semibold">
                                <i class="fas fa-moon text-gray-300"></i> OFF
                            </span>
                        </div>
                    </div>

                @else
                    {{-- Work Day --}}
                    <div class="flex items-center gap-4 px-6 py-4">
                        <div class="w-16 text-center shrink-0">
                            <p class="text-xs font-extrabold text-primary uppercase tracking-wider">
                                {{ $day['label'] }}
                            </p>
                            <p class="text-2xl font-black text-gray-800 mt-0.5">
                                {{ $day['date']->format('d') }}
                            </p>
                        </div>
                        <div class="h-12 w-px bg-primary/20"></div>
                        <div class="flex-1">
                            <p class="text-lg font-black text-gray-800 leading-tight">
                                {{ \Carbon\Carbon::parse($day['shift']->start_time)->format('H:i') }}
                                <span class="text-gray-400 font-normal mx-1">–</span>
                                {{ \Carbon\Carbon::parse($day['shift']->end_time)->format('H:i') }}
                            </p>
                            @if($day['shift']->position)
                                <p class="text-sm text-primary font-semibold mt-0.5">
                                    {{ $day['shift']->position }}
                                </p>
                            @endif
                            @if($day['shift']->notes)
                                <p class="text-xs text-gray-400 mt-1">
                                    <i class="fas fa-sticky-note mr-1"></i>{{ $day['shift']->notes }}
                                </p>
                            @endif
                        </div>
                        {{-- Duration Badge --}}
                        @php
                            $start    = \Carbon\Carbon::parse($day['shift']->start_time);
                            $end      = \Carbon\Carbon::parse($day['shift']->end_time);
                            $duration = $start->diffInHours($end);
                        @endphp
                        <div class="shrink-0 text-right">
                            <span class="inline-block bg-primary/10 text-primary text-sm font-bold px-3 py-1 rounded-full">
                                {{ $duration }}h
                            </span>
                        </div>
                    </div>
                @endif

            </div>
        @endforeach
    </div>

    {{-- Footer Note --}}
    <p class="text-center text-xs text-gray-400 mt-8">
        Jadwal ditampilkan 7 hari ke depan &middot; Hubungi manager jika ada perubahan
    </p>

</div>
@endsection
