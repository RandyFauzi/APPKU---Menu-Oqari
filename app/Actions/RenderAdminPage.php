<?php

namespace App\Actions;

use App\Models\CashRegisterSession;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\SchemaCompatibility;
use Illuminate\View\View;

class RenderAdminPage
{
    public function __construct(private SchemaCompatibility $schema) {}

    /** @param array<string, mixed> $data */
    public function execute(User $user, string $page, array $data = []): View
    {
        $definition = config('admin_pages.'.$page);
        abort_unless(is_array($definition) && $user->shop_id, 404);
        $shop = $user->shop;
        abort_unless($shop, 404);
        $activeSession = $user->can('manage-register') && $this->schema->missing(['register']) === []
            ? CashRegisterSession::whereHas('register', fn ($query) => $query->where('shop_id', $shop->id))->where('user_id', $user->id)->where('status', 'OPEN')->first()
            : null;
        $categories = $page === 'menu' ? Category::where('shop_id', $shop->id)->orderBy('sort_order')->get() : collect();
        $shopProducts = ($user->can('manage-settings') || $page === 'settings')
            ? Product::where('shop_id', $shop->id)->with('category:id,name')->select('id', 'shop_id', 'category_id', 'name', 'price', 'is_best_seller', 'image_path')->orderBy('name')->get()
            : collect();
        $dashboardShop = $user->can('manage-settings') ? $shop->toArray() : $shop->only(['id', 'name', 'slug', 'logo_url', 'primary_color', 'sales_modes']);
        $pageView = $definition['view'];
        $pageLabel = $definition['label'];
        $pageLinks = collect(config('admin_pages'))->filter(fn (array $item): bool => $user->can($item['ability']))
            ->map(fn (array $item, string $id): array => ['id' => $id, 'name' => $item['label'], 'url' => route($item['route'])])->values()->all();

        return view('Admin.Dashboard.dashboard', array_merge($data, compact('shop', 'activeSession', 'categories', 'dashboardShop', 'shopProducts', 'page', 'pageView', 'pageLabel', 'pageLinks')));
    }
}
