<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'department',
        'phone',
        'created_by',
        'updated_by',
        'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // ─── Role helpers ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManagement(): bool
    {
        return $this->role === 'management';
    }

    public function isNormal(): bool
    {
        return $this->role === 'normal';
    }

    // ─── Module Access Logic (FIXED) ───────────────────────────────
    public function canAccessModule(string $moduleSlug, string $action = 'can_view'): bool
    {
        // ✅ ADMIN / MANAGEMENT → FULL ACCESS (NO DB CHECK)
        if ($this->isAdmin() || $this->isManagement()) {
            return true;
        }

        // ❌ If table doesn't exist → avoid crash
        if (!Schema::hasTable('user_module_permissions')) {
            return false;
        }

        // ✅ Normal users → check permissions
        return $this->modulePermissions()
            ->whereHas('module', function ($q) use ($moduleSlug) {
                $q->where('slug', $moduleSlug)
                    ->where('is_active', true);
            })
            ->where($action, true)
            ->exists();
    }

    // ─── Relationships ─────────────────────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'user_profiles');
    }

    public function modulePermissions()
    {
        return $this->hasMany(UserModulePermission::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}