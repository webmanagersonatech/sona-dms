<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp_code',
        'purpose',
        'user_id',
        'file_id',
        'transfer_id',
        'device_id',
        'ip_address',
        'metadata',
        'expires_at',
        'verified_at',
        'is_used',
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    public function markAsUsed()
    {
        $this->is_used = true;
        $this->verified_at = now();
        $this->save();
    }

    public static function generateCode($length = 6)
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}