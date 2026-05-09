<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ActivityComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view)
    {
        $user = Auth::user();

        // If no user is logged in, return empty data
        if (!$user) {
            $view->with('recentActivities', collect());
            return;
        }

        // Fetch the 5 most recent messages received by the user
        $recentMessages = Message::where('receiver_id', $user->id)
            ->with('sender')
            ->latest()
            ->take(5)
            ->get();

        $view->with('recentActivities', $recentMessages);
    }
}