<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Register custom middleware alias
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        // Trust the reverse proxy / load balancer in front of production
        // (e.g. Cloudflare, cPanel's proxy, a CDN) so Laravel correctly detects
        // HTTPS. Without this, Laravel can think a request is plain HTTP even
        // though the browser is on HTTPS, which breaks secure session cookies
        // and causes CSRF/419 errors that only ever show up in production.
        $middleware->trustProxies(at: '*');
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        // A 419 (expired/stale CSRF token) should never show Laravel's raw
        // "Page Expired" page. Show our own branded, responsive page with a
        // "Go to Login" button instead (resources/views/errors/419.blade.php).
        // The login route's built-in "guest" middleware will automatically
        // bounce an already-authenticated user straight to the dashboard.
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired. Please try again.',
                ], 419);
            }

            return response()->view('errors.419', [], 419);
        });
    })->create();
