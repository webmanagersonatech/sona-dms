<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'location',
        'last_login_at',
        'last_activity_at',
        'is_active',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}