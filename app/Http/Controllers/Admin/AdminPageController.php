<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RenderAdminPage;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function index(Request $request, RenderAdminPage $renderPage): View|RedirectResponse
    {
        $legacyTab = $request->query('tab');
        if ($legacyTab !== null) {
            $aliases = ['profile' => 'profile.edit', 'crew' => 'admin.users.index', 'analytics' => 'admin.dashboard'];
            $destination = is_string($legacyTab) ? ($aliases[$legacyTab] ?? config('admin_pages.'.$legacyTab.'.route')) : null;
            abort_unless($destination, 404);

            if ($request->routeIs('admin.dashboard') || ! $request->routeIs($destination)) {
                return redirect()->route($destination, $request->except('tab', 'page'));
            }
        }
        $page = $request->route('page', 'dashboard');

        $role = Auth::user()->role;

        if ($role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        if ($page === 'dashboard' && Auth::user()->homeRoute() !== 'admin.dashboard') {
            return redirect()->route(Auth::user()->homeRoute());
        }

        $user = Auth::user();

        // JIKA USER BARU DAFTAR & BELUM PUNYA SHOP
        if (! $user->shop_id) {
            $baseSlug = Str::slug($user->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Shop::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            $newShop = Shop::create([
                'name' => $user->name.' Shop',
                'slug' => $slug,
                'primary_color' => '#1E5A7A', // Default color
                'onboarding_status' => 'pending',
                'onboarding_step' => 'welcome',
                'status' => 'onboarding', // using the existing status column as well for clarity
            ]);

            $user->update([
                'shop_id' => $newShop->id,
                'role' => 'owner', // Jadikan dia owner dari tokonya sendiri
            ]);
        }

        $shopId = $user->shop_id;
        $shop = Shop::find($shopId);

        // ONBOARDING GUARD
        // Hanya Owner dan Manager yang bisa mengakses / melanjutkan onboarding.
        if ($shop && $shop->onboarding_status !== 'completed' && $user->can('manage-settings')) {
            return redirect()->route('admin.onboarding');
        }

        $user->setRelation('shop', $shop);

        return $renderPage->execute($user, $page);
    }
}
