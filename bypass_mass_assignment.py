import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_create = r"""        $newCrew = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'shop_id' => $user->shop_id,
            'role' => $request->role
        ]);"""

new_create = r"""        $newCrew = new \App\Models\User();
        $newCrew->name = $request->name;
        $newCrew->email = $request->email;
        $newCrew->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $newCrew->shop_id = $user->shop_id;
        $newCrew->role = $request->role;
        $newCrew->save();"""

content = content.replace(old_create, new_create)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated DashboardController saveCrew to avoid mass assignment")
