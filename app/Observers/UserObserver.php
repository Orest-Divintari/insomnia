<?php

namespace App\Observers;

use App\User;
use App\User\Details;
use App\User\Privacy;

class UserObserver
{
    /**
     * Handle the user "deleting" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        $user->threads->each->delete();
        $user->activities->each->delete();
        $user->ignorings->each->delete();
    }

    /**
     * Handle the user "creating" event.
     *
     * @param User $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->details = config('settings.details.attributes');
        $user->privacy = config('settings.privacy.attributes');
        $user->preferences = config('settings.preferences.attributes');
    }

}