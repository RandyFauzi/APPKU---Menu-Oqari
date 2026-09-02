<?php
$content = file_get_contents("resources/views/Admin/Dashboard/tabs/pos.blade.php");

$htmlBlocker = <<<'HTML'
    <!-- SHIFT BLOCKER MODAL -->
    <div x-show="!hasActiveSession" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-md flex items-center justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-xl max-w-sm w-full border border-gray-100">
            <div class="w-16 h-16 bg-[#164A35]/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-[#164A35]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-center text-[#202522] mb-2">Buka Kasir</h2>
            <p class="text-sm text-center text-[#777873] mb-6">Masukkan modal awal (uang pecahan) di laci kasir Anda sebelum menerima pesanan.</p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-[#777873] mb-2">Modal Awal (Rp)</label>
                <input type="number" x-model="openingCash" class="w-full bg-[#F5F5F5] border-0 rounded-xl px-4 py-3 text-[#202522] font-bold text-lg focus:ring-2 focus:ring-[#164A35]" placeholder="0">
            </div>
            
            <button @click="openShift()" :disabled="isOpeningShift || openingCash === '' || openingCash < 0" class="w-full bg-[#164A35] text-white font-bold py-3.5 rounded-xl hover:bg-[#113828] transition-colors disabled:opacity-50 flex items-center justify-center">
                <span x-show="!isOpeningShift">Buka Shift</span>
                <span x-show="isOpeningShift" class="animate-spin w-5 h-5 border-2 border-white border-t-transparent rounded-full"></span>
            </button>
        </div>
    </div>
HTML;

// Insert blocker right after <!-- POS Main Container -->
$content = str_replace('<div class="flex h-screen bg-[#F5F5F5] overflow-hidden relative">', '<div class="flex h-screen bg-[#F5F5F5] overflow-hidden relative">' . "\n" . $htmlBlocker, $content);

// Update Alpine Data block
$oldDataStart = "            cart: [],";
$newDataStart = "            hasActiveSession: {{ isset(\$activeSession) ? 'true' : 'false' }},
            openingCash: '',
            isOpeningShift: false,
            activeSessionId: {{ isset(\$activeSession) ? \$activeSession->id : 'null' }},
            
            cart: [],";

$content = str_replace($oldDataStart, $newDataStart, $content);

// Add openShift function to Alpine
$openShiftFunc = <<<'JS'
            openShift() {
                if (this.openingCash === '' || this.openingCash < 0) return;
                this.isOpeningShift = true;
                
                fetch('/admin/shift/open', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ opening_cash: this.openingCash })
                })
                .then(res => res.json())
                .then(data => {
                    this.isOpeningShift = false;
                    if (data.success) {
                        this.hasActiveSession = true;
                        this.activeSessionId = data.session.id;
                        this.openingCash = '';
                    } else {
                        alert(data.message || 'Gagal membuka shift');
                    }
                })
                .catch(err => {
                    this.isOpeningShift = false;
                    console.error(err);
                    alert('Terjadi kesalahan jaringan.');
                });
            },
JS;

$content = str_replace('init() {', $openShiftFunc . "\n            init() {", $content);

file_put_contents("resources/views/Admin/Dashboard/tabs/pos.blade.php", $content);
echo "POS UI updated!\n";
