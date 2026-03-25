<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['name', 'code', 'status', 'description'];

    protected $casts = [
        'status' => 'string'
=======
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
<<<<<<< HEAD

    public function departmentAdmin()
    {
        return $this->hasOne(User::class)->whereHas('role', function($query) {
            $query->where('slug', 'department-admin');
        });
    }
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
}