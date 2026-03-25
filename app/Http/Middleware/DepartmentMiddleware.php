<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

class DepartmentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
<<<<<<< HEAD
        $user = Auth::user();
        
=======
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin bypass
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

<<<<<<< HEAD
        // If user is department admin, they can only access their department
        if ($user->isDepartmentAdmin()) {
            $requestedDepartmentId = $request->route('department') ?? 
                                    $request->input('department_id');
            
            if ($requestedDepartmentId && $requestedDepartmentId != $user->department_id) {
                abort(403, 'You can only access your own department.');
=======
        // Check department access for resources
        if ($request->route('department')) {
            $departmentId = $request->route('department');
            if ($user->department_id != $departmentId) {
                return redirect()->route('dashboard')->with('error', 'Access restricted to your department.');
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
            }
        }

        return $next($request);
    }
}