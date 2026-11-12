<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekonsiliasi Kas — {{ $shop->name ?? 'OQARI' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .sparta-register { font-family: 'DM Sans', sans-serif; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-register min-h-screen bg-white text-[#1e211f] antialiased" x-data="reconciliationRoot()">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-white">
    <a href="#register-content" class="sr-only focus:not-sr-only">Lewati navigasi</a>

    {{-- TOP HEADER BAR (Sparta Style) --}}
    <header class="h-[52px] bg-white border-b border-[#e9ebf0] px-4 lg:px-[26px] flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                class="w-9 h-9 rounded-lg bg-[#eae8e3] flex items-center justify-center text-[#1e211f] hover:bg-[#dfddd7] transition-colors md:hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
            <span class="text-[13px] font-semibold text-[#1e211f]">Rekonsiliasi Kas</span>
        </div>

        {{-- Topbar Datetime (Standardized Sparta Component) --}}
        <x-topbar-datetime />
    </header>

    @php
        $labels = [
            'BALANCED' => 'Kas sesuai',
            'SHORT' => 'Kas kurang',
            'OVER' => 'Kas lebih',
            'UNCOUNTED' => 'Belum dihitung',
        ];
    @endphp

    <main id="register-content" class="flex-1 w-full p-4 md:p-[24px] space-y-5">

        {{-- PAGE TITLE & TOP ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <p class="text-[11px] font-semibold text-[#df5826] uppercase tracking-[0.22px]">Operasional &amp; Kasir</p>
                <h1 class="text-[22px] font-bold text-[#1e211f] tracking-[-0.3px] leading-tight mt-0.5">
                    Rekonsiliasi Kas
                </h1>
                <p class="text-[12px] text-[#7f837e] mt-0.5">
                    Hitung dan cocokkan uang fisik di laci kasir dengan catatan sistem saat menutup shift.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.pos.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-[10px] bg-white border border-[#e6e5e0] text-[#111827] text-xs font-semibold shadow-[0px_1px_2px_rgba(0,0,0,0.02)] hover:bg-gray-50 transition">
                    <i class="fas fa-cash-register text-[#df5826]"></i>
                    <span>Buka Layar Kasir</span>
                </a>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div role="status" class="bg-[#eaf7ef] border border-[#bfe5cd] text-[#289e5c] px-4 py-3 rounded-[12px] text-[12.5px] font-semibold flex items-center gap-2.5">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] text-[12.5px] font-medium">
                <div class="font-bold flex items-center gap-1.5 mb-1">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>Penutupan shift belum berhasil disimpan:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- MAIN ACTIVE SHIFT RECONCILIATION SECTION --}}
        @if($activeSession)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                {{-- CARD 1: RINGKASAN KAS SISTEM (SHIFT AKTIF) --}}
                <section aria-labelledby="summary-heading" class="lg:col-span-5 bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-6 relative overflow-hidden shadow-[0px_3px_12px_rgba(38,39,35,0.04)] flex flex-col justify-between">
                    {{-- Accent top border --}}
                    <div class="h-1 bg-gradient-to-r from-[#f06428] to-[#df5826] absolute top-0 inset-x-0"></div>

                    <div>
                        {{-- Shift Header Pill --}}
                        <div class="flex items-start justify-between gap-2 pb-3.5 border-b border-[#f0f0ee]">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div>
                                    <h2 id="summary-heading" class="text-[14px] font-bold text-[#111827]">Shift Aktif #{{ $activeSession->id }}</h2>
                                    <p class="text-[11px] text-[#7f837e]">
                                        {{ auth()->user()->name }} · {{ $activeSession->register->name }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                <span>Belum dihitung</span>
                            </span>
                        </div>

                        <p class="text-[11.5px] text-[#53565a] mt-3">
                            <i class="far fa-clock text-[#7f837e] mr-1"></i>Dibuka {{ $activeSession->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i') }}
                        </p>

                        {{-- Big Highlighted Box: Kas Seharusnya (Hero Target) --}}
                        <div class="mt-3.5 p-4 rounded-[16px] bg-[#fff8f3] border border-[#f5d5c6] shadow-2xs">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#df5826] block">Kas seharusnya</span>
                            <div class="text-[24px] font-extrabold text-[#111827] mt-0.5 leading-tight">
                                Rp {{ number_format($breakdown['expected_cash'], 2, ',', '.') }}
                            </div>
                            <p class="text-[11px] text-[#7f837e] mt-1">
                                Target nominal saldo uang tunai yang harus ada di dalam laci kasir saat ini.
                            </p>
                        </div>

                        {{-- Calculation Breakdown Table --}}
                        <dl class="mt-4 space-y-2 text-xs">
                            <div class="flex justify-between items-center py-1.5 border-b border-[#f0f0ee]">
                                <dt class="text-[#53565a]">Modal awal kas:</dt>
                                <dd class="font-semibold text-[#111827]">Rp {{ number_format($breakdown['opening_cash'], 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-[#f0f0ee]">
                                <dt class="text-[#53565a]">Penjualan tunai:</dt>
                                <dd class="font-bold text-emerald-600">+ Rp {{ number_format($breakdown['cash_sales'], 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-[#f0f0ee]">
                                <dt class="text-[#53565a]">Kas masuk lainnya:</dt>
                                <dd class="font-semibold text-emerald-600">+ Rp {{ number_format($breakdown['cash_in'], 2, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between items-center py-1.5 border-b border-[#f0f0ee]">
                                <dt class="text-[#53565a]">Kas keluar:</dt>
                                <dd class="font-semibold text-red-600">− Rp {{ number_format($breakdown['cash_out'], 2, ',', '.') }}</dd>
                            </div>
                        </dl>

                        {{-- Non-Cash Breakdown --}}
                        <div class="mt-4 p-3 rounded-[14px] bg-[#fafaf9] border border-[#e6e5e0] text-xs">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-bold text-[#111827] flex items-center gap-1.5 text-[11.5px]">
                                    <i class="fas fa-credit-card text-[#7f837e]"></i>
                                    <span>Nontunai (Masuk ke Rekening / QRIS)</span>
                                </span>
                                <span class="text-[10px] text-gray-500 font-medium">Di luar laci kas</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                                <div class="bg-white p-2 rounded-[8px] border border-gray-200">
                                    <span class="text-[#7f837e] block text-[9.5px]">QRIS</span>
                                    <span class="font-bold text-[#111827]">Rp {{ number_format($activeSession->total_qris_sales, 2, ',', '.') }}</span>
                                </div>
                                <div class="bg-white p-2 rounded-[8px] border border-gray-200">
                                    <span class="text-[#7f837e] block text-[9.5px]">Kartu / EDC</span>
                                    <span class="font-bold text-[#111827]">Rp {{ number_format($activeSession->total_other_sales, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-[#f0f0ee] flex items-center justify-between">
                        <a href="{{ route('admin.register.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[9px] bg-white border border-[#d8dce2] text-xs font-semibold text-[#53565a] hover:bg-gray-50 hover:text-[#111827] transition shadow-2xs">
                            <i class="fas fa-rotate text-xs text-[#df5826]"></i>
                            <span>Perbarui Angka Kas</span>
                        </a>
                        <span class="text-[10.5px] text-[#7f837e]">Angka realtime sesi</span>
                    </div>
                </section>

                {{-- CARD 2: HITUNG FISIK & HITUNG SELISIH REALTIME --}}
                <section aria-labelledby="count-heading" class="lg:col-span-7 bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-6 shadow-[0px_3px_12px_rgba(38,39,35,0.04)]"
                         x-data="cashReconciliation({{ Illuminate\Support\Js::from($breakdown['expected_cash']) }}, {{ Illuminate\Support\Js::from(old('actual_cash', '')) }})">
                    
                    <div class="flex items-center justify-between pb-3.5 border-b border-[#f0f0ee]">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-[12px] bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <div>
                                <h2 id="count-heading" class="text-[14px] font-bold text-[#111827]">Hitung Fisik &amp; Tutup Shift</h2>
                                <p class="text-[11px] text-[#7f837e]">Ketik uang tunai nyata di laci atau samakan bila pas.</p>
                            </div>
                        </div>

                        <!-- 1-Click exact match helper button -->
                        <button type="button" @click="setExact()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[9px] border border-[#e6e5e0] bg-[#fafaf9] hover:bg-gray-100 text-xs font-bold text-[#df5826] transition shadow-2xs cursor-pointer"
                            title="Isi nominal fisik sama persis dengan kas seharusnya">
                            <i class="fas fa-wand-magic-sparkles text-xs"></i>
                            <span>Isi Sesuai Kas</span>
                        </button>
                    </div>

                    <form action="{{ route('shift.close') }}" method="POST" class="mt-5 space-y-4" @submit="handleSubmit($event)">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $activeSession->id }}">
                        <input type="hidden" name="expected_cash_confirmation" value="{{ $breakdown['expected_cash'] }}">

                        <!-- Input Nominal Fisik -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="actual-cash" class="block text-xs font-bold text-[#111827]">
                                    Uang Tunai Fisik di Laci Kas (Rp) <span class="text-red-500">*</span>
                                </label>
                                
                                <!-- Denomination toggle button -->
                                <button type="button" @click="showDenom = !showDenom" class="text-[11px] font-semibold text-[#df5826] hover:underline flex items-center gap-1 cursor-pointer">
                                    <i class="fas fa-coins"></i>
                                    <span x-text="showDenom ? 'Sembunyikan Kalkulator Pecahan' : 'Buka Kalkulator Pecahan Uang'"></span>
                                </button>
                            </div>

                            <div class="relative">
                                <span class="absolute left-3.5 top-3 text-sm font-bold text-gray-400">Rp</span>
                                <input id="actual-cash" name="actual_cash" type="number" inputmode="decimal" min="0" max="999999999.99" step="any" required
                                    x-model="actual" value="{{ old('actual_cash') }}" aria-describedby="actual-help"
                                    placeholder="Contoh: {{ (int)$breakdown['expected_cash'] }}"
                                    class="w-full pl-11 pr-4 py-3 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] text-[16px] font-black text-[#111827] outline-none transition">
                            </div>
                            <p id="actual-help" class="text-[11px] text-[#7f837e] mt-1.5">
                                Hitung seluruh uang tunai kertas dan koin termasuk modal awal. Jangan masukkan saldo QRIS atau kartu debit.
                            </p>
                        </div>

                        <!-- Collapsible Denomination Counter Calculator -->
                        <div x-show="showDenom" x-collapse x-cloak class="p-3.5 rounded-[14px] bg-gray-50 border border-gray-200 space-y-2.5">
                            <p class="text-[11px] font-bold text-[#111827] uppercase tracking-wider flex items-center justify-between">
                                <span>Kalkulator Lembaran &amp; Koin</span>
                                <button type="button" @click="resetDenom()" class="text-[10px] text-red-600 hover:underline cursor-pointer">Reset</button>
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 100.000</label>
                                    <input type="number" min="0" x-model.number="denom['100k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 50.000</label>
                                    <input type="number" min="0" x-model.number="denom['50k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 20.000</label>
                                    <input type="number" min="0" x-model.number="denom['20k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 10.000</label>
                                    <input type="number" min="0" x-model.number="denom['10k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 5.000</label>
                                    <input type="number" min="0" x-model.number="denom['5k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 2.000</label>
                                    <input type="number" min="0" x-model.number="denom['2k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Rp 1.000</label>
                                    <input type="number" min="0" x-model.number="denom['1k']" @input="updateFromDenom()" placeholder="0 lbr"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 block">Koin / Receh (Rp)</label>
                                    <input type="number" min="0" x-model.number="denom['coins']" @input="updateFromDenom()" placeholder="Rp 0"
                                        class="w-full p-1.5 rounded-lg border border-gray-200 text-xs font-semibold text-center">
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================== -->
                        <!-- 3-WAY COMPARISON BOX: SEHARUSNYA vs AKTUAL vs SELISIH          -->
                        <!-- ============================================================== -->
                        <div class="rounded-[14px] border p-4 transition-all"
                             :class="difference === null ? 'border-[#e6e5e0] bg-[#fafaf9]' : (difference === 0 ? 'border-emerald-200 bg-emerald-50/60' : (difference < 0 ? 'border-red-200 bg-red-50/60' : 'border-amber-200 bg-amber-50/60'))">
                            
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-[#7f837e]">
                                    Perbandingan Rekonsiliasi Kas
                                </span>
                                
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      :class="difference === null ? 'bg-gray-200 text-[#53565a]' : (difference === 0 ? 'bg-emerald-100 text-emerald-800' : (difference < 0 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'))">
                                    <i class="fas" :class="difference === null ? 'fa-hourglass-start' : (difference === 0 ? 'fa-check' : (difference < 0 ? 'fa-arrow-down' : 'fa-arrow-up'))"></i>
                                    <span x-text="label"></span>
                                </span>
                            </div>

                            <!-- 3 Mini Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                                <!-- Col 1: Kas Seharusnya -->
                                <div class="bg-white p-3 rounded-[10px] border border-gray-200/70 shadow-2xs">
                                    <span class="text-[10px] text-[#7f837e] font-medium block">Kas Seharusnya</span>
                                    <p class="text-xs font-bold text-[#111827] mt-0.5" x-text="money(expected)"></p>
                                </div>

                                <!-- Col 2: Kas Fisik Aktual -->
                                <div class="bg-white p-3 rounded-[10px] border border-gray-200/70 shadow-2xs">
                                    <span class="text-[10px] text-[#7f837e] font-medium block">Kas Fisik Aktual</span>
                                    <p class="text-xs font-bold text-[#111827] mt-0.5" x-text="actualValid ? money(actual) : '—'"></p>
                                </div>

                                <!-- Col 3: Selisih -->
                                <div class="p-3 rounded-[10px] border shadow-2xs"
                                     :class="difference === null ? 'bg-white border-gray-200/70' : (difference === 0 ? 'bg-emerald-100/60 border-emerald-300 text-emerald-900' : (difference < 0 ? 'bg-red-100/60 border-red-300 text-red-900' : 'bg-amber-100/60 border-amber-300 text-amber-900'))">
                                    <span class="text-[10px] font-medium block"
                                          :class="difference === null ? 'text-[#7f837e]' : (difference === 0 ? 'text-emerald-700' : (difference < 0 ? 'text-red-700' : 'text-amber-700'))">
                                        Selisih (Fisik − Sistem)
                                    </span>
                                    <p class="text-xs font-bold mt-0.5" x-text="difference === null ? '—' : (difference > 0 ? '+' : '') + money(difference)"></p>
                                </div>
                            </div>

                            <!-- Dynamic Explanatory Message -->
                            <div class="mt-2.5 text-[11px] leading-relaxed">
                                <template x-if="difference === null">
                                    <p class="text-[#7f837e]">
                                        ℹ️ Masukkan angka kas fisik di laci untuk menghitung selisih secara otomatis.
                                    </p>
                                </template>
                                <template x-if="difference === 0">
                                    <p class="text-emerald-700 font-medium">
                                        ✓ Kas fisik di laci tepat sesuai dengan perhitungan saldo sistem.
                                    </p>
                                </template>
                                <template x-if="difference < 0">
                                    <p class="text-red-700 font-semibold">
                                        ⚠️ Kas fisik kurang <span x-text="money(Math.abs(difference))"></span> dari sistem. Wajib sertakan keterangan penyebab selisih di catatan.
                                    </p>
                                </template>
                                <template x-if="difference > 0">
                                    <p class="text-amber-700 font-semibold">
                                        ℹ️ Kas fisik lebih <span x-text="money(difference)"></span> dari sistem (kelebihan kas). Wajib sertakan keterangan di catatan.
                                    </p>
                                </template>
                            </div>
                        </div>

                        <!-- Catatan Penutupan Textarea -->
                        <div>
                            <label for="closing-notes" class="block text-xs font-bold text-[#111827] mb-1">
                                Catatan Penutupan Shift 
                                <span class="font-normal" :class="isNotesRequired ? 'text-red-600 font-bold' : 'text-[#7f837e]'" 
                                      x-text="isNotesRequired ? '(Wajib Diisi)*' : '(Opsional)'"></span>
                            </label>
                            <textarea id="closing-notes" name="notes" rows="2" maxlength="1000" x-model="notes"
                                :required="isNotesRequired"
                                class="w-full p-3 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] text-xs text-[#111827] outline-none transition"
                                placeholder="Jelaskan penyebab selisih atau hasil pemeriksaan uang laci..."></textarea>
                            <p class="text-[11px] text-[#7f837e] mt-0.5">
                                <span x-show="isNotesRequired" class="text-red-600 font-medium">Keterangan wajib diisi ketika ada selisih kas kurang atau lebih. </span>
                                Maksimal 1.000 karakter.
                            </p>
                        </div>

                        <!-- Action Submit Button -->
                        <div class="pt-2">
                            <button type="submit" :disabled="submitting || difference === null || (isNotesRequired && (!notes || notes.trim() === ''))"
                                class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-[12px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-xs font-bold hover:opacity-95 transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                <i class="fas fa-lock text-xs"></i>
                                <span x-text="submitting ? 'Menyimpan & Menutup…' : 'Simpan Hitungan & Tutup Shift'">Simpan Hitungan &amp; Tutup Shift</span>
                            </button>
                            <p class="text-[10.5px] text-[#7f837e] text-center mt-2">
                                Menyimpan formulir akan mencatat rekonsiliasi kas permanen dan menutup sesi kasir saat ini.
                            </p>
                        </div>
                    </form>
                </section>
            </div>
        @else
            {{-- EMPTY STATE: NO ACTIVE SHIFT --}}
            <section class="bg-white border border-[#e6e5e0] rounded-[20px] p-6 text-center max-w-xl mx-auto shadow-[0px_3px_12px_rgba(38,39,35,0.04)] space-y-4">
                <div class="w-12 h-12 rounded-[14px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto text-lg">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-[#111827]">Tidak Ada Shift Kasir Aktif</h2>
                    <p class="text-xs text-[#7f837e] mt-1">
                        Buka shift kasir baru dengan modal awal untuk mulai melayani transaksi penjualan dan mencatat kas.
                    </p>
                </div>

                {{-- Direct Quick Open Shift Form --}}
                <form action="{{ route('shift.open') }}" method="POST" class="max-w-sm mx-auto space-y-3 pt-2">
                    @csrf
                    <div class="text-left">
                        <label for="opening-cash-input" class="block text-xs font-semibold text-[#111827] mb-1">
                            Modal Awal Kas di Laci (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-400">Rp</span>
                            <input id="opening-cash-input" name="opening_cash" type="number" min="0" step="any" required value="100000"
                                class="w-full pl-9 pr-3 py-2.5 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] text-xs font-bold text-[#111827] focus:bg-white focus:border-[#df5826] outline-none">
                        </div>
                    </div>
                    <button type="submit" 
                        class="w-full py-2.5 px-4 rounded-[12px] bg-gradient-to-b from-[#f06428] to-[#df5826] text-white text-xs font-bold shadow-xs hover:opacity-95 transition cursor-pointer">
                        Buka Shift Kasir Sekarang
                    </button>
                </form>
            </section>
        @endif

        {{-- RIWAYAT SESI KASIR TABLE --}}
        <section aria-labelledby="history-heading" class="bg-white border border-[#e6e5e0] rounded-[20px] overflow-hidden shadow-[0px_3px_12px_rgba(38,39,35,0.04)]">
            <div class="p-4 md:p-5 border-b border-[#e6e5e0] flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                <div>
                    <h2 id="history-heading" class="text-[15px] font-bold text-[#1e211f] leading-tight">
                        Riwayat Sesi Kasir &amp; Rekonsiliasi
                    </h2>
                    <p class="text-[11.5px] text-[#8b8e93] mt-0.5">
                        {{ auth()->user()->can('view-reports') ? 'Menampilkan riwayat rekonsiliasi seluruh sesi kasir kafe.' : 'Menampilkan sesi kasir Anda sendiri.' }}
                    </p>
                </div>

                <form method="GET" action="{{ route('admin.register.index') }}" class="flex flex-wrap items-end gap-2.5">
                    <div>
                        <label for="filter-status" class="block text-[11px] font-medium text-[#53565a] mb-1">Status Kas</label>
                        <select id="filter-status" name="status" 
                            class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition">
                            <option value="">Semua status</option>
                            @foreach($labels as $key => $label)
                                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter-from" class="block text-[11px] font-medium text-[#53565a] mb-1">Dibuka Mulai</label>
                        <input type="date" id="filter-from" name="from" value="{{ $filters['from'] ?? '' }}" 
                            class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition">
                    </div>

                    <div>
                        <label for="filter-to" class="block text-[11px] font-medium text-[#53565a] mb-1">Sampai</label>
                        <input type="date" id="filter-to" name="to" value="{{ $filters['to'] ?? '' }}" 
                            class="bg-[#f8f9fa] border border-[#d8dce2] focus:border-[#df5826] focus:bg-white rounded-[9px] px-3 py-2 text-[12px] font-semibold text-[#111827] outline-none transition">
                    </div>

                    <button type="submit" 
                        class="py-2 px-4 rounded-[9px] bg-gradient-to-b from-[#f06428] to-[#df5826] border border-[#df5826] shadow-[0px_1px_2px_rgba(223,88,38,0.18)] text-white text-[12px] font-semibold hover:opacity-95 transition min-h-[36px] cursor-pointer">
                        Terapkan
                    </button>
                    <a href="{{ route('admin.register.index') }}" 
                       class="py-2 px-3 rounded-[9px] bg-white border border-[#d8dce2] text-[12px] font-semibold text-[#53565a] hover:bg-gray-50 transition min-h-[36px] flex items-center cursor-pointer">
                        Reset
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-[12.5px] text-left border-collapse">
                    <caption class="sr-only">Daftar riwayat sesi kasir</caption>
                    <thead class="bg-[#f8f9fa] border-b border-[#e6e5e0] text-[11px] font-semibold text-[#8b8e93] uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Sesi &amp; Kasir</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Waktu Shift</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Status Kas</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Kas Seharusnya</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Kas Fisik (Aktual)</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Selisih</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">Catatan &amp; Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e6e5e0]">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-[#fcfcfd] transition-colors align-top">
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-bold text-[#111827]">Shift #{{ $session->id }}</div>
                                    <div class="text-[11px] text-[#53565a]">{{ $session->user?->name ?? 'Kasir' }}</div>
                                    <div class="text-[10px] text-[#8b8e93]">{{ $session->register?->name ?? 'Main Register' }}</div>
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                    <div class="text-[#111827] font-medium">
                                        <i class="fas fa-play text-[9px] text-emerald-600 mr-1"></i>{{ $session->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}
                                    </div>
                                    <div class="text-[#53565a] mt-0.5">
                                        <i class="fas fa-flag-checkered text-[9px] text-[#7f837e] mr-1"></i>{{ $session->closed_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i') ?? 'Masih Aktif' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @php
                                        $badgeClass = match ($session->reconciliation_status) {
                                            'BALANCED' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
                                            'SHORT' => 'bg-red-50 border-red-200 text-red-700',
                                            'OVER' => 'bg-amber-50 border-amber-200 text-amber-700',
                                            default => 'bg-gray-100 border-gray-200 text-[#53565a]',
                                        };
                                        $iconClass = match ($session->reconciliation_status) {
                                            'BALANCED' => 'fa-check',
                                            'SHORT' => 'fa-arrow-down',
                                            'OVER' => 'fa-arrow-up',
                                            default => 'fa-hourglass-start',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold {{ $badgeClass }}">
                                        <i class="fas {{ $iconClass }} text-[9px]"></i>
                                        <span>{{ $labels[$session->reconciliation_status] }}</span>
                                    </span>
                                    <span class="block text-[10px] text-[#8b8e93] mt-1">
                                        {{ $session->status === 'CLOSED' ? 'Shift Selesai' : 'Sedang Berlangsung' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap font-semibold text-[#111827]">
                                    @if($session->reconciliation_status !== 'UNCOUNTED')
                                        Rp {{ number_format($session->expected_cash, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 font-normal">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap font-bold text-[#111827]">
                                    @if($session->reconciliation_status !== 'UNCOUNTED')
                                        Rp {{ number_format($session->actual_cash, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400 font-normal">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($session->reconciliation_status !== 'UNCOUNTED')
                                        @php
                                            $diff = (float) $session->difference;
                                        @endphp
                                        <span class="font-bold text-xs {{ $diff < 0 ? 'text-red-600' : ($diff > 0 ? 'text-amber-600' : 'text-emerald-600') }}">
                                            {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-normal">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3.5 min-w-[160px] max-w-xs break-words">
                                    <p class="text-[#53565a] text-xs">{{ $session->notes ?: '—' }}</p>
                                    <button type="button" @click="openDetailModal({{ Illuminate\Support\Js::from([
                                        'id' => $session->id,
                                        'user' => $session->user?->name ?? 'Kasir',
                                        'register' => $session->register?->name ?? 'Main Register',
                                        'opened_at' => $session->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i'),
                                        'closed_at' => $session->closed_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i'),
                                        'status' => $session->status,
                                        'recon_status' => $labels[$session->reconciliation_status],
                                        'opening_cash' => (float) $session->opening_cash,
                                        'cash_sales' => (float) $session->total_cash_sales,
                                        'qris_sales' => (float) $session->total_qris_sales,
                                        'other_sales' => (float) $session->total_other_sales,
                                        'expected_cash' => (float) $session->expected_cash,
                                        'actual_cash' => (float) $session->actual_cash,
                                        'difference' => (float) $session->difference,
                                        'notes' => $session->notes
                                    ]) }})" class="inline-flex items-center gap-1 mt-1 text-[11px] font-semibold text-[#df5826] hover:underline cursor-pointer">
                                        <i class="fas fa-file-invoice text-[10px]"></i>
                                        <span>Lihat Rincian</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-4 text-center">
                                    <div class="w-11 h-11 rounded-[12px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto mb-2.5">
                                        <i class="fas fa-receipt text-sm"></i>
                                    </div>
                                    <p class="text-[13px] font-semibold text-[#1e211f]">Belum ada sesi kasir yang tercatat.</p>
                                    <p class="text-[11.5px] text-[#8b8e93] mt-0.5">Sesi yang ditutup akan otomatis muncul di tabel riwayat ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sessions->hasPages())
                <div class="p-4 border-t border-[#e6e5e0] bg-[#fcfcfd]">
                    {{ $sessions->links() }}
                </div>
            @endif
        </section>
    </main>

    {{-- MODAL DETAIL SESI KASIR (Sparta Style) --}}
    <div x-show="detailModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs font-sans"
         @keydown.escape.window="detailModalOpen = false" x-transition.opacity>
        <div class="relative w-full max-w-md rounded-[24px] bg-white p-6 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.22)] border border-[#e9ebf0] space-y-4" @click.away="detailModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3.5 border-[#f0f0ee]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center text-sm shadow-2xs">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-[#111827]">Rincian Shift #<span x-text="activeDetail.id"></span></h3>
                        <p class="text-[11px] text-[#7f837e]">
                            <span x-text="activeDetail.user"></span> · <span x-text="activeDetail.register"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-[14px] bg-[#fafaf9] border border-[#e9ebf0]">
                    <span class="text-[#7f837e] font-medium">Status Rekonsiliasi:</span>
                    <span class="font-bold px-2.5 py-1 rounded-[8px] bg-white border border-gray-200 shadow-2xs" x-text="activeDetail.recon_status"></span>
                </div>

                <div class="space-y-2 p-3.5 rounded-[14px] border border-[#e9ebf0] bg-[#fafaf9]">
                    <div class="flex justify-between"><span class="text-[#53565a]">Modal Awal:</span><strong class="text-[#111827]" x-text="money(activeDetail.opening_cash)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#53565a]">Penjualan Tunai:</span><strong class="text-emerald-600" x-text="'+ ' + money(activeDetail.cash_sales)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#53565a]">Penjualan QRIS:</span><strong class="text-blue-600" x-text="money(activeDetail.qris_sales)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#53565a]">Penjualan Kartu/Lainnya:</span><strong class="text-[#111827]" x-text="money(activeDetail.other_sales)"></strong></div>
                </div>

                <!-- 3-Way Summary in Modal -->
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2.5 rounded-[12px] bg-white border border-gray-200 shadow-2xs">
                        <span class="text-[10px] text-[#7f837e] block">Seharusnya</span>
                        <strong class="text-xs text-[#111827]" x-text="money(activeDetail.expected_cash)"></strong>
                    </div>
                    <div class="p-2.5 rounded-[12px] bg-white border border-gray-200 shadow-2xs">
                        <span class="text-[10px] text-[#7f837e] block">Fisik (Aktual)</span>
                        <strong class="text-xs text-[#111827]" x-text="money(activeDetail.actual_cash)"></strong>
                    </div>
                    <div class="p-2.5 rounded-[12px] border shadow-2xs"
                         :class="activeDetail.difference === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (activeDetail.difference < 0 ? 'bg-red-50 border-red-200 text-red-800' : 'bg-amber-50 border-amber-200 text-amber-800')">
                        <span class="text-[10px] block font-medium">Selisih</span>
                        <strong class="text-xs" x-text="(activeDetail.difference > 0 ? '+' : '') + money(activeDetail.difference)"></strong>
                    </div>
                </div>

                <template x-if="activeDetail.notes">
                    <div class="p-3 rounded-[12px] bg-amber-50/60 border border-amber-200">
                        <span class="text-[10px] font-bold text-amber-900 block mb-0.5">Catatan Kasir:</span>
                        <p class="text-[11px] text-[#53565a]" x-text="activeDetail.notes"></p>
                    </div>
                </template>

                <div class="pt-2">
                    <button type="button" @click="detailModalOpen = false" class="w-full py-2.5 px-4 rounded-[12px] bg-white border border-[#e9ebf0] hover:bg-gray-50 text-[#111827] text-xs font-bold transition shadow-2xs cursor-pointer">
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>

</div></div>

<script>
function reconciliationRoot() {
    return {
        detailModalOpen: false,
        activeDetail: {
            id: null,
            user: '',
            register: '',
            opened_at: '',
            closed_at: '',
            status: '',
            recon_status: '',
            opening_cash: 0,
            cash_sales: 0,
            qris_sales: 0,
            other_sales: 0,
            expected_cash: 0,
            actual_cash: 0,
            difference: 0,
            notes: ''
        },
        openDetailModal(detail) {
            this.activeDetail = detail;
            this.detailModalOpen = true;
        },
        money(value) {
            if (value === null || value === undefined || isNaN(value)) return 'Rp 0';
            return 'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }
    };
}

function cashReconciliation(expected, initialActual = '') {
    return {
        expected: expected,
        actual: initialActual,
        notes: '',
        submitting: false,
        showDenom: false,
        denom: {
            '100k': null,
            '50k': null,
            '20k': null,
            '10k': null,
            '5k': null,
            '2k': null,
            '1k': null,
            'coins': null
        },

        get actualValid() {
            return this.actual !== '' && this.actual !== null && Number.isFinite(Number(this.actual)) && Number(this.actual) >= 0;
        },

        get difference() {
            if (!this.actualValid) return null;
            return (Math.round(Number(this.actual) * 100) - Math.round(Number(this.expected) * 100)) / 100;
        },

        get label() {
            if (this.difference === null) return 'Belum Dihitung';
            if (this.difference < 0) return 'Kas Kurang';
            if (this.difference > 0) return 'Kas Lebih';
            return 'Kas Sesuai (Cocok)';
        },

        get isNotesRequired() {
            return this.difference !== null && this.difference !== 0;
        },

        setExact() {
            this.actual = this.expected;
        },

        updateFromDenom() {
            let total = 0;
            total += (this.denom['100k'] || 0) * 100000;
            total += (this.denom['50k'] || 0) * 50000;
            total += (this.denom['20k'] || 0) * 20000;
            total += (this.denom['10k'] || 0) * 10000;
            total += (this.denom['5k'] || 0) * 5000;
            total += (this.denom['2k'] || 0) * 2000;
            total += (this.denom['1k'] || 0) * 1000;
            total += (this.denom['coins'] || 0);
            this.actual = total;
        },

        resetDenom() {
            this.denom = { '100k': null, '50k': null, '20k': null, '10k': null, '5k': null, '2k': null, '1k': null, 'coins': null };
        },

        money(value) {
            if (value === null || value === undefined || isNaN(value)) return 'Rp 0';
            return 'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        handleSubmit(e) {
            if (this.isNotesRequired && (!this.notes || this.notes.trim() === '')) {
                e.preventDefault();
                alert('Wajib mengisi catatan ketika terdapat selisih kas kurang atau lebih!');
                return;
            }
            this.submitting = true;
        }
    };
}
</script>
</body>
</html>
