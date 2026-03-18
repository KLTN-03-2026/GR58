<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware chạy cho tất cả request - phải chạy TRƯỚC auth:sanctum
        $middleware->prependToGroup('api', \App\Http\Middleware\ResolveTokenableUser::class);

        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'staff.only' => \App\Http\Middleware\StaffOnly::class,
            'auth.multi' => \App\Http\Middleware\AuthenticateMultiModel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
