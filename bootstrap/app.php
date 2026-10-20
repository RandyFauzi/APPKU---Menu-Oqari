<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureShopIsNotSuspended;
use App\Http\Middleware\IsSuperAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', EnsureAccountIsActive::class);
        $middleware->appendToGroup('web', EnsureShopIsNotSuspended::class);
        $middleware->appendToGroup('api', EnsureShopIsNotSuspended::class);
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->redirectUsersTo(function (Request $request) {
            return route($request->user()?->homeRoute() ?? 'admin.login');
        });
        $middleware->alias([
            'superadmin' => IsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
