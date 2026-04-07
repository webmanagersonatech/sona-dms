<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'department_id', 'status',
        'phone', 'avatar', 'two_factor_secret', 'two_factor_recovery_codes',
        'last_login_at', 'last_login_ip'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'settings' => 'array',
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

    public function sharedFiles()
    {
        return $this->belongsToMany(File::class, 'file_shares', 'shared_with', 'file_id')
                    ->withPivot('permission_level', 'expires_at', 'status')
                    ->withTimestamps();
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'receiver_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function otpLogs()
    {
        return $this->hasMany(OtpLog::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withPivot('type')
                    ->withTimestamps();
    }

    public function hasPermission($permissionSlug)
    {
        // Check user-specific permissions first
        $userPermission = $this->permissions()
            ->where('slug', $permissionSlug)
            ->first();

        if ($userPermission) {
            return $userPermission->pivot->type === 'allow';
        }

        // Check role permissions
        return $this->role->permissions()
            ->where('slug', $permissionSlug)
            ->exists();
    }

    public function isSuperAdmin()
    {
        // Add null check to prevent errors
        if (!$this->role) {
            \Log::warning('User has no role', ['user_id' => $this->id, 'email' => $this->email]);
            return false;
        }
        
        return $this->role->slug === 'super-admin';
    }

    public function isDepartmentAdmin()
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->slug === 'department-admin';
    }

    /**
     * Check if user can access a specific file
     * 
     * @param File $file
     * @return bool
     */
    public function canAccessFile(File $file)
    {
        // Super admin can access all files
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Owner can access their own files
        if ($this->id === $file->owner_id) {
            return true;
        }

        // Department admin can access files in their department
        if ($this->isDepartmentAdmin() && $this->department_id === $file->department_id) {
            return true;
        }

        // Check if file is shared with user and share is active
        $share = $this->sharedFiles()
            ->where('file_id', $file->id)
            ->where('file_shares.status', 'active')  // Specify the table name
            ->where(function($query) {
                $query->whereNull('file_shares.expires_at')  // Specify table name
                      ->orWhere('file_shares.expires_at', '>', now());  // Specify table name
            })
            ->first();

        return $share !== null;
    }

    /**
     * Get active shared files for the user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function activeSharedFiles()
    {
        return $this->sharedFiles()
            ->wherePivot('status', 'active')
            ->wherePivot('expires_at', '>', now());
    }

    /**
     * Get expired shared files for the user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function expiredSharedFiles()
    {
        return $this->sharedFiles()
            ->wherePivot('status', 'active')
            ->wherePivot('expires_at', '<=', now());
    }

    /**
     * Get user's permission level for a file
     * 
     * @param File $file
     * @return string
     */
    /**
     * Get user settings as array with defaults
     * 
     * @return array
     */
    public function getSettings()
    {
        $defaults = [
            'two_factor_enabled' => false,
            'session_timeout' => 30,
            'email_on_login' => true,
            'email_on_device' => true,
            'require_otp_download' => true,
            'notify_file_access' => true,
            'email_notifications' => true,
            'push_notifications' => false,
            'file_shared_notification' => true,
            'transfer_created_notification' => true,
            'transfer_delivered_notification' => true,
            'file_accessed_notification' => true,
            'theme' => 'light',
            'sidebar_collapsed' => false,
            'dense_mode' => false,
        ];

        $settings = $this->settings;
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        return array_merge($defaults, $settings ?? []);
    }

    public function getFilePermission(File $file)
    {
        // Owner and Super Admin have full control
        if ($this->isSuperAdmin() || $this->id === $file->owner_id) {
            return 'full_control';
        }

        // Check specific share permissions (most granular)
        // If file->relation is loaded, use it to avoid extra queries
        $share = $file->relationLoaded('shares') 
            ? $file->shares->where('shared_with', $this->id)->where('status', 'active')->first()
            : FileShare::where('file_id', $file->id)
                ->where('shared_with', $this->id)
                ->where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->first();

        if ($share) {
            return $share->permission_level;
        }

        // Department admin can view all files in their department
        if ($this->isDepartmentAdmin() && $this->department_id === $file->department_id) {
            return 'view';
        }

        return 'none';
    }
}