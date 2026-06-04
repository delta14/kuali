<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureSuperAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ CSRF: excluimos el endpoint público del menú
        $middleware->validateCsrfTokens(except: [
            'm/orders',  // <-- sin slash inicial
        ]);

        // Middlewares de la pila "web" (además de los que pone Laravel por defecto)
        $middleware->web([
            \App\Http\Middleware\SetCurrentBusiness::class,
        ]);

        // Alias de middlewares por nombre
        $middleware->alias([
            'superadmin' => EnsureSuperAdmin::class,
            // aquí puedes tener más alias...
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
