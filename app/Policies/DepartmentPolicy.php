<?php
// app/Policies/DepartmentPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * This method is called before any other policy methods.
     * It gives super admin access to everything.
     */
    public function before(User $user, $ability)
    {
        // Check if user has role and is super admin
        if ($user->role && $user->role->slug === 'super-admin') {
            return true;
        }
        
        // Return null to allow other policy methods to run
        return null;
    }

    public function viewAny(User $user)
    {
        return $user->isDepartmentAdmin();
    }

    public function view(User $user, Department $department)
    {
        if ($user->isDepartmentAdmin() && $user->department_id === $department->id) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        return false; // Only super admin can create, handled by before()
    }

    public function update(User $user, Department $department)
    {
        return false; // Only super admin can update, handled by before()
    }

    public function delete(User $user, Department $department)
    {
        return false; // Only super admin can delete, handled by before()
    }

    public function assignAdmin(User $user, Department $department)
    {
        return false; // Only super admin can assign admin, handled by before()
    }
}