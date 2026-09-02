<?php
$content = file_get_contents("routes/web.php");
$useStatement = "use App\Http\Controllers\Admin\CashRegisterController;\n";

// Add use statement if not exists
if (strpos($content, "use App\Http\Controllers\Admin\CashRegisterController;") === false) {
    $content = preg_replace('/(use App\\\\Http\\\\Controllers\\\\Admin\\\\DashboardController;)/', "$1\n" . $useStatement, $content);
}

// Add routes
$routesBlock = <<<'PHP'
    // Cash Register (Shift) Routes
    Route::post('/shift/open', [CashRegisterController::class, 'openShift'])->name('admin.shift.open');
    Route::post('/shift/close', [CashRegisterController::class, 'closeShift'])->name('admin.shift.close');
PHP;

if (strpos($content, "/shift/open") === false) {
    $content = preg_replace('/(Route::get\(\'\/dashboard\', \[DashboardController::class, \'index\'\]\)->name\(\'admin\.dashboard\'\);)/', "$1\n\n    " . $routesBlock, $content);
}

file_put_contents("routes/web.php", $content);
echo "Routes updated!\n";
