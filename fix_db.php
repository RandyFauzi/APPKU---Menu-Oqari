<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('migrations')->where('migration', '2026_08_28_104623_add_role_to_users_table')->delete();

if (!Schema::hasColumn('users', 'role')) {
    Schema::table('users', function ($table) {
        $table->string('role')->default('admin');
    });
    echo "Column added successfully.";
} else {
    echo "Column already exists.";
}
