<div x-show="currentTab === 'shifts'" x-cloak class="flex-grow overflow-y-auto hide-scroll bg-[#f8f9fa] p-4 md:p-6 lg:p-7 font-['DM_Sans',sans-serif]">
    <!-- Top Header Bar -->
    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-[25px] font-semibold tracking-tight text-[#111827]">Jadwal Shift & Roster Kru</h1>
            <p class="mt-1 text-[13px] text-[#7f837e]">Kelola jadwal kerja bulanan tim kafe secara visual dan otomatis tersinkronisasi ke akun staf.</p>
        </div>
        <div class="flex items-center gap-2.5">
            @can('record-attendance')
            <a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-[#e9ebf0] bg-white px-4 text-xs font-semibold text-[#53565a] shadow-xs hover:bg-gray-50 hover:text-[#111827] transition-all">
                <i class="fas fa-user-clock text-[#f9731f]" aria-hidden="true"></i>Buka Absensi
            </a>
            @endcan
            <button type="button" @click="openShift()" :disabled="shiftsLoading || !!shiftsError" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] disabled:opacity-50 transition-all">
                <i class="fas fa-plus" aria-hidden="true"></i>Tambah Shift Manual
            </button>
        </div>
    </div>

    <!-- Sub-Navigation: Unified Tabs between Team Schedule and My Schedule -->
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-[#e9ebf0] pb-3">
        <div class="flex items-center gap-2">
            <button type="button" @click="shiftSubTab = 'team'" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all"
                    :class="shiftSubTab === 'team' ? 'bg-[#fff6f0] text-[#c94d00] shadow-xs' : 'text-[#53565a] hover:bg-gray-100 hover:text-[#111827]'">
                <i class="fas fa-users" aria-hidden="true"></i>
                <span>Jadwal Semua Tim</span>
                <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold shadow-xs text-[#c94d00]" x-text="shifts.length + ' Shift'"></span>
            </button>
            <button type="button" @click="shiftSubTab = 'my'" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition-all"
                    :class="shiftSubTab === 'my' ? 'bg-[#fff6f0] text-[#c94d00] shadow-xs' : 'text-[#53565a] hover:bg-gray-100 hover:text-[#111827]'">
                <i class="fas fa-calendar-user" aria-hidden="true"></i>
                <span>Jadwal Saya</span>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-[#53565a]" x-text="myShifts.length + ' Shift'"></span>
            </button>
        </div>

        <!-- Compact Summary Badges (Replaces Bulky 4 Cards for maximum calendar screen height) -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-[#e9ebf0] bg-white px-2.5 py-1 text-[11px] font-medium text-[#53565a] shadow-2xs">
                <i class="fas fa-user-clock text-emerald-600"></i>
                <span>Hari ini: <strong class="font-bold text-[#111827]" x-text="todayOnDutyShifts.length"></strong> kru</span>
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-[#e9ebf0] bg-white px-2.5 py-1 text-[11px] font-medium text-[#53565a] shadow-2xs">
                <i class="fas fa-clock text-blue-600"></i>
                <span>Total Jam: <strong class="font-bold text-[#111827]" x-text="teamTotalDurationText"></strong></span>
            </span>
            <span class="hidden sm:inline-flex items-center gap-1.5 rounded-lg border border-[#e9ebf0] bg-white px-2.5 py-1 text-[11px] font-medium text-[#53565a] shadow-2xs">
                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-[#7f837e]">Zona: <strong class="text-[#111827]" x-text="shiftTimezone || 'Asia/Jakarta'"></strong></span>
            </span>
        </div>
    </div>

    <!-- TAB 1: JADWAL SEMUA TIM -->
    <div x-show="shiftSubTab === 'team'" class="space-y-4">
        <!-- Control Toolbar & View Switcher -->
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-4 shadow-xs">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Left: Month / Date Navigator -->
                <div class="flex flex-wrap items-center gap-2">
                    <template x-if="shiftViewMode === 'matrix'">
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="prevMonth()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Bulan Sebelumnya">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" @click="thisMonth()" :disabled="shiftsLoading" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-[#e9ebf0] px-3 text-xs font-semibold text-[#53565a] hover:bg-gray-50 disabled:opacity-40">
                                <i class="fas fa-calendar-day text-[11px] text-[#ea580c]"></i>Bulan Ini
                            </button>
                            <button type="button" @click="nextMonth()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Bulan Berikutnya">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="ml-2 text-sm font-bold text-[#111827]" x-text="currentMatrixMonthLabel"></span>
                        </div>
                    </template>

                    <template x-if="shiftViewMode !== 'matrix'">
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="prevWeek()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Minggu Sebelumnya">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button type="button" @click="todayWeek()" :disabled="shiftsLoading" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-[#e9ebf0] px-3 text-xs font-semibold text-[#53565a] hover:bg-gray-50 disabled:opacity-40">
                                <i class="fas fa-calendar-day text-[11px] text-[#ea580c]"></i>Hari Ini
                            </button>
                            <button type="button" @click="nextWeek()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Minggu Berikutnya">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <span class="ml-2 text-xs font-bold text-[#111827]" x-text="shiftDateRangeLabel"></span>
                        </div>
                    </template>
                </div>

                <!-- Right: Search & View Switcher -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Search Input -->
                    <div class="relative w-44 sm:w-56">
                        <i class="fas fa-search absolute left-3 top-3 text-[11px] text-gray-400"></i>
                        <input type="search" x-model="shiftSearch" placeholder="Cari nama staf..." class="h-9 w-full rounded-lg border-[#e9ebf0] pl-8 text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- View Switcher -->
                    <div class="flex items-center gap-1 rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] p-1">
                        <button type="button" @click="shiftViewMode = 'matrix'; fetchShifts()" 
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all" 
                                :class="shiftViewMode === 'matrix' ? 'bg-white text-[#ea580c] shadow-xs font-bold' : 'text-[#7f837e] hover:text-[#111827]'">
                            <i class="fas fa-table-cells text-[11px]"></i>Kalender Matriks (1-31)
                        </button>
                        <button type="button" @click="shiftViewMode = 'timeline'; fetchShifts()" 
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all" 
                                :class="shiftViewMode === 'timeline' ? 'bg-white text-[#ea580c] shadow-xs font-bold' : 'text-[#7f837e] hover:text-[#111827]'">
                            <i class="fas fa-calendar-days text-[11px]"></i>Timeline Harian
                        </button>
                        <button type="button" @click="shiftViewMode = 'table'; fetchShifts()" 
                                class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all" 
                                :class="shiftViewMode === 'table' ? 'bg-white text-[#ea580c] shadow-xs font-bold' : 'text-[#7f837e] hover:text-[#111827]'">
                            <i class="fas fa-table-list text-[11px]"></i>Tabel Rinci
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ERROR ALERT -->
        <div x-show="shiftsError" role="alert" class="flex items-center justify-between gap-3 bg-red-50 p-4 text-xs text-red-700 rounded-xl border border-red-200">
            <span x-text="shiftsError"></span>
            <button type="button" @click="fetchShifts()" class="font-semibold underline">Coba lagi</button>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 1: KALENDER MATRIKS BULANAN (1-30 / 1-31) [REQUEST USER]   -->
        <!-- ============================================================== -->
        <div x-show="shiftViewMode === 'matrix'" class="space-y-3">
            <!-- Tool Selector & Shift Legend Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-[#e9ebf0] bg-white p-3.5 shadow-xs">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-xs font-bold text-[#111827] mr-1.5 flex items-center gap-1">
                        <i class="fas fa-hand-pointer text-[#ea580c]"></i> Mode Klik:
                    </span>
                    
                    <!-- Mode Auto Cycle -->
                    <button type="button" @click="matrixActiveTool = 'auto'" 
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === 'auto' ? 'bg-[#111827] text-white shadow-xs' : 'bg-gray-100 text-[#53565a] hover:bg-gray-200'"
                            title="Klik kotak untuk mengganti berurutan: 1 -> 2 -> 3 -> 4 -> Libur">
                        <i class="fas fa-arrows-rotate text-[10px]"></i>
                        <span>Auto Cycle (1→2→3→4→Off)</span>
                    </button>

                    <!-- Shift 1 Button -->
                    <button type="button" @click="matrixActiveTool = '1'" 
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === '1' ? 'bg-blue-600 text-white shadow-xs ring-2 ring-blue-300' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'">
                        <span class="flex h-4 w-4 items-center justify-center rounded bg-blue-700 text-[10px] text-white font-bold">1</span>
                        <span>Shift 1 (07:00–15:00)</span>
                    </button>

                    <!-- Shift 2 Button -->
                    <button type="button" @click="matrixActiveTool = '2'" 
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === '2' ? 'bg-emerald-600 text-white shadow-xs ring-2 ring-emerald-300' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'">
                        <span class="flex h-4 w-4 items-center justify-center rounded bg-emerald-700 text-[10px] text-white font-bold">2</span>
                        <span>Shift 2 (11:00–19:00)</span>
                    </button>

                    <!-- Shift 3 Button -->
                    <button type="button" @click="matrixActiveTool = '3'" 
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === '3' ? 'bg-purple-600 text-white shadow-xs ring-2 ring-purple-300' : 'bg-purple-50 text-purple-700 hover:bg-purple-100'">
                        <span class="flex h-4 w-4 items-center justify-center rounded bg-purple-700 text-[10px] text-white font-bold">3</span>
                        <span>Shift 3 (15:00–23:00)</span>
                    </button>

                    <!-- Shift 4 Button -->
                    <button type="button" @click="matrixActiveTool = '4'" 
                            class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === '4' ? 'bg-amber-600 text-white shadow-xs ring-2 ring-amber-300' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'">
                        <span class="flex h-4 w-4 items-center justify-center rounded bg-amber-700 text-[10px] text-white font-bold">4</span>
                        <span>Shift 4 (08:00–17:00)</span>
                    </button>

                    <!-- Delete Tool -->
                    <button type="button" @click="matrixActiveTool = 'delete'" 
                            class="inline-flex items-center gap-1 rounded-lg px-2 py-1.5 text-xs font-semibold transition-all" 
                            :class="matrixActiveTool === 'delete' ? 'bg-red-600 text-white shadow-xs' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                        <i class="far fa-trash-can text-[11px]"></i>
                        <span>Hapus / Off</span>
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Realtime Auto-save Indicator (Discreet, replaces disruptive toasts) -->
                    <div x-show="matrixPendingSaves > 0" class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-700 border border-amber-200">
                        <i class="fas fa-circle-notch fa-spin text-[10px]"></i>
                        <span>Menyimpan...</span>
                    </div>
                    <div x-show="matrixPendingSaves === 0" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>Tersimpan Otomatis</span>
                    </div>
                    <span class="hidden xl:inline text-[11px] text-[#7f837e]">
                        • Klik kotak kalender untuk ubah shift instan
                    </span>
                </div>
            </div>

            <!-- Matrix Grid Container (Left Worker Sticky, Right Calendar Scroll) -->
            <div class="relative overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-xs">
                <div class="overflow-x-auto select-none" style="max-height: 650px;">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="bg-[#f8f9fa] border-b border-[#e9ebf0]">
                                <!-- Left Sticky Header: Worker Column -->
                                <th scope="col" class="sticky left-0 top-0 z-30 bg-[#f8f9fa] border-r border-[#e9ebf0] px-4 py-3 min-w-[210px] max-w-[210px] shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#53565a]">Daftar Karyawan</span>
                                        <span class="rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-semibold text-[#53565a]" x-text="shiftEmployees.length + ' staf'"></span>
                                    </div>
                                </th>

                                <!-- Dynamic Calendar Days Columns (1 to 28/30/31) -->
                                <template x-for="day in matrixDays" :key="day.day">
                                    <th scope="col" class="sticky top-0 z-10 border-r border-[#f1f3f5] p-1.5 w-[42px] min-w-[42px] text-center"
                                        :class="day.isToday ? 'bg-[#fff5ee] text-[#ea580c] ring-1 ring-orange-300' : (day.isWeekend ? 'bg-red-50/40 text-red-600' : 'bg-[#f8f9fa] text-[#53565a]')">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] font-medium" x-text="day.dayName"></span>
                                            <span class="text-xs font-bold leading-tight" x-text="day.day"></span>
                                            <span x-show="day.isToday" class="inline-block h-1 w-3 rounded-full bg-[#ea580c] mt-0.5"></span>
                                        </div>
                                    </th>
                                </template>

                                <!-- Summary Column (Right) -->
                                <th scope="col" class="sticky top-0 z-10 bg-[#f8f9fa] border-l border-[#e9ebf0] px-3 py-3 min-w-[90px] text-center text-[10px] font-bold uppercase tracking-wider text-[#53565a]">
                                    Total Shift
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f1f3f5]">
                            <!-- Loading State -->
                            <template x-if="shiftsLoading">
                                <tr>
                                    <td :colspan="matrixDays.length + 2" class="py-16 text-center text-xs text-[#7f837e]">
                                        <i class="fas fa-spinner fa-spin text-lg text-orange-500 mb-2"></i>
                                        <p>Memuat kalender jadwal shift...</p>
                                    </td>
                                </tr>
                            </template>

                            <!-- Empty Worker State -->
                            <template x-if="!shiftsLoading && shiftEmployees.length === 0">
                                <tr>
                                    <td :colspan="matrixDays.length + 2" class="py-12 text-center text-xs text-[#7f837e]">
                                        Belum ada data anggota tim aktif di kafe ini. Silakan tambahkan staf di Manajemen Staf.
                                    </td>
                                </tr>
                            </template>

                            <!-- Staff Rows -->
                            <template x-for="employee in (shiftEmployees.filter(e => e.name.toLowerCase().includes(shiftSearch.toLowerCase())))" :key="employee.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <!-- Left Sticky Column: Employee Info -->
                                    <td class="sticky left-0 z-20 bg-white border-r border-[#e9ebf0] px-4 py-2.5 min-w-[210px] max-w-[210px] shadow-[2px_0_5px_rgba(0,0,0,0.03)]">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#f1f3f5] text-xs font-bold text-[#111827]" x-text="employee.initials || 'ST'"></div>
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-xs font-bold text-[#111827]" x-text="employee.name"></p>
                                                <p class="truncate text-[11px] text-[#7f837e]" x-text="employee.role"></p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Date Cells (1 to 28/30/31) -->
                                    <template x-for="day in matrixDays" :key="day.day">
                                        <td class="border-r border-[#f1f3f5] p-1 w-[42px] min-w-[42px] text-center"
                                            :class="day.isToday ? 'bg-[#fffaf6]' : (day.isWeekend ? 'bg-red-50/15' : '')">
                                            <div class="flex items-center justify-center">
                                                <!-- Populated Shift Cell -->
                                                <template x-if="shiftMatrixMap[employee.id + '_' + day.dateStr]">
                                                    <button type="button" 
                                                            @click="onMatrixCellClick(employee, day)" 
                                                            class="group relative h-8 w-8 rounded-lg flex items-center justify-center text-xs font-bold shadow-2xs transition-transform active:scale-90"
                                                            :class="{
                                                                'bg-blue-600 text-white hover:bg-blue-700': shiftMatrixMap[employee.id + '_' + day.dateStr].code === '1',
                                                                'bg-emerald-600 text-white hover:bg-emerald-700': shiftMatrixMap[employee.id + '_' + day.dateStr].code === '2',
                                                                'bg-purple-600 text-white hover:bg-purple-700': shiftMatrixMap[employee.id + '_' + day.dateStr].code === '3',
                                                                'bg-amber-600 text-white hover:bg-amber-700': shiftMatrixMap[employee.id + '_' + day.dateStr].code === '4',
                                                                'bg-teal-600 text-white hover:bg-teal-700': !['1','2','3','4'].includes(shiftMatrixMap[employee.id + '_' + day.dateStr].code)
                                                            }"
                                                            :title="employee.name + ' (' + day.day + ' ' + currentMonthShortName + '): ' + (shiftPresets[shiftMatrixMap[employee.id + '_' + day.dateStr].code]?.name || 'Shift') + ' · ' + shiftMatrixMap[employee.id + '_' + day.dateStr].start_time + '–' + shiftMatrixMap[employee.id + '_' + day.dateStr].end_time">
                                                        <span x-text="shiftMatrixMap[employee.id + '_' + day.dateStr].code || '•'"></span>
                                                    </button>
                                                </template>

                                                <!-- Empty Cell (Dash) -->
                                                <template x-if="!shiftMatrixMap[employee.id + '_' + day.dateStr]">
                                                    <button type="button" 
                                                            @click="onMatrixCellClick(employee, day)" 
                                                            class="h-8 w-8 rounded-lg border border-dashed border-gray-200 text-gray-300 hover:border-orange-400 hover:text-orange-500 hover:bg-orange-50/60 flex items-center justify-center text-[11px] font-semibold transition-all active:scale-90"
                                                            :title="'Klik untuk tambah shift: ' + employee.name + ' tanggal ' + day.day">
                                                        <span>-</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </td>
                                    </template>

                                    <!-- Summary Column: Total shifts for this employee -->
                                    <td class="border-l border-[#e9ebf0] px-3 py-2.5 text-center bg-gray-50/30">
                                        <p class="font-bold text-xs text-[#111827]" x-text="countWorkerMonthShifts(employee.id) + ' shift'"></p>
                                        <p class="text-[10px] text-[#7f837e]" x-text="calcWorkerMonthHours(employee.id)"></p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-[#f8f9fa] border-t-2 border-[#e9ebf0]">
                                <th scope="row" class="sticky left-0 z-20 bg-[#f8f9fa] border-r border-[#e9ebf0] px-4 py-2.5 text-[11px] font-bold text-[#53565a] uppercase">
                                    Total Kru Masuk
                                </th>
                                <template x-for="day in matrixDays" :key="day.day">
                                    <td class="border-r border-[#f1f3f5] p-1.5 text-center font-bold text-xs"
                                        :class="countWorkersForDate(day.dateStr) > 0 ? (day.isToday ? 'text-orange-600 bg-orange-100/50' : 'text-[#111827]') : 'text-gray-300'">
                                        <span x-text="countWorkersForDate(day.dateStr) > 0 ? countWorkersForDate(day.dateStr) : '-'"></span>
                                    </td>
                                </template>
                                <td class="border-l border-[#e9ebf0] px-3 py-2.5 text-center font-bold text-xs text-orange-600">
                                    <span x-text="shifts.length"></span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 2: TIMELINE HARIAN (Day-by-Day Grouping)                   -->
        <!-- ============================================================== -->
        <div x-show="shiftViewMode === 'timeline'" class="space-y-4">
            <template x-if="shiftsLoading">
                <div class="py-16 text-center text-xs text-[#7f837e]">
                    <i class="fas fa-spinner fa-spin text-lg text-orange-500 mb-2"></i>
                    <p>Memuat timeline jadwal...</p>
                </div>
            </template>

            <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length === 0">
                <div class="rounded-xl border border-[#e9ebf0] bg-white py-16 text-center shadow-xs">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#ea580c]">
                        <i class="far fa-calendar-xmark text-lg"></i>
                    </div>
                    <p class="font-semibold text-sm text-[#111827]">Belum ada jadwal pada periode ini</p>
                    <p class="mt-1 text-xs text-[#7f837e]">Pilih kalender matriks atau tambahkan shift manual.</p>
                </div>
            </template>

            <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length > 0">
                <div class="space-y-4">
                    <template x-for="day in groupedShiftsByDay" :key="day.date">
                        <div class="rounded-xl border transition-all"
                             :class="day.isToday ? 'border-orange-300 bg-[#fffdfa]' : (day.shifts.length > 0 ? 'border-[#e9ebf0] bg-white' : 'border-[#e9ebf0] bg-gray-50/50')">
                            <!-- Day Header -->
                            <div class="flex items-center justify-between border-b px-4 py-3"
                                 :class="day.isToday ? 'border-orange-200 bg-[#fff8f2]' : 'border-[#e9ebf0] bg-gray-50/80'">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 flex-col items-center justify-center rounded-lg"
                                         :class="day.isToday ? 'bg-[#ea580c] text-white font-bold' : 'bg-white border border-[#e9ebf0] text-[#111827] font-semibold'">
                                        <span class="text-xs leading-none" x-text="day.dateNum"></span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-xs font-bold text-[#111827]" x-text="day.dayName + ', ' + day.dateLabel"></h3>
                                            <span x-show="day.isToday" class="rounded-md bg-orange-500 px-2 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">HARI INI</span>
                                        </div>
                                        <p class="text-[11px] text-[#7f837e]">
                                            <span x-text="day.shifts.length > 0 ? day.shifts.length + ' staf terjadwal' : 'Tidak ada jadwal'"></span>
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="openShift(null, day.date)" class="inline-flex items-center gap-1 rounded-lg border border-[#e9ebf0] bg-white px-2.5 py-1 text-[11px] font-semibold text-[#53565a] hover:bg-gray-100 hover:text-[#111827] shadow-2xs">
                                    <i class="fas fa-plus text-[9px] text-[#ea580c]"></i>Shift
                                </button>
                            </div>

                            <!-- Day Shifts Cards -->
                            <div class="p-3">
                                <template x-if="day.shifts.length === 0">
                                    <div class="py-3 text-center text-xs text-[#9ca3af] italic">Belum ada staf yang dijadwalkan pada hari ini</div>
                                </template>
                                <template x-if="day.shifts.length > 0">
                                    <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                                        <template x-for="shift in day.shifts" :key="shift.id">
                                            <div class="relative flex flex-col justify-between rounded-xl border border-[#e9ebf0] bg-white p-3.5 shadow-xs hover:border-orange-200 transition-all">
                                                <div>
                                                    <div class="flex items-start justify-between gap-2">
                                                        <div class="flex items-center gap-2.5 min-w-0">
                                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#f1f3f5] text-xs font-bold text-[#111827]" x-text="shift.user_initials || 'ST'"></div>
                                                            <div class="min-w-0">
                                                                <p class="truncate text-xs font-bold text-[#111827]" x-text="shift.user_name"></p>
                                                                <span class="inline-block rounded-md bg-[#f8f9fa] border border-[#e9ebf0] px-1.5 py-0.5 text-[10px] font-medium text-[#53565a]" x-text="shift.position || shift.user_role || 'Karyawan'"></span>
                                                            </div>
                                                        </div>
                                                        <span x-show="isShiftLive(shift)" class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700 animate-pulse">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 flex items-center justify-between text-xs">
                                                        <div class="flex items-center gap-1.5 font-semibold text-[#111827]">
                                                            <i class="far fa-clock text-[#ea580c] text-[11px]"></i>
                                                            <span x-text="shift.start_time + ' – ' + shift.end_time"></span>
                                                            <span x-show="shift.overnight" class="rounded bg-orange-100 px-1 py-0.2 text-[9px] font-bold text-[#c94d00]">+1 hr</span>
                                                        </div>
                                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] text-[#53565a]" x-text="shiftDurationCalc(shift.start_time, shift.end_time)"></span>
                                                    </div>

                                                    <p x-show="shift.notes" class="mt-2 text-[11px] text-[#7f837e] italic line-clamp-2" x-text="shift.notes"></p>
                                                </div>

                                                <div class="mt-3 flex items-center justify-end gap-1 border-t border-[#f1f3f5] pt-2">
                                                    <button type="button" @click="openShift(shift)" :disabled="!!shiftDeletingId" class="inline-flex h-8 items-center gap-1 rounded-md px-2.5 text-[11px] font-medium text-[#53565a] hover:bg-gray-100">
                                                        <i class="fas fa-pen text-[10px]"></i>Edit
                                                    </button>
                                                    <button type="button" @click="deleteShift(shift.id)" :disabled="!!shiftDeletingId" class="inline-flex h-8 w-8 items-center justify-center rounded-md text-red-500 hover:bg-red-50" title="Hapus shift">
                                                        <i class="far fa-trash-can text-[11px]"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 3: TABEL RINCI (Table View)                                -->
        <!-- ============================================================== -->
        <div x-show="shiftViewMode === 'table'" class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-xs">
            <div class="overflow-x-auto" :aria-busy="shiftsLoading">
                <table class="w-full min-w-[760px] text-left text-[13px]">
                    <thead class="border-b border-[#e9ebf0] bg-[#f8f9fa] text-[10px] uppercase tracking-wider text-[#7f837e]">
                        <tr>
                            <th scope="col" class="px-5 py-4">Tanggal & Hari</th>
                            <th scope="col" class="px-5 py-4">Anggota Tim</th>
                            <th scope="col" class="px-5 py-4">Kode & Waktu Shift</th>
                            <th scope="col" class="px-5 py-4">Durasi</th>
                            <th scope="col" class="px-5 py-4">Catatan</th>
                            <th scope="col" class="px-5 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e9ebf0]">
                        <template x-if="shiftsLoading">
                            <tr><td colspan="6" class="py-16 text-center text-[#7f837e]">Memuat jadwal...</td></tr>
                        </template>
                        <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length === 0">
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#ea580c]">
                                        <i class="far fa-calendar"></i>
                                    </div>
                                    <p class="font-semibold text-[#111827]" x-text="shiftSearch ? 'Tidak ada jadwal yang cocok' : 'Belum ada jadwal pada periode ini'"></p>
                                    <p class="mt-2 text-xs text-[#7f837e]">Pilih periode lain atau tambahkan jadwal kerja tim.</p>
                                </td>
                            </tr>
                        </template>
                        <template x-for="shift in (shiftsLoading || shiftsError ? [] : filteredShifts)" :key="shift.id">
                            <tr class="hover:bg-[#fafbfc] transition-colors" :class="isShiftLive(shift) ? 'bg-[#fffaf6]' : ''">
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-[#53565a]">
                                    <div class="flex items-center gap-2">
                                        <span x-text="shift.date_label"></span>
                                        <span x-show="shift.date === shiftTodayDate" class="rounded bg-orange-100 px-1.5 py-0.5 text-[9px] font-bold text-[#c94d00]">HARI INI</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#f1f3f5] text-[11px] font-bold text-[#111827]" x-text="shift.user_initials || 'ST'"></div>
                                        <div>
                                            <p class="font-semibold text-[#111827]" x-text="shift.user_name"></p>
                                            <p class="text-xs text-[#7f837e]" x-text="shift.position || shift.user_role || 'Posisi belum ditentukan'"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-5 w-5 items-center justify-center rounded text-[10px] font-bold text-white shadow-2xs"
                                              :class="{
                                                  'bg-blue-600': shift.code === '1',
                                                  'bg-emerald-600': shift.code === '2',
                                                  'bg-purple-600': shift.code === '3',
                                                  'bg-amber-600': shift.code === '4',
                                                  'bg-teal-600': !['1','2','3','4'].includes(shift.code)
                                              }" x-text="shift.code || '1'"></span>
                                        <span class="rounded-md bg-[#fff6f0] px-2.5 py-1 text-xs font-semibold text-[#c94d00]" x-text="shift.start_time + ' – ' + shift.end_time"></span>
                                        <span x-show="isShiftLive(shift)" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Live
                                        </span>
                                    </div>
                                    <p x-show="shift.overnight" class="mt-1 text-[11px] text-[#7f837e]">Selesai hari berikutnya (+1)</p>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs text-[#53565a]" x-text="shiftDurationCalc(shift.start_time, shift.end_time)"></td>
                                <td class="max-w-xs break-words px-5 py-4 text-xs text-[#7f837e]" x-text="shift.notes || '—'"></td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-1.5">
                                        <button type="button" @click="openShift(shift)" :disabled="!!shiftDeletingId" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-[#e9ebf0] px-3 text-xs text-[#53565a] hover:bg-gray-50">
                                            <i class="fas fa-pen text-[10px]"></i>Edit
                                        </button>
                                        <button type="button" @click="deleteShift(shift.id)" :disabled="!!shiftDeletingId" class="h-9 w-9 rounded-lg text-red-500 hover:bg-red-50 disabled:opacity-40">
                                            <i class="far fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: JADWAL SAYA (Personal View for Logged-In Account) -->
    <div x-show="shiftSubTab === 'my'" class="space-y-5">
        <!-- Hero Card: Shift Hari Ini -->
        <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-[#fffaf6] to-[#fff3ea] p-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-[#ea580c] to-[#f9731f] text-white shadow-xs">
                        <i class="fas fa-calendar-day text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#ea580c]">Status Shift Anda Hari Ini</span>
                            <template x-if="myTodayShift">
                                <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800" x-text="isShiftLive(myTodayShift) ? 'SEDANG BERLANGSUNG' : 'TERJADWAL HARI INI'"></span>
                            </template>
                            <template x-if="!myTodayShift">
                                <span class="rounded-md bg-gray-200 px-2 py-0.5 text-[10px] font-bold text-[#53565a]">LIBUR / TIDAK ADA SHIFT</span>
                            </template>
                        </div>
                        <template x-if="myTodayShift">
                            <div>
                                <h3 class="mt-1 text-lg font-bold text-[#111827]">
                                    <span x-text="myTodayShift.start_time + ' – ' + myTodayShift.end_time"></span>
                                    <span class="text-xs font-normal text-[#53565a]" x-text="'(' + shiftDurationCalc(myTodayShift.start_time, myTodayShift.end_time) + ')'"></span>
                                </h3>
                                <p class="mt-0.5 text-xs text-[#53565a]">
                                    Posisi: <strong class="text-[#111827]" x-text="myTodayShift.position || 'Staf Operasional'"></strong>
                                    <span x-show="myTodayShift.notes" x-text="' · Catatan: ' + myTodayShift.notes"></span>
                                </p>
                            </div>
                        </template>
                        <template x-if="!myTodayShift">
                            <p class="mt-1 text-xs text-[#53565a]">Hari ini Anda tidak memiliki jadwal shift kerja. Selamat beristirahat atau nikmati hari libur Anda.</p>
                        </template>
                    </div>
                </div>

                @can('record-attendance')
                <div class="shrink-0">
                    <a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#ea580c] to-[#f9731f] px-5 text-xs font-bold text-white shadow-sm hover:from-[#c2410c] hover:to-[#ea580c] transition-all">
                        <i class="fas fa-fingerprint text-sm"></i>Buka Absensi Sekarang
                    </a>
                </div>
                @endcan
            </div>
        </div>

        <!-- 7-Day Personal Schedule List -->
        <section class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-xs">
            <div class="border-b border-[#e9ebf0] p-4 lg:p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#111827]">Rincian Jadwal Pribadi</h3>
                    <p class="text-xs text-[#7f837e]">Daftar shift Anda yang telah dimasukkan oleh manajer kafe.</p>
                </div>
                <span class="rounded-md bg-[#fff6f0] px-2.5 py-1 text-xs font-semibold text-[#c94d00]" x-text="myShifts.length + ' shift terjadwal'"></span>
            </div>

            <div class="divide-y divide-[#e9ebf0]">
                <template x-if="myShifts.length === 0">
                    <div class="py-16 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-[#7f837e]">
                            <i class="far fa-calendar-check text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-[#111827]">Tidak Ada Shift Personal</p>
                        <p class="mt-1 text-xs text-[#7f837e]">Anda belum memiliki shift yang dijadwalkan pada bulan ini.</p>
                    </div>
                </template>

                <template x-for="shift in myShifts" :key="shift.id">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 lg:p-5 hover:bg-[#fafbfc] transition-colors"
                         :class="shift.date === shiftTodayDate ? 'bg-[#fffaf6]' : ''">
                        <div class="flex items-center gap-3.5">
                            <div class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl border"
                                 :class="shift.date === shiftTodayDate ? 'border-orange-200 bg-[#fff0e4] text-[#c94d00]' : 'border-[#e9ebf0] bg-gray-50 text-[#53565a]'">
                                <span class="text-xs font-bold leading-none" x-text="shift.date_label.split(' ')[0]"></span>
                                <span class="mt-0.5 text-[10px] uppercase font-semibold" x-text="shift.date_label.split(' ')[1]"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-[#111827]" x-text="shift.day_name + ', ' + shift.date_label"></h4>
                                    <span x-show="shift.date === shiftTodayDate" class="rounded bg-orange-100 px-1.5 py-0.5 text-[9px] font-bold text-[#c94d00]">HARI INI</span>
                                    <span x-show="isShiftLive(shift)" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Live
                                    </span>
                                </div>
                                <p class="mt-0.5 text-xs text-[#53565a]">
                                    Posisi: <span class="font-medium text-[#111827]" x-text="shift.position || 'Staf Operasional'"></span>
                                    <span x-show="shift.notes" x-text="' · ' + shift.notes" class="text-[#7f837e]"></span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 self-end sm:self-auto">
                            <div class="text-right">
                                <p class="text-xs font-bold text-[#111827]" x-text="shift.start_time + ' – ' + shift.end_time"></p>
                                <p class="text-[11px] text-[#7f837e]" x-text="shiftDurationCalc(shift.start_time, shift.end_time)"></p>
                            </div>
                            <span class="flex h-6 w-6 items-center justify-center rounded-md text-[11px] font-bold text-white shadow-2xs"
                                  :class="{
                                      'bg-blue-600': shift.code === '1',
                                      'bg-emerald-600': shift.code === '2',
                                      'bg-purple-600': shift.code === '3',
                                      'bg-amber-600': shift.code === '4',
                                      'bg-teal-600': !['1','2','3','4'].includes(shift.code)
                                  }" x-text="shift.code || '1'"></span>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    </div>

    <!-- DIALOG MODAL: TAMBAH / EDIT SHIFT MANUAL -->
    <dialog x-ref="shiftDialog" @click="if ($event.target === $el && !shiftSaving) $el.close()" @cancel="if (shiftSaving) $event.preventDefault()" aria-labelledby="shift-dialog-title" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-[#e9ebf0] bg-white p-0 text-[#111827] shadow-2xl backdrop:bg-black/50">
        <div class="flex items-center justify-between border-b border-[#e9ebf0] px-5 py-4">
            <div>
                <h2 id="shift-dialog-title" class="text-base font-bold text-[#111827]" x-text="newShift.id ? 'Edit Jadwal Shift' : 'Tambah Jadwal Shift Baru'"></h2>
                <p class="text-xs text-[#7f837e]">Pastikan waktu shift tidak bertabrakan dengan jadwal staf lainnya.</p>
            </div>
            <button type="button" @click="$refs.shiftDialog.close()" :disabled="shiftSaving" aria-label="Tutup formulir shift" class="h-9 w-9 rounded-lg text-[#7f837e] hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <form @submit.prevent="saveShift()" class="space-y-4 p-5">
            <p x-show="shiftFormError" x-text="shiftFormError" role="alert" class="rounded-xl bg-red-50 p-3.5 text-xs leading-relaxed text-red-700 border border-red-200"></p>
            <fieldset :disabled="shiftSaving" class="space-y-4">
                <legend class="sr-only">Informasi jadwal shift</legend>

                <div>
                    <label for="shift-employee" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Pilih Anggota Tim *</label>
                    <select id="shift-employee" x-model="newShift.user_id" required class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Pilih staf aktif kafe</option>
                        <template x-for="employee in shiftEmployees" :key="employee.id">
                            <option :value="employee.id" x-text="employee.name + ' · ' + employee.role"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="shift-date" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Tanggal Shift *</label>
                    <input id="shift-date" type="date" x-model="newShift.date" required class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                </div>

                <!-- Quick Shift Preset Buttons -->
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#53565a]">Preset Cepat Jam Shift Kafe</span>
                        <span class="text-[11px] text-[#7f837e]">1-klik isi jam</span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                        <button type="button" @click="applyShiftPreset('07:00', '15:00', 'Barista')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-blue-600">☀️ Shift 1</p>
                            <p class="text-[10px] text-[#53565a]">07:00–15:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('11:00', '19:00', 'Kasir')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-emerald-600">☕ Shift 2</p>
                            <p class="text-[10px] text-[#53565a]">11:00–19:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('15:00', '23:00', 'Barista')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-purple-600">🌙 Shift 3</p>
                            <p class="text-[10px] text-[#53565a]">15:00–23:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('08:00', '17:00', 'Leader')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-amber-600">⚡ Shift 4</p>
                            <p class="text-[10px] text-[#53565a]">08:00–17:00</p>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="shift-start" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Jam Mulai *</label>
                        <input id="shift-start" type="time" x-model="newShift.start_time" required class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                    <div>
                        <label for="shift-end" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Jam Selesai *</label>
                        <input id="shift-end" type="time" x-model="newShift.end_time" required class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>
                </div>

                <div class="rounded-lg bg-[#f8f9fa] border border-[#e9ebf0] p-2.5 flex items-center justify-between text-xs">
                    <span class="text-[#7f837e]">Estimasi Durasi Kerja:</span>
                    <strong class="text-[#111827]" x-text="shiftDurationCalc(newShift.start_time, newShift.end_time)">—</strong>
                </div>

                <div>
                    <label for="shift-position" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Posisi / Peran Tugas <span class="font-normal text-[#9ca3af]">(opsional)</span></label>
                    <input id="shift-position" type="text" maxlength="100" x-model="newShift.position" placeholder="Contoh: Barista, Kasir, Kitchen, Server" class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        <template x-for="chip in ['Barista', 'Kasir', 'Kitchen', 'Server', 'Floor Leader']" :key="chip">
                            <button type="button" @click="newShift.position = chip" class="rounded-md border border-[#e9ebf0] bg-white px-2 py-0.5 text-[10px] text-[#53565a] hover:bg-orange-50 hover:text-[#ea580c] hover:border-orange-200 transition-colors" x-text="chip"></button>
                        </template>
                    </div>
                </div>

                <div>
                    <label for="shift-note" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Catatan Shift <span class="font-normal text-[#9ca3af]">(opsional)</span></label>
                    <textarea id="shift-note" x-model="newShift.notes" maxlength="255" rows="2" placeholder="Catatan tugas khusus, persiapan bahan, dll." class="w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500"></textarea>
                </div>
            </fieldset>

            <div class="flex justify-end gap-2.5 border-t border-[#e9ebf0] pt-4">
                <button type="button" @click="$refs.shiftDialog.close()" :disabled="shiftSaving" class="h-10 rounded-xl border border-[#e9ebf0] px-4 text-xs font-semibold text-[#53565a] hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" :disabled="shiftSaving" class="h-10 rounded-xl bg-gradient-to-r from-[#ea580c] to-[#f9731f] px-5 text-xs font-bold text-white shadow-sm hover:from-[#c2410c] hover:to-[#ea580c] disabled:opacity-50 transition-all flex items-center gap-2">
                    <i x-show="shiftSaving" class="fas fa-spinner fa-spin text-xs"></i>
                    <span x-text="shiftSaving ? 'Menyimpan...' : 'Simpan Jadwal Shift'"></span>
                </button>
            </div>
        </form>
    </dialog>
</div>
