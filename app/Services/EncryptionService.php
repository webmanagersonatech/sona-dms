<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class EncryptionService
{
    protected $key;

    public function __construct()
    {
        $this->key = config('app.key');
    }

    public function encrypt($data)
    {
        return Crypt::encryptString($data);
    }

    public function decrypt($data, $key = null)
    {
        return Crypt::decryptString($data);
    }

    public function getKey()
    {
        return $this->key;
    }
}