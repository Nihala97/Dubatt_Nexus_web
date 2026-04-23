<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ProfileModulePermission extends Model
{
    protected $fillable = [
        'profile_id',
        'module_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'granted_by',
    ];
    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}