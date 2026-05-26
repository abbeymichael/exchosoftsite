<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Log every API request for analytics
        $middleware->appendToGroup('api', \App\Http\Middleware\LogApiRequest::class);
         $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Track last-login for web auth
        $middleware->appendToGroup('web', \App\Http\Middleware\TrackLastLogin::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
