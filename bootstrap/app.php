<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\GenerateBreadcrumbs;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin-Routen registrieren
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Standard Web-Routen registrieren
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware-Aliase registrieren
        $middleware->alias([
            'breadcrumbs' => GenerateBreadcrumbs::class,
        ]);

        // Globale Middleware hinzufügen
        $middleware->append(SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception-Handling konfigurieren
    })
    ->create();
