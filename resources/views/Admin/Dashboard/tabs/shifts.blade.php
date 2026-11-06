<div x-show="currentTab === 'shifts'" x-cloak class="flex-grow overflow-y-auto hide-scroll bg-[#f8f9fa] p-4 md:p-6 lg:p-7 font-['DM_Sans',sans-serif]">
    <!-- Top Header Bar -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-[25px] font-semibold tracking-tight text-[#111827]">Jadwal Shift</h1>
            <p class="mt-1 text-[13px] text-[#7f837e]">Kelola pembagian shift tim kafe dan pantau jadwal kerja kru secara terintegrasi.</p>
        </div>
        <div class="flex items-center gap-2.5">
            @can('record-attendance')
            <a href="{{ route('admin.attendance.index') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-[#e9ebf0] bg-white px-4 text-xs font-semibold text-[#53565a] shadow-sm hover:bg-gray-50 hover:text-[#111827] transition-all">
                <i class="fas fa-user-clock text-[#f9731f]" aria-hidden="true"></i>Buka Absensi
            </a>
            @endcan
            <button type="button" @click="openShift()" :disabled="shiftsLoading || !!shiftsError" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#f9731f] to-[#ea580c] px-4 text-xs font-semibold text-white shadow-sm hover:from-[#ea580c] hover:to-[#c2410c] disabled:opacity-50 transition-all">
                <i class="fas fa-plus" aria-hidden="true"></i>Tambah Shift
            </button>
        </div>
    </div>

    <!-- Sub-Navigation: Unified Tabs between Team Schedule and My Schedule -->
    <div class="mb-6 flex items-center justify-between border-b border-[#e9ebf0] pb-2">
        <div class="flex items-center gap-2">
            <button type="button" @click="shiftSubTab = 'team'" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold transition-all"
                    :class="shiftSubTab === 'team' ? 'bg-[#fff6f0] text-[#c94d00] shadow-xs' : 'text-[#53565a] hover:bg-gray-100 hover:text-[#111827]'">
                <i class="fas fa-users" aria-hidden="true"></i>
                <span>Jadwal Semua Tim</span>
                <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold shadow-xs text-[#c94d00]" x-text="filteredShifts.length"></span>
            </button>
            <button type="button" @click="shiftSubTab = 'my'" 
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-semibold transition-all"
                    :class="shiftSubTab === 'my' ? 'bg-[#fff6f0] text-[#c94d00] shadow-xs' : 'text-[#53565a] hover:bg-gray-100 hover:text-[#111827]'">
                <i class="fas fa-calendar-user" aria-hidden="true"></i>
                <span>Jadwal Saya</span>
                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold text-[#53565a]" x-text="myShifts.length"></span>
            </button>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-xs text-[#7f837e]">
            <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>
            <span>Zona Waktu: <strong class="text-[#111827]" x-text="shiftTimezone || 'Asia/Jakarta'"></strong></span>
        </div>
    </div>

    <!-- 4 KPI Summary Cards (Sparta Dashboard Style) -->
    <div class="mb-6 grid gap-3 grid-cols-2 lg:grid-cols-4">
        <!-- KPI 1 -->
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-[#7f837e]">Total Shift Periode Ini</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#fff6f0] text-[#ea580c]">
                    <i class="fas fa-calendar-check text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-[#111827]">
                <span x-text="filteredShifts.length"></span> <span class="text-xs font-normal text-[#7f837e]">shift</span>
            </p>
            <p class="mt-1 text-[11px] text-[#7f837e] truncate" x-text="shiftDateRangeLabel || 'Periode 7 hari'"></p>
        </div>

        <!-- KPI 2 -->
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-[#7f837e]">Kru Bertugas Hari Ini</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fas fa-user-clock text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-[#111827]">
                <span x-text="todayOnDutyShifts.length"></span> <span class="text-xs font-normal text-[#7f837e]">orang</span>
            </p>
            <p class="mt-1 text-[11px] text-emerald-600 font-medium">Siap operasional hari ini</p>
        </div>

        <!-- KPI 3 -->
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-[#7f837e]">Total Rencana Jam Tim</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-clock text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-[#111827]" x-text="teamTotalDurationText">0 jam</p>
            <p class="mt-1 text-[11px] text-[#7f837e]">Alokasi jam kerja kru</p>
        </div>

        <!-- KPI 4 -->
        <div class="rounded-xl border border-[#e9ebf0] bg-white p-4 shadow-xs">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-[#7f837e]">Jadwal Saya Minggu Ini</p>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                    <i class="fas fa-id-badge text-xs" aria-hidden="true"></i>
                </div>
            </div>
            <p class="mt-2 text-xl font-bold text-[#111827]">
                <span x-text="myShifts.length"></span> <span class="text-xs font-normal text-[#7f837e]">shift</span>
            </p>
            <p class="mt-1 text-[11px] text-[#7f837e]" x-text="myTotalDurationText ? myTotalDurationText + ' rencana kerja' : 'Tidak ada shift'"></p>
        </div>
    </div>

    <!-- TAB CONTENT 1: JADWAL SEMUA TIM -->
    <div x-show="shiftSubTab === 'team'">
        <section class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm" aria-labelledby="shift-list-title">
            <!-- Filter & Navigation Toolbar -->
            <div class="flex flex-col gap-4 border-b border-[#e9ebf0] p-4 lg:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Week Fast Navigator -->
                    <div class="flex items-center gap-2">
                        <button type="button" @click="prevWeek()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Minggu Sebelumnya">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button type="button" @click="todayWeek()" :disabled="shiftsLoading" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-[#e9ebf0] px-3 text-xs font-semibold text-[#53565a] hover:bg-gray-50 disabled:opacity-40">
                            <i class="fas fa-calendar-day text-[11px] text-[#ea580c]" aria-hidden="true"></i>Hari Ini
                        </button>
                        <button type="button" @click="nextWeek()" :disabled="shiftsLoading" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[#e9ebf0] text-xs font-medium text-[#53565a] hover:bg-gray-50 disabled:opacity-40" title="Minggu Berikutnya">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                        <div class="ml-2">
                            <h2 id="shift-list-title" class="text-xs font-bold text-[#111827] flex items-center gap-2">
                                <span x-text="shiftDateRangeLabel || 'Memuat periode...'"></span>
                            </h2>
                            <p class="text-[11px] text-[#7f837e]">Periode 7 hari · <span x-text="shiftTimezone"></span></p>
                        </div>
                    </div>

                    <!-- View Switcher (Timeline vs Table) -->
                    <div class="flex items-center gap-1.5 rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] p-1 self-start sm:self-auto">
                        <button type="button" @click="shiftViewMode = 'timeline'" class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all" :class="shiftViewMode === 'timeline' ? 'bg-white text-[#ea580c] shadow-xs font-semibold' : 'text-[#7f837e] hover:text-[#111827]'">
                            <i class="fas fa-calendar-days text-[11px]" aria-hidden="true"></i>Timeline Harian
                        </button>
                        <button type="button" @click="shiftViewMode = 'table'" class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-xs font-medium transition-all" :class="shiftViewMode === 'table' ? 'bg-white text-[#ea580c] shadow-xs font-semibold' : 'text-[#7f837e] hover:text-[#111827]'">
                            <i class="fas fa-table-list text-[11px]" aria-hidden="true"></i>Tabel Rinci
                        </button>
                    </div>
                </div>

                <!-- Search and Filters Form -->
                <form @submit.prevent="fetchShifts()" class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-12 items-end">
                    <!-- Search Input -->
                    <div class="lg:col-span-4">
                        <label for="shift-search" class="mb-1 block text-xs font-medium text-[#53565a]">Cari staf / posisi</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-[11px] text-gray-400" aria-hidden="true"></i>
                            <input id="shift-search" type="search" x-model="shiftSearch" placeholder="Ketik nama atau posisi..." class="h-10 w-full rounded-lg border-[#e9ebf0] pl-8 text-xs focus:border-orange-500 focus:ring-orange-500">
                        </div>
                    </div>

                    <!-- Filter by Staff Dropdown -->
                    <div class="lg:col-span-3">
                        <label for="shift-staff-filter" class="mb-1 block text-xs font-medium text-[#53565a]">Filter Staf</label>
                        <select id="shift-staff-filter" x-model="shiftSearchStaff" class="h-10 w-full rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                            <option value="">Semua Anggota Tim</option>
                            <template x-for="employee in shiftEmployees" :key="employee.id">
                                <option :value="employee.id" x-text="employee.name + ' (' + employee.role + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Start Period Date Picker -->
                    <div class="lg:col-span-3">
                        <label for="shift-period" class="mb-1 block text-xs font-medium text-[#53565a]">Mulai periode</label>
                        <input id="shift-period" type="date" x-model="shiftDate" required class="h-10 w-full rounded-lg border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                    </div>

                    <!-- Submit Button -->
                    <div class="lg:col-span-2">
                        <button type="submit" :disabled="shiftsLoading" class="h-10 w-full rounded-lg bg-[#53565a] px-4 text-xs font-semibold text-white hover:bg-[#374151] disabled:opacity-50 transition-all flex items-center justify-center gap-1.5">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            <span>Tampilkan</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Error Alert -->
            <div x-show="shiftsError" role="alert" class="flex items-center justify-between gap-3 bg-red-50 p-4 text-xs text-red-700">
                <span x-text="shiftsError"></span>
                <button type="button" @click="fetchShifts()" class="font-semibold underline">Coba lagi</button>
            </div>

            <!-- VIEW 1: TIMELINE HARIAN (Day-by-Day Grouping) -->
            <div x-show="shiftViewMode === 'timeline'" class="p-4 lg:p-5 space-y-4">
                <template x-if="shiftsLoading">
                    <div class="py-16 text-center text-xs text-[#7f837e]">
                        <i class="fas fa-spinner fa-spin text-lg text-orange-500 mb-2"></i>
                        <p>Memuat jadwal shift...</p>
                    </div>
                </template>

                <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length === 0">
                    <div class="py-16 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#ea580c]">
                            <i class="far fa-calendar-xmark text-lg" aria-hidden="true"></i>
                        </div>
                        <p class="font-semibold text-sm text-[#111827]" x-text="shiftSearch || shiftSearchStaff ? 'Tidak ada jadwal yang cocok dengan filter' : 'Belum ada jadwal pada periode ini'"></p>
                        <p class="mt-1 text-xs text-[#7f837e]">Gunakan tombol di atas untuk menambahkan jadwal kru kafe.</p>
                        <button type="button" @click="openShift()" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-[#f9731f] px-3 py-2 text-xs font-semibold text-white shadow-xs hover:bg-[#ea580c]">
                            <i class="fas fa-plus text-[10px]"></i>Tambah Shift Sekarang
                        </button>
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

                                <!-- Day Shifts Grid / Empty -->
                                <div class="p-3">
                                    <template x-if="day.shifts.length === 0">
                                        <div class="py-3 text-center text-xs text-[#9ca3af] italic">
                                            Belum ada staf yang dijadwalkan pada hari ini
                                        </div>
                                    </template>
                                    <template x-if="day.shifts.length > 0">
                                        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                                            <template x-for="shift in day.shifts" :key="shift.id">
                                                <div class="relative flex flex-col justify-between rounded-xl border border-[#e9ebf0] bg-white p-3.5 shadow-xs hover:border-orange-200 transition-all">
                                                    <div>
                                                        <!-- Staff Info & Live Indicator -->
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="flex items-center gap-2.5 min-w-0">
                                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#f1f3f5] text-xs font-bold text-[#111827]" x-text="shift.user_initials || 'ST'"></div>
                                                                <div class="min-w-0">
                                                                    <p class="truncate text-xs font-bold text-[#111827]" x-text="shift.user_name"></p>
                                                                    <span class="inline-block rounded-md bg-[#f8f9fa] border border-[#e9ebf0] px-1.5 py-0.5 text-[10px] font-medium text-[#53565a]" x-text="shift.position || shift.user_role || 'Karyawan'"></span>
                                                                </div>
                                                            </div>
                                                            <!-- Live Shift Badge -->
                                                            <span x-show="isShiftLive(shift)" class="inline-flex shrink-0 items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2 py-0.5 text-[10px] font-bold text-emerald-700 animate-pulse">
                                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif
                                                            </span>
                                                        </div>

                                                        <!-- Time & Duration -->
                                                        <div class="mt-3 flex items-center justify-between text-xs">
                                                            <div class="flex items-center gap-1.5 font-semibold text-[#111827]">
                                                                <i class="far fa-clock text-[#ea580c] text-[11px]" aria-hidden="true"></i>
                                                                <span x-text="shift.start_time + ' – ' + shift.end_time"></span>
                                                                <span x-show="shift.overnight" class="rounded bg-orange-100 px-1 py-0.2 text-[9px] font-bold text-[#c94d00]">+1 hr</span>
                                                            </div>
                                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-[10px] text-[#53565a]" x-text="shiftDurationCalc(shift.start_time, shift.end_time)"></span>
                                                        </div>

                                                        <!-- Note -->
                                                        <p x-show="shift.notes" class="mt-2 text-[11px] text-[#7f837e] italic line-clamp-2" x-text="shift.notes"></p>
                                                    </div>

                                                    <!-- Actions -->
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

            <!-- VIEW 2: TABEL RINCI (Table View) -->
            <div x-show="shiftViewMode === 'table'" class="overflow-x-auto" :aria-busy="shiftsLoading">
                <table class="w-full min-w-[760px] text-left text-[13px]">
                    <caption class="sr-only">Jadwal kerja anggota tim kafe</caption>
                    <thead class="border-b border-[#e9ebf0] bg-[#f8f9fa] text-[10px] uppercase tracking-wider text-[#7f837e]">
                        <tr>
                            <th scope="col" class="px-5 py-4">Tanggal & Hari</th>
                            <th scope="col" class="px-5 py-4">Anggota Tim</th>
                            <th scope="col" class="px-5 py-4">Waktu Shift</th>
                            <th scope="col" class="px-5 py-4">Durasi</th>
                            <th scope="col" class="px-5 py-4">Catatan</th>
                            <th scope="col" class="px-5 py-4 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e9ebf0]">
                        <template x-if="shiftsLoading">
                            <tr><td colspan="6" class="py-16 text-center text-[#7f837e]" role="status">Memuat jadwal...</td></tr>
                        </template>
                        <template x-if="!shiftsLoading && !shiftsError && filteredShifts.length === 0">
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff6f0] text-[#ea580c]">
                                        <i class="far fa-calendar" aria-hidden="true"></i>
                                    </div>
                                    <p class="font-semibold text-[#111827]" x-text="shiftSearch || shiftSearchStaff ? 'Tidak ada jadwal yang cocok' : 'Belum ada jadwal pada periode ini'"></p>
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
                                            <i class="fas fa-pen text-[10px]" aria-hidden="true"></i>Edit
                                        </button>
                                        <button type="button" @click="deleteShift(shift.id)" :disabled="!!shiftDeletingId" :aria-label="'Hapus shift ' + shift.user_name + ' tanggal ' + shift.date_label" title="Hapus shift" class="h-9 w-9 rounded-lg text-red-500 hover:bg-red-50 disabled:opacity-40">
                                            <i class="far fa-trash-can" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Footer Notes -->
            <div class="border-t border-[#e9ebf0] px-5 py-3.5 text-[11px] text-[#7f837e] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span>Shift yang melewati tengah malam (+1 hari) berakhir pada keesokan harinya. Jadwal staf tidak boleh bertabrakan.</span>
                <span class="font-medium text-[#111827]">Total data: <span x-text="filteredShifts.length"></span> shift</span>
            </div>
        </section>
    </div>

    <!-- TAB CONTENT 2: JADWAL SAYA (Personal Schedule of Logged-in User) -->
    <div x-show="shiftSubTab === 'my'" class="space-y-5">
        <!-- Today Shift Hero Card -->
        <div class="rounded-xl border border-orange-200 bg-gradient-to-br from-[#fffaf6] to-[#fff3ea] p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-tr from-[#ea580c] to-[#f9731f] text-white shadow-xs">
                        <i class="fas fa-calendar-day text-lg" aria-hidden="true"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#ea580c]">Status Shift Hari Ini</span>
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
                        <i class="fas fa-fingerprint text-sm" aria-hidden="true"></i>Buka Absensi Sekarang
                    </a>
                </div>
                @endcan
            </div>
        </div>

        <!-- 7-Day Personal Schedule List -->
        <section class="overflow-hidden rounded-xl border border-[#e9ebf0] bg-white shadow-sm">
            <div class="border-b border-[#e9ebf0] p-4 lg:p-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#111827]">Rincian Jadwal Pribadi 7 Hari</h3>
                    <p class="text-xs text-[#7f837e]">Jadwal kerja Anda yang telah dialokasikan oleh pengelola kafe.</p>
                </div>
                <span class="rounded-md bg-[#fff6f0] px-2.5 py-1 text-xs font-semibold text-[#c94d00]" x-text="myShifts.length + ' shift terjadwal'"></span>
            </div>

            <div class="divide-y divide-[#e9ebf0]">
                <template x-if="myShifts.length === 0">
                    <div class="py-16 text-center">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-[#7f837e]">
                            <i class="far fa-calendar-check text-lg" aria-hidden="true"></i>
                        </div>
                        <p class="text-sm font-semibold text-[#111827]">Tidak Ada Shift Personal</p>
                        <p class="mt-1 text-xs text-[#7f837e]">Anda belum memiliki shift yang dijadwalkan pada rentang 7 hari ini.</p>
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
                            <span x-show="shift.overnight" class="rounded bg-orange-100 px-2 py-1 text-[10px] font-bold text-[#c94d00]">+1 Hari</span>
                        </div>
                    </div>
                </template>
            </div>
            <div class="border-t border-[#e9ebf0] p-4 text-[11px] text-[#7f837e]">
                Jadwal mengikuti zona waktu kafe (<span x-text="shiftTimezone"></span>). Hubungi manajer jika membutuhkan penyesuaian jadwal atau tukar shift.
            </div>
        </section>
    </div>

    <!-- DIALOG MODAL: TAMBAH / EDIT SHIFT -->
    <dialog x-ref="shiftDialog" @click="if ($event.target === $el && !shiftSaving) $el.close()" @cancel="if (shiftSaving) $event.preventDefault()" aria-labelledby="shift-dialog-title" class="m-auto w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-[#e9ebf0] bg-white p-0 text-[#111827] shadow-2xl backdrop:bg-black/50">
        <!-- Dialog Header -->
        <div class="flex items-center justify-between border-b border-[#e9ebf0] px-5 py-4">
            <div>
                <h2 id="shift-dialog-title" class="text-base font-bold text-[#111827]" x-text="newShift.id ? 'Edit Jadwal Shift' : 'Tambah Jadwal Shift Baru'"></h2>
                <p class="text-xs text-[#7f837e]">Pastikan waktu shift tidak bertabrakan dengan jadwal staf lainnya.</p>
            </div>
            <button type="button" @click="$refs.shiftDialog.close()" :disabled="shiftSaving" aria-label="Tutup formulir shift" class="h-9 w-9 rounded-lg text-[#7f837e] hover:bg-gray-100 flex items-center justify-center transition-colors">
                <i class="fas fa-xmark text-sm" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Dialog Form -->
        <form @submit.prevent="saveShift()" class="space-y-4 p-5">
            <p x-show="shiftFormError" x-text="shiftFormError" role="alert" class="rounded-xl bg-red-50 p-3.5 text-xs leading-relaxed text-red-700 border border-red-200"></p>
            <fieldset :disabled="shiftSaving" class="space-y-4">
                <legend class="sr-only">Informasi jadwal shift</legend>

                <!-- Employee Selection -->
                <div>
                    <label for="shift-employee" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Pilih Anggota Tim *</label>
                    <select id="shift-employee" x-model="newShift.user_id" required class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                        <option value="">Pilih staf aktif kafe</option>
                        <template x-for="employee in shiftEmployees" :key="employee.id">
                            <option :value="employee.id" x-text="employee.name + ' · ' + employee.role"></option>
                        </template>
                    </select>
                </div>

                <!-- Shift Date -->
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
                            <p class="text-[10px] font-bold text-[#ea580c]">☀️ Pagi</p>
                            <p class="text-[10px] text-[#53565a]">07:00–15:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('11:00', '19:00', 'Kasir')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-[#ea580c]">☕ Middle</p>
                            <p class="text-[10px] text-[#53565a]">11:00–19:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('15:00', '23:00', 'Barista')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-[#ea580c]">🌙 Malam</p>
                            <p class="text-[10px] text-[#53565a]">15:00–23:00</p>
                        </button>
                        <button type="button" @click="applyShiftPreset('08:00', '17:00', 'Leader')" class="rounded-lg border border-[#e9ebf0] bg-[#f8f9fa] px-2 py-1.5 text-center hover:bg-orange-50 hover:border-orange-200 transition-all">
                            <p class="text-[10px] font-bold text-[#ea580c]">⚡ Full Day</p>
                            <p class="text-[10px] text-[#53565a]">08:00–17:00</p>
                        </button>
                    </div>
                </div>

                <!-- Shift Start and End Time -->
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

                <!-- Helper Duration and Overnight info -->
                <div class="rounded-lg bg-[#f8f9fa] border border-[#e9ebf0] p-2.5 flex items-center justify-between text-xs">
                    <span class="text-[#7f837e]">Estimasi Durasi Kerja:</span>
                    <strong class="text-[#111827]" x-text="shiftDurationCalc(newShift.start_time, newShift.end_time)">—</strong>
                </div>

                <!-- Position with Suggestion Chips -->
                <div>
                    <label for="shift-position" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Posisi / Peran Tugas <span class="font-normal text-[#9ca3af]">(opsional)</span></label>
                    <input id="shift-position" type="text" maxlength="100" x-model="newShift.position" placeholder="Contoh: Barista, Kasir, Kitchen, Server" class="h-10 w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500">
                    <div class="mt-1.5 flex flex-wrap gap-1.5">
                        <template x-for="chip in ['Barista', 'Kasir', 'Kitchen', 'Server', 'Floor Leader']" :key="chip">
                            <button type="button" @click="newShift.position = chip" class="rounded-md border border-[#e9ebf0] bg-white px-2 py-0.5 text-[10px] text-[#53565a] hover:bg-orange-50 hover:text-[#ea580c] hover:border-orange-200 transition-colors" x-text="chip"></button>
                        </template>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="shift-note" class="mb-1.5 block text-xs font-semibold text-[#53565a]">Catatan Shift <span class="font-normal text-[#9ca3af]">(opsional)</span></label>
                    <textarea id="shift-note" x-model="newShift.notes" maxlength="255" rows="2" placeholder="Catatan tugas khusus, persiapan bahan, dll." class="w-full rounded-xl border-[#e9ebf0] text-xs focus:border-orange-500 focus:ring-orange-500"></textarea>
                </div>
            </fieldset>

            <!-- Actions Footer -->
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
