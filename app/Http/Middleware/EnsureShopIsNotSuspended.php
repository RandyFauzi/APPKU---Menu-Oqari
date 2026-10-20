<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShopIsNotSuspended
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('logout') && $request->user()?->role !== 'superadmin' && $request->user()?->shop?->isSuspended()) {
            abort(403, 'Toko dinonaktifkan. Hubungi administrator platform.');
        }

        return $next($request);
    }
}
