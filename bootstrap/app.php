<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/central-app.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectGuestsTo('/login');  

        $middleware->alias([
            'role'          => \App\Http\Middleware\CheckRole::class,
            'permission'    => \App\Http\Middleware\CheckPermission::class,
            'church.tenant' => \App\Http\Middleware\EnsureChurchTenant::class,
            'subscription'  => \App\Http\Middleware\CheckSubscriptionLimit::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();