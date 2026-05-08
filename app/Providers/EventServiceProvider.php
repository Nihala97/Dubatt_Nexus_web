<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class => [
            LogUserLogin::class,
        ],

        Logout::class => [
            LogUserLogout::class,
        ],
    ];

    public function boot(): void
    {

    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}