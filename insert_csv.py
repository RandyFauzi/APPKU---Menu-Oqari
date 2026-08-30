import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

func_code = '''
                handleCSVUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const text = e.target.result;
                        const lines = text.split('\\n');
                        let added = 0;
                        
                        for (let i = 1; i < lines.length; i++) {
                            const line = lines[i].trim();
                            if (!line) continue;
                            
                            const cols = line.split(',');
                            if (cols.length >= 3) {
                                const name = cols[0].replace(/^"|"$/g, '').trim();
                                const category = cols[1].replace(/^"|"$/g, '').trim();
                                const priceStr = cols[2].replace(/[^0-9]/g, '');
                                const price = parseInt(priceStr) || 0;
                                
                                let validCategory = this.categories[0];
                                const catLower = category.toLowerCase();
                                const matchedCat = this.categories.find(c => c.toLowerCase() === catLower);
                                if (matchedCat) validCategory = matchedCat;
                                
                                this.draftMenus.unshift({
                                    id: null,
                                    name: name,
                                    categoryId: validCategory,
                                    price: price,
                                    imagePreview: null,
                                    imageFile: null
                                });
                                added++;
                            }
                        }
                        
                        if (added > 0) {
                            this.addToast(added + ' item diimpor dari CSV', 'success');
                            if (this.draftMenus.length > 0 && this.draftMenus[this.draftMenus.length - 1].name === '') {
                                this.draftMenus.pop();
                            }
                        } else {
                            this.addToast('Format CSV tidak valid atau kosong', 'error');
                        }
                        
                        event.target.value = '';
                    };
                    reader.readAsText(file);
                },
'''

# Find place to insert
match = re.search(r'handleDraftImageUpload\(event, index\) \{', content)
if match:
    pos = match.start()
    new_content = content[:pos] + func_code + content[pos:]
    with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Added handleCSVUpload")
else:
    print("Could not find insertion point")
