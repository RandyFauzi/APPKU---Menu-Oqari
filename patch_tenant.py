import os

with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update Order lookups
content = content.replace("Order::findOrFail($orderId)", "Order::where('shop_id', Auth::user()->shop_id)->findOrFail($orderId)")
content = content.replace("Order::findOrFail($order)", "Order::where('shop_id', Auth::user()->shop_id)->findOrFail($order)")
content = content.replace("Order::with('items.product', 'table', 'shop')->findOrFail($orderId)", "Order::with('items.product', 'table', 'shop')->where('shop_id', Auth::user()->shop_id)->findOrFail($orderId)")

# 2. Update Product lookups
content = content.replace("Product::findOrFail($menu)", "Product::where('shop_id', Auth::user()->shop_id)->findOrFail($menu)")
content = content.replace("Product::findOrFail($menuId)", "Product::where('shop_id', Auth::user()->shop_id)->findOrFail($menuId)")
content = content.replace("Product::findOrFail($id)", "Product::where('shop_id', Auth::user()->shop_id)->findOrFail($id)")

# 3. Update User lookups (Crew)
content = content.replace("User::findOrFail($id)", "User::where('shop_id', Auth::user()->shop_id)->findOrFail($id)")

# 4. Update shift logic
old_save_shift_start = "public function saveShift(Request $request)"
old_save_shift_end = "return response()->json(['success' => true]);\n    }"

if old_save_shift_start in content and old_save_shift_end in content:
    s_idx = content.find(old_save_shift_start)
    e_idx = content.find(old_save_shift_end) + len(old_save_shift_end)
    
    new_save_shift = """public function saveShift(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'user_id' => [
                'required',
                \\Illuminate\\Validation\\Rule::exists('users', 'id')->where('shop_id', $user->shop_id)
            ],
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $data = [
            'user_id' => $request->user_id,
            'shop_id' => $user->shop_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
        ];

        if ($request->filled('id')) {
            $shift = CrewShift::where('shop_id', $user->shop_id)->findOrFail($request->id);
            $shift->update($data);
        } else {
            CrewShift::create($data);
        }

        return response()->json(['success' => true]);
    }"""
    content = content[:s_idx] + new_save_shift + content[e_idx:]


old_del_shift_start = "public function deleteShift($id)"
old_del_shift_end = "return response()->json(['success' => true]);\n    }"

if old_del_shift_start in content and old_del_shift_end in content:
    s_idx = content.find(old_del_shift_start)
    e_idx = content.find(old_del_shift_end) + len(old_del_shift_end)
    
    new_del_shift = """public function deleteShift($id)
    {
        $user = Auth::user();
        CrewShift::where('shop_id', $user->shop_id)->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }"""
    content = content[:s_idx] + new_del_shift + content[e_idx:]


with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
