<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\UpdateLastLogin;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

   /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the listener to handle the Login event
        Event::listen(
            Login::class,
            UpdateLastLogin::class
        );

        view()->composer('layouts.navigation-top', \App\Http\View\Composers\ActivityComposer::class);

        //  to force HTTPS in production
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}