import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_index = r"""        $tables = \App\Models\Table::where('shop_id', $shop->id)->get();

        return view('Admin.Dashboard.dashboard', compact('shop', 'menuItems', 'orders', 'tables'));"""

new_index = r"""        $tables = \App\Models\Table::where('shop_id', $shop->id)->get();
        $users = \App\Models\User::where('shop_id', $shop->id)->get();

        return view('Admin.Dashboard.dashboard', compact('shop', 'menuItems', 'orders', 'tables', 'users'));"""

if old_index in content:
    content = content.replace(old_index, new_index)
    with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated DashboardController index")
else:
    print("Could not find index method")
