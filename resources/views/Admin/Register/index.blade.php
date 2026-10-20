<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekonsiliasi Kas — OQARI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: var(--oq-ivory); color: var(--oq-ink); }
        [x-cloak] { display: none !important; }
        .cash-button { min-height: 44px; padding: .75rem 1rem; border-radius: .75rem; font-weight: 700; }
        :is(a,button,input,select,textarea):focus-visible { outline: 3px solid var(--oq-gold); outline-offset: 3px; }
    </style>
    <x-theme-head :shop="$shop ?? null" />
</head>
<body class="min-h-screen font-sans">
<div class="cafe-page-shell"><x-cafe-sidebar /><div class="cafe-page-content bg-ivory">
    <div class="oq-frieze fixed inset-x-0 top-0 z-[200] pointer-events-none" aria-hidden="true"></div>
    @php($labels = ['BALANCED' => 'Kas sesuai', 'SHORT' => 'Kas kurang', 'OVER' => 'Kas lebih', 'UNCOUNTED' => 'Belum dihitung'])
    <main class="max-w-7xl mx-auto p-4 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-primary">Rekonsiliasi Kas</h1>
        <p class="text-sm text-gray-600 mt-2">Periksa kas fisik selama shift dan simpan hasil akhir saat menutup kasir.</p>
        @if(session('success'))<p role="status" class="mt-4 p-4 bg-green-50 text-green-900 rounded-xl border border-green-200">{{ session('success') }}</p>@endif
        @if($errors->any())<div role="alert" class="mt-4 p-4 bg-red-50 text-red-900 rounded-xl border border-red-200"><p class="font-bold">Penutupan belum disimpan</p><ul class="list-disc pl-5 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @if($activeSession)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <section aria-labelledby="summary-heading" class="bg-white border border-line p-5 rounded-2xl">
                    <div class="flex flex-wrap justify-between gap-2"><h2 id="summary-heading" class="font-bold text-lg">Shift Aktif #{{ $activeSession->id }}</h2><span class="text-sm font-bold text-gold-600">Belum dihitung</span></div>
                    <p class="text-sm text-gray-600 mt-2">{{ auth()->user()->name }} · {{ $activeSession->register->name }}<br>Dibuka {{ $activeSession->opened_at?->format('d/m/Y H:i') }} ({{ config('app.timezone') }})</p>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><dt>Modal awal</dt><dd>Rp {{ number_format($breakdown['opening_cash'], 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between gap-3"><dt>Penjualan tunai</dt><dd>+ Rp {{ number_format($breakdown['cash_sales'], 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between gap-3"><dt>Kas masuk lainnya</dt><dd>+ Rp {{ number_format($breakdown['cash_in'], 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between gap-3"><dt>Kas keluar</dt><dd>− Rp {{ number_format($breakdown['cash_out'], 2, ',', '.') }}</dd></div>
                        <div class="flex justify-between gap-3 border-t border-line pt-4 text-base font-bold"><dt>Kas seharusnya</dt><dd>Rp {{ number_format($breakdown['expected_cash'], 2, ',', '.') }}</dd></div>
                    </dl>
                    <p class="mt-4 text-xs text-gray-600">Kas seharusnya = modal awal + penjualan tunai + kas masuk − kas keluar. Mutasi yang dibatalkan tidak dihitung.</p>
                    <div class="mt-5 p-4 bg-ivory rounded-xl text-sm"><h3 class="font-bold">Pembayaran nontunai (di luar laci kas)</h3><p class="mt-2">QRIS: Rp {{ number_format($activeSession->total_qris_sales, 2, ',', '.') }}<br>Kartu/lainnya: Rp {{ number_format($activeSession->total_other_sales, 2, ',', '.') }}</p></div>
                    <a href="{{ route('admin.register.index') }}" class="inline-block cash-button border border-gold-400 text-primary mt-4">Perbarui Angka Kas</a>
                    <p class="text-xs text-gray-600 mt-2">Angka berlaku saat halaman dibuka. Selesaikan transaksi sebelum hitung akhir dan tutup shift.</p>
                </section>
                <section aria-labelledby="count-heading" class="bg-white border border-line p-5 rounded-2xl" x-data="cashReconciliation({{ Illuminate\Support\Js::from($breakdown['expected_cash']) }}, {{ Illuminate\Support\Js::from(old('actual_cash', '')) }})">
                    <h2 id="count-heading" class="font-bold text-lg">Hitung Fisik &amp; Tutup Shift</h2>
                    <form action="{{ route('shift.close') }}" method="POST" class="mt-4 space-y-4" @submit="submitting = true">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $activeSession->id }}">
                        <input type="hidden" name="expected_cash_confirmation" value="{{ $breakdown['expected_cash'] }}">
                        <div><label for="actual-cash" class="block text-sm font-bold mb-2">Uang tunai fisik di laci (Rp)</label><input id="actual-cash" name="actual_cash" type="number" inputmode="decimal" min="0" max="999999999.99" step="0.01" required x-model="actual" value="{{ old('actual_cash') }}" aria-describedby="actual-help" class="w-full p-3 rounded-xl border-gray-300"><p id="actual-help" class="text-xs text-gray-600 mt-2">Hitung seluruh uang tunai termasuk modal awal. Jangan masukkan saldo QRIS atau kartu.</p></div>
                        <div class="p-4 bg-ivory border border-line rounded-xl" role="status" aria-live="polite">
                            <p class="text-sm">Pratinjau status</p><p class="font-bold text-xl mt-1" x-text="label" :class="difference === null ? 'text-gray-600' : difference < 0 ? 'text-red-800' : difference > 0 ? 'text-amber-800' : 'text-green-800'">Belum dihitung</p>
                            <p class="text-sm mt-2" x-text="difference === null ? 'Masukkan kas fisik untuk melihat selisih.' : 'Selisih (fisik − seharusnya): ' + money(difference)"></p>
                        </div>
                        <div><label for="closing-notes" class="block text-sm font-bold mb-2">Catatan penutupan <span x-text="difference !== null && difference !== 0 ? '(wajib)' : '(opsional)'">(opsional)</span></label><textarea id="closing-notes" name="notes" rows="3" maxlength="1000" :required="difference !== null && difference !== 0" aria-describedby="notes-help" class="w-full p-3 rounded-xl border-gray-300" placeholder="Jelaskan penyebab selisih atau hasil pemeriksaan">{{ old('notes') }}</textarea><p id="notes-help" class="text-xs text-gray-600 mt-2">Wajib diisi jika kas kurang atau lebih. Maksimal 1.000 karakter.</p></div>
                        <p class="text-xs text-gray-600">Mengisi form hanya menghitung pratinjau. Tombol di bawah menyimpan hasil akhir dan menutup shift. Hasil akhir dihitung ulang oleh server dan tidak dapat diedit dari halaman ini.</p>
                        <button type="submit" :disabled="submitting || difference === null" class="cash-button w-full bg-primary text-ivory disabled:opacity-50" x-text="submitting ? 'Menyimpan…' : 'Simpan Hitungan & Tutup Shift'">Simpan Hitungan &amp; Tutup Shift</button>
                    </form>
                </section>
            </div>
        @else
            <section class="bg-white border border-line p-5 rounded-2xl mt-6"><h2 class="font-bold">Tidak Ada Shift Aktif Anda</h2><p class="text-sm text-gray-600 mt-2">Buka shift dari halaman kasir untuk mulai mencatat transaksi dan merekonsiliasi kas.</p><a class="cash-button inline-block bg-primary text-ivory mt-4" href="{{ route('admin.pos.index') }}">Buka Halaman Kasir</a></section>
        @endif
        <section aria-labelledby="history-heading" class="bg-white border border-line p-5 rounded-2xl mt-6">
            <h2 id="history-heading" class="font-bold text-lg">Riwayat Sesi Kasir</h2><p class="text-sm text-gray-600 mt-2">{{ auth()->user()->can('view-reports') ? 'Menampilkan sesi cafe ini.' : 'Menampilkan sesi Anda sendiri.' }} Status selisih ditetapkan setelah sesi ditutup.</p>
            <form method="GET" action="{{ route('admin.register.index') }}" class="flex flex-wrap items-end gap-3 mt-4">
                <div><label for="filter-status" class="block text-sm mb-1">Status kas</label><select id="filter-status" name="status" class="rounded-lg border-gray-300"><option value="">Semua status</option>@foreach($labels as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></div>
                <div><label for="filter-from" class="block text-sm mb-1">Dibuka mulai</label><input type="date" id="filter-from" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded-lg border-gray-300"></div>
                <div><label for="filter-to" class="block text-sm mb-1">Sampai</label><input type="date" id="filter-to" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded-lg border-gray-300"></div>
                <button type="submit" class="cash-button border border-gold-400 text-primary">Terapkan</button><a class="cash-button text-primary" href="{{ route('admin.register.index') }}">Reset</a>
            </form>
            <div class="overflow-x-auto mt-5"><table class="w-full text-sm text-left"><thead><tr class="border-b border-line"><th class="p-3" scope="col">Sesi / Kasir</th><th class="p-3" scope="col">Dibuka / Ditutup</th><th class="p-3" scope="col">Status</th><th class="p-3" scope="col">Seharusnya / Fisik</th><th class="p-3" scope="col">Selisih</th><th class="p-3" scope="col">Catatan</th></tr></thead><tbody>
            @forelse($sessions as $session)
                <tr class="border-b border-gray-100 align-top"><td class="p-3 min-w-[150px]">#{{ $session->id }} · {{ $session->user?->name ?? 'Kasir' }}<br><span class="text-xs text-gray-600">{{ $session->register?->name }}</span></td><td class="p-3 whitespace-nowrap">{{ $session->opened_at?->format('d/m/Y H:i') ?? '—' }}<br>{{ $session->closed_at?->format('d/m/Y H:i') ?? 'Masih aktif' }}</td><td class="p-3"><span class="font-bold whitespace-nowrap {{ $session->reconciliation_status === 'SHORT' ? 'text-red-800' : ($session->reconciliation_status === 'OVER' ? 'text-amber-800' : ($session->reconciliation_status === 'BALANCED' ? 'text-green-800' : 'text-gray-600')) }}">{{ $labels[$session->reconciliation_status] }}</span><br><span class="text-xs text-gray-600">{{ $session->status === 'CLOSED' ? 'Ditutup' : 'Aktif' }}</span></td><td class="p-3 whitespace-nowrap">@if($session->reconciliation_status !== 'UNCOUNTED')Rp {{ number_format($session->expected_cash, 2, ',', '.') }}<br>Rp {{ number_format($session->actual_cash, 2, ',', '.') }}@else—@endif</td><td class="p-3 whitespace-nowrap">{{ $session->reconciliation_status === 'UNCOUNTED' ? '—' : 'Rp '.number_format($session->difference, 2, ',', '.') }}</td><td class="p-3 min-w-[160px] max-w-sm whitespace-pre-wrap break-words">{{ $session->notes ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-gray-600">Belum ada sesi yang sesuai filter.</td></tr>
            @endforelse
            </tbody></table></div>
            <div class="mt-4">{{ $sessions->links() }}</div>
        </section>
    </main>
    <script>
    function cashReconciliation(expected, initialActual = '') {
        return {
            actual: initialActual, submitting: false,
            get difference() {
                if (this.actual === '' || this.actual === null || !Number.isFinite(Number(this.actual)) || Number(this.actual) < 0) return null;
                return (Math.round(Number(this.actual) * 100) - Math.round(Number(expected) * 100)) / 100;
            },
            get label() { return this.difference === null ? 'Belum dihitung' : this.difference < 0 ? 'Kas kurang' : this.difference > 0 ? 'Kas lebih' : 'Kas sesuai'; },
            money(value) { return new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', minimumFractionDigits: 2}).format(value); },
        };
    }
    </script>
</div></div>
</body>
</html>
