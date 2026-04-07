<?php
// app/Policies/UserPolicy.php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }

    public function view(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentAdmin() && $user->department_id === $model->department_id) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function create(User $user)
    {
        return $user->isSuperAdmin() || $user->isDepartmentAdmin();
    }

    public function update(User $user, User $model)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentAdmin() && $user->department_id === $model->department_id) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model)
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentAdmin() && $user->department_id === $model->department_id) {
            return true;
        }

        return false;
    }
}