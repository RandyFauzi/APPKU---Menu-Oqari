<div x-show="currentTab === 'crew'" x-cloak class="flex-grow p-8 bg-[#F8F7F3] overflow-y-auto hide-scroll font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <p class="text-[#777873] text-[16px]">Manage your team members and roles.</p>
            <button @click="showAddCrewModal = true" class="bg-[#164A35] text-white px-5 py-2.5 rounded-[12px] font-bold text-[14px] hover:bg-[#0f3526] transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Crew
            </button>
        </div>

        <div class="bg-white rounded-[24px] border border-[#E3E1DC] overflow-hidden shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8F7F3] border-b border-[#E3E1DC] text-[#777873] text-[13px] uppercase tracking-wider">
                        <th class="py-4 px-6 font-bold">Name</th>
                        <th class="py-4 px-6 font-bold">Email</th>
                        <th class="py-4 px-6 font-bold">Role</th>
                        <th class="py-4 px-6 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in users" :key="user.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <img :src="'https://ui-avatars.com/api/?name='+user.name+'&background=DDEBDD&color=164A35'" class="w-8 h-8 rounded-full">
                                    <span class="font-bold text-[#202522]" x-text="user.name"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-[#777873] text-[14px]" x-text="user.email"></td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-md text-[12px] font-bold uppercase tracking-wider" 
                                    :class="{
                                        'bg-[#F7E5D2] text-[#D97A32]': user.role === 'admin',
                                        'bg-[#DDEBDD] text-[#164A35]': user.role !== 'admin'
                                    }" x-text="user.role"></span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button @click="openEditCrew(user)" x-show="user.role !== 'admin'" class="text-gray-400 hover:text-blue-600 p-2 transition-colors mr-1"><i class="fas fa-pen"></i></button>
                                <button @click="deleteCrew(user.id)" x-show="user.role !== 'admin'" class="text-red-400 hover:text-red-600 p-2 transition-colors"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Crew -->
    <div x-show="showAddCrewModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-[24px] w-full max-w-md p-8 shadow-2xl" @click.away="showAddCrewModal = false" x-transition>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[24px] font-bold text-[#164A35]" style="font-family: 'Playfair Display', serif;">Add New Crew</h3>
                <button @click="showAddCrewModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form @submit.prevent="saveCrew" class="flex flex-col gap-4">
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Name</label>
                    <input type="text" x-model="newCrew.name" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" x-model="newCrew.email" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Password</label>
                    <input type="password" x-model="newCrew.password" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Role</label>
                    <select x-model="newCrew.role" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                        <option value="barista">Barista</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="button" @click="showAddCrewModal = false" class="flex-1 bg-white border border-[#E3E1DC] text-[#777873] py-3 rounded-xl font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#164A35] text-white py-3 rounded-xl font-bold hover:bg-[#0f3526] transition-colors" :disabled="isSaving" x-text="isSaving ? 'Saving...' : 'Save Crew'"></button>
                </div>
            </form>
        </div>
    </div>


    <!-- Modal Edit Crew -->
    <div x-show="showEditCrewModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm" x-transition.opacity>
        <div class="bg-white rounded-[24px] w-full max-w-md p-8 shadow-2xl" @click.away="showEditCrewModal = false" x-transition>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-[24px] font-bold text-[#164A35]" style="font-family: 'Playfair Display', serif;">Edit Crew</h3>
                <button @click="showEditCrewModal = false" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
            </div>
            <form @submit.prevent="updateCrew" class="flex flex-col gap-4">
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Name</label>
                    <input type="text" x-model="editCrewData.name" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Email</label>
                    <input type="email" x-model="editCrewData.email" required class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Password <span class="text-[10px] lowercase normal-case text-gray-400">(Biarkan kosong jika tidak diubah)</span></label>
                    <input type="password" x-model="editCrewData.password" placeholder="••••••••" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                </div>
                <div>
                    <label class="block text-[13px] font-bold text-[#777873] mb-1.5 uppercase tracking-wide">Role</label>
                    <select x-model="editCrewData.role" class="w-full bg-[#F8F7F3] border border-[#E3E1DC] rounded-xl px-4 py-3 text-sm focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                        <option value="barista">Barista</option>
                        <option value="kitchen">Kitchen</option>
                    </select>
                </div>
                <div class="mt-4 flex gap-3">
                    <button type="button" @click="showEditCrewModal = false" class="flex-1 bg-white border border-[#E3E1DC] text-[#777873] py-3 rounded-xl font-bold hover:bg-gray-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#164A35] text-white py-3 rounded-xl font-bold hover:bg-[#0f3526] transition-colors" :disabled="isSaving" x-text="isSaving ? 'Saving...' : 'Update Crew'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
