<?php

use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\DealerAuthenticated;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfUserAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            CheckMaintenanceMode::class,
            SecurityHeaders::class,
            HandleInertiaRequests::class,
        ]);
        $middleware->alias([
            'dealer.auth' => DealerAuthenticated::class,
            'redirect.if.auth' => RedirectIfUserAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
