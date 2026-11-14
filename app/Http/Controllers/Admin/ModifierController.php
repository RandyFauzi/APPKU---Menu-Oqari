<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Modifiers\DeleteModifierGroupAction;
use App\Actions\Modifiers\SaveModifierGroupAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveModifierGroupRequest;
use App\Models\ModifierGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModifierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $shopId = Auth::user()->shop_id;
        abort_unless($shopId, 403, 'Akses ditolak.');

        $groups = ModifierGroup::where('shop_id', $shopId)
            ->with(['modifiers', 'products:id,name,price,category_id'])
            ->withCount('products')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'groups' => $groups,
        ]);
    }

    public function store(SaveModifierGroupRequest $request, SaveModifierGroupAction $action): JsonResponse
    {
        $shop = Auth::user()->shop;
        abort_unless($shop, 403);

        $group = $action->execute($shop, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Grup addon "'.$group->name.'" berhasil dibuat.',
            'group' => $group,
        ]);
    }

    public function update(SaveModifierGroupRequest $request, int $id, SaveModifierGroupAction $action): JsonResponse
    {
        $shop = Auth::user()->shop;
        abort_unless($shop, 403);

        $validated = $request->validated();
        $validated['id'] = $id;

        $group = $action->execute($shop, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Grup addon "'.$group->name.'" berhasil diperbarui.',
            'group' => $group,
        ]);
    }

    public function destroy(int $id, DeleteModifierGroupAction $action): JsonResponse
    {
        $shop = Auth::user()->shop;
        abort_unless($shop, 403);

        $action->execute($shop, $id);

        return response()->json([
            'success' => true,
            'message' => 'Grup addon berhasil dihapus.',
        ]);
    }

    public function toggle(int $id): JsonResponse
    {
        $shopId = Auth::user()->shop_id;
        abort_unless($shopId, 403);

        $group = ModifierGroup::where('shop_id', $shopId)->findOrFail($id);
        $group->is_active = ! $group->is_active;
        $group->save();

        return response()->json([
            'success' => true,
            'is_active' => $group->is_active,
            'message' => 'Status grup addon berhasil diubah.',
        ]);
    }
}
