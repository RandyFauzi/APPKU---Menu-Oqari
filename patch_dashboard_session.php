<?php
$content = file_get_contents("app/Http/Controllers/Admin/DashboardController.php");

$find = "\$users = User::where('shop_id', \$shop->id)->get();";
$replace = "\$users = User::where('shop_id', \$shop->id)->get();
        \$activeSession = \App\Models\CashRegisterSession::where('user_id', \$user->id)->where('status', 'OPEN')->first();";

$content = str_replace($find, $replace, $content);

$find2 = "return view('Admin.Dashboard.dashboard', compact('shop', 'orders', 'menuItems', 'categories', 'tables', 'users', 'analytics'));";
$replace2 = "return view('Admin.Dashboard.dashboard', compact('shop', 'orders', 'menuItems', 'categories', 'tables', 'users', 'analytics', 'activeSession'));";

$content = str_replace($find2, $replace2, $content);

file_put_contents("app/Http/Controllers/Admin/DashboardController.php", $content);
echo "DashboardController updated!\n";
