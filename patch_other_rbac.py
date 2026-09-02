import re
with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Add a check at the top of updateCrew too
old_update_crew = r"public function updateCrew\(Request \$request, \$id\)\s*\{"
new_update_crew = """public function updateCrew(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }"""
content = re.sub(old_update_crew, new_update_crew, content)

# Also fix the validation in updateCrew
old_update_val = r"\$request->validate\(\[\s*'name' => 'required\|string\|max:255',\s*'email' => 'required\|email\|unique:users,email,' . \$id,\s*'password' => 'nullable\|string\|min:6',\s*'role' => 'required\|string',\s*\]\);"
new_update_val = """$request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,cashier,kitchen,waiter',
        ]);"""
content = re.sub(old_update_val, new_update_val, content)

# Fix deleteCrew
old_del_crew = r"public function deleteCrew\(\$id\)\s*\{"
new_del_crew = """public function deleteCrew($id)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }"""
content = re.sub(old_del_crew, new_del_crew, content)

# Fix saveSettings
old_save_settings = r"public function saveSettings\(Request \$request\)\s*\{"
new_save_settings = """public function saveSettings(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }"""
content = re.sub(old_save_settings, new_save_settings, content)


with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
