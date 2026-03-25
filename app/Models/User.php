<?php
<<<<<<< HEAD
// app/Models/User.php
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\SoftDeletes;
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
<<<<<<< HEAD
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'department_id', 'status',
        'phone', 'avatar', 'two_factor_secret', 'two_factor_recovery_codes',
        'last_login_at', 'last_login_ip'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
<<<<<<< HEAD
        'last_login_at' => 'datetime',
=======
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'two_factor_recovery_codes' => 'array',
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
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

<<<<<<< HEAD
    public function sharedFiles()
    {
        return $this->belongsToMany(File::class, 'file_shares', 'shared_with', 'file_id')
                    ->withPivot('permission_level', 'expires_at', 'status')
                    ->withTimestamps();
    }

    public function transfers()
=======
    public function sentTransfers()
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    {
        return $this->hasMany(Transfer::class, 'sender_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'receiver_id');
    }

<<<<<<< HEAD
=======
    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

<<<<<<< HEAD
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
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}