import re

with open("app/Http/Controllers/ShopController.php", "r", encoding="utf-8") as f:
    content = f.read()

# Update validation rules
old_validation = """        $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ]);"""

new_validation = """        $request->validate([
            'table_id' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1|max:50',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:100', // Mencegah spam qty jutaan
            'items.*.notes' => 'nullable|string|max:200',
            // Variant dan Modifier validation placeholder jika struktur JSON dikirimkan
        ]);"""

content = content.replace(old_validation, new_validation)

# Update product checks
old_product_check = """            $product = Product::where('id', $item['id'])
                ->where('shop_id', $shop->id)
                ->first();

            if (! $product) {
                continue;
            }"""
            
new_product_check = """            // Anti-manipulasi harga & status produk
            $product = Product::where('id', $item['id'])
                ->where('shop_id', $shop->id)
                ->where('is_sold_out', false)
                ->first();

            if (! $product) {
                return response()->json(['success' => false, 'message' => 'Beberapa produk tidak tersedia atau telah habis.'], 400);
            }"""

content = content.replace(old_product_check, new_product_check)

with open("app/Http/Controllers/ShopController.php", "w", encoding="utf-8") as f:
    f.write(content)
