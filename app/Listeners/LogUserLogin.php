<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use App\Models\UserActivityLog;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        Log::info('LOGIN EVENT FIRED');

        UserActivityLog::create([
            'user_id' => $event->user->id,
            'action' => 'login',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'logged_at' => now(),
        ]);
    }
}