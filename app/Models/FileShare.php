<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
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

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with');
    }

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
    }
}