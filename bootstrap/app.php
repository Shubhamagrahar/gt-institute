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
            if (str_starts_with($request->path(), 'student/')) {
                return route('student.login');
            }
            if (str_starts_with($request->path(), 'staff/')) {
                return route('staff.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
