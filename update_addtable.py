import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_addTable = r"""                    fetch('/admin/api/table', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: tableName, qr_code_url: qrUrl })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.tables.push({
                                id: data.table.name,
                                qr: data.table.qr_code_url
                            });
                            this.newTableId = '';
                            this.showAddTableModal = false;
                            this.addToast('Meja berhasil ditambahkan', 'success');
                        }
                    });"""

new_addTable = r"""                    fetch('/admin/api/table', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ name: tableName, qr_code_url: qrUrl })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        if(data.success) {
                            this.tables.push({
                                id: data.table.name,
                                qr: data.table.qr_code_url
                            });
                            this.newTableId = '';
                            this.showAddTableModal = false;
                            this.addToast('Meja berhasil ditambahkan', 'success');
                        }
                    })
                    .catch(err => {
                        this.addToast(err.message || 'Gagal menyimpan meja', 'error');
                    });"""

content = content.replace(old_addTable, new_addTable)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated addTableFromModal logic")
