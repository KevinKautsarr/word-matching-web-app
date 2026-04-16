<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Carbon\Carbon;
use App\Models\User;

class UpdateUserStreakOnLogin
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
     */
    public function handle(Login $event): void
    {
        /** @var User */
        $user = $event->user;

        $today = Carbon::today();
        $lastActive = $user->last_active_at ? Carbon::parse($user->last_active_at)->startOfDay() : null;

        if ($lastActive === null) {
            // Login pertama kali
            $user->streak = 1;
        } elseif ($lastActive->eq($today)) {
            // Sudah login hari ini, tidak perlu update
            return;
        } elseif ($lastActive->eq($today->copy()->subDay())) {
            // Login kemarin → lanjutkan streak
            $user->streak += 1;
        } else {
            // Lewat lebih dari 1 hari → reset
            $user->streak = 1;
        }

        $user->last_active_at = Carbon::now();
        $user->save();
    }
}
