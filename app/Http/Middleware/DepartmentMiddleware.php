<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // If user is department admin, they can only access their department
        if ($user->isDepartmentAdmin()) {
            $requestedDepartmentId = $request->route('department') ?? 
                                    $request->input('department_id');
            
            if ($requestedDepartmentId && $requestedDepartmentId != $user->department_id) {
                abort(403, 'You can only access your own department.');
            }
        }

        return $next($request);
    }
}