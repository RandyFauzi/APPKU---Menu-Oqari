<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('migrations')->insert([
    'migration' => '2026_08_28_104623_add_role_to_users_table',
    'batch' => DB::table('migrations')->max('batch')
]);
echo "Migration logged.";
