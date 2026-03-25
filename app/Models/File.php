<?php
// app/Models/File.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            $file->uuid = (string) \Illuminate\Support\Str::uuid();
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

    public function shares()
    {
        return $this->hasMany(FileShare::class);
    }

    /**
     * Get active shares for this file
     */
    // public function activeShares()
    // {
    //     return $this->shares()
    //         ->where('status', 'active')
    //         ->where(function($q) {
    //             $q->whereNull('expires_at')
    //               ->orWhere('expires_at', '>', now());
    //         });
    // }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

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
    }


public function activeShares()
{
    return $this->hasMany(FileShare::class)
        ->where('status', 'active')
        ->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
}
}