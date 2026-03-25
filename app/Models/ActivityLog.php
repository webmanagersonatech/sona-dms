<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Builder;
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

class ActivityLog extends Model
{
    use HasFactory;

<<<<<<< HEAD
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
=======
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'file_id',
        'transfer_id',
        'ip_address',
        'user_agent',
        'device_id',
        'location',
        'latitude',
        'longitude',
        'action',
        'description',
        'metadata',
        'performed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'performed_at' => 'datetime',
    ];

    /**
     * Always order logs by performed_at DESC
     */
    protected static function booted()
    {
        static::addGlobalScope('latest', function (Builder $builder) {
            $builder->orderBy('performed_at', 'desc');
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
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
<<<<<<< HEAD

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
=======
}
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
