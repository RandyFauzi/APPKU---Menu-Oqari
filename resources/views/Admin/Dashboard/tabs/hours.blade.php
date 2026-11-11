<div x-show="currentTab === 'hours'" x-cloak class="flex-grow p-4 md:p-6 lg:p-7 overflow-y-auto hide-scroll bg-white font-['DM_Sans',sans-serif]" style="font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';">

    <!-- Header Page Title -->
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-[#111827] tracking-tight">Jadwal Buka & Tutup Toko</h1>
        <p class="text-xs md:text-sm text-[#7f837e] mt-1 font-normal">Atur jam operasional toko, termasuk jadwal harian dan tanggal khusus (hari libur).</p>
    </div>

    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Banner, Jadwal Mingguan, Hari Libur -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">

            <!-- 1. Top Banner: Status Hari Ini -->
            <div class="relative overflow-hidden rounded-[20px] bg-gradient-to-r from-[#207a48] to-[#1c693e] text-white p-5 md:p-6 shadow-sm">
                <!-- Subtle decorative background awning/store curves -->
                <div class="absolute -right-6 -bottom-8 w-44 h-44 opacity-10 pointer-events-none">
                    <svg viewBox="0 0 200 200" fill="currentColor">
                        <path d="M20,60 C20,40 40,20 60,20 L140,20 C160,20 180,40 180,60 L180,80 C180,95 165,110 150,110 C135,110 125,95 125,80 C125,95 110,110 100,110 C90,110 75,95 75,80 C75,95 65,110 50,110 C35,110 20,95 20,80 Z M35,120 L165,120 L165,180 L35,180 Z" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/95 flex items-center justify-center shrink-0 shadow-sm">
                            <i class="fas fa-store text-2xl text-[#207a48]"></i>
                        </div>
                        <div>
                            <div class="text-[11px] md:text-xs text-white/80 font-medium" x-text="'Hari Ini • ' + todayFormatted"></div>
                            <h2 class="text-lg md:text-xl font-bold text-white mt-0.5 tracking-tight" x-text="todaySchedule.is_closed ? 'Toko Tutup Hari Ini' : 'Toko Buka Hari Ini'"></h2>
                            <div class="flex items-center gap-1.5 text-xs md:text-sm text-white/90 mt-1 font-medium">
                                <i class="far fa-clock text-xs"></i>
                                <span x-text="todaySchedule.is_closed ? 'Tutup sepanjang hari' : (todaySchedule.open + ' – ' + todaySchedule.close + ' ' + tzShort)"></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white font-bold text-xs shadow-sm"
                              :class="!todaySchedule.is_closed ? 'text-[#207a48]' : 'text-red-600'">
                            <span class="w-2 h-2 rounded-full" :class="!todaySchedule.is_closed ? 'bg-[#289e5c] animate-pulse' : 'bg-red-500'"></span>
                            <span x-text="!todaySchedule.is_closed ? 'Buka' : 'Tutup'"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Card: Jadwal Mingguan -->
            <div class="bg-white rounded-[20px] border border-[#e9ebf0] p-5 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-700 shrink-0">
                        <i class="far fa-calendar-alt text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm md:text-base text-[#111827]">Jadwal Mingguan</h3>
                        <p class="text-xs text-[#7f837e]">Atur jam buka dan tutup toko untuk setiap hari.</p>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-[#f8f9fa] text-[#7f837e] uppercase font-semibold text-[11px] tracking-wider">
                                <th class="py-3 px-4 rounded-l-xl w-1/4">Hari</th>
                                <th class="py-3 px-4 w-1/4">Status Toko</th>
                                <th class="py-3 px-4 rounded-r-xl w-1/2">Jam Operasional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            <template x-for="day in daysList" :key="day.key">
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <!-- Hari -->
                                    <td class="py-3.5 px-4 font-bold text-sm text-[#111827]" x-text="day.label"></td>
                                    
                                    <!-- Status Toko Toggle -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-2.5">
                                            <button type="button" 
                                                    @click="operatingHours.weekly[day.key].is_closed = !operatingHours.weekly[day.key].is_closed" 
                                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" 
                                                    :class="!operatingHours.weekly[day.key].is_closed ? 'bg-[#289e5c]' : 'bg-gray-300'"
                                                    :aria-checked="!operatingHours.weekly[day.key].is_closed"
                                                    role="switch">
                                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                                                      :class="!operatingHours.weekly[day.key].is_closed ? 'translate-x-5' : 'translate-x-0'"></span>
                                            </button>
                                            <span class="text-xs font-bold select-none cursor-pointer" 
                                                  @click="operatingHours.weekly[day.key].is_closed = !operatingHours.weekly[day.key].is_closed"
                                                  :class="!operatingHours.weekly[day.key].is_closed ? 'text-[#289e5c]' : 'text-gray-400'" 
                                                  x-text="!operatingHours.weekly[day.key].is_closed ? 'Buka' : 'Tutup'"></span>
                                        </div>
                                    </td>

                                    <!-- Jam Operasional -->
                                    <td class="py-3.5 px-4">
                                        <!-- When Open -->
                                        <div class="flex items-center gap-2" x-show="!operatingHours.weekly[day.key].is_closed">
                                            <div class="relative flex items-center bg-white border border-[#e9ebf0] rounded-xl px-2.5 py-1.5 focus-within:border-[#df5826] focus-within:ring-1 focus-within:ring-[#df5826]/20 transition-all">
                                                <i class="far fa-clock text-gray-400 text-xs mr-2 shrink-0"></i>
                                                <select x-model="operatingHours.weekly[day.key].open" class="bg-transparent border-0 p-0 text-xs font-semibold text-[#111827] focus:ring-0 focus:outline-none pr-3 cursor-pointer">
                                                    <template x-for="t in timeOptions" :key="'open-' + t">
                                                        <option :value="t" x-text="t" :selected="operatingHours.weekly[day.key].open === t"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <span class="text-gray-400 font-bold">–</span>
                                            <div class="relative flex items-center bg-white border border-[#e9ebf0] rounded-xl px-2.5 py-1.5 focus-within:border-[#df5826] focus-within:ring-1 focus-within:ring-[#df5826]/20 transition-all">
                                                <i class="far fa-clock text-gray-400 text-xs mr-2 shrink-0"></i>
                                                <select x-model="operatingHours.weekly[day.key].close" class="bg-transparent border-0 p-0 text-xs font-semibold text-[#111827] focus:ring-0 focus:outline-none pr-3 cursor-pointer">
                                                    <template x-for="t in timeOptions" :key="'close-' + t">
                                                        <option :value="t" x-text="t" :selected="operatingHours.weekly[day.key].close === t"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- When Closed -->
                                        <div class="flex items-center gap-2 opacity-50 cursor-not-allowed select-none" x-show="operatingHours.weekly[day.key].is_closed">
                                            <div class="relative flex items-center bg-[#f8f9fa] border border-[#e9ebf0] rounded-xl px-2.5 py-1.5">
                                                <i class="far fa-clock text-gray-400 text-xs mr-2"></i>
                                                <span class="text-xs font-semibold text-gray-400 pr-3">-</span>
                                            </div>
                                            <span class="text-gray-300 font-bold">–</span>
                                            <div class="relative flex items-center bg-[#f8f9fa] border border-[#e9ebf0] rounded-xl px-2.5 py-1.5">
                                                <i class="far fa-clock text-gray-400 text-xs mr-2"></i>
                                                <span class="text-xs font-semibold text-gray-400 pr-3">-</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Card: Hari Libur / Jadwal Khusus -->
            <div class="bg-white rounded-[20px] border border-[#e9ebf0] p-5 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-700 shrink-0">
                            <i class="far fa-calendar-check text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm md:text-base text-[#111827]">Hari Libur / Jadwal Khusus</h3>
                            <p class="text-xs text-[#7f837e]">Atur tanggal tertentu dengan status buka atau tutup.</p>
                        </div>
                    </div>
                    <div>
                        <button type="button" 
                                @click="openAddHolidayModal()" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-[#df5826] text-[#df5826] hover:bg-orange-50 font-bold text-xs transition-colors shadow-sm">
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Tambah Tanggal Khusus</span>
                        </button>
                    </div>
                </div>

                <!-- Table or Empty State -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs" x-show="operatingHours.holidays && operatingHours.holidays.length > 0">
                        <thead>
                            <tr class="bg-[#f8f9fa] text-[#7f837e] uppercase font-semibold text-[11px] tracking-wider">
                                <th class="py-3 px-4 rounded-l-xl w-1/3">Tanggal</th>
                                <th class="py-3 px-4 w-1/3">Keterangan</th>
                                <th class="py-3 px-4 w-1/4">Status</th>
                                <th class="py-3 px-4 rounded-r-xl text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium">
                            <template x-for="(h, idx) in operatingHours.holidays" :key="idx">
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-sm text-[#111827]" x-text="formatHolidayDate(h.date)"></td>
                                    <td class="py-3.5 px-4 text-xs text-gray-600 font-medium" x-text="h.name"></td>
                                    <td class="py-3.5 px-4">
                                        <template x-if="h.is_closed">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                                                <span>🚫</span> Tutup
                                            </span>
                                        </template>
                                        <template x-if="!h.is_closed">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                                <span>✅</span> Buka
                                            </span>
                                        </template>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <button type="button" 
                                                @click="removeHoliday(idx)" 
                                                class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Hapus Tanggal Khusus">
                                            <i class="far fa-trash-alt text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <!-- Empty state -->
                    <div x-show="!operatingHours.holidays || operatingHours.holidays.length === 0" class="py-8 text-center bg-[#fcfcfd] rounded-2xl border border-dashed border-gray-200">
                        <i class="far fa-calendar-times text-3xl text-gray-300 mb-2"></i>
                        <p class="text-xs font-semibold text-gray-500">Belum ada hari libur atau jadwal khusus.</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Klik tombol di atas untuk menambahkan tanggal libur / khusus.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Pengaturan Operasional -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-6">

            <div class="bg-white rounded-[20px] border border-[#e9ebf0] p-5 md:p-6 shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex flex-col gap-6">
                <!-- Header -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gray-50 flex items-center justify-center text-gray-700 shrink-0">
                        <i class="fas fa-cog text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm md:text-base text-[#111827]">Pengaturan Operasional</h3>
                        <p class="text-xs text-[#7f837e]">Atur pengaturan penting terkait jam operasional toko.</p>
                    </div>
                </div>

                <!-- 1. Zona Waktu -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-globe text-gray-700 text-sm"></i>
                        <label class="font-bold text-xs md:text-sm text-[#111827]">Zona Waktu</label>
                    </div>
                    <div class="relative">
                        <select x-model="operatingHours.timezone" class="w-full bg-[#fcfcfd] border border-[#e9ebf0] rounded-xl px-3.5 py-2.5 text-xs font-medium text-[#111827] focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826]/20 transition-all cursor-pointer">
                            <option value="Asia/Jakarta">(GMT+07:00) Jakarta, WIB</option>
                            <option value="Asia/Makassar">(GMT+08:00) Makassar, WITA</option>
                            <option value="Asia/Jayapura">(GMT+09:00) Jayapura, WIT</option>
                        </select>
                    </div>
                </div>

                <div class="h-px bg-gray-100"></div>

                <!-- 2. Tutup Otomatis di Luar Jam Operasional -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-700 shrink-0 mt-0.5">
                            <i class="fas fa-store-slash text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs md:text-sm text-[#111827] leading-snug">Tutup Otomatis di Luar Jam Operasional</h4>
                            <p class="text-xs text-[#7f837e] mt-1 leading-relaxed">Toko akan otomatis berubah menjadi tutup setelah melewati jam operasional.</p>
                        </div>
                    </div>
                    <div class="shrink-0 pt-0.5">
                        <button type="button" 
                                @click="operatingHours.auto_close = !operatingHours.auto_close" 
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" 
                                :class="operatingHours.auto_close ? 'bg-[#289e5c]' : 'bg-gray-300'"
                                :aria-checked="operatingHours.auto_close"
                                role="switch">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                                  :class="operatingHours.auto_close ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>
                </div>

                <div class="h-px bg-gray-100"></div>

                <!-- 3. Terima Pre-order saat Tutup -->
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-gray-700 shrink-0 mt-0.5">
                            <i class="far fa-calendar-alt text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-xs md:text-sm text-[#111827] leading-snug">Terima Pre-order saat Tutup</h4>
                            <p class="text-xs text-[#7f837e] mt-1 leading-relaxed">Izinkan pelanggan tetap membuat pesanan meskipun toko sedang tutup.</p>
                        </div>
                    </div>
                    <div class="shrink-0 pt-0.5">
                        <button type="button" 
                                @click="operatingHours.allow_preorder = !operatingHours.allow_preorder" 
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" 
                                :class="operatingHours.allow_preorder ? 'bg-[#289e5c]' : 'bg-gray-300'"
                                :aria-checked="operatingHours.allow_preorder"
                                role="switch">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                                  :class="operatingHours.allow_preorder ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bottom Action Buttons: Reset & Simpan Perubahan -->
    <div class="mt-8 pt-4 border-t border-gray-200/60 flex items-center justify-end gap-3">
        <button type="button" 
                @click="resetOperatingHours()" 
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-200 bg-[#f3f4f6] hover:bg-gray-200 text-[#374151] font-bold text-xs transition-all shadow-sm">
            <i class="fas fa-rotate-left text-xs"></i>
            <span>Reset</span>
        </button>

        <button type="button" 
                @click="saveOperatingHours()" 
                :disabled="isSavingHours"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] hover:opacity-95 text-white font-bold text-xs transition-all shadow-md shadow-orange-500/20 active:scale-95 disabled:opacity-50">
            <i class="fas fa-spinner fa-spin text-xs" x-show="isSavingHours"></i>
            <i class="fas fa-floppy-disk text-xs" x-show="!isSavingHours"></i>
            <span x-text="isSavingHours ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
        </button>
    </div>

    <!-- Modal: Tambah Tanggal Khusus -->
    <div x-show="showHolidayModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="showHolidayModal = false"></div>
        <div class="relative bg-white rounded-[24px] max-w-md w-full p-6 shadow-2xl border border-gray-100 z-10" @keydown.escape.window="showHolidayModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-[#df5826]">
                        <i class="far fa-calendar-plus text-sm"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#111827]">Tambah Tanggal Khusus</h3>
                </div>
                <button type="button" @click="showHolidayModal = false" class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="py-4 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Tanggal</label>
                    <input type="date" x-model="newHoliday.date" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-semibold focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826]/20 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Keterangan</label>
                    <input type="text" x-model="newHoliday.name" placeholder="Contoh: Hari Kemerdekaan / Maintenance" class="w-full border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:border-[#df5826] focus:ring-1 focus:ring-[#df5826]/20 transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5">Status Toko</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-bold transition-all"
                               :class="newHoliday.is_closed ? 'border-red-500 bg-red-50/50 text-red-600' : 'border-gray-200 text-gray-600'">
                            <input type="radio" :value="true" x-model="newHoliday.is_closed" class="text-red-600 focus:ring-red-500">
                            <span>🚫 Tutup Toko</span>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 border rounded-xl cursor-pointer text-xs font-bold transition-all"
                               :class="!newHoliday.is_closed ? 'border-green-500 bg-green-50/50 text-green-700' : 'border-gray-200 text-gray-600'">
                            <input type="radio" :value="false" x-model="newHoliday.is_closed" class="text-green-600 focus:ring-green-500">
                            <span>✅ Buka Khusus</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" @click="showHolidayModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="button" @click="addHoliday()" class="px-5 py-2 rounded-xl bg-gradient-to-r from-[#f06428] to-[#df5826] text-white text-xs font-bold shadow-sm hover:opacity-95 transition-opacity">
                    Tambahkan
                </button>
            </div>
        </div>
    </div>

</div>
