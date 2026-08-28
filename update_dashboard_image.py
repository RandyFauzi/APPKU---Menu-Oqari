import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add ref to input file
content = content.replace(
    '<input type="file" class="w-full text-sm',
    '<input type="file" x-ref="menuImageInput" class="w-full text-sm'
)

# Replace saveNewMenu
old_save_logic = """fetch('/admin/api/menu', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.newMenu)
                    }).then(res => res.json()).then(data => {"""

new_save_logic = """let formData = new FormData();
                    if (this.newMenu.id) formData.append('id', this.newMenu.id);
                    formData.append('name', this.newMenu.name);
                    formData.append('price', this.newMenu.price);
                    formData.append('categoryId', this.newMenu.categoryId);
                    formData.append('desc', this.newMenu.desc || '');
                    if (this.$refs.menuImageInput && this.$refs.menuImageInput.files[0]) {
                        formData.append('image', this.$refs.menuImageInput.files[0]);
                    }
                    
                    fetch('/admin/api/menu', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json()).then(data => {"""

content = content.replace(old_save_logic, new_save_logic)

# Reset file input on success
content = content.replace(
    "this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '' };",
    "this.newMenu = { id: null, name: '', price: '', desc: '', categoryId: '' };\n                            if (this.$refs.menuImageInput) this.$refs.menuImageInput.value = '';"
)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated frontend for image upload in Add/Edit Menu")
