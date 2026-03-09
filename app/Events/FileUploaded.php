<?php

namespace App\Events;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FileUploaded
{
    use Dispatchable, SerializesModels;

    public $file;
    public $user;

    public function __construct(File $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }
}