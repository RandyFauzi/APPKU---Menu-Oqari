import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Change dropdown text from Settings to Profile
content = content.replace(
    '<i class="fas fa-cog w-4"></i> Settings',
    '<i class="fas fa-user w-4"></i> Profile'
)

# Add @include('Admin.Dashboard.tabs.profile')
content = content.replace(
    "@include('Admin.Dashboard.tabs.settings')",
    "@include('Admin.Dashboard.tabs.settings')\n        @include('Admin.Dashboard.tabs.profile')"
)

# Add profile state to Alpine data
alpine_state = """                isSavingSettings: false,
                profile: {
                    name: window.INITIAL_DATA.user?.name || '',
                    email: window.INITIAL_DATA.user?.email || '',
                    password: '',
                    password_confirmation: ''
                },
                isSavingProfile: false,
                saveProfile() {
                    this.isSavingProfile = true;
                    fetch('/admin/api/profile', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.profile)
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isSavingProfile = false;
                        if(data.success) {
                            this.addToast('Profil berhasil diperbarui!', 'success');
                            this.profile.password = '';
                            this.profile.password_confirmation = '';
                            if(data.user) {
                                this.profile.name = data.user.name;
                                this.profile.email = data.user.email;
                                window.INITIAL_DATA.user = data.user;
                            }
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan profil', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingProfile = false;
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });
                },"""
content = content.replace("                isSavingSettings: false,", alpine_state)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated dashboard.blade.php")
