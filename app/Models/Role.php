<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $fillable = ['name', 'slug', 'description'];
=======
    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f

    public function users()
    {
        return $this->hasMany(User::class);
    }
<<<<<<< HEAD

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
=======
>>>>>>> 0d0e6d232ac65287743e92e7c7778391eab60c9f
}