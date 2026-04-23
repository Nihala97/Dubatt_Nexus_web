<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Module extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'group', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function profilePermissions()
    {
        return $this->hasMany(ProfileModulePermission::class);
    }

    public function userPermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }
}
