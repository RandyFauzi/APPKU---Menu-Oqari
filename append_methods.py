import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_methods = r"""    public function saveCrew(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        
        $newCrew = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'shop_id' => $user->shop_id,
            'role' => $request->role
        ]);

        return response()->json(['success' => true, 'user' => $newCrew]);
    }

    public function deleteCrew($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $crew = \App\Models\User::where('shop_id', $user->shop_id)->where('id', $id)->first();
        
        if ($crew && $crew->role !== 'admin') {
            $crew->delete();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Cannot delete this user']);
    }
}"""

content = content.replace("}\n", new_methods + "\n", 1)
# Actually the easiest way to append methods before the last closing brace is regex
content = re.sub(r'}([\s\n]*)$', new_methods + r'\1', content)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added Crew methods")
