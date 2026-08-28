import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_methods = """    public function saveCrew(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        $user = \\Illuminate\\Support\\Facades\\Auth::user();
        
        $newCrew = \\App\\Models\\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \\Illuminate\\Support\\Facades\\Hash::make($request->password),
            'shop_id' => $user->shop_id,
            'role' => $request->role
        ]);

        return response()->json(['success' => true, 'user' => $newCrew]);
    }

    public function deleteCrew($id)
    {
        $user = \\Illuminate\\Support\\Facades\\Auth::user();
        $crew = \\App\\Models\\User::where('shop_id', $user->shop_id)->where('id', $id)->first();
        
        if ($crew && $crew->role !== 'admin') {
            $crew->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot delete this user']);
    }
}
"""

last_brace = content.rfind('}')
if last_brace != -1:
    content = content[:last_brace] + new_methods + content[last_brace+1:]
    with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Added Crew methods")
else:
    print("Failed to find last brace")
