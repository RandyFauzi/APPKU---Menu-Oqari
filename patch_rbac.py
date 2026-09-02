import re
with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

old_save_crew = r"public function saveCrew\(Request \$request\)\s*\{\s*\$request->validate\(\[[\s\S]*?\]\);\s*\$user = Auth::user\(\);\s*\$newCrew = new User;\s*\$newCrew->name = \$request->name;\s*\$newCrew->email = \$request->email;\s*\$newCrew->password = Hash::make\(\$request->password\);\s*\$newCrew->shop_id = \$user->shop_id;\s*\$newCrew->role = \$request->role;\s*\$newCrew->save\(\);\s*return response\(\)->json\(\['success' => true, 'user' => \$newCrew\]\);\s*\}"

new_save_crew = """public function saveCrew(Request $request)
    {
        // P0 Security Fix: RBAC and Whitelist
        if ($request->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya Pemilik Toko (Admin) yang dapat menambah crew.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,cashier,kitchen,waiter',
        ]);

        $user = Auth::user();

        $newCrew = new User;
        $newCrew->name = $request->name;
        $newCrew->email = $request->email;
        $newCrew->password = Hash::make($request->password);
        $newCrew->shop_id = $user->shop_id;
        $newCrew->role = $request->role;
        $newCrew->save();

        return response()->json(['success' => true, 'user' => $newCrew]);
    }"""

content = re.sub(old_save_crew, new_save_crew, content)
with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
