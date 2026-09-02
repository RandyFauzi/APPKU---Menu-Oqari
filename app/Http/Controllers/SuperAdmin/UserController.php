<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Shop;
use App\Models\SuperAdminAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('shop')
            ->when($request->query('user_search'), function ($q, $term) {
                $q->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('SuperAdmin.users.index', compact('users'));
    }

    public function create()
    {
        $shops = Shop::orderBy('name')->get();
        return view('SuperAdmin.users.create', compact('shops'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['superadmin', 'owner', 'admin', 'crew', 'kitchen', 'cashier', 'barista'])],
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('superadmin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $shops = Shop::orderBy('name')->get();
        return view('SuperAdmin.users.edit', compact('user', 'shops'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['superadmin', 'owner', 'admin', 'crew', 'kitchen', 'cashier', 'barista'])],
            'shop_id' => 'nullable|exists:shops,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // redirect back to where they came from (could be shop show page or users index)
        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))->with('success', 'Pengguna berhasil diperbarui.');
        }

        return redirect()->route('superadmin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->role === 'superadmin' && User::where('role', 'superadmin')->count() === 1) {
            return back()->with('error', 'Tidak bisa menghapus satu-satunya super admin.');
        }

        SuperAdminAuditLog::record(
            action: 'user.deleted',
            targetType: 'User',
            targetId: $user->id,
            targetLabel: "{$user->name} ({$user->email})",
            meta: ['role' => $user->role, 'shop_id' => $user->shop_id],
        );

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
