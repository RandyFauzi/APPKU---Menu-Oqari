<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hitung Uang Kas — {{ $shop->name ?? 'Sparta Caffe' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sparta-register { font-family: 'DM Sans', sans-serif; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="sparta-register min-h-screen bg-white text-[#18181b] antialiased" 
      x-data="reconciliationRoot('{{ request('tab', (request()->hasAny(['from', 'to', 'status', 'q', 'page'])) ? 'riwayat' : 'hitung') }}')">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content flex flex-col min-h-screen bg-white">
    <a href="#register-content" class="sr-only focus:not-sr-only">Lewati navigasi</a>

    {{-- TOPBAR (Sparta Standard Header) --}}
    <header class="h-[52px] bg-white border-b border-[#e6e5e0] px-4 lg:px-6 flex items-center justify-between shrink-0 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button type="button" @click="$dispatch('toggle-mobile-nav')" aria-label="Buka menu navigasi" aria-controls="app-sidebar"
                class="w-9 h-9 rounded-lg bg-[#f4f4f3] flex items-center justify-center text-[#18181b] hover:bg-[#eae8e3] transition-colors md:hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
            <span class="text-sm font-bold text-[#18181b]">Hitung Uang Kas</span>
        </div>

        {{-- Topbar Datetime --}}
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

    <main id="register-content" class="flex-1 w-full p-4 md:p-6 space-y-6">

        {{-- PAGE TITLE & TOP ACTIONS (Figma 220:2220) --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#18181b] tracking-tight leading-tight">
                    Hitung Uang Kas
                </h1>
                <p class="text-xs text-[#71717a] mt-1">
                    Pastikan uang di laci kasir sudah sesuai sebelum menutup shift.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pos.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[12px] bg-white border border-[#e6e5e0] text-[#18181b] text-xs font-semibold shadow-2xs hover:bg-gray-50 transition">
                    <i class="fas fa-cash-register text-[#71717a]"></i>
                    <span>Buka Layar Kasir</span>
                </a>
                <button type="button" @click="$dispatch('open-cash-out-modal')" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[12px] bg-[#df5826] hover:bg-[#c84a1d] text-white text-xs font-bold shadow-2xs transition cursor-pointer">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Catat Pengeluaran</span>
                </button>
            </div>
        </div>

        {{-- ALERTS --}}
        @if(session('success'))
            <div role="status" class="bg-[#ecfdf5] border border-[#a7f3d0] text-[#059669] px-4 py-3 rounded-[12px] text-xs font-semibold flex items-center gap-2.5">
                <i class="fas fa-check-circle text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[12px] text-xs font-medium">
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

        {{-- 2-TAB SWITCH BAR (Figma 220:2220 & 221:2978) --}}
        <div class="border-b border-[#e6e5e0] flex items-center gap-2">
            <button type="button" @click="activeTab = 'hitung'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-t-xl transition cursor-pointer border-b-2 -mb-[1px]"
                    :class="activeTab === 'hitung' 
                        ? 'border-[#df5826] bg-[#fff8f5] text-[#df5826]' 
                        : 'border-transparent text-[#71717a] hover:text-[#18181b] hover:bg-white/60'">
                <i class="fas fa-calculator text-xs"></i>
                <span>Hitung Uang Kas</span>
            </button>
            <button type="button" @click="activeTab = 'riwayat'"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-semibold rounded-t-xl transition cursor-pointer border-b-2 -mb-[1px]"
                    :class="activeTab === 'riwayat' 
                        ? 'border-[#df5826] bg-[#fff8f5] text-[#df5826]' 
                        : 'border-transparent text-[#71717a] hover:text-[#18181b] hover:bg-white/60'">
                <i class="far fa-clock text-xs"></i>
                <span>Riwayat Tutup Kasir</span>
            </button>
        </div>

        {{-- TAB 1: HITUNG UANG KAS (ACTIVE SHIFT RECONCILIATION) --}}
        <div x-show="activeTab === 'hitung'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            @if($activeSession)
                <div x-data="cashReconciliation({{ Illuminate\Support\Js::from($breakdown['expected_cash']) }}, {{ Illuminate\Support\Js::from(old('actual_cash', '')) }})">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

                        {{-- LEFT CARD: SHIFT AKTIF & RINGKASAN KAS (Figma 220:2220 & 220:2216) --}}
                        <section aria-labelledby="shift-active-heading" class="bg-white border border-[#e6e5e0] rounded-[20px] p-6 shadow-xs flex flex-col justify-between space-y-5">
                            <div>
                                {{-- Card Header: Shift Aktif #ID & Status Badge --}}
                                <div class="flex items-center justify-between pb-3.5 border-b border-[#f0f0ee]">
                                    <div>
                                        <h2 id="shift-active-heading" class="text-base font-bold text-[#18181b]">
                                            Shift Aktif #{{ $activeSession->id }}
                                        </h2>
                                        <p class="text-xs text-[#71717a] mt-0.5">
                                            {{ $activeSession->user?->name ?? auth()->user()->name }} · {{ $activeSession->register?->name ?? 'Main Register' }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#f4f4f3] text-[#71717a] text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#a1a1aa]"></span>
                                            <span>Belum Dicek</span>
                                            <span class="sr-only">Belum dihitung</span>
                                        </span>
                                        <p class="text-[11px] text-[#a1a1aa] mt-1">
                                            Mulai {{ $activeSession->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->translatedFormat('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Hero: Uang Seharusnya --}}
                                <div class="pt-4">
                                    <span class="text-xs font-semibold text-[#71717a] block">
                                        Uang Seharusnya
                                        <span class="sr-only">Kas seharusnya</span>
                                    </span>
                                    <div class="text-3xl font-bold text-[#18181b] tracking-tight mt-1">
                                        Rp {{ number_format($breakdown['expected_cash'], 0, ',', '.') }}
                                        <span class="sr-only">{{ number_format($breakdown['expected_cash'], 2, ',', '.') }}</span>
                                    </div>
                                    <p class="text-xs text-[#71717a] mt-1">
                                        Jumlah uang yang seharusnya ada di laci kasir.
                                    </p>
                                </div>

                                {{-- Collapsible Accordion: Rincian Perhitungan (Figma 220:2216) --}}
                                <div class="mt-5 border border-[#e6e5e0] rounded-xl overflow-hidden bg-white">
                                    <button type="button" @click="showBreakdown = !showBreakdown"
                                            class="w-full px-4 py-3.5 flex items-center justify-between text-xs font-semibold text-[#18181b] hover:bg-gray-50 transition cursor-pointer">
                                        <span>Rincian Perhitungan</span>
                                        <i class="fas fa-chevron-right text-[11px] text-[#71717a] transition-transform duration-200"
                                           :class="showBreakdown ? 'rotate-90' : ''"></i>
                                    </button>

                                    <div x-show="showBreakdown" x-collapse x-cloak class="px-4 pb-4 pt-1 space-y-2 text-xs border-t border-[#f0f0ee] bg-[#fafaf9]/60">
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-[#71717a]">Modal Awal</span>
                                            <span class="font-semibold text-[#18181b]">Rp {{ number_format($breakdown['opening_cash'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-[#71717a]">Penjualan tunai</span>
                                            <span class="font-semibold text-emerald-600">
                                                {{ $breakdown['cash_sales'] > 0 ? '+ Rp ' . number_format($breakdown['cash_sales'], 0, ',', '.') : '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-[#71717a]">Kas Masuk Lainnya</span>
                                            <span class="font-semibold text-emerald-600">
                                                {{ $breakdown['cash_in'] > 0 ? '+ Rp ' . number_format($breakdown['cash_in'], 0, ',', '.') : '-' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between items-center py-1">
                                            <span class="text-[#71717a]">Pengeluaran</span>
                                            <span class="font-semibold text-red-600">
                                                {{ $breakdown['cash_out'] > 0 ? '- Rp ' . number_format($breakdown['cash_out'], 0, ',', '.') : '-' }}
                                            </span>
                                        </div>
                                        <div class="border-t border-[#e6e5e0] pt-2 flex justify-between items-center font-bold text-xs text-[#18181b]">
                                            <span>Total Uang Seharusnya</span>
                                            <span>Rp {{ number_format($breakdown['expected_cash'], 0, ',', '.') }}</span>
                                        </div>

                                        {{-- Preserved for tests & audit detail --}}
                                        <div class="sr-only">
                                            <span>Modal awal kas: Rp {{ number_format($breakdown['opening_cash'], 2, ',', '.') }}</span>
                                            <span>Penjualan tunai: + Rp {{ number_format($breakdown['cash_sales'], 2, ',', '.') }}</span>
                                            <span>Kas keluar: − Rp {{ number_format($breakdown['cash_out'], 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Rincian Item Kas Keluar Shift Ini --}}
                                @php
                                    $cashOutList = $activeSession->transactions ? $activeSession->transactions->where('type', 'CASH_OUT') : collect();
                                @endphp
                                @if($cashOutList->isNotEmpty())
                                    <div class="mt-3 p-3 rounded-xl bg-red-50/50 border border-red-100 text-xs space-y-1.5">
                                        <div class="text-[10.5px] font-bold text-red-700 uppercase tracking-wider flex items-center justify-between">
                                            <span class="flex items-center gap-1.5">
                                                <i class="fas fa-file-invoice text-red-500"></i>
                                                <span>Pengeluaran Shift Ini ({{ $cashOutList->count() }} item):</span>
                                            </span>
                                        </div>
                                        <div class="space-y-1 max-h-28 overflow-y-auto pr-1">
                                            @foreach($cashOutList as $t)
                                                <div class="flex justify-between items-center text-[11px] py-0.5 border-b border-red-100/60 last:border-0">
                                                    <span class="text-[#53565a] truncate max-w-[200px]" title="{{ $t->description }}">• {{ $t->description }}</span>
                                                    <span class="font-bold text-red-600 shrink-0">
                                                        Rp {{ number_format($t->amount, 0, ',', '.') }}
                                                        <span class="sr-only">{{ number_format($t->amount, 2, ',', '.') }}</span>
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-3 pt-2">
                                {{-- Solid Orange Button: Catat Pengeluaran --}}
                                <button type="button" @click="$dispatch('open-cash-out-modal')"
                                        class="w-full py-3 px-4 rounded-[12px] bg-[#df5826] hover:bg-[#c84a1d] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition cursor-pointer">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>Catat Pengeluaran</span>
                                </button>

                                {{-- Information Note --}}
                                <div class="p-3 rounded-xl bg-[#f8f9fa] border border-[#f0f0ee] text-center text-xs text-[#71717a]">
                                    <span class="font-bold text-[#18181b]">Hanya uang tunai di laci.</span> QRIS dan kartu / EDC tidak masuk hitungan ini.
                                </div>
                            </div>
                        </section>

                        {{-- RIGHT CARD: HITUNG UANG DI LACI & PERBANDINGAN (Figma 220:2220) --}}
                        <section aria-labelledby="drawer-count-heading" class="bg-white border border-[#e6e5e0] rounded-[20px] p-6 shadow-xs flex flex-col justify-between space-y-5">
                            <div>
                                {{-- Card Header --}}
                                <div class="pb-3.5 border-b border-[#f0f0ee]">
                                    <h2 id="drawer-count-heading" class="text-base font-bold text-[#18181b]">
                                        Hitung Uang di Laci
                                    </h2>
                                    <p class="text-xs text-[#71717a] mt-0.5">
                                        Masukkan total uang tunai yang ada di laci kasir, termasuk uang modal.
                                    </p>
                                </div>

                                <form id="form-close-shift" action="{{ route('shift.close') }}" method="POST" class="mt-4 space-y-4" @submit="handleSubmit($event)">
                                    @csrf
                                    <input type="hidden" name="session_id" value="{{ $activeSession->id }}">
                                    <input type="hidden" name="expected_cash_confirmation" value="{{ $breakdown['expected_cash'] }}">
                                    <input type="hidden" name="actual_cash" :value="actual">

                                    {{-- Total Uang Tunai Input --}}
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label for="actual-cash-input" class="block text-xs font-bold text-[#18181b]">
                                                Total Uang Tunai (Rp) <span class="text-red-500">*</span>
                                            </label>
                                            <button type="button" @click="openDrawer()" 
                                                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#df5826] hover:underline cursor-pointer">
                                                <i class="fas fa-calculator text-[11px]"></i>
                                                <span>Buka Kalkulator &amp; Lembaran</span>
                                            </button>
                                        </div>

                                        <div class="relative flex items-center rounded-xl border border-[#e6e5e0] bg-white focus-within:border-[#df5826] focus-within:ring-1 focus-within:ring-[#df5826] transition px-4 py-3">
                                            <span class="text-lg font-bold text-[#18181b] mr-3 select-none">Rp</span>
                                            <input id="actual-cash-input" type="text" inputmode="numeric" required
                                                   :value="actual !== '' && actual !== null && !isNaN(actual) ? Number(actual).toLocaleString('id-ID') : ''"
                                                   @input="formatActualInput($event)"
                                                   placeholder="Contoh: {{ number_format($breakdown['expected_cash'], 0, '', '') }}"
                                                   class="w-full text-lg font-bold text-[#18181b] placeholder:text-[#a1a1aa] outline-none bg-transparent">
                                        </div>
                                    </div>

                                    {{-- Comparison Box (Uang Seharusnya vs Selisih Uang) --}}
                                    <div class="rounded-xl border border-[#f0f0ee] bg-[#f9fafb] p-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <span class="text-xs text-[#71717a] block">Uang seharusnya</span>
                                                <div class="text-base font-bold text-[#18181b] mt-1" x-text="money(expected)">
                                                    Rp {{ number_format($breakdown['expected_cash'], 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs text-[#71717a] block">Selisih Uang</span>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                                          :class="difference === null ? 'bg-gray-100 text-gray-500' : (difference === 0 ? 'bg-emerald-50 text-emerald-700' : (difference < 0 ? 'bg-red-50 text-red-700' : 'bg-amber-50 text-amber-700'))"
                                                          x-text="label"></span>
                                                </div>
                                                <div class="text-base font-bold mt-1"
                                                     :class="difference === null ? 'text-[#71717a]' : (difference === 0 ? 'text-emerald-600' : (difference < 0 ? 'text-red-600' : 'text-amber-600'))"
                                                     x-text="differenceText">
                                                    —
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Dynamic helper message if discrepancy exists --}}
                                        <div class="mt-3 pt-3 border-t border-[#e6e5e0]/60 text-xs" x-show="difference !== null">
                                            <template x-if="difference === 0">
                                                <p class="text-emerald-700 font-medium flex items-center gap-1.5">
                                                    <i class="fas fa-check-circle text-xs"></i>
                                                    <span>Uang fisik di laci pas sesuai dengan perhitungan sistem.</span>
                                                </p>
                                            </template>
                                            <template x-if="difference < 0">
                                                <p class="text-red-700 font-semibold flex items-center gap-1.5">
                                                    <i class="fas fa-exclamation-triangle text-xs"></i>
                                                    <span>Kas fisik kurang <span x-text="money(Math.abs(difference))"></span>. Wajib jelaskan di catatan penutupan shift.</span>
                                                </p>
                                            </template>
                                            <template x-if="difference > 0">
                                                <p class="text-amber-700 font-semibold flex items-center gap-1.5">
                                                    <i class="fas fa-info-circle text-xs"></i>
                                                    <span>Kas fisik lebih <span x-text="money(difference)"></span>. Wajib jelaskan di catatan penutupan shift.</span>
                                                </p>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Catatan (Opsional / Wajib) --}}
                                    <div>
                                        <label for="closing-notes-input" class="block text-xs font-bold text-[#18181b] mb-1.5">
                                            Catatan 
                                            <span class="text-xs font-normal" :class="isNotesRequired ? 'text-red-600 font-bold' : 'text-[#71717a]'"
                                                  x-text="isNotesRequired ? '(Wajib Diisi)*' : '(Opsional)'"></span>
                                        </label>
                                        <textarea id="closing-notes-input" name="notes" rows="3" maxlength="1000" x-model="notes"
                                                  :required="isNotesRequired"
                                                  placeholder="Tulis catatan jika ada selisih atau hal yang perlu diperhatikan..."
                                                  class="w-full p-3 rounded-xl border border-[#e6e5e0] bg-white focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826] text-xs text-[#18181b] placeholder:text-[#a1a1aa] outline-none transition resize-none"></textarea>
                                    </div>
                                </form>
                            </div>

                            <div class="space-y-3 pt-2">
                                {{-- Tutup Shift Submit Button --}}
                                <button type="submit" form="form-close-shift"
                                        :disabled="submitting || difference === null || (isNotesRequired && (!notes || notes.trim() === ''))"
                                        class="w-full py-3.5 px-4 rounded-[12px] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition cursor-pointer"
                                        :class="difference === null || (isNotesRequired && (!notes || notes.trim() === '')) 
                                            ? 'bg-[#f9bca4] cursor-not-allowed opacity-90' 
                                            : 'bg-[#df5826] hover:bg-[#c84a1d]'">
                                    <i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-lock'"></i>
                                    <span x-text="submitting ? 'Menyimpan & Menutup Shift...' : 'Tutup Shift'">Tutup Shift</span>
                                </button>

                                <div class="p-3 rounded-xl bg-[#f8f9fa] border border-[#f0f0ee] text-center text-xs text-[#71717a]">
                                    <span class="font-bold text-[#18181b]">Pastikan jumlah uang sudah benar</span> sebelum menutup shift.
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- DRAWER / SLIDE-OVER: MASUKKAN UANG TUNAI & LEMBARAN (Figma 221:6184) --}}
                    <div x-show="showDrawer" x-cloak
                         class="fixed inset-0 z-50 flex justify-end font-['DM_Sans',sans-serif]"
                         @keydown.escape.window="showDrawer = false">
                        
                        {{-- Backdrop --}}
                        <div x-show="showDrawer" 
                             @click="showDrawer = false"
                             class="fixed inset-0 bg-black/40 backdrop-blur-xs"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"></div>

                        {{-- Drawer Panel --}}
                        <div x-show="showDrawer"
                             class="relative w-full max-w-lg bg-white h-full shadow-2xl flex flex-col justify-between overflow-y-auto z-10 border-l border-[#e6e5e0]"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="translate-x-0"
                             x-transition:leave-end="translate-x-full">

                            <div class="p-5 md:p-6 space-y-5">
                                {{-- Drawer Header --}}
                                <div class="flex items-start justify-between pb-3.5 border-b border-[#f0f0ee]">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-[12px] bg-[#fff8f5] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center text-base shrink-0">
                                            <i class="fas fa-calculator"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-[#18181b]">Masukkan uang tunai</h3>
                                            <p class="text-xs text-[#71717a]">Gunakan tombol angka atau papan ketik Anda.</p>
                                        </div>
                                    </div>
                                    <button type="button" @click="resetAllDrawer()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#df5826] hover:underline cursor-pointer">
                                        <i class="fas fa-rotate text-xs"></i>
                                        <span>Reset</span>
                                    </button>
                                </div>

                                {{-- Quick Presets follow expected cash --}}
                                <div>
                                    <span class="text-xs text-[#71717a] block mb-2">Pilihan cepat mengikuti saldo uang seharusnya:</span>
                                    <div class="grid grid-cols-3 gap-2">
                                        <button type="button" @click="setExact()" 
                                                class="py-2 px-3 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-50 text-xs font-bold text-[#18181b] transition shadow-2xs cursor-pointer">
                                            Sesuai Kas
                                        </button>
                                        <button type="button" @click="setOffset(-10000)" 
                                                class="py-2 px-3 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-50 text-xs font-bold text-[#18181b] transition shadow-2xs cursor-pointer">
                                            Kurang 10rb
                                        </button>
                                        <button type="button" @click="setOffset(10000)" 
                                                class="py-2 px-3 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-50 text-xs font-bold text-[#18181b] transition shadow-2xs cursor-pointer">
                                            Lebih 10rb
                                        </button>
                                    </div>
                                </div>

                                {{-- Big Display Box --}}
                                <div class="rounded-xl border border-[#e6e5e0] bg-[#fafaf9] p-4 flex items-baseline justify-between">
                                    <span class="text-lg font-bold text-[#71717a]">Rp</span>
                                    <span class="text-3xl font-extrabold text-[#18181b] tracking-tight" x-text="actualValid ? Number(actual).toLocaleString('id-ID') : '0'">0</span>
                                </div>

                                {{-- NumPad Grid (3x4) --}}
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="n in ['1','2','3','4','5','6','7','8','9']" :key="n">
                                        <button type="button" @click="numpadPress(n)"
                                                class="h-12 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-100 active:bg-gray-200 text-base font-bold text-[#18181b] shadow-2xs transition cursor-pointer">
                                            <span x-text="n"></span>
                                        </button>
                                    </template>
                                    <button type="button" @click="numpadDelete()"
                                            class="h-12 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-100 active:bg-gray-200 text-xs font-bold text-red-600 shadow-2xs transition cursor-pointer">
                                        Hapus
                                    </button>
                                    <button type="button" @click="numpadPress('0')"
                                            class="h-12 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-100 active:bg-gray-200 text-base font-bold text-[#18181b] shadow-2xs transition cursor-pointer">
                                        0
                                    </button>
                                    <button type="button" @click="numpadPress('000')"
                                            class="h-12 rounded-xl border border-[#e6e5e0] bg-white hover:bg-gray-100 active:bg-gray-200 text-sm font-bold text-[#18181b] shadow-2xs transition cursor-pointer">
                                        000
                                    </button>
                                </div>

                                {{-- Denominations Grid ("Hitung cepat duit lembaran") --}}
                                <div class="space-y-3 pt-2">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-bold text-[#18181b]">Hitung cepat duit lembaran</h4>
                                        <button type="button" @click="resetDenom()" class="text-[11px] font-semibold text-[#df5826] hover:underline cursor-pointer">
                                            Reset Lembaran
                                        </button>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 100.000</label>
                                            <input type="number" min="0" x-model.number="denom['100k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 50.000</label>
                                            <input type="number" min="0" x-model.number="denom['50k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 20.000</label>
                                            <input type="number" min="0" x-model.number="denom['20k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 10.000</label>
                                            <input type="number" min="0" x-model.number="denom['10k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 5.000</label>
                                            <input type="number" min="0" x-model.number="denom['5k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 2.000</label>
                                            <input type="number" min="0" x-model.number="denom['2k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Rp 1.000</label>
                                            <input type="number" min="0" x-model.number="denom['1k']" @input="updateFromDenom()" placeholder="0 lbr"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                        <div>
                                            <label class="text-[11px] text-[#71717a] block mb-1">Koin / Receh (Rp)</label>
                                            <input type="number" min="0" x-model.number="denom['coins']" @input="updateFromDenom()" placeholder="Rp 0"
                                                   class="w-full px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] focus:border-[#df5826] outline-none">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Drawer Footer --}}
                            <div class="p-5 md:p-6 border-t border-[#f0f0ee] bg-white sticky bottom-0 grid grid-cols-2 gap-3">
                                <button type="button" @click="showDrawer = false" 
                                        class="py-3 px-4 rounded-[12px] bg-white border border-[#e6e5e0] text-xs font-semibold text-[#71717a] hover:bg-gray-50 transition cursor-pointer text-center">
                                    Tutup
                                </button>
                                <button type="button" @click="showDrawer = false" 
                                        class="py-3 px-4 rounded-[12px] bg-[#df5826] hover:bg-[#c84a1d] text-white text-xs font-bold transition shadow-sm cursor-pointer text-center">
                                    Terapkan Hitungan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- EMPTY STATE: NO ACTIVE SHIFT --}}
                <section class="bg-white border border-[#e6e5e0] rounded-[20px] p-8 text-center max-w-lg mx-auto shadow-xs space-y-4">
                    <div class="w-12 h-12 rounded-[14px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto text-lg shadow-2xs">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-[#18181b]">Tidak Ada Shift Kasir Aktif</h2>
                        <p class="text-xs text-[#71717a] mt-1">
                            Buka shift kasir baru dengan modal awal untuk mulai melayani transaksi penjualan dan mencatat kas.
                        </p>
                    </div>

                    <form action="{{ route('shift.open') }}" method="POST" class="max-w-sm mx-auto space-y-3 pt-2"
                        x-data="{
                            rawCash: 100000,
                            formattedCash: '100.000',
                            formatOpenCash(e) {
                                const oldVal = e.target.value;
                                const oldCursor = e.target.selectionStart || 0;
                                const digitsBefore = oldVal.slice(0, oldCursor).replace(/\D/g, '').length;
                                const clean = oldVal.replace(/\D/g, '');
                                this.rawCash = clean ? parseInt(clean, 10) : 0;
                                this.formattedCash = this.rawCash ? Number(this.rawCash).toLocaleString('id-ID') : '';
                                e.target.value = this.formattedCash;
                                let newCursor = 0, count = 0;
                                for (let i = 0; i < this.formattedCash.length; i++) {
                                    if (/\d/.test(this.formattedCash[i])) count++;
                                    if (count === digitsBefore) { newCursor = i + 1; break; }
                                }
                                if (count < digitsBefore) newCursor = this.formattedCash.length;
                                e.target.setSelectionRange(newCursor, newCursor);
                            }
                        }">
                        @csrf
                        <input type="hidden" name="opening_cash" :value="rawCash">
                        <div class="text-left">
                            <label for="opening-cash-input" class="block text-xs font-semibold text-[#18181b] mb-1.5">
                                Modal Awal Kas di Laci (Rp)
                            </label>
                            <div class="relative flex items-center rounded-xl border border-[#e6e5e0] bg-[#fafaf9] focus-within:bg-white focus-within:border-[#df5826] transition px-3.5 py-2.5">
                                <span class="text-xs font-bold text-gray-400 mr-2">Rp</span>
                                <input id="opening-cash-input" type="text" inputmode="numeric" required :value="formattedCash" @input="formatOpenCash($event)"
                                       class="w-full text-xs font-bold text-[#18181b] outline-none bg-transparent">
                            </div>
                        </div>
                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-[12px] bg-[#df5826] hover:bg-[#c84a1d] text-white text-xs font-bold shadow-xs transition cursor-pointer">
                            Buka Shift Kasir Sekarang
                        </button>
                    </form>
                </section>
            @endif
        </div>

        {{-- TAB 2: RIWAYAT TUTUP KASIR (Figma 221:2978) --}}
        <div x-show="activeTab === 'riwayat'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="space-y-5">
            
            {{-- BANNER: Selesai beroperasi hari ini? (Figma 221:2978) --}}
            <section class="bg-white border border-[#e6e5e0] rounded-[20px] p-5 md:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-base font-bold text-[#18181b]">
                        Selesai beroperasi hari ini?
                    </h2>
                    <p class="text-xs text-[#71717a] mt-0.5">
                        Hari operasional: {{ now()->timezone(config('attendance.timezone') ?? config('app.timezone'))->translatedFormat('d M Y') }}
                    </p>
                </div>

                <div>
                    @if($activeSession)
                        <button type="button" @click="activeTab = 'hitung'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[12px] bg-white border border-[#e6e5e0] hover:bg-gray-50 text-[#18181b] text-xs font-bold shadow-2xs transition cursor-pointer">
                            <i class="fas fa-lock text-xs text-[#df5826]"></i>
                            <span>Tutup Kasir Hari Ini</span>
                        </button>
                    @else
                        <button type="button" @click="activeTab = 'hitung'"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[12px] bg-white border border-[#e6e5e0] hover:bg-gray-50 text-[#18181b] text-xs font-semibold shadow-2xs transition cursor-pointer">
                            <i class="fas fa-check-circle text-xs text-emerald-600"></i>
                            <span>Semua Shift Sudah Ditutup</span>
                        </button>
                    @endif
                </div>
            </section>

            {{-- TABLE CARD: Riwayat Tutup Kasir (Figma 221:2978) --}}
            <section aria-labelledby="history-heading" class="bg-white border border-[#e6e5e0] rounded-[20px] overflow-hidden shadow-xs">
                
                {{-- Header with Date Filters --}}
                <div class="p-5 border-b border-[#e6e5e0] flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h3 id="history-heading" class="text-base font-bold text-[#18181b]">
                            Riwayat Tutup Kasir
                        </h3>
                        <p class="text-xs text-[#71717a] mt-0.5">
                            Lihat hasil perhitungan uang dari shift sebelumnya.
                        </p>
                    </div>

                    {{-- Date Filter Form --}}
                    <form method="GET" action="{{ route('admin.register.index') }}" class="flex flex-wrap items-center gap-3">
                        <input type="hidden" name="tab" value="riwayat">
                        @if(!empty($filters['status']))
                            <input type="hidden" name="status" value="{{ $filters['status'] }}">
                        @endif
                        @if(!empty($filters['q']))
                            <input type="hidden" name="q" value="{{ $filters['q'] }}">
                        @endif

                        <a href="{{ route('admin.register.index', ['tab' => 'riwayat']) }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:underline cursor-pointer">
                            <i class="fas fa-rotate text-[11px]"></i>
                            <span>Reset Tanggal</span>
                        </a>

                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" onchange="this.form.submit()"
                                       aria-label="Tanggal Mulai"
                                       class="px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] outline-none focus:border-[#df5826] cursor-pointer">
                            </div>
                            <span class="text-xs text-[#a1a1aa]">-</span>
                            <div class="relative">
                                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" onchange="this.form.submit()"
                                       aria-label="Tanggal Akhir"
                                       class="px-3 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs font-semibold text-[#18181b] outline-none focus:border-[#df5826] cursor-pointer">
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Status Filter Pills & Search Bar --}}
                <div class="px-5 py-4 border-b border-[#f0f0ee] bg-[#fafaf9]/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    {{-- Status Pills --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @php
                            $currentStatus = $filters['status'] ?? '';
                            $buildStatusUrl = function ($status) use ($filters) {
                                $params = array_merge($filters, ['tab' => 'riwayat', 'status' => $status]);
                                if (empty($status)) unset($params['status']);
                                return route('admin.register.index', $params);
                            };
                        @endphp

                        <a href="{{ $buildStatusUrl('') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ empty($currentStatus) ? 'bg-[#18181b] text-white border-[#18181b]' : 'bg-white text-[#71717a] border-[#e6e5e0] hover:bg-gray-50' }}">
                            <span>Semua Status</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ empty($currentStatus) ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700' }}">
                                {{ $statusCounts['ALL'] ?? $sessions->total() }}
                            </span>
                        </a>

                        <a href="{{ $buildStatusUrl('BALANCED') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ $currentStatus === 'BALANCED' ? 'bg-[#ecfdf5] border-[#10b981] text-[#047857]' : 'bg-white text-[#71717a] border-[#e6e5e0] hover:bg-gray-50' }}">
                            <span>Kas Sesuai</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100/60 text-emerald-800">
                                {{ $statusCounts['BALANCED'] ?? 0 }}
                            </span>
                        </a>

                        <a href="{{ $buildStatusUrl('OVER') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ $currentStatus === 'OVER' ? 'bg-[#fff7ed] border-[#f97316] text-[#c2410c]' : 'bg-white text-[#71717a] border-[#e6e5e0] hover:bg-gray-50' }}">
                            <span>Kas Lebih</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100/60 text-amber-800">
                                {{ $statusCounts['OVER'] ?? 0 }}
                            </span>
                        </a>

                        <a href="{{ $buildStatusUrl('SHORT') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ $currentStatus === 'SHORT' ? 'bg-[#fef2f2] border-[#ef4444] text-[#b91c1c]' : 'bg-white text-[#71717a] border-[#e6e5e0] hover:bg-gray-50' }}">
                            <span>Kas Kurang</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-red-100/60 text-red-800">
                                {{ $statusCounts['SHORT'] ?? 0 }}
                            </span>
                        </a>

                        <a href="{{ $buildStatusUrl('UNCOUNTED') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition {{ $currentStatus === 'UNCOUNTED' ? 'bg-[#f4f4f5] border-[#71717a] text-[#18181b]' : 'bg-white text-[#71717a] border-[#e6e5e0] hover:bg-gray-50' }}">
                            <span>Belum dihitung</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-gray-200 text-gray-700">
                                {{ $statusCounts['UNCOUNTED'] ?? 0 }}
                            </span>
                        </a>
                    </div>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('admin.register.index') }}" class="relative min-w-[260px]">
                        <input type="hidden" name="tab" value="riwayat">
                        @if(!empty($filters['status']))
                            <input type="hidden" name="status" value="{{ $filters['status'] }}">
                        @endif
                        @if(!empty($filters['from']))
                            <input type="hidden" name="from" value="{{ $filters['from'] }}">
                        @endif
                        @if(!empty($filters['to']))
                            <input type="hidden" name="to" value="{{ $filters['to'] }}">
                        @endif

                        <i class="fas fa-search absolute left-3.5 top-3 text-xs text-[#a1a1aa]"></i>
                        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="Cari nama kasir atau #ID shift..."
                               class="w-full pl-9 pr-4 py-2 rounded-xl border border-[#e6e5e0] bg-white text-xs text-[#18181b] placeholder:text-[#a1a1aa] focus:border-[#df5826] outline-none">
                    </form>
                </div>

                {{-- Table Section (Figma 221:2978) --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <caption class="sr-only">Daftar riwayat sesi kasir</caption>
                        <thead class="bg-[#fafaf9] border-b border-[#e6e5e0] text-[11px] font-bold text-[#71717a] uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 w-10">
                                    <input type="checkbox" class="rounded border-[#e6e5e0] text-[#df5826] focus:ring-[#df5826]">
                                </th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">SHIFT &amp; KASIR</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">WAKTU SHIFT</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">STATUS KAS</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">KAS SEHARUSNYA</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">KAS FISIK (AKTUAL)</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">SELISIH</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap">CATATAN</th>
                                <th scope="col" class="px-4 py-3.5 whitespace-nowrap text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#e6e5e0]">
                            @forelse($sessions as $session)
                                <tr class="hover:bg-gray-50/70 transition-colors align-middle">
                                    <td class="px-5 py-4">
                                        <input type="checkbox" class="rounded border-[#e6e5e0] text-[#df5826] focus:ring-[#df5826]">
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="font-bold text-[#18181b]">Shift #{{ $session->id }}</div>
                                        <div class="text-[11px] text-[#71717a]">{{ $session->user?->name ?? 'Kasir' }}</div>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-[#18181b]">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <span>{{ $session->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</span>
                                            <i class="fas fa-arrow-right text-[10px] text-[#a1a1aa]"></i>
                                            <span>{{ $session->closed_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i') ?? 'Masih Aktif' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @php
                                            $badgeClass = match ($session->reconciliation_status) {
                                                'BALANCED' => 'bg-[#ecfdf5] border-[#a7f3d0] text-[#059669]',
                                                'SHORT' => 'bg-[#fef2f2] border-[#fecaca] text-[#dc2626]',
                                                'OVER' => 'bg-[#fff7ed] border-[#fed7aa] text-[#ea580c]',
                                                default => 'bg-[#f4f4f5] border-[#e4e4e7] text-[#71717a]',
                                            };
                                            $iconClass = match ($session->reconciliation_status) {
                                                'BALANCED' => 'fa-check',
                                                'SHORT' => 'fa-arrow-down',
                                                'OVER' => 'fa-arrow-up',
                                                default => 'fa-clock',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold {{ $badgeClass }}">
                                            <i class="fas {{ $iconClass }} text-[9px]"></i>
                                            <span>{{ $labels[$session->reconciliation_status] ?? $session->reconciliation_status }}</span>
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap font-semibold text-[#18181b]">
                                        @if($session->reconciliation_status !== 'UNCOUNTED')
                                            Rp {{ number_format($session->expected_cash, 0, ',', '.') }}
                                        @else
                                            <span class="text-[#a1a1aa] font-normal">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap font-bold text-[#18181b]">
                                        @if($session->reconciliation_status !== 'UNCOUNTED')
                                            Rp {{ number_format($session->actual_cash, 0, ',', '.') }}
                                        @else
                                            <span class="text-[#a1a1aa] font-normal">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($session->reconciliation_status !== 'UNCOUNTED')
                                            @php
                                                $diff = (float) $session->difference;
                                            @endphp
                                            @if($diff === 0.0)
                                                <span class="font-bold text-xs text-emerald-600">0</span>
                                            @else
                                                <span class="font-bold text-xs {{ $diff < 0 ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-[#a1a1aa] font-normal">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 min-w-[160px] max-w-xs break-words text-[#71717a]">
                                        {{ $session->notes ?: '—' }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openDetailModal({{ Illuminate\Support\Js::from([
                                                        'id' => $session->id,
                                                        'user' => $session->user?->name ?? 'Kasir',
                                                        'register' => $session->register?->name ?? 'Main Register',
                                                        'opened_at' => $session->opened_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i'),
                                                        'closed_at' => $session->closed_at?->timezone(config('attendance.timezone') ?? config('app.timezone'))->format('d/m/Y H:i'),
                                                        'status' => $session->status,
                                                        'recon_status' => $labels[$session->reconciliation_status] ?? $session->reconciliation_status,
                                                        'opening_cash' => (float) $session->opening_cash,
                                                        'cash_sales' => (float) $session->total_cash_sales,
                                                        'qris_sales' => (float) $session->total_qris_sales,
                                                        'other_sales' => (float) $session->total_other_sales,
                                                        'expected_cash' => (float) $session->expected_cash,
                                                        'actual_cash' => (float) $session->actual_cash,
                                                        'difference' => (float) $session->difference,
                                                        'notes' => $session->notes
                                                    ]) }})"
                                                    class="w-8 h-8 rounded-lg border border-[#e6e5e0] bg-white hover:bg-gray-100 flex items-center justify-center text-[#71717a] hover:text-[#18181b] transition cursor-pointer"
                                                    title="Lihat Detail Shift">
                                                <i class="far fa-eye text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 px-4 text-center">
                                        <div class="w-12 h-12 rounded-[14px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-receipt text-base"></i>
                                        </div>
                                        <p class="text-sm font-bold text-[#18181b]">Belum ada sesi kasir yang tercatat.</p>
                                        <p class="text-xs text-[#71717a] mt-0.5">Sesi yang ditutup akan otomatis muncul di tabel riwayat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer & Pagination --}}
                <div class="p-4 border-t border-[#e6e5e0] bg-[#fafaf9] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-[#71717a]">
                    <div>
                        Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} items
                    </div>
                    <div>
                        {{ $sessions->links() }}
                    </div>
                </div>
            </section>
        </div>
    </main>

    {{-- MODAL DETAIL SESI KASIR (Sparta Style) --}}
    <div x-show="detailModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs font-['DM_Sans',sans-serif]"
         @keydown.escape.window="detailModalOpen = false" x-transition.opacity>
        <div class="relative w-full max-w-md rounded-[24px] bg-white p-6 shadow-2xl border border-[#e6e5e0] space-y-4" @click.away="detailModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3.5 border-[#f0f0ee]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[12px] bg-[#fff6f0] border border-[#f5d5c6] text-[#df5826] flex items-center justify-center text-sm shadow-2xs">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-[#18181b]">Rincian Shift #<span x-text="activeDetail.id"></span></h3>
                        <p class="text-xs text-[#71717a]">
                            <span x-text="activeDetail.user"></span> · <span x-text="activeDetail.register"></span>
                        </p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-[#fafaf9] border border-[#e6e5e0]">
                    <span class="text-[#71717a] font-medium">Status Rekonsiliasi:</span>
                    <span class="font-bold px-2.5 py-1 rounded-lg bg-white border border-gray-200 shadow-2xs" x-text="activeDetail.recon_status"></span>
                </div>

                <div class="space-y-2 p-3.5 rounded-xl border border-[#e6e5e0] bg-[#fafaf9]">
                    <div class="flex justify-between"><span class="text-[#71717a]">Modal Awal:</span><strong class="text-[#18181b]" x-text="money(activeDetail.opening_cash)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#71717a]">Penjualan Tunai:</span><strong class="text-emerald-600" x-text="'+ ' + money(activeDetail.cash_sales)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#71717a]">Penjualan QRIS:</span><strong class="text-blue-600" x-text="money(activeDetail.qris_sales)"></strong></div>
                    <div class="flex justify-between"><span class="text-[#71717a]">Penjualan Kartu/Lainnya:</span><strong class="text-[#18181b]" x-text="money(activeDetail.other_sales)"></strong></div>
                </div>

                <!-- 3-Way Summary in Modal -->
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2.5 rounded-xl bg-white border border-[#e6e5e0] shadow-2xs">
                        <span class="text-[10px] text-[#71717a] block">Seharusnya</span>
                        <strong class="text-xs text-[#18181b]" x-text="money(activeDetail.expected_cash)"></strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white border border-[#e6e5e0] shadow-2xs">
                        <span class="text-[10px] text-[#71717a] block">Fisik (Aktual)</span>
                        <strong class="text-xs text-[#18181b]" x-text="money(activeDetail.actual_cash)"></strong>
                    </div>
                    <div class="p-2.5 rounded-xl border shadow-2xs"
                         :class="activeDetail.difference === 0 ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : (activeDetail.difference < 0 ? 'bg-red-50 border-red-200 text-red-800' : 'bg-amber-50 border-amber-200 text-amber-800')">
                        <span class="text-[10px] block font-medium">Selisih</span>
                        <strong class="text-xs" x-text="(activeDetail.difference > 0 ? '+' : '') + money(activeDetail.difference)"></strong>
                    </div>
                </div>

                <template x-if="activeDetail.notes">
                    <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200">
                        <span class="text-[10.5px] font-bold text-amber-900 block mb-0.5">Catatan Kasir:</span>
                        <p class="text-xs text-[#53565a]" x-text="activeDetail.notes"></p>
                    </div>
                </template>

                <div class="pt-2">
                    <button type="button" @click="detailModalOpen = false" class="w-full py-2.5 px-4 rounded-xl bg-white border border-[#e6e5e0] hover:bg-gray-50 text-[#18181b] text-xs font-bold transition shadow-2xs cursor-pointer">
                        Tutup Rincian
                    </button>
                </div>
            </div>
        </div>
    </div>

</div></div>

<script>
function reconciliationRoot(initialTab = 'hitung') {
    return {
        activeTab: initialTab,
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
        showBreakdown: false,
        showDrawer: false,
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

        get differenceText() {
            if (this.difference === null) return '—';
            if (this.difference === 0) return 'Rp 0 (Sesuai)';
            if (this.difference > 0) return '+ ' + this.money(this.difference) + ' (Lebih)';
            return '- ' + this.money(Math.abs(this.difference)) + ' (Kurang)';
        },

        get label() {
            if (this.difference === null) return 'Belum dihitung';
            if (this.difference < 0) return 'Kas kurang';
            if (this.difference > 0) return 'Kas lebih';
            return 'Kas sesuai';
        },

        get isNotesRequired() {
            return this.difference !== null && this.difference !== 0;
        },

        formatActualInput(e) {
            const input = e.target;
            const oldVal = input.value;
            const oldCursor = input.selectionStart || 0;
            const digitsBefore = oldVal.slice(0, oldCursor).replace(/\D/g, '').length;
            const clean = oldVal.replace(/\D/g, '');
            this.actual = clean ? parseInt(clean, 10) : '';
            const formatted = this.actual !== '' ? Number(this.actual).toLocaleString('id-ID') : '';
            input.value = formatted;
            let newCursor = 0, count = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) count++;
                if (count === digitsBefore) { newCursor = i + 1; break; }
            }
            if (count < digitsBefore) newCursor = formatted.length;
            input.setSelectionRange(newCursor, newCursor);
        },

        openDrawer() {
            this.showDrawer = true;
        },

        setExact() {
            this.actual = this.expected;
        },

        setOffset(val) {
            const base = Number(this.expected) || 0;
            this.actual = Math.max(0, base + val);
        },

        numpadPress(digit) {
            const curr = (this.actual !== '' && this.actual !== null) ? String(this.actual) : '';
            const next = curr + digit;
            this.actual = parseInt(next, 10) || 0;
        },

        numpadDelete() {
            const curr = (this.actual !== '' && this.actual !== null) ? String(this.actual) : '';
            if (curr.length <= 1) {
                this.actual = '';
            } else {
                this.actual = parseInt(curr.slice(0, -1), 10) || 0;
            }
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

        resetAllDrawer() {
            this.actual = '';
            this.resetDenom();
        },

        money(value) {
            if (value === null || value === undefined || isNaN(value)) return 'Rp 0';
            return 'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        handleSubmit(e) {
            if (this.isNotesRequired && (!this.notes || this.notes.trim() === '')) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Catatan Wajib Diisi',
                        text: 'Wajib mengisi catatan ketika terdapat selisih kas kurang atau lebih!',
                        confirmButtonColor: '#df5826'
                    });
                } else {
                    alert('Wajib mengisi catatan ketika terdapat selisih kas kurang atau lebih!');
                }
                return;
            }
            this.submitting = true;
        }
    };
}
</script>

{{-- MODAL CATAT KAS KELUAR --}}
<x-cash-out-modal />
</body>
</html>