<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
        'transfer_id', 'sender_id', 'receiver_id', 'receiver_name', 'receiver_email',
        'receiver_phone', 'file_id', 'purpose', 'description', 'expected_delivery_time',
        'actual_delivery_time', 'status', 'tracking_number', 'courier_name',
        'delivery_location', 'received_by', 'signature', 'notes'
    ];

    protected $casts = [
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
        'expected_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
<<<<<<< HEAD
            $transfer->transfer_id = 'TRF-' . strtoupper(uniqid());
=======
            $transfer->transfer_uuid = \Str::uuid();
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
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

<<<<<<< HEAD
=======
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
=======
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInTransit()
    {
        return $this->status === 'in_transit';
    }

>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

<<<<<<< HEAD
    public function isOverdue()
    {
        return $this->status !== 'delivered' && 
               $this->expected_delivery_time < now();
=======
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
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    }
}