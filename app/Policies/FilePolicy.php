<?php
// app/Policies/FilePolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\File;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, File $file)
    {
        return $user->canAccessFile($file);
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, File $file)
    {
        return $user->id === $file->owner_id || $user->isSuperAdmin();
    }

    public function delete(User $user, File $file)
    {
        return $user->id === $file->owner_id || $user->isSuperAdmin();
    }
}