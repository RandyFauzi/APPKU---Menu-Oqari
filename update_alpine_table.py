import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_methods = r"""                addTableFromModal() {
                    if (!this.newTableId.trim()) {
                        this.addToast('Silakan masukkan nama/nomor meja!', 'error');
                        return;
                    }
                    
                    const tableNumStr = this.newTableId.replace(/[^a-zA-Z0-9]/g, ''); // bersihkan untuk URL
                    this.tables.push({
                        id: this.newTableId.trim(),
                        qr: this.getQRUrl(tableNumStr)
                    });
                    
                    this.newTableId = '';
                    this.showAddTableModal = false;
                },
                printQR(table) {
                    if(window.printQRWindow) window.printQRWindow(table);
                },
                resetQR(table) {
                    if(confirm(`Yakin ingin mereset/mengganti URL QR Code untuk ${table.id}? URL lama tidak akan bisa diakses lagi.`)) {
                        const randomToken = Math.random().toString(36).substring(2, 8);
                        const tableNum = table.id.replace('Meja ', '');
                        table.qr = this.getQRUrl(tableNum, randomToken);
                    }
                },"""

new_methods = r"""                addTableFromModal() {
                    if (!this.newTableId.trim()) {
                        this.addToast('Silakan masukkan nama/nomor meja!', 'error');
                        return;
                    }
                    
                    const tableNumStr = this.newTableId.replace(/[^a-zA-Z0-9]/g, ''); // bersihkan untuk URL
                    const tableName = this.newTableId.trim();
                    const qrUrl = this.getQRUrl(tableNumStr);
                    
                    fetch('/admin/api/table', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
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
                    });
                },
                printQR(table) {
                    if(window.printQRWindow) window.printQRWindow(table);
                },
                resetQR(table) {
                    if(confirm(`Yakin ingin mereset/mengganti URL QR Code untuk ${table.id}? URL lama tidak akan bisa diakses lagi.`)) {
                        const randomToken = Math.random().toString(36).substring(2, 8);
                        const tableNum = table.id.replace('Meja ', '');
                        const newQrUrl = this.getQRUrl(tableNum, randomToken);
                        
                        fetch('/admin/api/table', {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ name: table.id, qr_code_url: newQrUrl })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.success) {
                                table.qr = newQrUrl;
                                this.addToast('QR Code di-reset!', 'success');
                            }
                        });
                    }
                },"""

content = content.replace(old_methods, new_methods)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated Alpine App logic for Tables")
