<?php

namespace App\Observers;

use App\User;

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
    }
}