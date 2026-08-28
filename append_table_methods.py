import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_methods = """    public function saveTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'qr_code_url' => 'required|string'
        ]);

        $user = \\Illuminate\\Support\\Facades\\Auth::user();
        
        $table = \\App\\Models\\Table::create([
            'shop_id' => $user->shop_id,
            'name' => $request->name,
            'qr_code_url' => $request->qr_code_url
        ]);

        return response()->json(['success' => true, 'table' => $table]);
    }

    public function updateTableQR(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'qr_code_url' => 'required|string'
        ]);

        $user = \\Illuminate\\Support\\Facades\\Auth::user();
        $table = \\App\\Models\\Table::where('shop_id', $user->shop_id)->where('name', $request->name)->first();
        
        if ($table) {
            $table->qr_code_url = $request->qr_code_url;
            $table->save();
            return response()->json(['success' => true, 'table' => $table]);
        }
        
        return response()->json(['success' => false, 'message' => 'Table not found']);
    }
}
"""

last_brace = content.rfind('}')
if last_brace != -1:
    content = content[:last_brace] + new_methods + content[last_brace+1:]
    with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Added Table methods")
else:
    print("Failed to find last brace")
