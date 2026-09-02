import os

with open("app/Http/Controllers/Admin/DashboardController.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("Order::findOrFail($order)", "Order::where('shop_id', Auth::user()->shop_id)->findOrFail($order)")
content = content.replace("Order::with('items.product')->findOrFail($order)", "Order::with('items.product')->where('shop_id', Auth::user()->shop_id)->findOrFail($order)")

content = content.replace("Product::findOrFail($menu)", "Product::where('shop_id', Auth::user()->shop_id)->findOrFail($menu)")
content = content.replace("Product::findOrFail($id)", "Product::where('shop_id', Auth::user()->shop_id)->findOrFail($id)")
content = content.replace("User::findOrFail($id)", "User::where('shop_id', Auth::user()->shop_id)->findOrFail($id)")
content = content.replace("CrewShift::findOrFail($id)", "CrewShift::where('shop_id', Auth::user()->shop_id)->findOrFail($id)")

old_save_shift_val = "'user_id' => 'required|exists:users,id',"
new_save_shift_val = """'user_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('shop_id', $user->shop_id)
            ],"""
content = content.replace(old_save_shift_val, new_save_shift_val)

with open("app/Http/Controllers/Admin/DashboardController.php", "w", encoding="utf-8") as f:
    f.write(content)
