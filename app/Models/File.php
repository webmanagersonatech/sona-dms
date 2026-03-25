<?php
<<<<<<< HEAD
// app/Models/File.php
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'original_name', 'file_path', 'file_type', 'mime_type',
        'file_size', 'extension', 'description', 'owner_id', 'department_id',
        'status', 'is_encrypted', 'encryption_key', 'download_count', 'view_count',
        'last_accessed_at', 'checksum', 'version', 'tags', 'metadata'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_encrypted' => 'boolean',
        'last_accessed_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
=======

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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
<<<<<<< HEAD
            $file->uuid = (string) \Illuminate\Support\Str::uuid();
=======
            $file->file_uuid = \Str::uuid();
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
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

<<<<<<< HEAD
=======
    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function shares()
    {
        return $this->hasMany(FileShare::class);
    }

<<<<<<< HEAD
    /**
     * Get active shares for this file
     */
    public function activeShares()
    {
        return $this->shares()
            ->where('status', 'active')
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

<<<<<<< HEAD
    public function getSizeForHumansAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIconAttribute()
    {
        $icons = [
            'pdf' => 'bi-file-pdf',
            'doc' => 'bi-file-word',
            'docx' => 'bi-file-word',
            'xls' => 'bi-file-excel',
            'xlsx' => 'bi-file-excel',
            'ppt' => 'bi-file-ppt',
            'pptx' => 'bi-file-ppt',
            'jpg' => 'bi-file-image',
            'jpeg' => 'bi-file-image',
            'png' => 'bi-file-image',
            'gif' => 'bi-file-image',
            'bmp' => 'bi-file-image',
            'svg' => 'bi-file-image',
            'webp' => 'bi-file-image',
            'zip' => 'bi-file-zip',
            'rar' => 'bi-file-zip',
            '7z' => 'bi-file-zip',
            'tar' => 'bi-file-zip',
            'gz' => 'bi-file-zip',
            'txt' => 'bi-file-text',
            'csv' => 'bi-file-spreadsheet',
            'mp3' => 'bi-file-music',
            'wav' => 'bi-file-music',
            'mp4' => 'bi-file-play',
            'avi' => 'bi-file-play',
            'mov' => 'bi-file-play',
            'mkv' => 'bi-file-play',
            'php' => 'bi-file-code',
            'js' => 'bi-file-code',
            'html' => 'bi-file-code',
            'css' => 'bi-file-code',
            'json' => 'bi-file-code',
            'xml' => 'bi-file-code',
        ];

        return $icons[strtolower($this->extension)] ?? 'bi-file';
    }

    /**
     * Check if file is previewable in browser
     */
    public function getIsPreviewableAttribute()
    {
        $previewableTypes = [
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', // images
            'pdf', // pdf
            'mp4', 'webm', 'ogg', 'mov', 'avi', // video
            'mp3', 'wav', 'm4a', // audio
            'txt', 'php', 'js', 'html', 'css', 'json', 'xml', 'md', // text/code
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx' // office (via google docs)
        ];
        return in_array(strtolower($this->extension), $previewableTypes);
    }

    /**
     * Get preview URL
     */
    public function getPreviewUrlAttribute()
    {
        if ($this->is_previewable) {
            return route('files.preview', $this);
        }
        return null;
    }

    public function getDownloadUrlAttribute()
    {
        return route('files.download', $this);
    }

    /**
     * Get file content for preview
     */
    public function getContent()
    {
        if (!Storage::disk('private')->exists($this->file_path)) {
            return null;
        }

        return Storage::disk('private')->get($this->file_path);
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}