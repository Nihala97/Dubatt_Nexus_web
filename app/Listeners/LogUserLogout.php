<?php
namespace App\Listeners;

use App\Models\UserActivityLog;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function handle(Logout $event): void
    {
        if (!$event->user)
            return;

        UserActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'logout',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'logged_at' => now(),
        ]);
    }
}