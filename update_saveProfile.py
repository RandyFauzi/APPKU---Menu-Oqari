import re

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_save = """                        if(data.success) {
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
                    });"""

new_save = """                        if(data.success) {
                            this.addToast('Profil berhasil diperbarui!', 'success');
                            this.profile.password = '';
                            this.profile.password_confirmation = '';
                            if(data.user) {
                                this.profile.name = data.user.name;
                                this.profile.email = data.user.email;
                                window.INITIAL_DATA.user = data.user;
                            }
                        } else if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            this.addToast(firstError, 'error');
                        } else {
                            this.addToast(data.message || 'Gagal menyimpan profil', 'error');
                        }
                    })
                    .catch(err => {
                        this.isSavingProfile = false;
                        this.addToast('Terjadi kesalahan jaringan', 'error');
                    });"""

content = content.replace(old_save, new_save)

with open('resources/views/Admin/Dashboard/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated saveProfile to handle validation errors")
