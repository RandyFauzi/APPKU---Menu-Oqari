<div x-show="currentTab === 'shifts'" x-cloak class="flex-grow overflow-y-auto hide-scroll bg-[#f8f9fa] p-4 md:p-6 lg:p-7 font-['DM_Sans',sans-serif]">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-[25px] font-semibold tracking-tight text-[#111827]">Jadwal Shift</h1><p class="mt-1.5 text-[13px] text-[#7f837e]">Atur jadwal kerja tim dan pembagian tugas kafe.</p></div>
        <button type="button" @click="openShift()" :disabled="shiftsLoading || !!shiftsError" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] disabled:opacity-50"><i class="fas fa-plus" aria-hidden="true"></i>Tambah Shift</button>
    </div>
    <section class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm" aria-labelledby="shift-list-title">
        <div class="flex flex-col gap-4 border-b border-[#e9ebf0] p-5 lg:flex-row lg:items-end lg:justify-between">
            <div><h2 id="shift-list-title" class="text-sm font-semibold text-[#111827]">Jadwal tim <span class="ml-2 rounded-md bg-gray-100 px-2 py-1 text-[11px] text-[#53565a]" x-text="shifts.length + ' shift'"></span></h2><p class="mt-2 text-xs text-[#7f837e]">Periode 7 hari mulai tanggal pilihan · <span x-text="shiftTimezone"></span></p></div>
            <form @submit.prevent="fetchShifts()" class="flex flex-wrap items-end gap-2.5">
                <div class="flex-1"><label for="shift-search" class="mb-1.5 block text-xs text-[#53565a]">Cari staf / posisi</label><input id="shift-search" type="search" x-model="shiftSearch" placeholder="Nama atau posisi..." class="h-11 w-full rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500"></div>
                <div><label for="shift-period" class="mb-1.5 block text-xs text-[#53565a]">Mulai periode</label><input id="shift-period" type="date" x-model="shiftDate" required class="h-11 rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500"></div>
                <button type="submit" :disabled="shiftsLoading" class="h-11 rounded-lg border border-[#e9ebf0] px-4 text-xs font-semibold text-[#53565a] hover:bg-gray-50 disabled:opacity-50">Tampilkan</button>
            </form>
        </div>
        <div x-show="shiftsError" role="alert" class="flex items-center justify-between gap-3 bg-red-50 p-5 text-sm text-red-700"><span x-text="shiftsError"></span><button type="button" @click="fetchShifts()" class="min-h-[44px] font-semibold underline">Coba lagi</button></div>
        <div class="overflow-x-auto" :aria-busy="shiftsLoading">
            <table class="w-full min-w-[760px] text-left text-[13px]">
                <caption class="sr-only">Jadwal kerja anggota tim kafe</caption>
                <thead class="border-b border-[#e9ebf0] bg-[#f8f9fa] text-[10px] uppercase tracking-wider text-[#7f837e]"><tr><th scope="col" class="px-5 py-4">Tanggal</th><th scope="col" class="px-5 py-4">Anggota tim</th><th scope="col" class="px-5 py-4">Waktu</th><th scope="col" class="px-5 py-4">Catatan</th><th scope="col" class="px-5 py-4 text-right">Tindakan</th></tr></thead>
                <tbody class="divide-y divide-[#e9ebf0]">
                    <template x-if="shiftsLoading"><tr><td colspan="5" class="py-16 text-center text-[#7f837e]" role="status">Memuat jadwal...</td></tr></template>
                    <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length === 0"><tr><td colspan="5" class="px-5 py-16 text-center"><div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#ea580c]"><i class="far fa-calendar" aria-hidden="true"></i></div><p class="font-semibold text-[#111827]" x-text="shiftSearch ? 'Tidak ada jadwal yang cocok' : 'Belum ada jadwal pada periode ini'"></p><p class="mt-2 text-xs text-[#7f837e]">Pilih periode lain atau tambahkan jadwal kerja tim.</p></td></tr></template>
                    <template x-for="shift in (shiftsLoading || shiftsError ? [] : filteredShifts)" :key="shift.id">
                        <tr class="hover:bg-[#fafbfc]">
                            <td class="whitespace-nowrap px-5 py-4 font-medium text-[#53565a]" x-text="shift.date_label"></td>
                            <td class="px-5 py-4"><p class="font-semibold text-[#111827]" x-text="shift.user_name"></p><p class="mt-1 text-xs text-[#7f837e]" x-text="shift.position || 'Posisi belum ditentukan'"></p></td>
                            <td class="whitespace-nowrap px-5 py-4"><span class="rounded-md bg-[#fff6f0] px-2.5 py-1.5 text-xs font-medium text-[#c94d00]" x-text="shift.start_time + ' – ' + shift.end_time"></span><p x-show="shift.overnight" class="mt-2 text-[11px] text-[#7f837e]">Selesai hari berikutnya</p></td>
                            <td class="max-w-xs break-words px-5 py-4 text-xs text-[#7f837e]" x-text="shift.notes || '—'"></td>
                            <td class="px-5 py-4"><div class="flex justify-end gap-1.5"><button type="button" @click="openShift(shift)" :disabled="!!shiftDeletingId" class="inline-flex h-11 items-center gap-2 rounded-lg border border-[#e9ebf0] px-3 text-xs text-[#53565a] hover:bg-gray-50"><i class="fas fa-pen text-[11px]" aria-hidden="true"></i>Edit</button><button type="button" @click="deleteShift(shift.id)" :disabled="!!shiftDeletingId" :aria-label="'Hapus shift ' + shift.user_name + ' tanggal ' + shift.date_label" title="Hapus shift" class="h-11 w-11 rounded-lg text-red-500 hover:bg-red-50 disabled:opacity-40"><i class="far fa-trash-can" aria-hidden="true"></i></button></div></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="border-t border-[#e9ebf0] px-5 py-4 text-[11px] text-[#7f837e]">Shift yang melewati tengah malam berakhir pada hari berikutnya. Jadwal staf tidak boleh bertabrakan.</div>
    </section>
    <dialog x-ref="shiftDialog" @click="if ($event.target === $el && !shiftSaving) $el.close()" @cancel="if (shiftSaving) $event.preventDefault()" aria-labelledby="shift-dialog-title" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-xl border border-[#e9ebf0] bg-white p-0 text-[#111827] shadow-xl backdrop:bg-black/40">
        <div class="flex items-center justify-between border-b border-[#e9ebf0] px-5 py-4"><h2 id="shift-dialog-title" class="text-base font-semibold" x-text="newShift.id ? 'Edit Shift' : 'Tambah Shift'"></h2><button type="button" @click="$refs.shiftDialog.close()" :disabled="shiftSaving" aria-label="Tutup formulir shift" class="h-11 w-11 rounded-lg text-[#7f837e] hover:bg-gray-50"><i class="fas fa-xmark" aria-hidden="true"></i></button></div>
        <form @submit.prevent="saveShift()" class="space-y-4 p-5">
            <p x-show="shiftFormError" x-text="shiftFormError" role="alert" class="rounded-lg bg-red-50 p-3 text-xs leading-relaxed text-red-700"></p>
            <fieldset :disabled="shiftSaving" class="space-y-4">
                <legend class="sr-only">Informasi jadwal shift</legend>
                <div><label for="shift-employee" class="mb-2 block text-xs font-medium text-[#53565a]">Anggota tim *</label><select id="shift-employee" x-model="newShift.user_id" required class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"><option value="">Pilih staf aktif</option><template x-for="employee in shiftEmployees" :key="employee.id"><option :value="employee.id" x-text="employee.name + ' · ' + employee.role"></option></template></select></div>
                <div><label for="shift-date" class="mb-2 block text-xs font-medium text-[#53565a]">Tanggal mulai *</label><input id="shift-date" type="date" x-model="newShift.date" required class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"></div>
                <div class="grid grid-cols-2 gap-3"><div><label for="shift-start" class="mb-2 block text-xs font-medium text-[#53565a]">Jam mulai *</label><input id="shift-start" type="time" x-model="newShift.start_time" required class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"></div><div><label for="shift-end" class="mb-2 block text-xs font-medium text-[#53565a]">Jam selesai *</label><input id="shift-end" type="time" x-model="newShift.end_time" required class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"></div></div>
                <p class="text-[11px] leading-relaxed text-[#7f837e]">Waktu <span x-text="shiftTimezone"></span>. Jika jam selesai lebih awal dari jam mulai, shift berakhir besok.</p>
                <div><label for="shift-position" class="mb-2 block text-xs font-medium text-[#53565a]">Posisi <span class="font-normal text-[#9ca3af]">(opsional)</span></label><input id="shift-position" type="text" maxlength="100" x-model="newShift.position" placeholder="Contoh: Kasir, Barista" class="h-11 w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"></div>
                <div><label for="shift-note" class="mb-2 block text-xs font-medium text-[#53565a]">Catatan <span class="font-normal text-[#9ca3af]">(opsional)</span></label><textarea id="shift-note" x-model="newShift.notes" maxlength="255" rows="2" class="w-full rounded-lg border-[#e9ebf0] text-[13px] focus:border-orange-500 focus:ring-orange-500"></textarea></div>
            </fieldset>
            <div class="flex justify-end gap-3 border-t border-[#e9ebf0] pt-4"><button type="button" @click="$refs.shiftDialog.close()" :disabled="shiftSaving" class="h-11 rounded-lg border border-[#e9ebf0] px-4 text-xs font-medium text-[#53565a]">Batal</button><button type="submit" :disabled="shiftSaving" class="h-11 rounded-lg bg-[#f9731f] px-4 text-xs font-semibold text-white hover:bg-[#ea580c] disabled:opacity-50" x-text="shiftSaving ? 'Menyimpan...' : 'Simpan Shift'"></button></div>
        </form>
    </dialog>
</div>
