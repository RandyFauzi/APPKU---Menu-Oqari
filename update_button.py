import re

with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the bulk upload CSV button
old_button = '''<button class="bg-transparent border border-[#164A35] text-[#164A35] px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#DDEBDD] transition-colors flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Bulk upload CSV
                </button>'''

new_button = '''<button @click="$refs.csvInput.click()" class="bg-transparent border border-[#164A35] text-[#164A35] px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#DDEBDD] transition-colors flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Bulk upload CSV
                </button>
                <input type="file" x-ref="csvInput" @change="handleCSVUpload" accept=".csv" class="hidden">'''

if old_button in content:
    content = content.replace(old_button, new_button)
    with open('resources/views/Admin/Dashboard/tabs/menu.blade.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated button in menu.blade.php")
else:
    print("Could not find button")
