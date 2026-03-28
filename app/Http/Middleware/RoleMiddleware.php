<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }

        // Inactive accounts
        if ($user->status === 'inactive') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been deactivated. Contact support.');
        }

        return $next($request);
    }
}
