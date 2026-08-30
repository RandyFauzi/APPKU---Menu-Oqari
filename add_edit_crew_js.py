import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add edit state
state_pattern = r"newCrew: \{ name: '', email: '', password: '', role: 'barista' \},"
state_repl = r"""newCrew: { name: '', email: '', password: '', role: 'barista' },
                showEditCrewModal: false,
                editCrewData: { id: null, name: '', email: '', password: '', role: 'barista' },"""

content = re.sub(state_pattern, state_repl, content)

# Add updateCrew method next to saveCrew
saveCrew_pattern = r"saveCrew\(\) \{.*?\}(?=\s*,\s*deleteCrew)"
saveCrew_match = re.search(saveCrew_pattern, content, re.DOTALL)
if saveCrew_match:
    save_method = saveCrew_match.group(0)
    update_method = r""",
                openEditCrew(user) {
                    this.editCrewData = { 
                        id: user.id, 
                        name: user.name, 
                        email: user.email, 
                        password: '', 
                        role: user.role 
                    };
                    this.showEditCrewModal = true;
                },
                updateCrew() {
                    this.isSaving = true;
                    fetch('/admin/api/crew/' + this.editCrewData.id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ...this.editCrewData, _method: 'PUT' })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.isSaving = false;
                        if(data.success) {
                            const idx = this.users.findIndex(u => u.id === data.user.id);
                            if(idx !== -1) this.users[idx] = data.user;
                            this.showEditCrewModal = false;
                            this.addToast('Crew berhasil diupdate', 'success');
                        } else {
                            this.addToast(data.message || 'Error update crew', 'error');
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
                }"""
    content = content[:saveCrew_match.end()] + update_method + content[saveCrew_match.end():]

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added edit state and methods to dashboardApp")
