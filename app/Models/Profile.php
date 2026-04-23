<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active', 'created_by', 'updated_by'];
    protected $casts = ['is_active' => 'boolean'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_profiles');
    }

    public function modulePermissions()
    {
        return $this->hasMany(ProfileModulePermission::class);
    }
}