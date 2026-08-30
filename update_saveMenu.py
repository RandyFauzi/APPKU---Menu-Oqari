import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

new_saveMenu = r"""    public function saveMenu(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $shopId = $user->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_name' => $request->categoryId,
            'description' => $request->desc,
        ];

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('menus', 'public');
        }

        $menu = \App\Models\Product::updateOrCreate(
            ['id' => $request->id, 'shop_id' => $shopId],
            $data
        );
        return response()->json(['success' => true, 'menu' => $menu]);
    }"""

# Use regex to replace the old saveMenu function
pattern = r"public function saveMenu\(Request \$request\).*?return response\(\)->json\(\['success' => true, 'menu' => \$menu\]\);\s*\}"
content = re.sub(pattern, new_saveMenu.replace('\\', '\\\\'), content, flags=re.DOTALL)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated saveMenu to handle image uploads")
