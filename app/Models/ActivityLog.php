<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'action', 'module', 'file_id', 'transfer_id', 'description',
        'old_data', 'new_data', 'ip_address', 'user_agent', 'device_type',
        'browser', 'platform', 'location', 'latitude', 'longitude'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            // Prevent modification of logs
        });

        static::updating(function ($log) {
            return false; // Prevent updates
        });

        static::deleting(function ($log) {
            return false; // Prevent deletion
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function getFormattedDataAttribute()
    {
        $data = [];
        
        if ($this->old_data) {
            $data['old'] = $this->old_data;
        }
        
        if ($this->new_data) {
            $data['new'] = $this->new_data;
        }
        
        return $data;
    }
}