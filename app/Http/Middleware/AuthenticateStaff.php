<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('staff')->user();

        if (!$user || $user->role !== 'staff' || $user->status !== 'active') {
            Auth::guard('staff')->logout();
            return redirect()->route('staff.login')
                ->with('error', 'Please log in to access the staff panel.');
        }

        return $next($request);
    }
}
