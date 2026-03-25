<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'status', 'description'];

    protected $casts = [
        'status' => 'string'
    ];

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