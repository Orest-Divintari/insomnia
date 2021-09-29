<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Notifications\Notification;

trait HandlesNotifications
{

    /**
     * Send notification to the given user
     *
     * @param User $user
     * @param Notification $notification
     * @return void
     */
    public function notify($user, $notification)
    {
        if (auth()->check() && (auth()->user()->isIgnored($user) || $user->hasNotVerifiedEmail())
        ) {
            return;
        }

        $user->notify($notification);
    }
}