<?php

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
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'has_company' => \App\Http\Middleware\EnsureHasCompany::class,
        ]);

        $middleware->prependToGroup('web', [
            \App\Http\Middleware\CheckInstalled::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsureUserNotSuspended::class,
            \App\Http\Middleware\EnsureHasCompany::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
