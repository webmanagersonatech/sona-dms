<?php
// app/Models/FileShare.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
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
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the user who shared the file
     */
    public function sharedBy()
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    /**
     * Get the user who received the share
     */
    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with');
    }

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
    }
}