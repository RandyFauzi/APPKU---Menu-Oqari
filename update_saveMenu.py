import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_saveMenu = '''    public function saveMenu(Request )
    {
         = \Illuminate\Support\Facades\Auth::user();
         = ->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
         = \App\Models\Product::updateOrCreate(
            ['id' => ->id, 'shop_id' => ],
            [
                'name' => ->name,
                'price' => ->price,
                'category_name' => ->categoryId,
                'description' => ->desc,
            ]
        );
        return response()->json(['success' => true, 'menu' => ]);
    }'''

new_saveMenu = '''    public function saveMenu(Request )
    {
         = \Illuminate\Support\Facades\Auth::user();
         = \App\Models\Shop::find(->shop_id) ?? \App\Models\Shop::first() ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop']);
         = ->id;
        
         = [
            'name' => ->name,
            'price' => ->price,
            'category_name' => ->categoryId,
            'description' => ->desc,
        ];
        
         = null;
        if (->id) {
             = \App\Models\Product::where('id', ->id)->where('shop_id', )->first();
        }

        if (->hasFile('image')) {
             = ->file('image');
             = \Illuminate\Support\Str::slug(->name);
             = ->getClientOriginalExtension();
             = "{}.{}";
             = "shops/{->slug}/menus";
            
             = ->storeAs(, , 'public');
            ['image_url'] = ;

            if ( && ->image_url && ->image_url !== ) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(->image_url);
            }
        }

        if () {
            ->update();
        } else {
            ['shop_id'] = ;
             = \App\Models\Product::create();
        }
        
        return response()->json(['success' => true, 'menu' => ]);
    }'''

if old_saveMenu in content:
    content = content.replace(old_saveMenu, new_saveMenu)
    with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Updated saveMenu")
else:
    print("Could not find saveMenu")
