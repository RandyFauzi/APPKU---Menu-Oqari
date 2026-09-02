<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\SuperAdmin\ActivateShopAction;
use App\Actions\SuperAdmin\DeleteShopAction;
use App\Actions\SuperAdmin\SuspendShopAction;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $shops = Shop::withCount(['users', 'orders'])
            ->search($request->query('search'))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('SuperAdmin.shops.index', compact('shops'));
    }

    public function show(Shop $shop)
    {
        $shop->loadCount(['users', 'orders']);
        $users = $shop->users()->latest()->get();
        return view('SuperAdmin.shops.show', compact('shop', 'users'));
    }

    public function edit(Shop $shop)
    {
        return view('SuperAdmin.shops.edit', compact('shop'));
    }

    public function update(Request $request, Shop $shop)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shops,slug,' . $shop->id,
            'whatsapp_number' => 'nullable|string|max:255',
            'slogan' => 'nullable|string|max:255',
        ]);

        $shop->update($validated);

        return redirect()->route('superadmin.shops.show', $shop)->with('success', 'Informasi toko berhasil diperbarui.');
    }

    public function suspend(Request $request, Shop $shop, SuspendShopAction $action)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $action->execute($shop, $request->string('reason'));
        return back()->with('success', "{$shop->name} berhasil di-suspend.");
    }

    public function activate(Shop $shop, ActivateShopAction $action)
    {
        $action->execute($shop);
        return back()->with('success', "{$shop->name} berhasil diaktifkan kembali.");
    }

    public function destroy(Request $request, Shop $shop, DeleteShopAction $action)
    {
        $request->validate(['confirm_slug' => 'required|string']);

        try {
            $action->execute($shop, $request->string('confirm_slug'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->with('error', $e->getMessage());
        }

        return redirect()->route('superadmin.shops.index')->with('success', 'Toko berhasil dihapus permanen.');
    }
}
