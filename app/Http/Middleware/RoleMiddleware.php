<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($user->role->slug === $role) {
                return $next($request);
            }
        }

        // Check department access for file routes
        if ($request->route('file')) {
            $file = $request->route('file');
            if ($file->department_id === $user->department_id) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
    }
}