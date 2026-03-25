<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_id', 'sender_id', 'receiver_id', 'receiver_name', 'receiver_email',
        'receiver_phone', 'file_id', 'purpose', 'description', 'expected_delivery_time',
        'actual_delivery_time', 'status', 'tracking_number', 'courier_name',
        'delivery_location', 'received_by', 'signature', 'notes'
    ];

    protected $casts = [
        'expected_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transfer) {
            $transfer->transfer_id = 'TRF-' . strtoupper(uniqid());
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

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isOverdue()
    {
        return $this->status !== 'delivered' && 
               $this->expected_delivery_time < now();
    }
}