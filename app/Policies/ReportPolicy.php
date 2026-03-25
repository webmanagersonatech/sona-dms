<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any reports
     */
    public function viewAny(User $user): bool
    {
        // Super admin can always view reports
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Department admin can view reports
        if ($user->isDepartmentAdmin()) {
            return true;
        }

        // Check if user has permission via role or direct permission
        if ($user->hasPermission('view-reports')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can view file reports
     */
    public function viewFileReports(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine if the user can view transfer reports
     */
    public function viewTransferReports(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine if the user can view user reports
     */
    public function viewUserReports(User $user): bool
    {
        // Only super admin and department admin can view user reports
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }

    /**
     * Determine if the user can export reports
     */
    public function export(User $user): bool
    {
        return $this->viewAny($user);
    }
}