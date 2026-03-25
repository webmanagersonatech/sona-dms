<?php
// app/Policies/RolePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any roles.
     */
    public function viewAny(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the role.
     */
    public function view(User $user, Role $role)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can update the role.
     */
    public function update(User $user, Role $role)
    {
        // Prevent editing Super Admin role
        if ($role->slug === 'super-admin') {
            return false;
        }
        
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can delete the role.
     */
    public function delete(User $user, Role $role)
    {
        // Prevent deleting Super Admin role
        if ($role->slug === 'super-admin') {
            return false;
        }
        
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can manage role permissions.
     */
    public function managePermissions(User $user, Role $role)
    {
        return $user->isSuperAdmin();
    }
}