import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

update_logic = r"""    public function updateCrew(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string'
        ]);

        $currentUser = \Illuminate\Support\Facades\Auth::user();
        $crew = \App\Models\User::where('shop_id', $currentUser->shop_id)->where('id', $id)->first();
        
        if (!$crew) {
            return response()->json(['success' => false, 'message' => 'Crew not found'], 404);
        }

        // Prevent changing admin role or self-role logic if necessary
        if ($crew->role === 'admin' && $request->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot change admin role'], 403);
        }

        $crew->name = $request->name;
        $crew->email = $request->email;
        if ($request->filled('password')) {
            $crew->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $crew->role = $request->role;
        $crew->save();

        return response()->json(['success' => true, 'user' => $crew]);
    }

    public function deleteCrew($id)"""

content = content.replace("    public function deleteCrew($id)", update_logic)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Added updateCrew method")
