<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
<<<<<<< HEAD
use Illuminate\Support\Facades\Auth;
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
<<<<<<< HEAD
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (in_array($user->role->slug, $roles)) {
            return $next($request);
        }

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
        abort(403, 'Unauthorized access.');
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}