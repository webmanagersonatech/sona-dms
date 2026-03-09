<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DepartmentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check department access for resources
        if ($request->route('department')) {
            $departmentId = $request->route('department');
            if ($user->department_id != $departmentId) {
                return redirect()->route('dashboard')->with('error', 'Access restricted to your department.');
            }
        }

        return $next($request);
    }
}