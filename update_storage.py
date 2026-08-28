import re

with open('app/Http/Controllers/Admin/DashboardController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace saveMenuBulk
old_bulk = '''    public function saveMenuBulk(Request )
    {
         = \Illuminate\Support\Facades\Auth::user();
         = ->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;

         = ->input('items', []);
         = [];

        foreach ( as  => ) {
            // Check if there's an image file for this specific index
             = null;
            if (->hasFile("images.{}")) {
                 = ->file("images.{}")->store('menus', 'public');
            }

            // Validasi sederhana
            if (empty(['name']) || empty(['price'])) {
                continue; // Lewati item yang kosong
            }

             = [
                'name' => ['name'],
                'price' => ['price'],
                'category_name' => ['category_name'] ?? 'Uncategorized',
                'description' => ['description'] ?? null,
            ];

            if () {
                ['image_url'] = ;
            }

            // Jika ada id, berarti update, jika tidak, create
            if (!empty(['id'])) {
                 = \App\Models\Product::where('id', ['id'])->where('shop_id', )->first();
                if () {
                    ->update();
                    [] = ;
                }
            } else {
                ['shop_id'] = ;
                 = \App\Models\Product::create();
                [] = ;
            }
        }

        return response()->json(['success' => true, 'menus' => ]);
    }'''

new_bulk = '''    public function saveMenuBulk(Request )
    {
         = \Illuminate\Support\Facades\Auth::user();
         = \App\Models\Shop::find(->shop_id) ?? \App\Models\Shop::first() ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop']);
         = ->id;
         = ->slug;

         = ->input('items', []);
         = [];

        foreach ( as  => ) {
            // Validasi sederhana
            if (empty(['name']) || empty(['price'])) {
                continue; // Lewati item yang kosong
            }

             = [
                'name' => ['name'],
                'price' => ['price'],
                'category_name' => ['category_name'] ?? 'Uncategorized',
                'description' => ['description'] ?? null,
            ];

             = null;
            if (!empty(['id'])) {
                 = \App\Models\Product::where('id', ['id'])->where('shop_id', )->first();
            }

            // Handle image upload logic
            if (->hasFile("images.{}")) {
                 = ->file("images.{}");
                
                // Name format: shops/{shop_slug}/menus/{menu-slug}.{ext}
                 = \Illuminate\Support\Str::slug(['name']);
                 = ->getClientOriginalExtension();
                 = "{}.{}";
                 = "shops/{}/menus";
                
                // storeAs will naturally overwrite if file exists with same name
                 = ->storeAs(, , 'public');
                ['image_url'] = ;

                // Cleanup old image if it exists and path is different (e.g. extension changed or menu renamed)
                if ( && ->image_url && ->image_url !== ) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete(->image_url);
                }
            }

            if () {
                ->update();
                [] = ;
            } else {
                ['shop_id'] = ;
                 = \App\Models\Product::create();
                [] = ;
            }
        }

        return response()->json(['success' => true, 'menus' => ]);
    }'''

content = content.replace(old_bulk, new_bulk)

# Replace saveSettings
old_settings = '''    public function saveSettings(Request )
    {
        ->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048'
        ]);

         = \Illuminate\Support\Facades\Auth::user();
         = ->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
        if (!->shop_id) {
            ->update(['shop_id' => ]);
        }

         = \App\Models\Shop::find();
        if (!) {
            return response()->json(['success' => false, 'message' => 'Shop not found.']);
        }

        ->name = ->name;
        ->slug = \Illuminate\Support\Str::slug(->slug);
        
        if (->filled('primary_color')) {
            ->primary_color = ->primary_color;
        }

        if (->hasFile('logo')) {
             = ->file('logo')->store('logos', 'public');
            ->logo_url = ;
        }

        ->save();

        return response()->json([
            'success' => true,
            'logo_url' => ->logo_url
        ]);
    }'''

new_settings = '''    public function saveSettings(Request )
    {
        ->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'primary_color' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048'
        ]);

         = \Illuminate\Support\Facades\Auth::user();
         = ->shop_id ?? \App\Models\Shop::first()->id ?? \App\Models\Shop::create(['name' => 'My Shop', 'slug' => 'my-shop'])->id;
        
        if (!->shop_id) {
            ->update(['shop_id' => ]);
        }

         = \App\Models\Shop::find();
        if (!) {
            return response()->json(['success' => false, 'message' => 'Shop not found.']);
        }

        ->name = ->name;
        ->slug = \Illuminate\Support\Str::slug(->slug);
        
        if (->filled('primary_color')) {
            ->primary_color = ->primary_color;
        }

        if (->hasFile('logo')) {
             = ->file('logo');
             = ->getClientOriginalExtension();
             = "logo.{}";
             = "shops/{->slug}/settings";
            
             = ->storeAs(, , 'public');
            
            // Clean up old logo if different
            if (->logo_url && ->logo_url !== ) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(->logo_url);
            }
            
            ->logo_url = ;
        }

        ->save();

        return response()->json([
            'success' => true,
            'logo_url' => ->logo_url
        ]);
    }'''

content = content.replace(old_settings, new_settings)

with open('app/Http/Controllers/Admin/DashboardController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated DashboardController storage logic")
