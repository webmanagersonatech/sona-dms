<?php
// app/Policies/TransferPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\Transfer;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Transfer $transfer)
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isDepartmentAdmin()) {
            return $transfer->sender->department_id === $user->department_id ||
                   $transfer->receiver?->department_id === $user->department_id;
        }

        return $user->id === $transfer->sender_id || 
               $user->id === $transfer->receiver_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Transfer $transfer)
    {
        return $user->isSuperAdmin() || $user->id === $transfer->sender_id;
    }

    public function delete(User $user, Transfer $transfer)
    {
        return $user->isSuperAdmin();
    }
}