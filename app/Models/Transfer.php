<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_uuid',
        'sender_id',
        'receiver_id',
        'file_id',
        'transfer_type',
        'purpose',
        'status',
        'expected_delivery_time',
        'actual_delivery_time',
        'delivery_location',
        'latitude',
        'longitude',
        'third_party_involved',
        'third_party_name',
        'third_party_email',
        'notes',
    ];

    protected $casts = [
        'third_party_involved' => 'boolean',
        'expected_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            $transfer->transfer_uuid = \Str::uuid();
        });
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInTransit()
    {
        return $this->status === 'in_transit';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isReceived()
    {
        return $this->status === 'received';
    }

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_transit' => 'info',
            'delivered' => 'success',
            'received' => 'primary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}