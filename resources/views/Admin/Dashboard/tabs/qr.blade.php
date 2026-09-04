<div x-show="currentTab === 'qr'" x-cloak class="flex-grow p-4 md:p-8 overflow-auto hide-scroll">
            <div class="flex justify-between items-center mb-8">
                <!-- Removed redundant title since it's already in the top header -->
                <p class="text-gray-500 font-medium">Kelola QR Code untuk setiap meja secara dinamis.</p>
                <button @click="showAddTableModal = true; $nextTick(() => $refs.tableInput.focus())" class="bg-primary text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2 shadow-sm hover:bg-[#154660] transition-colors">
                    <i class="fas fa-plus"></i> Tambah Meja Baru
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-20">
                <template x-for="table in tables" :key="table.id">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-5 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-sans font-bold text-2xl text-textdark mb-1" x-text="table.id"></h4>
                                <div class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-md inline-block uppercase tracking-wider">Aktif</div>
                            </div>
                            <div class="p-1.5 border border-gray-100 rounded-xl bg-gray-50 shrink-0">
                                <img :src="table.qr" alt="QR Code" class="w-20 h-20 mix-blend-multiply">
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button @click="printQR(table)" class="flex-grow py-2.5 rounded-xl text-sm font-semibold bg-primary/10 text-primary hover:bg-primary/20 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i> Print QR
                            </button>
                            <button @click="resetQR(table)" class="w-11 h-11 shrink-0 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors flex items-center justify-center shadow-sm" title="Reset URL">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
