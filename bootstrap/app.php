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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'             => \App\Http\Middleware\RoleMiddleware::class,
            'check.session'    => \App\Http\Middleware\CheckSession::class,
            'auth.staff'       => \App\Http\Middleware\AuthenticateStaff::class,
            'staff.permission' => \App\Http\Middleware\CheckStaffPermission::class,
        ]);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            // AJAX / JSON requests → 401 (don't redirect to HTML page)
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $guard = 'institute';
            if (str_starts_with($request->path(), 'owner/')) {
                $guard = 'owner';
            } elseif (str_starts_with($request->path(), 'student/')) {
                $guard = 'student';
            } elseif (str_starts_with($request->path(), 'staff/')) {
                $guard = 'staff';
            } elseif (str_starts_with($request->path(), 'franchise/')) {
                // Franchise users authenticate via the 'institute' guard,
                // but we use 'franchise' as a label so session-expired page
                // redirects them to /franchise/login (not the institute /login).
                $guard = 'franchise';
            }

            return url('/session-expired') . '?guard=' . $guard . '&reason=unauthenticated';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
