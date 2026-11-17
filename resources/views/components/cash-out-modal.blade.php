<div x-data="cashOutModalHandler()" 
     @open-cash-out-modal.window="openModal()" 
     @keydown.escape.window="closeModal()" 
     x-cloak>
    
    {{-- BACKDROP & MODAL WRAPPER --}}
    <div x-show="isOpen" 
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs font-['DM_Sans',sans-serif]"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative w-full max-w-lg rounded-[24px] bg-white p-5 md:p-6 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.25)] border border-[#e9ebf0] space-y-4"
             @click.away="closeModal()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">
            
            {{-- MODAL HEADER --}}
            <div class="flex items-center justify-between border-b pb-3.5 border-[#f0f0ee]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[12px] bg-red-50 border border-red-200 text-red-600 flex items-center justify-center text-sm shadow-2xs shrink-0">
                        <i class="fas fa-arrow-up-from-bracket"></i>
                    </div>
                    <div>
                        <h3 class="text-[16px] font-bold text-[#111827]">Catat Kas Keluar</h3>
                        <p class="text-[11.5px] text-[#7f837e]">Pengeluaran uang tunai operasional dari laci kasir</p>
                    </div>
                </div>
                <button type="button" @click="closeModal()" class="w-8 h-8 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 flex items-center justify-center transition cursor-pointer">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- FORM --}}
            <form @submit.prevent="submitForm()" class="space-y-4">
                {{-- ITEM / KEPERLUAN --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="cash-out-item" class="block text-xs font-bold text-[#111827]">
                            Item / Keperluan Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[10px] text-gray-400">Pilih cepat atau ketik sendiri</span>
                    </div>

                    <div class="relative">
                        <input id="cash-out-item" type="text" x-model="form.item" required maxlength="150"
                               placeholder="Contoh: Beli Es Batu, Gas LPG, Plastik..."
                               class="w-full px-3.5 py-2.5 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] text-xs font-semibold text-[#111827] outline-none transition placeholder:text-gray-400">
                    </div>

                    {{-- QUICK CHIPS SUGGESTIONS --}}
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <template x-for="suggestion in suggestions" :key="suggestion">
                            <button type="button" @click="selectChip(suggestion)"
                                    class="px-2.5 py-1 rounded-full text-[10.5px] font-medium border transition cursor-pointer"
                                    :class="form.item === suggestion ? 'bg-[#df5826] text-white border-[#df5826]' : 'bg-[#f4f4f2] text-[#53565a] border-[#e6e5e0] hover:bg-gray-200'">
                                <span x-text="suggestion"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- NOMINAL (RP) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="cash-out-amount" class="block text-xs font-bold text-[#111827]">
                            Nominal Kas Keluar (Rp) <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs font-bold text-red-600" x-text="formattedAmount"></span>
                    </div>

                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-gray-400">Rp</span>
                        <input id="cash-out-amount" type="text" inputmode="numeric" :value="form.amount ? Number(form.amount).toLocaleString('id-ID') : ''" @input="formatAmount($event)" required
                               placeholder="0"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] text-sm font-black text-[#111827] outline-none transition placeholder:text-gray-400">
                    </div>

                    {{-- QUICK AMOUNT CHIPS --}}
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" @click="addAmount(5000)" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 cursor-pointer">+5rb</button>
                        <button type="button" @click="addAmount(10000)" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 cursor-pointer">+10rb</button>
                        <button type="button" @click="addAmount(20000)" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 cursor-pointer">+20rb</button>
                        <button type="button" @click="addAmount(50000)" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 cursor-pointer">+50rb</button>
                        <button type="button" @click="addAmount(100000)" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200 cursor-pointer">+100rb</button>
                        <button type="button" @click="form.amount = ''" class="px-2 py-0.5 rounded-[7px] text-[10.5px] font-semibold text-red-600 hover:bg-red-50 border border-red-200 cursor-pointer">Reset</button>
                    </div>
                </div>

                {{-- CATATAN (OPSIONAL) --}}
                <div>
                    <label for="cash-out-notes" class="block text-xs font-bold text-[#111827] mb-1.5">
                        Catatan / Keterangan Tambahan <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea id="cash-out-notes" x-model="form.notes" rows="2" maxlength="255"
                              placeholder="Keterangan lebih lanjut (misal: warung sebelah, ada struk nota, dll)..."
                              class="w-full px-3.5 py-2 rounded-[12px] border border-[#d8dce2] bg-[#f8f9fa] focus:bg-white focus:border-[#df5826] text-xs text-[#111827] outline-none transition placeholder:text-gray-400"></textarea>
                </div>

                {{-- NOTICE / HELPER INFO --}}
                <div class="p-3 rounded-[12px] bg-amber-50 border border-amber-200 flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-amber-600 text-xs mt-0.5 shrink-0"></i>
                    <p class="text-[11px] text-amber-900 leading-relaxed">
                        Pengeluaran ini akan langsung tercatat di <strong>Rekonsiliasi Kas</strong> dan mengurangi target <strong>Kas Seharusnya</strong> pada shift aktif kasir.
                    </p>
                </div>

                {{-- ACTIONS --}}
                <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-[#f0f0ee]">
                    <button type="button" @click="closeModal()" 
                            class="px-4 py-2.5 rounded-[10px] bg-white border border-[#d8dce2] text-xs font-semibold text-[#53565a] hover:bg-gray-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting || !isFormValid"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px] bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-xs font-bold text-white transition shadow-sm cursor-pointer">
                        <i class="fas" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Kas Keluar'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cashOutModalHandler() {
    return {
        isOpen: false,
        isSubmitting: false,
        suggestions: [
            'Beli Es Batu',
            'Gas LPG',
            'Bahan Baku Tambahan',
            'Operasional & Kebersihan',
            'Parkir / Transport',
            'Kasbon Karyawan',
            'Lainnya'
        ],
        form: {
            item: '',
            amount: '',
            notes: ''
        },

        get isFormValid() {
            return this.form.item.trim().length > 0 && Number(this.form.amount) >= 100;
        },

        get formattedAmount() {
            const val = Number(this.form.amount);
            if (!val || isNaN(val) || val <= 0) return '';
            return 'Rp ' + val.toLocaleString('id-ID');
        },

        openModal() {
            this.form.item = '';
            this.form.amount = '';
            this.form.notes = '';
            this.isOpen = true;
            this.$nextTick(() => {
                const input = document.getElementById('cash-out-item');
                if (input) input.focus();
            });
        },

        closeModal() {
            if (this.isSubmitting) return;
            this.isOpen = false;
        },

        selectChip(val) {
            this.form.item = val;
        },

        addAmount(val) {
            const current = Number(this.form.amount) || 0;
            this.form.amount = current + val;
        },

        formatAmount(e) {
            const input = e.target;
            const oldVal = input.value;
            const oldCursor = input.selectionStart || 0;
            const digitsBefore = oldVal.slice(0, oldCursor).replace(/\D/g, '').length;
            const clean = oldVal.replace(/\D/g, '');
            this.form.amount = clean ? parseInt(clean, 10) : '';
            const formatted = this.form.amount ? Number(this.form.amount).toLocaleString('id-ID') : '';
            input.value = formatted;
            let newCursor = 0, count = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) count++;
                if (count === digitsBefore) { newCursor = i + 1; break; }
            }
            if (count < digitsBefore) newCursor = formatted.length;
            input.setSelectionRange(newCursor, newCursor);
        },

        async submitForm() {
            if (!this.isFormValid || this.isSubmitting) return;
            this.isSubmitting = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || '{{ csrf_token() }}';

            try {
                const response = await fetch('{{ route('shift.cash-out') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        item: this.form.item,
                        amount: this.form.amount,
                        notes: this.form.notes
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.isOpen = false;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Kas Keluar Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#df5826',
                            confirmButtonText: 'Selesai'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                } else {
                    const message = data.message || 'Terjadi kesalahan saat mencatat kas keluar.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: message,
                            confirmButtonColor: '#df5826',
                            confirmButtonText: 'Tutup'
                        });
                    } else {
                        alert(message);
                    }
                }
            } catch (error) {
                console.error('Error recording cash out:', error);
                const errorMsg = 'Gagal mengirim data kas keluar. Silakan periksa koneksi atau coba lagi.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: errorMsg,
                        confirmButtonColor: '#df5826',
                        confirmButtonText: 'Tutup'
                    });
                } else {
                    alert(errorMsg);
                }
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
