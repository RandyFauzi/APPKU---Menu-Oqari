<div x-show="currentTab === 'shifts'" x-cloak class="flex-grow p-4 md:p-8 bg-[#F8F7F3] overflow-y-auto hide-scroll font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <p class="text-[#777873] text-[16px]">Atur jadwal kerja harian untuk seluruh pegawai.</p>
            <button @click="showAddShiftModal = true; newShift = { id: null, user_id: '', date: '', start_time: '', end_time: '', notes: '' }" 
                x-show="['owner', 'manager'].includes((user.role || '').toLowerCase())" class="bg-[#164A35] text-white px-5 py-2.5 rounded-[12px] font-bold text-[14px] hover:bg-[#0f3526] transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-calendar-plus"></i> Tambah Shift
            </button>
        </div>

    <div class="bg-white rounded-[24px] border border-[#E3E1DC] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8F7F3] border-b border-[#E3E1DC]">
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Tanggal</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Crew</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Waktu Shift</th>
                        <th class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider">Catatan</th>
                        <th x-show="['owner', 'manager'].includes((user.role || '').toLowerCase())" class="py-4 px-6 text-[13px] font-bold text-[#777873] uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="shifts.length === 0">
                        <tr><td colspan="5" class="text-center py-8 text-gray-500">Belum ada jadwal shift.</td></tr>
                    </template>
                    <template x-for="shift in shifts" :key="shift.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6 font-bold text-[#164A35]" x-text="shift.date"></td>
                            <td class="py-4 px-6 font-medium text-[#202522]" x-text="shift.user_name"></td>
                            <td class="py-4 px-6 text-[#777873]"><span x-text="shift.start_time"></span> - <span x-text="shift.end_time"></span></td>
                            <td class="py-4 px-6 text-[#777873]" x-text="shift.notes || '-'"></td>
                            <td x-show="['owner', 'manager'].includes((user.role || '').toLowerCase())" class="py-4 px-6 text-right">
                                <button @click="deleteShift(shift.id)" class="text-red-400 hover:text-red-600 p-2 transition-colors"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Shift -->
    <div x-show="showAddShiftModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-[24px] w-full max-w-md p-8 shadow-2xl" @click.away="showAddShiftModal = false" x-transition>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[24px] font-bold text-[#164A35]" style="font-family: 'Playfair Display', serif;">Tambah Jadwal Shift</h3>
                <button @click="showAddShiftModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form @submit.prevent="saveShift" class="flex flex-col gap-4">
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Pilih Crew</label>
                    <select x-model="newShift.user_id" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] outline-none">
                        <option value="">-- Pilih Pegawai --</option>
                        <template x-for="u in users" :key="u.id">
                            <option :value="u.id" x-text="u.name + ' (' + u.role + ')'"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Tanggal</label>
                    <input type="date" x-model="newShift.date" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Mulai</label>
                        <input type="time" x-model="newShift.start_time" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] outline-none">
                    </div>
                    <div>
                        <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Selesai</label>
                        <input type="time" x-model="newShift.end_time" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Catatan (Opsional)</label>
                    <input type="text" x-model="newShift.notes" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] outline-none">
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="button" @click="showAddShiftModal = false" class="flex-1 bg-white border border-[#E3E1DC] text-[#777873] py-3 rounded-xl font-bold hover:bg-gray-50 transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-[#164A35] text-white py-3 rounded-xl font-bold hover:bg-[#0f3526] transition-colors" :disabled="isSaving" x-text="isSaving ? 'Menyimpan...' : 'Simpan Shift'"></button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
