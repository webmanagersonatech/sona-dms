<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'status', 'description', 'settings'];

    protected $casts = [
        'status' => 'string',
        'settings' => 'array',
    ];

    /**
     * Get department settings with defaults
     */
    public function getSettings()
    {
        $defaults = [
            'max_storage_gb' => 10,
            'allow_external_sharing' => true,
            'require_otp_for_all' => false,
            'auto_purge_days' => 0, // 0 means disabled
        ];

        $settings = $this->settings;
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        return array_merge($defaults, $settings ?? []);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function departmentAdmin()
    {
        return $this->hasOne(User::class)->whereHas('role', function($query) {
            $query->where('slug', 'department-admin');
        });
    }
}