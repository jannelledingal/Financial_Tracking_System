<?php

namespace App\Providers;

use App\Listeners\UpdateLastLogin;
use App\Models\FinancialTrans;
use App\Observers\TransactionObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        FinancialTrans::observe(TransactionObserver::class);
    }
}