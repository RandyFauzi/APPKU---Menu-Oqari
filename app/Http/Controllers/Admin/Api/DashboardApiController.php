<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardApiController extends Controller
{
    public function getSummary(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (! $shopId) {
            return response()->json(['error' => 'No shop assigned'], 403);
        }

        $categories = $request->user()->can('manage-menu') ? Category::where('shop_id', $shopId)->orderBy('sort_order')->get() : collect();
        $tables = $request->user()->canAny(['view-orders', 'manage-settings']) ? Table::where('shop_id', $shopId)->get() : collect();
        $userColumns = ['id', 'name', 'role'];
        if (Schema::hasColumn('users', 'is_active')) {
            $userColumns[] = 'is_active';
        }
        $users = $request->user()->canAny(['manage-crew', 'manage-users']) ? User::where('shop_id', $shopId)->where('role', '!=', 'superadmin')->get($userColumns)->map(function (User $user): array {
            return array_merge($user->only(['id', 'name', 'role']), ['is_active' => $user->isAccountActive()]);
        }) : collect();

        return response()->json([
            'categories' => $categories,
            'tables' => $tables,
            'users' => $users,
        ]);
    }
}
