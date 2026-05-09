<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Carbon\Carbon;

class UpdateLastLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * * @param Login $event
     * @return void
     */
    public function handle(Login $event): void
    {
        // Update the last_login_at column for the user who just logged in
        $event->user->update([
            'last_login_at' => Carbon::now(),
        ]);
    }
}