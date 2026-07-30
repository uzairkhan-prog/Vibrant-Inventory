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
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        // A 419 (expired/stale CSRF token) should never dead-end the user with
        // Laravel's raw "Page Expired" page. Instead, bounce them back to the
        // same form (which now has a fresh token) with their input preserved
        // and a friendly message, on every device/browser.
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired. Please try again.',
                ], 419);
            }

            return redirect()
                ->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['csrf' => 'Your session expired, please try again.']);
        });
    })->create();
