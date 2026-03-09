<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'employee_id',
        'phone',
        'is_active',
        'is_locked',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'two_factor_recovery_codes' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function files()
    {
        return $this->hasMany(File::class, 'owner_id');
    }

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'receiver_id');
    }

    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

public function hasPermission(string $permission): bool
{
    // Super admin bypass
    if ($this->role?->slug === 'super-admin') {
        return true;
    }

    // Permissions are already cast to array in Role model
    $permissions = $this->role->permissions ?? [];

    // Wildcard support
    if (in_array('*', $permissions, true)) {
        return true;
    }

    return in_array($permission, $permissions, true);
}



    public function isSuperAdmin()
    {
        return $this->role->slug === 'super-admin';
    }

    public function isOwner()
    {
        return $this->role->slug === 'owner';
    }

    public function isSender()
    {
        return $this->role->slug === 'sender';
    }

    public function isReceiver()
    {
        return $this->role->slug === 'receiver';
    }

    public function isThirdParty()
    {
        return $this->role->slug === 'third-party';
    }
}