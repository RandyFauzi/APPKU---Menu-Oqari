<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return $next($request);
        }

        return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Anda bukan Super Admin.');
    }
}
