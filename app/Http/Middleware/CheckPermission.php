<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            return redirect()->route('dashboard')->with('error', 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}