import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_create = r"""        $table = \App\Models\Table::create([
            'shop_id' => $user->shop_id,
            'name' => $request->name,
            'qr_code_url' => $request->qr_code_url
        ]);"""

new_create = r"""        $table = new \App\Models\Table();
        $table->shop_id = $user->shop_id;
        $table->name = $request->name;
        $table->qr_code_url = $request->qr_code_url;
        $table->save();"""

content = content.replace(old_create, new_create)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated DashboardController saveTable to avoid mass assignment")
