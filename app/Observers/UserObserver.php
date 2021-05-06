<?php

namespace App\Observers;

use App\User;
use App\User\Details;

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

    /**
     * Handle the user "creating" event.
     *
     * @param User $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->details = (new Details([], $user))->getDefault();
    }

}