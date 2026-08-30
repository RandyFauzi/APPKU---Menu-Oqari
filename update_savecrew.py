import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_saveCrew = r"""                saveCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
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
                },"""

new_saveCrew = r"""                saveCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.newCrew)
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) {
                            throw data;
                        }
                        return data;
                    })
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
                    .catch((err) => {
                        this.isSaving = false;
                        if (err.errors) {
                            const firstError = Object.values(err.errors)[0][0];
                            this.addToast(firstError, 'error');
                        } else {
                            this.addToast(err.message || 'Network error', 'error');
                        }
                    });
                },"""

content = content.replace(old_saveCrew, new_saveCrew)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated saveCrew logic")
