<?php

namespace App\Providers;

use App\Listeners\LogLoginActivity;
use App\Listeners\LogLogoutActivity;
use App\Models\Peserta;
use App\Observers\PesertaObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, LogLoginActivity::class);
        Event::listen(Logout::class, LogLogoutActivity::class);

        Peserta::observe(PesertaObserver::class);
    }
}
