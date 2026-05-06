<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'session_id',
        'logged_at'
    ];

    protected $casts = ['logged_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}