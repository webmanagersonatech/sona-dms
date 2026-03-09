<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_uuid',
        'owner_id',
        'department_id',
        'original_name',
        'storage_name',
        'file_path',
        'mime_type',
        'size',
        'extension',
        'encryption_status',
        'encryption_key',
        'permissions',
        'description',
        'tags',
        'is_archived',
        'is_shared',
        'archived_at',
        'expires_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'tags' => 'array',
        'is_archived' => 'boolean',
        'is_shared' => 'boolean',
        'archived_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            $file->file_uuid = \Str::uuid();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function shares()
    {
        return $this->hasMany(FileShare::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isArchived()
    {
        return $this->is_archived;
    }

    public function canBeAccessedBy($user)
    {
        // Owner can always access
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Department access
        if ($this->department_id === $user->department_id && $user->hasPermission('files.view')) {
            return true;
        }

        // Check active shares
        $activeShare = $this->shares()
            ->where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('shared_with', $user->id)
                      ->orWhere('shared_email', $user->email);
            })
            ->where('valid_until', '>', now())
            ->where(function ($query) {
                $query->whereNull('valid_from')
                      ->orWhere('valid_from', '<=', now());
            })
            ->first();

        return $activeShare !== null;
    }
}