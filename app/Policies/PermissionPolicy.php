<?php
// app/Policies/PermissionPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any permissions.
     */
    public function viewAny(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view the permission.
     */
    public function view(User $user, Permission $permission)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can create permissions.
     */
    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can update the permission.
     */
    public function update(User $user, Permission $permission)
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine if the user can delete the permission.
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->isSuperAdmin();
    }
}