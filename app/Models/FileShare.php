<?php
<<<<<<< HEAD
// app/Models/FileShare.php
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'file_id', 
        'shared_by', 
        'shared_with', 
        'permission_level', 
        'expires_at', 
        'status', 
        'access_token'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the file that was shared
     */
=======
        'share_token',
        'file_id',
        'shared_by',
        'shared_with',
        'shared_email',
        'permissions',
        'valid_from',
        'valid_until',
        'max_access_count',
        'access_count',
        'requires_otp_approval',
        'is_active',
        'last_accessed_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'requires_otp_approval' => 'boolean',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($share) {
            $share->share_token = \Str::uuid();
        });
    }

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function file()
    {
        return $this->belongsTo(File::class);
    }

<<<<<<< HEAD
    /**
     * Get the user who shared the file
     */
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

<<<<<<< HEAD
    /**
     * Get the user who received the share
     */
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with');
    }

<<<<<<< HEAD
    /**
     * Check if the share is still valid
     */
    public function isValid()
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    /**
     * Check if the share has expired
     */
    public function isExpired()
    {
        return $this->expires_at !== null && $this->expires_at <= now();
    }

    /**
     * Scope a query to only include active shares
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope a query to only include shares for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('shared_with', $userId);
    }

    /**
     * Scope a query to only include shares from a specific user
     */
    public function scopeFromUser($query, $userId)
    {
        return $query->where('shared_by', $userId);
=======
    public function isExpired()
    {
        return $this->valid_until->isPast();
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->max_access_count && $this->access_count >= $this->max_access_count) {
            return false;
        }

        return true;
    }

    public function hasPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    public function incrementAccessCount()
    {
        $this->access_count++;
        $this->last_accessed_at = now();
        $this->save();
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}