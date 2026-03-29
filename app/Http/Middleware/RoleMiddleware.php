<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Owner panel
        if (in_array('owner', $roles)) {
            if (!Auth::guard('web')->check()) {
                return redirect()->route('login');
            }
            $admin = Auth::guard('web')->user();
            if ($admin->status === 'inactive') {
                Auth::guard('web')->logout();
                return redirect()->route('login')->withErrors(['login' => 'Account deactivated.']);
            }
            return $next($request);
        }

        // Institute panel
        if (!Auth::guard('institute')->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard('institute')->user();
        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized');
        }
        if ($user->status === 'inactive') {
            Auth::guard('institute')->logout();
            return redirect()->route('login')->withErrors(['login' => 'Account deactivated.']);
        }
        return $next($request);
    }
}