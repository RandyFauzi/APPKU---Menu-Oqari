<?php
$migrationsDir = __DIR__ . '/database/migrations/';
$files = scandir($migrationsDir);

foreach ($files as $file) {
    if (strpos($file, 'create_shops_table') !== false) {
        file_put_contents($migrationsDir . $file, "<?php\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\nreturn new class extends Migration {\n    public function up(): void {\n        Schema::create('shops', function (Blueprint \$table) {\n            \$table->id();\n            \$table->string('name');\n            \$table->string('slug')->unique();\n            \$table->string('logo_url')->nullable();\n            \$table->string('primary_color')->default('#1E5A7A');\n            \$table->string('theme_style')->default('modern');\n            \$table->timestamps();\n        });\n    }\n    public function down(): void { Schema::dropIfExists('shops'); }\n};");
    }
    
    if (strpos($file, 'create_users_table') !== false) {
        $content = file_get_contents($migrationsDir . $file);
        $content = str_replace("\$table->id();", "\$table->id();\n            \$table->foreignId('shop_id')->nullable()->constrained()->onDelete('cascade');", $content);
        file_put_contents($migrationsDir . $file, $content);
    }

    if (strpos($file, 'create_products_table') !== false) {
        file_put_contents($migrationsDir . $file, "<?php\nuse Illuminate\Database\Migrations\Migration;\nuse Illuminate\Database\Schema\Blueprint;\nuse Illuminate\Support\Facades\Schema;\nreturn new class extends Migration {\n    public function up(): void {\n        Schema::create('products', function (Blueprint \$table) {\n            \$table->id();\n            \$table->foreignId('shop_id')->constrained()->onDelete('cascade');\n            \$table->string('category_name');\n            \$table->string('name');\n            \$table->decimal('price', 10, 2);\n            \$table->string('image_url')->nullable();\n            \$table->boolean('is_sold_out')->default(false);\n            \$table->timestamps();\n        });\n    }\n    public function down(): void { Schema::dropIfExists('products'); }\n};");
    }
}

file_put_contents(__DIR__ . '/database/seeders/DatabaseSeeder.php', "<?php\nnamespace Database\Seeders;\nuse Illuminate\Database\Seeder;\nuse Illuminate\Support\Facades\Hash;\nuse App\Models\User;\nuse App\Models\Shop;\nuse App\Models\Product;\n\nclass DatabaseSeeder extends Seeder\n{\n    public function run(): void\n    {\n        \$shop1 = Shop::create(['name' => 'Bitten Coffee', 'slug' => 'bitten', 'primary_color' => '#1E5A7A', 'theme_style' => 'modern']);\n        User::create(['name' => 'Admin Bitten', 'email' => 'admin@bitten.com', 'password' => Hash::make('password'), 'shop_id' => \$shop1->id]);\n        Product::create(['shop_id' => \$shop1->id, 'category_name' => 'Coffee', 'name' => 'Vanilla Latte', 'price' => 25000]);\n        \n        \$shop2 = Shop::create(['name' => 'Goodwill Coffee', 'slug' => 'goodwill', 'primary_color' => '#276749', 'theme_style' => 'minimalist']);\n        User::create(['name' => 'Admin Goodwill', 'email' => 'admin@goodwill.com', 'password' => Hash::make('password'), 'shop_id' => \$shop2->id]);\n        Product::create(['shop_id' => \$shop2->id, 'category_name' => 'Signature', 'name' => 'Goodwill Matcha', 'price' => 28000]);\n\n        \$shop3 = Shop::create(['name' => 'Mada Coffee', 'slug' => 'mada', 'primary_color' => '#744210', 'theme_style' => 'classic']);\n        User::create(['name' => 'Admin Mada', 'email' => 'admin@mada.com', 'password' => Hash::make('password'), 'shop_id' => \$shop3->id]);\n        Product::create(['shop_id' => \$shop3->id, 'category_name' => 'Espresso Based', 'name' => 'Mada Americano', 'price' => 20000]);\n    }\n}\n");
