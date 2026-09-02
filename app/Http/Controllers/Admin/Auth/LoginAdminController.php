<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginAdminController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('Admin.Auth.login_admin');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = $request->user()->role;

        $destination = match ($role) {
            'cashier' => route('admin.pos.index', absolute: false),
            'barista', 'kitchen' => route('admin.kitchen.index', absolute: false),
            'crew' => route('admin.my-schedule', absolute: false),
            'superadmin' => route('superadmin.dashboard', absolute: false),
            default => route('admin.dashboard', absolute: false), // owner, manager
        };

        return redirect()->intended($destination);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
