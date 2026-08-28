<?php

use Illuminate\Support\Facades\Route;
use App\Models\Shop;

Route::get('/', function () {
    return view('welcome_saas');
});

Route::get('/admin/dashboard', function () {
    return view('Admin.Dashboard.dashboard');
});

Route::get('/{shop_slug}/menu', function ($shop_slug) {
    $shop = Shop::where('slug', $shop_slug)->firstOrFail();
    $products = App\Models\Product::where('shop_id', $shop->id)->get();
    return view('Customer.Menu.catalog_menu', compact('shop', 'products'));
});
