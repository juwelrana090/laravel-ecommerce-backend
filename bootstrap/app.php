<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\JWTAuthenticate;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SellerMiddleware;
use App\Http\Middleware\CheckSellerOrAdmin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => Authenticate::class,
            'isAdmin' => AdminMiddleware::class,
            'isSeller' => SellerMiddleware::class,
            'seller_or_admin' => CheckSellerOrAdmin::class,
            'JWTAuthenticate' => JWTAuthenticate::class,
        ]);

        // Define middleware groups
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // Add other middleware for API routes here
        ]);

        $middleware->group('web', [
            // Add middleware for web routes here
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
