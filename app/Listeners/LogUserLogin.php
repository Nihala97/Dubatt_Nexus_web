<?php
namespace App\Listeners;

use App\Models\UserActivityLog;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        UserActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'logged_at' => now(),
        ]);

        // Also update last_login_at on users table
        $event->user->update(['last_login_at' => now()]);
    }
}