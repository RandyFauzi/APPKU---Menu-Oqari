import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

start_index = content.find('<!-- MODAL: BULK UPLOAD MENU -->')
if start_index != -1:
    content = content[:start_index] + '''<!-- MODAL: BULK UPLOAD MENU -->
<div x-show="showBulkUpload" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-10 font-sans">
    <!-- Backdrop -->
    <div @click="showBulkUpload = false" class="absolute inset-0 bg-[#202522]/30 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-5xl mx-auto bg-[#FFFFFF] rounded-[28px] p-10 shadow-[0_10px_35px_rgba(0,0,0,0.05)] flex flex-col max-h-[90vh] overflow-hidden border border-[#E3E1DC]">
        
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-[#E3E1DC] shrink-0">
            <div class="flex gap-5">
                <div class="w-20 h-20 rounded-[18px] bg-[#164A35] text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-cloud-upload-alt text-3xl"></i>
                </div>
                <div class="flex flex-col justify-center">
                    <h2 class="text-[32px] text-[#164A35] leading-tight mb-1" style="font-family: 'Playfair Display', serif; font-weight: 700;">Upload Menu</h2>
                    <p class="text-[#777873] text-[16px]">Add your coffee and pastry items with images, prices, and categories.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-transparent border border-[#164A35] text-[#164A35] px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#DDEBDD] transition-colors flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Bulk upload CSV
                </button>
            </div>
        </div>

        <!-- Scrollable Body -->
        <div class="flex-grow overflow-y-auto hide-scroll py-2">
            <div class="flex flex-col gap-3">
                <template x-for="(draft, index) in draftMenus" :key="index">
                    <!-- Row -->
                    <div class="flex items-center gap-5 p-4 bg-white rounded-[16px] border border-[#E3E1DC] shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                        <!-- Image Box -->
                        <div class="w-[218px] h-[130px] shrink-0 relative rounded-xl overflow-hidden flex items-center justify-center group cursor-pointer transition-colors"
                             :class="!draft.imagePreview ? 'border-2 border-dashed border-[#E3E1DC] bg-[#F8F7F3] hover:border-[#164A35]' : 'bg-gray-100'">
                            <input type="file" @change="handleDraftImageUpload($event, index)" accept="image/jpeg, image/png, image/webp" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                            
                            <template x-if="draft.imagePreview">
                                <img :src="draft.imagePreview" class="w-full h-full object-cover">
                            </template>
                            
                            <template x-if="!draft.imagePreview">
                                <div class="text-center flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-cloud-upload-alt text-[#777873] text-2xl group-hover:text-[#164A35] transition-colors"></i>
                                    <p class="text-[12px] text-[#777873] font-medium leading-snug">Upload image<br>JPG, PNG or WEBP</p>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Inputs -->
                        <div class="flex-grow grid grid-cols-3 gap-5">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Item name</label>
                                <input type="text" x-model="draftMenus[index].name" placeholder="e.g. Blueberry Muffin" class="w-full bg-white border border-[#E3E1DC] rounded-[11px] px-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none placeholder-[#C4C4C4]">
                            </div>
                            
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Category</label>
                                <div class="relative">
                                    <select x-model="draftMenus[index].categoryId" class="w-full bg-[#F8F7F3] border border-transparent rounded-[11px] px-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none appearance-none font-medium cursor-pointer">
                                        <option value="Coffee">? Coffee</option>
                                        <option value="Pastry">?? Pastry</option>
                                        <option value="Beverages">?? Beverages</option>
                                        <option value="Foods">?? Foods</option>
                                        <option value="Snacks">?? Snacks</option>
                                        <option value="Sweets">?? Sweets</option>
                                    </select>
                                    <i class="fas fa-chevron-down absolute right-4 top-4 text-xs text-[#777873] pointer-events-none"></i>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[12px] font-semibold text-[#777873] mb-1.5">Price</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-3 text-[16px] text-[#202522] font-medium">Rp</span>
                                    <input type="number" x-model="draftMenus[index].price" placeholder="0" class="w-full bg-white border border-[#E3E1DC] rounded-[11px] pl-11 pr-4 py-3 text-[16px] text-[#202522] focus:border-[#164A35] focus:ring-1 focus:ring-[#164A35] outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="shrink-0 w-12 flex justify-center ml-2">
                            <template x-if="index === draftMenus.length - 1">
                                <button @click="addDraftRow()" class="w-11 h-11 rounded-[10px] bg-transparent border border-[#DDEBDD] text-[#164A35] hover:bg-[#DDEBDD] flex items-center justify-center transition-colors">
                                    <i class="fas fa-plus text-lg"></i>
                                </button>
                            </template>
                            <template x-if="index !== draftMenus.length - 1">
                                <button @click="removeDraftRow(index)" class="w-11 h-11 rounded-[10px] bg-white border border-[#F7E5D2] text-[#D97A32] hover:bg-[#F7E5D2] flex items-center justify-center transition-colors">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div> <!-- End Scrollable Body -->

        <!-- Footer -->
        <div class="flex justify-between items-center pt-6 mt-2 shrink-0">
            <div class="text-[15px] text-[#777873] font-medium"><span class="text-[#164A35] font-bold text-lg mr-1" x-text="draftMenus.length"></span> items added</div>
            <div class="flex gap-4">
                <button @click="showBulkUpload = false" class="px-6 py-3.5 rounded-[12px] font-semibold text-[15px] bg-white border border-[#E3E1DC] text-[#202522] hover:bg-[#F8F7F3] transition-colors shadow-sm">Save draft</button>
                <button @click="saveBulkMenu" class="px-7 py-3.5 rounded-[12px] font-semibold text-[15px] bg-[#D97A32] text-white hover:bg-[#c26d2d] transition-colors shadow-md flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Publish menu
                </button>
            </div>
        </div>
        
        <div class="text-center mt-5 shrink-0">
            <p class="text-[14px] text-[#777873] font-medium flex items-center justify-center gap-2">
                <i class="fas fa-shield-check text-[#DDEBDD] text-lg"></i> 
                Customers will see your updated menu immediately after publishing.
            </p>
        </div>
    </div>
</div>
</div>
'''
    with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated menu.blade.php with new modal design.')
else:
    print('Could not find MODAL: BULK UPLOAD MENU')
