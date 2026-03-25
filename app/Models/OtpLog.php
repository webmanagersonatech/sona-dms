<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'otp_code', 'purpose', 'file_id', 'target_user_id',
        'target_email', 'expires_at', 'verified_at', 'status', 'attempts',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function isValid()
    {
        return $this->status === 'pending' && 
               $this->expires_at > now() &&
               $this->attempts < 3;
    }

    public function isExpired()
    {
        return $this->expires_at <= now();
    }
}