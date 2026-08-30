import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_state = r"""                showAddMenuModal: false,
                showAddTableModal: false,"""
new_state = r"""                showAddMenuModal: false,
                showAddTableModal: false,
                showAddCrewModal: false,
                users: window.INITIAL_DATA.users || [],
                newCrew: { name: '', email: '', password: '', role: 'barista' },"""

content = content.replace(old_state, new_state)

old_methods = r"""                saveSettings() {"""
new_methods = r"""                saveCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.newCrew)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSaving = false;
                        if(data.success) {
                            this.users.push(data.user);
                            this.showAddCrewModal = false;
                            this.newCrew = { name: '', email: '', password: '', role: 'barista' };
                            this.addToast('Crew berhasil ditambahkan', 'success');
                        } else {
                            this.addToast(data.message || 'Error menambahkan crew', 'error');
                        }
                    })
                    .catch(() => {
                        this.isSaving = false;
                        this.addToast('Network error', 'error');
                    });
                },
                deleteCrew(id) {
                    if(!confirm('Hapus crew ini?')) return;
                    fetch('/admin/api/crew/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            this.users = this.users.filter(u => u.id !== id);
                            this.addToast('Crew dihapus', 'success');
                        }
                    });
                },
                saveSettings() {"""

content = content.replace(old_methods, new_methods)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated Alpine App logic for Crew")
