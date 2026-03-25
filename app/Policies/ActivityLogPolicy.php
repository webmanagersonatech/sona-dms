<?php
// app/Policies/ActivityLogPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityLogPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }

    public function view(User $user, ActivityLog $activityLog)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentAdmin()) {
            return $activityLog->user->department_id === $user->department_id;
        }

        return $user->id === $activityLog->user_id;
    }

    public function export(User $user)
    {
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }

    public function cleanup(User $user)
    {
        return $user->isSuperAdmin();
    }

    public function viewStats(User $user)
    {
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }


}